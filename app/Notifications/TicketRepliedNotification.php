<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Mail\TicketReplyMail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TicketRepliedNotification extends Notification implements ShouldQueue
{
    use Queueable, InteractsWithQueue;


    public $tries = 3;
    public $backoff = [10, 30, 60];
    public $timeout = 120;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Ticket $ticket)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): TicketReplyMail
    {
        return (new TicketReplyMail($this->ticket))
            ->to($notifiable->email);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
