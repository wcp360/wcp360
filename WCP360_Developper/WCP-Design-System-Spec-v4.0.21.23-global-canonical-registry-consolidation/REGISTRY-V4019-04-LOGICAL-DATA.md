# V4.0.19 — Resource Logical Data Registry

## Statut
**NORMATIVE — V4.0.19 TRACEABILITY CLOSURE — NO CODE**

## Base
Cette passe ferme exclusivement le backlog P0 de `PLAN-V4018-18-CLOSURE-BACKLOG.md`. Elle n'ajoute aucune capability produit et ne crée aucun artefact exécutable.

## Règle de fermeture
Chaque cellule obligatoire est `PASS` ou `N/A` avec justification. Aucun `PARTIAL` P0 n'est accepté dans les registries V4.0.19.

## Invariants
Toutes les ressources mutables portent version optimiste et timestamps UTC. Les clés d'intégrité/RBAC restent structurées ; JSONB est réservé aux specs/versioned payloads. Aucun secret plaintext, fichier client, dump DB ou contenu mailbox n'est stocké dans la DB control-plane.

| Resource | Canonical key | Tenant scope | Parent / uniqueness | Delete policy | Sensitive class |
|---|---|---|---|---|---|
| `tenant` | stable opaque ID | tenant-owned or explicit admin scope | canonical UUID; contextual uniqueness by parent/tenant; relationships table where cross-resource | lifecycle delete; physical purge only after policy | CONTROL-PLANE |
| `user` | stable opaque ID | actor/global with scoped bindings | canonical UUID; contextual uniqueness by parent/tenant; relationships table where cross-resource | lifecycle delete; physical purge only after policy | CONTROL-PLANE |
| `role_binding` | stable opaque ID | actor/global with scoped bindings | canonical UUID; contextual uniqueness by parent/tenant; relationships table where cross-resource | lifecycle delete; physical purge only after policy | SECURITY |
| `hosting_account` | stable opaque ID | tenant-owned or explicit admin scope | canonical UUID; contextual uniqueness by parent/tenant; relationships table where cross-resource | lifecycle delete; physical purge only after policy | CONTROL-PLANE |
| `reseller` | stable opaque ID | tenant-owned or explicit admin scope | canonical UUID; contextual uniqueness by parent/tenant; relationships table where cross-resource | lifecycle delete; physical purge only after policy | CONTROL-PLANE |
| `plan` | stable opaque ID | tenant-owned or explicit admin scope | canonical UUID; contextual uniqueness by parent/tenant; relationships table where cross-resource | lifecycle delete; physical purge only after policy | CONTROL-PLANE |
| `entitlement` | stable opaque ID | tenant-owned or explicit admin scope | canonical UUID; contextual uniqueness by parent/tenant; relationships table where cross-resource | lifecycle delete; physical purge only after policy | CONTROL-PLANE |
| `domain` | stable opaque ID | tenant-owned or explicit admin scope | canonical UUID; contextual uniqueness by parent/tenant; relationships table where cross-resource | lifecycle delete; physical purge only after policy | CONTROL-PLANE |
| `site` | stable opaque ID | tenant-owned or explicit admin scope | canonical UUID; contextual uniqueness by parent/tenant; relationships table where cross-resource | lifecycle delete; physical purge only after policy | CONTROL-PLANE |
| `runtime_profile` | stable opaque ID | tenant-owned or explicit admin scope | canonical UUID; contextual uniqueness by parent/tenant; relationships table where cross-resource | lifecycle delete; physical purge only after policy | CONTROL-PLANE |
| `certificate` | stable opaque ID | tenant-owned or explicit admin scope | canonical UUID; contextual uniqueness by parent/tenant; relationships table where cross-resource | lifecycle delete; physical purge only after policy | CONTROL-PLANE |
| `dns_zone` | stable opaque ID | tenant-owned or explicit admin scope | canonical UUID; contextual uniqueness by parent/tenant; relationships table where cross-resource | lifecycle delete; physical purge only after policy | CONTROL-PLANE |
| `dns_record` | stable opaque ID | tenant-owned or explicit admin scope | canonical UUID; contextual uniqueness by parent/tenant; relationships table where cross-resource | lifecycle delete; physical purge only after policy | CONTROL-PLANE |
| `database` | stable opaque ID | tenant-owned or explicit admin scope | canonical UUID; contextual uniqueness by parent/tenant; relationships table where cross-resource | lifecycle delete; physical purge only after policy | CONTROL-PLANE |
| `database_user` | stable opaque ID | tenant-owned or explicit admin scope | canonical UUID; contextual uniqueness by parent/tenant; relationships table where cross-resource | lifecycle delete; physical purge only after policy | CONTROL-PLANE |
| `mail_domain` | stable opaque ID | tenant-owned or explicit admin scope | canonical UUID; contextual uniqueness by parent/tenant; relationships table where cross-resource | lifecycle delete; physical purge only after policy | CONTROL-PLANE |
| `mailbox` | stable opaque ID | tenant-owned or explicit admin scope | canonical UUID; contextual uniqueness by parent/tenant; relationships table where cross-resource | lifecycle delete; physical purge only after policy | CONTROL-PLANE |
| `mail_alias` | stable opaque ID | tenant-owned or explicit admin scope | canonical UUID; contextual uniqueness by parent/tenant; relationships table where cross-resource | lifecycle delete; physical purge only after policy | CONTROL-PLANE |
| `mail_policy` | stable opaque ID | tenant-owned or explicit admin scope | canonical UUID; contextual uniqueness by parent/tenant; relationships table where cross-resource | lifecycle delete; physical purge only after policy | CONTROL-PLANE |
| `backup_policy` | stable opaque ID | tenant-owned or explicit admin scope | canonical UUID; contextual uniqueness by parent/tenant; relationships table where cross-resource | lifecycle delete; physical purge only after policy | CONTROL-PLANE |
| `backup_run` | stable opaque ID | tenant-owned or explicit admin scope | canonical UUID; contextual uniqueness by parent/tenant; relationships table where cross-resource | lifecycle delete; physical purge only after policy | CONTROL-PLANE |
| `restore_run` | stable opaque ID | tenant-owned or explicit admin scope | canonical UUID; contextual uniqueness by parent/tenant; relationships table where cross-resource | lifecycle delete; physical purge only after policy | CONTROL-PLANE |
| `cron_job` | stable opaque ID | tenant-owned or explicit admin scope | canonical UUID; contextual uniqueness by parent/tenant; relationships table where cross-resource | lifecycle delete; physical purge only after policy | CONTROL-PLANE |
| `api_token` | stable opaque ID | tenant-owned or explicit admin scope | canonical UUID; contextual uniqueness by parent/tenant; relationships table where cross-resource | revoke/retain metadata | SECURITY |
| `secret_ref` | stable opaque ID | tenant-owned or explicit admin scope | canonical UUID; contextual uniqueness by parent/tenant; relationships table where cross-resource | revoke/retain metadata | SECRET-REF only |
| `integration` | stable opaque ID | tenant-owned or explicit admin scope | canonical UUID; contextual uniqueness by parent/tenant; relationships table where cross-resource | lifecycle delete; physical purge only after policy | CONTROL-PLANE |
| `job` | stable opaque ID | tenant-owned or explicit admin scope | canonical UUID; contextual uniqueness by parent/tenant; relationships table where cross-resource | lifecycle delete; physical purge only after policy | CONTROL-PLANE |
| `event` | stable opaque ID | tenant-owned or explicit admin scope | canonical UUID; contextual uniqueness by parent/tenant; relationships table where cross-resource | immutable/retention only | CONTROL-PLANE |
| `audit_record` | stable opaque ID | tenant-owned or explicit admin scope | canonical UUID; contextual uniqueness by parent/tenant; relationships table where cross-resource | immutable/retention only | CONTROL-PLANE |
| `notification_route` | stable opaque ID | tenant-owned or explicit admin scope | canonical UUID; contextual uniqueness by parent/tenant; relationships table where cross-resource | lifecycle delete; physical purge only after policy | CONTROL-PLANE |
| `security_finding` | stable opaque ID | tenant-owned or explicit admin scope | canonical UUID; contextual uniqueness by parent/tenant; relationships table where cross-resource | lifecycle delete; physical purge only after policy | SECURITY |
| `node` | stable opaque ID | global/admin | canonical UUID; contextual uniqueness by parent/tenant; relationships table where cross-resource | lifecycle delete; physical purge only after policy | CONTROL-PLANE |

## Fermeture
Clé, scope, relation, suppression et classe sensible sont assignés sans produire de SQL. **LOGICAL DATA GAP = CLOSED.**
