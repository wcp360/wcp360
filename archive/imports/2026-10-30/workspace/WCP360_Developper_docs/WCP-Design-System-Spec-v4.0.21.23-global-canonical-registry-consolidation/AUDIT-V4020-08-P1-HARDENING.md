# V4.0.20 --- P1 Hardening Findings

## Statut

**INDEPENDENT AUDIT --- V4.0.20 --- NO CODE**

## Mandat

Cette passe n'ajoute aucune capability produit. Elle tente de réfuter
les `PASS` de V4.0.19 et vérifie si les contrats sont réellement assez
déterministes pour autoriser un repository skeleton sans laisser de
décisions P0 implicites aux développeurs.

## Règle d'audit

Un `PASS` n'est accepté que si le contrat permet de déterminer sans
invention locale : - la ressource et ses invariants ; - l'action exacte
; - l'autorisation et le scope exacts ; - la transaction ou le Job exact
; - le RPC/adaptateur exact ou un `N/A` non ambigu ; - la vérification
observée ; - l'audit/event ; - l'oracle d'acceptation.

Une famille générique, un wildcard, `policy based`, `when needed`,
`according to adapter`, `*`, `CRUD`, `+ actions` ou `N/A` ambigu ne
ferme pas à lui seul une décision P0.

## P1-001

Pagination/filter/sort contract exact par grande collection.

## P1-002

Rate-limit classes et backpressure API/Jobs/providers.

## P1-003

Canonical error-code registry et retryability mapping complet.

## P1-004

Observability field/metric naming, cardinality limits et redaction
contract.

## P1-005

UI degraded/unknown/ambiguous state mapping action-by-action.

## P1-006

Supportability bundle contents, privacy filtering and retention.

## Gate

Ces points doivent être fermés avant production-grade implementation de
leurs surfaces, mais ne sont pas tous des blockers du simple repository
skeleton une fois les P0 fermés.
