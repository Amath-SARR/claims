@component('mail::message')
![{{ config('app.name') }}]({{ asset('assets/images/mbr-96x96.png') }})<br>

## Bonjour **_{{$userClaimAssigned->name}}_**,

**_{{$authUser->name}}_** vous a assigné à une réclamation.

Quelques informations sur la réclamation:

## Objet : **{{ $reclamation->objet }}**

## Description :
{!! $reclamation->description !!}

Merci de cliquer sur le bouton ci-dessous pour voir la réclamation:
@component('mail::button', ['url' => request()->getSchemeAndHttpHost().'/#!/reclamation/'.$reclamation->id])
Voir la réclamation
@endcomponent

Si le clic sur le bouton ne fonctionne pas, merci de copier et coller le lien suivant dans un navigateur : {{request()->getSchemeAndHttpHost().'/#!/reclamation/'.$reclamation->id}}

Cordialement, **{{ config('app.name') }}**.
@endcomponent
