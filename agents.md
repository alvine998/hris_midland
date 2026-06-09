# AGENTS.md — Laravel 12 Project Guide

## Project Stack

* Framework: Laravel 12
* Language: PHP 8.4+
* Package manager: Composer
* Frontend: Vite, Blade, optional React/Vue
* Database: MySQL
* Testing: Pest or PHPUnit
* Code style: Laravel Pint
* Notification: Toast

Laravel includes routing, ORM, migrations, queues, validation, storage, testing, and background jobs by default.

## Agent Rules

1. Follow Laravel conventions first.
2. Do not create custom architecture unless needed.
3. Prefer Service classes for business logic.
4. Prefer Form Request classes for validation.
5. Use Eloquent relationships instead of raw SQL when possible.
6. Use migrations for all database changes.
7. Never edit `.env` directly unless requested.
8. Never commit secrets, tokens, passwords, or private keys.
9. Keep controllers thin.
10. Keep models focused on relationships, casts, scopes, and attributes.
11. Do not use alret confirm for every confirmation, use modal to confirm.

## Common Commands

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

For frontend:

```bash
npm install
npm run dev
```

For production build:

```bash
npm run build
```

Laravel also supports running local dev services through Composer scripts such as `composer run dev`.

## Testing Commands

```bash
php artisan test
```

Run specific test:

```bash
php artisan test --filter=UserTest
```

## Code Style

Before finishing changes, run:

```bash
./vendor/bin/pint
```

## Recommended Folder Pattern

```txt
app/
  Http/
    Controllers/
    Requests/
    Resources/
  Models/
  Services/
  Actions/
  Jobs/
  Policies/
  Observers/
  Enums/
database/
  migrations/
  seeders/
routes/
  web.php
  api.php
tests/
  Feature/
  Unit/
```

## Controller Rules

Controllers should only handle:

* Receiving request
* Calling service/action
* Returning response

Example:

```php
public function store(StoreUserRequest $request, UserService $service)
{
    $user = $service->create($request->validated());

    return response()->json([
        'message' => 'User created successfully',
        'data' => $user,
    ]);
}
```

## Service Rules

Use services for business logic.

```php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function create(array $data): User
    {
        $data['password'] = Hash::make($data['password']);

        return User::create($data);
    }
}
```

## Validation Rules

Use Form Requests.

```bash
php artisan make:request StoreUserRequest
```

Example:

```php
public function rules(): array
{
    return [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'unique:users,email'],
        'password' => ['required', 'string', 'min:8'],
    ];
}
```

## API Response Pattern

Use consistent JSON:

```php
return response()->json([
    'message' => 'Success',
    'data' => $data,
]);
```

For errors:

```php
return response()->json([
    'message' => 'Something went wrong',
    'errors' => $errors,
], 422);
```

## Database Rules

* Use migrations.
* Use foreign keys.
* Use soft deletes only when data recovery is needed.
* Use indexes for searchable columns.
* Avoid changing old migrations after they are deployed.

Create model with migration:

```bash
php artisan make:model Product -m
```

## Eloquent Rules

Use relationships:

```php
public function company()
{
    return $this->belongsTo(Company::class);
}
```

Use casts:

```php
protected $casts = [
    'metadata' => 'array',
    'is_active' => 'boolean',
];
```

Use scopes:

```php
public function scopeActive($query)
{
    return $query->where('is_active', true);
}
```

## Queue Rules

Use jobs for slow processes:

```bash
php artisan make:job SendUserWelcomeEmail
```

Run worker:

```bash
php artisan queue:work
```

Use queues for:

* Email sending
* Report generation
* File processing
* Notifications
* External API sync

## Scheduler Rules

Define scheduled tasks in `routes/console.php`.

Example:

```php
Schedule::command('report:daily')->daily();
```

Run scheduler:

```bash
php artisan schedule:run
```

## Security Rules

* Validate every input.
* Authorize sensitive actions with policies/gates.
* Hash passwords using Laravel Hash.
* Never expose stack traces in production.
* Use signed URLs for temporary private access.
* Use CSRF protection for web routes.
* Use Sanctum for API authentication when suitable.

## Skill: CRUD Generation

When asked to create CRUD:

1. Create migration.
2. Create model.
3. Create Form Requests.
4. Create Controller.
5. Create Service.
6. Add routes.
7. Add tests.
8. Return consistent JSON.

## Skill: API Endpoint

When asked to create API endpoint:

1. Add route in `routes/api.php`.
2. Create request validation.
3. Use controller method.
4. Delegate logic to service/action.
5. Return JSON response.
6. Add feature test.

## Skill: Authentication

Prefer Laravel Sanctum for simple API authentication.

Use:

```bash
php artisan install:api
```

Then protect routes:

```php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn (Request $request) => $request->user());
});
```

## Skill: Debugging

When debugging:

1. Check logs in `storage/logs/laravel.log`.
2. Check `.env` configuration.
3. Clear cache if config changed:

```bash
php artisan optimize:clear
```

4. Re-run migrations if database issue:

```bash
php artisan migrate:status
```

## Skill: Deployment

Before deployment:

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```

After deployment:

```bash
php artisan queue:restart
```

## Skill: Testing

For every important feature:

* Add Feature test for endpoint behavior.
* Add Unit test for business logic.
* Test validation errors.
* Test authorization.
* Test successful response.

## Do Not Do

* Do not place business logic inside routes.
* Do not write raw SQL unless necessary.
* Do not expose `.env`.
* Do not skip validation.
* Do not modify vendor files.
* Do not hardcode URLs, API keys, or credentials.
* Do not create unnecessary abstractions.

## Final Checklist

Before completing any task:

```bash
php artisan test
./vendor/bin/pint
php artisan optimize:clear
```

Confirm:

* Code follows Laravel conventions.
* Validation exists.
* Authorization exists when needed.
* Tests pass.
* No secrets are committed.
* No breaking migration changes are made.
