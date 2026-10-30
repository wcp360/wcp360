# V4.0.15 --- ADR Closure Register

## P0 --- avant repository skeleton de production

ADR-001 Supported Linux distribution/filesystem baseline\
ADR-002 Control-plane language/runtime/package layout final\
ADR-003 Control-plane database\
ADR-004 Job queue/outbox/event persistence\
ADR-005 Root daemon IPC/authentication/protocol\
ADR-006 Secret store/root-of-trust\
ADR-007 Nginx/PHP-FPM configuration ownership/generation\
ADR-008 DNS engine baseline\
ADR-009 MariaDB/MySQL lifecycle and privilege model\
ADR-010 Mail stack canonical topology\
ADR-011 Firewall implementation/nftables strategy\
ADR-012 Backup format/encryption/provider baseline\
ADR-013 Observability local baseline\
ADR-014 Release artifact/signing/distribution\
ADR-015 Installer/bootstrap delivery\
ADR-016 Filesystem ownership/UID/GID/quota/isolation model\
ADR-017 API versioning/idempotency/error contract\
ADR-018 Authentication/session/MFA/recovery architecture\
ADR-019 Audit/event retention and tamper-evidence\
ADR-020 V1 single-node vs cluster boundary.

## P1 --- avant modules concernés

PostgreSQL, Redis/Memcached, WordPress Toolkit, Git/CI, external DNS,
billing, transfer adapters, Node/Python runtimes, Apache compatibility
and provider-specific integrations.

## P2

Cluster coordination/quorum, HA DB/storage/LB topologies and
enterprise/future capabilities.

## Gate

Chaque ADR doit inclure contexte, options, décision, raisons,
conséquences, security impact, migration/rollback implications et
supersession rules.


## V4.0.16 closure

Les 20 ADR P0 ci-dessus sont fermés par `ADR-4016-001-*` à `ADR-4016-020-*`. Les P1/P2 restent ouverts intentionnellement selon le scope.
