@extends('layouts.app')

@section('title', 'AI Assistant — ' . config('app.name'))

@section('content')
<div
    x-data="aiChat({
        sessionId: {{ $selectedSession?->id ?? 'null' }},
        initialMessages: @js($initialMessages->map(fn ($m) => [
            'id' => $m->id,
            'role' => $m->role,
            'content' => $m->content,
            'created_at' => $m->created_at?->toIso8601String(),
        ])),
        model: @js($selectedSession?->model ?? 'auto'),
    })"
    x-init="init()"
    class="ai-shell relative flex h-[calc(100vh-4rem)] -mx-4 sm:-mx-6 -my-4 lg:-my-6 overflow-hidden"
>
    {{-- ===== Left rail (sessions) ===== --}}
    <aside
        x-cloak
        x-show="railOpen || !isMobile"
        x-transition:enter="transition transform ease-out duration-200"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition transform ease-in duration-150"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="ai-rail absolute inset-y-0 left-0 z-30 w-72 flex-shrink-0 border-r border-gray-200 bg-white/95 backdrop-blur dark:border-gray-800 dark:bg-gray-900/95 lg:static lg:translate-x-0"
    >
        {{-- New chat button --}}
        <div class="flex items-center gap-2 px-3 pt-3">
            <button
                type="button"
                @click="newSession()"
                :disabled="creating"
                class="flex flex-1 items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New chat
            </button>
            <button
                type="button"
                @click="railOpen = false"
                class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 lg:hidden"
                aria-label="Close sidebar"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Search --}}
        <div class="px-3 pt-3">
            <div class="relative">
                <svg class="pointer-events-none absolute left-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>
                <input
                    type="search"
                    x-model="search"
                    placeholder="Search chats…"
                    class="w-full rounded-lg border-0 bg-gray-100 py-2 pl-8 pr-3 text-sm placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-200 dark:placeholder-gray-400"
                />
            </div>
        </div>

        {{-- Sessions list --}}
        <nav class="ai-scroll mt-3 flex-1 space-y-0.5 overflow-y-auto px-2 pb-3">
            <template x-for="group in groupedSessions" :key="group.label">
                <div class="mb-2">
                    <p class="px-2 pb-1 pt-2 text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500" x-text="group.label"></p>
                    <template x-for="s in group.items" :key="s.id">
                        <div
                            class="group flex items-center gap-1 rounded-lg px-2 py-1.5 text-sm cursor-pointer transition-colors"
                            :class="s.id === sessionId ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-200' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800'"
                            @click="selectSession(s.id)"
                        >
                            <template x-if="renamingId !== s.id">
                                <span class="flex-1 truncate" x-text="s.title || 'New chat'"></span>
                            </template>
                            <template x-if="renamingId === s.id">
                                <input
                                    type="text"
                                    x-ref="renameInput"
                                    x-model="renameDraft"
                                    @keydown.enter.prevent="commitRename(s)"
                                    @keydown.escape.prevent="cancelRename()"
                                    @blur="commitRename(s)"
                                    class="flex-1 rounded border border-indigo-300 bg-white px-1.5 py-0.5 text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-indigo-700 dark:bg-gray-800 dark:text-gray-100"
                                />
                            </template>
                            <div class="hidden items-center gap-0.5 group-hover:flex" @click.stop>
                                <button
                                    type="button"
                                    @click="startRename(s)"
                                    class="rounded p-1 text-gray-400 hover:bg-gray-200 hover:text-gray-700 dark:hover:bg-gray-700 dark:hover:text-gray-200"
                                    title="Rename"
                                >
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button
                                    type="button"
                                    @click="confirmDelete(s)"
                                    class="rounded p-1 text-gray-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-500/20 dark:hover:text-red-400"
                                    title="Delete"
                                >
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
            <div x-show="filteredSessions.length === 0" class="px-3 py-6 text-center text-sm text-gray-400 dark:text-gray-500">
                <span x-show="search && sessions.length">No chats match "<span x-text="search"></span>".</span>
                <span x-show="!search || !sessions.length">No chats yet.</span>
            </div>
        </nav>

        {{-- Memory card --}}
        <div x-show="hasMemory" class="mx-3 mb-3 rounded-lg border border-indigo-100 bg-indigo-50/60 p-3 text-xs text-indigo-900 dark:border-indigo-500/20 dark:bg-indigo-500/5 dark:text-indigo-200">
            <div class="mb-1 flex items-center gap-1.5 font-semibold">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                Memory
            </div>
            <p class="line-clamp-3 leading-relaxed" x-text="memoryPreview"></p>
        </div>
    </aside>

    {{-- Mobile rail backdrop --}}
    <div
        x-show="railOpen && isMobile"
        x-cloak
        @click="railOpen = false"
        x-transition:enter="transition-opacity ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-20 bg-gray-900/50 lg:hidden"
    ></div>

    {{-- ===== Main column ===== --}}
    <section class="relative flex min-w-0 flex-1 flex-col bg-white dark:bg-gray-900">

        {{-- Top bar --}}
        <header class="flex h-14 shrink-0 items-center gap-2 border-b border-gray-200 px-3 dark:border-gray-800 sm:px-4">
            <button
                type="button"
                @click="railOpen = true"
                class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 lg:hidden"
                aria-label="Open sidebar"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            <div class="flex min-w-0 flex-1 items-center gap-2">
                <div class="flex h-7 w-7 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 text-white shadow-sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/></svg>
                </div>
                <div class="min-w-0">
                    <h1 class="truncate text-sm font-semibold text-gray-900 dark:text-white" x-text="activeTitle"></h1>
                    <p class="hidden text-[11px] text-gray-500 dark:text-gray-400 sm:block" x-text="activeSubtitle"></p>
                </div>
            </div>

            {{-- Model selector --}}
            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                <button
                    type="button"
                    @click="open = !open"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                >
                    <span class="hidden sm:inline">Model:</span>
                    <span x-text="model"></span>
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div
                    x-show="open"
                    x-cloak
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute right-0 z-10 mt-2 w-48 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800"
                >
                    <template x-for="opt in models" :key="opt.id">
                        <button
                            type="button"
                            @click="model = opt.id; open = false"
                            class="flex w-full items-center justify-between gap-2 px-3 py-2 text-left text-sm hover:bg-gray-100 dark:hover:bg-gray-700"
                            :class="model === opt.id ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-200' : 'text-gray-700 dark:text-gray-200'"
                        >
                            <span class="flex flex-col">
                                <span class="font-medium" x-text="opt.label"></span>
                                <span class="text-[11px] text-gray-500 dark:text-gray-400" x-text="opt.hint"></span>
                            </span>
                            <svg x-show="model === opt.id" class="h-4 w-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </button>
                    </template>
                </div>
            </div>
        </header>

        {{-- Messages --}}
        <div class="ai-scroll relative flex-1 overflow-y-auto" x-ref="thread" @scroll="onScroll($event)">
            <div class="mx-auto w-full max-w-3xl px-4 pb-6 sm:px-6">

                {{-- Empty state --}}
                <div x-show="messages.length === 0 && !loadingMessages" class="flex min-h-[60vh] flex-col items-center justify-center text-center">
                    <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-500 text-white shadow-lg">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z"/></svg>
                    </div>
                    <h2 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">How can I help you today?</h2>
                    <p class="mt-1 max-w-md text-sm text-gray-500 dark:text-gray-400">I'm Midland AI, your HR assistant. Ask me about policies, employees, leave, payroll, or anything work-related.</p>

                    <div class="mt-8 grid w-full grid-cols-1 gap-2 sm:grid-cols-2">
                        <template x-for="p in prompts" :key="p.title">
                            <button
                                type="button"
                                @click="usePrompt(p)"
                                class="group flex items-start gap-3 rounded-xl border border-gray-200 bg-white p-3 text-left transition-colors hover:border-indigo-300 hover:bg-indigo-50/40 dark:border-gray-700 dark:bg-gray-800 dark:hover:border-indigo-500/40 dark:hover:bg-indigo-500/5"
                            >
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500 group-hover:bg-indigo-100 group-hover:text-indigo-600 dark:bg-gray-700 dark:text-gray-400 dark:group-hover:bg-indigo-500/20 dark:group-hover:text-indigo-300">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-html="p.icon"></svg>
                                </span>
                                <span class="min-w-0">
                                    <span class="block text-sm font-medium text-gray-900 dark:text-white" x-text="p.title"></span>
                                    <span class="block truncate text-xs text-gray-500 dark:text-gray-400" x-text="p.subtitle"></span>
                                </span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Message bubbles --}}
                <div class="space-y-6 py-6">
                    <template x-for="(m, index) in messages" :key="m.id ?? ('tmp-' + index)">
                        <div class="group flex gap-3 sm:gap-4" :class="m.role === 'user' ? 'justify-end' : 'justify-start'">
                            <template x-if="m.role === 'assistant'">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 text-white shadow-sm">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/></svg>
                                </div>
                            </template>

                            <div class="min-w-0 max-w-[85%] sm:max-w-[78%]">
                                <div
                                    class="ai-bubble"
                                    :class="m.role === 'user'
                                        ? 'rounded-2xl rounded-br-md bg-indigo-600 px-4 py-2.5 text-white shadow-sm'
                                        : 'rounded-2xl rounded-bl-md bg-gray-100 px-4 py-2.5 text-gray-900 shadow-sm dark:bg-gray-800 dark:text-gray-100'"
                                >
                                    <div
                                        x-show="m.role === 'user'"
                                        class="whitespace-pre-wrap break-words text-sm leading-relaxed"
                                        x-text="m.content"
                                    ></div>
                                    <div
                                        x-show="m.role === 'assistant'"
                                        class="ai-md text-sm leading-relaxed"
                                        x-html="renderMd(m.content)"
                                    ></div>
                                    <span x-show="m.role === 'assistant' && sending && m === lastMessage && !m.content" class="ai-cursor inline-block h-4 w-1.5 -mb-0.5 ml-0.5 align-middle bg-indigo-500"></span>
                                </div>

                                {{-- Bubble actions --}}
                                <div x-show="m.role === 'assistant' && m.content && m.id" class="mt-1.5 flex items-center gap-1 px-1 text-gray-400 opacity-0 transition-opacity group-hover:opacity-100">
                                    <button
                                        type="button"
                                        @click="copy(m)"
                                        class="flex items-center gap-1 rounded px-1.5 py-0.5 text-xs hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-gray-800 dark:hover:text-gray-200"
                                        :class="copiedId === m.id ? 'text-emerald-500' : ''"
                                    >
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-2M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v2m-6 8l2 2 4-4"/></svg>
                                        <span x-text="copiedId === m.id ? 'Copied' : 'Copy'"></span>
                                    </button>
                                    <button
                                        type="button"
                                        @click="regenerate()"
                                        :disabled="sending"
                                        class="flex items-center gap-1 rounded px-1.5 py-0.5 text-xs hover:bg-gray-100 hover:text-gray-700 disabled:opacity-50 dark:hover:bg-gray-800 dark:hover:text-gray-200"
                                    >
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        <span>Regenerate</span>
                                    </button>
                                </div>
                            </div>

                            <template x-if="m.role === 'user'">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-200 text-sm font-semibold text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                                </div>
                            </template>
                        </div>
                    </template>

                    {{-- Typing indicator --}}
                    <div x-show="sending && lastMessage && !lastMessage.content" class="flex gap-3 sm:gap-4">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 text-white shadow-sm">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/></svg>
                        </div>
                        <div class="rounded-2xl rounded-bl-md bg-gray-100 px-4 py-3 dark:bg-gray-800">
                            <div class="flex items-center gap-1.5">
                                <span class="h-1.5 w-1.5 rounded-full bg-gray-400 ai-dot"></span>
                                <span class="h-1.5 w-1.5 rounded-full bg-gray-400 ai-dot" style="animation-delay:.15s"></span>
                                <span class="h-1.5 w-1.5 rounded-full bg-gray-400 ai-dot" style="animation-delay:.3s"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Jump-to-bottom button --}}
            <button
                x-show="showJumpToBottom"
                x-cloak
                @click="scrollToBottom(true)"
                x-transition.opacity
                class="absolute bottom-4 left-1/2 -translate-x-1/2 rounded-full border border-gray-200 bg-white p-2 text-gray-600 shadow-md hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                aria-label="Scroll to latest"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
            </button>
        </div>

        {{-- Error banner --}}
        <div x-show="error" x-cloak class="mx-3 mb-2 sm:mx-6">
            <div class="flex items-start gap-2 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-700 dark:border-red-800 dark:bg-red-900/30 dark:text-red-300">
                <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="flex-1" x-text="error"></span>
                <button type="button" @click="error = ''" class="text-red-700 hover:text-red-900 dark:text-red-300 dark:hover:text-red-100">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        {{-- Composer --}}
        <div class="border-t border-gray-200 bg-white px-3 pb-3 pt-2 dark:border-gray-800 dark:bg-gray-900 sm:px-4 sm:pb-4">
            <form @submit.prevent="send()" class="mx-auto max-w-3xl">
                <div
                    class="ai-composer flex items-end gap-2 rounded-2xl border border-gray-300 bg-white p-2 shadow-sm focus-within:border-indigo-400 focus-within:ring-2 focus-within:ring-indigo-100 dark:border-gray-700 dark:bg-gray-800 dark:focus-within:border-indigo-500 dark:focus-within:ring-indigo-500/20"
                    :class="sending ? 'opacity-90' : ''"
                >
                    <textarea
                        x-ref="input"
                        x-model="draft"
                        rows="1"
                        :placeholder="sending ? 'Generating…' : 'Message Midland AI…'"
                        class="flex-1 resize-none border-0 bg-transparent px-2 py-1.5 text-sm leading-6 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-0 dark:text-gray-100 dark:placeholder-gray-500"
                        style="max-height: 12rem"
                        @keydown.enter.prevent="if (!$event.shiftKey) send()"
                        @input="autoGrow($event)"
                    ></textarea>

                    <button
                        x-show="!sending"
                        type="submit"
                        :disabled="draft.trim() === ''"
                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-600 text-white transition-opacity hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:cursor-not-allowed disabled:opacity-30"
                        aria-label="Send"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M13 5l7 7-7 7"/></svg>
                    </button>

                    <button
                        x-show="sending"
                        x-cloak
                        type="button"
                        @click="stop()"
                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gray-900 text-white transition-colors hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 dark:bg-gray-200 dark:text-gray-900 dark:hover:bg-white"
                        aria-label="Stop generating"
                    >
                        <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24"><rect x="6" y="6" width="12" height="12" rx="1.5"/></svg>
                    </button>
                </div>
                <p class="mt-1.5 text-center text-[11px] text-gray-400 dark:text-gray-500">
                    Midland AI can make mistakes. Verify important information.
                </p>
            </form>
        </div>
    </section>

    {{-- ===== Delete confirmation modal ===== --}}
    <div x-cloak x-show="deleteTarget" class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-gray-900/50" @click="deleteTarget = null"></div>
        <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Delete this chat?</h3>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                "<span class="font-medium" x-text="deleteTarget?.title"></span>" and its memory will be permanently removed. This action cannot be undone.
            </p>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" @click="deleteTarget = null" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">Cancel</button>
                <button type="button" @click="destroySession()" :disabled="deleting" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 disabled:opacity-50">Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('head')
