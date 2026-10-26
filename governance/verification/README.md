# SC-M01 artifact fingerprint preflight (NON-CANONICAL)

Date: 2026-10-26. GitHub issue #13 / #41.

The 8-artifact manifest and Python tool are **read-only repository tooling**. They do not create implementation code, change a baseline, approve DB/QA/Engineering contents, verify E3 or open the SC-M01 gate.

## Local commands

Run on a clean checkout (Python 3.9+ standard library):

    python3 -m unittest discover -s tools/governance -p 'test_sc_m01_preflight.py' -v

Print inventory (informational, exits 0 even while incomplete):

    python3 tools/governance/sc_m01_preflight.py --format json

Require all eight exact pinned local blobs (exit 2 if incomplete):

    python3 tools/governance/sc_m01_preflight.py --require-all-pinned

The tool calculates Git blob SHA-1, not plain SHA-1. For a local file with bytes B, this is SHA1('blob '+len(B)+'\\0'+B). A matching pin is a byte-level input check, **not proof of FINAL owner approval**.

Source snapshot: main e1fd356f5aa6bc89e1a9316442f6ed033e90ed14.

- Five observed canonical files are pinned to their published Git blob SHAs.
- DB (#6), QA (#7), Engineering (#9) remain unpinned or unspecified.
- QA filename stays null until owner publication chooses the canonical target path.
- A new owner-approved version needs a reviewed manifest update with new file path, SHA, owner provenance, and version impact.

## Limits of evidence

- Worktree-only inspection: a matching local file may not be merged into GitHub main.
- Does not verify GitHub issue state, reviewer identity or baseline membership.
- Does not execute WCP360 product tests or verify semantic compatibility.
- Fails closed for missing files, unconfigured paths, changed hashes, invalid manifests and unsafe paths when strict mode is used.
- No result can change SC-M01 PAUSED or CODE-BASELINE NONE; only MASTER after owner decisions and independent verification may do so.

## Safety

No external network requests, no process execution, no writes by the script, no secrets and no application daemon interaction. CI is a read-only check on pull requests; it cannot replace independent review.
