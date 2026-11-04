#!/usr/bin/env python3
"""Read-only admission of approved WCP360 pull-request branch routes.

This does not replace live GitHub rulesets, source review, or release decisions.
"""
from __future__ import annotations

import argparse
import re

PERMANENT = ("develop", "stable", "main", "production")
FEATURE = re.compile(r"features/[A-Za-z0-9][A-Za-z0-9._-]*\Z")
LEGACY_NUMBER = 126
LEGACY_BRANCH = "sc-m01/ratified-handoff-878c68f"
TECHNICAL_NUMBER = 134  # D-TECH-RESOLUTION-0001; existing work ref only.
DOC_NUMBER = 133
DOC_BRANCH = "docs/core-documentation-2026-11-03"


def require_route(base: str, head: str, number: int) -> str:
    if not isinstance(base, str) or not isinstance(head, str):
        raise ValueError("PR branch references must be strings")
    if not isinstance(number, int) or isinstance(number, bool) or number <= 0:
        raise ValueError("A positive pull-request number is required")
    if base not in PERMANENT:
        raise ValueError("Unknown PR target")
    if base == "develop":
        if FEATURE.fullmatch(head):
            return "feature-to-develop"
        if number == LEGACY_NUMBER and head == LEGACY_BRANCH:
            return "grandfathered-pr-126-to-develop"
        if number == TECHNICAL_NUMBER and head == LEGACY_BRANCH:
            return "existing-work-ref-pr-134-to-develop"
        if number == DOC_NUMBER and head == DOC_BRANCH:
            return "existing-documentation-pr-133-to-develop"
        raise ValueError("develop accepts features/* except the exact existing-ref PRs #126, #133 and #134")
    previous = {"stable": "develop", "main": "stable", "production": "main"}[base]
    if head != previous:
        raise ValueError(f"{base} only accepts pull requests from {previous}")
    return f"{head}-to-{base}"


def self_test() -> None:
    admitted = [
        ("develop", "features/core-http-security-headers", 127),
        ("develop", "features/branch-protection-readiness", 128),
        ("develop", "features/fix-file-manager-upload", 8),
        ("develop", "features/plugin-backup", 123),
        ("develop", LEGACY_BRANCH, LEGACY_NUMBER),
        ("develop", DOC_BRANCH, DOC_NUMBER),
        ("develop", LEGACY_BRANCH, TECHNICAL_NUMBER),
        ("stable", "develop", 140),
        ("main", "stable", 141),
        ("production", "main", 142),
    ]
    forbidden = [
        ("develop", "main", 100),
        ("develop", "stable", 101),
        ("develop", "features/", 102),
        ("develop", "features/nested/path", 103),
        ("develop", "features/.hidden", 104),
        ("develop", "features/invalid name", 105),
        ("develop", LEGACY_BRANCH, 127),
        ("main", LEGACY_BRANCH, LEGACY_NUMBER),
        ("stable", LEGACY_BRANCH, LEGACY_NUMBER),
        ("develop", "sc-m01/other", LEGACY_NUMBER),
        ("stable", "features/core", 111),
        ("stable", "main", 112),
        ("main", "features/fix-emergency", 113),
        ("main", "develop", 114),
        ("production", "features/fix-emergency", 115),
        ("production", "stable", 116),
        ("production", "develop", 117),
        ("testing", "features/x", 118),
        ("", "features/x", 119),
        ("develop", "", 120),
        ("develop", "features/x", 0),
        ("develop", "features/x", -1),
        ("develop", DOC_BRANCH, DOC_NUMBER + 1),
        ("develop", "docs/other", DOC_NUMBER),
        ("develop", DOC_BRANCH + "-other", DOC_NUMBER),
        ("stable", DOC_BRANCH, DOC_NUMBER),
        ("main", DOC_BRANCH, DOC_NUMBER),
        ("production", DOC_BRANCH, DOC_NUMBER),
        ("develop", DOC_BRANCH, True),
        ("develop", LEGACY_BRANCH, TECHNICAL_NUMBER + 1),
        ("develop", LEGACY_BRANCH + "-other", TECHNICAL_NUMBER),
        ("develop", "sc-m01/other", TECHNICAL_NUMBER),
        ("stable", LEGACY_BRANCH, TECHNICAL_NUMBER),
        ("main", LEGACY_BRANCH, TECHNICAL_NUMBER),
        ("production", LEGACY_BRANCH, TECHNICAL_NUMBER),
    ]
    for base, head, number in admitted:
        require_route(base, head, number)
    for base, head, number in forbidden:
        try:
            require_route(base, head, number)
        except ValueError:
            continue
        raise AssertionError(f"Disallowed PR route accepted: {base}/{head}/{number}")
    print(f"Branch route tests: {len(admitted)} allowed, {len(forbidden)} forbidden; PASS.")


def main() -> None:
    parser = argparse.ArgumentParser()
    parser.add_argument("--self-test", action="store_true")
    parser.add_argument("--base")
    parser.add_argument("--head")
    parser.add_argument("--number", type=int)
    args = parser.parse_args()
    if args.self_test:
        self_test()
    else:
        if args.base is None or args.head is None or args.number is None:
            parser.error("A PR requires --base, --head and --number")
        print("PR branch route accepted:", require_route(args.base, args.head, args.number))


if __name__ == "__main__":
    main()
