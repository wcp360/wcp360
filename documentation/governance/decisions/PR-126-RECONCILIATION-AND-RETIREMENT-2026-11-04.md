# MASTER — SC-M01 record reconciliation and bounded branch retirement

Date: 2026-11-04. Authority: the owner's single-maintainer and existing-branch
consolidation instructions, recorded in #131. This is same-author assessment.

## PR #126 preservation and conflict resolution

The exact `5e597d6372c011e887db3fd29311b2d92fa7867d` history is incorporated as
a parent of the existing documentation branch. Four non-conflicting files are
retained byte-for-byte: `SOURCE/daemon/README.md`, `SOURCE/daemon/inputs.json`,
the November 3 inert-foundation owner decision, and its first-package handoff.
No Go source or daemon runtime behavior changes.

The other four #126 changes conflict with later coordination or duplicate its
historical gate updates. `CONTRIBUTING.md` retains the November 4 reconciled
version. `WCP360_Core/WORKING-GUIDE.md`, `documentation/governance/PROJECT-STATUS.md`
and `documentation/manifests/WCP360-BM-0001.md` retain the preceding develop
versions. This is a documented content resolution, not a claim that every #126
blob was accepted unchanged. The original full diff remains in Git ancestry.

The preserved November 3 records describe their recording checkpoint, including
then-unrun follow-up CI and independent-review obligations. Those statements do
not override the November 4 [single-maintainer decision](D-GOV-SOLO-0001.md), the
[consolidation record](MASTER-CONSOLIDATION-2026-11-04.md), or subsequent exact-run
results. No obsolete placeholder review is treated as independent acceptance.
The metadata points to the existing restricted daemon authorization; it does not
authorize new policy execution or canonical IPC-B dispatch. Artifact inspection
not performed remains not performed. Preserving the records is not production
or full daemon-runtime acceptance and does not close that broader part of #13.

## Safe retirement of the three explicitly requested branches

The connector has no direct delete-ref action. The separately reviewed
`retire-integrated-branches.yml` workflow provides the repository's own bounded
maintenance operation, using its short-lived repository-scoped GITHUB_TOKEN.
It does not grant administration permissions or activate branch protections.

The write job runs only after an integrated push to this repository's `develop`,
not on pull-request code. It expires at 2026-11-06T00:00:00Z. Its fixed allowlist is:

| Existing branch | Exact PR required |
| --- | --- |
| features/branch-protection-readiness | 128 |
| docs/core-documentation-2026-11-03 | 133 |
| features/core-http-security-headers | 127 |

The same-repository PR must be merged and target develop. The current branch HEAD
must equal its reviewed PR HEAD. Its exact commit must be an ancestor of the
observed develop commit. An open PR using that branch as head or base prevents
retirement. Incomplete inventories, malformed identities, unauthorized endpoints
and redirects fail closed. An explicit `--force-with-lease=<ref>:<expected>`
**conditional deletion** rejects a concurrent branch update; it does not rewrite
history or update main/develop. Server-side rules are never bypassed. Unmerged
or newly updated branches are preserved. A subsequent read must confirm absence
before reporting DELETED. Permanent and unrequested refs are unconditionally
excluded; the separate SC-M01 branch is not in the cleanup allowlist.

The read-only safety job runs synthetic refusal cases and a real local temporary
Git lease race before the write job. Local test result: 3 admitted PR shapes,
34 refusals, open-PR/redirect checks, stale-lease refusal and correct-lease deletion
passed without any GitHub writes. CI must reproduce this on the published source.
Failures and skipped retirement are not branch-deletion proof. Credentials and raw
remote errors are never printed, and no personal token or production secret is
required. Disable/remove this workflow in a reviewed change to revoke the procedure;
after its fixed expiry it performs no writes even if a later push occurs.

References: Git's `git-push` documentation for explicit leases and GitHub's
GITHUB_TOKEN / workflow-permissions documentation. This operation is separate
from #129's still-required administrative branch-protection configuration.

DOC/DESIGN/BM unchanged; CODE NONE; no release, new branch or privileged product
operation. A separate exact-source MASTER integration decision remains required.
