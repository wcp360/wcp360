# Installation, qualification and releases

[WCP360_Releases](../../WCP360_Releases/README.md) holds qualified release records. Build output and unreleased scaffolds are development inputs, not stable versions. The current repository has no qualified product release.

## Source, build and published output

```text
reviewed Core / SDK / plugin sources + dependency locks
    -> controlled WCP360_Build procedure
    -> disposable build/ candidate artifacts
    -> installation, integration, security and recovery evidence
    -> applicable owner and independent reviews
    -> immutable product tag + GitHub Release assets
    -> WCP360_Releases version record and qualified channel pointer
```

Build Core, SDK and each plugin separately. A Core release includes its approved built-in modules and mandatory runtime dependencies. Detached plugins remain independently versioned packages; a plugin build cannot silently replace a Core artifact.

## Product artifacts

A qualified release record should identify:

| Item | Purpose |
| :--- | :--- |
| Version/tag and exact source revisions | Bind Core, agents, build inputs and metadata to immutable Git identities. |
| Core application package | First-party Symfony/Twig code, locked production PHP dependencies and prepared assets; no development credentials/caches. |
| wcpd and wcp-privd packages | Executables qualified for each explicitly supported OS/architecture combination, for the non-root agent and minimal privileged broker; an inert CLI or a native package probe is not a substitute. |
| Installation package | Reviewed CLI/bootstrap source, package/layout metadata, supported preconditions and recovery behavior. |
| Integrity/trust metadata | Checksums, signature verification material, provenance and the accepted publisher/trust/revocation policy. |
| Dependency inventory | SBOM/licenses and vulnerability dispositions for the exact assembled artifact. |
| Operator documents | Requirements, installation/setup, release notes, compatibility, migration, upgrade and recovery instructions. |
| Qualification evidence | Source/merge SHA, target image/toolchains, test assertions/results, review identities and retained evidence/digests. |

Exact archive/deb layout, signature scheme, key distribution and release metadata schema remain the appropriate DevOps/Security contracts. Do not put private signing keys or production secrets in this repository.

Large binary assets belong to GitHub Releases or the approved distribution storage. The repository version directory keeps small reviewed records and links/digests; GitHub's automatic source archive is not the qualified assembled panel package.

## CLI installation

The real installer source belongs in [Core/installer](../../WCP360_Core/installer/README.md). Its target sequence is:

1. Verify an exact supported target: Debian 12/13 or Ubuntu 22.04/24.04/26.04 LTS, then its qualified architecture, PHP 8.4/PostgreSQL 17 providers, toolchain prerequisites, resources, ports, DNS/time and incompatible existing services.
2. Validate the expected artifact identity/trust and installation plan before changing the machine.
3. Create only the approved identities, directories, configuration boundaries and runtime dependencies.
4. Install prepared product artifacts; production hosts should not compile Go or resolve arbitrary frontend dependencies.
5. Initialize only approved control-plane schema/configuration and start narrowly scoped health checks.
6. Provide restricted one-time setup access through the approved local/bootstrap channel.
7. Hand off to the wizard, persist progress and disable bootstrap access after verified completion.

Repeated execution must distinguish fresh install, resume, repair and upgrade. Detect partial state, hold an installation lock, keep durable progress and provide bounded recovery. A web wizard cannot grant itself a generic privileged shell or keep an unrestricted root session.

The [platform guide](SUPPORTED-PLATFORMS.md) and shared target catalogue distinguish declared targets from executed native-package checks, the complete Debian 13 Core candidate and production qualification. Native PHP defaults differ across the five releases; a release installer must not silently install an incompatible default, add an unqualified PPA, reuse another distribution's suite or accept a derivative through `ID_LIKE`. The Debian 13 privileged candidate and reboot guards stay restricted until reviewed adapters and target VM evidence exist.

## Setup wizard

The first-party UI belongs in templates/setup; orchestration belongs to the Setup module and controlled installation interfaces.

