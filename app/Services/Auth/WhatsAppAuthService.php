<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\WhatsAppSession;
use App\Services\LoginAttemptService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class WhatsAppAuthService
{
    /**
     * Cache TTL for temporary email storage (minutes).
     */
    private const EMAIL_CACHE_TTL = 5;

    /**
     * Max failed attempts before rate limiting kicks in.
     */
    private const MAX_FAILED_ATTEMPTS = 5;

    /**
     * Rate limit lockout duration (minutes).
     */
    private const LOCKOUT_MINUTES = 15;

    /**
     * The login attempt service instance.
     */
    private ?LoginAttemptService $loginAttemptService;

    /**
     * Create a new WhatsAppAuthService instance.
     */
    public function __construct(?LoginAttemptService $loginAttemptService = null)
    {
        $this->loginAttemptService = $loginAttemptService;
    }

    /**
     * Main entry point — handles an incoming WhatsApp message for authentication.
     *
     * Routes the message based on the current session state:
     * - unauthenticated → starts auth flow (prompts for email)
     * - awaiting_email   → validates email, prompts for password
     * - awaiting_password → validates password, completes auth
     * - authenticated    → returns authenticated immediately
     *
     * @return array{authenticated: bool, message: string, user: ?User}
     */
    public function handle(string $phoneNumber, string $message): array
    {
        $session = $this->getSession($phoneNumber);

        // If the session has expired, reset it to unauthenticated
        if ($session->isExpired()) {
            $session->update([
                'state' => 'unauthenticated',
                'user_id' => null,
                'expires_at' => null,
            ]);
            $this->clearCache($phoneNumber);

            Log::info('WhatsApp session expired, reset to unauthenticated', [
                'phone' => $phoneNumber,
            ]);
        }

        // Already authenticated and session still valid
        if ($session->isActive()) {
            $session->touchActivity();

            return [
                'authenticated' => true,
                'message' => 'You are already authenticated.',
                'user' => $session->user,
            ];
        }

        // Route based on current state
        return match ($session->state) {
            'unauthenticated' => $this->promptEmail($phoneNumber),
            'awaiting_email' => $this->validateEmail($phoneNumber, $message),
            'awaiting_password' => $this->validatePassword($phoneNumber, $message),
            default => $this->resetAndPrompt($phoneNumber),
        };
    }

    /**
     * Start the authentication flow by prompting for an email address.
     *
     * Transitions state: unauthenticated → awaiting_email
     *
     * @return array{authenticated: false, message: string, user: null}
     */
    public function promptEmail(string $phoneNumber): array
    {
        $session = $this->getSession($phoneNumber);
        $session->update(['state' => 'awaiting_email']);

        Log::info('WhatsApp auth: prompted for email', [
            'phone' => $phoneNumber,
        ]);

        return [
            'authenticated' => false,
            'message' => 'Please enter your registered email address to log in.',
            'user' => null,
        ];
    }

    /**
     * Validate the provided email, look up the user, and prompt for a password.
     *
     * Transitions state: awaiting_email → awaiting_password (on success)
     *
     * @return array{authenticated: bool, message: string, user: ?User}
     */
    public function validateEmail(string $phoneNumber, string $email): array
    {
        $session = $this->getSession($phoneNumber);

        // Basic email format validation
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'authenticated' => false,
                'message' => 'That does not look like a valid email address. Please try again.',
                'user' => null,
            ];
        }

        // Check rate limiting
        if ($this->isRateLimited($phoneNumber)) {
            return [
                'authenticated' => false,
                'message' => $this->rateLimitMessage(),
                'user' => null,
            ];
        }

        // Look up user by email
        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->recordFailedAttempt($phoneNumber);

            Log::warning('WhatsApp auth: email not found', [
                'phone' => $phoneNumber,
                'email' => $email,
            ]);

            return [
                'authenticated' => false,
                'message' => 'No account found with that email address. Please check and try again.',
                'user' => null,
            ];
        }

        // Store email temporarily in cache (5-minute TTL)
        Cache::put(
            "wa_auth_email:{$phoneNumber}",
            $email,
            now()->addMinutes(self::EMAIL_CACHE_TTL)
        );

        // Update session state to awaiting_password
        $session->update([
            'state' => 'awaiting_password',
            'user_id' => $user->id,
        ]);

        Log::info('WhatsApp auth: email validated, prompting for password', [
            'phone' => $phoneNumber,
            'email' => $email,
            'user_id' => $user->id,
        ]);

        return [
            'authenticated' => false,
            'message' => 'Email found. Please enter your password.',
            'user' => null,
        ];
    }

    /**
     * Validate the provided password and complete authentication.
     *
     * Transitions state: awaiting_password → authenticated (on success)
     *
     * @return array{authenticated: bool, message: string, user: ?User}
     */
    public function validatePassword(string $phoneNumber, string $password): array
    {
        $session = $this->getSession($phoneNumber);

        // Retrieve email from cache
        $email = Cache::get("wa_auth_email:{$phoneNumber}");

        // If email is missing from cache, the session has expired
        if (! $email) {
            $session->update([
                'state' => 'awaiting_email',
                'user_id' => null,
            ]);

            return [
                'authenticated' => false,
                'message' => 'Your session has expired. Please start again by entering your email address.',
                'user' => null,
            ];
        }

        // Check rate limiting
        if ($this->isRateLimited($phoneNumber)) {
            return [
                'authenticated' => false,
                'message' => $this->rateLimitMessage(),
                'user' => null,
            ];
        }

        // Re-fetch user (may have been deleted between steps)
        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->resetSession($session, $phoneNumber);

            return [
                'authenticated' => false,
                'message' => 'Something went wrong. Please start again by entering your email address.',
                'user' => null,
            ];
        }

        // Verify password
        if (! Hash::check($password, $user->password)) {
            $this->recordFailedAttempt($phoneNumber);

            if ($this->loginAttemptService) {
                $this->loginAttemptService->recordFailed($user);
            }

            Log::warning('WhatsApp auth: invalid password', [
                'phone' => $phoneNumber,
                'email' => $email,
                'user_id' => $user->id,
            ]);

            return [
                'authenticated' => false,
                'message' => 'Incorrect password. Please try again.',
                'user' => null,
            ];
        }

        // Password is correct — complete authentication
        $session->update([
            'state' => 'authenticated',
            'last_activity_at' => now(),
            'expires_at' => now()->addMinutes(
                (int) config('services.whatsapp_ai.session_ttl_minutes', 30)
            ),
        ]);

        // Clear temporary cache
        $this->clearCache($phoneNumber);

        // Record successful login
        if ($this->loginAttemptService) {
            $this->loginAttemptService->recordSuccessful($user);
        }

        Log::info('WhatsApp auth: authentication successful', [
            'phone' => $phoneNumber,
            'email' => $email,
            'user_id' => $user->id,
        ]);

        return [
            'authenticated' => true,
            'message' => 'Login successful! Welcome back.',
            'user' => $user,
        ];
    }

    /**
     * Get or create a WhatsApp session for the given phone number.
     */
    public function getSession(string $phoneNumber): WhatsAppSession
    {
        return WhatsAppSession::firstOrCreate(
            ['phone' => $phoneNumber],
            [
                'state' => 'unauthenticated',
                'last_activity_at' => now(),
            ]
        );
    }

    /**
     * Log out the user by clearing the session and cache.
     */
    public function logout(string $phoneNumber): void
    {
        $this->clearCache($phoneNumber);

        WhatsAppSession::where('phone', $phoneNumber)->update([
            'state' => 'unauthenticated',
            'user_id' => null,
            'expires_at' => null,
        ]);

        Log::info('WhatsApp auth: user logged out', [
            'phone' => $phoneNumber,
        ]);
    }

    // -----------------------------------------------------------------------
    //  Internal helpers
    // -----------------------------------------------------------------------

    /**
     * Clear all temporary cache entries for the given phone number.
     */
    private function clearCache(string $phoneNumber): void
    {
        Cache::forget("wa_auth:{$phoneNumber}");
        Cache::forget("wa_auth_email:{$phoneNumber}");
    }

    /**
     * Reset session and cache back to the unauthenticated state.
     */
    private function resetSession(WhatsAppSession $session, string $phoneNumber): void
    {
        $session->update([
            'state' => 'unauthenticated',
            'user_id' => null,
            'expires_at' => null,
        ]);

        $this->clearCache($phoneNumber);
    }

    /**
     * Reset the session and prompt the user to start over.
     *
     * @return array{authenticated: false, message: string, user: null}
     */
    private function resetAndPrompt(string $phoneNumber): array
    {
        $this->resetSession($this->getSession($phoneNumber), $phoneNumber);

        return [
            'authenticated' => false,
            'message' => 'Something went wrong with your session. Please start again by entering your email address.',
            'user' => null,
        ];
    }

    /**
     * Check if the given phone number is currently rate-limited.
     */
    private function isRateLimited(string $phoneNumber): bool
    {
        $attempts = (int) Cache::get("wa_auth:{$phoneNumber}", 0);

        return $attempts >= self::MAX_FAILED_ATTEMPTS;
    }

    /**
     * Get the rate-limit lockout message.
     */
    private function rateLimitMessage(): string
    {
        return 'Too many failed attempts. Please try again in '.self::LOCKOUT_MINUTES.' minutes.';
    }

    /**
     * Record a failed login attempt for rate limiting.
     */
    private function recordFailedAttempt(string $phoneNumber): void
    {
        $attempts = (int) Cache::get("wa_auth:{$phoneNumber}", 0);
        Cache::put(
            "wa_auth:{$phoneNumber}",
            $attempts + 1,
            now()->addMinutes(self::LOCKOUT_MINUTES)
        );
    }
}
