#!/usr/bin/env python3
"""Fail-closed registry drift sentry for issue #136.

This checks the five imported canonical contract *declarations*, not the
production Core implementation. Passing is never a TEST-API qualification.
Do not silently update the pinned expectations to make a change pass:
approved versioned API/Security/QA decisions must precede any amendment.
"""

from __future__ import annotations

import argparse
import json
from pathlib import Path
from typing import Any


class ContractDrift(ValueError):
    """The reviewed canonical #136 mapping no longer matches source."""


# Explicit review input pinned to canonical API v1.0 and issue #136.
# The expected values are not proof of runtime permission enforcement.
EXPECTED: dict[str, dict[str, str]] = {
    "API-WCP-0004": {
        "producer": "B",
        "surface": "BOTH",
        "authentication": "AUTH-B",
        "permission_id": "jobs.read",
        "action_id": "jobs.read",
        "resource_type": "job",
        "tenant_scope": "TS-B",
        "request_schema": "{job_id?,resource_type?,resource_id?,state?,cursor?,limit?}",
        "response_schema": "JobView/Collection",
        "errors": "E-J",
        "timeout": "10s",
        "retry": "RR",
        "idempotency": "none",
        "audit": "AU0",
        "security": "S-Q",
        "test_id": "TEST-API-0004",
    },
    "API-WCP-0006": {
        "producer": "P",
        "surface": "POLICY",
        "authentication": "AUTH-P",
        "permission_id": "audit.read",
        "action_id": "audit.read",
        "resource_type": "audit_record",
        "tenant_scope": "TS-P",
        "request_schema": "{scope_ref,audit_record_id?,audit_event_id?,actor_id?,resource_type?,resource_id?,from?,to?,cursor?,limit?}",
        "response_schema": "AuditRecordView/Collection",
        "errors": "E-AUD",
        "timeout": "10s",
        "retry": "RR",
        "idempotency": "none",
        "audit": "AU4",
        "security": "S-Q",
        "test_id": "TEST-API-0006",
    },
    "API-WCP-0701": {
        "producer": "B",
        "surface": "BOTH",
        "authentication": "AUTH-B",
        "permission_id": "security.sessions.manage",
        "action_id": "security.sessions.manage",
        "resource_type": "session",
        "tenant_scope": "TS-S",
        "request_schema": "{operation,session_id?}",
        "response_schema": "SessionView/Collection/Mutation",
        "errors": "E-M",
        "timeout": "15s",
        "retry": "RK",
        "idempotency": "key when mutating",
        "audit": "AU3",
        "security": "S-P",
        "test_id": "TEST-API-0701",
    },
    "API-WCP-0801": {
        "producer": "A",
        "surface": "WCP-ADMIN",
        "authentication": "AUTH-A",
        "permission_id": "servers.read",
        "action_id": "servers.read",
        "resource_type": "server",
        "tenant_scope": "TS-A",
        "request_schema": "{server_id?,cursor?,limit?}",
        "response_schema": "ServerView/Collection",
        "errors": "E-R",
        "timeout": "10s",
        "retry": "RR",
        "idempotency": "none",
        "audit": "AU0",
        "security": "S-A",
        "test_id": "TEST-API-0801",
    },
    "API-WCP-0901": {
        "producer": "A",
        "surface": "WCP-ADMIN",
        "authentication": "AUTH-A",
        "permission_id": "iam.users.manage",
        "action_id": "iam.users.manage",
        "resource_type": "principal",
        "tenant_scope": "TS-B",
        "request_schema": "{operation,principal_id?,account_id?,profile?,expected_revision?,idempotency_key?}",
        "response_schema": "IAMUserView/Collection",
        "errors": "E-M",
        "timeout": "15s",
        "retry": "RK",
        "idempotency": "key when mutating",
        "audit": "AU1",
        "security": "S-P",
        "test_id": "TEST-API-0901",
    },
}

PERMISSION_SURFACES = {
    "jobs.read": "BOTH / scope-bound",
    "audit.read": "POLICY / privileged",
    "security.sessions.manage": "BOTH / scope-bound",
    "servers.read": "WCP-ADMIN",
    "iam.users.manage": "WCP-ADMIN / OWNER",
}

SOURCE_FILES = {
    "api": "SOURCE/platform/contracts/api-contracts.v1.json",
    "actions": "SOURCE/platform/contracts/action-catalog.v1.json",
    "permissions": "SOURCE/platform/contracts/permissions.v1.json",
    "daemon": "SOURCE/daemon/internal/contracts/registry.json",
}

DAEMON_FIELDS = {
    "producer": "producer",
    "surface": "surface",
    "authentication": "authentication",
    "permission_id": "permission",
    "action_id": "action",
    "resource_type": "resource",
    "tenant_scope": "scope",
    "request_schema": "request_schema",
    "response_schema": "response_schema",
    "errors": "errors",
    "timeout": "timeout",
    "retry": "retry",
    "idempotency": "idempotency",
    "audit": "audit",
    "security": "security",
    "test_id": "test",
}