| Step | Required behavior |
| :--- | :--- |
| Bootstrap admission | One-time scoped enrollment, restricted origin/access, expiry and rate limits; no public uninitialized administrator takeover. |
| Read-only preflight | Explain actual supported/failed requirements without claiming readiness from UI simulation. |
| ADMIN / USER topology | Distinct origins, TLS/cookie/session/runtime rules and no port-only cookie-isolation assumption. |
| Administrator enrollment | Strong local credential/MFA and safe recovery handling; no default shared password or secret logging. |
| Node and service configuration | Approved service identities, baseline engines, directories, database/transport references and validated settings. |
| Recovery and notifications | Verify local recovery/backup and baseline administrator alerts; optional vendor services cannot block Core recovery. |
| Apply and verify | Show durable operation progress, verify observed health and preserve recovery evidence. |
| Finish | Close bootstrap enrollment, rotate/revoke temporary access and expose ordinary authorized administration. |

Automated/unattended installation must satisfy the same validation and secret-handling rules. Input files use reviewed secret references and permissions rather than committed production credentials.

## Continuous development checks

| Change | Minimum relevant evidence |
| :--- | :--- |
| Core PHP | Deterministic dependency install, syntax/static/build checks, unit and domain/contract tests. |
| Twig/UX | Template validation, components, keyboard/accessibility, responsive behavior and surface/session negatives. |
| Go agents | Approved toolchain, formatting/vet/build/unit checks and relevant protocol/privilege negatives on the supported target. |
| Public SDK | Contract compatibility, serialization, failures/retries/redaction and supported consumer matrix. |
| Detached plugin | Isolated package tests plus Core lifecycle, permission, data and failure-containment integration. |
| Installer/upgrade | Disposable target install, interruption/resume, failed precondition, upgrade and restore/recovery rehearsal. |
| Platform/provider | Exact OS recognition; native package probe; qualified PHP 8.4/PostgreSQL 17 supply; complete Core, service and VM recovery checks on each supported combination. |
| Package/release | Complete content/permission inventory, dependency/secret checks, provenance, signature validation and no excluded private assets. |

Path-scoped CI can keep routine work fast, but any shared SDK/contract/Core-host change also runs its affected integration matrix. Pinned inputs and lock changes require review. No successful workflow is asserted until it actually executes against the recorded candidate/integration.

## Release acceptance

A version is eligible only when all applicable conditions are evidenced:

- Selected functional scope is complete and traced to the existing capability inventory and accepted QA baseline.
- Core's essential hosting journey works with zero optional plugins and during detached-plugin failure/lifecycle tests.
- Fresh installation and setup, upgrade from supported versions and documented recovery have executed successfully.
- Tenant/surface isolation, permissions, broker confinement, supply-chain and dependency decisions are reviewed with required negatives.
- Package inventory excludes secrets, customer state, development dependencies, caches and private vendor-side services.
- Compatibility, migration irreversibility, downtime where required and operator recovery are explicit.
- Required Engineering/DevOps/Security/QA approvals and genuine independent review apply to the exact final revision/artifacts.
- Immutable assets, verified trust metadata, dependency inventory, release notes and reproducible verification instructions are available.

A green build alone does not make a secure release. Channel promotion happens only after qualification; previous artifacts remain retrievable for the documented recovery procedure.

## Version and channel handling

Use product tags such as vX.Y.Z for qualified Core versions. SDK and plugin tags/records have explicit component identities and independent versions. Pre-release candidates remain clearly labeled candidate builds.

Create WCP360_Releases/vX.Y.Z only with its qualified record. Add a latest/stable link or signed distribution pointer only after a stable target actually exists. There is currently no latest link because neither v1.0.0 nor v1.1.0 has been qualified.

The earlier versioned shell templates are preserved under [Core installer scaffolds](../../WCP360_Core/installer/scaffolds/README.md). Their Bash bytes and executable modes are retained; they still perform no installation. They are not published product versions.
