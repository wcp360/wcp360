# Branch integration — November 2, 2026

**Local integration status:** all fifteen frozen branch heads are ancestors of the integration candidate. Publication to main and branch retirement are pending remote verification.

This register records the user's instruction to finish all work branches and consolidate their retained work into `main`. The frozen input contains **15 branches: main and 14 work branches**, with 11 open pull requests. The earlier [October 31 reconciliation](BRANCH-RECONCILIATION-2026-10-31.md) remains historical evidence.

## Content and history

Use normal content merges, resolve shared files explicitly and preserve the original commit histories. A branch is eligible for retirement only when its frozen head is an ancestor of published main and its useful content has a recorded disposition. Branch count or a merged PR is not a production-readiness measure.

| Input branch | Frozen head | Content disposition |
| :--- | :--- | :--- |
| `audit/closure-matrix-refresh-2026-10-03` | [`1fdbe4994f54`](https://github.com/yyogas/wcp360/commit/1fdbe4994f54ddc7682fba03a8b0c37038608b45) | Already an ancestor of main; no duplicate import. |
| `design-system/import-v2.5.3.36.4-markdown` | [`5ff41780a5b7`](https://github.com/yyogas/wcp360/commit/5ff41780a5b72f9b7d4153debd7dc5449c688cae) | Already an ancestor of main; no duplicate import. |
| `extensions/file-manager-v0.3.0-import` | [`899e0020a342`](https://github.com/yyogas/wcp360/commit/899e0020a3421ac2231dee689f173cc2dd3e6631) | File Manager sources/CI reconciled at the existing WCP360_Plugins path. |
| `implementation/core-executable-foundation-20261031` | [`c94bd41e849c`](https://github.com/yyogas/wcp360/commit/c94bd41e849c91450bcbff2f549727e52114e3b8) | Current Symfony Core, SDK, premium catalogues, prototype and local workspace. |
| `integration/sc-m01-owner-readiness-20261026` | [`b1ae801dbc82`](https://github.com/yyogas/wcp360/commit/b1ae801dbc82f0972ab94a9caf0156b86b0b82f1) | Historical bundle reconciled with narrower successors; current decisions retained. |
| `main` | [`7eddd41a4ef6`](https://github.com/yyogas/wcp360/commit/7eddd41a4ef62dd6a3c737481b304f67bca4f5ba) | Integration target; existing main history retained. |
| `maintenance/sc-m01-repository-guardrails` | [`fb2a95620f35`](https://github.com/yyogas/wcp360/commit/fb2a95620f3547007a17bad9b82e18004a413bc8) | Repository guardrails retained and adapted to the current tree. |
| `research/sc-m01-control-protocol-options-20261026` | [`44c85eebcd29`](https://github.com/yyogas/wcp360/commit/44c85eebcd297dd00ca8afe55f84b26a6df919ce) | Inert architecture labs retained separately; current contract locators repaired. |
| `research/sc-m01-ipcb-go-framing-20261027` | [`c5ef94c875fe`](https://github.com/yyogas/wcp360/commit/c5ef94c875fe16e9b7874541a363d9c783d50f55) | Inert architecture labs retained separately; current contract locators repaired. |
| `source/full-platform-candidate` | [`284daabc8d57`](https://github.com/yyogas/wcp360/commit/284daabc8d57a65d3ad11e326baee32898e64254) | Standalone SOURCE workbench; compatible daemon/domain additions combined. |
| `source/sc-m01-domain-foundation` | [`31fd3fe4d920`](https://github.com/yyogas/wcp360/commit/31fd3fe4d9203d9e5dbc2b43843c6c9ec74f2940) | Standalone SOURCE workbench; compatible daemon/domain additions combined. |
| `source/sc-m01-inert-daemon` | [`71deefa2976a`](https://github.com/yyogas/wcp360/commit/71deefa2976a7005983d410907dc2050287be05f) | Standalone SOURCE workbench; compatible daemon/domain additions combined. |
| `test/sc-m01-cli-process-contract` | [`462792110330`](https://github.com/yyogas/wcp360/commit/462792110330f00b2a1911dd03175e8257379b3a) | Daemon process-contract tests retained with the combined CLI. |
| `tooling/sc-m01-contract-traceability-20261026` | [`a1c7a2743c30`](https://github.com/yyogas/wcp360/commit/a1c7a2743c300f1cc426485267af1be66dcdac3d) | Governance verification/traceability tools retained with explicit historical/current scopes. |
| `tooling/sc-m01-preflight-checks-20261026` | [`547669eee40d`](https://github.com/yyogas/wcp360/commit/547669eee40dd2b866282f5e2fd5d6941c9a6d47) | Governance verification/traceability tools retained with explicit historical/current scopes. |

The initial main is [`7eddd41a4ef6`](https://github.com/yyogas/wcp360/commit/7eddd41a4ef62dd6a3c737481b304f67bca4f5ba); the uploaded workspace before consolidation is [`c94bd41e849c`](https://github.com/yyogas/wcp360/commit/c94bd41e849c91450bcbff2f549727e52114e3b8). Exact input refs and path-level decisions are retained in [the integration evidence](BRANCH-INTEGRATION-2026-11-02.json).

## Maintained locations

| Location | Role after consolidation |
| :--- | :--- |
| [WCP360_Core](../../../WCP360_Core/README.md) | Current Symfony application, production-engine reservations and isolated Go planning/provisioning experiments. |
| [SOURCE](../../../SOURCE/README.md) | Separate framework-independent platform, inert daemon and domain/browser-policy workbench. It is not the current Symfony Core and is not loaded by Core Composer or startup. |
| [research/architecture-lab](../../../research/architecture-lab/) | Inert protocol research and explicit parser tests; no authenticated production listener or privileged operation. |
| [WCP360_Plugins/wcp360-file-manager-xampp](../../../WCP360_Plugins/wcp360-file-manager-xampp/README.md) | One standalone File Manager source tree, with the branch's CI harness and repaired path consumers. No duplicate WCP360_Extensions tree. |
| [tools](../../../tools/) | Repository integrity/secrets and governance verification sources, with applicable current-tree checks separated from pinned historical evidence. |
| [documentation/governance](../) | Relocated handoffs, verification manifests and dated evidence. The retired root governance layout is not restored as a second authority. |

Existing release records, Core dependencies, premium design-only admission flags and accepted specification bytes retain their own authority. An old source branch cannot overwrite current product choices, optional-MFA policy, supported OS targets or the Core license by virtue of being merged.

## Verification and interpretation

Local executable tests remain stopped. Static inspection, source identities, conflict resolution, path checks and remote GitHub execution are distinct evidence. Remote results apply only to their exact source/merge SHA; failed or unexecuted checks remain explicit.

The governance preflight distinguishes the current relocated inputs from the complete pinned historical eight-input snapshot. Publication inventory for a current tree without the retired library mirror is not described as successful mirror verification; strict historical mirror checks remain separate. Historical reports are not current test results.

The integration is publication of development candidates under the user's consolidation instruction. It does not assert independent Engineering E3, audit closure, security-owner acceptance, production installation support, a stable release or a new CODE-BASELINE. Remaining qualification requirements stay recorded in the [Core status](../../../WCP360_Core/STATUS.md), [security policy](../../../SECURITY.md) and original review evidence.

## Branch completion

After main contains the tested integration, verify each frozen branch SHA against main, close any remaining superseded/stacked pull requests with an integration reference, and remove only the verified work-branch refs. Keep main, immutable Git history, source inventories and the dated records. Continue subsequent local work from main.