<style>
    .ai-shell { color-scheme: light dark; }
    .ai-rail { display: flex; flex-direction: column; }
    @media (min-width: 1024px) { .ai-rail { display: flex !important; } }
    .ai-scroll::-webkit-scrollbar { width: 8px; }
    .ai-scroll::-webkit-scrollbar-thumb { background: rgba(0,0,0,.12); border-radius: 4px; }
    .dark .ai-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,.12); }
    .dark .ai-scroll::-webkit-scrollbar-track { background: transparent; }

    /* Streaming cursor blink */
    @keyframes ai-cursor-blink { 0%,100% { opacity: 1; } 50% { opacity: 0; } }
    .ai-cursor { animation: ai-cursor-blink 1s step-end infinite; }

    /* Typing dots */
    @keyframes ai-dot { 0%, 80%, 100% { transform: translateY(0); opacity: .4; } 40% { transform: translateY(-4px); opacity: 1; } }
    .ai-dot { animation: ai-dot 1.2s ease-in-out infinite; display: inline-block; }

    /* Markdown styling */
    .ai-md p { margin: 0.25rem 0; }
    .ai-md p + p { margin-top: 0.5rem; }
    .ai-md ul, .ai-md ol { margin: 0.4rem 0 0.4rem 1.25rem; }
    .ai-md ul { list-style: disc; }
    .ai-md ol { list-style: decimal; }
    .ai-md li + li { margin-top: 0.15rem; }
    .ai-md h1, .ai-md h2, .ai-md h3, .ai-md h4 { font-weight: 600; margin: 0.6rem 0 0.3rem; line-height: 1.3; }
    .ai-md h1 { font-size: 1.05rem; }
    .ai-md h2 { font-size: 1rem; }
    .ai-md h3 { font-size: 0.95rem; }
    .ai-md code { background: rgba(0,0,0,.06); padding: 0.1rem 0.35rem; border-radius: 0.25rem; font-size: 0.85em; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; }
    .dark .ai-md code { background: rgba(255,255,255,.08); }
    .ai-md pre { background: rgba(0,0,0,.04); padding: 0.6rem 0.8rem; border-radius: 0.5rem; overflow-x: auto; margin: 0.5rem 0; }
    .dark .ai-md pre { background: rgba(255,255,255,.04); }
    .ai-md pre code { background: transparent; padding: 0; }
    .ai-md blockquote { border-left: 3px solid rgba(99,102,241,.5); padding-left: 0.75rem; color: rgba(75,85,99,.95); margin: 0.5rem 0; }
    .dark .ai-md blockquote { color: rgba(209,213,219,.95); }
    .ai-md a { color: #4f46e5; text-decoration: underline; text-underline-offset: 2px; }
    .dark .ai-md a { color: #818cf8; }
    .ai-md table { width: 100%; border-collapse: collapse; margin: 0.5rem 0; font-size: 0.9em; }
    .ai-md th, .ai-md td { border: 1px solid rgba(0,0,0,.1); padding: 0.35rem 0.5rem; text-align: left; }
    .dark .ai-md th, .dark .ai-md td { border-color: rgba(255,255,255,.1); }
    .ai-md strong { font-weight: 600; }
    .ai-md hr { border: 0; border-top: 1px solid rgba(0,0,0,.1); margin: 0.75rem 0; }
    .dark .ai-md hr { border-top-color: rgba(255,255,255,.1); }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('aiChat', ({ sessionId, initialMessages, model }) => ({
        csrfToken: document.querySelector('meta[name="csrf-token"]').content,
        sessions: @json($sessions->map(fn ($s) => ['id' => $s->id, 'title' => $s->title, 'updated_at' => $s->last_message_at?->toIso8601String() ?? $s->created_at?->toIso8601String()])),
        sessionId,
        messages: initialMessages,
        memoryPreview: '',
        hasMemory: false,
        draft: '',
        sending: false,
        creating: false,
        loadingMessages: false,
        deleting: false,
        deleteTarget: null,
        error: '',
        search: '',
        railOpen: false,
        isMobile: window.matchMedia('(max-width: 1023px)').matches,
        renamingId: null,
        renameDraft: '',
        copiedId: null,
        showJumpToBottom: false,
        activeAbort: null,
        model: model || 'mimo-v2.5',
        models: [
            { id: 'mimo-v2.5', label: 'MiMo V2.5', hint: 'Xiaomi multimodal AI' },
        ],
        prompts: [
            { title: 'Explain my leave balance', subtitle: 'Understand quota and accruals', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H5v12z"/>' },
            { title: 'Draft a performance review', subtitle: 'Start with a structure', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5l5 5v11a2 2 0 01-2 2z"/>' },
            { title: 'Summarize HR policies', subtitle: 'Pull the key points', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>' },
            { title: 'Help me prepare a 1:1', subtitle: 'Talking points & questions', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M9 20H4v-2a3 3 0 015.356-1.857M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>' },
        ],

        get filteredSessions() {
            const q = this.search.trim().toLowerCase();
            if (!q) return this.sessions;
            return this.sessions.filter(s => (s.title || '').toLowerCase().includes(q));
        },

        get groupedSessions() {
            const now = new Date();
            const startOfDay = (d) => { const x = new Date(d); x.setHours(0,0,0,0); return x.getTime(); };
            const todayStart = startOfDay(now);
            const yesterdayStart = todayStart - 86400000;
            const weekStart = todayStart - 7 * 86400000;

            const buckets = { Today: [], Yesterday: [], 'Previous 7 days': [], Older: [] };
            for (const s of this.filteredSessions) {
                const t = s.updated_at ? new Date(s.updated_at).getTime() : 0;
                if (t >= todayStart) buckets.Today.push(s);
                else if (t >= yesterdayStart) buckets.Yesterday.push(s);
                else if (t >= weekStart) buckets['Previous 7 days'].push(s);
                else buckets.Older.push(s);
            }
            return Object.entries(buckets).filter(([_, items]) => items.length).map(([label, items]) => ({ label, items }));
        },

        get activeTitle() {
            if (!this.sessionId) return 'Midland AI';
            const s = this.sessions.find(x => x.id === this.sessionId);
            return s?.title && s.title !== 'New chat' ? s.title : 'New chat';
        },

        get activeSubtitle() {
            return this.sending ? 'Generating…' : 'Powered by Alvine IT Solutions';
        },

        get lastMessage() {
            return this.messages[this.messages.length - 1];
        },

        init() {
            window.addEventListener('resize', () => { this.isMobile = window.matchMedia('(max-width: 1023px)').matches; });
            if (this.sessionId) this.loadMemory();
            this.$watch('messages', () => { this.$nextTick(() => this.maybeScroll()); });
            this.$refs.input?.focus();
        },

        usePrompt(p) {
            this.draft = p.title + (p.subtitle ? ' — ' + p.subtitle : '');
            this.$refs.input?.focus();
        },

        autoGrow(event) {
            const el = event.target;
            el.style.height = 'auto';
            el.style.height = Math.min(el.scrollHeight, 192) + 'px';
        },

        maybeScroll() {
            const t = this.$refs.thread;
            if (!t) return;
            const distFromBottom = t.scrollHeight - t.scrollTop - t.clientHeight;
            this.showJumpToBottom = distFromBottom > 200;
            if (distFromBottom < 200 || this.sending) {
                t.scrollTop = t.scrollHeight;
            }
        },

        scrollToBottom(force = false) {
            const t = this.$refs.thread;
            if (t) t.scrollTop = t.scrollHeight;
            this.showJumpToBottom = false;
        },

        onScroll(event) {
            const t = event.target;
            const distFromBottom = t.scrollHeight - t.scrollTop - t.clientHeight;
            this.showJumpToBottom = distFromBottom > 200;
        },

        // Tiny markdown renderer — handles headings, lists, code, bold/italic,
        // links, blockquotes, tables, hr. Escapes HTML first.
        renderMd(text) {
            if (!text) return '';
            let s = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');

            // Code blocks
            s = s.replace(/```([a-zA-Z0-9_-]*)\n?([\s\S]*?)```/g, (m, lang, code) =>
                `<pre><code class="lang-${lang}">${code.replace(/\n$/, '')}</code></pre>`);
            // Inline code
            s = s.replace(/`([^`\n]+)`/g, '<code>$1</code>');

            // Headings
            s = s.replace(/^######\s+(.+)$/gm, '<h6>$1</h6>')
                 .replace(/^#####\s+(.+)$/gm, '<h5>$1</h5>')
                 .replace(/^####\s+(.+)$/gm, '<h4>$1</h4>')
                 .replace(/^###\s+(.+)$/gm, '<h3>$1</h3>')
                 .replace(/^##\s+(.+)$/gm, '<h2>$1</h2>')
                 .replace(/^#\s+(.+)$/gm, '<h1>$1</h1>');

            // Horizontal rule
            s = s.replace(/^\s*---+\s*$/gm, '<hr>');

            // Blockquote
            s = s.replace(/^>\s?(.*)$/gm, '<blockquote>$1</blockquote>');
            s = s.replace(/(<\/blockquote>\s*<blockquote>)/g, '<br>');

            // Tables (very small subset)
            s = s.replace(/((?:\|.*\n)+)/g, (block) => {
                const lines = block.trim().split('\n');
                if (lines.length < 2 || !/^\|?[\s:-]+\|?$/.test(lines[1])) return block;
                const head = lines[0].split('|').map(c => c.trim()).filter(Boolean);
                const rows = lines.slice(2).map(l => l.split('|').map(c => c.trim()).filter(Boolean));
                let html = '<table><thead><tr>' + head.map(h => `<th>${h}</th>`).join('') + '</tr></thead><tbody>';
                for (const r of rows) html += '<tr>' + r.map(c => `<td>${c}</td>`).join('') + '</tr>';
                return html + '</tbody></table>';
            });

            // Unordered lists (group consecutive lines)
            s = s.replace(/(^[-*]\s+.*(?:\n[-*]\s+.*)*)/gm, (block) => {
                const items = block.split('\n').map(l => l.replace(/^[-*]\s+/, '')).map(t => `<li>${t}</li>`).join('');
                return `<ul>${items}</ul>`;
            });
            // Ordered lists
            s = s.replace(/(^\d+\.\s+.*(?:\n\d+\.\s+.*)*)/gm, (block) => {
                const items = block.split('\n').map(l => l.replace(/^\d+\.\s+/, '')).map(t => `<li>${t}</li>`).join('');
                return `<ol>${items}</ol>`;
            });

            // Bold + italic
            s = s.replace(/\*\*([^*\n]+)\*\*/g, '<strong>$1</strong>')
                 .replace(/__([^_\n]+)__/g, '<strong>$1</strong>')
                 .replace(/\*([^*\n]+)\*/g, '<em>$1</em>')
                 .replace(/(^|[^_])_([^_\n]+)_/g, '$1<em>$2</em>');

            // Links
            s = s.replace(/\[([^\]]+)\]\((https?:[^)]+)\)/g, '<a href="$2" target="_blank" rel="noopener noreferrer">$1</a>');

            // Paragraphs (double newline)
            s = s.split(/\n{2,}/).map(p => {
                if (/^\s*<(h\d|ul|ol|pre|blockquote|hr|table)/.test(p)) return p;
                return '<p>' + p.replace(/\n/g, '<br>') + '</p>';
            }).join('');

            return s;
        },

        async copy(m) {
            try {
                await navigator.clipboard.writeText(m.content);
                this.copiedId = m.id;
                setTimeout(() => { if (this.copiedId === m.id) this.copiedId = null; }, 1500);
            } catch (e) {
                this.error = 'Copy failed.';
            }
        },

        async newSession() {
            this.creating = true;
            this.error = '';
            try {
                const response = await fetch('{{ route("ai-chat.sessions.store") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': this.csrfToken, Accept: 'application/json' },
                });
                const json = await response.json();
                if (!response.ok) throw new Error(json.message || 'Failed to create session.');

                const session = json.data ?? json.session;
                this.sessions.unshift({ id: session.id, title: session.title, updated_at: new Date().toISOString() });
                this.sessionId = session.id;
                this.messages = [];
                this.memoryPreview = '';
                this.hasMemory = false;
                this.railOpen = false;
                window.history.replaceState({}, '', '{{ route("ai-chat.index") }}?session=' + session.id);
                this.$refs.input?.focus();
            } catch (e) {
                this.error = e.message;
            } finally {
                this.creating = false;
            }
        },

        async selectSession(id) {
            if (id === this.sessionId) return;
            this.sessionId = id;
            this.error = '';
            this.loadingMessages = true;
            this.railOpen = false;
            window.history.replaceState({}, '', '{{ route("ai-chat.index") }}?session=' + id);
            try {
                const response = await fetch(`{{ route('ai-chat.index') }}/sessions/${id}/messages`, {
                    headers: { Accept: 'application/json' },
                });
                const json = await response.json();
                if (!response.ok) throw new Error(json.message || 'Failed to load messages.');
                this.messages = json.data.messages;
                this.hasMemory = json.data.memory.has;
                this.memoryPreview = json.data.memory.preview;
                this.$nextTick(() => this.scrollToBottom(true));
            } catch (e) {
                this.error = e.message;
            } finally {
                this.loadingMessages = false;
            }
        },

        startRename(s) {
            this.renamingId = s.id;
            this.renameDraft = s.title === 'New chat' ? '' : (s.title || '');
            this.$nextTick(() => this.$refs.renameInput?.focus());
        },

        cancelRename() {
            this.renamingId = null;
            this.renameDraft = '';
        },

        async commitRename(s) {
            const title = this.renameDraft.trim();
            this.renamingId = null;
            this.renameDraft = '';
            if (!title || title === s.title) return;
            try {
                const res = await fetch(`{{ route('ai-chat.index') }}/sessions/${s.id}`, {
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Content-Type': 'application/json', Accept: 'application/json' },
                    body: JSON.stringify({ title }),
                });
                if (!res.ok) throw new Error('Failed to rename.');
                s.title = title;
            } catch (e) {
                this.error = e.message;
            }
        },

        confirmDelete(session) {
            this.deleteTarget = session;
        },

        async destroySession() {
            if (!this.deleteTarget) return;
            this.deleting = true;
            try {
                const response = await fetch(`{{ route('ai-chat.index') }}/sessions/${this.deleteTarget.id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': this.csrfToken, Accept: 'application/json' },
                });
                if (!response.ok) throw new Error('Failed to delete session.');
                this.sessions = this.sessions.filter((s) => s.id !== this.deleteTarget.id);
                if (this.sessionId === this.deleteTarget.id) {
                    this.sessionId = null;
                    this.messages = [];
                    this.hasMemory = false;
                    this.memoryPreview = '';
                    window.history.replaceState({}, '', '{{ route("ai-chat.index") }}');
                }
                this.deleteTarget = null;
            } catch (e) {
                this.error = e.message;
            } finally {
                this.deleting = false;
            }
        },

        resetInputHeight() {
            if (this.$refs.input) this.$refs.input.style.height = 'auto';
        },

        stop() {
            if (this.activeAbort) {
                this.activeAbort.abort();
                this.activeAbort = null;
            }
        },

        async send(regenerate = false) {
            const content = this.draft.trim();
            if (this.sending) return;
            if (!regenerate && !content) return;

            if (!regenerate && !this.sessionId) {
                await this.newSession();
                if (!this.sessionId) return;
            }

            if (!regenerate) {
                this.draft = '';
                this.resetInputHeight();
                this.messages.push({ id: Date.now(), role: 'user', content });
            }
            this.messages.push({ id: Date.now() + 1, role: 'assistant', content: '' });
            this.sending = true;
            this.error = '';
            const replyIndex = this.messages.length - 1;
            const controller = new AbortController();
            this.activeAbort = controller;
            this.$nextTick(() => this.scrollToBottom(true));

            try {
                const response = await fetch(`{{ route('ai-chat.index') }}/sessions/${this.sessionId}/stream`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        Accept: 'text/event-stream',
                    },
                    body: JSON.stringify({ content, regenerate }),
                    signal: controller.signal,
                });

                if (!response.ok || !response.body) {
                    let message = 'Failed to send message.';
                    try { message = (await response.json()).message ?? message; } catch (e) {}
                    throw new Error(message);
                }

                const reader = response.body.getReader();
                const decoder = new TextDecoder();
                let buffer = '';

                while (true) {
                    const { done, value } = await reader.read();
                    if (done) break;
                    buffer += decoder.decode(value, { stream: true });

                    let newline;
                    while ((newline = buffer.indexOf('\n')) !== -1) {
                        const line = buffer.slice(0, newline).trim();
                        buffer = buffer.slice(newline + 1);
                        if (!line.startsWith('data:')) continue;
                        const payload = line.slice(5).trim();
                        if (!payload || payload === '[DONE]') continue;

                        let evt;
                        try { evt = JSON.parse(payload); } catch (e) { continue; }

                        if (evt.error) throw new Error(evt.error);

                        if (evt.delta && this.messages[replyIndex]) {
                            this.messages[replyIndex].content += evt.delta;
                        }

                        if (evt.done && this.messages[replyIndex]) {
                            this.messages[replyIndex] = { ...this.messages[replyIndex], ...evt.reply };
                            if (evt.title) {
                                const current = this.sessions.find((s) => s.id === this.sessionId);
                                if (current) {
                                    current.title = evt.title;
                                    current.updated_at = new Date().toISOString();
                                }
                            }
                            this.loadMemory();
                        }
                    }
                }

                if (this.messages[replyIndex] && this.messages[replyIndex].content === '') {
                    this.messages.splice(replyIndex, 1);
                }
            } catch (e) {
                if (e.name === 'AbortError') {
                    // User stopped the stream — keep what we have.
                } else {
                    if (this.messages[replyIndex] && this.messages[replyIndex].content === '') {
                        this.messages.splice(replyIndex, 1);
                    }
                    this.error = e.message;
                }
            } finally {
                this.sending = false;
                this.activeAbort = null;
                this.$nextTick(() => this.$refs.input?.focus());
            }
        },

        async regenerate() {
            if (this.sending) return;
            // The last user message is the prompt to retry.
            const lastUser = [...this.messages].reverse().find(m => m.role === 'user');
            const content = lastUser?.content || '';
            // Drop the last assistant message locally and resend.
            for (let i = this.messages.length - 1; i >= 0; i--) {
                if (this.messages[i].role === 'assistant') {
                    this.messages.splice(i, 1);
                    break;
                }
            }
            this.draft = content;
            await this.send(true);
            this.draft = '';
        },

        async loadMemory() {
            if (!this.sessionId) return;
            try {
                const response = await fetch(`{{ route('ai-chat.index') }}/sessions/${this.sessionId}/messages`, {
                    headers: { Accept: 'application/json' },
                });
                if (!response.ok) return;
                const json = await response.json();
                this.hasMemory = json.data.memory.has;
                this.memoryPreview = json.data.memory.preview;
            } catch (e) {
                // Non-critical.
            }
        },
    }));
});
</script>
@endpush
