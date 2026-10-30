# 58 --- WCP-ADMIN : incidents, maintenance et changements

## Objectif V2

Structurer les opérations à risque au-delà d'un simple bouton
Start/Stop.

## Incident

Résumé, sévérité, ressources affectées, timeline, alertes corrélées,
actions prises, liens logs/jobs/audit.

## Maintenance

Fenêtre planifiée, scope, services concernés, début/fin, message
opérateur, tâches associées. Un mode maintenance doit être visible
globalement.

## Change

Pour configuration sensible : auteur, justification
optionnelle/obligatoire selon politique, diff, approbation si activée,
job d'application, résultat, rollback disponible.

## Règle

Incident, alerte, job et audit event sont liés mais restent des objets
distincts.
