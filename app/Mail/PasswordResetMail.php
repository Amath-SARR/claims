<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;
    public $user, $passwordReset;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $passwordReset)
    {
        $this->user = $user;
        $this->passwordReset = $passwordReset;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.password-reset-mail')
            ->subject('Réinitialisation de votre mot de passe - ' . config('app.name'));
    }
}
