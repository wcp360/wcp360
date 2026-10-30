# ADR-017 --- API Versioning, Idempotency & Error Contract

## Statut

**ACCEPTED FOR V1 --- V4.0.16 --- NO CODE**

## Autorité

Cet ADR ferme un choix P0 identifié par `ADR-V4015-07-REGISTER.md`. Il
concrétise la baseline V4.0.15 sans élargir le scope fonctionnel.

## Règles

-   Toute implémentation incompatible exige un ADR de supersession.
-   Les secrets ne sont jamais placés en clair dans les arguments de
    processus, logs ou artefacts exportés.
-   Toute mutation privilégiée reste typée, auditable,
    idempotente/reconciliable et vérifiée par état observé.
-   Le V1 reste single-node-first tout en conservant des interfaces qui
    n'interdisent pas le cluster futur.

## Décision

REST JSON versionnée sous `/api/v1`; UI WCP utilise les mêmes services
métier, sans API privée contournant les règles.

## Mutations longues

Retour `202` + Job resource. Mutations rapides peuvent être synchrones
si bornées.

## Idempotency

`Idempotency-Key` obligatoire/recommandée selon classe de mutation ;
fingerprint de requête et réponse/job associés avec TTL/policy.

## Errors

Envelope stable : code machine, message safe, field errors, correlation
ID, retryability et optional remediation link. Pas de stack
trace/secrets.

## Concurrency

ETag/version ou precondition pour ressources sensibles afin d'éviter
lost update.

## Pagination

Cursor-based pour collections volumineuses ; filtres/sorts allowlistés.

## Deprecation

Versioning et sunset documentés ; breaking change interdit dans v1 sans
migration/version.
