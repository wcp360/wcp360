# PR #135 — repository evidence index

Date: 2026-11-04. Storage decision: [D-GOV-REPOSITORY-FIRST-0001](../../REPOSITORY-DELIVERY.md).
[Pull request](https://github.com/wcp360/wcp360/pull/135).

No owner download or manual file transfer is required. Read the files here in
GitHub. This index and the attached evidence are part of the existing work branch
and PR; they are not yet merged into develop.

## Current checkpoint versus historical evidence

The implementation of repository verification tools is at
`9db6fc8844facbb2502943e758744944a67995fd`, based on
`bcc6f7ab4cb6a36b377ae40e3bb9258506396be9`. Its tested merge is
`dff8d82767ecddebb4d327462e77a53582794e4d`, tree
`ce44ab49e95eb596470cd8f9b32d85bde907d641`.
This later documentation-only delivery preserves evidence for that source; it
does not claim those runs tested the delivery commit itself.

The runtime authentication proposal remains unpublished. No MFA implementation,
new application test, canonical approval or production qualification is established
by transferring these documents into Git. PR #135 remains DRAFT / UNMERGED.

## Readable reports now versioned

| Record | Meaning |
|---|---|
| [Historical engineering report](local-review-2026-11-04/ENGINEERING-REVIEW.md) | Exact original documentary report; describes the earlier scope-only checkpoint, not current PR state |
| [Historical local execution evidence](local-review-2026-11-04/local-test-evidence.json) | Original isolated local probes, with recorded outputs and limits; not PHPUnit or full-stack CI |
| [Historical proposal manifest](local-review-2026-11-04/local-proposal-manifest.json) | Identities of unpublished local proposal files; metadata does not publish those files |
| [Original dossier README](local-review-2026-11-04/README.md) | Preserved historical delivery note |
| [Original dossier checksums](local-review-2026-11-04/SHA256SUMS.txt) | Verifies the four original documentary files above |
| [Exact CI MFA report](run-41874965779/mfa-alignment.json) | Eight source identities, fifteen sensitive profiles, eight test sources; six gates remain OPEN |
| [CI evidence index](run-41874965779/EVIDENCE.json) | Source/run identities, archive digest and all six artifact-member digests |

The five historical files are copied byte-for-byte from the reviewed documentary
archive. That archive did not contain application source or an executable patch.
Statements such as "scope only" or "queued" in those historical files remain
attached to their original commit/time and are superseded for current status by
this index and subsequent source-bound reports.

The earlier local results and the later repository-tool CI are different evidence
classes. The local proposal is not the code executed by the CI report below.

## Source and tools already in this PR

- [Contract composition review](../../../specifications/security/MFA-IDENTITY-COMPOSITION-2026-11-04.md).
- [Source coverage ledger](../../../specifications/security/MFA-ALIGNMENT-COVERAGE.json).
- [Read-only traceability checker](../../../../tools/repository/check_mfa_alignment.py).
- [Checker regression tests](../../../../tools/repository/test_mfa_alignment.py).
- [Symbolic transaction-model tests](../../../../tools/repository/test_mfa_transaction_model.py).

## CI records for the verification-tool commit

[Repository guardrails 41874965779](https://github.com/wcp360/wcp360/actions/runs/41874965779)
completed successfully, including fifteen checker tests, ten symbolic model tests,
eleven admitted and forty-two refused routing fixtures, and source traceability.
[Platform verification 41874965982](https://github.com/wcp360/wcp360/actions/runs/41874965982)
and [inert-daemon checks 41874965939](https://github.com/wcp360/wcp360/actions/runs/41874965939)
completed within their own existing scopes. None is a run of the unpublished
runtime patch or a new fourteen-job Core campaign for that patch.

The complete generated publication inventories are retained as
[repository artifact 13346761433](https://github.com/wcp360/wcp360/actions/runs/41874965779/artifacts/13346761433).
Their identities and the essential MFA report are committed here so the project's
status does not depend on a chat attachment or artifact availability. Large raw
inventory copies are not duplicated in Git by this delivery. They can be
regenerated from the pinned commits using the versioned publication checker;
regeneration is not a claim to reproduce the original archive bytes.

## Unresolved acceptance gates

Canonical Security/API version disposition; atomic factor/session adapter; USER
MFA journey; session-isolated database equivalence; blocked runtime publication;
full-stack qualification. All remain OPEN. The owner policy is settled, but these
engineering tasks are not replaced by alternate-account approvals or file storage.

No new branch, protected-branch push, merge, source-contract rewrite, blocked
runtime-patch publication or release is performed by this documentary migration.
