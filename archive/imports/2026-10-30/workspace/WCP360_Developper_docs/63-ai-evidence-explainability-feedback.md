# 63 --- IA : preuves, explicabilité et feedback

## Evidence

Une recommandation doit pointer vers les signaux qui la motivent :
configuration, métrique, log, règle détectée ou documentation locale,
lorsque disponible.

## Séparation

`Observation système` ≠ `Interprétation IA` ≠ `Action proposée`.

## Feedback

Utile / non utile / incorrect, avec commentaire optionnel. Le feedback
n'autorise aucune mutation.

## Données

Le produit doit documenter quelles données sont envoyées au moteur IA,
local ou externe. Les secrets sont exclus ou redacted par défaut.

## Historique

Conserver mission, version, scope, résultat, approbateur et exécution
liée selon politique de rétention.
