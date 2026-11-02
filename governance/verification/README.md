# SC-M01 artifact fingerprint preflight (NON-CANONICAL)

Current snapshot: 2026-10-30. GitHub issue #13 / #41.

The 8-artifact manifest and Python tool are **read-only repository tooling**. They do not create implementation code, change a baseline, approve DB/QA/Engineering contents, verify E3 or open the SC-M01 gate.

## Local commands

Run on a clean checkout (Python 3.9+ standard library):

    python3 -m unittest discover -s tools/governance -p 'test_sc_m01_preflight.py' -v

Print inventory (informational, exits 0 even while incomplete):

    python3 tools/governance/sc_m01_preflight.py --format json

Require all eight exact pinned local blobs (exit 2 if incomplete):

    python3 tools/governance/sc_m01_preflight.py --require-all-pinned

The tool calculates Git blob SHA-1, not plain SHA-1. For a local file with bytes B, this is SHA1('blob '+len(B)+'\\0'+B). A matching pin is a byte-level input check, **not proof of FINAL owner approval**.

Current source snapshot: main 580e9ed58286304ade30142caf9415f4b87c0c28.

- All eight canonical input files are pinned to the exact Git blobs present in
  that main snapshot: API, IPC, permissions, Security, Database, QA, DevOps and
  Engineering. The selected Engineering version is v0.3; its legacy v0.2 file
  name is a locator, not the active normative version.
- The default inventory is
  `SC-M01-CANONICAL-INPUT-FINGERPRINTS-2026-10-30.json`.
- The original 2026-10-26 inventory remains byte-for-byte unchanged as historical
  five-pin evidence. Use `--manifest` explicitly to inspect that older snapshot;
  it cannot satisfy strict mode and does not describe current publication state.
- A new owner-approved version needs a reviewed manifest update with new file path, SHA, owner provenance, and version impact.

CI runs the strict check against the actual checkout, in addition to unit tests.
The new end-to-end cases check the committed eight-input snapshot, changed
Database bytes and a missing final QA artifact. Every result continues to report
owner approval, E3 and implementation authorization as unverified by this tool.
Eight exact matches are source-integrity evidence, not a grant of authority.

## Limits of evidence

- Worktree-only inspection: a matching local file may not be merged into GitHub main.
- Does not verify GitHub issue state, reviewer identity or baseline membership.
- Does not execute WCP360 product tests or verify semantic compatibility.
- Fails closed for missing files, unconfigured paths, changed hashes, invalid manifests and unsafe paths when strict mode is used.
- No result can change the MASTER-selected slice, release an implementation gate
  or establish a CODE-BASELINE; the current manifest authorizes only the inert
  daemon foundation. This R1 tooling change still needs E1 and independent E3
  plus applicable Engineering/DevOps/Security review before merge.

## Safety

No external network requests, no process execution, no writes by the script, no secrets and no application daemon interaction. CI is a read-only check on pull requests; it cannot replace independent review.
