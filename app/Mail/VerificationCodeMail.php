<?php


namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationCodeMail extends Mailable
{
use Queueable, SerializesModels;



public function __construct(public int $code)
{

}

public function build()
{
return $this->subject('Your Verification Code')
->view('emails.verification_code')
->with(['code' => $this->code]);
}
}
