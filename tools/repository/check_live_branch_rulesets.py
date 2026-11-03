#!/usr/bin/env python3
"""Read-only audit of proposed WCP360 GitHub branch rulesets.

Configuration comparison != negative enforcement evidence. No GitHub writes.
"""
from __future__ import annotations

import argparse
import copy
import json
from pathlib import Path
from urllib.error import HTTPError, URLError
from urllib.request import Request, urlopen

REPO = "wcp360/wcp360"
ROOT = Path(__file__).resolve().parents[2]
DIRECTORY = ROOT / "documentation/governance/rulesets"
BRANCHES = {"develop": "wcp360-develop.disabled.json",
            "main": "wcp360-main.disabled.json"}
API = f"https://api.github.com/repos/{REPO}/rulesets"
MAX_BYTES = 524288


def expected(branch: str) -> dict:
    return json.loads((DIRECTORY / BRANCHES[branch]).read_text(encoding="utf-8"))


def differences(live: object, template: dict) -> list[str]:
    """Conservative template conformance check; never asserts enforcement."""
    if not isinstance(live, dict):
        return ["malformed_ruleset"]
    defects = []
    for name in ("name", "target", "conditions", "bypass_actors"):
        if live.get(name) != template[name]:
            defects.append(f"{name}_mismatch")
    if live.get("enforcement") != "active":
        defects.append("enforcement_not_active")
    rules = live.get("rules")
    if not isinstance(rules, list) or any(not isinstance(x, dict) for x in rules):
        return defects + ["invalid_rules"]
    found = {x.get("type"): x for x in rules}
    wanted = {x["type"]: x for x in template["rules"]}
    if len(found) != len(rules) or set(found) != set(wanted):
        defects.append("rules_mismatch")
    for name in ("deletion", "non_fast_forward"):
        if name not in found:
            defects.append(f"missing_{name}")
    for name in ("pull_request", "required_status_checks"):
        if name not in found:
            defects.append(f"missing_{name}")
            continue
        actual = found[name].get("parameters")
        need = wanted[name]["parameters"]
        if not isinstance(actual, dict) or any(actual.get(k) != value for k, value in need.items()):
            defects.append(f"{name}_parameters_mismatch")
    return sorted(set(defects))


def self_test() -> None:
    successes = 0
    negatives = 0
    for branch in BRANCHES:
        model = expected(branch)
        accepted = copy.deepcopy(model)
        accepted["enforcement"] = "active"
        assert differences(accepted, model) == [], branch
        successes += 1
        cases = [
            ("disabled", lambda r: r.update(enforcement="disabled")),
            ("evaluate", lambda r: r.update(enforcement="evaluate")),
            ("wrong_name", lambda r: r.update(name="unrelated")),
            ("wrong_target", lambda r: r.update(target="tag")),
            ("empty_include", lambda r: r["conditions"]["ref_name"].update(include=[])),
            ("wrong_branch", lambda r: r["conditions"]["ref_name"].update(include=["refs/heads/other"])),
            ("bypass", lambda r: r["bypass_actors"].append({"actor_id": 5})),
            ("unsafe_update", lambda r: r["rules"].append({"type": "update"})),
            ("duplicate_rule", lambda r: r["rules"].append({"type": "deletion"})),
            ("no_deletion", lambda r: r["rules"].pop(0)),
            ("no_pr", lambda r: r["rules"].pop(2)),
            ("review_count_zero", lambda r: r["rules"][2]["parameters"].update(required_approving_review_count=0)),
            ("no_latest_push_review", lambda r: r["rules"][2]["parameters"].update(require_last_push_approval=False)),
            ("no_stale_dismissal", lambda r: r["rules"][2]["parameters"].update(dismiss_stale_reviews_on_push=False)),
            ("no_checks", lambda r: r["rules"].pop(3)),
            ("missing_check", lambda r: r["rules"][3]["parameters"]["required_status_checks"].pop()),
            ("non_strict_checks", lambda r: r["rules"][3]["parameters"].update(strict_required_status_checks_policy=False)),
        ]
        for name, mutation in cases:
            invalid = copy.deepcopy(accepted)
            mutation(invalid)
            if not differences(invalid, model):
                raise AssertionError(f"unsafe ruleset accepted: {branch}/{name}")
            negatives += 1
    print(f"Ruleset live-audit fixtures: {successes} active templates accepted; "
          f"{negatives} invalid variants rejected; no API calls.")


def get_json(url: str) -> object:
    # Only fixed GitHub REST URLs are formed. No caller-supplied host or redirects.
    request = Request(url, headers={
        "Accept": "application/vnd.github+json",
        "User-Agent": "wcp360-ruleset-readonly-audit",
        "X-GitHub-Api-Version": "2022-11-28",
    })
    with urlopen(request, timeout=10) as response:
        payload = response.read(MAX_BYTES + 1)
    if len(payload) > MAX_BYTES:
        raise ValueError("Oversized GitHub API response")
    return json.loads(payload)


def live_report() -> tuple[dict, bool]:
    items = get_json(API)
    if not isinstance(items, list) or len(items) > 75:
        raise ValueError("Unexpected ruleset listing")
    names = {expected(branch)["name"]: branch for branch in BRANCHES}
    results = {branch: {"configuration": "MISSING", "id": None,
                        "reasons": ["no_named_ruleset"],
                        "enforcement_negative_tests": "NOT_EXECUTED"}
               for branch in BRANCHES}
    seen = set()
    for item in items:
        if not isinstance(item, dict) or item.get("name") not in names:
            continue
        branch = names[item["name"]]
        identifier = item.get("id")
        if not isinstance(identifier, int) or identifier <= 0 or branch in seen:
            results[branch] = {"configuration": "UNVERIFIED", "id": identifier,
                               "reasons": ["ambiguous_ruleset_identity"],
                               "enforcement_negative_tests": "NOT_EXECUTED"}
            continue
        seen.add(branch)
        live = get_json(f"{API}/{identifier}")
        defects = differences(live, expected(branch))
        results[branch] = {
            "configuration": "MATCHES_TEMPLATE" if not defects else "NONCONFORMING",
            "id": identifier,
            "reasons": defects,
            "enforcement_negative_tests": "NOT_EXECUTED",
        }
    ok = all(r["configuration"] == "MATCHES_TEMPLATE" for r in results.values())
    return {"repository": REPO, "observation": "READ_ONLY",
            "branch_results": results,
            "configuration_matches_required_templates": ok,
            "live_enforcement_verified": False}, ok


def main() -> None:
    parser = argparse.ArgumentParser()
    mode = parser.add_mutually_exclusive_group(required=True)
    mode.add_argument("--self-test", action="store_true")
    mode.add_argument("--live", action="store_true")
    parser.add_argument("--report-only", action="store_true",
                        help="Show absent rules as an observation rather than a failing exit status")
    args = parser.parse_args()
    if args.self_test:
        if args.report_only:
            parser.error("--report-only requires --live")
        self_test()
        return
    try:
        report, matched = live_report()
    except (HTTPError, URLError, OSError, ValueError, json.JSONDecodeError):
        # Error output is sanitized: no tokens, raw GitHub responses or operator URLs.
        report, matched = {
            "repository": REPO, "observation": "UNVERIFIED",
            "reason": "github_api_read_unavailable",
            "configuration_matches_required_templates": False,
            "live_enforcement_verified": False,
        }, False
    print(json.dumps(report, sort_keys=True, indent=2))
    if not matched and not args.report_only:
        raise SystemExit(2)


if __name__ == "__main__":
    main()
