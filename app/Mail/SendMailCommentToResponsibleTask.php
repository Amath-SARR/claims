<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMailCommentToResponsibleTask extends Mailable
{
    use Queueable, SerializesModels;
    public $comment;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.send-mail-comment-to-responsible-task', ['comment' => $this->comment])
        ->subject("[". $this->comment->reclamation->number ."]  Nouveau commentaire-". $this->comment->reclamation->objet . " ". config("app.name", "Application de suivi des reclamations..."));
    }
}
