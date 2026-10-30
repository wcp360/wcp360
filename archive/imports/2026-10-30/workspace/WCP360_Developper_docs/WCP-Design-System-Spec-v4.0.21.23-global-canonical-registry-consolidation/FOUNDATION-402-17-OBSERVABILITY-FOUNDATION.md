# V4.0.2 --- Observability Foundation

## Statut

**NORMATIVE --- V4.0.2 FOUNDATIONS DEEP SPECIFICATION --- NO CODE**

Ce document approfondit les fondations transversales de WCP. Il ne
choisit pas de framework, de bibliothèque ni de format exécutable. Toute
implémentation future devra respecter ce contrat.

## Séparation

Logs = événements techniques ; metrics = séries numériques ;
traces/correlation = chemin d'une opération ; health = aptitude actuelle
; audit = preuve de sécurité/métier.

## Labels

Éviter cardinalité non bornée. IDs de ressources peuvent être utiles
dans logs/traces mais ne deviennent pas automatiquement labels de
métriques.

## Health

`healthy`, `degraded`, `unhealthy`, `unknown`. Un health endpoint ne
doit pas déclencher d'opération lourde/destructive.

## SLO

Les modules critiques doivent définir ultérieurement indicateurs de
succès/latence/freshness adaptés plutôt qu'un unique « service up ».

## V4.0.10 normative deepening

- `OPS-4010-01-OBSERVABILITY-CANONICAL-MODEL.md`
- `OPS-4010-03-METRICS-MODEL.md`
- `OPS-4010-06-LOGGING-CANONICAL.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
