<?php

namespace App\Services\Ai;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AlvineAiRouterClient
{
    protected string $baseUrl;

    protected string $model;

    protected ?string $apiKey;

    protected float $temperature;

    protected int $maxTokens;

    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.alvine_ai_router.base_url', 'https://alvineitsolutions.com/api/router-customers'), '/');
        $this->apiKey = config('services.alvine_ai_router.api_key');
        $this->model = (string) config('services.alvine_ai_router.model', 'auto');
        $this->temperature = (float) config('services.alvine_ai_router.temperature', 0.4);
        $this->maxTokens = (int) config('services.alvine_ai_router.max_tokens', 1024);
        $this->timeout = (int) config('services.alvine_ai_router.timeout', 25);
    }

    /**
     * Send an OpenAI-compatible chat completion request to the Alvine AI Router.
     *
     * @param  array<int, array{role: string, content: string}>  $messages
     * @param  string|null  $model  Override the default model (defaults to "auto" routing).
     * @return array{content: string, model: ?string, credits_used: ?float}
     *
     * @throws \RuntimeException When the API key is missing or the response is invalid.
     * @throws RequestException When the API returns a non-2xx status.
     */
    public function chat(array $messages, ?string $model = null): array
    {
        if (empty($this->apiKey)) {
            throw new \RuntimeException('AI API key is not configured. Set ALVINE_AI_ROUTER_KEY in your .env file.');
        }

        $payload = [
            'model' => ($model && $model !== 'auto') ? $model : $this->model,
            'stream' => false,
            'temperature' => $this->temperature,
            'max_tokens' => $this->maxTokens,
            'messages' => $messages,
        ];

        Log::debug('AlvineAiRouterClient::chat — Sending request', [
            'model' => $payload['model'],
            'message_count' => count($messages),
        ]);

        $response = Http::withHeaders($this->headers())
            ->timeout($this->timeout)
            ->withOptions(['read_timeout' => $this->timeout])
            ->post("{$this->baseUrl}/chat/completions", $payload);

        if ($response->failed()) {
            Log::error('AlvineAiRouterClient::chat — API request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            $response->throw();
        }

        $data = $response->json();

        // Reasoning models can burn the entire token budget while "thinking",
        // returning empty content with finish_reason=length. Retry once with
        // a larger budget so the user still gets an answer.
        if ($this->isEmptyLengthResponse($data)) {
            Log::warning('AlvineAiRouterClient::chat — Empty reply (length), retrying with more tokens');

            $payload['max_tokens'] = min($payload['max_tokens'] * 2, 8192);

            $response = Http::withHeaders($this->headers())
                ->timeout($this->timeout)
                ->withOptions(['read_timeout' => $this->timeout])
                ->post("{$this->baseUrl}/chat/completions", $payload);

            if ($response->failed()) {
                $response->throw();
            }

            $data = $response->json();
        }

        $content = $data['choices'][0]['message']['content'] ?? null;

        if (! is_string($content) || trim($content) === '') {
            Log::error('AlvineAiRouterClient::chat — Unexpected response shape', [
                'body' => mb_substr($response->body(), 0, 500),
            ]);

            throw new \RuntimeException('Unexpected response from the AI service.');
        }

        $creditsUsed = data_get($data, '_credits.credit_out');

        return [
            'content' => trim($content),
            'model' => $data['model'] ?? ($payload['model'] ?? null),
            'credits_used' => is_numeric($creditsUsed) ? (float) $creditsUsed : null,
        ];
    }

    /**
     * Stream a chat completion from the router, invoking $onDelta for each
     * content chunk as it arrives. Returns the assembled reply when done.
     *
     * @param  array<int, array{role: string, content: string}>  $messages
     * @param  callable(string): void  $onDelta
     * @param  string|null  $model  Override the default model (defaults to "auto" routing).
     * @return array{content: string, model: ?string, credits_used: ?float}
     *
     * @throws \RuntimeException When the API key is missing or the stream fails.
     */
    public function chatStream(array $messages, callable $onDelta, ?string $model = null): array
    {
        if (empty($this->apiKey)) {
            throw new \RuntimeException('AI API key is not configured. Set ALVINE_AI_ROUTER_KEY in your .env file.');
        }

        $payload = [
            'model' => ($model && $model !== 'auto') ? $model : $this->model,
            'stream' => true,
            'temperature' => $this->temperature,
            'max_tokens' => $this->maxTokens,
            'messages' => $messages,
        ];

        Log::debug('AlvineAiRouterClient::chatStream — Sending request', [
            'model' => $payload['model'],
            'message_count' => count($messages),
        ]);

        $response = Http::withHeaders($this->headers(sse: true))
            ->timeout($this->timeout)
            ->withOptions(['stream' => true, 'read_timeout' => $this->timeout])
            ->post("{$this->baseUrl}/chat/completions", $payload);

        if ($response->failed()) {
            Log::error('AlvineAiRouterClient::chatStream — API request failed', [
                'status' => $response->status(),
                'body' => mb_substr($response->body(), 0, 500),
            ]);

            $response->throw();
        }

        $content = '';
        $resolvedModel = $payload['model'];
        $creditsUsed = null;

        foreach ($this->iterateSseEvents($response->toPsrResponse()->getBody()) as $data) {
            if ($data === '[DONE]') {
                break;
            }

            $decoded = json_decode($data, true);

            if (! is_array($decoded)) {
                continue;
            }

            if (isset($decoded['_credits'])) {
                $out = data_get($decoded, '_credits.credit_out');
                $creditsUsed = is_numeric($out) ? (float) $out : $creditsUsed;
            }

            $delta = $decoded['choices'][0]['delta']['content']
                ?? $decoded['choices'][0]['message']['content']
                ?? null;

            if (is_string($delta) && $delta !== '') {
                $content .= $delta;

                // Allow the caller to stop the stream mid-flight (e.g. user
                // clicked "Stop"). Returning false aborts the read loop.
                if ($onDelta($delta) === false) {
                    break;
                }
            }

            $resolvedModel = $decoded['model'] ?? $resolvedModel;
        }

        if (trim($content) === '') {
            Log::error('AlvineAiRouterClient::chatStream — Stream produced no content');

            throw new \RuntimeException('The AI service returned an empty response.');
        }

        return [
            'content' => trim($content),
            'model' => $resolvedModel,
            'credits_used' => $creditsUsed,
        ];
    }

    protected function headers(bool $sse = false): array
    {
        return array_filter([
            'Authorization' => 'Bearer '.$this->apiKey,
            'Content-Type' => 'application/json',
            'Accept' => $sse ? 'text/event-stream' : null,
        ]);
    }

    /**
     * Detect a response where the model's reasoning consumed the whole token
     * budget before any visible content was produced.
     */
    protected function isEmptyLengthResponse(array $data): bool
    {
        $choice = $data['choices'][0] ?? [];

        return ($choice['finish_reason'] ?? null) === 'length'
            && blank($choice['message']['content'] ?? null);
    }

    /**
     * Yield each "data:" payload line from an SSE body.
     */
    protected function iterateSseEvents($body): \Generator
    {
        $buffer = '';

        // Bound how long we will sit waiting for bytes from the router. Guzzle's
        // read_timeout applies to the whole request, not to a single read, so
        // $body->read() can otherwise block on PHP's default_socket_timeout.
        $this->applyReadTimeout($body, $this->timeout);
        $deadline = microtime(true) + $this->timeout;

        while (! $body->eof()) {
            $chunk = $body->read(8192);

            if ($chunk === false || $chunk === '') {
                break;
            }

            $buffer .= $chunk;
            $deadline = microtime(true) + $this->timeout;

            while (($newline = strpos($buffer, "\n")) !== false) {
                $line = trim(substr($buffer, 0, $newline));
                $buffer = substr($buffer, $newline + 1);

                if (str_starts_with($line, 'data:')) {
                    yield trim(substr($line, 5));
                }
            }
        }

        if (microtime(true) >= $deadline && trim($buffer) === '') {
            Log::error('AlvineAiRouterClient::iterateSseEvents — Stream stalled with no data before timeout', [
                'timeout' => $this->timeout,
            ]);
        }

        $line = trim($buffer);

        if ($line !== '' && str_starts_with($line, 'data:')) {
            yield trim(substr($line, 5));
        }
    }

    /**
     * Apply a per-read timeout to the stream's underlying socket when possible.
     * Has no effect on stream wrappers that do not expose a socket resource.
     */
    protected function applyReadTimeout($body, int $seconds): void
    {
        if ($seconds < 1) {
            return;
        }

        if (! method_exists($body, 'getStream')) {
            return;
        }

        $resource = @stream_get_meta_data($body->getStream())['stream'] ?? null;

        if (is_resource($resource)) {
            @stream_set_timeout($resource, $seconds);
        }
    }
}
