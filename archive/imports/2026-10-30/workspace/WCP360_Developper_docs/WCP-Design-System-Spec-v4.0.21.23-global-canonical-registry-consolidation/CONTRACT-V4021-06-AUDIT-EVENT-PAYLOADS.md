# V4.0.21 --- Audit Action & Event Payload Registry

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

## Mandatory audit envelope

Every administrative/security mutation records:
`audit_id, action_id, occurred_at_utc, real_actor_id, effective_actor_id?, capability, resolved_scope, resource_type, resource_id, resource_version_before?, resource_version_after?, outcome, correlation_id, job_id?, request_origin_class, reason?`.

  -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  Audit action                Event type(s)                             Mandatory action fields                                                Redaction
  --------------------------- ----------------------------------------- ---------------------------------------------------------------------- ----------------------------------------------------
  `account.create`            `account.provisioned`                     `actor,account_id,plan_id,outcome,job_id,version`                      `no secret/path content`

  `account.state.change`      `account.suspended|account.resumed`       `actor,account_id,from,to,outcome,job_id`                              `safe state only`

  `account.delete`            `account.deleted`                         `actor,account_id,retention_decision,outcome,job_id`                   `no deleted file listing`

  `domain.mutate`             `domain.changed|domain.deleted`           `actor,domain_id,fqdn_ascii,changed_fields,outcome,job_id`             `safe field names not secrets`

  `site.mutate`               `site.changed|site.deleted`               `actor,site_id,changed_fields,generation_id,outcome,job_id`            `no env values`

  `certificate.action`        `certificate.changed`                     `actor,certificate_id,action,fingerprint,expiry,outcome,job_id`        `never private key`

  `dns.zone.mutate`           `dns.zone.changed`                        `actor,zone_id,serial,action,outcome,job_id`                           `record data allowed only per audit privacy class`

  `dns.record.mutate`         `dns.record.changed`                      `actor,record_id,zone_id,action,rrtype,outcome,job_id`                 `rdata redacted for secret-like record classes`

  `database.action`           `database.changed`                        `actor,database_id,action,outcome,job_id`                              `no dump/credential`

  `database.user.action`      `database.user.changed`                   `actor,database_user_id,action,outcome,job_id`                         `no credential`

  `mailbox.action`            `mailbox.changed`                         `actor,mailbox_id,action,outcome,job_id`                               `no password/message content`

  `backup.run`                `backup.completed|backup.failed`          `actor,run_id,scope,manifest_ref_safe,outcome,job_id`                  `no object credentials`

  `restore.run`               `restore.completed|restore.failed`        `actor,run_id,target,conflict_policy,outcome,job_id`                   `no restored content`

  `secret.rotate`             `secret.rotated|secret.rotation_failed`   `actor,secret_ref_id,purpose,old_version,new_version,outcome,job_id`   `no material`

  `security.finding.action`   `security.finding.changed`                `actor,finding_id,action,severity,outcome,job_id`                      `evidence_ref not raw sensitive evidence`

  `system.node.action`        `node.reconciled|readiness.evaluated`     `actor,node_id,action,outcome,job_id`                                  `safe findings summary`
  -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

## Event envelope

`event_id, event_type, occurred_at_utc, tenant_id?, resource_type, resource_id, resource_version, correlation_id, causation_job_id?, payload_safe`.

Audit is append-oriented and authoritative for who-did-what; events are
integration/business signals and may be delivered at-least-once.
