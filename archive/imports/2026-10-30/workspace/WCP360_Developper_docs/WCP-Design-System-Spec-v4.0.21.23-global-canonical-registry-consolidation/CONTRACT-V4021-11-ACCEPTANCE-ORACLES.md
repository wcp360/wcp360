# V4.0.21 --- Security & E2E Acceptance Oracle Registry

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

  -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  Test ID            Purpose                              Fixture                                                   Exact action family                                                                                          Positive/negative oracle                                                                   Required evidence
  ------------------ ------------------------------------ --------------------------------------------------------- ------------------------------------------------------------------------------------------------------------ ------------------------------------------------------------------------------------------ ---------------------------------------------------
  `SEC-ACCEPT-001`   `Tenant isolation`                   `Tenant A/B resources + actor A`                          `list/get/filter/search/export/event/job indirect references`                                                `no B identifiers/data; no B Job/event existence leak`                                     `audit denied attempts without B secret metadata`

  `SEC-ACCEPT-004`   `RBAC escalation`                    `tenant admin with bounded role`                          `bind higher/platform role; bind outside scope`                                                              `403; no binding/job/outbox`                                                               `rbac denial with real/effective actor`

  `SEC-ACCEPT-009`   `API token security`                 `valid/revoked/expired/scoped tokens`                     `use token across scope; replay after revoke`                                                                `only allowed scopes work; revoked/expired fail`                                           `token ID not material logged`

  `SEC-ACCEPT-010`   `Secret protection`                  `secret_ref + active material`                            `read API/jobs/logs/audit/export; rotate`                                                                    `plaintext absent except show-once response; old rejected after cutover`                   `secret version IDs only`

  `SEC-ACCEPT-014`   `Repair privilege boundary`          `repairable finding + non-repairable target`              `submit altered remediation/target`                                                                          `only allowlisted remediation RPCs/targets execute`                                        `finding/action/job correlation`

  `SEC-ACCEPT-015`   `Path/symlink/archive traversal`     `tenant roots + malicious symlink/archive`                `../, absolute path, symlink escape, zip/tar traversal`                                                      `all escapes rejected; outside file unchanged`                                             `security finding/audit where policy requires`

  `SEC-ACCEPT-016`   `Auth/MFA/recovery`                  `user with TOTP/recovery codes`                           `session fixation, recovery replay, step-up expiry`                                                          `rotation/replay rejection/timeouts enforced`                                              `auth security events`

  `SEC-ACCEPT-017`   `Supply chain integrity`             `valid/tampered/unsigned release artifacts`               `stage each artifact`                                                                                        `only valid signed compatible artifact proceeds`                                           `release verification evidence`

  `SEC-ACCEPT-018`   `Backup integrity`                   `valid/corrupt/missing chunk manifests`                   `verify/restore`                                                                                             `corruption blocks restore before target mutation`                                         `backup/restore failure evidence`

  `E2E-ACCEPT-002`   `Account provision`                  `tenant+plan+free UID/GID`                                `ACC-CREATE then poll Job`                                                                                   `uid/gid/path/quota observed and READY`                                                    `account.create audit + completion event`

  `E2E-ACCEPT-006`   `Domain lifecycle`                   `account+managed DNS mode`                                `DOM-CREATE/UPDATE/DELETE`                                                                                   `claim/bind/DNS state follows desired; delete absence verified`                            `domain audit/events`

  `E2E-ACCEPT-007`   `Site provision`                     `account+domain+runtime`                                  `SITE-CREATE`                                                                                                `Nginx/PHP syntax, socket, listener/http probe pass`                                       `site audit/job evidence`

  `E2E-ACCEPT-009`   `Certificate lifecycle`              `domain with ACME prerequisites`                          `CERT-ISSUE/RENEW/VERIFY/REVOKE`                                                                             `served fingerprint changes/validates; revoke state observed`                              `certificate audit/events`

  `E2E-ACCEPT-010`   `DNS lifecycle`                      `managed zone`                                            `zone/record create-update-delete-reconcile`                                                                 `authoritative queries match desired serial/RRsets`                                        `DNS audit/events`

  `E2E-ACCEPT-011`   `Database lifecycle`                 `account`                                                 `DB create + user + backup + restore + delete`                                                               `schema/grants/data probe/absence match each state`                                        `DB audit/jobs`

  `E2E-ACCEPT-013`   `Mailbox lifecycle`                  `mail domain`                                             `create/suspend/resume/rotate/delete`                                                                        `auth/delivery/quota and old-secret rejection verified`                                    `mailbox audit/events`

  `E2E-ACCEPT-016`   `Backup run`                         `configured S3 integration`                               `BACKUP-START`                                                                                               `manifest/chunks/checksums/remote HEAD verification pass`                                  `backup event/audit`

  `E2E-ACCEPT-021`   `Config rollback`                    `valid current generation + intentionally invalid next`   `site/runtime update`                                                                                        `invalid generation never active; previous remains healthy`                                `failed Job + rollback evidence`

  `E2E-ACCEPT-022`   `Provider outage`                    `PowerDNS/S3/ACME simulated unavailable`                  `execute external-backoff Job`                                                                               `bounded retries/backoff; no false READY`                                                  `Job attempts + degraded finding`

  `E2E-ACCEPT-023`   `Worker/wcpd crash reconciliation`   `provision Job fixture`                                   `crash before mutation; after mutation before ACK; during verify; after completion before outbox dispatch`   `no duplicate unsafe effect; observe/reconcile converges; event at-least-once dedupable`   `attempts/correlation/evidence`

  `E2E-ACCEPT-024`   `Install→readiness`                  `fresh supported server`                                  `preflight/install/bootstrap/wizard/readiness`                                                               `READY only when all blocking checks observed`                                             `bootstrap/readiness audit`
  -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

## Universal test contract

Every executable future test derived from this registry must declare
topology, actor/capabilities, preconditions, exact operation IDs,
expected Job ID/type, expected RPC sequence or explicit none,
observed-system evidence, audit/event evidence, cleanup and
release-blocking class.

Tenant/root/secret boundary failures are always release-blocking.
