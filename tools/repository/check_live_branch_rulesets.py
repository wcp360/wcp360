#!/usr/bin/env python3
"""Read-only audit of proposed WCP360 GitHub branch rulesets.

Configuration comparison != negative enforcement evidence. No GitHub writes.
"""
from __future__ import annotations

import argparse
import copy
import json
import re
from collections.abc import Callable
from pathlib import Path
from urllib.error import HTTPError, URLError
from urllib.request import HTTPRedirectHandler, Request, build_opener

REPO = "wcp360/wcp360"
ROOT = Path(__file__).resolve().parents[2]
DIRECTORY = ROOT / "documentation/governance/rulesets"
BRANCHES = {"develop": "wcp360-develop.disabled.json",
            "main": "wcp360-main.disabled.json"}
API = f"https://api.github.com/repos/{REPO}/rulesets"
MAX_BYTES = 524288


def expected(branch: str) -> dict:
    return json.loads((DIRECTORY / BRANCHES[branch]).read_text(encoding="utf-8"))


def parameters_match(actual: object, required: dict, rule_type: str) -> bool:
    """Allow only reviewed GitHub readback defaults, never arbitrary extra keys."""
    if not isinstance(actual, dict):
        return False
    wanted = copy.deepcopy(required)
    if rule_type == "pull_request":
        readback_defaults = {
            "required_reviewers": [],
            "dismissal_restriction": {"enabled": False, "allowed_actors": []},
        }
        # GitHub's documented extra-review flag has no effect at zero approvals.
        # Do not normalize it for a nonzero/frozen promotion policy.
        if type(required.get("required_approving_review_count")) is int and required["required_approving_review_count"] == 0:
            readback_defaults["require_extra_approval_for_unattributed_changes"] = True
        for name, value in readback_defaults.items():
            if name in actual and name not in wanted:
                wanted[name] = value
    # Exact JSON also rejects false/0 and true/1 substitutions, nested unknown
    # keys, nonempty reviewers and altered checks. Inputs are never mutated.
    return json.dumps(actual, sort_keys=True) == json.dumps(wanted, sort_keys=True)


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
    if not isinstance(rules, list) or any(
        not isinstance(x, dict) or not isinstance(x.get("type"), str) for x in rules
    ):
        return defects + ["invalid_rules"]
    found = {x["type"]: x for x in rules}
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
        # Only the explicit readback defaults below are compatible additions.
        if not parameters_match(actual, need, name):
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
        readback_fields = {
            "required_reviewers": [],
            "dismissal_restriction": {"enabled": False, "allowed_actors": []},
        }
        if branch == "develop":
            readback_fields["require_extra_approval_for_unattributed_changes"] = True
        names = tuple(readback_fields)
        # Cover every partial/default-expanded response, not just today's full
        # GitHub payload. A provider may omit these optional fields.
        for mask in range(1, 1 << len(names)):
            expanded = copy.deepcopy(accepted)
            expanded["rules"][2]["parameters"].update({
                name: copy.deepcopy(readback_fields[name])
                for index, name in enumerate(names) if mask & (1 << index)
            })
            before = copy.deepcopy(expanded)
            if differences(expanded, model) or expanded != before:
                raise AssertionError(f"Reviewed readback altered or refused: {branch}/{mask}")
            successes += 1
        # Apply all pre-existing hostile mutations also to the expanded shape.
        accepted["rules"][2]["parameters"].update(copy.deepcopy(readback_fields))
        needs_last_push_review = model["rules"][2]["parameters"]["require_last_push_approval"]
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
            ("wrong_review_count", lambda r: r["rules"][2]["parameters"].update(required_approving_review_count=1)),
            ("wrong_last_push_policy", lambda r: r["rules"][2]["parameters"].update(require_last_push_approval=not needs_last_push_review)),
            ("no_stale_dismissal", lambda r: r["rules"][2]["parameters"].update(dismiss_stale_reviews_on_push=False)),
            ("no_checks", lambda r: r["rules"].pop(3)),
            ("missing_check", lambda r: r["rules"][3]["parameters"]["required_status_checks"].pop()),
            ("missing_app_binding", lambda r: r["rules"][3]["parameters"]["required_status_checks"][0].pop("integration_id")),
            ("wrong_app_binding", lambda r: r["rules"][3]["parameters"]["required_status_checks"][1].update(integration_id=4321)),
            ("non_strict_checks", lambda r: r["rules"][3]["parameters"].update(strict_required_status_checks_policy=False)),
            ("extra_pr_parameter", lambda r: r["rules"][2]["parameters"].update(unreviewed_extension=True)),
            ("extra_status_parameter", lambda r: r["rules"][3]["parameters"].update(unreviewed_extension=True)),
            ("boolean_review_count", lambda r: r["rules"][2]["parameters"].update(required_approving_review_count=False)),
            ("numeric_last_push", lambda r: r["rules"][2]["parameters"].update(require_last_push_approval=int(needs_last_push_review))),
            ("malformed_rule_type", lambda r: r["rules"][0].update(type=[])),
            ("required_reviewers_null", lambda r: r["rules"][2]["parameters"].update(required_reviewers=None)),
            ("required_reviewers_object", lambda r: r["rules"][2]["parameters"].update(required_reviewers={})),
            ("required_reviewers_nonempty", lambda r: r["rules"][2]["parameters"].update(required_reviewers=[{"id": 123}])),
            ("restricted_dismissal", lambda r: r["rules"][2]["parameters"]["dismissal_restriction"].update(enabled=True)),
            ("dismissal_actor", lambda r: r["rules"][2]["parameters"]["dismissal_restriction"].update(allowed_actors=[{"id": 123}])),
            ("numeric_dismissal_flag", lambda r: r["rules"][2]["parameters"]["dismissal_restriction"].update(enabled=0)),
            ("missing_dismissal_field", lambda r: r["rules"][2]["parameters"]["dismissal_restriction"].pop("allowed_actors")),
            ("extra_dismissal_field", lambda r: r["rules"][2]["parameters"]["dismissal_restriction"].update(unknown=False)),
            ("null_dismissal", lambda r: r["rules"][2]["parameters"].update(dismissal_restriction=None)),
            ("unreviewed_extra_approval_false", lambda r: r["rules"][2]["parameters"].update(require_extra_approval_for_unattributed_changes=False)),
            ("numeric_extra_approval", lambda r: r["rules"][2]["parameters"].update(require_extra_approval_for_unattributed_changes=1)),
            ("null_extra_approval", lambda r: r["rules"][2]["parameters"].update(require_extra_approval_for_unattributed_changes=None)),
        ]
        if branch == "main":
            cases.append(("unreviewed_main_extra_approval", lambda r: r["rules"][2]["parameters"].update(require_extra_approval_for_unattributed_changes=True)))
        for name, mutation in cases:
            invalid = copy.deepcopy(accepted)
            mutation(invalid)
            if not differences(invalid, model):
                raise AssertionError(f"unsafe ruleset accepted: {branch}/{name}")
            negatives += 1
    # Deterministic offline listing simulations prevent a false PASS from
    # ambiguous identities or a truncated GitHub ruleset API page.
    models = {branch: expected(branch) for branch in BRANCHES}
    entries = [
        {"name": models["develop"]["name"], "id": 101},
        {"name": models["main"]["name"], "id": 102},
    ]

    def fake_fetch(url: str, listing: list[dict] = entries) -> object:
        if url == f"{API}?per_page=100":
            return listing
        if url == f"{API}/101":
            return {**models["develop"], "enforcement": "active"}
        if url == f"{API}/102":
            return {**models["main"], "enforcement": "active"}
        raise AssertionError("Unexpected remote endpoint in offline test")

    good_report, good = live_report(fake_fetch)
    if not good or good_report["live_enforcement_verified"] is not False:
        raise AssertionError("Conforming rulesets must be configuration-only matches")

    # The currently observed partial rollout must not become a global PASS:
    # develop matches with its known readback defaults; main is still missing.
    def partial_rollout(url: str) -> object:
        if url == f"{API}?per_page=100":
            return entries[:1]
        if url == f"{API}/101":
            observed = copy.deepcopy(models["develop"])
            observed["enforcement"] = "active"
            observed["rules"][2]["parameters"].update({
                "required_reviewers": [],
                "dismissal_restriction": {"enabled": False, "allowed_actors": []},
                "require_extra_approval_for_unattributed_changes": True,
            })
            return observed
        raise AssertionError("Unexpected request for missing main protection")

    report, complete = live_report(partial_rollout)
    if (complete or report["live_enforcement_verified"] is not False
            or report["branch_results"]["develop"]["configuration"] != "MATCHES_TEMPLATE"
            or report["branch_results"]["main"]["configuration"] != "MISSING"):
        raise AssertionError("Partial protection rollout misrepresented")

    ambiguous = {
        "duplicate_named_ruleset": entries + [{"name": models["develop"]["name"], "id": 103}],
        "invalid_id_before_valid": [{"name": models["develop"]["name"], "id": "invalid"}] + entries,
        "truncated_page": entries + [{"name": "unrelated", "id": 1000 + i} for i in range(98)],
    }
    for name, rows in ambiguous.items():
        try:
            _, approved = live_report(lambda url, values=rows: fake_fetch(url, values))
        except ValueError:
            if name != "truncated_page":
                raise AssertionError(f"Unexpected listing error: {name}")
        else:
            if approved:
                raise AssertionError(f"Ambiguous or incomplete listing accepted: {name}")

    explicit = {**models["develop"]["rules"][2]["parameters"],
                "require_extra_approval_for_unattributed_changes": False}
    changed = {**explicit, "require_extra_approval_for_unattributed_changes": True}
    if (not parameters_match(explicit, explicit, "pull_request")
            or parameters_match(changed, explicit, "pull_request")):
        raise AssertionError("Explicit template fields overridden by readback defaults")

    for forbidden_url in ("https://untrusted.example.invalid/rulesets", API + "/123/other"):
        try:
            get_json(forbidden_url)
        except ValueError:
            pass
        else:
            raise AssertionError("Unapproved API URL accepted")
    if DenyRedirects().redirect_request(
        None, None, 302, "redirect", {}, "https://untrusted.example.invalid/"
    ) is not None:
        raise AssertionError("HTTP redirect permitted")

    print(f"Ruleset live-audit fixtures: {successes} active templates accepted; "
          f"{negatives} invalid variants rejected; 3 unsafe listings, 2 "
          f"unapproved URLs and redirects rejected; no API calls.")


