# WCP360 internal and public distribution architecture

Date: 2026-10-29

## Decision

WCP360 is developed as two security products joined by signed, versioned contracts:

1. **WCP360 STABLE PUBLIC** is the autonomous hosting product installed on customer servers. It contains WCP-USER, the technical WCP-ADMIN, API, node daemon, installer/updater and the public modules permitted by an edition.
2. **WCP360 INTERNAL** is the private vendor control plane. It contains the license authority, application and extension publishers, build factory, release manager, download/update origins, customer commerce, consented fleet registry and commercial analytics.

INTERNAL is not a higher WCP-ADMIN role, a customer-visible profile or universal Linux root. It has a separate identity provider, permission model, network, audit trail, deployment and repository boundary.

The prototype reference for the private product now lives at '../WCP360 Internal/index.html', with its protected console at '../WCP360 Internal/internal-distribution.html'. It is physically outside the public prototype, absent from USER, RESELLER and ROOT sidebars, and protected by a dedicated workforce login. 'software-manager.html' remains the customer ROOT owner for local packages, public extensions and signed updates.

## Repository boundary

The production target uses separate repositories and separate CI credentials:

~~~text
wcp360-public/
  core/
  daemon/
  modules/
  extensions-public/
  installer/
  packaging-contracts/
  specifications/
  tests/

wcp360-internal/
  licensing-authority/
  catalog-publisher/
  extension-publisher/
  build-control/
  release-control/
  download-update-origin/
  fleet-registry/
  customer-commerce/
  commercial-analytics/

wcp360-insights-connector/
  manifest.json
  diagnostics-worker/
  consent-policy/
  report-schema/
  root-console/
~~~

Private source must not be stored in a public monorepo and merely omitted from a ZIP. Physical separation prevents private history, developer metadata, internal endpoints and CI configuration from becoming public.

Secrets never enter either repository. Signing keys are non-exportable HSM objects; CI obtains short-lived workload identities.

## Component families

| Family | Examples | Distribution rule |
|---|---|---|
| Public core | WCP-USER, WCP-ADMIN, API, daemon, IAM, jobs, audit, installer/updater | Always present in every supported public edition |
| Licensed public modules | DNS, mail, database, backup, monitoring | Selected by an edition manifest; no private server code |
| Public extensions and adapters | App Installer, provider connectors, optional workflows | Signed, compatible, isolated and removable |
| Detached support extensions | WCP360 Insights Connector | Separate signed artifact, disabled by default, explicit consent and no core dependency |
| Public service clients | License verifier, catalog client, update client | Minimal client for a private service; cached and failure-safe |
| Private internal services | License authority, catalog publisher, build/release control, fleet and commerce | Never eligible for a public manifest |

The private publisher and public client are different components:

- the **license authority** owns customers, subscriptions, activations, renewals, suspensions and revocations; the **license verifier** validates a signed minimum entitlement and implements grace continuity;
- the **marketplace publisher** ingests, scans, tests and signs applications; the **catalog client** consumes signed entries allowed by customer ROOT policy;
- the **release control plane** selects and signs packages; the **public updater** verifies manifests, performs preflight, updates transactionally and recovers locally.

## Positive public allowlist

A release is built only from an immutable positive manifest. The builder rejects unknown components. It never copies a repository tree and never relies on an exclusion list.

~~~yaml
schema: wcp360.release/v1
version: 2.5.9
edition: business
source_commit: 8a71f0c
include:
  - core@8a71f0c
  - daemon@2.5.9
  - installer@2.5.9
  - modules/dns@4.3.1
deny_unknown_components: true
private_repository_access: false
~~~

The build worker receives source archives for the listed public component IDs only. It has no credentials or network route to private source control.

## Stable publication gates

'select -> test -> build -> audit -> sign -> publish'

| Gate | Blocking evidence |
|---|---|
| Select | Approved component IDs, versions, edition and exact commits |
| Test | Unit, integration, migration, uninstall, upgrade and recovery suites |
| Build | Locked dependencies, signed ephemeral runner and reproducible secondary hash |
| Audit | Secret scan, private-identifier scan, malware scan, SBOM, license policy and vulnerability decision |
| Sign | HSM package signature, signed checksum, provenance attestation and dual approval |
| Publish | Immutable artifact path, atomic manifest, staged CDN rollout and tested rollback target |

Stable promotion references an existing verified artifact and does not rebuild it. Alpha remains private. Beta and RC require customer opt-in. Stable is the only default public channel.

## Public package contract

Every downloadable version publishes:

- a versioned archive per edition;
- SHA-256 checksums and detached signatures;
- a CycloneDX SBOM;
- build provenance containing commit, dependency locks, runner digest and builder identity;
- release notes, compatibility range and migration requirements;
- an update manifest with package hash, preflight, health gates and rollback target.

The updater stages the package, verifies it, checks disk/database/service/extension compatibility, switches atomically, runs migrations, validates health and restores the last known good version when a gate fails.

## Licensing continuity

