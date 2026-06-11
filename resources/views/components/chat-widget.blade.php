@props([
    'chats' => collect(),
    'users' => collect(),
])

@auth
<div
    x-data="{
        init() { this.panelOpen = false; },
        panelOpen: false,
        roomId: null,
        roomName: '',
        messages: [],
        text: '',
        lastId: 0,
        interval: null,
        selectedUserId: '',
        currentUserId: {{ auth()->id() }},
        rooms: @js($chats->map(function ($chat) {
            $otherUser = $chat->user_one_id === auth()->id() ? $chat->userTwo : $chat->userOne;

            return [
                'id' => $chat->id,
                'name' => $otherUser?->name ?? 'Chat',
                'email' => $otherUser?->email,
            ];
        })->values()),
        openRoom(id, name) {
            this.panelOpen = true;
            this.roomId = id;
            this.roomName = name;
            this.messages = [];
            this.lastId = 0;
            this.fetchMessages();
            clearInterval(this.interval);
            this.interval = setInterval(() => this.fetchMessages(), 2500);
        },
        fetchMessages() {
            if (!this.roomId) return;
            fetch(`/communication/chats/${this.roomId}/messages?after_id=${this.lastId}`)
                .then(response => response.json())
                .then(data => {
                    data.messages.forEach(message => {
                        this.messages.push(message);
                        this.lastId = Math.max(this.lastId, message.id);
                    });
                    this.$nextTick(() => {
                        const box = this.$refs.messages;
                        if (box) box.scrollTop = box.scrollHeight;
                    });
                });
        },
        createRoom() {
            if (!this.selectedUserId) return;
            fetch('{{ route('communication.chats.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ user_two_id: this.selectedUserId })
            })
                .then(response => response.json())
                .then(data => {
                    if (!this.rooms.find(room => room.id === data.chat.id)) {
                        this.rooms.unshift(data.chat);
                    }
                    this.selectedUserId = '';
                    this.openRoom(data.chat.id, data.chat.name);
                });
        },
        send() {
            if (!this.text.trim() || !this.roomId) return;
            fetch(`/communication/chats/${this.roomId}/messages`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message: this.text })
            })
                .then(response => response.json())
                .then(data => {
                    this.messages.push(data.message);
                    this.lastId = Math.max(this.lastId, data.message.id);
                    this.text = '';
                    this.$nextTick(() => {
                        const box = this.$refs.messages;
                        if (box) box.scrollTop = box.scrollHeight;
                    });
                });
        }
    }"
    x-cloak
    class="fixed bottom-6 right-6 z-40 w-[calc(100vw-3rem)] max-w-md overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-2xl dark:border-gray-700 dark:bg-gray-800"
>
    <div class="flex items-center justify-between bg-indigo-600 px-4 py-3 text-sm font-semibold text-white">
        <span x-text="roomName || 'Chats'"></span>
        <button type="button" @click="panelOpen = !panelOpen" class="flex h-6 w-6 items-center justify-center rounded-md text-lg leading-none hover:bg-white/10" x-text="panelOpen ? '-' : '+'"></button>
    </div>

    <div x-show="panelOpen" x-cloak class="grid h-[620px] grid-rows-[auto_1fr_auto]">
        <div class="border-b border-gray-200 p-3 dark:border-gray-700">
            <form @submit.prevent="createRoom()" class="flex gap-2">
                <select x-model="selectedUserId" required class="min-w-0 flex-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">Start chat with...</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
                <button class="rounded-lg bg-indigo-600 px-3 py-2 text-sm font-medium text-white">New</button>
            </form>
        </div>

        <div class="grid min-h-0 grid-cols-[150px_1fr]">
            <div class="overflow-y-auto border-r border-gray-200 dark:border-gray-700">
                <template x-for="room in rooms" :key="room.id">
                    <button type="button" @click="openRoom(room.id, room.name)" class="block w-full border-b border-gray-100 px-3 py-3 text-left text-sm hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700">
                        <span class="block truncate font-medium text-gray-900 dark:text-white" x-text="room.name"></span>
                        <span class="block truncate text-xs text-gray-500" x-text="room.email"></span>
                    </button>
                </template>
                <div x-show="rooms.length === 0" class="px-3 py-8 text-center text-xs text-gray-500">No rooms.</div>
            </div>

            <div class="flex min-w-0 flex-col">
                <div x-ref="messages" class="min-h-0 flex-1 space-y-3 overflow-y-auto p-3">
                    <template x-for="message in messages" :key="message.id">
                        <div class="flex" :class="message.user_id === currentUserId ? 'justify-end' : 'justify-start'">
                            <div class="max-w-[85%] rounded-xl px-3 py-2 text-sm" :class="message.user_id === currentUserId ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-900 dark:bg-gray-700 dark:text-white'">
                                <div class="text-xs font-semibold opacity-80" x-text="message.user_name"></div>
                                <div x-text="message.message"></div>
                                <div class="mt-1 text-[10px] opacity-70" x-text="message.created_at"></div>
                            </div>
                        </div>
                    </template>
                    <div x-show="!roomId" class="py-20 text-center text-sm text-gray-500">Select a room.</div>
                </div>

                <div class="flex gap-2 border-t border-gray-200 p-3 dark:border-gray-700">
                    <input type="text" x-model="text" @keydown.enter.prevent="send()" :disabled="!roomId" class="min-w-0 flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm disabled:bg-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:disabled:bg-gray-900" placeholder="Type message...">
                    <button type="button" @click="send()" :disabled="!roomId" class="rounded-lg bg-indigo-600 px-3 py-2 text-sm font-medium text-white disabled:opacity-50">Send</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endauth
