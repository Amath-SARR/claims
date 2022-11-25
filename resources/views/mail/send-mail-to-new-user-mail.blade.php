@component('mail::message')
![{{ config('app.name') }}]({{ asset('assets/images/mbr-96x96.png') }})<br>
## Bonjour **_{{ $user->name }}_**,

**{{ config('app.name') }}** _- la plateforme de gestion des réclamations_.
<br>

Votre adresse email a été remplacée avec succès. <br>
Voici les détails du changement:
- ## Email précédent:  **{{ $oldMail }}**.
- ## Nouvel email:  **{{ $user->email }}**.

| Accedez à la plateforme pour vous connecter en cliquant sur le bouton suivant :

@component('mail::button', ['url' => request()->getSchemeAndHttpHost()])
    Ouvrir {{ config('app.name') }}
@endcomponent

_Si vous ne parvenez pas à cliquer sur le bouton ci-dessus, merci de copier et coller le lien suivant dans votre navigateur :_ <br>
{{ request()->getSchemeAndHttpHost() }}

Cordialement, <br>
**{{ config('app.name') }}**.
@endcomponent