Licensing is a commercial control, never a destructive hosting dependency:

- an authority outage uses a bounded signed grace period;
- an expired paid entitlement stops new premium actions only after policy expiry;
- existing websites, mailboxes, databases, login, export, backup and recovery keep operating;
- no license event deletes data or terminates accounts;
- the public client sends no hosted domains, files, mail, database content or credentials.

## Fleet privacy and support

Fleet registration is opt-in and purpose-limited. Minimum records are installation ID, edition, version, public module compatibility and update outcome. Operational diagnostics require separate consent.

Fleet Management is not hidden remote Linux access. Support actions require an explicit customer-approved session, a scoped purpose, time limit, least privilege and immutable audit.

## Detached Insights Connector

WCP360 INTERNAL itself is never a plugin. The optional 'com.wcp360.insights-connector' is a separate public extension that implements a narrow, auditable client contract:

- customer ROOT installs, enables, configures and removes it locally;
- it is excluded from the public core ZIP and released as its own signed artifact;
- collection is deny-by-default and split into inventory, health, extension failure, security metadata and reviewed bug-context scopes;
- it sends structured evidence through a pinned outbound mTLS connection only;
- it has no inbound listener, shell, node-agent command, impersonation, secret export or remote remediation capability;
- disabling or uninstalling revokes pairing and stops collection and transmission while WCP360 core remains operational.

The private Fleet view may receive sanitized audit reports, normalized findings and customer-submitted bug reports. Hosted files, messages, database rows, credentials and raw customer content are forbidden. The customer can export local reports even when INTERNAL is unavailable.

### Independent release lane

The connector never shares an output job or update manifest with a WCP360 edition:

- the core lane resolves only allowlisted public-core components and produces Community, Business or Enterprise archives;
- the detached lane mounts only the extension source and produces its own package, manifest, CycloneDX SBOM and provenance;
- the extension index declares compatible core versions and is published independently from the Stable core manifest;
- revocation is scoped to one artifact identity, so revoking an extension does not revoke a core archive and revoking core does not silently remove an extension;
- the public allowlist export applies hard exclusions for every detached component and every INTERNAL service, even if stored state is altered.

## Extension publisher boundary

Marketplace applications and WCP360 extensions are not the same artifact:

- an application is installed inside an owned website account and follows the application catalog contract;
- an extension adds routes, jobs, events or provider adapters to WCP360 and follows the extension-platform contract.

The private extension publisher owns publisher verification, package ingestion, permission diffs, compatibility evidence, conformance tests and signed catalog publication. Customer WCP-ADMIN owns local publisher policy, exact permission approval, staged activation, health and rollback. It cannot modify or republish an upstream package.

An extension review must exercise install, upgrade, timeout, crash, circuit breaker, disable, rollback and uninstall while proving that authentication and the hosting core remain operational.

## Customer and commerce boundary

The private Customers center owns legal organizations, billing references, contacts, edition, subscription and renewal state. It does not own:

- activation keys or entitlement history, which belong to Licensing;
- registered installations or diagnostics, which belong to Fleet;
- hosting accounts, domains, files, mailboxes, databases or infrastructure, which remain on customer WCP-ADMIN.

A commercial suspension produces a signed entitlement status after a reviewed grace policy. It may stop new paid-feature expansion, but it cannot terminate accounts, remove data, shut down a customer server or block login, export, backup and recovery. Destructive customer-side operations always require a separate authorized workflow.

## INTERNAL workforce identity and JIT access

The private plane uses a workforce identity provider that is independent from customer identities and Linux accounts. Its dedicated login accepts only vendor identities issued by 'internal.example.com'; public ROOT, RESELLER and USER credentials are rejected. Human operators use phishing-resistant FIDO2 authentication; automation uses short-lived mTLS workload identities.

Standing access is limited to low-risk read operations. A privileged action requires:

1. exact capability and resource scope;
2. business purpose plus incident or change ticket;
3. maximum duration or one-operation lifetime;
4. an approver who is different from the requester;
5. revalidation of current policy and target state at execution;
6. automatic expiry and immutable session evidence.

Approval is not execution. An approved request creates a temporary capability token. The target service still verifies release identity, customer consent, service health and all safety gates before accepting the command. Session records correlate UI actions, API calls, jobs and resulting audit events.

## Emergency response

A compromised release follows one audited sequence:

1. freeze the affected release channel;
2. publish a signed artifact-hash revocation;
3. rotate the signing trust root when required;
4. publish a fixed release and customer advisory;
5. offer a verified rollback or safe update;
6. preserve source, build and operator evidence in immutable storage;
7. complete and publish the appropriate root-cause review.

Revocation prevents new installation or update to the affected hash. It does not remotely disable healthy customer workloads.

## Development rule

The public product must build, start, authenticate, manage hosting and recover locally when every private service is unavailable. Contract tests run public clients against unavailable, slow, invalid-signature, expired and incompatible private-service responses. A private outage can degrade discovery or commercial actions, but cannot block the hosting core.
