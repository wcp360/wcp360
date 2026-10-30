# V4.0.21.13 --- Source of Truth / Supersession Rules

## Problème

Un corpus volumineux peut conserver d'anciens `PARTIAL` alors qu'une
deep closure plus récente existe.

## Règle

Chaque famille doit déclarer : `canonical_document`, `supersedes[]`,
`supplements[]`, `deprecated[]`, `effective_version`.

## Résolution

Une ancienne spécification n'est jamais modifiée silencieusement pour
paraître READY. Elle est marquée superseded/deprecated par métadonnée
documentaire et le registre pointe vers la source canonique actuelle.

## Conflit

Deux documents canoniques incompatibles → `DECISION_REQUIRED`, jamais
"latest file wins" implicite.
