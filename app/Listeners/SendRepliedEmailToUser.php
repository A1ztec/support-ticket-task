<?php

namespace App\Listeners;

use App\Events\TicketReplied;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\TicketRepliedNotification;

class SendRepliedEmailToUser implements ShouldQueue
{
    use InteractsWithQueue;

    public $tries = 3;
    public $backoff = [10, 30, 60];
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
   try {
            Log::info('SendRepliedEmailToUser started', [
                'ticket_id' => $event->ticket->id
            ]);


            $ticket = $event->ticket->load(['user', 'messages.user']);
            $user = $ticket->user;

            if (!$user) {
                Log::warning('No user found for ticket', [
                    'ticket_id' => $ticket->id
                ]);
                return;
            }

            if (!$user->email) {
                Log::warning('User has no email', [
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id
                ]);
                return;
            }

            n
            $user->notify(new TicketRepliedNotification($ticket));

            Log::info('Ticket reply notification sent successfully', [
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'user_email' => $user->email
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send ticket reply notification', [
                'ticket_id' => $event->ticket->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e; // Rethrow to retry
        }
    }
}
