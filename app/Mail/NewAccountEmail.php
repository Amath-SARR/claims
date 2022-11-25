<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewAccountEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
    public $plainPassword;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user,$plainPassword)
    {
        $this->user = $user;
        $this->plainPassword = $plainPassword;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.new-account-email',['user'=>$this->user,'plainPassword'=>$this->plainPassword])
        ->subject("Création compte - ".config("app.name","Application de suivi des reclamations..."));
    }
}
