<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Mail\VerificationCodeMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;


class SendVerificationCode
{
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
    public function handle(UserRegistered $event): void
    {
        try {

            Mail::to($event->user->email)->send(new VerificationCodeMail($event->verificationCode));
            Log::channel('email')->info('Sent verification code to: ' . $event->user->email);
        } catch (\Exception $e) {
            Log::error('Failed to send verification code: ' . $e->getMessage());
        }
    }
}
