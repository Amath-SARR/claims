@component('mail::message')
![{{ config('app.name') }}]({{ asset('assets/images/mbr-96x96.png') }})<br>

## Bonjour **_{{ $user->name }}_**,

Un administrateur a modifié le mot de passe de votre compte **{{ config('app.name') }}** le **_{{ \Carbon\Carbon::parse($user->updated_at)->translatedFormat('l j F Y à H:i:s')}} {{config('app.timezone')}}_**.

A présent votre nouveau mot de passe est **_{{ $newPassword }}_**.

Veuillez contactez immédiatement un administrateur si vous n'êtes pas à l'origine de cette demande.

@component('mail::button', ['url' => request()->getSchemeAndHttpHost().'/#!/login'])
    Ouvrir {{ config('app.name') }}
@endcomponent

_Si vous ne parvenez pas à cliquer sur le bouton ci-dessus, merci de copier et coller le lien suivant dans votre navigateur :_ {{ request()->getSchemeAndHttpHost().'/#!/login' }}

À très bientôt.

Cordialement, **{{ config('app.name') }}**.
@endcomponent
