<?php

namespace App\Services\Ai;

use App\Models\User;
use App\Models\WhatsAppMessage;
use App\Models\WhatsAppSession;
use App\Services\Auth\WhatsAppAuthService;
use Illuminate\Support\Facades\Log;

class ConversationManager
{
    /**
     * The WhatsApp authentication service instance.
     */
    protected WhatsAppAuthService $authService;

    /**
     * The BigModel AI client instance.
     */
    protected BigModelClient $bigModelClient;

    /**
     * The function registry for discovering available AI functions.
     */
    protected FunctionRegistry $functionRegistry;

    /**
     * The function dispatcher for executing tool calls returned by the AI.
     */
    protected FunctionDispatcher $functionDispatcher;

    /**
     * Create a new ConversationManager instance.
     */
    public function __construct(
        WhatsAppAuthService $authService,
        BigModelClient $bigModelClient,
        FunctionRegistry $functionRegistry,
        FunctionDispatcher $functionDispatcher,
    ) {
        $this->authService = $authService;
        $this->bigModelClient = $bigModelClient;
        $this->functionRegistry = $functionRegistry;
        $this->functionDispatcher = $functionDispatcher;
    }

    /**
     * Main entry point — receives a user message from WhatsApp and orchestrates
     * the authentication check, AI conversation, function calling, and response.
     *
     * Flow:
     * 1. Get or create WhatsAppSession for phone number
     * 2. If the session is not active, delegate to the auth service for the
     *    multi-step authentication flow
     * 3. If authenticated, search for relevant functions, build the conversation
     *    history and system prompt, call Gemini, handle any function calls, and
     *    persist the messages
     */
    public function handleMessage(string $phoneNumber, string $message): string
    {
        try {
            $session = $this->authService->getSession($phoneNumber);

            // ───────────────────────────────────────────────────────────
            //  Not authenticated — route through the auth service
            // ───────────────────────────────────────────────────────────
            if (! $session->isActive()) {
                $authResult = $this->authService->handle($phoneNumber, $message);

                return $authResult['message'];
            }

            /** @var User $user */
            $user = $session->user;

            // Touch activity to keep the session alive
            $session->touchActivity();

            // ───────────────────────────────────────────────────────────
            //  Search for relevant function tools
            // ───────────────────────────────────────────────────────────
            $functions = $this->functionRegistry->searchFunctions($message);

            // Filter functions by user's RBAC permissions
            $functions = $this->filterFunctionsByPermission($functions, $user);

            // ───────────────────────────────────────────────────────────
            //  Build the AI request
            // ───────────────────────────────────────────────────────────
            $conversationHistory = $this->buildConversationHistory($phoneNumber, $message);
            $systemPrompt = $this->buildSystemPrompt($user);
            $messages = $this->buildMessages($systemPrompt, $conversationHistory);

            $tools = $this->bigModelClient->buildFunctionDeclaration($functions);

            // ───────────────────────────────────────────────────────────
            //  Call BigModel API
            // ───────────────────────────────────────────────────────────
            $response = $this->bigModelClient->chat($messages, $tools);

            // ───────────────────────────────────────────────────────────
            //  Handle tool calls if present
            // ───────────────────────────────────────────────────────────
            $toolCalls = $this->bigModelClient->extractToolCalls($response);

            if ($toolCalls !== null) {
                $response = $this->handleToolCalls($toolCalls, $user, $messages, $tools);
            }

            $aiResponse = $this->bigModelClient->extractText($response);

            // ───────────────────────────────────────────────────────────
            //  Persist the conversation
            // ───────────────────────────────────────────────────────────
            $this->storeMessages($session, $message, $aiResponse);

            Log::info('ConversationManager: AI response generated', [
                'phone' => $phoneNumber,
                'user_id' => $user->id,
                'has_tool_calls' => $toolCalls !== null,
                'response_length' => strlen($aiResponse),
            ]);

            return $aiResponse;

        } catch (\Throwable $e) {
            Log::error('ConversationManager: Failed to handle message', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return 'Sorry, I encountered an issue processing your message. Please try again later.';
        }
    }

    /**
     * Build the conversation history from stored WhatsApp messages
     * plus the current user message.
     *
     * Retrieves the last N messages (configurable via
     * `config('services.whatsapp_ai.max_history_messages')`, default 10) ordered
     * by creation date and converts them into the AI's message format.
     *
     * @return array<int, array{role: string, content: string}>
     */
    public function buildConversationHistory(string $phoneNumber, string $newMessage): array
    {
        $session = $this->authService->getSession($phoneNumber);

        $maxMessages = (int) config('services.whatsapp_ai.max_history_messages', 10);

        // Fetch the most recent messages in reverse chronological order,
        // then reverse to chronological for the AI payload.
        $messages = $session->messages()
            ->orderBy('created_at', 'desc')
            ->take($maxMessages)
            ->get()
            ->reverse();

        $history = [];

        foreach ($messages as $msg) {
            $history[] = [
                'role' => $msg->direction === 'incoming' ? 'user' : 'assistant',
                'content' => $msg->content,
            ];
        }

        // Append the current (unsaved) user message
        $history[] = [
            'role' => 'user',
            'content' => $newMessage,
        ];

        return $history;
    }

    /**
     * Build the system prompt with the authenticated user's context.
     *
     * The prompt instructs the AI about its role as an HR assistant and provides
     * the current user's details (name, email, position, department) so responses
     * can be personalised.
     */
    public function buildSystemPrompt(User $user): string
    {
        $employeeName = $user->employee?->name ?? $user->name;
        $employeePosition = $user->employee?->jobPosition?->name ?? 'N/A';
        $employeeDepartment = $user->employee?->department?->name ?? 'N/A';
        $isAdmin = $user->isAdmin() ? 'Yes (full access)' : 'No';

        return <<<PROMPT
You are a helpful HR assistant for Midland Holdings. You help employees with HR-related queries via WhatsApp.

Current user:
- Name: {$employeeName}
- Email: {$user->email}
- Position: {$employeePosition}
- Department: {$employeeDepartment}
- Is Admin: {$isAdmin}

You have access to various functions that can help you retrieve and manage HR information.
Only use the functions provided to you — do not make up capabilities you don't have.
When a user asks for something that requires data lookup or action, use the appropriate function.
If the user asks for something you cannot do, explain what you CAN help with.
Be friendly, professional, and concise in your responses (WhatsApp format).
PROMPT;
    }

    /**
     * Execute a function call returned by Gemini and return the result.
     *
     * Delegates to the FunctionDispatcher and catches any exceptions so a single
     * failing function never crashes the entire conversation.
     *
     * @param  array{name: string, args: array<string, mixed>}  $functionCall
     * @return array<string, mixed>
     */
    public function processFunctionCall(array $functionCall, User $user): array
    {
        Log::info('ConversationManager: Processing function call', [
            'function' => $functionCall['name'],
            'user_id' => $user->id,
        ]);

        try {
            return $this->functionDispatcher->dispatch(
                $functionCall['name'],
                $functionCall['args'],
                $user
            );
        } catch (\Throwable $e) {
            Log::error('ConversationManager: Function dispatch failed', [
                'function' => $functionCall['name'],
                'error' => $e->getMessage(),
            ]);

            return [
                'error' => 'Failed to execute function: '.$e->getMessage(),
            ];
        }
    }

    // ─────────────────────────────────────────────────────────────────
    //  Private helpers
    // ─────────────────────────────────────────────────────────────────

    /**
     * Handle tool calls from the AI by dispatching them and re-calling the AI
     * with the tool responses to obtain a natural language answer.
     *
     * @param  array<int, array{id: string, name: string, args: array<string, mixed>}>  $toolCalls
     * @param  array<int, array{role: string, content?: string, parts?: array, tool_calls?: array, tool_call_id?: string}>  $messages
     * @param  array<int, array{type: string, function: array}>  $tools
     * @return array<string, mixed>
     */
    private function handleToolCalls(
        array $toolCalls,
        User $user,
        array $messages,
        array $tools
    ): array {
        // Append the assistant's tool call request
        $toolCallsForMessage = array_map(function ($tc) {
            return [
                'id' => $tc['id'],
                'type' => 'function',
                'function' => [
                    'name' => $tc['name'],
                    'arguments' => json_encode($tc['args']),
                ],
            ];
        }, $toolCalls);

        $messages[] = [
            'role' => 'assistant',
            'content' => null,
            'tool_calls' => $toolCallsForMessage,
        ];

        // Execute each tool and append results
        foreach ($toolCalls as $toolCall) {
            $result = $this->processFunctionCall($toolCall, $user);

            $messages[] = [
                'role' => 'tool',
                'content' => json_encode($result),
                'tool_call_id' => $toolCall['id'],
            ];
        }

        return $this->bigModelClient->chat($messages, $tools);
    }

    /**
     * Build the full messages array for the AI by prepending the system prompt
     * as the first user message, followed by the conversation history.
     *
     * @param  array<int, array{role: string, parts: array}>  $conversationHistory
     * @return array<int, array{role: string, content: string}>
     */
    private function buildMessages(string $systemPrompt, array $conversationHistory): array
    {
        $messages = [
            [
                'role' => 'user',
                'content' => $systemPrompt,
            ],
        ];

        foreach ($conversationHistory as $msg) {
            $content = $msg['content'] ?? $this->extractTextFromParts($msg['parts'] ?? []);

            $messages[] = [
                'role' => $msg['role'] === 'model' ? 'assistant' : $msg['role'],
                'content' => $content,
            ];
        }

        return $messages;
    }

    /**
     * Extract text content from parts array.
     */
    private function extractTextFromParts(array $parts): string
    {
        foreach ($parts as $part) {
            if (isset($part['text'])) {
                return $part['text'];
            }
        }

        return '';
    }

    /**
     * Filter functions by the user's RBAC permissions.
     *
     * Only returns functions where:
     * - No permission is required (public functions), OR
     * - The user has the required permission (including wildcard '*')
     *
     * @param  array<int, array{name: string, description: string, permission: string, parameters: array, class: string, method: string, score: int}>  $functions
     * @return array<int, array{name: string, description: string, permission: string, parameters: array, class: string, method: string, score: int}>
     */
    private function filterFunctionsByPermission(array $functions, User $user): array
    {
        return array_values(array_filter($functions, function ($function) use ($user) {
            $permission = $function['permission'] ?? '';

            // No permission required — always available
            if ($permission === '') {
                return true;
            }

            // Check if user has the permission (includes wildcard '*' check)
            return $user->hasPermission($permission);
        }));
    }

    /**
     * Persist the user message and AI response in the database.
     */
    private function storeMessages(WhatsAppSession $session, string $userMessage, string $aiResponse): void
    {
        WhatsAppMessage::create([
            'whatsapp_session_id' => $session->id,
            'direction' => 'incoming',
            'content' => $userMessage,
        ]);

        WhatsAppMessage::create([
            'whatsapp_session_id' => $session->id,
            'direction' => 'outgoing',
            'content' => $aiResponse,
        ]);
    }
}
