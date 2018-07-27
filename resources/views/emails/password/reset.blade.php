@component('mail::message')
# Token de changement de mot de passe

@component('mail::panel')
{{ $pass->token }}
@endcomponent

Une demande a été faite sur {{ config('app.name') }} pour perte de mot de passe.

Ci-dessus se trouve le token à quatre caractères qu'il vous faudra renseigner
sur la page où la demande a été formulée.

Vous n'aurez plus qu'à compléter le troisième champ avec votre nouveau mot de
passe, et il vous sera à nouveau possible de vous connecter.

Bonne journée,<br>
{{ config('app.name') }}
@endcomponent
