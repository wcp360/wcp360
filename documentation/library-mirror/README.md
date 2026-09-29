# WCP360 — Project Library Markdown Mirror

Snapshot date: 2026-09-29

This directory mirrors **all Markdown files currently materialized under the WCP360 Project/Library path**.

Verified import:

```text
SOURCE MARKDOWN FILES = 147
PRESENT IN THIS MIRROR = 147
MISSING = 0
```

## Authority rule

This mirror is a **documentary snapshot / evidence archive**.

It does **not** automatically make every mirrored file an active implementation authority.

Canonical implementation authority remains under:

```text
/specifications/
/manifests/
/governance/
```

according to WCP360-BM-0001 and the owner-domain governance rules.

Historical, candidate, audit, staging and superseded files in this mirror remain historical/candidate/audit/staging/superseded even though they are versioned in GitHub.

## Important exclusions

Nothing in this mirror may reactivate:

- cancelled Sprint 0;
- cancelled V4.1 “Executable Contracts”;
- obsolete WC-CLIENT / WC-ADMIN naming;
- superseded permission registries;
- historical Design System versions as current implementation authority.

## Source Code gate

```text
SOURCE CODE = PAUSED
SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```

SOURCE CODE must consume only approved canonical artifacts, not arbitrary files from this mirror.
