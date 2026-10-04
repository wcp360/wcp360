# WCP360 — PR #42 Independent Technical Review Packet

**Status:** REVIEW REQUEST PACKAGE — NON-APPROVAL  
**Target PR:** #42 — repository guardrails  
**Review class required:** E3 — independent technical review  
**Candidate SHA:** c10c6296868852f15ed600025b8b31d589135be7  
**Base main used by latest E1 run:** 1b828833bb13b99381e65971c58d9d18c0f57af4  
**Latest merge-test SHA:** ffbafd88729ed62f2f0394f999241163950e2db7  
**Latest E1 run:** 37194191355 — SUCCESS

This packet exists so an independent reviewer can evaluate PR #42 without reconstructing its history from dozens of comments.

It is not a review. The author/assistant must not fill the reviewer verdict section.

## 1. Why E3 is required

Under \`governance/REVIEW-MERGE-POLICY.md\`, PR #42 is class R1 because it changes repository security/CI tooling:

- Git ignore policy;
- GitHub Actions;
- repository validation scripts;
- secret-pattern checking;
- pre-commit hook behavior.

Current evidence:

    E0 authored = YES
    E1 automated verification = YES
    E2 same-author publication control = YES
    E3 independent technical review = NO

The PR must remain unmerged until E3 is supplied or governance explicitly changes that requirement.

## 2. Changed paths to review

Exactly 18 PR paths:

    .githooks/pre-commit
    .github/workflows/repository-guardrails.yml
    .gitignore
    documentation/branding/candidates/README.md

    governance/maintenance/REPOSITORY-SECRET-CHECK.md
    governance/maintenance/SC-M01-REPOSITORY-GUARDRAILS.md
    governance/maintenance/STAGED-SECRET-CHECK.md
    governance/maintenance/evidence/branding-source-check-2026-10-02.json
    governance/maintenance/evidence/gitignore-regression-2026-10-02.json
    governance/maintenance/evidence/staged-parser-fix-2026-10-02.json
    governance/maintenance/evidence/staged-secret-check-2026-10-02.json

    tools/repository/check_branding_archive.py
    tools/repository/check_gitignore.py
    tools/repository/check_publication.py
    tools/repository/check_repository_secrets.py
    tools/repository/test_publication_checker.py
    tools/repository/test_repository_secrets.py
    tools/repository/test_staged_repository_secrets.py

The latest synchronization commit preserves these 18 blobs; it only merges the current main history.

## 3. Intended behavior

### 3.1 .gitignore

Goal:
- keep secrets/local/runtime/build-output/dumps excluded;
- stop hiding legitimate source paths such as migrations, fixtures, nested runtime/build source directories and selected documentation;
- never make the ignore file itself the only secret control.

Expected regression result:

    original rules = 41 / 52 expectations satisfied
    candidate rules = 52 / 52 expectations satisfied

Reviewer should verify the exceptions do not accidentally allow common dump/credential paths.

### 3.2 check_gitignore.py

Goal:
- evaluate synthetic tracked/ignored paths using Git itself;
- run in an isolated temporary repo;
- avoid relying on caller Git configuration/hooks;
- return failure when expectations regress.

Review points:
- correct handling of negated patterns;
- no unsafe filesystem traversal;
- deterministic probes;
- fail-closed error handling;
- no accidental dependency on production repository content.

### 3.3 check_publication.py

Goal:
- inventory exact Git objects at a commit;
- recalculate Git blob identity and SHA-256;
- verify the 147-entry documentary mirror against its committed manifest;
- report audit/canonical-artifact presence without claiming unavailable external archives are complete.

Review points:
- ref validation;
- tree/path parsing;
- size/aggregate bounds;
- safe manifest path handling;
- correct interpretation of "verified against manifest";
- no misleading statement that Git mirror equality proves original Library byte equality.

Tests currently cover:
- valid inventory;
- changed blob;
- missing file;
- unlisted file;
- unsafe ref rejection;
- invalid manifest count.

### 3.4 check_branding_archive.py

Goal:
- validate the exact expected archive of seven recovered branding candidates if/when it exists;
- keep absence represented as NOT_PUBLISHED;
- avoid turning image candidates into approved brand assets.

Review points:
- archive path safety;
- count/name/hash matching;
- symlink/special entry handling;
- decompression/bomb bounds;
- optional absence semantics;
- no secret/private source leakage.

Self-tests currently reported: 9 / 9.

### 3.5 check_repository_secrets.py

Goal:
- inspect committed Git blobs, and optionally the staged index, for a bounded set of high-risk patterns/paths;
- never print matched secret values or raw sensitive filenames in reports/errors;
- fail closed on incomplete/unsupported scans;
- do not follow symlinks/submodules.

Selected rules include:
- sensitive paths such as .env/key containers/dumps;
- private-key PEM headers;
- selected GitHub token forms;
- installation-token/JWT-like pattern.

Important limitation:
- this is **not** a universal secret scanner;
- it does not prove history clean;
- it does not decompress archives;
- it does not validate credentials online;
- arbitrary passwords/other providers may not be detected.

Review points:
- regex false-negative/false-positive tradeoffs;
- binary handling;
- bounded memory/time/file count;
- Git object identity verification;
- alternate index behavior;
- sanitization of errors and path identifiers;
- whether findings are sufficiently actionable without leaking paths/values;
- no hidden network use;
- no execution of repository source.

### 3.6 staged-index / pre-commit behavior

The staged mode reads the index, not the working tree.

The opt-in hook:
- is versioned;
- is not automatically installed;
- honors Git alternate index for partial commits;
- fails if checker/python/repository unavailable;
- does not replace server-side CI.

Historical defect fixed:
- scan launched from a subdirectory omitted root paths;
- regression tests now anchor scans at repository root.

Historical CI parser defect fixed:
- explicit --staged --ref HEAD conflict behaved differently due argparse default;
- failing test retained;
- parser default corrected;
- reverse-order/empty-ref cases added.

Review points:
- staged partial commit semantics;
- alternate GIT_INDEX_FILE trust/path validation;
- index mutation/recheck race limitations;
- hook bypassability accurately documented;
- no user Git config mutation.

## 4. Latest automated evidence

GitHub Actions:

    run = 37194191355
    result = SUCCESS
    candidate head = c10c6296868852f15ed600025b8b31d589135be7
    merge-test = ffbafd88729ed62f2f0394f999241163950e2db7

Jobs:

    repository-secrets / 111412518289 = SUCCESS
    gitignore-regression / 111412518450 = SUCCESS

All listed steps completed successfully.

Evidence artifacts:

    repository secrets artifact
    id = 11300088105
    digest = sha256:42866f38cae2f2ec67312e5d185366650d719770d466b89feacee6024616669b

    publication artifact
    id = 11299853630
    digest = sha256:94ed2422754c5e819083d685f7d9a668e4fd4607eb6f012eafe10d789758872e

Parsed latest reports:

    base main = 305 files / 1,389,780 bytes
    candidate = 322 files / 1,491,881 bytes
    merge-test = 322 files / 1,491,881 bytes

    mirror = 147 / 147 exact manifest matches
    audit = 4 / 4

    secret scan merge-test files = 322
    secret scan bytes = 1,491,881
    selected-rule findings = 0
    status = NO_KNOWN_PATTERN_DETECTED

Other regression counts:

    gitignore probes = 52 / 52
    publication-checker synthetic tests = 6 / 6
    branding archive tests = 9 / 9
    repository-secret tests = 37 / 37
    staged-index/hook tests = 37 / 37

These numbers are E1 evidence only.

## 5. Security review checklist

Independent reviewer should explicitly answer:

### SR-01 — workflow token

- Is top-level \`permissions: contents: read\` sufficiently restrictive?
- Is any step able to obtain write permissions indirectly?
- Are checkout credentials non-persistent?
- Are third-party actions pinned to immutable commit SHA?

### SR-02 — untrusted PR input

- Do scripts execute untrusted repository code?
- Do they interpolate paths/commit data into shell unsafely?
- Can crafted filenames/ref/manifest content cause command injection?
- Are Git configs/hooks/protocol/lazy-fetch controlled appropriately?

### SR-03 — secret leakage

- Can a detected value appear in stdout/stderr, JSON artifact or exception?
- Can a sensitive filename be printed?
- Can subprocess stderr containing secret content escape?
- Does the artifact itself reveal more than intended?

### SR-04 — filesystem / archive

- Are path traversal, symlink and special archive entries rejected?
- Are archive/file-size bounds sufficient for this repository maintenance scope?
- Can output paths overwrite/symlink-follow existing sensitive files?

### SR-05 — Git index / TOCTOU

- Is staged-index behavior correct for normal, partial and --all commits?
- Is alternate index handling fail-closed?
- Are races documented rather than falsely eliminated?
- Could a crafted index/submodule/symlink silently bypass scanning?

### SR-06 — false confidence

- Do names/statuses avoid claiming exhaustive secret safety?
- Is "mirror verified" correctly scoped to the committed manifest?
- Is missing branding archive still NOT_PUBLISHED?
- Are repository-tool tests kept distinct from application QA?

## 6. Engineering review checklist

### ER-01 — maintainability

- scripts understandable and appropriately bounded;
- duplicate test/setup code acceptable or refactor requested;
- error codes stable enough for CI use;
- function responsibilities reasonable;
- complexity appropriate.

### ER-02 — portability

- Python standard library only where intended;
- Git command features available on supported dev/CI environments;
- path/encoding handling adequate;
- Ubuntu GitHub runner use is clearly repository-CI only, not product OS support.

### ER-03 — test quality

- tests contain real negative controls;
- tests do not merely assert implementation internals;
- historical regression bugs remain covered;
- test count descriptions match actual suites;
- timeout/resource limits prevent hanging CI.

### ER-04 — workflow reliability

- concurrency cancellation appropriate;
- artifact upload with \`if: always()\` does not hide primary failure;
- missing evidence fails where expected;
- job names stable enough if later used as required checks.

## 7. DevOps review checklist

- no production secret needed;
- no deployment;
- no privileged/self-hosted runner requirement;
- no mutation of repository from workflow;
- artifacts have bounded retention;
- workflow can run on main after merge;
- check names suitable for later enforcement:
  - \`gitignore-regression\`
  - \`repository-secrets\`

Reviewer should explicitly note if different job names should become the enforcement API before merge.

## 8. Required reviewer output

An E3 reviewer should post on PR #42, bound to the exact candidate SHA:

    PR = #42
    REVIEWED SHA = c10c6296868852f15ed600025b8b31d589135be7
    REVIEWER =
    REVIEW CLASS = E3 INDEPENDENT TECHNICAL REVIEW

    SECURITY VERDICT =
    ENGINEERING VERDICT =
    DEVOPS VERDICT =

    BLOCKING FINDINGS =
    NON-BLOCKING FINDINGS =
    REQUIRED FIXES =
    TESTS RE-RUN / EVIDENCE REVIEWED =
    SCOPE NOT REVIEWED =

    FINAL VERDICT =
      APPROVE
      REQUEST CHANGES
      APPROVE WITH NON-BLOCKING RESERVATIONS

If changes are requested and the reviewed blobs change, a later E3 review must bind to the new final SHA.

## 9. Merge criteria after E3

PR #42 may move toward merge only if:

1. E3 reviewer provides a non-blocking verdict on exact final SHA;
2. any blocking findings are fixed and tests rerun;
3. E1 is green against current main;
4. changed-file scope remains understood;
5. merge is not presented as final application/security certification;
6. issue #0109 remains open until actual branch enforcement is demonstrated;
7. issue #0110 may only advance according to Audit/MASTER after E3 evidence is accepted.

## 10. Non-goals

This review does not validate:
- WCP360 backend/daemon functionality;
- API/IPC product semantics;
- Database model;
- final QA baseline;
- final Engineering v0.2 ruleset;
- absence of every secret type;
- GitHub branch protection.

Those remain separate concerns.

## 11. Current conclusion

    E1 = SATISFIED
    E2 = SATISFIED
    E3 = REQUESTED / MISSING
    PR #42 MERGE = BLOCKED ON E3
    MAIN ENFORCEMENT = NOT ACTIVE
