#!/usr/bin/env python3
"""Check a source-bound MFA review ledger; never authenticate or authorize users.

A successful result establishes traceability only. It cannot ratify an owner
contract, close the listed runtime gaps, or substitute for application tests.
"""
from __future__ import annotations

import argparse
import hashlib
import json
import re
import sys
from pathlib import Path
from typing import Any

MATRIX_PATH = "documentation/specifications/security/MFA-ALIGNMENT-COVERAGE.json"
SOURCE_COMMIT = "bcc6f7ab4cb6a36b377ae40e3bb9258506396be9"
SOURCE_PINS = {
    "security": ("documentation/specifications/security/WCP360_Security_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md", "6d74f2f363524511424f839ca7262143c2af95ab"),
    "api": ("documentation/specifications/api/WCP360_API_Contract_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md", "4893c5191b4928ecfa5a70f4dc14b39a8939fcea"),
    "database": ("documentation/specifications/database/WCP360_DB_Model_v1.0_Canonical_Implementation_Contract.md", "2660e057bad1d00592c57f354b1f3745ccccbe7d"),
    "session_boundary": ("WCP360_Core/src/Shared/Security/SessionBoundary.php", "9e9e626cdf2314459fde225aad2fde77c4c88677"),
    "sessions": ("WCP360_Core/src/Module/IdentityAccess/Application/CandidateSessions.php", "1e8f5be5941a5b81b70270ea3b1e23c98144b6f1"),
    "factor": ("WCP360_Core/src/Module/IdentityAccess/Application/CandidateMfa.php", "144dedb4bd7b2beaf7767071769754974514c650"),
    "factor_guard": ("WCP360_Core/src/Module/IdentityAccess/Infrastructure/MfaSessionGuard.php", "b333d24a1d9d7069040b9903226a672998eb1e12"),
    "recovery_renewal": ("WCP360_Core/src/Module/IdentityAccess/Application/RegenerateMfaRecoveryCodes.php", "13c652ee850d7422cc39caf10cb9c9d411e0a1e5"),
}
POLICY = {
    "user_enrollment_required": False,
    "admin_enrollment_required": False,
    "enabled_factor_enforced": True,
    "password_only_is_mfa": False,
    "proof_from_request_allowed": False,
    "ordinary_login_grants_privileged_context": False,
}
API_PROFILES = {"API-WCP-0706": "AUTH-P", "API-WCP-0708": "AUTH-E"}
API_PROFILES.update({"API-WCP-" + value: "AUTH-PRIV" for value in (
    "0707", "0802", "0803", "0002", "0805", "0807", "0809", "0810",
    "0812", "0003", "0007", "0814", "0816")})
TEST_PREFIX = "WCP360_Core/tests/"
QA_SOURCES = {
    1: ["Unit/OptionalLoginMfaPolicyTest.php", "Integration/MfaJourneyTest.php"],
    2: ["Integration/CandidateMfaTest.php", "Integration/MfaJourneyTest.php"],
    3: ["Integration/MfaJourneyTest.php", "Integration/CandidateSessionsTest.php"],
    4: ["Integration/MfaRecoveryRenewalTest.php", "Integration/ChangeOwnPasswordTest.php"],
    5: ["Integration/CandidateSessionsTest.php", "Integration/MfaJourneyTest.php"],
    6: ["Integration/CandidateAuthorizationTest.php"],
    7: ["Unit/OptionalLoginMfaPolicyTest.php", "Unit/SessionTimestampConsistencyTest.php"],
}
GAPS = {
    "canonical-policy-version", "atomic-factor-session-upgrade", "user-mfa-journey",
    "isolated-database-equivalence", "blocked-runtime-publication", "full-stack-qualification",
}
DATA = {
    "user_sessions": "wcp_iam.user_sessions",
    "admin_sessions": "wcp_iam.admin_sessions",
    "candidate_strengths_are_canonical": False,
    "migration_added": False,
    "atomic_upgrade_implemented": False,
}


class AlignmentError(ValueError):
    """A review ledger is missing, stale, contradictory or overstated."""


def require(condition: bool, code: str) -> None:
    if not condition:
        raise AlignmentError(code)


def unique_object(pairs: list[tuple[str, Any]]) -> dict[str, Any]:
    result: dict[str, Any] = {}
    for key, value in pairs:
        require(key not in result, "DUPLICATE_JSON_KEY")
        result[key] = value
    return result


def parse_json(text: str) -> dict[str, Any]:
    try:
        value = json.loads(text, object_pairs_hook=unique_object,
                           parse_constant=lambda _: (_ for _ in ()).throw(AlignmentError("NONFINITE_JSON")))
    except (json.JSONDecodeError, RecursionError) as error:
        raise AlignmentError("INVALID_JSON") from error
    require(type(value) is dict, "OBJECT_REQUIRED")
    return value


def exact(value: Any, expected: dict[str, Any], code: str) -> None:
    require(type(value) is dict and set(value) == set(expected), code)
    for key, item in expected.items():
        require(type(value[key]) is type(item) and value[key] == item, code)


