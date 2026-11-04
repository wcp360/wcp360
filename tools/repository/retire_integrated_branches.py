#!/usr/bin/env python3
"""Retire only the three owner-requested refs after verified integration.

The write path is a short-lived, repository-scoped maintenance operation. It
never creates a remote ref, modifies main/develop, or overrides GitHub rules.
"""
from __future__ import annotations

import argparse
import base64
import copy
import datetime as dt
import json
import os
import re
import subprocess
import tempfile
from pathlib import Path
from urllib.error import HTTPError
from urllib.parse import quote
from urllib.request import HTTPRedirectHandler, Request, build_opener

REPO = "wcp360/wcp360"
ORIGIN = "https://github.com/wcp360/wcp360.git"
API = f"https://api.github.com/repos/{REPO}/"
TARGET = "develop"
AUTHORIZED = {
    "features/branch-protection-readiness": 128,
    "docs/core-documentation-2026-11-03": 133,
    "features/core-http-security-headers": 127,
}
EXPIRES = dt.datetime(2026, 11, 6, 0, 0, tzinfo=dt.timezone.utc)
SHA = re.compile(r"[0-9a-f]{40}\Z")


def valid_sha(value: object) -> str:
    if not isinstance(value, str) or SHA.fullmatch(value) is None:
        raise ValueError("Invalid commit identity")
    return value


def qualified_head(branch: str, pr: object) -> str:
    if branch not in AUTHORIZED or not isinstance(pr, dict):
        raise ValueError("Not an owner-requested branch")
    if (type(pr.get("number")) is not int or pr["number"] != AUTHORIZED[branch]
            or pr.get("merged") is not True or pr.get("state") != "closed"):
        raise ValueError("Exact PR is not merged")
    for side, ref in (("head", branch), ("base", TARGET)):
        item = pr.get(side)
        if (not isinstance(item, dict) or item.get("ref") != ref
                or not isinstance(item.get("repo"), dict)
                or item["repo"].get("full_name") != REPO):
            raise ValueError("Unexpected PR repository or route")
    valid_sha(pr.get("merge_commit_sha"))
    return valid_sha(pr["head"].get("sha"))


def deletion_args(branch: str, expected: str) -> list[str]:
    if branch not in AUTHORIZED:
        raise ValueError("Deletion outside the exact allowlist")
    expected = valid_sha(expected)
    ref = f"refs/heads/{branch}"
    # The explicit lease makes deletion conditional on the same current HEAD.
    # It does not rewrite history or permit deletion of a permanent branch.
    return ["push", "--porcelain", f"--force-with-lease={ref}:{expected}",
            "origin", f":{ref}"]


def has_dependency(branch: str, prs: object) -> bool:
    if not isinstance(prs, list) or len(prs) >= 100:
        raise ValueError("Incomplete open-PR inventory")
    for pr in prs:
        if not isinstance(pr, dict):
            raise ValueError("Malformed open PR")
        for side in ("head", "base"):
            item = pr.get(side)
            if not isinstance(item, dict):
                raise ValueError("Malformed open PR side")
            if item.get("ref") == branch:
                return True
    return False


class NoRedirect(HTTPRedirectHandler):
    def redirect_request(self, req, fp, code, msg, headers, newurl):
        return None


def api_get(path: str, token: str, missing_ok: bool = False) -> object:
    allowed = {"pulls?state=open&per_page=100", "git/ref/heads/develop"}
    allowed.update(f"pulls/{number}" for number in AUTHORIZED.values())
    allowed.update("git/ref/heads/" + quote(branch, safe="") for branch in AUTHORIZED)
    if path not in allowed:
        raise ValueError("Unapproved API path")
    request = Request(API + path, headers={
        "Authorization": f"Bearer {token}", "Accept": "application/vnd.github+json",
        "X-GitHub-Api-Version": "2022-11-28", "User-Agent": "wcp360-bounded-retirement",
    })
    try:
        with build_opener(NoRedirect).open(request, timeout=20) as response:
            if response.headers.get("Link"):
                raise ValueError("Paginated API response is not complete")
            data = response.read(1_048_577)
    except HTTPError as error:
        if error.code == 404 and missing_ok:
            return None
        raise
    if len(data) > 1_048_576:
        raise ValueError("Oversized API response")
    return json.loads(data)


def ref_sha(value: object) -> str:
    if not isinstance(value, dict) or not isinstance(value.get("object"), dict):
        raise ValueError("Malformed ref response")
    if value["object"].get("type") != "commit":
        raise ValueError("Ref is not a commit")
    return valid_sha(value["object"].get("sha"))


