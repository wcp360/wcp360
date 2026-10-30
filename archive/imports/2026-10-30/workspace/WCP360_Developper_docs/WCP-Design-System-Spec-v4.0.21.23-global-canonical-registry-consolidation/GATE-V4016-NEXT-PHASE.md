# V4.0.16 --- Next Phase Gate

## Autorisé ensuite

Une V4.0.17 peut transformer l'architecture gelée en spécifications
d'implémentation **toujours sans code** : 1. canonical resource/data
model ; 2. database logical schema specification ; 3. API
endpoint/resource contract map ; 4. Job/state-machine catalog ; 5.
`wcpd` RPC method catalog ; 6. package/filesystem ownership tree ; 7.
systemd unit specification ; 8. configuration generation contracts ; 9.
error taxonomy ; 10. security acceptance tests ; 11. E2E acceptance
matrix ; 12. migration/versioning plan.

## Toujours interdit

Créer le repository, SQL exécutable, OpenAPI final généré, protobuf
compilable, scripts shell, services installables ou code applicatif
avant validation de cette couche.

## Raison

Le corpus passe de « quoi et pourquoi » à « contrats précis de comment
», sans franchir prématurément le seuil d'implémentation.
