<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAiChatMessageRequest;
use App\Models\AiChatMessage;
use App\Models\AiChatSession;
use App\Services\AiChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AiChatController extends Controller
{
    public function __construct(protected AiChatService $service)
    {
        $this->middleware(function ($request, $next) {
            abort_unless(Auth::user()?->isAdmin(), 403, 'AI Assistant is restricted to Super Admin.');

            return $next($request);
        });
    }

    public function index(): View
    {
        $user = Auth::user();
        $sessions = $this->sessionsFor($user);
        $selected = $sessions->first();

        return view('ai-chat.index', [
            'sessions' => $sessions,
            'selectedSession' => $selected,
            'initialMessages' => $selected
                ? $selected->messages()->orderBy('created_at')->orderBy('id')->get()
                : collect(),
        ]);
    }

    public function storeSession(): RedirectResponse|JsonResponse
    {
        $session = $this->service->createSession(Auth::user());

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Session created.', 'data' => $session], 201);
        }

        return redirect()->route('ai-chat.index', ['session' => $session->id]);
    }

    public function destroySession(AiChatSession $session): RedirectResponse|JsonResponse
    {
        $this->authorizeOwner($session);

        $session->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Session deleted.']);
        }

        return redirect()->route('ai-chat.index')->with('success', 'Chat session deleted.');
    }

    public function messages(AiChatSession $session): JsonResponse
    {
        $this->authorizeOwner($session);

        return response()->json([
            'data' => [
                'messages' => $session->messages()
                    ->orderBy('created_at')
                    ->orderBy('id')
                    ->get(['id', 'role', 'content', 'model', 'credits_used', 'created_at'])
                    ->map(fn ($message) => [
                        'id' => $message->id,
                        'role' => $message->role,
                        'content' => $message->content,
                        'credits_used' => $message->credits_used !== null ? (float) $message->credits_used : null,
                        'created_at' => $message->created_at?->toIso8601String(),
                    ]),
                'memory' => [
                    'has' => filled($session->memory),
                    'preview' => str($session->memory)->limit(220),
                ],
            ],
        ]);
    }

    public function send(AiChatSession $session, StoreAiChatMessageRequest $request): JsonResponse
    {
        $this->authorizeOwner($session);

        try {
            // Keep the request well under PHP's max_execution_time so the AI
            // service's own timeouts are reached first.
            set_time_limit(max(60, (int) config('services.alvine_ai_router.timeout', 25) * 3 + 15));

            $reply = $this->service->send($session, $request->validated('content'));
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'The AI assistant is unavailable right now. Please try again.',
            ], 502);
        }

        return response()->json([
            'message' => 'Reply received.',
            'data' => [
                'reply' => $this->replyPayload($reply),
            ],
        ]);
    }

    public function stream(AiChatSession $session, StoreAiChatMessageRequest $request): StreamedResponse
    {
        $this->authorizeOwner($session);
        $content = $request->validated('content');
        $regenerate = (bool) $request->boolean('regenerate');

        // Keep the request well under PHP's max_execution_time so the AI
        // service's own timeouts are reached first.
        set_time_limit(max(60, (int) config('services.alvine_ai_router.timeout', 25) * 3 + 15));

        // Client-side abort (e.g. user clicked "Stop"). The connection's
        // read_disabled flag flips when the client goes away, and we hook
        // the AI service's onDelta to bail out cleanly.
        $aborted = false;
        $clientGone = function () use (&$aborted) {
            if (connection_aborted() !== 0) {
                $aborted = true;
            }

            return $aborted;
        };

        return response()->stream(function () use ($session, $content, $regenerate, &$aborted, $clientGone): void {
            header('X-Accel-Buffering: no');

            echo "event: start\ndata: {}\n\n";
            flush();

            try {
                $reply = $this->service->sendStreaming(
                    $session,
                    $content,
                    function (string $delta) use (&$aborted, $clientGone): bool {
                        if ($clientGone()) {
                            return false;
                        }

                        echo 'data: '.json_encode(['delta' => $delta], JSON_UNESCAPED_UNICODE)."\n\n";

                        if (ob_get_level() > 0) {
                            @ob_flush();
                        }
                        @flush();

                        return true;
                    },
                    regenerate: $regenerate,
                );

                echo 'data: '.json_encode([
                    'done' => true,
                    'reply' => $this->replyPayload($reply),
                    'title' => $session->refresh()->title,
                    'aborted' => $aborted,
                ], JSON_UNESCAPED_UNICODE)."\n\n";
            } catch (\Throwable $e) {
                report($e);

                echo 'data: '.json_encode([
                    'error' => 'The AI assistant is unavailable right now. Please try again.',
                ])."\n\n";
            }

            echo "data: [DONE]\n\n";

            if (ob_get_level() > 0) {
                @ob_flush();
            }
            @flush();
        }, 200, [
            'Content-Type' => 'text/event-stream; charset=UTF-8',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    public function updateSession(AiChatSession $session, Request $request): JsonResponse
    {
        $this->authorizeOwner($session);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
        ]);

        $session->update(['title' => trim($data['title'])]);

        return response()->json([
            'message' => 'Session updated.',
            'data' => ['id' => $session->id, 'title' => $session->title],
        ]);
    }

    public function destroyLastAssistant(AiChatSession $session): JsonResponse
    {
        $this->authorizeOwner($session);

        $last = $session->messages()->where('role', 'assistant')->latest('id')->first();

        if (! $last) {
            return response()->json(['message' => 'Nothing to remove.'], 404);
        }

        $last->delete();

        return response()->json(['message' => 'Last assistant message removed.']);
    }

    protected function replyPayload(AiChatMessage $reply): array
    {
        return [
            'id' => $reply->id,
            'role' => $reply->role,
            'content' => $reply->content,
            'credits_used' => $reply->credits_used !== null ? (float) $reply->credits_used : null,
            'created_at' => $reply->created_at?->toIso8601String(),
        ];
    }

    protected function sessionsFor($user)
    {
        return AiChatSession::forUser($user->id)->orderByDesc('last_message_at')->orderByDesc('id')->get();
    }

    protected function authorizeOwner(AiChatSession $session): void
    {
        abort_unless((int) $session->user_id === (int) Auth::id(), 403);
    }
}
