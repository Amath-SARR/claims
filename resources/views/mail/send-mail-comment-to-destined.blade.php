@component('mail::message')
![{{ config('app.name') }}]({{ asset('assets/images/mbr-96x96.png') }})<br>

@if (!Auth::check())
Bonjour {{$comment->reclamation->user->name}}, <br>
Un réclameur a répondu à un commentaire sur la réclamation [**{{ $comment->reclamation->number}}**]-**{{$comment->reclamation->objet}}**.
@else

@if($comment->comment_id==null)
## Bonjour **{{$comment->reclamation->user->name}}**,
**{{ $comment->user->name}}** a répondu à un commentaire sur la réclamation [**{{ $comment->reclamation->number}}**]-**{{$comment->reclamation->objet}}**.
@else
## Bonjour {{$comment->comment->user->name}},
**{{ $comment->user->name}}** a répondu à votre commentaire sur la réclamation [**{{ $comment->reclamation->number}}**]-**{{$comment->reclamation->objet}}**.
@endif

@endif

Voici les détails du commentaire:
> {!! $comment->commentaire !!}.

Merci de cliquer sur le boutton ci-dessous pour ouvrir le commentaire:
@component('mail::button', ['url' => request()->getSchemeAndHttpHost(). '/#!'. '/reclamation/'. $comment->reclamation->id])
    Ouvrir le commentaire
@endcomponent

_Si vous ne parvenez pas à cliquer sur le bouton ci-dessus, merci de copier et coller le lien suivant dans votre navigateur :_ <br>
{{ request()->getSchemeAndHttpHost(). '/#!'. '/reclamation/'. $comment->reclamation->id }}

Cordialement, <br>
**{{ config('app.name') }}**.
@endcomponent
