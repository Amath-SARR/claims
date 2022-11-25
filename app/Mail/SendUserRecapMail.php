<?php

namespace App\Mail;

use App\Models\Application;
use App\Models\Reclamation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;


class SendUserRecapMail extends Mailable
{
    use Queueable, SerializesModels;
    private $user;
    private $tabApplicationAndReclamations;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, $tabApplicationAndReclamations)
    {
        $this->user = $user;
        $this->tabApplicationAndReclamations = $tabApplicationAndReclamations;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown(
            'mail.send-user-recap-mail',
            ['user' => $this->user, 'tabApplicationAndReclamations' => $this->tabApplicationAndReclamations]
        )
            ->subject("[Notification] - De nouveaux tickets vous attendent sur - [". config("app.name") . "]");
    }
}
