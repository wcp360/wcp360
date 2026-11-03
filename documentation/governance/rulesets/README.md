# WCP360 — Repository branch-protection templates

**STATUS: PROPOSAL / DISABLED / NOT INSTALLED.** Owner-confirmed branch policy:
`features/* → develop → stable → main → production`.

This directory contains two repository-level, importable JSON templates for the **existing** branches `develop` and `main`, not an organization-wide ruleset and not an executed ruleset migration. The GitHub connector cannot apply repository ruleset settings. These files must never be confused with the live GitHub protection state.

| File | Exact target | Approval target | Required always-on checks |
| --- | --- | --- | --- |
| `wcp360-develop.disabled.json` | `refs/heads/develop` | 1 independent reviewer | `repository-secrets`, `gitignore-regression` |
| `wcp360-main.disabled.json` | `refs/heads/main` | 2 independent reviewers | same |

The files deliberately use `enforcement: disabled`. **Do not activate** until the acceptance conditions below have been met. GitHub's nonblocking `evaluate` ruleset mode may require an Enterprise plan; this repository-level proposal does not rely on its availability.

## Activation order and safeguards

1. Have Engineering/DevOps independently review and merge [PR #128](https://github.com/wcp360/wcp360/pull/128) through the authorized integration gate. Until it lands on the target branch, the guardrails workflow there does not necessarily emit the required check names. `develop` must receive the trigger change before activating `develop` protections. `main` must receive that change by the approved `develop → stable → main` promotion path before activating `main` protections.
2. Have MASTER approve the rule thresholds and name the required **distinct human reviewers** with verified repo access. `.github/CODEOWNERS` currently lists only `@yyogas`: self-approval does **not** count. `require_code_owner_review=false` intentionally avoids silently requiring the original author as an independent owner. `require_last_push_approval=true` preserves review separation.
3. Open repository [Settings → Rules → Rulesets](https://github.com/wcp360/wcp360/settings/rules). Select **New ruleset → Import a ruleset** and import one JSON file. Inspect the exact target, `disabled` state, approvals and checks before saving. Do not import as an organization-wide ruleset.
4. Confirm `repository-secrets` and `gitignore-regression` appear as successful checks on a fresh PR targeting the chosen branch, with review capability available. Then change the new ruleset to **Active**. No existing rule should lock merges unexpectedly.
5. On disposable test contributions, verify rejections for direct push, force push, deletion, zero/insufficient independent reviews, stale approvals, missing/failing checks, and acceptance of a legitimate approved PR. Record observed rule ID, actions, actor, SHA and denial evidence in [OPS-WCP-GIT-0001](https://github.com/wcp360/wcp360/issues/129). Avoid destructive tests on permanent source.
6. Recheck API/branch protection, live rulesets and actual review-enforcement evidence. Write `PROTECTED` only after these checks pass.

The templates **do not** contain `update` or `creation` restrictions or admin bypasses. They do forbid deletion and non-fast-forward updates and require a PR and status checks once active. Avoid repointing the legacy `DEFAULT` ruleset with blanket `update` to a permanent branch: it can block legitimate merges.

`stable` requires an independently selected stabilization candidate; `production` requires proof of an actually deployed commit. Do not create either merely to fill the workflow. A tag, build artifact and production deployment are distinct objects. No CODE-BASELINE change is implied.

## Read-only verification

From the repository root, run:
    python3 -I tools/repository/check_branch_ruleset_templates.py

This validates these exact disabled templates and exercises 28 negative fixtures, without installing rulesets, changing branches, calling GitHub APIs or running product code. It is included in the draft [repository guardrails](../../../.github/workflows/repository-guardrails.yml) check. A green CI is not evidence of live branch protection.

## CI branch-route validation

The existing `gitignore-regression` job now additionally checks pull-request routing under the four-stage permanent branch policy. Normal admission is `features/* → develop`, `develop → stable`, `stable → main`, and `main → production`. The sole documented pre-policy exception is **existing PR #126** (`sc-m01/ratified-handoff-878c68f → develop`), not a reusable prefix or permission for new branches. The validator exercises eight allowed and 22 forbidden routes using only the Python standard library, without GitHub write access. Direct pushes still require live ruleset enforcement; this check is not an independent review, production gate, release or CODE-BASELINE decision. Exceptional urgent production routing requires a separate MASTER/DevOps contract, not an unreviewed `features/fix-* → production` bypass.
