<?php

namespace App\Services;

use App\Services\Ai\ConversationManager;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookService
{
    public function __construct(
        protected ConversationManager $conversationManager
    ) {}

    /**
     * Process incoming WhatsApp webhook payload.
     */
    public function handleIncomingMessage(array $payload): void
    {
        $entry = data_get($payload, 'entry', []);

        foreach ($entry as $entryData) {
            $changes = data_get($entryData, 'changes', []);

            foreach ($changes as $change) {
                $value = data_get($change, 'value', []);
                $messages = data_get($value, 'messages', []);

                foreach ($messages as $message) {
                    $this->processMessage($message, $value);
                }
            }
        }
    }

    /**
     * Process a single message from the webhook payload.
     */
    protected function processMessage(array $message, array $value): void
    {
        $from = data_get($message, 'from');
        $messageId = data_get($message, 'id');
        $timestamp = data_get($message, 'timestamp');
        $messageType = data_get($message, 'type', 'text');

        Log::info('WhatsApp message received', [
            'from' => $from,
            'message_id' => $messageId,
            'type' => $messageType,
            'timestamp' => $timestamp,
        ]);

        match ($messageType) {
            'text' => $this->handleTextMessage($message, $value),
            'image' => $this->handleMediaMessage($message, $value, 'image'),
            'audio' => $this->handleMediaMessage($message, $value, 'audio'),
            'video' => $this->handleMediaMessage($message, $value, 'video'),
            'document' => $this->handleMediaMessage($message, $value, 'document'),
            'location' => $this->handleLocationMessage($message, $value),
            'contacts' => $this->handleContactsMessage($message, $value),
            'button' => $this->handleButtonMessage($message, $value),
            'interactive' => $this->handleInteractiveMessage($message, $value),
            'reaction' => $this->handleReactionMessage($message, $value),
            default => Log::warning('Unhandled WhatsApp message type', ['type' => $messageType]),
        };
    }

    /**
     * Handle incoming text message.
     */
    protected function handleTextMessage(array $message, array $value): void
    {
        $body = data_get($message, 'text.body', '');
        $from = data_get($message, 'from');

        Log::info('WhatsApp text message', [
            'from' => $from,
            'body' => $body,
        ]);

        // Process through AI conversation manager
        $response = $this->conversationManager->handleMessage($from, $body);

        // Send AI response back to user
        $this->sendTextMessage($from, $response);
    }

    /**
     * Handle incoming media message (image, audio, video, document).
     */
    protected function handleMediaMessage(array $message, array $value, string $type): void
    {
        $media = data_get($message, $type, []);
        $from = data_get($message, 'from');
        $mediaId = data_get($media, 'id');
        $mimeType = data_get($media, 'mime_type');
        $caption = data_get($media, 'caption');

        Log::info("WhatsApp {$type} message", [
            'from' => $from,
            'media_id' => $mediaId,
            'mime_type' => $mimeType,
            'caption' => $caption,
        ]);

        // TODO: Download media using WhatsApp Media API
        // Endpoint: GET /v1/{phone-number-id}/media/{media-id}
    }

    /**
     * Handle incoming location message.
     */
    protected function handleLocationMessage(array $message, array $value): void
    {
        $location = data_get($message, 'location', []);
        $from = data_get($message, 'from');

        Log::info('WhatsApp location message', [
            'from' => $from,
            'latitude' => data_get($location, 'latitude'),
            'longitude' => data_get($location, 'longitude'),
            'name' => data_get($location, 'name'),
            'address' => data_get($location, 'address'),
        ]);
    }

    /**
     * Handle incoming contacts message.
     */
    protected function handleContactsMessage(array $message, array $value): void
    {
        $contacts = data_get($message, 'contacts', []);
        $from = data_get($message, 'from');

        Log::info('WhatsApp contacts message', [
            'from' => $from,
            'contacts' => $contacts,
        ]);
    }

    /**
     * Handle incoming button response message.
     */
    protected function handleButtonMessage(array $message, array $value): void
    {
        $button = data_get($message, 'button', []);
        $from = data_get($message, 'from');

        Log::info('WhatsApp button response', [
            'from' => $from,
            'payload' => data_get($button, 'payload'),
            'text' => data_get($button, 'text'),
        ]);
    }

    /**
     * Handle incoming interactive message (list, reply button).
     */
    protected function handleInteractiveMessage(array $message, array $value): void
    {
        $interactive = data_get($message, 'interactive', []);
        $from = data_get($message, 'from');
        $type = data_get($interactive, 'type');

        Log::info('WhatsApp interactive message', [
            'from' => $from,
            'interactive_type' => $type,
            'data' => $interactive,
        ]);
    }

    /**
     * Handle incoming reaction message.
     */
    protected function handleReactionMessage(array $message, array $value): void
    {
        $reaction = data_get($message, 'reaction', []);
        $from = data_get($message, 'from');

        Log::info('WhatsApp reaction', [
            'from' => $from,
            'message_id' => data_get($reaction, 'message_id'),
            'emoji' => data_get($reaction, 'emoji'),
        ]);
    }

    /**
     * Handle message status updates (sent, delivered, read, failed).
     */
    public function handleStatusUpdate(array $payload): void
    {
        $entry = data_get($payload, 'entry', []);

        foreach ($entry as $entryData) {
            $changes = data_get($entryData, 'changes', []);

            foreach ($changes as $change) {
                $value = data_get($change, 'value', []);
                $statuses = data_get($value, 'statuses', []);

                foreach ($statuses as $status) {
                    $this->processStatus($status);
                }
            }
        }
    }

    /**
     * Process a single status update.
     */
    protected function processStatus(array $status): void
    {
        $messageId = data_get($status, 'id');
        $recipientId = data_get($status, 'recipient_id');
        $statusValue = data_get($status, 'status');

        Log::info('WhatsApp message status update', [
            'message_id' => $messageId,
            'recipient_id' => $recipientId,
            'status' => $statusValue,
        ]);

        // TODO: Update message status in your database
    }

    /**
     * Send a text message via WhatsApp Cloud API.
     *
     * @param  string  $to  Recipient phone number (with country code)
     * @param  string  $body  Message text
     */
    public function sendTextMessage(string $to, string $body): array
    {
        $phoneNumberId = config('services.whatsapp.phone_number_id');
        $accessToken = config('services.whatsapp.access_token');

        if (! $phoneNumberId || ! $accessToken) {
            Log::error('WhatsApp API credentials not configured', [
                'phone_number_id_set' => ! empty($phoneNumberId),
                'access_token_set' => ! empty($accessToken),
            ]);

            return ['error' => 'WhatsApp API credentials not configured'];
        }

        try {
            $response = Http::withToken($accessToken)
                ->throw()
                ->post("https://graph.facebook.com/v18.0/{$phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => $to,
                    'type' => 'text',
                    'text' => [
                        'body' => $body,
                    ],
                ]);

            Log::info('WhatsApp message sent successfully', [
                'to' => $to,
                'message_id' => data_get($response->json(), 'messages.0.id'),
            ]);

            return $response->json();
        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error('WhatsApp API request failed', [
                'to' => $to,
                'status' => $e->response->status(),
                'body' => $e->response->body(),
            ]);

            return ['error' => $e->response->body()];
        }
    }

    /**
     * Send a template message via WhatsApp Cloud API.
     *
     * @param  string  $to  Recipient phone number (with country code)
     * @param  string  $templateName  Template name
     * @param  string  $languageCode  Language code (e.g., 'en', 'id')
     * @param  array  $components  Template components with variables
     */
    public function sendTemplateMessage(string $to, string $templateName, string $languageCode = 'en', array $components = []): array
    {
        $phoneNumberId = config('services.whatsapp.phone_number_id');
        $accessToken = config('services.whatsapp.access_token');

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => [
                    'code' => $languageCode,
                ],
            ],
        ];

        if (! empty($components)) {
            $payload['template']['components'] = $components;
        }

        $response = Http::withToken($accessToken)
            ->post("https://graph.facebook.com/v18.0/{$phoneNumberId}/messages", $payload);

        return $response->json();
    }
}
