# WCP360 — Authority Precedence

Reference: `WCP360-BM-0001 FINAL`

This file defines repository authority precedence so that documentary mirrors, historical material and staging exports cannot silently override approved implementation contracts.

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

If two files disagree:

1. do not silently choose the newer-looking filename;
2. do not use numeric version ordering across unrelated domains;
3. identify each file's owner/domain/status;
4. apply this precedence order;
5. if ambiguity remains, open a WCP360 CONFLICT and keep implementation blocked for that point.

## Source Code rule

SOURCE CODE may not consume arbitrary files from the mirror as implementation contracts.

Only approved canonical owner artifacts explicitly accepted by the SC-M01 gate may be used.
