# V4.0.21.5 — Isolation Jobs & Closed Privileged Actions

## Statut
**NORMATIVE — V1 STANDALONE — NO CODE — REPOSITORY BLOCKED**

Cette famille transforme les options d’isolation et de commandes de V4.0.21.4.1 en contrats B1/B2 explicites.

## Jobs
| Job ID | But | Actions privilégiées fermées | Oracle |
|---|---|---|---|
| `isolation.apply` | appliquer profil | `cgroup.ensure`, `cgroup.limit.write`, `unit.scope.ensure`, `quota.apply` selon capacités | readback = desired effective |
| `isolation.detect` | détecter backend | lecture contrôlée controllers/filesystem/systemd | capability snapshot signé/horodaté |
| `isolation.reconcile` | corriger drift | mêmes actions fermées qu’apply | observed = effective |
| `isolation.verify` | vérification non destructive | lectures contrôlées | PASS/DEGRADED/FAIL |
| `quota.apply` | appliquer quota | `quota.apply` | usage/limit backend readback |
| `quota.reconcile` | corriger quota drift | `quota.apply` si nécessaire | backend = effective |

## Actions privilégiées
Chaque action reçoit un DTO typé. Sont interdits : executable path libre, argv libre, shell fragment, unit name libre, path absolu libre, device libre non issu du registry.

## Options commandables
`--dry-run`, `--wait`, `--timeout`, `--reason`, `--request-id`, `--idempotency-key`, `--output` restent control-plane. Elles ne sont pas transmises telles quelles au shell/root.