def load_json(root: Path, relative: str) -> dict[str, Any]:
    source = root / relative
    try:
        data = json.loads(source.read_text(encoding="utf-8"))
    except (OSError, UnicodeError, json.JSONDecodeError) as exc:
        raise ContractDrift(f"Required source unavailable or invalid: {relative}") from exc
    if not isinstance(data, dict):
        raise ContractDrift(f"Source must be a JSON object: {relative}")
    return data


def index_rows(document: dict[str, Any], key: str, id_key: str) -> dict[str, dict[str, Any]]:
    items = document.get(key)
    if not isinstance(items, list):
        raise ContractDrift(f"Missing source array: {key}")
    entries: dict[str, dict[str, Any]] = {}
    for row in items:
        if not isinstance(row, dict) or not isinstance(row.get(id_key), str):
            raise ContractDrift(f"Invalid source entry in {key}")
        identity = row[id_key]
        if identity in entries:
            raise ContractDrift(f"Duplicate source entry: {identity}")
        entries[identity] = row
    return entries


def exact(row: dict[str, Any], field: str, expected: str, label: str) -> None:
    if row.get(field) != expected:
        raise ContractDrift(f"{label}: {field} differs from reviewed #136 declaration")


def verify(root: Path) -> dict[str, Any]:
    documents = {name: load_json(root, path) for name, path in SOURCE_FILES.items()}
    api = documents["api"]
    if api.get("version") != "1.0" or api.get("count") != 86:
        raise ContractDrift("Canonical API v1.0 catalogue metadata differs")
    if documents["actions"].get("version") != "1.0" or documents["actions"].get("count") != 86:
        raise ContractDrift("Canonical action catalogue metadata differs")
    if documents["permissions"].get("version") != "1.0" or documents["permissions"].get("count") != 82:
        raise ContractDrift("Canonical permission catalogue metadata differs")
    if documents["daemon"].get("schema_version") != 1:
        raise ContractDrift("Daemon registry schema metadata differs")

    contracts = index_rows(api, "contracts", "contract_id")
    actions = index_rows(documents["actions"], "actions", "contract_id")
    permissions = index_rows(documents["permissions"], "permissions", "id")
    daemon = index_rows(documents["daemon"], "apis", "id")
    if len(daemon) != 86:
        raise ContractDrift("Daemon API contract count differs")
    if "candidate.hosting.plan" in permissions:
        raise ContractDrift("Candidate-only permission leaked into canonical permission registry")

    for contract_id, fields in EXPECTED.items():
        if contract_id not in contracts or contract_id not in actions or contract_id not in daemon:
            raise ContractDrift(f"Required canonical contract missing: {contract_id}")
        canonical = contracts[contract_id]
        emitted = daemon[contract_id]
        action = actions[contract_id]
        exact(canonical, "version", "1.0", contract_id)
        exact(canonical, "compatibility", "C1/D0", contract_id)
        exact(emitted, "version", "1.0", contract_id)
        exact(emitted, "compatibility", "C1/D0", contract_id)
        for field, expected_value in fields.items():
            exact(canonical, field, expected_value, contract_id)
            exact(emitted, DAEMON_FIELDS[field], expected_value, contract_id + " daemon")

        for name in ("action_id", "permission_id", "surface", "resource_type"):
            exact(action, name, fields[name], contract_id + " action catalogue")
        exact(action, "domain", fields["action_id"].split(".", 1)[0], contract_id)
        permission = fields["permission_id"]
        if permission not in permissions:
            raise ContractDrift(f"Canonical permission missing: {permission}")
        exact(permissions[permission], "surface", PERMISSION_SURFACES[permission], permission)
        exact(permissions[permission], "status", "ACTIVE", permission)
        if permission == "candidate.hosting.plan":
            raise ContractDrift("Candidate-only authorization must never alias canonical permissions")

    return {
        "status": "DECLARATION_DRIFT_CHECK_ONLY",
        "reference": "wcp360/wcp360#136",
        "contract_ids_checked": sorted(EXPECTED),
        "contracts_checked": len(EXPECTED),
        "canonical_implementation_qualified": False,
        "canonical_test_api_passes": 0,
        "authorizes_permission_expansion": False,
        "authorizes_merge_or_release": False,
    }


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--root", type=Path, default=Path(__file__).resolve().parents[2])
    args = parser.parse_args()
    try:
        result = verify(args.root.resolve())
    except ContractDrift as exc:
        print(f"FAIL #136 declaration drift: {exc}")
        return 1
    print(json.dumps(result, sort_keys=True))
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
