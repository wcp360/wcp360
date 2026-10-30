# V4.0.20 --- Cross-resource & Partial Failure Audit

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

## Graphes P0

### Account

account → Linux identity → data root → quota → site/mail/db children →
suspension/delete.

### Domain

domain → ownership mode → site binding → DNS mode → certificate → mail
domain optional.

### Plan

plan/entitlements → effective limits → account quota/resource
reconciliation.

### Mailbox

mail domain → mailbox → credential → quota → routing/config → protocol
verification.

### Restore

backup manifest → conflict plan → filesystem/DB/mail/config datasets →
service reconciliation → final verification.

## Failure semantics à fermer

Pour chaque edge : ordering, lock, rollback/compensation, retry class,
ownership, delete cascade/restrict/orphan, and what becomes `DEGRADED`
versus `FAILED`.

## Verdict

Les composants existent, mais les transactions distribuées/orchestrées
entre eux ne sont pas encore toutes déterministes.
