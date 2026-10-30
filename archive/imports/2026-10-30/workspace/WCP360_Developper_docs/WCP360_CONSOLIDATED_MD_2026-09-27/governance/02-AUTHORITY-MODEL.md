# WCP360 — Specialized Authority Model

## Governance and references
- **MASTER / Project Control:** baselines, versions, dependencies, conflicts, handoffs, readiness, cross-domain consolidation.
- **Documentation projet détaillée:** documentary and functional authority.
- **Design System:** UI/UX authority.

## Architecture and contracts
- **MODULES:** functional module catalogue and module dependencies.
- **API / IPC / CONTRACTS:** API, IPC, PHP↔Go, jobs, events, webhooks, errors, idempotency, retries, timeouts, versioning and compatibility.
- **DATABASE / DATA MODEL:** PostgreSQL schema/model, relations, constraints, indexes, transactions, locking, migrations, retention, audit data and backup/restore data model.

## Security and quality
- **SECURITY / HARDENING:** authn/authz, RBAC, scopes, entitlements, tenant isolation, privileges, daemon/socket/filesystem/secrets, Linux hardening and service security.
- **TESTS / QA:** validation strategy and global verification matrix.
- **AUDIT / ARCHITECTURE REVIEW:** independent architecture review.

## Development
- **SOURCE CODE:** implementation only after authorization; never silently redefine other domains.
- **ENGINEERING / CODE REVIEW:** independent review of produced code.
- **BUGS / FIXES:** `REPRODUCE → DIAGNOSE → ROOT CAUSE → FIX → TEST → REGRESSION TEST → VERIFY → CLOSE`.

## Infrastructure and operations
- **DEVOPS / BUILD / RELEASE:** OS support, filesystem layout, Linux identities, systemd, installer, packaging, CI/CD, releases, upgrades, rollback, recovery.
- **PERFORMANCE / SCALABILITY:** latency, throughput, concurrency, capacity, DB/cache/queues/IPC/filesystem/DNS/mail/backups.
- **OBSERVABILITY / INCIDENTS / SRE:** logs, metrics, traces, health, alerts, drift, incidents and postmortems.

## Research
- **TECHNICAL R&D / ARCHITECTURE LAB:** research/POCs only; cannot independently modify baselines.

## Collaboration rules
- Each discussion owns its domain.
- Cross-domain impact requires `WCP360 HANDOFF`.
- Contradiction requires `WCP360 CONFLICT`.
- MASTER consolidates; it does not silently replace specialized owners.
