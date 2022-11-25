@component('mail::message')
![{{ config('app.name') }}]({{ asset('assets/images/mbr-96x96.png') }})<br>
## ***{{ config("app.name") }}***

Bonjour, <br>

Vous avez reçu un nouveau message sur la réclamation ***{{ $comment->reclamation->objet }}***
que vous avez soumis le ***{{ $comment->reclamation->created_at->format('d-m-Y') }}*** portant sur
 l'application **{{ $comment->reclamation->categorieReclamation->application->nom }}**.

 Description de la réclamation:
 >*{!! $comment->reclamation->description !!}*

Contenu du message:
<hr>
{!! $comment->commentaire !!}.
<hr>
<br>
Pour répondre à ce message merci de cliquer sur le boutton ci-dessous :
@if ($comment->for_reclameur)
@component('mail::button', ['url' => request()->getSchemeAndHttpHost(). '/#!'. '/comment/reclameur/'. $comment->uid])
    Répondre au message
@endcomponent

_Si vous ne parvenez pas à cliquer sur le bouton ci-dessus, merci de copier et coller le lien ci-dessous dans votre
navigateur :_ <br>
{{ request()->getSchemeAndHttpHost(). '/#!'. '/comment/reclameur/'. $comment->uid }}
@else
@component('mail::button', ['url' => request()->getSchemeAndHttpHost(). '/#!'. '/comment/reclameur/'. $comment->comment->uid])
    Répondre au message
@endcomponent

_Si vous ne parvenez pas à cliquer sur le bouton ci-dessus, merci de copier et coller le lien ci-dessous dans votre
navigateur :_ <br>
{{ request()->getSchemeAndHttpHost(). '/#!'. '/comment/reclameur/'. $comment->comment->uid }}
@endif


À très bientôt.

Cordialement,
**{{ config('app.name') }}**.
@endcomponent