def git(directory: str, env: dict[str, str], args: list[str]) -> subprocess.CompletedProcess:
    return subprocess.run(["git", "-C", directory, *args], env=env,
                          capture_output=True, text=True, timeout=90, check=False)


def apply() -> dict:
    if (os.environ.get("GITHUB_REPOSITORY") != REPO
            or os.environ.get("GITHUB_EVENT_NAME") != "push"
            or os.environ.get("GITHUB_REF") != "refs/heads/develop"
            or os.environ.get("GITHUB_ACTIONS") != "true"):
        raise ValueError("Write operation requires the trusted develop push workflow")
    if dt.datetime.now(dt.timezone.utc) >= EXPIRES:
        return {"status": "EXPIRED_NO_ACTION", "branches": []}
    token = os.environ.get("WCP360_RETIRE_TOKEN", "")
    if not token:
        raise ValueError("Missing repository-scoped workflow token")
    results = []
    for branch, number in AUTHORIZED.items():
        row = {"branch": branch, "pr": number, "status": "NOT_EXECUTED"}
        results.append(row)
        ref_path = "git/ref/heads/" + quote(branch, safe="")
        current = api_get(ref_path, token, missing_ok=True)
        if current is None:
            row["status"] = "ALREADY_ABSENT"
            continue
        pr = api_get(f"pulls/{number}", token)
        try:
            head = qualified_head(branch, pr)
        except ValueError:
            row["status"] = "NOT_MERGED_OR_ROUTE_MISMATCH"
            continue
        if ref_sha(current) != head:
            row["status"] = "NEW_HEAD_PRESERVED"
            continue
        if has_dependency(branch, api_get("pulls?state=open&per_page=100", token)):
            row["status"] = "OPEN_PR_DEPENDENCY_PRESERVED"
            continue
        target = ref_sha(api_get("git/ref/heads/develop", token))
        row.update(expected_head=head, observed_develop=target)
        with tempfile.TemporaryDirectory(prefix="wcp360-retire-") as directory:
            auth = base64.b64encode(("x-access-token:" + token).encode()).decode()
            env = {"PATH": os.environ.get("PATH", "/usr/bin:/bin"), "HOME": directory,
                   "GIT_TERMINAL_PROMPT": "0", "GIT_CONFIG_NOSYSTEM": "1",
                   "GIT_CONFIG_GLOBAL": "/dev/null", "GIT_CONFIG_COUNT": "4",
                   "GIT_CONFIG_KEY_0": "http.https://github.com/.extraheader",
                   "GIT_CONFIG_VALUE_0": "AUTHORIZATION: basic " + auth,
                   "GIT_CONFIG_KEY_1": "http.followRedirects", "GIT_CONFIG_VALUE_1": "false",
                   "GIT_CONFIG_KEY_2": "credential.helper", "GIT_CONFIG_VALUE_2": "",
                   "GIT_CONFIG_KEY_3": "core.hooksPath", "GIT_CONFIG_VALUE_3": "/dev/null"}
            commands = [["init", "--bare", "--quiet"], ["remote", "add", "origin", ORIGIN],
                        ["fetch", "--quiet", "--no-tags", "--filter=blob:none", "origin", target, head]]
            if any(git(directory, env, command).returncode for command in commands):
                raise RuntimeError("Trusted Git preparation failed")
            if git(directory, env, ["merge-base", "--is-ancestor", head, target]).returncode:
                row["status"] = "UNMERGED_COMMITS_PRESERVED"
                continue
            result = git(directory, env, deletion_args(branch, head))
            if result.returncode:
                row["status"] = "LEASE_OR_SERVER_POLICY_REFUSED"
                continue
        # No token, raw remote output, or credential-bearing Git config is logged.
        row["status"] = ("DELETED" if api_get(ref_path, token, missing_ok=True) is None
                         else "REF_STILL_PRESENT_RECHECK_REQUIRED")
    return {"status": "CHECKED", "repository": REPO, "branches": results,
            "permanent_branch_writes": False, "rulesets_changed": False}


