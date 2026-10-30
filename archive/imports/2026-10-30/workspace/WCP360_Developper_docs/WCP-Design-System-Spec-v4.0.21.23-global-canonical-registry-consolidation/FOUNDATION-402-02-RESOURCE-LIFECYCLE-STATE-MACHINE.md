# V4.0.2 --- Resource Lifecycle & State Machine

## Statut

**NORMATIVE --- V4.0.2 FOUNDATIONS DEEP SPECIFICATION --- NO CODE**

Ce document approfondit les fondations transversales de WCP. Il ne
choisit pas de framework, de bibliothèque ni de format exécutable. Toute
implémentation future devra respecter ce contrat.

## États canoniques

Tous les modules ne doivent pas utiliser tous les états, mais ils
doivent mapper vers une sémantique commune : `pending`, `provisioning`,
`active`, `updating`, `suspending`, `suspended`, `deleting`, `deleted`,
`failed`, `degraded`, `unknown`.

## Transitions

Une transition est demandée par un acteur, validée par
policy/RBAC/entitlements, matérialisée par un Job et terminée uniquement
après vérification.

`failed` signifie qu'une opération a échoué ; `degraded` signifie que la
ressource existe mais qu'une partie de son service est défaillante ;
`unknown` signifie que WCP ne dispose pas de preuve suffisante.

## Interdictions

Ne jamais transformer une erreur de lecture en `deleted`. Ne jamais
considérer un timeout comme un échec sans effet. Ne jamais annoncer
`active` uniquement parce qu'un fichier a été écrit.

## Recovery

Chaque transition mutante doit déclarer : retry-safe ou non, méthode de
verify, stratégie reconcile, compensation possible/impossible et
conditions d'intervention humaine.

## V4.0.12 normative deepening

- `LIFE-4012-15-DEPRECATION-EOL.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.
