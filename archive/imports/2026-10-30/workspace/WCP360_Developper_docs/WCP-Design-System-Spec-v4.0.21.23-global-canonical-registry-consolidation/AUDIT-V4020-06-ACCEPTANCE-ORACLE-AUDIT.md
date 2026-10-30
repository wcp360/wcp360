# V4.0.20 --- Security & E2E Acceptance Oracle Audit

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

## Finding

Les IDs V4.0.19 sont utiles et stables, mais beaucoup d'expected
evidence restent des phrases de haut niveau.

## Contrat minimal par test

-   ID stable ;
-   requirement/ADR links ;
-   fixture/topology ;
-   actor + capabilities ;
-   preconditions ;
-   exact API/service actions ;
-   expected Job/RPC sequence ou N/A ;
-   positive oracle ;
-   negative oracle ;
-   observed-system evidence ;
-   audit/event evidence ;
-   timeout/retry behavior ;
-   cleanup/recovery ;
-   release-blocking class.

## Exemples critiques

`SEC-ACCEPT-001` doit tester list/get/search/export/indirect
relationship leaks, pas seulement un GET direct.

`E2E-ACCEPT-023` doit injecter le crash avant mutation, après mutation
avant ACK, pendant verify et après commit afin de prouver
reconciliation/idempotency.
