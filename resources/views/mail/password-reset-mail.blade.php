@component('mail::message')
![{{ config('app.name') }}]({{ asset('assets/images/mbr-96x96.png') }})<br>

Bonjour **_{{ $user->name }}_**, souhaitiez-vous réinitialiser votre mot de passe ?

Quelqu'un (espérons que ce soit vous), nous a demandé de réinitialiser le mot de passe de votre compte
**{{ config('app.name') }}**.

Cliquez sur le bouton ci-dessous pour effectuer ce changement.

Si vous n'avez pas demandé à réinitialiser votre mot de passe, **ignorez ce message** !

@component('mail::button', ['url' => request()->getSchemeAndHttpHost() .'/#!'. '/password-reset/' . $passwordReset->token .'?email='. $user->email])
    Changer de mot de passe
@endcomponent

Si le lien ne fonctionne pas, merci de copier et coller le lien suivant dans votre navigateur:
[{{ request()->getSchemeAndHttpHost() .'/#!'. '/password-reset/' . $passwordReset->token .'?email='. $user->email}}]({{ request()->getSchemeAndHttpHost() .'/#!'. '/password-reset/' . $passwordReset->token .'?email='. $user->email }})

À très bientôt.

Cordialement, **{{ config('app.name') }}**.
@endcomponent
