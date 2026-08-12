<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function create(): View
    {
        return view('tickets.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'company_id' => ['nullable', 'exists:companies,id'],
        ]);

        $ticket = Ticket::create([
            ...$data,
            'status' => 'open',
            'priority' => 'normal',
        ]);

        return redirect()->route('tickets.create')->with('success', 'Ticket submitted — '.$ticket->ticket_code.'. We will reply via email.');
    }
}
