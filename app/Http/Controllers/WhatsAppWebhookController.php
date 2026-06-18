<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppWebhookService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WhatsAppWebhookController extends Controller
{
    public function __construct(
        protected WhatsAppWebhookService $webhookService
    ) {}

    /**
     * Verify the webhook endpoint (WhatsApp Cloud API verification).
     *
     * GET request from WhatsApp to verify the webhook URL.
     */
    public function verify(Request $request): Response
    {
        $mode = $request->input('hub_mode');
        $token = $request->input('hub_verify_token');
        $challenge = $request->input('hub_challenge');

        $verifyToken = config('services.whatsapp.verify_token');

        if ($mode === 'subscribe' && $token === $verifyToken) {
            return response($challenge, 200);
        }

        return response('Forbidden', 403);
    }

    /**
     * Handle incoming WhatsApp webhook events.
     *
     * GET: WhatsApp Cloud API verification
     * POST: Message and status updates
     */
    public function handle(Request $request): Response
    {
        if ($request->isMethod('get')) {
            return $this->verify($request);
        }

        $payload = $request->all();

        // Log the raw payload for debugging
        logger()->info('WhatsApp webhook received', $payload);

        // Check if payload contains messages
        if (data_get($payload, 'entry.0.changes.0.value.messages')) {
            $this->webhookService->handleIncomingMessage($payload);
        }

        // Check if payload contains status updates
        if (data_get($payload, 'entry.0.changes.0.value.statuses')) {
            $this->webhookService->handleStatusUpdate($payload);
        }

        // Always return 200 OK to acknowledge receipt
        return response('EVENT_RECEIVED', 200);
    }
}
