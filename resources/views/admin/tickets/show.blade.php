@extends('admin.layouts.admin')

@section('title', 'Ticket ' . $ticket->ticket_code)

@section('content')
<div class="space-y-6 max-w-3xl">
    <a href="{{ route('admin.tickets.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Back to tickets</a>
    <h2 class="text-lg font-semibold">{{ $ticket->ticket_code }} — {{ $ticket->subject }}</h2>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 space-y-3 text-sm">
        <div class="flex justify-between"><span class="text-gray-500">From</span><span class="font-medium">{{ $ticket->name }} &lt;{{ $ticket->email }}&gt;</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Status</span><span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $ticket->status==='open'?'bg-amber-100 text-amber-700':($ticket->status==='answered'?'bg-green-100 text-green-700':($ticket->status==='closed'?'bg-gray-200 text-gray-700':'bg-blue-100 text-blue-700')) }}">{{ ucfirst($ticket->status) }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Created</span><span>{{ $ticket->created_at->format('d M Y H:i') }}</span></div>
        <div class="pt-3 border-t border-gray-100 dark:border-gray-700"><div class="text-gray-500 mb-1">Message</div><div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-3 whitespace-pre-wrap">{{ $ticket->message }}</div></div>
    </div>

    <form method="POST" action="{{ route('admin.tickets.update-status', $ticket) }}" class="flex gap-2">
        @csrf @method('PATCH')
        <select name="status" class="px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm">
            @foreach(['open','pending','answered','closed'] as $s)<option value="{{ $s }}" @selected($ticket->status===$s)>{{ ucfirst($s) }}</option>@endforeach
        </select>
        <button type="submit" class="px-4 py-2.5 bg-gray-800 text-white rounded-xl text-sm">Update status</button>
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="font-semibold text-sm mb-3">Replies ({{ $ticket->replies->count() }})</h3>
        @forelse($ticket->replies->reverse() as $r)
            <div class="border-b border-gray-100 dark:border-gray-700 py-3 last:border-0">
                <div class="text-xs text-gray-500">{{ $r->admin?->name ?? 'Admin' }} · {{ $r->created_at->format('d M Y H:i') }}</div>
                <div class="mt-1 text-sm whitespace-pre-wrap">{{ $r->message }}</div>
            </div>
        @empty
            <p class="text-sm text-gray-500">No replies yet.</p>
        @endforelse
    </div>

    <form method="POST" action="{{ route('admin.tickets.reply', $ticket) }}" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1.5">Reply to {{ $ticket->email }}</label>
            <textarea name="message" rows="5" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm @error('message') border-red-500 @enderror" placeholder="Type your reply...">{{ old('message') }}</textarea>
            @error('message')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            <p class="text-xs text-gray-500 mt-1">Reply will be emailed to customer. Ticket will be marked answered.</p>
        </div>
        <button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-sm">Send reply</button>
    </form>
</div>
@endsection
