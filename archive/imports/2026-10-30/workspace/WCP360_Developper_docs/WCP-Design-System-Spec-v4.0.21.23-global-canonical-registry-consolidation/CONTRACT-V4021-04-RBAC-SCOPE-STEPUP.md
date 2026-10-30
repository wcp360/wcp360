# V4.0.21 --- Deterministic RBAC, Scope & Step-up Matrix

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

  ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  Capability               Eligible actor classes                                                                Scope resolver                                        Step-up                                               Constraints
  ------------------------ ------------------------------------------------------------------------------------- ----------------------------------------------------- ----------------------------------------------------- ------------------------------------------------------------------------------------------
  `tenant.view`            `platform_admin; tenant_admin`                                                        `platform or exact tenant`                            `NO`                                                  `read only`

  `tenant.manage`          `platform_admin`                                                                      `platform`                                            `REQUIRED`                                            `tenant creation/deletion cannot be delegated`

  `identity.view`          `platform_admin; tenant_admin; reseller_admin`                                        `platform/tenant/reseller descendants`                `NO`                                                  `PII fields masked by role`

  `identity.manage`        `platform_admin; tenant_admin`                                                        `platform/exact tenant`                               `REQUIRED for disable/delete/MFA reset`               `cannot grant beyond actor ceiling`

  `rbac.manage`            `platform_admin; tenant_admin`                                                        `platform/exact tenant`                               `REQUIRED`                                            `binding target scope must be descendant/equal and role rank ≤ actor delegation ceiling`

  `account.create`         `platform_admin; tenant_admin; reseller_admin`                                        `tenant/reseller descendants`                         `NO`                                                  `plan must be assignable in same scope`

  `account.operate`        `platform_admin; tenant_admin; reseller_admin`                                        `tenant/reseller descendants`                         `REQUIRED for cross-user destructive state change`    `impersonation does not expand scope`

  `account.delete`         `platform_admin; tenant_admin; reseller_admin if policy grants`                       `tenant/reseller descendants`                         `REQUIRED`                                            `reason mandatory`

  `domain.manage`          `platform_admin; tenant_admin; reseller_admin; account_admin`                         `owned account scope`                                 `REQUIRED only for delete or ownership-mode change`   `managed FQDN uniqueness checked globally without leaking owner`

  `site.manage`            `platform_admin; tenant_admin; reseller_admin; account_admin`                         `owned account scope`                                 `REQUIRED for delete`                                 `raw unsafe config remains separately forbidden`

  `site.runtime.manage`    `same as site.manage`                                                                 `owned site`                                          `REQUIRED when env secret refs change`                `cannot expose secret material`

  `certificate.manage`     `platform_admin; tenant_admin; reseller_admin; account_admin`                         `owned domain`                                        `REQUIRED for revoke`                                 `private key never returned`

  `dns.manage`             `platform_admin; tenant_admin; reseller_admin; account_admin`                         `owned managed zone`                                  `REQUIRED for zone delete/DNSSEC key action`          `protected records require elevated rule`

  `database.manage`        `platform_admin; tenant_admin; reseller_admin; account_admin`                         `owned account/db`                                    `REQUIRED for delete/restore`                         `no DB root credential exposure`

  `database.user.manage`   `same as database.manage`                                                             `owned db`                                            `REQUIRED for rotate/delete`                          `show-once credential only on create/rotate`

  `mail.manage`            `platform_admin; tenant_admin; reseller_admin; account_admin`                         `owned account/domain`                                `REQUIRED for mail-domain delete`                     `mail data not exposed by capability`

  `mailbox.manage`         `platform_admin; tenant_admin; reseller_admin; account_admin; mailbox_self limited`   `owned domain; self only for own password/settings`   `REQUIRED for admin rotate/delete`                    `admin impersonation cannot reveal current password`

  `mail.security.manage`   `platform_admin; tenant_admin; reseller_admin within ceiling`                         `tenant/reseller/domain policy scope`                 `REQUIRED for outbound/security weakening`            `child cannot weaken enforced parent floor`

  `backup.run`             `platform_admin; tenant_admin; reseller_admin; account_admin`                         `owned scope`                                         `NO`                                                  `backup content access separate from start`

  `backup.restore`         `platform_admin; tenant_admin; reseller_admin; account_admin if granted`              `owned target`                                        `REQUIRED`                                            `conflict plan mandatory`

  `secret.manage`          `platform_admin; owning-service privileged role`                                      `exact owning resource scope`                         `REQUIRED`                                            `metadata visible; plaintext only show-once new material`

  `integration.manage`     `platform_admin; tenant_admin where tenant integration`                               `platform/exact tenant`                               `REQUIRED for credential change/delete`               `secret refs masked`

  `job.operate`            `platform_admin; tenant_admin; reseller_admin`                                        `only Jobs whose primary resource is in scope`        `REQUIRED for retry of destructive Job`               `cannot change Job payload`

  `audit.view`             `platform_admin; tenant_auditor`                                                      `platform/exact tenant`                               `REQUIRED for sensitive audit classes`                `effective actor does not broaden scope`

  `audit.export`           `platform_admin; tenant_auditor if enabled`                                           `platform/exact tenant`                               `REQUIRED`                                            `reason and bounded range mandatory`

  `security.manage`        `platform_admin; tenant_security_admin`                                               `platform/exact tenant`                               `REQUIRED for repair/suppress`                        `repair authorization rechecked at execution`

  `system.operate`         `platform_admin`                                                                      `platform/node`                                       `REQUIRED`                                            `never available to reseller/tenant roles`
  ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

## Scope algorithm

1.  Resolve real actor and active session.
2.  Resolve effective actor only if an active audited impersonation
    session exists.
3.  Compute maximum delegated scope from real actor; impersonation may
    reduce, never expand it.
4.  Resolve resource tenant/account/reseller ancestry from control-plane
    data.
5.  Require capability binding at equal/ancestor scope and within
    delegation ceiling.
6.  Apply action-specific step-up and reason rule.
7.  Only then create mutation transaction/Job.

`mailbox_self` is a restricted self-service actor class, not a general
mailbox administrator.
