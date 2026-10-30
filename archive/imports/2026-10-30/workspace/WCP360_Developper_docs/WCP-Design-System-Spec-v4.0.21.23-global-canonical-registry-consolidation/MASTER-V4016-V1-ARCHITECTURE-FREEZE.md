# MASTER V4.0.16 --- V1 Architecture Freeze

## Statut

**ARCHITECTURE V1 FROZEN --- P0 ADR CLOSED --- NO CODE**

## Décisions structurantes

-   Debian stable comme OS de référence.
-   Go pour le daemon privilégié `wcpd`; PHP pour WCP-ADMIN/WCP-USER/API
    non-root.
-   PostgreSQL pour le control-plane et la persistance
    Jobs/outbox/events.
-   Unix domain socket local pour l'IPC privilégié.
-   Root-of-trust local root-only V1 avec envelope encryption et
    adapters KMS/HSM futurs.
-   Nginx + PHP-FPM comme web baseline.
-   PowerDNS Authoritative comme DNS local baseline.
-   MariaDB comme DB client baseline.
-   Postfix + Dovecot + Rspamd comme mail baseline.
-   nftables comme firewall baseline.
-   Manifest de backup versionné + S3-compatible comme premier remote
    adapter.
-   Observability locale native, integrations externes optionnelles.
-   Releases signées avec manifest/digests/provenance/SBOM.
-   Installer vérifié + First Boot Wizard.
-   UID/GID Linux dédié par hosting account, quotas filesystem + logique
    WCP.
-   REST `/api/v1`, Jobs asynchrones, idempotency et optimistic
    concurrency.
-   Auth native + TOTP/recovery codes V1.
-   Audit append-oriented + tamper-evidence/export.
-   Production V1 single-node ; cluster préparé mais non requis.

## Architecture de flux

Browser/API → PHP control-plane → PostgreSQL desired state/Jobs/outbox →
worker/orchestrator → Unix socket → Go `wcpd` → adapters système →
observed verification → events/audit/telemetry.

## Règle

Aucune équipe d'implémentation ne peut remplacer une décision ci-dessus
par préférence locale. Toute divergence exige un ADR de supersession
approuvé.

## Suite autorisée

La prochaine phase peut détailler les **contrats d'implémentation non
exécutables** : schémas canoniques, state machines, API resource map,
Job catalog, daemon RPC catalog, filesystem/package tree, migrations
plan et test acceptance map. Le repository/code reste interdit tant que
cette traduction n'est pas auditée.
