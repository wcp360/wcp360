# WCP360 — Repository branch-protection templates

**STATUS: VERSIONED TEMPLATES DISABLED; DEVELOP ACTIVATION OBSERVED 2026-11-04.**

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
refused branch routes, and 12 conforming / 73 rejected live-configuration variants,
plus unsafe listings, URLs, redirects, partial rollout and explicit-field
precedence. They do not call GitHub or change refs.

For configuration-only readback:

```sh
python3 -I tools/repository/check_live_branch_rulesets.py --live
```

`--live --report-only` reports uncertainty without a failing shell status.
`MATCHES_TEMPLATE` means configuration agreement only. An unavailable API is
UNVERIFIED. The observer rejects redirects, unknown destinations, oversized
responses, ambiguous identities, incomplete pagination and unreviewed policy
fields. Boolean values cannot stand in for numeric review counts.

## Reviewed GitHub readback fields — November 4, 2026

[Live readback in #129](https://github.com/wcp360/wcp360/issues/129#issuecomment-6631293983)
observed active ruleset `26531327`, exact target `refs/heads/develop`, and
`develop.protected=true`. Main was still unprotected. This dated observation is
not a promise that settings cannot change and not a live refusal-test result.

GitHub adds optional PR parameters absent from the original import. The observer
accepts only these explicitly reviewed values when not already defined by the
versioned template:

- `required_reviewers: []`;
- `dismissal_restriction: {"enabled": false, "allowed_actors": []}`;
- `require_extra_approval_for_unattributed_changes: true`, **only** when the
  template requires integer zero approving reviews.

The [official GitHub rules reference](https://docs.github.com/en/rest/repos/rules)
defines the extra-review flag as applying to unattributed Copilot PRs only when a
nonzero approval count is required. It therefore adds no reviewer to develop.
For main's nonzero frozen threshold, this field is deliberately not normalized;
any such readback requires a separate explicit policy decision. The templates
and live Settings are not modified by this compatibility fix.

Every other parameter remains an exact, type-sensitive JSON comparison.
Nonempty reviewers, restricted dismissal, unexpected nested fields, wrong
booleans/counts, modified checks and arbitrary new fields remain nonconforming.
An explicitly specified template field always wins over compatibility defaults.
The partial-rollout fixture verifies that matching develop while main is missing
never produces overall conformance or `live_enforcement_verified=true`.

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
