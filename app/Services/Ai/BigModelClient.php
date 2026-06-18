<?php

namespace App\Services\Ai;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BigModelClient
{
    private const BASE_URL = 'https://open.bigmodel.cn/api/paas/v4';

    protected string $model;

    protected ?string $apiKey;

    protected float $temperature;

    protected int $maxTokens;

    public function __construct()
    {
        $this->apiKey = config('services.bigmodel.api_key');
        $this->model = config('services.bigmodel.model', 'glm-5.1');
        $this->temperature = (float) config('services.bigmodel.temperature', 0.2);
        $this->maxTokens = (int) config('services.bigmodel.max_tokens', 1024);
    }

    /**
     * Send a chat request to the BigModel API with optional function declarations as tools.
     *
     * @param  array<int, array{role: string, parts?: array, content?: string, tool_calls?: array, tool_call_id?: string}>  $messages
     * @param  array<int, array{type: string, function: array}>  $tools
     * @return array<string, mixed>
     *
     * @throws \RuntimeException When the API key is missing.
     * @throws RequestException When the API returns a non-2xx status.
     */
    public function chat(array $messages, array $tools = []): array
    {
        if (empty($this->apiKey)) {
            throw new \RuntimeException('BigModel API key is not configured. Set BIGMODEL_API_KEY in your .env file.');
        }

        $url = sprintf('%s/chat/completions', self::BASE_URL);

        $payload = $this->buildPayload($messages, $tools);

        Log::debug('BigModelClient::chat — Sending request', [
            'model' => $this->model,
            'message_count' => count($messages),
            'tool_count' => count($tools),
            'payload' => json_encode($payload, JSON_PRETTY_PRINT),
        ]);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($url, $payload);

        if ($response->failed()) {
            Log::error('BigModelClient::chat — API request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            $response->throw();
        }

        $data = $response->json();

        Log::debug('BigModelClient::chat — Response received', [
            'has_choices' => isset($data['choices']),
        ]);

        return $data;
    }

    /**
     * Convert an array of function definitions into BigModel tool declarations.
     *
     * @param  array<int, array{name: string, description: string, parameters: array}>  $functions
     * @return array<int, array{type: string, function: array}>
     */
    public function buildFunctionDeclaration(array $functions): array
    {
        if (empty($functions)) {
            return [];
        }

        $declarations = [];

        foreach ($functions as $function) {
            $declarations[] = [
                'type' => 'function',
                'function' => [
                    'name' => $function['name'],
                    'description' => $function['description'] ?? '',
                    'parameters' => $function['parameters'] ?? [
                        'type' => 'object',
                        'properties' => [],
                    ],
                ],
            ];
        }

        return $declarations;
    }

    /**
     * Extract tool calls from a BigModel API response.
     *
     * Returns null when the model responded with text instead of tool calls.
     *
     * @param  array<string, mixed>  $response
     * @return array<int, array{id: string, name: string, args: array<string, mixed>}>|null
     */
    public function extractToolCalls(array $response): ?array
    {
        $choice = $response['choices'][0] ?? null;

        if ($choice === null) {
            return null;
        }

        $toolCalls = $choice['message']['tool_calls'] ?? null;

        if (empty($toolCalls)) {
            return null;
        }

        $result = [];

        foreach ($toolCalls as $toolCall) {
            $result[] = [
                'id' => $toolCall['id'] ?? '',
                'name' => $toolCall['function']['name'] ?? '',
                'args' => json_decode($toolCall['function']['arguments'] ?? '{}', true),
            ];
        }

        return $result;
    }

    /**
     * Extract the text content from a BigModel API response.
     *
     * @param  array<string, mixed>  $response
     */
    public function extractText(array $response): string
    {
        $choice = $response['choices'][0] ?? null;

        if ($choice === null) {
            return '';
        }

        return $choice['message']['content'] ?? '';
    }

    /**
     * Build the request payload for the BigModel API.
     *
     * @param  array<int, array{role: string, parts?: array, content?: string, tool_calls?: array, tool_call_id?: string}>  $messages
     * @param  array<int, array{type: string, function: array}>  $tools
     * @return array<string, mixed>
     */
    private function buildPayload(array $messages, array $tools): array
    {
        $formattedMessages = [];

        foreach ($messages as $message) {
            $role = $message['role'] ?? 'user';

            // Handle tool role messages
            if ($role === 'tool') {
                $formattedMessages[] = [
                    'role' => 'tool',
                    'content' => $message['content'] ?? json_encode($message['parts'][0]['functionResponse']['response'] ?? []),
                    'tool_call_id' => $message['tool_call_id'] ?? '',
                ];

                continue;
            }

            // Handle assistant/model role with tool calls
            if (in_array($role, ['assistant', 'model'], true)) {
                if (isset($message['tool_calls'])) {
                    $formattedMessages[] = [
                        'role' => 'assistant',
                        'content' => null,
                        'tool_calls' => $message['tool_calls'],
                    ];
                } else {
                    $formattedMessages[] = [
                        'role' => 'assistant',
                        'content' => $message['content'] ?? $this->extractTextFromParts($message['parts'] ?? []),
                    ];
                }

                continue;
            }

            // Handle user role
            $content = $message['content'] ?? $this->extractTextFromParts($message['parts'] ?? []);

            $formattedMessages[] = [
                'role' => 'user',
                'content' => $content,
            ];
        }

        $payload = [
            'model' => $this->model,
            'messages' => $formattedMessages,
            'temperature' => $this->temperature,
            'max_tokens' => $this->maxTokens,
        ];

        if (! empty($tools)) {
            $payload['tools'] = $tools;
            $payload['tool_choice'] = 'auto';
        }

        return $payload;
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
}
