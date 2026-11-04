# WCP360 — Repository branch-protection templates

**STATUS: VERSIONED CONFIGURATION CANDIDATE / DISABLED / NOT INSTALLED.**

The target workflow remains `features/* → develop → stable → main → production`.
The owner prohibits creating any new branch. Existing contributions are processed
through existing branches and PRs, without direct permanent-branch pushes.

## Single-maintainer development policy

[D-GOV-SOLO-0001](../decisions/D-GOV-SOLO-0001.md) records the owner's decision.
The owner's alternate accounts are not independent reviewers. Neither an approval
from an alias nor assistant self-review may be presented as independent E3.

| File | Exact target | Review threshold | Required checks |
| --- | --- | --- | --- |
| `wcp360-develop.disabled.json` | `refs/heads/develop` | 0 other-person approvals; PR and substantive source-bound assessment still required | `repository-secrets`, `gitignore-regression` |
| `wcp360-main.disabled.json` | `refs/heads/main` | 2 independent approvals; unchanged frozen promotion policy | same |

On `develop`, `require_last_push_approval` is false so that the latest author push
does not recreate an impossible second-person requirement. Required conversation
resolution, strict checks, stale-review dismissal, no bypass actors, no force-push
and no deletion remain. This does not waive failed tests, findings, contracts or
MASTER's exact-source integration decision.

The owner has not approved a release-policy relaxation. The main template is
therefore unchanged: this is a protected/frozen promotion target, not a claim
that aliases can satisfy its independent-review policy. No `main` promotion,
`stable`/`production` creation, deployment or CODE-BASELINE is authorized here.

## Administrative activation is a separate operation

The files deliberately remain `enforcement: disabled`. Importing these files or
passing their offline tests is not evidence of live protection. The connected
GitHub actions expose content/PR changes but not ruleset administration or branch
deletion. The user's own repository Admin role does not make an unsupported
connector operation available.

1. Review and integrate #128 under the recorded single-maintainer controls. The
   integration bootstraps the always-on checks on `develop`; it does not activate
   GitHub Settings.
2. A settings-authorized administrator must inspect the existing DEFAULT ruleset,
   exact target, bypass actors and checks. Do not silently remove an existing rule.
   Do not repoint blanket `update`/`creation` restrictions onto permanent branches.
3. In repository Settings → Rules → Rulesets, import each reviewed JSON, inspect
   its exact branch and disabled state, and activate it only after the actual
   required check names/app are available on that target. Check existing `main`
   workflow availability directly rather than creating a promotion branch.
4. Confirm required checks originate from GitHub Actions (`integration_id=15368`).
   This constrains status provenance, not the integrity of workflow source.
5. Record ruleset IDs, configuration readback, recovery operator and evidence in
   #129. Configuration conformance and real denial evidence are separate fields.
   Never run force-push/deletion probes on an unprotected permanent branch. Never
   create a disposable branch against the owner's instruction. Mark unperformed
   tests BLOCKED/NOT EXECUTED rather than PASS.

Use the existing PR merge controls and non-mutating observations for safe initial
checks. A legitimate authorized merge is separate positive evidence; it does not
prove every refusal scenario. The main promotion policy remains frozen pending a
separate release decision.

## Read-only tests and audit

Run from the repository root:

```sh
python3 -I tools/repository/check_branch_ruleset_templates.py
python3 -I tools/repository/check_branch_routing.py --self-test
python3 -I tools/repository/check_live_branch_rulesets.py --self-test
```

The current fixtures exercise 38 rejected template variants, 9 admitted / 29
refused branch routes, and 2 conforming / 48 rejected live-configuration variants,
plus unsafe listings, URLs and redirects. They do not call GitHub or change refs.

For configuration-only readback:

```sh
python3 -I tools/repository/check_live_branch_rulesets.py --live
```

`--live --report-only` reports uncertainty without a failing shell status.
`MATCHES_TEMPLATE` means configuration agreement only. An unavailable API is
UNVERIFIED. The observer rejects redirects, unknown destinations, oversized
responses, ambiguous identities, incomplete pagination and extra policy fields.
Boolean values cannot stand in for numeric review counts.

## Existing-branch routing exceptions and retirement

Normal routes remain `features/* → develop`, `develop → stable`, `stable → main`
and `main → production`. Two narrowly enumerated exceptions preserve already
existing work without creating replacement branches:

- PR #126 only: `sc-m01/ratified-handoff-878c68f → develop`.
- PR #133 only: `docs/core-documentation-2026-11-03 → develop`.

Neither exception grants a reusable branch family or permission to create refs.
Wrong PR numbers, similar branch names and promotion targets are rejected.

The owner requested retirement of the existing documentation branch and the
`features/branch-protection-readiness` / `features/core-http-security-headers`
branches. Before deleting any, verify the PR is merged, its exact latest HEAD is
preserved in the target ancestry, and no new commits or dependent open PRs would
be lost. A closed PR and a deleted branch are different states. No deletion is
claimed until GitHub confirms the ref is absent. No force push is a cleanup tool.
