# WCP360 — FIRST IMPLEMENTATION PACKAGE — HANDOFF

- **Date:** 2026-11-03
- **Slice:** `SC-M01 — INERT DAEMON FOUNDATION`
- **Authority:** [Ratified MASTER decision](../decisions/OWNER-DECISION-SC-M01-INERT-DAEMON-FOUNDATION-2026-11-03.md)
- **Package status:** **BLOCKED — exact-candidate CI and independent reviews remain outstanding**
- **Code baseline:** `NONE`

## Decision and scope

The owner explicitly ratified the limited SC-M01 decision on 2026-11-03. Only verification/consolidation of the already-present inert Go daemon, minimal foundation fixes, CLI/import-boundary validation, dependency/build checks, directly related positive/negative tests and the review handoff are authorized. No Core application implementation, daemon duplication under `WCP360_Core/agents/`, privileged capability, IPC-B execution, database change, release or production qualification is included.

## Exact source and inventory

| Identity | Value | Meaning |
|---|---|---|
| Repository | `https://github.com/wcp360/wcp360` | Source repository |
| Branch at inventory | `main` | Observed branch |
| Inventory reference commit | `d42d86ab12968ea0cbb3b0dec2a2eee4c9ec625f` | Reference only; not a newly approved code SHA |
| Inventory tree | `2174bff9439f29dbfc26e5a6849dea9da85f9a53` | Git tree at the inventory reference |
| Daemon subtree | `dcf0954cf83cb8cbdc9fda5c4719c6d35353241a` | Exact daemon tree identity |
| CI workflow blob | `2aeae8662371e0dea142c60d3f034708944fcefb` | Exact workflow file identity |
| Daemon input manifest blob | `ef0e49d9103a5331a5a9cad87785d9143d5f9529` | Exact `inputs.json` identity at inventory reference |

The 30-file inventory is:

```text
SOURCE/daemon/
  README.md
  go.mod
  inputs.json
  cmd/wcpd/main.go
  internal/README.md
  internal/browserpolicy/{authenticate.go,authenticate_test.go,cookie.go,cookie_test.go,mutation.go,mutation_test.go,session_mutation.go,session_mutation_test.go}
  internal/cli/{cli.go,cli_test.go}
  internal/consistency/{guards.go,guards_test.go}
  internal/contracts/{catalog.go,catalog_test.go,registry.json,retry.go,retry_test.go}
  internal/sessionpolicy/{policy.go,policy_test.go}
  tools/{check-cli.sh,check-negative.sh,check.sh,contracts/generate.py,govulncheck.modules,scan.sh}
```

That inventory contains 19 Go files, four shell scripts, one Python generator, two JSON files, one `go.mod`, one scanner module inventory and two Markdown files. The existing daemon subtree includes pure policy/catalog candidate packages; their presence and CI do not independently approve integration. Their owner guide still requires Engineering and Security review before integration.

The implementation consumes the exact source blobs listed in the [decision's contract provenance table](../decisions/OWNER-DECISION-SC-M01-INERT-DAEMON-FOUNDATION-2026-11-03.md). The API, permission and IPC registry data are source-derived metadata; they are not executable authorization or permission grants.

## Files changed for this handoff

No Go implementation, canonical API/IPC/Security/Database contract or permission definition was changed. This increment records the ratification and source provenance in the decision, manifest, status, contributor/Core guide, daemon guide and daemon input metadata; this handoff document is the evidence summary. Exact changed-file fingerprints and the resulting local candidate commit are recorded in the companion local quality report `projects/WCP360_Quality/reports/sc-m01-inert-daemon-foundation-20261103/report.json`.

## Tests, CI and security evidence

| Control | Status | Evidence / limitation |
|---|---|---|
| Exact source identity and isolated inventory | `passed` | The isolated worktree was clean before edits at `d42d86ab12968ea0cbb3b0dec2a2eee4c9ec625f`; the pre-edit source tree is recorded above. |
| Static boundary review | `passed` | CLI is finite and only `cmd/wcpd` imports process arguments/streams; the workflow's exact import allowlist excludes filesystem, process, network-I/O, database and arbitrary execution imports from policy packages. This is static review, not independent E3. |
| Reference CI run | `passed` | Run [41549448511](https://github.com/wcp360/wcp360/actions/runs/41549448511), head `cf968e7a246ceccd9296423060c323fac72c06f3`, completed successfully on 2026-11-03. Before this handoff's documentation/provenance edits, its daemon subtree, workflow, `inputs.json` and five consumed contract-source blobs matched the `d42d86a` inventory reference exactly. The new candidate edits `README.md` and `inputs.json`, so this is not CI for the final candidate. |
| CI artifact metadata | `passed` | The reference run exposes one non-expired 4,714,007-byte artifact. The artifact contents were not downloaded or independently inspected. |
| CI on the exact inventory SHA or resulting candidate | `not_run` | No workflow run for `d42d86ab12968ea0cbb3b0dec2a2eee4c9ec625f` was found. The workflow has no manual-dispatch trigger; no branch was pushed and no PR was created. |
| Build, unit tests, CLI process tests, negative fixtures, reproducibility and vulnerability scan on the exact SHA | `not_run` | Local execution is prohibited by the ratified decision; the exact SHA workflow evidence is absent. Reference run output does not transfer to the exact SHA. |
| Independent Engineering E3 | `not_run` | Required on the final candidate SHA; no independent approval is claimed. |
| Security / Hardening review | `not_run` | Required before merge. Existing scanner egress notes are not an independent review. |
| QA review | `not_run` | Required before merge. |
| DevOps / Build / Release review | `not_run` | Required before merge. |
| Production qualification / release | `not_applicable` | Explicitly outside this slice and not authorized. |

The successful reference workflow covers a pinned Debian/Go environment, offline build and unit gates, negative fixtures, CLI process tests, reproducible builds and the constrained `govulncheck` scan. The result remains bound to its own head SHA. A green run or passing test does not constitute independent review.

## Findings and blockers

1. **Exact-revision evidence is missing.** The one successful public main-branch run found is for `cf968e7...`, not the inventory commit or the subsequent candidate. Equal source/input blobs reduce uncertainty but do not satisfy the exact-SHA evidence requirement.
2. **Independent reviews remain mandatory.** The repository's review policy requires technically separate, exact-SHA E3; existing PR/status checks do not substitute for it.
3. **No code defect was established by this static pass.** No source correction was made. This is not a claim that the daemon is qualified or that all candidate policy packages are approved.
4. **Existing pure policy packages remain unapproved for integration.** Their own guide identifies them as later candidates and calls for Engineering/Security review. Their continued presence on `main` is not retroactively accepted here.
5. **Historical documentation links remain stale.** The unchanged lower section of `PROJECT-STATUS.md` references readiness snapshots absent from this tree. New/modified links for this decision and handoff are checked separately; the historical references are not represented as passing.

## Handoff decision

Do not merge, release, deploy, create a code baseline or widen the slice. The next controlled step is for MASTER to authorize a single review/CI submission of the local candidate so the existing workflow can run on its exact commit. Then obtain the required E3, Security, QA and DevOps opinions on that same final SHA and return the evidence package to MASTER.
