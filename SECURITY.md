# Security Policy

**WCP360 — Web Control Panel 360**

Report vulnerabilities privately to **[security@wcp360.com](mailto:security@wcp360.com)**.

WCP360 manages hosting resources and sensitive server operations. This policy explains how to report a vulnerability, how reports are handled, and which security boundaries contributors must preserve.

[Report a vulnerability](#reporting-a-vulnerability) · [Supported versions](#supported-versions) · [Security model](#security-model) · [Disclosure](#coordinated-disclosure) · [Security authority](#security-authority)

## Reporting a vulnerability

Send your report to **security@wcp360.com**, using a subject such as `[WCP360 Security] Short description`.

Keep vulnerability details out of public issues, pull requests, discussions, and commits while assessment and remediation are being coordinated. For ordinary bugs and feature requests, use the [issue tracker](https://github.com/yyogas/wcp360/issues).

A useful report includes:

| Information | Details |
| :--- | :--- |
| Affected component | Core, WCP-ADMIN, WCP-USER, daemon, extension, dependency, delivery tooling, or deployment instructions. |
| Exact revision | Repository, branch, commit SHA, package version, or artifact identity. |
| Environment | Operating system, runtime versions, installation mode, enabled integrations, and relevant redacted configuration. |
| Preconditions | Required identity, privileges, tenant relationship, network access, or configuration. |
| Reproduction | Minimal steps using synthetic data, with expected and observed behavior. |
| Impact and evidence | The affected security boundary, demonstrated consequences, relevant code locations, and redacted evidence. Separate observations from assumptions. |

Start with a concise, redacted report. If sensitive evidence is necessary, request an agreed secure transfer method before sending it. No encryption key is published by this policy.

Test only systems and accounts you own or are explicitly authorized to assess. Use the least intrusive demonstration necessary, protect other users' information, and stop before causing destructive changes, persistence, or service disruption. This policy does not authorize testing customer or third-party infrastructure.

If credentials are exposed, report their location without reproducing usable values or using them to expand access.

## Supported versions

WCP360 is under active development. The [baseline manifest](documentation/manifests/WCP360-BM-0001.md) and [project status](documentation/governance/PROJECT-STATUS.md) define the current implementation and release authority.

| Delivery class | Status |
| :--- | :--- |
| Production releases | No production release series or security maintenance window is currently declared by this repository policy. |
| `main` and development branches | Reports are assessed against the exact affected revision. Source publication does not establish production support. |
| Standalone extensions | The package's documented version, operating assumptions, and integration status apply separately. |
| Prototypes and archived material | Identify any connection to executable code, distributed artifacts, active integrations, or operational guidance. |

Reports concerning older revisions or third-party components remain useful when they identify a weakness affecting WCP360. Include the relevant integration, configuration, or distribution path.

## How reports are handled

| Stage | Maintainer workflow |
| :--- | :--- |
| Intake | Establish private follow-up and clarify the affected component and revision. |
| Assessment | Reproduce the issue where possible, assess impact and prerequisites, and involve the responsible security owner. |
| Remediation | Coordinate containment, corrective changes, regression coverage, and affected upstream or downstream parties. |
| Verification | Check the correction against an exact revision and environment; identify remaining limitations and required operator actions. |
| Disclosure | Coordinate an advisory, remediation guidance, and researcher credit where appropriate. |

Follow-up uses the private reporting channel. Response and remediation timing depend on severity, reproducibility, affected components, and coordination needs; this policy does not establish a fixed service-level commitment.

## Security model

WCP360's target architecture separates the PHP/Symfony control plane, hosted customer workloads, isolated extension processes, and Go system agents. PostgreSQL control-plane data and credentials are separate from hosted customer databases.

Browser input, resource identifiers, filenames, uploads, archive members, webhooks, repository/build input, extension assets, provider responses, and restored data must be treated as untrusted at their receiving boundary.

| Boundary | Required property |
| :--- | :--- |
| Authentication and authorization | Authenticate the caller and authorize the specific action, registered permission, role constraints, entitlement, resource, tenant, ownership/delegation, policy, authentication strength, and state. Deny by default. |
| Tenant isolation | Membership and delegation do not transfer ownership or grant access outside the effective tenant scope. Reseller actions cannot exceed the delegator's envelope. |
| WCP-USER and WCP-ADMIN | Preserve separate origins, cookies, server-side session namespaces, surface claims, runtime identities, and data access. A USER session cannot authenticate on ADMIN. |
| Sessions and browser requests | Enforce the canonical MFA, session rotation, expiry, revocation, CSRF, Origin, CORS, output encoding, and CSP requirements. Browser visibility never grants permission. |
| Jobs and approvals | Revalidate mutable authorization before effects. An approval, job ID, audit ID, or IPC-operation ID does not itself grant authority. |
| Files and data | Prevent traversal, symlink/hardlink/mount escape, unsafe archives, and check/use races. Reauthorize downloads and restores; keep the control-plane database outside customer administration tools. |
| Network access | Use scoped adapters and validated destinations; prevent SSRF, redirect escape, DNS-rebinding bypass, and unauthorized internal or metadata access. |
| System operations | Use the bounded privileged architecture below. Unknown peers, operations, targets, and malformed requests fail closed without a generic fallback. |
| Extensions | Use isolated workers, dedicated identities, owned data, declared capabilities, resource limits, and controlled network access. No direct Core-table writes or unrestricted root access. |
| Audit and recovery | Preserve required durable intent and outcomes. Observe and reconcile uncertain effects; never blindly retry destructive operations or silently discard mandatory audit. |
| Delivery and dependencies | Protect build/signing credentials, validate artifact integrity and trust, and review compatibility, capability changes, migrations, and recovery before activation. |
| Core availability | Essential hosting, local administration, recovery, audit, and baseline alerts remain independent of optional extensions and vendor service availability. |

These are security requirements, not claims that every planned component is implemented or validated. Exact identifiers, protocol parameters, and acceptance criteria belong to the canonical artifacts linked below.

### Privileged architecture invariant

```text
backend
→ wcpd (non-root)
→ bounded authenticated IPC
→ wcp-privd (minimal root)
→ explicit privileged primitive
```

Forbidden:

```text
shell.exec
arbitrary command execution
arbitrary executable
generic sudo proxy
generic systemctl proxy
arbitrary filesystem target/write
arbitrary package-manager argv
arbitrary SQL
arbitrary signal
generic root terminal
```

Any change introducing an equivalent generic privileged executor is a blocking security defect.

A ROOT panel role is not the Linux root identity. Web code, templates, extensions, and diagnostic processes must not bypass this boundary. A scoped non-root customer terminal is a separate capability.

Local IPC uses the peer authentication and access rules of its approved protocol. Encryption and remote transport guarantees must be established by their own contracts, not inferred from the existence of a socket.

### Repository secrets

Do not commit real passwords, API tokens, private keys, database credentials, session material, MFA seeds, recovery codes or customer data.

Use synthetic fixtures and secret references. Check source, configuration, screenshots, archives, CI output, generated reports, and support bundles. Removing a secret from a file does not revoke it; coordinate credential rotation and any history cleanup with the affected owner.

### Sensitive telemetry

Logs and audit data must never expose passwords, full tokens, private keys, sensitive cookies, database credentials, MFA secrets or raw sensitive IPC payloads.

Diagnostic collection and support exports require appropriate consent, scope, redaction, recipient controls, and retention. The optional Insights Connector does not grant remote administration, customer-content collection, or secret export. Metrics and diagnostic identifiers must follow the canonical telemetry policy.

## Security assessment

Report plausible, reachable failures of authentication, authorization, tenant or surface isolation, privileged execution, browser security, filesystem confinement, secret protection, backup access, artifact trust, or availability.

Explain the source-to-effect path, attacker prerequisites, affected resources, and demonstrated impact. Dependency versions and scanner labels provide context; severity depends on actual exposure and consequences. Authentication requirements, development status, or a documented control do not by themselves make a finding safe.

Distinguish an implementation vulnerability from an unimplemented feature, design gap, or missing test evidence. This policy creates no blanket finding exclusions and records no new accepted risks. Changes to severity rules or Core security exceptions require the responsible security owner's decision.

### Current implementation limits

- The full Symfony/PostgreSQL/Messenger/agent platform still requires integration and qualification. The current manifest records `CODE-BASELINE = NONE`.
- The authorized inert daemon foundation does not establish a deployed IPC listener, privileged broker, or complete hosting service.
- The standalone File Manager assumes private single-owner storage exclusively modified by the module. Its local backend is unsuitable for hostile multi-tenant homes concurrently writable through PHP, SSH, FTP, cron, or other processes.
- Browser simulations and manifest declarations do not prove production authentication, cryptographic verification, transport security, consent enforcement, or supported compatibility.
- Planned or ready tests are not executed results. Verification must identify the exact revision, environment, outcome, and limits. Automated checks and same-author verification are not independent technical review.

## Coordinated disclosure

Please coordinate disclosure timing through **security@wcp360.com** so that maintainers and affected operators can assess impact and prepare appropriate remediation.

Where appropriate, an advisory should identify affected revisions, fixed revisions or mitigations, required operator actions, and any recovery or credential-rotation steps. Researcher credit is included with the researcher's agreement.

If active exploitation is suspected, the affected operator should follow its incident response procedure, contain exposure, and preserve relevant evidence. Keep private reports, customer information, and operational secrets out of public communications.

## Security authority

Security findings belong to:

`WCP360 — SECURITY / HARDENING`

Canonical artifacts:

```text
documentation/specifications/security/WCP360_Permission_Registry_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md
documentation/specifications/security/WCP360_Security_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md
```

- [Permission Registry](documentation/specifications/security/WCP360_Permission_Registry_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md): approved permissions and namespace rules.
- [Security Baseline](documentation/specifications/security/WCP360_Security_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md): required boundaries, protocol constraints, and negative test expectations.
- [Review and merge evidence policy](documentation/governance/REVIEW-MERGE-POLICY.md): verification, independent review, and publication requirements.

This policy summarizes existing requirements and defines reporting guidance. It does not amend canonical contracts, authorize implementation or release, certify compliance, or close audit findings. Conflicting requirements must be resolved by the responsible owner.
