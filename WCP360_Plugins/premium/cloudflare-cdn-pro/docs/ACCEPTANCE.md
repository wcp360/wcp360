# Acceptance record: Cloudflare CDN and edge connector

Proposal: `plugin.cloudflare-cdn-pro`. State: **design / not installable**. The authoritative entry is in [CATALOG.json](../../CATALOG.json). These are future evidence requirements, not completed tests or a security certification.

## Package-specific and common gates

- [ ] Prove authenticated/private paths, cookies and uploads remain outside any unqualified cache policy.
- [ ] Scope purge operations to the owned site and distinguish provider acceptance from observed invalidation.
- [ ] Test TLS/origin conflicts, disabled integration and provider outage while existing hosting remains available.
- [ ] Keep response caching disabled for the all-traffic Apache profile unless a separately admitted profile proves protected assets, nested .htaccess, authorization revocation, cookies, private responses and tenant cache-key/purge correctness.
- [ ] Qualify origin TLS, origin exposure and bypass paths, trusted client-IP provenance and selected IPv4/IPv6/UDP/HTTP3 behavior before changing an admitted edge profile.
- [ ] Review compression negotiation, Vary/ETag, purge propagation, stale-response behavior and rollback independently; compression is not permission to make content public and no speedup or DDoS capacity is guaranteed.
- [ ] Before implementation, qualify an explicitly selected provider/interface revision, supported compatibility matrix, immutable package provenance, dependency inventory and licensing model; no release is admitted by this proposal.
- [ ] Enforce current Core actor, tenant and capability checks at each admitted request; keep credentials in scoped secret references, redact diagnostics and reject arbitrary destinations, shell commands or host/container sockets.
- [ ] Demonstrate that disablement, removal, provider outage and commercial entitlement loss preserve essential Core access, hosted workloads, local audit and recovery; retain owned data unless a separately authorized purge is requested.

## Evidence to record before qualification

| Evidence | Required record |
| :--- | :--- |
| Provider and interface | Selected service/protocol revision, admitted deployment/account scope and supported capability profile. |
| Core contract | Reviewed SDK/API/use-case revision, current actor/tenant checks and explicit capability boundaries. |
| Compatibility | Qualified Core/package revisions and OS/architecture requirements when relevant; no universal support assumption. |
| Artifact and licensing | Immutable package identity, provenance, dependency inventory, license/source-distribution review and trust decision. |
| Data and credentials | Secret references, disclosure/retention policy, scoped egress, redacted diagnostics and credential rotation/revocation. |
| Recovery | Bounded timeout/retry/queue policy, partial-failure reconciliation, disable/drain and retained-data restore evidence. |
| Verification | Synthetic fixture or isolated target, revision/digests, tools, exact checks, outcomes, unexecuted checks and limitations. |

A successful schema check establishes document shape only. Provider authentication, publisher trust, commercial entitlement, package admission and current Core operation authorization remain separate checks. No arbitrary shell hooks, browser-selected host paths, unreviewed network destinations or host/container sockets are admitted by this proposal.

Installation must begin disabled once an actual installer exists. Removal and provider failure must not block essential local Core administration, hosted workloads or recovery. Uninstall retains owned data by default; a purge requires explicit authorization and a documented recovery decision.

[Proposal README](../README.md) · [JSON metadata](../package.proposal.json) · [Catalogue contract](../../README.md) · [Editions and licensing boundaries](../../../../documentation/development/EDITIONS-AND-PREMIUM.md)
