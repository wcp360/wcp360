# ADR-008 --- DNS Engine Baseline

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

## Options

BIND ; PowerDNS Authoritative ; Knot DNS.

## Décision

**PowerDNS Authoritative** est la baseline DNS locale V1, avec backend
SQL dédié et API/adapter WCP. Les providers DNS externes restent
adapters.

## Raisons

Séparation service/API, automation structurée, DNSSEC et trajectoire
multi-node plus naturelle qu'une édition directe de fichiers de zones
comme contrat central.

## Ownership

WCP possède les zones créées/importées sous son mode managed. Les zones
external-unmanaged restent lecture/indication seulement.

## DNSSEC

Keys et rollover suivent secret/key lifecycle ; DS/delegation externe
reste un workflow distinct.

## Failure

Un write DB réussi n'est pas suffisant : authoritative
query/serial/health vérifient l'état.
