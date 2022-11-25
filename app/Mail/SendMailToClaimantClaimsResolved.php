<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMailToClaimantClaimsResolved extends Mailable
{
    use Queueable, SerializesModels;
    public $reclamation;
    public $userclaimsubmitted;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($reclamation)
    {
        $this->reclamation = $reclamation;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.send-mail-to-claimant-claims-resolved', ['reclamation'=>$this->reclamation])
        ->subject("Notification de prise en charge de réclamation - ".config("app.name","Application de suivi des reclamations..."));
    }
}
