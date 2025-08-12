<?php

namespace App\Http\Controllers\Admin;

use App\Models\Ticket;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Events\TicketReplied;
use App\Enums\Ticket\TicketStatus;
use App\Http\Controllers\Controller;
use Illuminate\Container\Attributes\Log;
use App\Http\Requests\UpdateStatusRequest;
use App\Http\Requests\Api\ReplyToTicketRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TicketController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $query = Ticket::with(['user', 'messages']);


        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }


        $tickets = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.tickets.index', compact('tickets'));
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['user', 'messages.user']);

        return view('admin.tickets.show', compact('ticket'));
    }

    public function reply(ReplyToTicketRequest $request, Ticket $ticket)
    {
        $data = $request->validated();

        $this->authorize('reply', $ticket);

        try {
            $ticket->messages()->create([
                'user_id' => auth()->id(),
                'message' => $data['message'],
            ]);


            $ticket->update(['status' => TicketStatus::IN_PROGRESS]);

            event(new TicketReplied($ticket));

            return redirect()->route('admin.tickets.show', $ticket)
                ->with('success', 'Reply sent successfully.');
        } catch (\Exception $e) {

            Log::error('Failed to send reply', [
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('admin.tickets.show', $ticket)
                ->with('error', 'Failed to send reply.');
        }
    }

    public function updateStatus(UpdateStatusRequest $request, Ticket $ticket)
    {
        $data = $request->validated();

        $this->authorize('updateStatus', $ticket);

        $newStatus = $data['status'];

        $ticket->update(['status' => $newStatus]);

        $statusMessages = [
            'open' => 'Ticket reopened',
            'in_progress' => 'Ticket marked as in progress',
            'closed' => 'Ticket closed'
        ];

        $message = $statusMessages[$newStatus] ?? 'Ticket status updated';

        return redirect()->route('admin.tickets.show', $ticket)
            ->with('success', $message . ' successfully.');
    }
}
