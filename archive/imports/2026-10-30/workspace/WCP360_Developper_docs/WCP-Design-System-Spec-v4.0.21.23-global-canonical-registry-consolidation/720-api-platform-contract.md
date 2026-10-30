# 720 --- API Platform Implementation Contract

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 1**

Ce document transforme les décisions V3.6 déjà acceptées en contrat de
développement. Il ne remplace pas les règles de
`MASTER-SECURE-DEVELOPMENT-STANDARD.md`.

## Envelope

JSON UTF-8 ; dates RFC3339 UTC ; IDs opaques ; champs inconnus tolérés
côté client lorsque possible.

## Erreurs

`type`, `title`, `status`, `code`, `detail`, `request_id`,
`correlation_id`, `errors[]` pour validation. Aucun stack trace externe.

## Collections

Cursor pagination ; `limit` borné ; filtres/sort explicitement
allowlistés.

## Versioning

`/api/v1`. Les changements incompatibles nécessitent nouvelle version ou
période de dépréciation documentée.

## Async

Une action longue retourne 202 et un Job. Le polling n'invente pas de
progression.

## V4.0.9 normative deepening

- `API-409-01-API-FIRST-CANONICAL-CONTRACT.md`
- `API-409-02-API-AUTH-SERVICE-ACCOUNTS.md`
- `API-409-05-IDEMPOTENCY-MUTATIONS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
