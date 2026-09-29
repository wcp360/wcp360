# WCP360 — Materialized Markdown Inventory

Reference date: 2026-09-29

This inventory records the WCP360 Markdown files that are currently materialized and directly retrievable from Project/Library sources.

## Total materialized Markdown files

```text
33 files
```

## Canonical implementation artifacts already published in GitHub

```text
specifications/api/WCP360_API_Contract_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md
specifications/api/WCP360_API_IPC_CANONICAL_IMPLEMENTATION_EXPORT_MANIFEST.md
specifications/ipc/WCP360_IPC_Contract_WCP360-IPC-B-1_CANONICAL_IMPLEMENTATION_EXPORT.md
specifications/security/WCP360_Permission_Registry_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md
specifications/security/WCP360_Security_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md
specifications/devops/WCP360_SC-M01_DEVOPS_BASELINE_v1.0.md
```

## Design System Markdown snapshot

27 source Markdown files from the materialized staging set are published under:

```text
documentation/design-system/V2.5.3.36.4/
```

The snapshot includes contracts, coverage matrices, state machines, filesystem/runtime boundaries, daemon security material, traceability, freeze gates and related design documentation.

## Authority rule

The Design System snapshot is documentation evidence and design context.

It does not override later canonical owner artifacts under `specifications/`.

## DOC baseline archive availability

The canonical documentation baseline is identified as:

```text
DOC-BASELINE = v4.0.21.23
WCP-Design-System-Spec-v4.0.21.23-global-canonical-registry-consolidation.zip
```

At this time the exact archive is not materialized as a retrievable Library/Project ZIP, so its internal Markdown corpus has **not** been substituted from an older version.

No older v4.0.21.x archive may silently replace v4.0.21.23.

When the exact archive becomes materialized, its Markdown files must be imported under:

```text
documentation/doc-baseline/v4.0.21.23/
```

with a snapshot README preserving authority and supersession rules.

## Current implementation gate

```text
SC-M01 canonical implementation evidence = 5 / 8
SOURCE CODE = PAUSED
SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```
