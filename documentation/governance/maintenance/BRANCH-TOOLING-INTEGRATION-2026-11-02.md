# Historical branch tooling integration — 2026-11-02

This integration preserves useful development tooling from the former SC-M01
branches. It does not declare independent E3 review, acceptance of an executable
CODE-BASELINE, completion of the Core roadmap or production qualification.
The current implementation is described by the [Core status](../../../WCP360_Core/STATUS.md)
and [working guide](../../../WCP360_Core/WORKING-GUIDE.md).

## Sources and precedence

| Branch | Immutable source tip | Integration treatment |
| --- | --- | --- |
| `audit/closure-matrix-refresh-2026-10-03` | `1fdbe4994f54ddc7682fba03a8b0c37038608b45` | Already an ancestor of both the previous main and current implementation; no source restoration. |
| `design-system/import-v2.5.3.36.4-markdown` | `5ff41780a5b72f9b7d4153debd7dc5449c688cae` | Already an ancestor; its retired source archives are not recreated. |
| `integration/sc-m01-owner-readiness-20261026` | `b1ae801dbc82f0972ab94a9caf0156b86b0b82f1` | Dated coordination documents and inert parser research retained; narrower tooling tips take precedence for shared paths. |
| `maintenance/sc-m01-repository-guardrails` | `fb2a95620f3547007a17bad9b82e18004a413bc8` | Repository tooling and opt-in hook retained; explicit current inventory mode added without changing strict historical defaults. |
| `tooling/sc-m01-contract-traceability-20261026` | `a1c7a2743c300f1cc426485267af1be66dcdac3d` | Traceability/mapping tools, adversarial tests and historical QA proposal consistency retained. |
| `tooling/sc-m01-preflight-checks-20261026` | `547669eee40dd2b866282f5e2fd5d6941c9a6d47` | Latest preflight and original Oct26/Oct30 fingerprint snapshots retained. |

The four divergent branches contribute 49 distinct source paths, including
`.gitignore`; overlapping copies are resolved once. Dated governance documents
now live under `documentation/governance/`. Original JSON evidence snapshots
retain their bytes, historical locators and claims; they are not relabelled as
current approvals. Source Git commits remain available for original Markdown
and tooling bytes. Research fixtures remain isolated under
`research/architecture-lab/`; they are not product agents or privileged daemons.

## Current repository checks

- `tools/repository/check_publication.py --inventory-only` inventories the exact
  requested committed tree, including paths, modes, sizes and content hashes.
  It reports the historical mirror/audit checks as `NOT_APPLICABLE` with an
  explicit reason. It never certifies current completeness or byte equivalence
  to the retired mirror.
- The same checker's default mode remains strict: missing/changed/unlisted mirror
  entries and missing historical audit files fail. CI runs this separately on
  immutable commit `fb2a95620f3547007a17bad9b82e18004a413bc8`, whose own mirror
  manifest and audit documents exist. Current-tree inventories cannot replace
  that historical integrity result.
- The root ignore policy permits reviewed source-only SQL fixture/migration
  locations and named Markdown documentation. Runtime state, `var/`, dependencies,
  root build output and `WCP360_Build/build/` downloaded toolchains remain ignored.
  The reviewed `SOURCE/platform/src/Shared/SecretSafe.php` helper has one exact
  filename exception; no general secrets-path bypass is introduced.
- The opt-in executable pre-commit hook scans a selected staged index. Integration
  does not install `core.hooksPath`. Secret checks emit redacted identifiers, never
  matched values; their selected rules are not a complete secret or history audit.

## Current relocated inputs and historical fixtures

The unchanged Oct30 snapshot pins eight historical artifacts. Five exact blobs
are retained in the current tree under `documentation/specifications/`: API,
IPC, Permissions, Security and Database. QA, DevOps and Engineering historical
files are not restored into the checkout. Their absence must not be presented
as eight matching current Core contracts.

`sc_m01_historical_inputs.py --verify-current` checks the five required current
relocated blobs and reports all eight input states. A present but altered optional
input also fails. The report keeps incomplete status when only five exist and
always refuses to claim Core compatibility, E3 or implementation authorization.
The preflight's `--input-layout documentation` changes only a fixed locator
prefix; it does not rewrite the original manifest or weaken strict eight-pin mode.

CI materializes a fresh private fixture root outside the checkout using:

- all eight pinned canonical blobs from
  `580e9ed58286304ade30142caf9415f4b87c0c28`;
- thirteen historical QA proposal/coverage inputs from
  `a1c7a2743c300f1cc426485267af1be66dcdac3d`;
- the unchanged current copies of the three historical JSON snapshots and the
  ten selected tooling/test files under review.

The exporter has no caller-selected source revision or arbitrary source-path
interface. It checks safe paths, source pins, byte limits, fresh external output,
exclusive writes and per-file provenance. It performs read-only local Git access
with inherited Git configuration, hooks, replacement objects and network fetching
disabled. Missing historical objects fail; workflows use full Git history so no
external download fallback is necessary.

Tests execute from that private fixture root, preserving the original tests'
source-relative assumptions and adversarial assertions. The strict preflight
still requires eight matching pins. Historical 199-record QA proposals and the
85 proposal triage rows remain source-consistency evidence with no product test
executions or owner approval. They are separate from current Core PHPUnit/Go
results and from current production readiness.

## Validation and limits

No Python, PHP, Go, browser or service tests were executed locally for this port,
as requested. Local preparation was limited to source inspection, read-only Git
ancestry/blob comparisons and static path/encoding/reference checks. The imported
and added executable assertions are scheduled in the remote workflows; their
results must be read on the exact published integration revision before any
qualification claim. A workflow definition does not configure branch protection
or provide an independent security review.
