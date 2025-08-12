<?php

namespace App\Listeners;

use App\Events\TicketReplied;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\TicketRepliedNotification;

class SendRepliedEmailToUser
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
    public function handle(TicketReplied $event): void
    {
        $user = $event->ticket->user;
        if ($user) {
            try {
                $user->notify(new TicketRepliedNotification($event->ticket));
            } catch (\Exception $e) {
                Log::error('Failed to send ticket reply notification: ' . $e->getMessage());
            }
        } else {
            Log::warning('No user found for ticket ID: ' . $event->ticket->id);
        }
    }
}
