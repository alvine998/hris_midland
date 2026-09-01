<?php

namespace Tests\Feature;

use App\Models\AiChatMessage;
use App\Models\AiChatSession;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiChatTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.alvine_ai_router.api_key', 'sk-test-key');
        $this->user = User::factory()->create();
        $role = Role::create(['name' => 'Super Admin', 'description' => 'Full system access', 'rbac' => ['*']]);
        UserRole::create(['user_id' => $this->user->id, 'role_id' => $role->id]);
        $this->user->load('userRoles.role');
    }

    public function test_guests_cannot_access_ai_chat(): void
    {
        $this->get(route('ai-chat.index'))->assertRedirect(route('login'));
    }

    public function test_chat_page_renders_for_authenticated_user(): void
    {
        $this->actingAs($this->user)
            ->get(route('ai-chat.index'))
            ->assertOk()
            ->assertSee('AI Assistant');
    }

    public function test_user_can_create_a_session(): void
    {
        $response = $this->actingAs($this->user)->postJson(route('ai-chat.sessions.store'));

        $response->assertCreated()
            ->assertJsonPath('data.title', 'New chat');

        $this->assertDatabaseHas('ai_chat_sessions', [
            'user_id' => $this->user->id,
            'title' => 'New chat',
            'model' => 'auto',
        ]);
    }

    public function test_send_stores_user_and_assistant_messages(): void
    {
        Http::fake([
            '*/chat/completions' => Http::response([
                'model' => 'muse/muse-spark-1.2-contributor',
                'choices' => [
                    ['message' => ['role' => 'assistant', 'content' => 'Hello! How can I help you today?']],
                ],
                '_credits' => ['balance' => 499, 'credit_out' => 1],
            ]),
        ]);

        $session = AiChatSession::create(['user_id' => $this->user->id, 'title' => 'New chat']);

        $response = $this->actingAs($this->user)
            ->postJson(route('ai-chat.sessions.send', $session), ['content' => 'Hi there']);

        $response->assertOk()
            ->assertJsonPath('data.reply.role', 'assistant')
            ->assertJsonPath('data.reply.content', 'Hello! How can I help you today?');

        $this->assertDatabaseHas('ai_chat_messages', [
            'ai_chat_session_id' => $session->id,
            'role' => 'user',
            'content' => 'Hi there',
        ]);

        $assistant = AiChatMessage::query()
            ->where('ai_chat_session_id', $session->id)
            ->where('role', 'assistant')
            ->first();

        $this->assertSame('Hello! How can I help you today?', $assistant->content);
        $this->assertSame(1.0, (float) $assistant->credits_used);

        $session->refresh();
        $this->assertNotEquals('New chat', $session->title);
        $this->assertNotNull($session->last_message_at);

        Http::assertSent(fn ($request) => str_contains(
            $request->url(),
            'https://token-plan-sgp.xiaomimimo.com/v1/chat/completions'
        ) && $request->header('Authorization')[0] === 'Bearer sk-test-key');
    }

    public function test_memory_window_is_sent_to_router(): void
    {
        Http::fake([
            '*/chat/completions' => Http::response([
                'choices' => [['message' => ['role' => 'assistant', 'content' => 'Noted.']]],
            ]),
        ]);

        $session = AiChatSession::create(['user_id' => $this->user->id, 'title' => 'Remember this']);
        $session->update(['memory' => '- User prefers short answers']);
        $session->messages()->createMany([
            ['role' => 'user', 'content' => 'My name is Alvine'],
            ['role' => 'assistant', 'content' => 'Nice to meet you!'],
        ]);

        $this->actingAs($this->user)
            ->postJson(route('ai-chat.sessions.send', $session), ['content' => 'What is my name?'])
            ->assertOk();

        Http::assertSent(function ($request) {
            $messages = $request->data()['messages'];
            $roles = array_column($messages, 'role');
            $contents = implode("\n", array_column($messages, 'content'));

            return $roles[0] === 'system'
                && str_contains($contents, 'User prefers short answers')
                && str_contains($contents, 'My name is Alvine')
                && in_array('user', $roles, true)
                && end($messages)['content'] === 'What is my name?';
        });
    }

    public function test_user_cannot_use_another_users_session(): void
    {
        $other = AiChatSession::create(['user_id' => User::factory()->create()->id, 'title' => 'Private']);

        $this->actingAs($this->user)
            ->postJson(route('ai-chat.sessions.send', $other), ['content' => 'Hello?'])
            ->assertForbidden();

        $this->actingAs($this->user)
            ->deleteJson(route('ai-chat.sessions.destroy', $other))
            ->assertForbidden();

        $this->actingAs($this->user)
            ->getJson(route('ai-chat.sessions.messages', $other))
            ->assertForbidden();
    }

    public function test_validation_rejects_empty_message(): void
    {
        $session = AiChatSession::create(['user_id' => $this->user->id, 'title' => 'New chat']);

        $this->actingAs($this->user)
            ->postJson(route('ai-chat.sessions.send', $session), ['content' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('content');
    }

    public function test_streaming_endpoint_emits_deltas_and_persists_reply(): void
    {
        Http::fake([
            '*/chat/completions' => Http::response(
                "data: {\"choices\":[{\"delta\":{\"content\":\"Hel\"}}]}\n\n".
                "data: {\"model\":\"muse-spark\",\"choices\":[{\"delta\":{\"content\":\"lo!\"}}],\"_credits\":{\"credit_out\":2}}\n\n".
                "data: [DONE]\n\n"
            ),
        ]);

        $session = AiChatSession::create(['user_id' => $this->user->id, 'title' => 'New chat']);

        $response = $this->actingAs($this->user)
            ->post(route('ai-chat.sessions.stream', $session), ['content' => 'Hi']);

        $response->assertOk();

        $streamed = $response->streamedContent();
        $this->assertStringContainsString('"delta":"Hel"', $streamed);
        $this->assertStringContainsString('"delta":"lo!"', $streamed);
        $this->assertStringContainsString('"done":true', $streamed);

        $assistant = AiChatMessage::query()
            ->where('ai_chat_session_id', $session->id)
            ->where('role', 'assistant')
            ->first();

        $this->assertNotNull($assistant);
        $this->assertSame('Hello!', $assistant->content);
        $this->assertSame(2.0, (float) $assistant->credits_used);
    }

    public function test_router_failure_returns_502_without_losing_user_message(): void
    {
        Http::fake(['*/chat/completions' => Http::response(['error' => 'upstream down'], 503)]);

        $session = AiChatSession::create(['user_id' => $this->user->id, 'title' => 'New chat']);

        $this->actingAs($this->user)
            ->postJson(route('ai-chat.sessions.send', $session), ['content' => 'Are you there?'])
            ->assertStatus(502);

        $this->assertDatabaseHas('ai_chat_messages', [
            'ai_chat_session_id' => $session->id,
            'role' => 'user',
            'content' => 'Are you there?',
        ]);
        $this->assertSame(1, $session->messages()->count());
    }
}
