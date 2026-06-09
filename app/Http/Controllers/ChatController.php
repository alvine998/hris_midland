<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ChatRoom;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function index(): View
    {
        $userId = auth()->id();

        return view('chats.index', [
            'chats' => Chat::with(['userOne', 'userTwo'])
                ->where(fn ($query) => $query->where('user_one_id', $userId)->orWhere('user_two_id', $userId))
                ->latest()
                ->get(),
            'users' => User::whereKeyNot($userId)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'user_two_id' => ['required', 'exists:users,id'],
        ]);

        $userId = auth()->id();
        abort_if((int) $data['user_two_id'] === (int) $userId, 422, 'You cannot create a chat with yourself.');

        $existingChat = Chat::query()
            ->with(['userOne', 'userTwo'])
            ->where(function ($query) use ($data, $userId): void {
                $query->where('user_one_id', $userId)->where('user_two_id', $data['user_two_id']);
            })
            ->orWhere(function ($query) use ($data, $userId): void {
                $query->where('user_one_id', $data['user_two_id'])->where('user_two_id', $userId);
            })
            ->first();

        if ($existingChat) {
            $this->roomFor($existingChat);

            if ($request->expectsJson()) {
                return response()->json(['chat' => $this->chatPayload($existingChat), 'message' => 'Chat room already exists.']);
            }

            return back()->with('success', 'Chat room already exists.');
        }

        $room = ChatRoom::create(['messages' => []]);
        $chat = Chat::create([
            'user_one_id' => $userId,
            'user_two_id' => $data['user_two_id'],
            'room_id' => (string) $room->id,
        ]);
        $chat->load(['userOne', 'userTwo']);

        if ($request->expectsJson()) {
            return response()->json(['chat' => $this->chatPayload($chat), 'message' => 'Chat room created successfully.']);
        }

        return back()->with('success', 'Chat room created successfully.');
    }

    public function messages(Chat $chat, Request $request): JsonResponse
    {
        $this->authorizeChatAccess($chat);

        $room = $this->roomFor($chat);
        $afterId = (int) $request->query('after_id', 0);
        $messages = collect($room->messages ?? [])
            ->filter(fn ($message) => (int) ($message['id'] ?? 0) > $afterId)
            ->values();

        return response()->json(['messages' => $messages]);
    }

    public function send(Chat $chat, Request $request): JsonResponse
    {
        $this->authorizeChatAccess($chat);

        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $user = auth()->user();
        $room = $this->roomFor($chat);
        $messages = $room->messages ?? [];
        $messages[] = [
            'id' => count($messages) + 1,
            'user_id' => (int) $user->id,
            'user_name' => $user->name,
            'message' => $data['message'],
            'created_at' => now()->toDateTimeString(),
        ];
        $room->update(['messages' => $messages]);

        return response()->json(['message' => last($messages)]);
    }

    private function authorizeChatAccess(Chat $chat): void
    {
        $userId = auth()->id();

        abort_unless((int) $chat->user_one_id === (int) $userId || (int) $chat->user_two_id === (int) $userId, 403);
    }

    private function roomFor(Chat $chat): ChatRoom
    {
        $room = $chat->room_id ? ChatRoom::find($chat->room_id) : null;

        if ($room) {
            return $room;
        }

        $room = ChatRoom::create(['messages' => []]);
        $chat->update(['room_id' => (string) $room->id]);

        return $room;
    }

    private function chatPayload(Chat $chat): array
    {
        $otherUser = (int) $chat->user_one_id === (int) auth()->id() ? $chat->userTwo : $chat->userOne;

        return [
            'id' => $chat->id,
            'name' => $otherUser?->name ?? 'Chat',
            'email' => $otherUser?->email,
        ];
    }
}
