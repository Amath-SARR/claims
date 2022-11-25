@component('mail::message')
![{{ config('app.name') }}]({{ asset('assets/images/mbr-96x96.png') }})<br>

## Bonjour **_{{ $user->name }}_**,

Vous avez reçu de nouvelles réclamations qui sont en attentes depuis 1h de temps.

@for ($i = 0; $i < count($tabApplicationAndReclamations); $i++)

---
## Application concernée : _{{ $tabApplicationAndReclamations[$i]['application']->nom }}_


- Nombre de réclamation : {{ $tabApplicationAndReclamations[$i]['reclamations']->count() }}

@foreach ($tabApplicationAndReclamations[$i]['reclamations'] as $reclamation)

**Catégorie de la réclamation** : {{ $reclamation->categorieReclamation->nom }}

**Priorité** : {{ $reclamation->priorite->nom }}

*Code*: [{{ $reclamation->number }}]( {{ request()->getSchemeAndHttpHost() }}/#!/reclamation/{{ $reclamation->id }} ) - [{{ $reclamation->objet }}]( {{ request()->getSchemeAndHttpHost() }}/#!/reclamation/{{ $reclamation->id }} )

@component('mail::button', ['url'=> request()->getSchemeAndHttpHost() . '/#!/reclamation/' . $reclamation->id])
Afficher la réclamation
@endcomponent

@endforeach

@endfor

---

Merci de cliquer sur le bouton ci-dessous pour accéder à la plateforme:
@component('mail::button', ['url' => request()->getSchemeAndHttpHost() . '/#!/reclamation-dashboard'])
Voir toutes les réclamations
@endcomponent

À très bientôt.

Cordialement, **{{ config('app.name') }}**.
@endcomponent
