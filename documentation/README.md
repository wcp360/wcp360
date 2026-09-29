# WCP360 Documentation

This directory stores versioned documentary evidence and baseline snapshots.

## Current canonical reference

```text
DOC-BASELINE = v4.0.21.23
CANONICAL ARCHIVE REFERENCE =
WCP-Design-System-Spec-v4.0.21.23-global-canonical-registry-consolidation.zip
```

The physical canonical archive is not currently available in Project/Library storage, so it has **not** been reconstructed or substituted.

Tracking: GitHub issues #16 and #24.

## Available snapshot

`snapshots/2026-09-27/`

Contains the full 42-Markdown consolidated working snapshot previously generated for WCP360, plus an authority-status file.

This snapshot:

- is preserved for history and traceability;
- predates final H3-CM/H3-V2 closure;
- contains provisional/open states later superseded;
- is not implementation authority;
- does not replace `WCP360-BM-0001 FINAL`;
- does not replace current owner artifacts under `specifications/`.

## Publication rule

When the physical DOC baseline archive becomes available, import it byte-faithfully under:

`documentation/baselines/v4.0.21.23/`

Preserve relative paths and publish a checksum manifest. Do not silently edit or normalize the imported baseline.


## Materialized Project/Library mirror

The complete Markdown corpus currently materialized under the WCP360 Project/Library path is versioned under:

`documentation/library-mirror/`

Verified:

```text
SOURCE MARKDOWN = 147
MIRRORED = 147
MISSING = 0
```

This mirror is documentary evidence only. It does not automatically promote historical, staging, audit, candidate or superseded material to implementation authority.

See:

- `governance/AUTHORITY-PRECEDENCE.md`
- `governance/CANONICAL-ARTIFACT-INDEX.md`
- `governance/SC-M01-CANONICAL-ARTIFACT-STATUS.md`
