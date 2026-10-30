# V4.0.20 --- RBAC, Step-up & Privilege Boundary Audit

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

## PASS conservés

-   deny-by-default ;
-   capability-based authorization ;
-   séparation real/effective actor ;
-   `wcpd` sans shell générique ;
-   Unix socket local ;
-   target allowlists.

## Blocker

Les scopes `tenant/tree/admin according actor binding` et step-up
`policy/risk based` restent des décisions d'implémentation.

## À fermer

Pour chaque action : 1. actors éligibles ; 2. scope resolver ; 3.
delegation ceiling ; 4. reseller descendant rule ; 5. impersonation
behavior ; 6. step-up REQUIRED / CONDITIONAL / NO ; 7. reason
requirement ; 8. sensitive-field masking ; 9. audit action.

## Important

Le frontend ne décide jamais du scope. Le service métier résout et
impose le scope avant création de Job.
