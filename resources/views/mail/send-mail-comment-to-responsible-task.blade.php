@component('mail::message')
![{{ config('app.name') }}]({{ asset('assets/images/mbr-96x96.png') }})<br>

Bonjour **{{$comment->reclamation->user->name}}**, <br>
**{{ $comment->user->name}}** a répondu à un commentaire sur la réclamation [**{{ $comment->reclamation->number}}**]-**{{$comment->reclamation->objet}}**.

Voici les détails du commentaire:
> {!! $comment->commentaire !!}.

Merci de cliquer sur le boutton ci-dessous pour ouvrir le commentaire:

@component('mail::button', ['url' => request()->getSchemeAndHttpHost(). '/#!'. '/reclamation/'. $comment->reclamation->id ])
    Ouvrir le commentaire
@endcomponent

_Si vous ne parvenez pas à cliquer sur le bouton ci-dessus, merci de copier et coller le lien suivant dans votre navigateur :_ <br>
{{ request()->getSchemeAndHttpHost(). '/#!'. '/reclamation/'. $comment->reclamation->id }}

Cordialement, <br>
**{{ config('app.name') }}**.
@endcomponent
