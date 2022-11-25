<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifyUserToNewPasswordAttributed extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $newPassword;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $newPassword)
    {
        $this->user = $user;
        $this->newPassword = $newPassword;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.notify-user-to-new-password-attributed')
            ->subject("Votre compte " . config("app.name") . " - Attribution de nouveau mot de passe");
    }
}
