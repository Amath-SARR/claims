<?php

namespace App\Mail;

use App\Models\Reclamation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendReclamateurRappelMail extends Mailable
{
    use Queueable, SerializesModels;
    private $reclamation;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Reclamation $reclamation)
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
        return $this->markdown(
            'mail.send-reclamateur-rappel-mail',
            ['reclamation' => $this->reclamation]
        )
            ->subject("[Notification] - Réclamation résolue depuis plusieurs jours sur - [" . config("app.name") . "]");
    }
}
