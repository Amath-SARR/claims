<?php

namespace App\Mail;

use App\Models\Reclamation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifyUserToNewClaimAttributed extends Mailable
{
    use Queueable, SerializesModels;
    private $userClaimAssigned;
    private $authUser;
    private $reclamation;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $userClaimAssigned, User $authUser, Reclamation $reclamation)
    {
        $this->userClaimAssigned = $userClaimAssigned;
        $this->authUser = $authUser;
        $this->reclamation = $reclamation;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.notify-user-to-new-claim-attributed',
        ['userClaimAssigned' => $this->userClaimAssigned,'authUser' => $this->authUser,'reclamation' => $this->reclamation])
          ->subject(config("app.name")." - ". $this->authUser->name ." vous a assigné une nouvelle réclamation");
    }
}
