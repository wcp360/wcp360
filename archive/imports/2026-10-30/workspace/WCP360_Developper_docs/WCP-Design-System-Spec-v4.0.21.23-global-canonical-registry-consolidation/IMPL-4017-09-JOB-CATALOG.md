# V4.0.17 — V1 Job Catalog

## Statut
**NORMATIVE — V4.0.17 — CANONICAL IMPLEMENTATION CONTRACT — NO CODE**

## Portée
Ce document traduit les décisions gelées en V4.0.16 en contrat d’implémentation non exécutable. Il ne crée ni SQL, ni OpenAPI, ni protobuf, ni unit systemd installable, ni code.

## Invariants transversaux
- IDs internes stables et non dérivés d’un nom affiché.
- `desired_state`, `observed_state` et état d’exécution restent distincts.
- Toute mutation privilégiée passe par Job puis `wcpd`, sauf lecture explicitement non privilégiée.
- RBAC, tenant scope, audit, idempotency, secrets et correlation ID s’appliquent à chaque surface.
- Suppression logique, rétention et destruction physique sont des étapes distinctes lorsque des données client existent.

## Comptes/système
`account.provision`, `account.suspend`, `account.resume`, `account.delete`, `quota.apply`, `permissions.reconcile`.

## Web
`site.provision`, `site.reconfigure`, `site.delete`, `nginx.render_apply_verify`, `php_pool.render_apply_verify`.

## DNS/TLS
`dns.zone.create/update/delete/reconcile`, `certificate.issue/renew/revoke/deploy/verify`.

## DB
`database.create/delete/backup/restore`, `database_user.create/rotate/delete`, `database.grants.reconcile`.

## Mail
`mail_domain.provision/delete`, `mailbox.create/suspend/resume/delete/password_rotate`, `mail_config.reconcile`, `dkim.rotate`, `mail_queue.action` bornée.

## Backup/Ops
`backup.run`, `restore.run`, `security.scan`, `repair.apply`, `update.stage/apply/rollback`, `service.reconcile`, `readiness.evaluate`.

## Règle
Chaque Job définit inputs typés, authorization capability, locks, retry class, timeout, cancellation semantics, verification et compensating/recovery path.
