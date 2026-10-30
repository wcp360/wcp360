# V4.0.20 --- Logical Data & Invariant Audit

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

Le logical registry est trop homogène pour représenter les vraies
différences métier.

## Exemples à fermer

-   `user`: identité globale ou tenant-local ? unicité email/login ?
    état disabled vs deleted ?
-   `role_binding`: tuple unique actor/role/scope ? délégation maximale
    ? héritage ?
-   `domain`: normalisation IDNA, unicité globale du FQDN géré,
    ownership mode, relation site/DNS/mail.
-   `dns_record`: owner/name/type/value/TTL, records multi-valués, CNAME
    conflicts.
-   `database_user`: scope engine/database, username allocation, grant
    relation.
-   `mailbox`: adresse canonique, localpart/domain relation, quota, auth
    state.
-   `backup_run`: immutabilité, policy snapshot, manifest reference,
    terminal states.
-   `job`: deduplication/idempotency relation, lease owner, attempt
    semantics.
-   `node`: single-node stable identity et non-suppression accidentelle.

## Verdict

Le document actuel est une bonne **meta-policy**, pas encore un logical
schema déterministe.
