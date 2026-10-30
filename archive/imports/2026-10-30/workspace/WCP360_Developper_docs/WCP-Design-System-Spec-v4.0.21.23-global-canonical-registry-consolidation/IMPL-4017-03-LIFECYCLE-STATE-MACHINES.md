# V4.0.17 — Canonical Lifecycle State Machines

## Statut
**NORMATIVE — V4.0.17 — CANONICAL IMPLEMENTATION CONTRACT — NO CODE**

## Portée
Ce document traduit les décisions gelées en V4.0.16 en contrat d’implémentation non exécutable. Il ne crée ni SQL, ni OpenAPI, ni protobuf, ni unit systemd installable, ni code.

## Invariants transversaux
- IDs internes stables et non dérivés d’un nom affiché.
- `desired_state`, `observed_state` et état d’exécution restent distincts.
- Toute mutation privilégiée passe par Job puis `wcpd`, sauf lecture explicitement non privilégiée.
- RBAC, tenant scope, audit, idempotency, secrets et correlation ID s’appliquent à chaque surface.
- Suppression logique, rétention et destruction physique sont des étapes distinctes lorsque des données client existent.

## Provisioned resource
`REQUESTED → VALIDATING → QUEUED → PROVISIONING → VERIFYING → READY`.
Branches : `FAILED`, `BLOCKED`, `DEGRADED`, `RECOVERY_REQUIRED`.

## Suspension
`READY → SUSPEND_REQUESTED → SUSPENDING → SUSPENDED`; reprise symétrique avec vérification.

## Deletion
`DELETE_REQUESTED → RETENTION/PENDING_PURGE → DELETING → VERIFYING_ABSENCE → DELETED`. Les ressources sans rétention peuvent omettre l’étape de délai, jamais la vérification.

## Job
`PENDING → CLAIMED → RUNNING → VERIFYING → SUCCEEDED`; branches `RETRY_WAIT`, `FAILED`, `CANCEL_REQUESTED`, `CANCELLED`, `AMBIGUOUS/RECONCILE_REQUIRED`.

## Interdit
Passer directement à READY sur simple retour exit-code sans observed verification.
