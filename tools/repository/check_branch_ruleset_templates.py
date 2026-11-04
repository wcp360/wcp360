#!/usr/bin/env python3
"""Fail-closed, read-only checks for not-yet-installed GitHub ruleset templates.

Checks proposal safety only; never claims live repository enforcement.
"""
from __future__ import annotations

import copy
import json
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
FILES = {
    "develop": ("wcp360-develop.disabled.json", 0),
    "main": ("wcp360-main.disabled.json", 2),
}
REQUIRED_CHECKS = ("repository-secrets", "gitignore-regression")
ALLOWED_METHODS = ("merge", "squash", "rebase")
EXPECTED_STATUS_APP_ID = 15368  # GitHub Actions, verified on exact PR check runs.
RULE_TYPES = ("deletion", "non_fast_forward", "pull_request", "required_status_checks")


def check(data: object, branch: str, review_count: int) -> None:
    def require(condition: bool, reason: str) -> None:
        if not condition:
            raise ValueError(reason)

    require(isinstance(data, dict), "ruleset must be an object")
    require(set(data) == {"name", "target", "enforcement", "bypass_actors", "conditions", "rules"}, "unexpected ruleset keys")
    require(data["name"] == f"WCP360 - {branch.capitalize()} Protection", "unexpected ruleset name")
    require(data["target"] == "branch", "not a branch ruleset")
    require(data["enforcement"] == "disabled", "template must be disabled until approved")
    require(data["bypass_actors"] == [], "unreviewed bypass actor")
    require(data["conditions"] == {
        "ref_name": {"include": [f"refs/heads/{branch}"], "exclude": []}
    }, "missing, broad, or incorrect target")
    rules = data["rules"]
    require(isinstance(rules, list) and len(rules) == len(RULE_TYPES), "unexpected rule count")
    require(all(isinstance(rule, dict) and isinstance(rule.get("type"), str) for rule in rules), "invalid rule")
    types = [rule["type"] for rule in rules]
    require(tuple(types) == RULE_TYPES, "unsafe, missing, duplicated or reordered rule")
    require(rules[0] == {"type": "deletion"} and rules[1] == {"type": "non_fast_forward"}, "invalid ref controls")
    require(set(rules[2]) == {"type", "parameters"}, "unexpected PR rule fields")
    pr = rules[2]["parameters"]
    require(isinstance(pr, dict) and set(pr) == {
        "allowed_merge_methods", "dismiss_stale_reviews_on_push",
        "require_code_owner_review", "require_last_push_approval",
        "required_approving_review_count", "required_review_thread_resolution"
    }, "unknown PR policy fields")
    require(type(pr["required_approving_review_count"]) is int, "review count must be an integer, not a boolean")
    require(all(type(pr[key]) is bool for key in (
        "dismiss_stale_reviews_on_push", "require_code_owner_review",
        "require_last_push_approval", "required_review_thread_resolution"
    )), "review policy flags must be booleans")
    require(pr == {
        "allowed_merge_methods": list(ALLOWED_METHODS),
        "dismiss_stale_reviews_on_push": True,
        "require_code_owner_review": False,
        "require_last_push_approval": review_count > 0,
        "required_approving_review_count": review_count,
        "required_review_thread_resolution": True,
    }, "review protections differ from approved candidate")
    # D-GOV-SOLO-0001 removes impossible second-person approval on develop only.
    require(set(rules[3]) == {"type", "parameters"}, "unexpected status rule fields")
    params = rules[3]["parameters"]
    require(isinstance(params, dict) and set(params) == {
        "strict_required_status_checks_policy", "do_not_enforce_on_create",
        "required_status_checks"
    }, "unexpected status check policy fields")
    require(params == {
        "strict_required_status_checks_policy": True,
        "do_not_enforce_on_create": True,
        "required_status_checks": [{"context": name, "integration_id": EXPECTED_STATUS_APP_ID} for name in REQUIRED_CHECKS],
    }, "unknown or missing required checks")


def self_test() -> None:
    negatives = 0
    for branch, (file_name, required) in FILES.items():
        data = json.loads((ROOT / "documentation/governance/rulesets" / file_name).read_text(encoding="utf-8"))
        check(data, branch, required)

        def must_reject(mutator: object, case: str) -> None:
            changed = copy.deepcopy(data)
            mutator(changed)
            try:
                check(changed, branch, required)
            except ValueError:
                return
            raise AssertionError(f"dangerous ruleset fixture admitted: {branch}/{case}")

        cases = [
            ("premature-active", lambda d: d.update(enforcement="active")),
            ("empty-target", lambda d: d["conditions"]["ref_name"].update(include=[])),
            ("wrong-target", lambda d: d["conditions"]["ref_name"].update(include=["refs/heads/features/*"])),
            ("all-targets", lambda d: d["conditions"]["ref_name"].update(include=["~ALL"])),
            ("unreviewed-bypass", lambda d: d.update(bypass_actors=[{"actor_id": 5, "actor_type": "RepositoryRole", "bypass_mode": "always"}])),
            ("restrict-updates", lambda d: d["rules"].insert(0, {"type": "update"})),
            ("unreviewed-create-lock", lambda d: d["rules"].insert(0, {"type": "creation"})),
            ("wrong-review-count", lambda d: d["rules"][2]["parameters"].update(required_approving_review_count=1)),
            ("wrong-last-push-policy", lambda d: d["rules"][2]["parameters"].update(require_last_push_approval=not (required > 0))),
            ("code-owner-self-lock", lambda d: d["rules"][2]["parameters"].update(require_code_owner_review=True)),
            ("removed-check", lambda d: d["rules"][3]["parameters"]["required_status_checks"].pop()),
            ("missing-app-id", lambda d: d["rules"][3]["parameters"]["required_status_checks"][0].pop("integration_id")),
            ("wrong-app-id", lambda d: d["rules"][3]["parameters"]["required_status_checks"][1].update(integration_id=4321)),
            ("wildcard-check", lambda d: d["rules"][3]["parameters"].update(required_status_checks=[{"context": "*"}])),
            ("non-strict-ci", lambda d: d["rules"][3]["parameters"].update(strict_required_status_checks_policy=False)),
            ("force-push-allowed", lambda d: d["rules"].pop(1)),
            ("boolean-review-count", lambda d: d["rules"][2]["parameters"].update(required_approving_review_count=False)),
            ("numeric-last-push", lambda d: d["rules"][2]["parameters"].update(require_last_push_approval=int(required > 0))),
            ("unresolved-threads", lambda d: d["rules"][2]["parameters"].update(required_review_thread_resolution=False)),
        ]
        for name, mutate in cases:
            must_reject(mutate, name)
            negatives += 1
    print(f"Ruleset templates verified: 2 disabled drafts; {negatives} hostile cases rejected; no GitHub settings changed.")


if __name__ == "__main__":
    self_test()
