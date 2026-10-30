# V4.0.21.5 — Exact Isolation / Quota API Operations

## Statut
**NORMATIVE — V1 STANDALONE — NO CODE — REPOSITORY BLOCKED**

Cette famille transforme les options d’isolation et de commandes de V4.0.21.4.1 en contrats B1/B2 explicites.

| Operation ID | Method | Path | Capability | Scope | Step-up | Execution | Audit |
|---|---|---|---|---|---|---|---|
| `ISOLATION-PROFILE-LIST` | `GET` | `/api/v1/isolation-profiles` | `isolation.read` | `scope:isolation` | `NO` | `NO_JOB` | `isolation.profile.read` |
| `ISOLATION-PROFILE-CREATE` | `POST` | `/api/v1/isolation-profiles` | `isolation.manage` | `scope:isolation` | `YES` | `NO_JOB` | `isolation.profile.created` |
| `ISOLATION-PROFILE-UPDATE` | `PATCH` | `/api/v1/isolation-profiles/{profile_id}` | `isolation.manage` | `scope:profile` | `YES` | `NO_JOB` | `isolation.profile.changed` |
| `ISOLATION-PROFILE-ARCHIVE` | `POST` | `/api/v1/isolation-profiles/{profile_id}/archive` | `isolation.manage` | `scope:profile` | `YES` | `NO_JOB` | `isolation.profile.archived` |
| `ISOLATION-PROFILE-ASSIGN` | `PUT` | `/api/v1/accounts/{account_id}/isolation-profile` | `isolation.assign` | `scope:account` | `YES` | `JOB:isolation.apply` | `isolation.profile.assigned` |
| `ISOLATION-EFFECTIVE-READ` | `GET` | `/api/v1/accounts/{account_id}/isolation/effective` | `isolation.read` | `scope:account` | `NO` | `NO_JOB` | `isolation.effective.read` |
| `ISOLATION-CAPABILITY-DETECT` | `POST` | `/api/v1/nodes/{node_id}/isolation/detect` | `isolation.detect` | `scope:node` | `YES` | `JOB:isolation.detect` | `isolation.capability.detected` |
| `ISOLATION-RECONCILE` | `POST` | `/api/v1/accounts/{account_id}/isolation/reconcile` | `isolation.reconcile` | `scope:account` | `YES` | `JOB:isolation.reconcile` | `isolation.reconciled` |
| `ISOLATION-VERIFY` | `POST` | `/api/v1/accounts/{account_id}/isolation/verify` | `isolation.verify` | `scope:account` | `NO` | `JOB:isolation.verify` | `isolation.verified` |
| `ISOLATION-EVENTS-READ` | `GET` | `/api/v1/accounts/{account_id}/isolation/events` | `isolation.read` | `scope:account` | `NO` | `NO_JOB` | `isolation.events.read` |
| `QUOTA-USAGE-READ` | `GET` | `/api/v1/accounts/{account_id}/quota/usage` | `quota.read` | `scope:account` | `NO` | `NO_JOB` | `quota.usage.read` |
| `QUOTA-SET` | `PUT` | `/api/v1/accounts/{account_id}/quota` | `quota.manage` | `scope:account` | `YES` | `JOB:quota.apply` | `quota.changed` |
| `QUOTA-RECONCILE` | `POST` | `/api/v1/accounts/{account_id}/quota/reconcile` | `quota.reconcile` | `scope:account` | `YES` | `JOB:quota.reconcile` | `quota.reconciled` |

Toutes les mutations existantes utilisent `If-Match`/version et les submissions de Job acceptent `Idempotency-Key`. `dry-run` produit un diff/plan sans mutation privilégiée.
