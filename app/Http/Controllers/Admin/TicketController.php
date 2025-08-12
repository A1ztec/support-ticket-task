<?php

namespace App\Http\Controllers\Admin;

use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Enums\Ticket\TicketStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ReplyToTicketRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TicketController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        if (request('status')) {
            $tickets = Ticket::where('status', request('status'))->with(['user', 'messages'])->paginate(10);
        } else {
            $tickets = Ticket::with(['user', 'messages'])->paginate(10);
        }

        return view('admin.tickets.index', compact('tickets'));
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['user', 'messages.user']);

        return view('admin.tickets.show', compact('ticket'));
    }

    public function reply(ReplyToTicketRequest $request, Ticket $ticket)
    {
        $this->authorize('reply', $ticket);

        $ticket->messages()->create([
            'user_id' => auth()->id(),
            'body' => $request->input('message'),
        ]);

        $ticket->update(['status' => TicketStatus::IN_PROGRESS]);

        return redirect()->route('admin.tickets.show', $ticket)->with('success', 'Reply sent successfully.');
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $this->authorize('updateStatus', $ticket);

        $ticket->update(['status' => $request->input('status')]);

        return redirect()->route('admin.tickets.show', $ticket)->with('success', 'Ticket status updated successfully.');
    }
}
