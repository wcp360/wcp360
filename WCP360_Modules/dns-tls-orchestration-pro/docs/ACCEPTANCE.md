# DNS & TLS Orchestration Pro acceptance preparation

**Status: design.** These are required future checks, not executed results or production acceptance. The authoritative requirements are the acceptance list in [package.proposal.json](../package.proposal.json), materialized from [CATALOG.json](../../CATALOG.json).

## Package-specific checks

1. Resolve portfolio domains, zones, delegations, certificates, nodes and provider identities to authoritative Core owners and current revisions; review imported differences without taking over unrelated records or copying the DNS registry.
2. Qualify each provider/API/version and authoritative engine capability separately, including PowerDNS through Core-owned adapters. Keep provider transports in connectors and preserve free DNS records/zones, DNSSEC, ACME, DNS-01 wildcard issuance and ordinary renewal/deployment.
3. Review imports, merges, conflicts and scoped reconciliation using typed records, DNS semantics, serial/delegation policy and current permissions; no raw zone/config injection or unrestricted provider credentials.
4. Use versioned campaigns with explicit node/provider scope, canary/batch budgets, idempotency, leases/fencing and per-target receipts; exercise partitions, stale workers, revoked credentials and partial outcomes without instantaneous-convergence claims.
5. Measure DNS observations from bounded authorized vantage points with TTL, negative caching, resolver/delegation and freshness recorded; an accepted API update or observed resolver answer is not proof of universal propagation.
6. Stage certificate issuance/renewal and deployment through existing owned operations, scoped DNS-01 permissions and secret references; qualify CA rate/issuance constraints, challenge cleanup, wildcard/apex/SAN identity and key retention without exposing private keys.
7. Verify hostname/SNI/chain/expiry and each consuming Web/mail/ingress endpoint after deployment; a certificate file or issued serial alone is not evidence that every service presents the intended identity.
8. Qualify DNSSEC rollover, delegation/provider moves and certificate changes with owner-admitted sequencing and service-specific recovery; destructive rollback cannot assume prior DNS/cache state is instantly restored.
9. Keep unknown, pending, divergent and failed targets visible with correlation and bounded retries; separate delivery receipts, deployed revisions and measured DNS/TLS observations.
10. On expiry, disablement or removal preserve DNS serving, certificates/private keys under their Core owner, free renewal/automation, independent control and documented migration/recovery; provider exit must account for active dependencies.

## Shared release and exit checks

- Keep all 233 essential capabilities, seven Core engine adapters and resource authority available through the free Core. Verify wrong-tenant, revoked/stale authority and malformed-input refusals at the actual use-case boundary.
- Review the selected package source/license model and every distributed dependency/upstream asset; record provenance and applicable obligations. Core's license does not decide this package's terms or compatibility.
- Verify bounded work, secret redaction, replay/failure recovery and independent Core operation after expiry, provider outage, disablement, update and removal. Optional entitlement never creates a permission or bypasses package trust.
- Bind each result to exact source, versions, target, fixtures and outcome; unavailable or unexecuted checks remain explicit. Do not infer passing runtime from these documents or restart local executables.

Consult [Core licensing](../../../WCP360_Core/LICENSING.md), [editions/premium strategy](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and the [documentation-only template](../../_template/README.md). No installer, arbitrary shell, Core manifest or production provider is supplied here.
