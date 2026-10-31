# V4.0.19 — Canonical Capability Registry

## Statut
**NORMATIVE — V4.0.19 TRACEABILITY CLOSURE — NO CODE**

## Base
Cette passe ferme exclusivement le backlog P0 de `PLAN-V4018-18-CLOSURE-BACKLOG.md`. Elle n'ajoute aucune capability produit et ne crée aucun artefact exécutable.

## Règle de fermeture
Chaque cellule obligatoire est `PASS` ou `N/A` avec justification. Aucun `PARTIAL` P0 n'est accepté dans les registries V4.0.19.

## Deny model
Absence de capability ou scope mismatch = DENY. Un rôle est un bundle, jamais un bypass. Les capabilities système/security/audit ne sont pas héritées automatiquement par impersonation.

| Capability | Resources | Scope | Step-up | Default bundles |
|---|---|---|---|---|
| `account.create` | hosting_account | tenant/tree/admin according actor binding | policy/risk based | WCP admin + tenant/reseller role when resource ownership permits |
| `account.delete` | hosting_account | tenant/tree/admin according actor binding | policy/risk based | WCP admin + tenant/reseller role when resource ownership permits |
| `account.operate` | hosting_account | tenant/tree/admin according actor binding | policy/risk based | WCP admin + tenant/reseller role when resource ownership permits |
| `audit.view|audit.export` | audit_record | tenant/tree/admin according actor binding | policy/risk based | WCP admin; specialized operator where allowed |
| `backup.policy.manage` | backup_policy | tenant/tree/admin according actor binding | policy/risk based | WCP admin + tenant/reseller role when resource ownership permits |
| `backup.restore` | restore_run | tenant/tree/admin according actor binding | YES | WCP admin + tenant/reseller role when resource ownership permits |
| `backup.run` | backup_run | tenant/tree/admin according actor binding | policy/risk based | WCP admin + tenant/reseller role when resource ownership permits |
| `certificate.manage` | certificate | tenant/tree/admin according actor binding | policy/risk based | WCP admin + tenant/reseller role when resource ownership permits |
| `cron.manage` | cron_job | tenant/tree/admin according actor binding | policy/risk based | WCP admin + tenant/reseller role when resource ownership permits |
| `database.manage` | database | tenant/tree/admin according actor binding | policy/risk based | WCP admin + tenant/reseller role when resource ownership permits |
| `database.user.manage` | database_user | tenant/tree/admin according actor binding | policy/risk based | WCP admin + tenant/reseller role when resource ownership permits |
| `dns.manage` | dns_record, dns_zone | tenant/tree/admin according actor binding | policy/risk based | WCP admin + tenant/reseller role when resource ownership permits |
| `domain.manage` | domain | tenant/tree/admin according actor binding | policy/risk based | WCP admin + tenant/reseller role when resource ownership permits |
| `event.view` | event | tenant/tree/admin according actor binding | policy/risk based | WCP admin + tenant/reseller role when resource ownership permits |
| `identity.manage` | user | tenant/tree/admin according actor binding | policy/risk based | WCP admin; specialized operator where allowed |
| `identity.view` | user | tenant/tree/admin according actor binding | policy/risk based | WCP admin; specialized operator where allowed |
| `integration.manage` | integration | tenant/tree/admin according actor binding | policy/risk based | WCP admin + tenant/reseller role when resource ownership permits |
| `job.operate` | job | tenant/tree/admin according actor binding | policy/risk based | WCP admin + tenant/reseller role when resource ownership permits |
| `mail.manage` | mail_alias, mail_domain | tenant/tree/admin according actor binding | policy/risk based | WCP admin + tenant/reseller role when resource ownership permits |
| `mail.security.manage` | mail_policy | tenant/tree/admin according actor binding | policy/risk based | WCP admin + tenant/reseller role when resource ownership permits |
| `mailbox.manage` | mailbox | tenant/tree/admin according actor binding | policy/risk based | WCP admin + tenant/reseller role when resource ownership permits |
| `notification.manage` | notification_route | tenant/tree/admin according actor binding | policy/risk based | WCP admin + tenant/reseller role when resource ownership permits |
| `plan.manage` | entitlement, plan | tenant/tree/admin according actor binding | policy/risk based | WCP admin / reseller admin within scope |
| `rbac.manage` | role_binding | tenant/tree/admin according actor binding | YES | WCP admin; specialized operator where allowed |
| `reseller.manage` | reseller | tenant/tree/admin according actor binding | policy/risk based | WCP admin / reseller admin within scope |
| `secret.manage` | secret_ref | tenant/tree/admin according actor binding | YES | WCP admin + tenant/reseller role when resource ownership permits |
| `security.manage` | security_finding | tenant/tree/admin according actor binding | YES | WCP admin; specialized operator where allowed |
| `site.manage` | site | tenant/tree/admin according actor binding | policy/risk based | WCP admin + tenant/reseller role when resource ownership permits |
| `site.runtime.manage` | runtime_profile | tenant/tree/admin according actor binding | policy/risk based | WCP admin + tenant/reseller role when resource ownership permits |
| `system.view|system.operate` | node | tenant/tree/admin according actor binding | policy/risk based | WCP admin; specialized operator where allowed |
| `tenant.manage` | tenant | tenant/tree/admin according actor binding | policy/risk based | WCP admin + tenant/reseller role when resource ownership permits |
| `tenant.view` | tenant | tenant/tree/admin according actor binding | policy/risk based | WCP admin + tenant/reseller role when resource ownership permits |
| `token.manage` | api_token | tenant/tree/admin according actor binding | policy/risk based | WCP admin + tenant/reseller role when resource ownership permits |

## Fermeture
Chaque action CORE de la registry API référence une capability canonique. **CAPABILITY GAP = CLOSED.**
