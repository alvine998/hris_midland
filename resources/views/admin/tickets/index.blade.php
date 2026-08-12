@extends('admin.layouts.admin')

@section('title', 'Tickets')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div><h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Customer Tickets</h2><p class="text-sm text-gray-500">Incoming support tickets — read & reply via email.</p></div>
    </div>

    <form method="GET" action="{{ route('admin.tickets.index') }}" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search code, subject, name, email..." class="px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm">
            <select name="status" class="px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm">
                <option value="all">All Status</option>
                @foreach(['open','pending','answered','closed'] as $s)<option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>@endforeach
            </select>
            <div class="flex gap-2"><button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-sm">Filter</button>@if(request()->hasAny(['search','status']))<a href="{{ route('admin.tickets.index') }}" class="px-4 py-2.5 text-sm text-gray-600">Clear</a>@endif</div>
        </div>
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs font-semibold text-gray-500 uppercase border-b border-gray-200 dark:border-gray-700"><th class="px-5 py-3">Ticket</th><th class="px-5 py-3">Subject</th><th class="px-5 py-3">Customer</th><th class="px-5 py-3">Status</th><th class="px-5 py-3 text-right">Actions</th></tr></thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($tickets as $t)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                        <td class="px-5 py-4"><span class="font-mono text-xs font-medium">{{ $t->ticket_code }}</span><div class="text-xs text-gray-500">{{ $t->created_at->format('d M Y H:i') }}</div></td>
                        <td class="px-5 py-4"><div class="font-medium">{{ Str::limit($t->subject, 50) }}</div><div class="text-xs text-gray-500">{{ Str::limit($t->message, 60) }}</div></td>
                        <td class="px-5 py-4"><div class="font-medium">{{ $t->name }}</div><div class="text-xs text-gray-500">{{ $t->email }}</div></td>
                        <td class="px-5 py-4"><span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $t->status==='open'?'bg-amber-100 text-amber-700':($t->status==='answered'?'bg-green-100 text-green-700':($t->status==='closed'?'bg-gray-200 text-gray-700':'bg-blue-100 text-blue-700')) }}">{{ ucfirst($t->status) }}</span></td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('admin.tickets.show', $t) }}" class="inline-flex px-3 py-1.5 text-xs font-medium text-indigo-600 hover:bg-indigo-50 rounded-lg">View & Reply</a>
                            <form method="POST" action="{{ route('admin.tickets.destroy', $t) }}" class="inline" onsubmit="return confirm('Delete ticket?')">@csrf @method('DELETE')<button type="submit" class="inline-flex px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 rounded-lg">Delete</button></form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-10 text-center text-sm text-gray-500">No tickets yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($tickets->hasPages())<div>{{ $tickets->links() }}</div>@endif
</div>
@endsection
