<?php

namespace App\Http\Controllers\Api;

use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Enums\Ticket\TicketStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Ticket\TicketResource;
use App\Http\Requests\Api\CreateTicketRequest;
use App\Http\Requests\Api\ReplyToTicketRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TicketController extends Controller
{
    use ApiResponseTrait, AuthorizesRequests;

    public function listTickets()
    {
        $user = Auth::user();

        $tickets = $user->tickets()
            ->with(['user', 'messages.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->successResponse(
            data: TicketResource::collection($tickets),
            message: $tickets->isEmpty()
                ? __('No tickets found.')
                : __('Tickets retrieved successfully.')
        );
    }

    public function showTicket(Ticket $ticket)
    {
        if (!$ticket) {
            return $this->notFoundResponse(__('Ticket not found.'));
        }

        $ticket->load(['user', 'messages.user']);

        $this->authorize('view', $ticket);

        return $this->successResponse(
            data: TicketResource::make($ticket),
            message: __('Ticket retrieved successfully.')
        );
    }

    public function createTicket(CreateTicketRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request->validated();
            $user = Auth::user();
            $attachmentPath = null;

            if (isset($data['attachment'])) {
                $file = $data['attachment'];
                $filename = 'ticket_' . time() . '_' . $file->getClientOriginalName();


                $attachmentPath = $file->storeAs('tickets/attachments', $filename, 'public');

                if (!$attachmentPath) {
                    throw new \Exception('Failed to store attachment file');
                }
            }

            try {
                $ticket = $user->tickets()->create([
                    'subject'    => $data['subject'],
                    'message'    => $data['message'],
                    'attachment' => $attachmentPath,
                    'status'     => TicketStatus::OPEN,
                ]);

                $ticket->load(['user', 'messages.user']);

                Log::info('Ticket created successfully', [
                    'user_id'   => $user->id,
                    'ticket_id' => $ticket->id
                ]);

                return $this->successResponse(
                    data: TicketResource::make($ticket),
                    message: __('Ticket created successfully.'),
                    code: 201
                );
            } catch (\Exception $e) {

                if ($attachmentPath) {
                    Storage::disk('public')->delete($attachmentPath);
                }
                throw $e;
            }
        });
    }

    public function downloadAttachment(Ticket $ticket)
    {
        if (!$ticket) {
            return $this->notFoundResponse(__('Ticket not found.'));
        }

        if (!$ticket->attachment) {
            return $this->notFoundResponse(__('No attachment found for this ticket.'));
        }

        $filePath = storage_path('app/public/' . $ticket->attachment);

        if (!file_exists($filePath)) {
            return $this->notFoundResponse(__('Attachment file not found.'));
        }

        $this->authorize('canDownloadAttachment', $ticket);

        return response()->download($filePath);
    }

    public function reply(ReplyToTicketRequest $request)
    {
        $data = $request->validated();

        $ticket = Ticket::findOrFail($data['ticket_id']);

        $this->authorize('reply', $ticket);

        $ticket->messages()->create([
            'user_id' => Auth::id(),
            'message' => $data['message'],
        ]);

        $ticket->update(['status' => TicketStatus::OPEN]);

        $ticket->load(['user', 'messages.user']);

        return $this->successResponse(
            data: TicketResource::make($ticket),
            message: __('Reply sent successfully.')
        );
    }
}
