# V4.0.21 --- Cross-resource Orchestration DAGs & Failure Semantics

## Statut

**NORMATIVE --- V4.0.21 DETERMINISTIC CONTRACT CLOSURE --- NO CODE**

## Mandat

Cette passe ferme exclusivement les 12 blockers P0 de
`AUDIT-V4020-02-P0-BLOCKERS.md`. Aucune capability produit n'est
ajoutée. Les identifiants ci-dessous sont des contrats de conception non
exécutables.

## Règle

Aucun `CRUD`, wildcard `*`, `when needed`, `policy based`,
`according to adapter`, `if large` ou `N/A` non justifié n'est autorisé
dans un registry normatif final.

## Account provision DAG

`validate plan → allocate system_name/uid/gid → Linux identity → data root → quota → base permissions → READY`.
No child site/mail/db is implicitly created. Failure before Linux
identity leaves no external state. Failure after identity invokes
observe/reconcile; compensation removes only resources created by the
current Job when safe.

## Domain provision DAG

`normalize/claim FQDN → persist domain desired state → bind optional site → create/reconcile managed DNS if mode=MANAGED_LOCAL → request certificate only if tls_policy requires and prerequisites observed → optional mail-domain link → verify`.
DNS/TLS/mail are independent child capabilities; failure of optional TLS
may yield `DEGRADED` while ownership/domain binding remains valid. FQDN
ownership conflict is `BLOCKED`, never retried blindly.

## Site provision DAG

`validate account/runtime/path → ensure directory → render PHP pool → render Nginx generation → syntax validate → activate PHP/Nginx → reload → listener/socket probe → HTTP probe → READY`.
If activation fails, reactivate previous valid generation. Data
directory is not deleted as rollback unless it was newly created and
empty.

## Plan change DAG

`validate new plan → compute effective entitlement diff → reject below current non-reducible usage unless explicit enforcement policy → update desired plan/version → quota/resource reconciliation Jobs → observe effective limits`.
A plan DB update is not `READY` until required quota reconciliation
completes.

## Mailbox create DAG

`validate mail-domain/address/quota → create secret pending → create mailbox storage/ownership → publish auth/routing config → reload/reconcile → auth probe + delivery-target probe → activate secret → READY`.
Failure after secret creation but before service cutover revokes pending
secret.

## Restore DAG

`validate manifest → build conflict plan → lock target → restore filesystem dataset → restore DB dataset → restore mail dataset → regenerate managed configs from desired model, not blindly from backup → service reconcile → dataset probes → readiness → unlock`.
Partial restore failure yields `RECOVERY_REQUIRED`; automatic
destructive rollback is forbidden unless dataset-specific contract
proves it safe.

## Account delete DAG

`freeze mutations → dependency inventory → require delete/retain decisions → suspend account → detach routing/public exposure → delete selected child managed resources in dependency order → retain/purge datasets per plan → delete Linux identity last → verify absence/retention catalog → DELETED`.
