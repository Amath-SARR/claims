<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMailToNewUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public $oldMail;
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($oldMail, $user)
    {
        $this->oldMail = $oldMail;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.send-mail-to-new-user-mail')
            ->subject("Changement de votre adresse email dans " . config("app.name"));
    }
}
