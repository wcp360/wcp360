# V4.0.17 — V1 Resource Type Catalog

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

## CORE V1
`tenant`, `user`, `role_binding`, `hosting_account`, `reseller`, `plan`, `entitlement`, `domain`, `site`, `runtime_profile`, `certificate`, `dns_zone`, `dns_record`, `database`, `database_user`, `mail_domain`, `mailbox`, `mail_alias`, `mail_policy`, `backup_policy`, `backup_run`, `restore_run`, `cron_job`, `api_token`, `secret_ref`, `integration`, `job`, `event`, `audit_record`, `notification_route`, `security_finding`, `node`.

## Règle
Un type n’est ajouté au CORE que s’il possède lifecycle, ownership, permissions, API/service contract, Job mapping et acceptance criteria.

## P1/P2
WordPress instance, Git deployment, Redis/Memcached instance, Node/Python app, cluster placement/failover restent extensions conformes au modèle canonique.
