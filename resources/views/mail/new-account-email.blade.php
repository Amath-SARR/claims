@component('mail::message')
![{{ config('app.name') }}]({{ asset('assets/images/mbr-96x96.png') }})<br>

Bonjour **{{ $user->name }}**,

Bienvenue sur _la plateforme de gestion des réclamations de l’Université Iba
Der Thiam de Thiès(UIDT)_ dénommé **{{ config('app.name') }}**.

Votre compte est créé avec succès. <br>
Cette application vous permettra de suivre la réclamation
du _Personnel d’Enseignement et de Recherche_(PER), et/ou du _Personnel Administratif
Technique et de Service_(PATS) et/ou des _étudiants_ de l’Université Iba
Der Thiam de Thiès(UIDT).


Voici vos identifiants de connexion qui vous permettra de se connecter:
- ## Email:  {{ $user->email }}
- ## Mot de passe:  {{ $plainPassword }}



@component('mail::button', ['url' => request()->getSchemeAndHttpHost() ])
Ouvrir {{ config('app.name') }}
@endcomponent



Si le boutton ne fonctionne pas, merci de copier et coller le lien suivant dans votre navigateur:
{{ request()->getSchemeAndHttpHost() }}

Cordialement, <br>
{{ config('app.name') }}
@endcomponent

