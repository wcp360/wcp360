# V4.0.19 — Resource Lifecycle Assignment Matrix

## Statut
**NORMATIVE — V4.0.19 TRACEABILITY CLOSURE — NO CODE**

## Base
Cette passe ferme exclusivement le backlog P0 de `PLAN-V4018-18-CLOSURE-BACKLOG.md`. Elle n'ajoute aucune capability produit et ne crée aucun artefact exécutable.

## Règle de fermeture
Chaque cellule obligatoire est `PASS` ou `N/A` avec justification. Aucun `PARTIAL` P0 n'est accepté dans les registries V4.0.19.

| Resource | Assigned machine | Rule |
|---|---|---|
| `tenant` | `CONTROL` | control-plane lifecycle: ACTIVE/DISABLED/DELETING where applicable; no fake provisioning states |
| `user` | `CONTROL` | control-plane lifecycle: ACTIVE/DISABLED/DELETING where applicable; no fake provisioning states |
| `role_binding` | `CONTROL` | control-plane lifecycle: ACTIVE/DISABLED/DELETING where applicable; no fake provisioning states |
| `hosting_account` | `DELETABLE` | provisioned lifecycle with explicit retention/purge/delete verification |
| `reseller` | `CONTROL` | control-plane lifecycle: ACTIVE/DISABLED/DELETING where applicable; no fake provisioning states |
| `plan` | `CONTROL` | control-plane lifecycle: ACTIVE/DISABLED/DELETING where applicable; no fake provisioning states |
| `entitlement` | `CONTROL` | control-plane lifecycle: ACTIVE/DISABLED/DELETING where applicable; no fake provisioning states |
| `domain` | `PROVISIONED` | canonical provisioned lifecycle REQUESTED→...→READY + delete |
| `site` | `PROVISIONED` | canonical provisioned lifecycle REQUESTED→...→READY + delete |
| `runtime_profile` | `PROVISIONED` | canonical provisioned lifecycle REQUESTED→...→READY + delete |
| `certificate` | `PROVISIONED` | canonical provisioned lifecycle REQUESTED→...→READY + delete |
| `dns_zone` | `PROVISIONED` | canonical provisioned lifecycle REQUESTED→...→READY + delete |
| `dns_record` | `CHILD` | inherits parent availability; own VALIDATING/READY/FAILED only when external apply required |
| `database` | `PROVISIONED` | canonical provisioned lifecycle REQUESTED→...→READY + delete |
| `database_user` | `CHILD` | inherits parent availability; own VALIDATING/READY/FAILED only when external apply required |
| `mail_domain` | `PROVISIONED` | canonical provisioned lifecycle REQUESTED→...→READY + delete |
| `mailbox` | `SUSPENDABLE` | provisioned + suspension/resume state machine |
| `mail_alias` | `CHILD` | inherits parent availability; own VALIDATING/READY/FAILED only when external apply required |
| `mail_policy` | `CONTROL` | control-plane lifecycle: ACTIVE/DISABLED/DELETING where applicable; no fake provisioning states |
| `backup_policy` | `CONTROL` | control-plane lifecycle: ACTIVE/DISABLED/DELETING where applicable; no fake provisioning states |
| `backup_run` | `EXECUTION` | Job/run lifecycle PENDING→...→SUCCEEDED/FAILED |
| `restore_run` | `EXECUTION` | Job/run lifecycle PENDING→...→SUCCEEDED/FAILED |
| `cron_job` | `PROVISIONED` | canonical provisioned lifecycle REQUESTED→...→READY + delete |
| `api_token` | `CONTROL` | control-plane lifecycle: ACTIVE/DISABLED/DELETING where applicable; no fake provisioning states |
| `secret_ref` | `INTERNAL` | internal metadata lifecycle ACTIVE/ROTATING/REVOKED |
| `integration` | `CONTROL` | control-plane lifecycle: ACTIVE/DISABLED/DELETING where applicable; no fake provisioning states |
| `job` | `EXECUTION` | Job/run lifecycle PENDING→...→SUCCEEDED/FAILED |
| `event` | `IMMUTABLE` | append/read lifecycle; no update/delete except retention policy |
| `audit_record` | `IMMUTABLE` | append/read lifecycle; no update/delete except retention policy |
| `notification_route` | `CONTROL` | control-plane lifecycle: ACTIVE/DISABLED/DELETING where applicable; no fake provisioning states |
| `security_finding` | `FINDING` | OPEN/ACKNOWLEDGED/REMEDIATING/RESOLVED/SUPPRESSED(policy) |
| `node` | `SINGLE_NODE` | REGISTERED/HEALTHY/DEGRADED/RECOVERY_REQUIRED; no cluster placement lifecycle |

## Fermeture
Chaque type CORE possède une state-machine ou une classe explicitement limitée. **LIFECYCLE ASSIGNMENT GAP = CLOSED.**
