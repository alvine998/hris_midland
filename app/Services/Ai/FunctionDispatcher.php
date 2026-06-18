<?php

namespace App\Services\Ai;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class FunctionDispatcher
{
    /**
     * The function registry instance.
     */
    protected FunctionRegistry $registry;

    /**
     * Create a new FunctionDispatcher instance.
     */
    public function __construct(FunctionRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Dispatch a function call from the AI.
     *
     * Looks up the function in the registry, verifies the user has permission,
     * instantiates the handler, and calls its execute method.
     *
     * @param  string  $functionName  The name of the function to call.
     * @param  array<string, mixed>  $args  The arguments to pass to the function.
     * @param  User  $user  The authenticated user making the request.
     * @return array{success: bool, data: mixed, error: ?string}
     */
    public function dispatch(string $functionName, array $args, User $user): array
    {
        Log::debug('FunctionDispatcher::dispatch — Dispatching function', [
            'function' => $functionName,
            'args' => $args,
            'user_id' => $user->id,
        ]);

        // 1. Look up the function in the registry.
        $definition = $this->registry->getFunction($functionName);

        // 2. If not found, return an error.
        if ($definition === null) {
            Log::warning('FunctionDispatcher::dispatch — Function not found', [
                'function' => $functionName,
            ]);

            return [
                'success' => false,
                'data' => null,
                'error' => 'Function not found',
            ];
        }

        // 3. Check permission if the function defines one.
        $permission = $definition['permission'] ?? '';

        if ($permission !== '' && ! $this->checkPermission($user, $permission)) {
            Log::warning('FunctionDispatcher::dispatch — Permission denied', [
                'function' => $functionName,
                'permission' => $permission,
                'user_id' => $user->id,
            ]);

            return [
                'success' => false,
                'data' => null,
                'error' => 'Permission denied',
            ];
        }

        // 4. Instantiate the function handler class.
        $className = $definition['class'] ?? null;

        if ($className === null || ! class_exists($className)) {
            Log::error('FunctionDispatcher::dispatch — Invalid handler class', [
                'function' => $functionName,
                'class' => $className,
            ]);

            return [
                'success' => false,
                'data' => null,
                'error' => 'Function handler not found',
            ];
        }

        try {
            $handler = app($className);

            // 5. Call the execute method with the user and the provided arguments
            //    spread as named parameters (matching the method parameter names).
            $result = $handler->execute($user, ...$args);

            Log::debug('FunctionDispatcher::dispatch — Function executed successfully', [
                'function' => $functionName,
            ]);

            return [
                'success' => true,
                'data' => $result,
                'error' => null,
            ];
        } catch (\Throwable $e) {
            Log::error('FunctionDispatcher::dispatch — Execution error', [
                'function' => $functionName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'data' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check whether the user has the given permission.
     */
    protected function checkPermission(User $user, string $permission): bool
    {
        // The User model has a hasPermission method that checks against role RBAC.
        if (method_exists($user, 'hasPermission')) {
            return $user->hasPermission($permission);
        }

        // For models with Spatie-style permission traits.
        if (method_exists($user, 'can')) {
            return $user->can($permission);
        }

        // Fallback: just verify the user is authenticated.
        return $user !== null;
    }
}
