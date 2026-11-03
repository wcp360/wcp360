# Branch completion — November 3, 2026

**Completed:** only `main` remains locally and remotely, with **no open pull requests** at the recorded verification snapshot. The [original integration](BRANCH-INTEGRATION-2026-11-02.md) and [follow-up PR #125](https://github.com/yyogas/wcp360/pull/125) preserve the development histories and the reviewed dependency changes. Exact refs, CI identities and dispositions are in [the completion evidence](BRANCH-COMPLETION-2026-11-03.json).

| Scope | Result |
| :--- | :--- |
| Original main and 14 work branches | All frozen input heads retained in main; shared files reconciled, source locations documented and duplicate File Manager tree removed. |
| Three compatible dependency proposals | #117 UX Turbo 3.6, #118 Checkout 7.0.1 and #121 Upload Artifact 7.0.2 integrated and tested together. |
| Five major-migration proposals | #119, #120, #122, #123 and #124 closed as deferred; complete diffs and original histories archived, without adopting PHP 8.5 or Symfony 8. |
| Reviewed reference count | 23 branch refs: main plus 22 retired work refs. Seventeen work heads are ancestors of main; five deferred heads are preserved by archive tags. |
| Final working branch | main; origin/HEAD points to main. No replacement integration branch was created. |

## Published source and checks

| Integration | Exact evidence |
| :--- | :--- |
| Initial consolidation, #116 | Head `59fb0ce189e8fdf312c5f48ca4a7e9a7881e6302`; main merge `d6f04e1697062cb0f1b0f1f46ad676eddab12fe9`; all 30 configured checks succeeded. |
| Dependency follow-up, #125 | Head `5bd4b54f1f2f12d6783d018f94b04ed48acd12cb`; tested PR merge `8b887c9ab093b64fb36fb08d265002ca232a7a01`; main merge `cf968e7a246ceccd9296423060c323fac72c06f3`. |
| Published tree | `d42ddb97a101b32140f900f77321b8f4d80e221a`, identical to the qualified follow-up candidate tree. Later documentation records do not create another runtime result. |
| Follow-up CI | **31/31 check runs** succeeded, including the Dependabot configuration check and 12 workflows. The [Core run](https://github.com/yyogas/wcp360/actions/runs/41545847754) completed **14/14 jobs**. |

Core evidence includes Composer 2.11.1 manifest/platform/audit checks, PHPStan 2.3.4, **1,383 PHPUnit tests and 14,353 assertions**, two browser tests with 44 assertions, FrankenPHP Worker, five target userspaces, native package probes, and the isolated Debian 13 recovery/reboot rehearsal. Two non-failing PHPUnit notices about mocks without expectations remain recorded.

The [File Manager run](https://github.com/yyogas/wcp360/actions/runs/41545847684) verifies all three PHP lanes, including the new Actions and their actual uploaded artifacts: 50 tests, 122 assertions and 28 browser journeys per lane. Artifact metadata/digests were checked without downloading or executing the archives.

Local executable tests remained stopped. Static file, path, Git-ancestry and archive-integrity checks are separate from the remote test results.

## Preserved decisions and sources

Use the [411-path integration map](BRANCH-INTEGRATION-2026-11-02.paths.json) for historical source reconciliation. Current Symfony Core, the separate SOURCE workbench, inert architecture labs, canonical File Manager package and relocated governance tools retain their documented ownership.

Read the [deferred migration guide](deferred-updates-2026-11-03/README.md) and [checksum manifest](deferred-updates-2026-11-03/MANIFEST.json) before revisiting a proposal. The five `archive/deferred-pr-N-20261103` tags preserve exact original commits; they are not release tags. Deferred changes were not marked as merged or made ancestors through a content-discarding merge.

The [Dependabot configuration](../../../.github/dependabot.yml) continues routine compatible proposals and preserves security-update proposals. Future normal proposals may create new branches; this document records the completed snapshot, not a permanent ban on automation.

## Limits and continuation

Source consolidation does not complete the [Core roadmap](../../../WCP360_Core/ROADMAP.md), grant independent E3 approval, qualify a production installation or publish a stable release. The [Core status](../../../WCP360_Core/STATUS.md) remains the executable checkpoint and acceptance reference.

A concurrent local edit to `WCP360_SDK/php/registry-client/examples/local-registry.php` appeared during the operation. It was preserved uncommitted outside this consolidation and is not part of the tested candidate. Private preparation, toolchains and generated evidence remain ignored.

Continue Core development from main using the [working guide](../../../WCP360_Core/WORKING-GUIDE.md).
