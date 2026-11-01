# Security test and release evidence matrix

This matrix complements the [toolchain catalogue](QUALITY-SECURITY-STACK.md), [executable testing guide](TESTING.md), [Security Policy](../../SECURITY.md) and canonical [Security Baseline](../specifications/security/WCP360_Security_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md). It defines behaviors to prove, not a claim that all tests exist or have passed. Canonical requirements and independent release review remain authoritative.

## Application, data and extension boundaries

| Boundary | Required positive and negative cases | Evidence and environment |
| :--- | :--- | :--- |
| Authentication and sessions | Valid login; invalid/disabled account; rate limit; session rotation/expiry/revocation; USER/ADMIN context separation; MFA/recovery when implemented. | PHPUnit functional tests, real HTTP/browser sessions and explicit credential/session lifecycle traces. |
| Tenant authorization | Own resource; unrelated tenant; removed membership/delegation; stale grants; changed resource ownership; tenant-scoped queries and downloads. | Functional tests and direct database constraints/grants against disposable PostgreSQL. |
| Browser requests | Missing/forged CSRF; wrong or missing Origin where required; output encoding; CSP; CORS; cookie properties; authorization unaffected by hidden UI controls. | WebTestCase, Panther/Playwright and scoped ZAP. |
| Durable jobs | Atomic submission and audit; idempotency conflict; concurrent claims; worker interruption; authorization/revision changes before effects; uncertain outcome inspection; bounded retries/timeouts. | Real persistence/transport tests; controlled interruption and recovery; no blind replay of privileged mutations. |
| Files and archives | Traversal; absolute paths; sibling-prefix confusion; symlink/hardlink/mount escape; concurrent replacement; archive-member escape; quotas; permission and owner changes; cross-tenant access. | Pure policy tests plus real Linux filesystem/identity tests; actual authorized data paths. |
| Network adapters | Authorized destination; SSRF; redirects; DNS rebinding; IPv4/IPv6/local/metadata targets; secret redaction and timeout/cancellation. | Isolated DNS/HTTP fixtures and controlled egress; no third-party probing. |
| Plugins | Manifest/capability validation; incompatible version; denied access; worker isolation; activation/deactivation/install/uninstall; failure/resource exhaustion; migration/rollback; Core availability. | Package-specific tests and a running Core with the plugin boundary actually exercised. |
| PostgreSQL | Tenant constraints and role grants; transaction/audit rollback; concurrency/deadlocks; forward migrations; integrity checks; empty-database restore; customer/control-plane separation. | Separate roles/databases in disposable PostgreSQL; pgTAP/pg_prove and load tools where configured. |
| Redis/other async transports | Delivery, acknowledgement, duplicate messages, retry/dead-letter handling, connection loss and restart; no secret payload logging. | Real configured transport when in use; a mock does not prove delivery semantics. |

## System agents and Linux operations

| Boundary | Required cases | Evidence and environment |
| :--- | :--- | :--- |
| IPC parser | Exact schema/types; unknown/duplicate/null/trailing fields; oversize/truncated frames; invalid encoding; unsupported operation/revision. | Unit tests and bounded native Go fuzzing; no system effects from fuzz targets. |
| Peer and operation authorization | Wrong UID/GID; substituted endpoint; unauthorized worker; wrong tenant/site/revision; response mismatch; undeclared operation or executable/argument/target. | Real Unix sockets and process identities in a disposable Linux harness. |
| Privileged effects | Fixed allowed primitive succeeds; arbitrary command/SQL/systemctl/signal/path is refused; permission and ownership boundaries remain correct. | Explicitly guarded disposable VM/container; verify observed effects and effective UID, not just an exit code. |
| Recovery and supervision | Duplicate broker; clean restart; master/worker failure; malformed/incomplete journal; changed/orphan configuration; process cleanup; no provisioning replay; full guest reboot. | Actual systemd lifecycle, changed kernel boot ID, persistent identity/code/configuration/journal/session/state checks. |
| Installer and lifecycle | Fresh/repeated run; unexpected pre-existing files; interrupted writes; failed dependency/service; upgrade compatibility; backup/restore/rollback/uninstall failures. | Disposable VM snapshots/volumes and retained before/after evidence; operator actions distinguished from automatic repair. |
| Hardening and resources | Effective capabilities; allowed UID/GID drop; syscall/MAC denials; no mount/path escape; CPU/memory/process/file limits; intended network policy. | Actual enforcing policies, systemd-analyze plus functional/adversarial tests; a score alone is insufficient. |

## Browser, accessibility and performance

| Area | Required cases | Evidence |
| :--- | :--- | :--- |
| Implemented UX | Forms and errors; search/filter/sort/pagination; bulk actions; menus; themes; Turbo navigation; stale state and double submission. | Real browser actions against implemented routes; avoid screenshots or placeholders as the only assertion. |
| Accessibility | Keyboard reachability; focus on dialogs/errors; semantic labels; contrast; screen-reader behavior. | axe-core, keyboard journeys and manual assistive-technology review. |
| Responsive behavior | Supported browsers, viewport sizes and touch/keyboard interactions. | Bounded Playwright/browser matrix with documented support. |
| Performance | Representative requests/jobs, database contention, worker recycling/state cleanup, memory and allocation growth, error budgets and resource exhaustion. | k6/pgbench/benchmarks/pprof or language profilers on controlled comparable environments. |

## Dependencies, CI and release artifacts

| Area | Required cases | Evidence |
| :--- | :--- | :--- |
| Source and dependencies | Type/static checks; reachable dependency issues; unexpected capability changes; secrets in source/history/configuration/build outputs; triaged exceptions. | Actual locks/manifests, SAST/SCA/secret tools, database versions and redacted reports. |
| CI trust | Untrusted PR input; privileged triggers; minimum token permissions; pinned actions/tools; dependency installation; shell interpolation; cache/artifact poisoning; secret access. | actionlint/zizmor and independent workflow review; scanners do not approve their own trust configuration. |
| Distribution | Artifact digest; supported target; SBOM tied to artifact; signature/provenance verification against an approved identity/issuer/key; missing/invalid signatures; tampered metadata or payload. | Verification of the actual distributed package and refusal of untrusted artifacts; signed installation/update path when implemented. |
| Release decision | Required checks actually ran; correct source/artifact; compatibility and recovery evidence; resolved findings; independent engineering/security approval. | Revision-bound review record under the existing governance gates. No missing tool or unconfigured service becomes a passing check. |

## Evidence record

For every applicable check, record:

1. Test/control identifier, requirement and owning package.
2. Source and merge SHA; artifact digest and dependency lock where applicable.
3. Platform, runtime, image, tool/configuration and vulnerability-database versions.
4. Preconditions, exact scope, synthetic fixture identities and declared exclusions.
5. Command or harness, observed positive/negative outcomes and redacted retained reports.
6. Status: passed, failed, not run, not configured or not applicable, with a reason.
7. Finding/fix/regression references and the responsible reviewer's decision.

The current candidate has existing PHP, Go, PostgreSQL, browser, runtime and guarded VM checks described in [TESTING.md](TESTING.md). This matrix extends the acceptance scope; it does not retroactively qualify unimplemented MFA, arbitrary application uploads, generic plugin packages, canonical IPC-B, signed delivery or unsupported operating systems.
