# MASTER ADR GOVERNANCE

## Objectif

Fermer les `DECISION REQUIRED` sans transformer des hypothèses en
exigences silencieuses.

## Format obligatoire d'un ADR

`Status` · `Context` · `Decision` · `Alternatives` · `Consequences` ·
`Security impact` · `Migration/rollback` · `Affected specs`.

## États

`PROPOSED` → `ACCEPTED` → `SUPERSEDED` ou `DEPRECATED`.

## Règle

Une spécification `PROPOSAL` qui dépend d'un choix de technologie reste
`PARTIAL` tant que l'ADR correspondant n'est pas `ACCEPTED`.
