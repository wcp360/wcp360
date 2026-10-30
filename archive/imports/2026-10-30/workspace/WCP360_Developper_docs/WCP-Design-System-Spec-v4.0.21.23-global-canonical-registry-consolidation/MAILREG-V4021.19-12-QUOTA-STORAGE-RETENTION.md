# V4.0.21.19 --- Mail Quota / Storage / Retention

Mailbox quota derives from platform/reseller/plan/account policy and
cannot exceed ceilings. Logical quota and physical filesystem quota are
reconciled where supported.

OVERQUOTA is explicit observed state. Policy defines inbound behavior
near/full quota without silent message deletion.

Delete/retention/backup/archive are separate concerns. Legal/archive
policy, when enabled, can block immediate purge and is visible to
authorized admins.
