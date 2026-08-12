<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $tickets = Ticket::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->string('search');
                $q->where(fn ($qq) => $qq->where('ticket_code', 'like', "%{$s}%")->orWhere('subject', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%")->orWhere('name', 'like', "%{$s}%"));
            })
            ->when($request->filled('status') && $request->input('status') !== 'all', fn ($q) => $q->where('status', $request->input('status')))
            ->latest()->paginate(15)->withQueryString();

        return view('admin.tickets.index', ['tickets' => $tickets]);
    }

    public function show(Ticket $ticket): View
    {
        $ticket->load(['replies.admin']);

        return view('admin.tickets.show', ['ticket' => $ticket]);
    }

    public function reply(Request $request, Ticket $ticket): RedirectResponse
    {
        $data = $request->validate(['message' => ['required', 'string', 'max:5000']]);

        $reply = $ticket->replies()->create([
            'admin_id' => auth('admin')->id(),
            'message' => $data['message'],
        ]);

        if ($ticket->status !== 'closed') {
            $ticket->update(['status' => 'answered']);
        }

        // ponytail: log mailer; upgrade to queue + HTML template when volume grows
        try {
            Mail::raw("Hi {$ticket->name},\n\nRe: {$ticket->subject} [{$ticket->ticket_code}]\n\n{$data['message']}\n\n— ".config('app.name').' Support', function ($m) use ($ticket) {
                $m->to($ticket->email)->subject('Re: '.$ticket->subject.' ['.$ticket->ticket_code.']');
            });
        } catch (\Throwable $e) {
            report($e);
        }

        return back()->with('success', 'Reply sent to '.$ticket->email);
    }

    public function updateStatus(Request $request, Ticket $ticket): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:open,pending,answered,closed']]);
        $ticket->update($data);

        return back()->with('success', 'Ticket status updated.');
    }

    public function destroy(Ticket $ticket): RedirectResponse
    {
        $ticket->delete();

        return redirect()->route('admin.tickets.index')->with('success', 'Ticket deleted.');
    }
}