def self_test() -> None:
    positive = 0
    negative = 0
    for branch, number in AUTHORIZED.items():
        specimen = {"number": number, "state": "closed", "merged": True,
                    "merge_commit_sha": "b" * 40,
                    "head": {"ref": branch, "sha": "a" * 40, "repo": {"full_name": REPO}},
                    "base": {"ref": TARGET, "repo": {"full_name": REPO}}}
        assert qualified_head(branch, specimen) == "a" * 40
        assert deletion_args(branch, "a" * 40)[-1] == f":refs/heads/{branch}"
        positive += 1
        mutations = [lambda p: p.update(merged=False), lambda p: p.update(merged=1),
                     lambda p: p.update(state="open"), lambda p: p.update(number=999),
                     lambda p: p["base"].update(ref="main"),
                     lambda p: p["head"].update(ref="features/other"),
                     lambda p: p["head"]["repo"].update(full_name="fork/wcp360"),
                     lambda p: p["head"].update(sha="a" * 39),
                     lambda p: p.update(merge_commit_sha=None)]
        for mutate in mutations:
            bad = copy.deepcopy(specimen)
            mutate(bad)
            try:
                qualified_head(branch, bad)
            except ValueError:
                negative += 1
            else:
                raise AssertionError("Unsafe PR accepted")
        assert has_dependency(branch, [{"head": {"ref": branch}, "base": {"ref": TARGET}}])
        assert has_dependency(branch, [{"head": {"ref": "other"}, "base": {"ref": branch}}])
        assert not has_dependency(branch, [])
    for branch in ("main", "develop", "stable", "production", "features/other", ""):
        try:
            deletion_args(branch, "a" * 40)
        except ValueError:
            negative += 1
        else:
            raise AssertionError("Permanent/unrequested ref admitted")
    try:
        has_dependency(next(iter(AUTHORIZED)), [{}] * 100)
    except ValueError:
        negative += 1
    else:
        raise AssertionError("Incomplete inventory accepted")
    assert NoRedirect().redirect_request(None, None, 302, "redirect", {}, "https://example.invalid") is None
    # Actual Git lease behavior, confined to a temporary local bare repository.
    with tempfile.TemporaryDirectory(prefix="wcp360-lease-test-") as directory:
        remote = str(Path(directory) / "remote.git")
        work = str(Path(directory) / "work")
        Path(remote).mkdir()
        Path(work).mkdir()
        env = {**os.environ, "GIT_CONFIG_NOSYSTEM": "1", "GIT_CONFIG_GLOBAL": "/dev/null",
               "GIT_AUTHOR_NAME": "Fixture", "GIT_AUTHOR_EMAIL": "fixture@example.invalid",
               "GIT_COMMITTER_NAME": "Fixture", "GIT_COMMITTER_EMAIL": "fixture@example.invalid"}
        assert git(remote, env, ["init", "--bare", "--quiet"]).returncode == 0
        assert git(work, env, ["init", "--quiet"]).returncode == 0
        assert git(work, env, ["remote", "add", "origin", remote]).returncode == 0
        assert git(work, env, ["commit", "--quiet", "--allow-empty", "-m", "first"]).returncode == 0
        first = git(work, env, ["rev-parse", "HEAD"]).stdout.strip()
        branch = next(iter(AUTHORIZED))
        assert git(work, env, ["push", "origin", f"HEAD:refs/heads/{branch}"]).returncode == 0
        assert git(work, env, ["commit", "--quiet", "--allow-empty", "-m", "second"]).returncode == 0
        second = git(work, env, ["rev-parse", "HEAD"]).stdout.strip()
        assert git(work, env, ["push", "origin", f"HEAD:refs/heads/{branch}"]).returncode == 0
        assert git(work, env, deletion_args(branch, first)).returncode != 0
        assert git(remote, env, ["rev-parse", f"refs/heads/{branch}"]).stdout.strip() == second
        assert git(work, env, deletion_args(branch, second)).returncode == 0
        assert git(remote, env, ["show-ref", "--verify", f"refs/heads/{branch}"]).returncode != 0
    print(f"Retirement safety: {positive} allowed PRs; {negative} refusals; dependency/redirect and real local Git lease race PASS; no GitHub writes.")


def main() -> None:
    parser = argparse.ArgumentParser()
    group = parser.add_mutually_exclusive_group(required=True)
    group.add_argument("--self-test", action="store_true")
    group.add_argument("--apply", action="store_true")
    args = parser.parse_args()
    if args.self_test:
        self_test()
        return
    try:
        report = apply()
    except Exception as error:
        # HTTP and subprocess messages may include request/credential material.
        print(json.dumps({"status": "BLOCKED", "error_class": type(error).__name__,
                          "action": "STOP_AND_INSPECT_WITHOUT_ASSUMING_DELETION"}))
        raise SystemExit(2) from None
    print(json.dumps(report, indent=2, sort_keys=True))


if __name__ == "__main__":
    main()
