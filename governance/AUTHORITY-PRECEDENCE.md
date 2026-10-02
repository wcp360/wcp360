# WCP360 — Authority Precedence

Reference: `WCP360-BM-0001 FINAL`

This file defines repository authority precedence so that documentary mirrors, historical material and staging exports cannot silently override approved implementation contracts.

## Authority model

The manifest selects and records the active versions, gate state and cross-project baseline references. It does **not** acquire ownership of the technical contents of another domain.

Approved owner artifacts remain authoritative **inside their domain**:

- Documentation project detailed = functional/documentary authority;
- Design System = UI/UX authority;
- MODULES = functional module catalogue and dependencies;
- API / IPC / CONTRACTS = API, IPC, jobs/events/webhooks and contract semantics;
- DATABASE / DATA MODEL = physical/logical PostgreSQL data model and persistence rules;
- SECURITY / HARDENING = authentication, authorization, isolation, privileged-boundary and hardening rules;
- TESTS / QA = canonical test strategy, assertions and validation status;
- DEVOPS / BUILD / RELEASE = platform, filesystem, service/build/package/release lifecycle;
- ENGINEERING / CODE REVIEW = engineering and code-review rules;
- PERFORMANCE / SCALABILITY = performance/capacity validation;
- OBSERVABILITY / INCIDENTS / SRE = observability and incident/SRE rules;
- AUDIT / ARCHITECTURE REVIEW = independent architecture findings and conflict review;
- MASTER / Project Control = baseline coordination, gate state, conflict routing and cross-domain acceptance.

There is **no total technical ordering between owner domains**. A file path, filename, timestamp, higher-looking numeric version or a MASTER summary never overrides an owner rule in another domain.

When one behavior spans domains, all applicable owner constraints compose. Example: an API operation may be defined by API/IPC, but its authorization still follows Security, its persistence follows Database and its proof follows QA. A convenient implementation choice in one domain does not waive another domain's invariant.

If two approved owner artifacts conflict on the same cross-domain behavior, implementation remains blocked for that point until an explicit WCP360 CONFLICT / owner decision / Architecture Review resolution is recorded. No folder hierarchy resolves that conflict automatically.

## Repository-source precedence

The following order answers **which repository source is eligible for consumption**, not which owner wins a technical disagreement.

## Precedence order

For implementation decisions, use the first applicable source in this order:

1. `manifests/WCP360-BM-0001.md`
2. approved canonical owner artifacts under `specifications/`
3. approved governance decisions under `governance/`
4. active Design System snapshot under `documentation/design-system/V2.5.3.36.4/`
5. documentary evidence under `documentation/library-mirror/`
6. historical / staging / audit / superseded evidence

Lower-precedence material may provide context or traceability, but may not override a higher-precedence source.

## Canonical implementation artifacts

At the current gate, implementation authority already present in GitHub includes:

```text
specifications/api/WCP360_API_Contract_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md
specifications/ipc/WCP360_IPC_Contract_WCP360-IPC-B-1_CANONICAL_IMPLEMENTATION_EXPORT.md
specifications/security/WCP360_Permission_Registry_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md
specifications/security/WCP360_Security_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md
specifications/devops/WCP360_SC-M01_DEVOPS_BASELINE_v1.0.md
```

## Mirror rule

`documentation/library-mirror/` is evidence, not automatic authority.

A file in the mirror may be:

- active context;
- historical;
- candidate;
- staging;
- audit evidence;
- superseded;
- cancelled.

Its presence in GitHub does not change that status.

## Explicit non-authorities

The following must never be treated as active merely because they are versioned:

- cancelled Sprint 0;
- cancelled V4.1 “Executable Contracts”;
- obsolete `WC-CLIENT` / `WC-ADMIN` naming;
- pre-H3CM permission registries containing cross-namespace identifiers;
- historical Design System versions;
- candidate audit findings not adopted by MASTER;
- owner drafts superseded by final canonical exports.

## Conflict handling

Content conflict procedure:

1. identify the exact behavior/identifier/resource in conflict;
2. identify each source's owner, version and normative status;
3. separate same-domain supersession from cross-domain conflict;
4. for same-domain supersession, use the owner's approved current artifact selected by the manifest/governance;
5. for cross-domain behavior, preserve all non-conflicting constraints and ask the relevant owners for an explicit joint delta/decision;
6. route unresolved cross-domain conflict to MASTER and, where architectural, AUDIT / ARCHITECTURE REVIEW;
7. record BEFORE → FINDING → DECISION → AFTER → RATIONALE → OWNER → TESTS;
8. only then allow SOURCE CODE to consume the resolved behavior.

Repository precedence still applies to reject historical/mirror/staging material.

Legacy shorthand retained below for compatibility:


If two files disagree:

1. do not silently choose the newer-looking filename;
2. do not use numeric version ordering across unrelated domains;
3. identify each file's owner/domain/status;
4. apply this precedence order;
5. if ambiguity remains, open a WCP360 CONFLICT and keep implementation blocked for that point.

## Source Code rule

SOURCE CODE may not consume arbitrary files from the mirror as implementation contracts.

Only approved canonical owner artifacts explicitly accepted by the SC-M01 gate may be used.
