<?php

namespace App\Http\Controllers\Api;

use Exception;
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
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TicketController extends Controller
{
    use ApiResponseTrait;

    public function listTickets()
    {
        try {
            $user = Auth::user();
            $tickets = $user->tickets()
                ->with(['messages.user'])
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->successResponse(
                data: TicketResource::collection($tickets),
                message: $tickets->isEmpty()
                    ? __('No tickets found.')
                    : __('Tickets retrieved successfully.')
            );
        } catch (Exception $e) {
            Log::error('Error retrieving tickets: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
            ]);
            return $this->errorResponse(__('Failed to retrieve tickets.'), 500);
        }
    }

    public function showTicket($id)
    {
        try {
            $user = Auth::user();
            $ticket = $user->tickets()
                ->with(['messages.user'])
                ->findOrFail($id);

            $this->authorize('view', [$user, $ticket]);

            return $this->successResponse(
                data: TicketResource::make($ticket),
                message: __('Ticket retrieved successfully.')
            );
        } catch (AuthorizationException $e) {
            return $this->errorResponse(__('You are not authorized to view this ticket.'), 403);
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse(__('Ticket not found.'));
        } catch (Exception $e) {
            Log::error('Error retrieving ticket: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'ticket_id' => $id
            ]);
            return $this->errorResponse(__('Failed to retrieve ticket.'), 500);
        }
    }

    public function createTicket(CreateTicketRequest $request)
    {
        try {

            return DB::transaction(function () use ($request) {
                $data = $request->validated();
                $user = Auth::user();
                $attachmentPath = null;


                if (isset($data['attachment'])) {
                    $file = $data['attachment'];
                    $filename = 'ticket_' . time() . '_' . $file->getClientOriginalName();
                    $attachmentPath = $file->storeAs('tickets/attachments', $filename, 'public');
                }

                $ticket = $user->tickets()->create([
                    'subject' => $data['subject'],
                    'message' => $data['message'],
                    'attachment' => $attachmentPath,
                    'status' => TicketStatus::OPEN,
                ]);


                $ticket->load(['messages.user']);

                Log::info('Ticket created successfully', [
                    'user_id' => $user->id,
                    'ticket_id' => $ticket->id
                ]);

                return $this->successResponse(
                    data: TicketResource::make($ticket),
                    message: __('Ticket created successfully.'),
                    code: 201
                );
            });
        } catch (Exception $e) {
            Log::error('Error creating ticket: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'request_data' => $request->safe()->except(['attachment'])
            ]);


            if (isset($attachmentPath) && $attachmentPath) {
                Storage::disk('public')->delete($attachmentPath);
            }

            return $this->errorResponse(__('Failed to create ticket.'), 500);
        }
    }

    public function downloadAttachment($ticketId)
    {
        try {
            $user = Auth::user();
            $ticket = $user->tickets()->findOrFail($ticketId);



            if (!$ticket->attachment) {
                return $this->notFoundResponse(__('No attachment found for this ticket.'));
            }

            $filePath = storage_path('app/public/' . $ticket->attachment);

            if (!file_exists($filePath)) {
                return $this->notFoundResponse(__('Attachment file not found.'));
            }

            $this->authorize('canDownloadAttachment', [$user, $ticket]);

            return response()->download($filePath);
        } catch (AuthorizationException $e) {
            return $this->errorResponse(__('You are not authorized to download this attachment.'), 403);
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse(__('Ticket not found.'));
        } catch (Exception $e) {
            Log::error('Error downloading attachment: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'ticket_id' => $ticketId
            ]);
            return $this->errorResponse(__('Failed to download attachment.'), 500);
        }
    }


    public function reply(ReplyToTicketRequest $request)
    {
        try {

            $data = $request->validated();
            $ticket = Ticket::findOrFail($data['ticket_id']);

            dd($ticket->user_id, Auth::id());

            $this->authorize('reply', $ticket);



            $message = $ticket->messages()->create([
                'user_id' => Auth::id(),
                'message' => $data['message'],
            ]);


            $ticket->update(['status' => TicketStatus::OPEN]);

            $ticket->load(['user', 'messages.user']);

            return $this->successResponse(
                data: TicketResource::make($ticket),
                message: __('Reply sent successfully.')
            );
        } catch (AuthorizationException $e) {
            return $this->errorResponse(__('You are not authorized to reply to this ticket.'), 403);
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse(__('Ticket not found.'));
        } catch (Exception $e) {
            Log::error('Error replying to ticket: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'ticket_id' => $data['ticket_id']
            ]);
            return $this->errorResponse(__('Failed to send reply.'), 500);
        }
    }
}
