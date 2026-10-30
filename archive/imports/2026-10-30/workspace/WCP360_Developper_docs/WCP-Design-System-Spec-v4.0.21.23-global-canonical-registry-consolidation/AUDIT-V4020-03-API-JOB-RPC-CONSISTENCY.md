# V4.0.20 --- API → Job → RPC Consistency Audit

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

## Incohérences concrètes

-   `domain create/update/delete` pointe vers
    `site.reconfigure + dns.zone.* when managed` : la
    création/suppression de domaine n'a pas un workflow canonique propre
    et la dépendance au site n'est pas universelle.
-   `secret_ref rotate` est `SYNC_DB or owning-resource JOB` : deux
    chemins d'exécution sans règle de sélection.
-   `audit export` est `EXPORT_JOB if large` : aucun seuil ni Job
    canonique n'est défini.
-   `job retry/cancel` est décrit comme `JOB control semantics` :
    contrôler un Job n'est pas automatiquement un nouveau Job ; la state
    transition exacte reste à fermer.
-   `node get/health/readiness/reconcile` agrège lectures et mutations
    sous `JOB readiness.evaluate|service.reconcile`, ce qui mélange HTTP
    read semantics et orchestration.
-   `notification_route CRUD/test` agrège
    `SYNC_DB + JOB notification.test` sans séparer les actions.
-   Les familles `certificate.*`, `database.*`, `database_user.*`,
    `mail_domain.*`, `mailbox.*` ne sont pas des identifiants
    d'opération finaux.

## Règle V4.0.21

Une ligne finale = une action canonique. Les agrégations ne sont
permises que dans un document de synthèse, jamais dans le registry
normatif.
