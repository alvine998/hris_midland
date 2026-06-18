<?php

namespace App\Services\Ai;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiClient
{
    /**
     * Base URL for the Gemini API.
     */
    private const BASE_URL = 'https://generativelanguage.googleapis.com/v1beta/models';

    /**
     * The configured model name.
     */
    protected string $model;

    /**
     * The API key.
     */
    protected string $apiKey;

    /**
     * The temperature setting for generation.
     */
    protected float $temperature;

    /**
     * The maximum number of output tokens.
     */
    protected int $maxTokens;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->model = config('services.gemini.model', 'gemini-2.0-flash');
        $this->temperature = (float) config('services.gemini.temperature', 0.2);
        $this->maxTokens = (int) config('services.gemini.max_tokens', 1024);
    }

    /**
     * Send a chat request to the Gemini API with optional function declarations as tools.
     *
     * Accepts an array of messages where each message has:
     *   - 'role': 'user' | 'model' | 'function'
     *   - 'parts': array of part objects (text, functionCall, functionResponse)
     *
     * Tools should be an array of function declarations. Use
     * buildFunctionDeclaration() to convert your own schemas into the expected format.
     *
     * @param  array<int, array{role: string, parts: array}>  $messages
     * @param  array<int, array{functionDeclarations: array}>  $tools
     * @return array<string, mixed>
     *
     * @throws \RuntimeException When the API key is missing.
     * @throws RequestException When the API returns a non-2xx status.
     */
    public function chat(array $messages, array $tools = []): array
    {
        if (empty($this->apiKey)) {
            throw new \RuntimeException('Gemini API key is not configured. Set GEMINI_API_KEY in your .env file.');
        }

        $url = sprintf('%s/%s:generateContent', self::BASE_URL, $this->model);

        $payload = $this->buildPayload($messages, $tools);

        Log::debug('GeminiClient::chat — Sending request', [
            'model' => $this->model,
            'message_count' => count($messages),
            'tool_count' => count($tools),
        ]);

        $response = Http::withHeaders([
            'x-goog-api-key' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($url, $payload);

        if ($response->failed()) {
            Log::error('GeminiClient::chat — API request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            $response->throw();
        }

        $data = $response->json();

        Log::debug('GeminiClient::chat — Response received', [
            'has_candidates' => isset($data['candidates']),
        ]);

        return $data;
    }

    /**
     * Convert an array of function definitions into Gemini tool declarations.
     *
     * Each entry in $functions should be an associative array with:
     *   - 'name'        (string)  – the function name the model may call
     *   - 'description' (string)  – a description of what the function does
     *   - 'parameters'  (array)   – a JSON Schema object describing the parameters
     *
     * Example:
     *  [
     *      'name'        => 'get_weather',
     *      'description' => 'Get the current weather for a location',
     *      'parameters'  => [
     *          'type'       => 'object',
     *          'properties' => [
     *              'location' => [
     *                  'type'        => 'string',
     *                  'description' => 'City or region',
     *              ],
     *          ],
     *          'required'   => ['location'],
     *      ],
     *  ]
     *
     * @param  array<int, array{name: string, description: string, parameters: array}>  $functions
     * @return array<int, array{functionDeclarations: array}>
     */
    public function buildFunctionDeclaration(array $functions): array
    {
        if (empty($functions)) {
            return [];
        }

        $declarations = [];

        foreach ($functions as $function) {
            $declarations[] = [
                'name' => $function['name'],
                'description' => $function['description'] ?? '',
                'parameters' => $function['parameters'] ?? [
                    'type' => 'object',
                    'properties' => [],
                ],
            ];
        }

        return [
            [
                'functionDeclarations' => $declarations,
            ],
        ];
    }

    /**
     * Extract a function call from a Gemini API response, if one is present.
     *
     * Returns null when the model responded with text instead of a function call,
     * or when the response structure is unexpected.
     *
     * @param  array<string, mixed>  $response  The full API response from chat()
     * @return array{name: string, args: array<string, mixed>}|null
     */
    public function extractFunctionCall(array $response): ?array
    {
        $candidate = $response['candidates'][0] ?? null;

        if ($candidate === null) {
            return null;
        }

        $parts = $candidate['content']['parts'] ?? [];

        foreach ($parts as $part) {
            if (isset($part['functionCall'])) {
                $functionCall = $part['functionCall'];

                return [
                    'name' => $functionCall['name'] ?? '',
                    'args' => $functionCall['args'] ?? [],
                ];
            }
        }

        return null;
    }

    /**
     * Extract the text content from a Gemini API response.
     *
     * Returns an empty string when the response contains a function call instead
     * of text, or when no text is present.
     *
     * @param  array<string, mixed>  $response  The full API response from chat()
     */
    public function extractText(array $response): string
    {
        $candidate = $response['candidates'][0] ?? null;

        if ($candidate === null) {
            return '';
        }

        $parts = $candidate['content']['parts'] ?? [];

        foreach ($parts as $part) {
            if (isset($part['text'])) {
                return $part['text'];
            }
        }

        return '';
    }

    /**
     * Build the request payload for the Gemini API.
     *
     * @param  array<int, array{role: string, parts: array}>  $messages
     * @param  array<int, array{functionDeclarations: array}>  $tools
     * @return array<string, mixed>
     */
    private function buildPayload(array $messages, array $tools): array
    {
        // Convert messages to Gemini's expected "contents" format.
        $contents = [];

        foreach ($messages as $message) {
            $role = $message['role'] ?? 'user';

            // Gemini uses 'model' not 'assistant'
            if ($role === 'assistant') {
                $role = 'model';
            }

            // Skip roles that aren't part of the contents array
            if (! in_array($role, ['user', 'model', 'function'], true)) {
                continue;
            }

            $contents[] = [
                'role' => $role,
                'parts' => $message['parts'],
            ];
        }

        $payload = [
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => $this->temperature,
                'maxOutputTokens' => $this->maxTokens,
            ],
        ];

        if (! empty($tools)) {
            $payload['tools'] = $tools;
        }

        return $payload;
    }
}