class DenyRedirects(HTTPRedirectHandler):
    """Prevent urllib from following GitHub HTTP redirects to another host."""

    def redirect_request(self, req, fp, code, msg, headers, newurl):
        return None


def get_json(url: str) -> object:
    listing = f"{API}?per_page=100"
    if url != listing and re.fullmatch(re.escape(API) + r"/[1-9][0-9]*", url) is None:
        raise ValueError("Unapproved GitHub ruleset endpoint")
    request = Request(url, headers={
        "Accept": "application/vnd.github+json",
        "User-Agent": "wcp360-ruleset-readonly-audit",
        "X-GitHub-Api-Version": "2022-11-28",
    })
    opener = build_opener(DenyRedirects)
    with opener.open(request, timeout=10) as response:
        payload = response.read(MAX_BYTES + 1)
    if len(payload) > MAX_BYTES:
        raise ValueError("Oversized GitHub API response")
    return json.loads(payload)


def live_report(fetcher: Callable[[str], object] = get_json) -> tuple[dict, bool]:
    items = fetcher(f"{API}?per_page=100")
    # A full page or ambiguous identity is never sufficient for PASS.
    if not isinstance(items, list) or len(items) >= 100:
        raise ValueError("Unexpected or possibly paginated ruleset listing")
    names = {expected(branch)["name"]: branch for branch in BRANCHES}
    results = {
        branch: {"configuration": "MISSING", "id": None,
                 "reasons": ["no_named_ruleset"],
                 "enforcement_negative_tests": "NOT_EXECUTED"}
        for branch in BRANCHES
    }
    for name, branch in names.items():
        matching = [item for item in items
                    if isinstance(item, dict) and item.get("name") == name]
        if not matching:
            continue
        if len(matching) != 1:
            results[branch] = {
                "configuration": "UNVERIFIED", "id": None,
                "reasons": ["ambiguous_ruleset_identity"],
                "enforcement_negative_tests": "NOT_EXECUTED",
            }
            continue
        identifier = matching[0].get("id")
        if isinstance(identifier, bool) or not isinstance(identifier, int) or identifier <= 0:
            results[branch] = {
                "configuration": "UNVERIFIED", "id": None,
                "reasons": ["invalid_ruleset_identifier"],
                "enforcement_negative_tests": "NOT_EXECUTED",
            }
            continue
        live = fetcher(f"{API}/{identifier}")
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
