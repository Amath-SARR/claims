@component('mail::message')
![{{ config('app.name') }}]({{ asset('assets/images/mbr-96x96.png') }})<br>

# Bonjour,


**{{ config('app.name') }}** _- la plateforme de gestion des réclamations_ de UIDT.
<br>

Votre réclamation portant sur **{{ $reclamation->objet }}** précédemment soumise à l'équipe Support pour l'application **{{ $reclamation->categorieReclamation->application->nom }}** est marquée résolue. <br>
Voici les détails que vous aviez soumis:
- ## Soumise: {{ \Carbon\Carbon::parse($reclamation->created_at)->translatedFormat('l j F Y à H:i:s')}}.
- ## Description: 
  {!! $reclamation->description  !!}.


 Si vous êtes satisfait(e) de la résolution de votre réclamation, merci de  cliquer sur le bouton suivant:

<!-- Pour toute autre réclamation merci de cliquer sur le boutton suivant pour accéder à la plateforme : -->
@component('mail::button', ['url' => request()->getSchemeAndHttpHost().'/#!'. '/reclamation/satisfaction/'.$reclamation->uid])
    Confirmer la satisfaction
    <!-- Ouvrir {{ config('app.name') }} -->
@endcomponent
<br>

_Si vous ne parvenez pas à cliquer sur le bouton ci-dessus, merci de copier et coller le lien suivant dans votre navigateur :_ <br>
{{ request()->getSchemeAndHttpHost() .'/#!/reclamation/satisfaction/'.$reclamation->uid }}
<br> <br>

Si vous pensez que la réclamation n'est pas correctement prise en charge, veuillez cliquer sur le lien suivant:
{{ request()->getSchemeAndHttpHost(). '/#!/reclamation/insatisfaction/'. $reclamation->uid }}


Cordialement, <br>
**{{ config('app.name') }}**.
@endcomponent
