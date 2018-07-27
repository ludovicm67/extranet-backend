<?php

namespace App\Mail;

use App\ResetPassword as Pass;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $pass;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Pass $pass)
    {
      $this->pass = $pass;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.password.reset');
    }
}
