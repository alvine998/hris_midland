<?php

namespace App\Services;

use App\Models\AiChatMessage;
use App\Models\AiChatSession;
use App\Models\User;
use App\Services\Ai\AlvineAiRouterClient;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AiChatService
{
    public function __construct(
        protected AlvineAiRouterClient $client,
        protected KnowledgeBaseService $knowledge,
    ) {}

    public function createSession(User $user, string $model = 'auto'): AiChatSession
    {
        return AiChatSession::create([
            'user_id' => $user->id,
            'title' => 'New chat',
            'model' => $model,
        ]);
    }

    /**
     * Send a user message within a session and return the stored assistant reply
     * without streaming (used by the JSON fallback endpoint).
     *
     * @throws \RuntimeException
     * @throws RequestException
     */
    public function send(AiChatSession $session, string $content): AiChatMessage
    {
        $session->messages()->create(['role' => 'user', 'content' => $content]);

        $this->consolidateMemory($session);

        $reply = $this->client->chat(
            $this->buildContextMessages($session),
            $session->model,
        );

        return $this->persistReply($session, $reply);
    }

    /**
     * Send a user message, streaming the assistant reply through $onDelta as
     * it arrives, and persist the completed reply.
     *
     * @param  callable(string): bool  $onDelta  Return false to abort the stream.
     *
     * @throws \RuntimeException
     * @throws RequestException
     */
    public function sendStreaming(AiChatSession $session, string $content, callable $onDelta, bool $regenerate = false): AiChatMessage
    {
        if ($regenerate) {
            // Drop the previous assistant turn so the model can try again with
            // the same user prompt and updated context.
            $session->messages()
                ->where('role', 'assistant')
                ->latest('id')
                ->first()
                ?->delete();
        } else {
            $session->messages()->create(['role' => 'user', 'content' => $content]);
        }

        $this->consolidateMemory($session);

        $reply = $this->client->chatStream(
            $this->buildContextMessages($session),
            $onDelta,
            $session->model,
        );

        return $this->persistReply($session, $reply);
    }

    protected function persistReply(AiChatSession $session, array $reply): AiChatMessage
    {
        $message = $session->messages()->create([
            'role' => 'assistant',
            'content' => $reply['content'],
            'model' => $reply['model'],
            'credits_used' => $reply['credits_used'],
        ]);

        $session->update([
            'title' => $this->resolveTitle($session),
            'last_message_at' => now(),
        ]);

        return $message;
    }

    /**
     * Build the prompt sent to the router: system prompt + long-term memory
     * summary + the most recent messages kept verbatim (short-term memory).
     *
     * @return array<int, array{role: string, content: string}>
     */
    public function buildContextMessages(AiChatSession $session): array
    {
        $window = max(2, (int) config('services.alvine_ai_router.memory_window', 20));

        $messages = [['role' => 'system', 'content' => $this->systemPrompt($session)]];

        // RAG: inject relevant knowledge base context based on latest user message
        $latestUserMessage = $session->messages()->where('role', 'user')->latest('id')->first();
        if ($latestUserMessage) {
            $searchTerm = $this->knowledge->toSearchTerm($latestUserMessage->content);
            $kbContext = $this->knowledge->buildContext($searchTerm);
            if (filled($kbContext)) {
                $messages[] = ['role' => 'system', 'content' => $kbContext];
            }
        }

        if (filled($session->memory)) {
            $messages[] = [
                'role' => 'system',
                'content' => "Long-term memory of this conversation:\n".$session->memory,
            ];
        }

        foreach ($session->messages()->latest()->take($window)->get()->reverse() as $message) {
            $messages[] = ['role' => $message->role, 'content' => $message->content];
        }

        return $messages;
    }

    /**
     * When enough conversation has fallen out of the short-term window,
     * summarize the pending overflow into the session's long-term memory.
     * Consolidation is batched so the extra API round-trip only happens
     * occasionally instead of on every turn.
     */
    protected function consolidateMemory(AiChatSession $session): void
    {
        $window = max(2, (int) config('services.alvine_ai_router.memory_window', 20));
        $batch = max(1, (int) config('services.alvine_ai_router.consolidation_batch', 10));

        // Reserve room for the newest user message and its upcoming reply.
        $baseId = (int) ($session->memory_upto_id ?? 0);
        $recentKeep = $window - 1;

        $pendingQuery = $session->messages()->where('id', '>', $baseId)->orderBy('id');
        $overflow = max(0, (clone $pendingQuery)->count() - $recentKeep);

        if ($overflow < $batch) {
            return;
        }

        $toSummarize = $pendingQuery->take($overflow)->get();

        if ($toSummarize->isEmpty()) {
            return;
        }

        $transcript = $toSummarize->map(fn (AiChatMessage $m) => ucfirst($m->role).': '.$m->content)->implode("\n");
        $previous = filled($session->memory) ? "Previous memory summary:\n{$session->memory}\n\n" : '';

        try {
            $summary = $this->client->chat([
                ['role' => 'system', 'content' => 'Summarize the conversation excerpt into concise bullet points of durable facts, decisions and preferences. Keep prior bullets that are still relevant. Reply with the summary only.'],
                ['role' => 'user', 'content' => $previous.$transcript],
            ], $session->model);

            $session->update([
                'memory' => trim($summary['content']),
                'memory_upto_id' => $toSummarize->last()->id,
            ]);
        } catch (\Throwable $e) {
            // Memory consolidation is best-effort; never block the chat exchange.
            Log::warning('AiChatService::consolidateMemory — failed to update memory', [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function resolveTitle(AiChatSession $session): string
    {
        if ($session->title !== 'New chat') {
            return $session->title;
        }

        $firstUserMessage = $session->messages()->where('role', 'user')->orderBy('id')->first();

        return Str::limit(trim($firstUserMessage?->content ?? 'New chat'), 60, '…');
    }

    protected function systemPrompt(AiChatSession $session): string
    {
        $userName = $session->user?->name ?? 'the user';

        return implode("\n", [
            'You are Midland AI, a helpful HR assistant embedded in an HRIS application.',
            "You are chatting with {$userName}. Be concise, professional and friendly.",
            'Use the conversation memory when provided to stay consistent with earlier parts of the conversation.',
            'You have access to the company HR knowledge base. When relevant context is provided, use it to answer questions about HR policies, leave rules, attendance, payroll, and company procedures.',
            'If the knowledge base does not contain relevant information, say so honestly rather than making up answers.',
        ]);
    }
}
