# V4.0.19 — Canonical Audit Action & Event Registry

## Statut
**NORMATIVE — V4.0.19 TRACEABILITY CLOSURE — NO CODE**

## Base
Cette passe ferme exclusivement le backlog P0 de `PLAN-V4018-18-CLOSURE-BACKLOG.md`. Elle n'ajoute aucune capability produit et ne crée aucun artefact exécutable.

## Règle de fermeture
Chaque cellule obligatoire est `PASS` ou `N/A` avec justification. Aucun `PARTIAL` P0 n'est accepté dans les registries V4.0.19.

## Naming
Action IDs sont stables et séparés des messages UI. Les events asynchrones utilisent le même domaine nominal avec outcome/state explicite. Secret values, auth credentials et raw message bodies sont exclus des diffs.

| Action/Event family | Trigger | Required audit | Event visibility | Retention class |
|---|---|---|---|---|
| `account.create` | corresponding canonical action | real/effective actor, capability, resource, outcome, correlation/job, safe diff | tenant when own resource + admin | `ADMIN_STANDARD` |
| `account.delete` | corresponding canonical action | real/effective actor, capability, resource, outcome, correlation/job, safe diff | tenant when own resource + admin | `ADMIN_STANDARD` |
| `account.state.change` | corresponding canonical action | real/effective actor, capability, resource, outcome, correlation/job, safe diff | tenant when own resource + admin | `ADMIN_STANDARD` |
| `audit.access` | corresponding canonical action | real/effective actor, capability, resource, outcome, correlation/job, safe diff | admin/security only | `SECURITY_LONG` |
| `backup.policy.mutate` | corresponding canonical action | real/effective actor, capability, resource, outcome, correlation/job, safe diff | tenant when own resource + admin | `ADMIN_STANDARD` |
| `backup.run` | corresponding canonical action | real/effective actor, capability, resource, outcome, correlation/job, safe diff | tenant when own resource + admin | `ADMIN_STANDARD` |
| `certificate.action` | corresponding canonical action | real/effective actor, capability, resource, outcome, correlation/job, safe diff | tenant when own resource + admin | `ADMIN_STANDARD` |
| `cron.mutate` | corresponding canonical action | real/effective actor, capability, resource, outcome, correlation/job, safe diff | tenant when own resource + admin | `ADMIN_STANDARD` |
| `database.action` | corresponding canonical action | real/effective actor, capability, resource, outcome, correlation/job, safe diff | tenant when own resource + admin | `ADMIN_STANDARD` |
| `database.user.action` | corresponding canonical action | real/effective actor, capability, resource, outcome, correlation/job, safe diff | tenant when own resource + admin | `ADMIN_STANDARD` |
| `dns.record.mutate` | corresponding canonical action | real/effective actor, capability, resource, outcome, correlation/job, safe diff | tenant when own resource + admin | `ADMIN_STANDARD` |
| `dns.zone.mutate` | corresponding canonical action | real/effective actor, capability, resource, outcome, correlation/job, safe diff | tenant when own resource + admin | `ADMIN_STANDARD` |
| `domain.mutate` | corresponding canonical action | real/effective actor, capability, resource, outcome, correlation/job, safe diff | tenant when own resource + admin | `ADMIN_STANDARD` |
| `entitlement.mutate` | corresponding canonical action | real/effective actor, capability, resource, outcome, correlation/job, safe diff | tenant when own resource + admin | `ADMIN_STANDARD` |
| `event.view` | corresponding canonical action | real/effective actor, capability, resource, outcome, correlation/job, safe diff | tenant when own resource + admin | `ADMIN_STANDARD` |
| `identity.mutate` | corresponding canonical action | real/effective actor, capability, resource, outcome, correlation/job, safe diff | admin/security only | `SECURITY_LONG` |
| `identity.view` | corresponding canonical action | real/effective actor, capability, resource, outcome, correlation/job, safe diff | admin/security only | `SECURITY_LONG` |
| `integration.mutate` | corresponding canonical action | real/effective actor, capability, resource, outcome, correlation/job, safe diff | tenant when own resource + admin | `ADMIN_STANDARD` |
| `job.action` | corresponding canonical action | real/effective actor, capability, resource, outcome, correlation/job, safe diff | tenant when own resource + admin | `ADMIN_STANDARD` |
| `mail.alias.mutate` | corresponding canonical action | real/effective actor, capability, resource, outcome, correlation/job, safe diff | tenant when own resource + admin | `ADMIN_STANDARD` |
| `mail.domain.mutate` | corresponding canonical action | real/effective actor, capability, resource, outcome, correlation/job, safe diff | tenant when own resource + admin | `ADMIN_STANDARD` |
| `mail.policy.mutate` | corresponding canonical action | real/effective actor, capability, resource, outcome, correlation/job, safe diff | tenant when own resource + admin | `ADMIN_STANDARD` |
| `mailbox.action` | corresponding canonical action | real/effective actor, capability, resource, outcome, correlation/job, safe diff | tenant when own resource + admin | `ADMIN_STANDARD` |
| `notification.route.mutate` | corresponding canonical action | real/effective actor, capability, resource, outcome, correlation/job, safe diff | tenant when own resource + admin | `ADMIN_STANDARD` |
| `plan.mutate` | corresponding canonical action | real/effective actor, capability, resource, outcome, correlation/job, safe diff | tenant when own resource + admin | `ADMIN_STANDARD` |
| `rbac.binding.mutate` | corresponding canonical action | real/effective actor, capability, resource, outcome, correlation/job, safe diff | admin/security only | `SECURITY_LONG` |
| `reseller.mutate` | corresponding canonical action | real/effective actor, capability, resource, outcome, correlation/job, safe diff | tenant when own resource + admin | `ADMIN_STANDARD` |
| `restore.run` | corresponding canonical action | real/effective actor, capability, resource, outcome, correlation/job, safe diff | tenant when own resource + admin | `ADMIN_STANDARD` |
| `runtime.mutate` | corresponding canonical action | real/effective actor, capability, resource, outcome, correlation/job, safe diff | tenant when own resource + admin | `ADMIN_STANDARD` |
| `secret.rotate` | corresponding canonical action | real/effective actor, capability, resource, outcome, correlation/job, safe diff | admin/security only | `SECURITY_LONG` |
| `security.finding.action` | corresponding canonical action | real/effective actor, capability, resource, outcome, correlation/job, safe diff | admin/security only | `SECURITY_LONG` |
| `site.mutate` | corresponding canonical action | real/effective actor, capability, resource, outcome, correlation/job, safe diff | tenant when own resource + admin | `ADMIN_STANDARD` |
| `system.node.action` | corresponding canonical action | real/effective actor, capability, resource, outcome, correlation/job, safe diff | tenant when own resource + admin | `ADMIN_STANDARD` |
| `tenant.mutate` | corresponding canonical action | real/effective actor, capability, resource, outcome, correlation/job, safe diff | tenant when own resource + admin | `ADMIN_STANDARD` |
| `tenant.view` | corresponding canonical action | real/effective actor, capability, resource, outcome, correlation/job, safe diff | tenant when own resource + admin | `ADMIN_STANDARD` |
| `token.mutate` | corresponding canonical action | real/effective actor, capability, resource, outcome, correlation/job, safe diff | admin/security only | `SECURITY_LONG` |

## Fermeture
Toutes les mutations/lectures sensibles CORE sont reliées à une famille d'audit. **AUDIT/EVENT GAP = CLOSED.**
