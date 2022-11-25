@component('mail::message')
![{{ config('app.name') }}]({{ asset('assets/images/mbr-96x96.png') }})<br>

## Bonjour,

Vous avez une réclamation résolue depuis plusieurs jours.

## Application concernée : _{{ $reclamation->categorieReclamation->application->nom }}_

---

Bref description de la réclamation:

*Catégorie de la réclamation*: {{ $reclamation->categorieReclamation->nom }}

*Objet*: [{{ $reclamation->objet }}]( {{ request()->getSchemeAndHttpHost() }}/#!/ )

---

Merci de cliquer sur le bouton ci-dessous pour accéder à la plateforme:
@component('mail::button', ['url' => request()->getSchemeAndHttpHost() . '/#!/'])
    {{ config('app.name') }}
@endcomponent

À très bientôt.

Cordialement, **{{ config('app.name') }}**.
@endcomponent
