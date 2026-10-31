# V4.0.17 — V1 End-to-End Acceptance Matrix

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

## Provisioning
Create tenant/account/plan → Linux identity/quota → site → PHP → domain/DNS → TLS → DB → mail → backup → health, avec vérification réelle.

## Lifecycle
Suspend/resume account ; rename display data sans casser IDs ; plan/quota change ; certificate renewal ; password rotation ; backup/restore ; delete avec retention/purge.

## Failure
Nginx invalid generation rollback ; DNS unavailable ; MariaDB provisioning partial ; mail queue degradation ; S3 outage ; disk/inode pressure ; worker crash ; wcpd restart ; timeout ambigu puis reconciliation.

## Security
Cross-tenant attempts, privilege escalation, expired MFA/recovery, impersonation audit, malicious archive/path, webhook replay.

## Operations
Install → first run → readiness → update → repair → rescue → control-plane restore → decommission.

## Gate
Chaque parcours possède expected resource states, Jobs, audit, user-visible outcome et recovery path.