def validate_matrix(data: dict[str, Any]) -> None:
    keys = {"schema_version", "source_commit", "status", "policy", "sources", "data",
            "sensitive_api_profiles", "qa", "remaining_gates", "runtime_qualification"}
    require(set(data) == keys, "MATRIX_KEYS")
    require(type(data["schema_version"]) is int and data["schema_version"] == 1, "SCHEMA_VERSION")
    require(data["source_commit"] == SOURCE_COMMIT, "SOURCE_COMMIT")
    require(data["status"] == "REVIEW", "OWNER_APPROVAL_NOT_ESTABLISHED")
    require(data["runtime_qualification"] == "NOT_ESTABLISHED", "RUNTIME_NOT_QUALIFIED")
    exact(data["policy"], POLICY, "POLICY_CONTRADICTION")
    exact(data["data"], DATA, "DATA_CLAIM_CONTRADICTION")
    exact(data["sensitive_api_profiles"], API_PROFILES, "API_PROFILE_SET")
    require(type(data["sources"]) is dict and set(data["sources"]) == set(SOURCE_PINS), "SOURCE_SET")
    for key, (path, blob) in SOURCE_PINS.items():
        exact(data["sources"][key], {"path": path, "git_blob": blob}, "SOURCE_PIN")
    require(type(data["remaining_gates"]) is dict and set(data["remaining_gates"]) == GAPS, "GATE_SET")
    require(all(value == "OPEN" for value in data["remaining_gates"].values()), "GATE_NOT_DISCHARGED")
    require(type(data["qa"]) is list and len(data["qa"]) == len(QA_SOURCES), "QA_SET")
    seen: set[int] = set()
    for item in data["qa"]:
        require(type(item) is dict and set(item) == {"packet_scenario", "test_files", "evidence"}, "QA_KEYS")
        number = item["packet_scenario"]
        require(type(number) is int and number in QA_SOURCES and number not in seen, "QA_SCENARIO")
        seen.add(number)
        require(item["test_files"] == [TEST_PREFIX + name for name in QA_SOURCES[number]], "QA_SOURCE_MAP")
        require(item["evidence"] == "SOURCE_MAPPED_NOT_EXECUTED_BY_THIS_CHECK", "QA_NOT_EXECUTED")


def safe_bytes(root: Path, relative: str) -> bytes:
    path = Path(relative)
    require(not path.is_absolute() and ".." not in path.parts and bool(path.parts), "UNSAFE_PATH")
    current = root
    for part in path.parts:
        current = current / part
        require(not current.is_symlink(), "SYMLINK_FORBIDDEN")
    require(current.is_file() and current.stat().st_size <= 2_000_000, "SOURCE_MISSING_OR_OVERSIZED")
    return current.read_bytes()


def blob_sha(data: bytes) -> str:
    return hashlib.sha1(b"blob " + str(len(data)).encode("ascii") + b"\0" + data).hexdigest()


def read_api_profiles(text: str) -> dict[str, str]:
    rows: dict[str, str] = {}
    for line in text.splitlines():
        columns = [part.strip().strip("`") for part in line.strip().strip("|").split("|")]
        if not columns or not re.fullmatch(r"API-WCP-[0-9]{4}", columns[0]):
            continue
        require(len(columns) >= 19 and columns[0] not in rows, "INVALID_API_REGISTRY_ROW")
        rows[columns[0]] = columns[4]
    return rows


def verify_sources(root: Path, data: dict[str, Any]) -> dict[str, Any]:
    validate_matrix(data)
    contents: dict[str, str] = {}
    for key, (path, expected) in SOURCE_PINS.items():
        raw = safe_bytes(root, path)
        require(blob_sha(raw) == expected, "SOURCE_DRIFT:" + key)
        contents[key] = raw.decode("utf-8")
    profiles = read_api_profiles(contents["api"])
    require(all(profiles.get(key) == value for key, value in API_PROFILES.items()), "API_AUTHENTICATION_DRIFT")
    require("| MFA at login | policy/account dependent | mandatory |" in contents["security"], "FROZEN_CONFLICT_CHANGED")
    for name in (DATA["user_sessions"], DATA["admin_sessions"]):
        require("### " + name in contents["database"], "CANONICAL_SESSION_NAMESPACE_MISSING")
    tests = sorted({path for row in data["qa"] for path in row["test_files"]})
    for path in tests:
        require(bool(safe_bytes(root, path).strip()), "EMPTY_QA_SOURCE")
    return {
        "schema_version": 1,
        "scope": "SOURCE_TRACEABILITY_ONLY",
        "result": "PASS_WITH_DECLARED_GAPS",
        "source_reference_commit": SOURCE_COMMIT,
        "checked_source_blobs": {key: value[1] for key, value in SOURCE_PINS.items()},
        "sensitive_api_profiles_checked": len(API_PROFILES),
        "test_sources_present": len(tests),
        "application_tests_executed": False,
        "canonical_amendment_approved": False,
        "runtime_qualification": "NOT_ESTABLISHED",
        "remaining_gates": data["remaining_gates"],
    }


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--root", type=Path, default=Path.cwd())
    parser.add_argument("--report", type=Path)
    args = parser.parse_args()
    try:
        root = args.root.resolve(strict=True)
        report = verify_sources(root, parse_json(safe_bytes(root, MATRIX_PATH).decode("utf-8")))
    except (AlignmentError, OSError, UnicodeError) as error:
        code = str(error) if isinstance(error, AlignmentError) else type(error).__name__
        print("MFA alignment evidence: FAIL " + code, file=sys.stderr)
        return 1
    if args.report is not None:
        args.report.parent.mkdir(parents=True, exist_ok=True)
        args.report.write_text(json.dumps(report, indent=2, sort_keys=True) + "\n", encoding="utf-8")
    print("MFA alignment: source traceability PASS; runtime NOT_ESTABLISHED; six gates OPEN.")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
