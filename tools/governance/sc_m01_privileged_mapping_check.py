#!/usr/bin/env python3
"""Read-only Security v1.0 / IPC-B/1 mapping audit; no privilege is granted.

This is a review tool, not an authorization library or final owner decision.
"""
from __future__ import annotations
import argparse
import json
import re
import sys
from pathlib import Path

from sc_m01_contract_traceability import FILES, parse_table

SECURITY_PATH = (
    "specifications/security/"
    "WCP360_Security_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md"
)
TITLE = "## 9. Privileged Operation Security Matrix — 47 ACTIVE operations"
MULTI_EVENT_LIFECYCLES = {
    "server.reboot": "server.reboot.requested + server.reboot.completed",
    "server.shutdown": "server.shutdown.requested + server.shutdown.completed",
}
PARENT_BINDING_OPEN = frozenset({
    "filesystem.directory.create",
    "filesystem.ownership.apply",
    "filesystem.permissions.apply",
    "filesystem.quota.apply",
    "nginx_vhost.validate",
    "nginx_vhost.apply",
    "nginx_vhost.remove",
    "nginx.reload",
    "php_pool.validate",
    "php_pool.apply",
    "php_pool.remove",
    "php_fpm.reload",
})
EXPECTED_OPEN_PERMISSION_EXPRESSIONS = {
    "filesystem.directory.create": "files.write + approved provisioning binding",
    "filesystem.ownership.apply": "files.permissions.manage + approved provisioning binding",
    "filesystem.permissions.apply": "files.permissions.manage + approved provisioning binding",
    "filesystem.quota.apply": "iam.account_overrides.manage OR applications.runtime.manage",
    "nginx_vhost.validate": "approved app/domain/admin binding",
    "nginx_vhost.apply": "approved app/domain/admin binding",
    "nginx_vhost.remove": "approved app/domain/admin binding",
    "nginx.reload": "approved technical parent OR servers.services.manage",
    "php_pool.validate": "approved app/admin binding",
    "php_pool.apply": "approved app/admin binding",
    "php_pool.remove": "approved app/admin binding",
    "php_fpm.reload": "approved technical parent OR servers.services.manage",
}
ID_PATTERN = re.compile(r"^[a-z][a-z0-9_.-]{1,127}$")
REQUIRED_COLUMNS = (
    "ipc_operation_id",
    "permission_expression",
    "origin_action_expression",
    "surface",
    "scope",
    "audit_event_id",
    "failure_class",
)


def parse_security(content: str) -> list[dict[str, str]]:
    lines = content.splitlines()
    positions = [i for i, line in enumerate(lines) if line.strip() == TITLE]
    if len(positions) != 1:
        raise ValueError("Security privileged matrix section missing or duplicated")
    stage = 0
    records = []
    for line in lines[positions[0] + 1:]:
        if line.startswith("## "):
            break
        if not line.startswith("|"):
            continue
        if not line.endswith("|"):
            raise ValueError("Security privileged matrix malformed row")
        values = [v.strip().strip(chr(96)) for v in line[1:-1].split("|")]
        if len(values) != 7:
            raise ValueError("Security privileged matrix must have seven columns")
        if stage == 0:
            if values[0] != "ipc_operation_id":
                raise ValueError("Security privileged matrix header drift")
            stage = 1
            continue
        if stage == 1:
            if not all(re.fullmatch(r"[-: ]+", v) for v in values):
                raise ValueError("Security privileged matrix separator missing")
            stage = 2
            continue
        if any(not v for v in values):
            raise ValueError("Security privileged matrix empty cell")
        if not ID_PATTERN.fullmatch(values[0]):
            raise ValueError("Security privileged matrix operation_id invalid")
        records.append(dict(zip(REQUIRED_COLUMNS, values)))
    if stage != 2 or not records:
        raise ValueError("Security privileged matrix incomplete")
    return records


def validate(
    security: list[dict[str, str]],
    ipc: list[dict[str, str]],
    permissions: list[dict[str, str]],
) -> dict:
    if (len(security), len(ipc), len(permissions)) != (47, 47, 82):
        raise ValueError("Security/IPC/Permission registry count mismatch")
    sec_ids = [r["ipc_operation_id"] for r in security]
    ipc_ids = [r["ipc_operation_id"] for r in ipc]
    if len(set(sec_ids)) != 47 or len(set(ipc_ids)) != 47:
        raise ValueError("duplicate IPC operation identifier")
    if set(sec_ids) != set(ipc_ids):
        raise ValueError("Security matrix and IPC-B registry operation IDs diverge")
    known = {r["permission_id"] for r in permissions}
    if len(known) != 82:
        raise ValueError("permission registry ID duplicates")
    direct = []
    needs_parent = []
    events = []
    ipc_map = {r["ipc_operation_id"]: r for r in ipc}
    for row in security:
        op = row["ipc_operation_id"]
        expression = row["permission_expression"]
        audit_expression = row["audit_event_id"]
        if op in MULTI_EVENT_LIFECYCLES and audit_expression == MULTI_EVENT_LIFECYCLES[op]:
            # Two-event lifecycle annotations in the FINAL Security source:
            # requested + completed, never a single audit_event_id.
            events.extend(audit_expression.split(" + "))
        elif ID_PATTERN.fullmatch(audit_expression):
            events.append(audit_expression)
        else:
            raise ValueError("invalid audit_event_id: " + op)
        if row["scope"] not in {"T", "T/S", "S", "S/G", "C"}:
            raise ValueError("unexpected Security scope: " + op)
        if any(x not in {"E1", "E2", "E3", "E4", "E5", "E6"}
               for x in row["failure_class"].split("/")):
            raise ValueError("unknown failure class: " + op)
        if expression in known:
            if op in PARENT_BINDING_OPEN:
                raise ValueError("review change to previously abstract binding: " + op)
            direct.append(op)
        else:
            if op not in PARENT_BINDING_OPEN:
                raise ValueError("new unratified abstract permission binding: " + op)
            if not ("approved" in expression or " OR " in expression or " + " in expression) or "approved" not in row["origin_action_expression"]:
                raise ValueError("abstract permission expression altered: " + op)
            if expression != EXPECTED_OPEN_PERMISSION_EXPRESSIONS[op]:
                raise ValueError("abstract permission expression altered: owner review required " + op)
            if row["origin_action_expression"] != "same approved parent action":
                raise ValueError("abstract permission parent action expression altered: " + op)
            needs_parent.append({
                "ipc_operation_id": op,
                "permission_expression": expression,
                "origin_action_expression": row["origin_action_expression"],
                "ipc_contract_id": ipc_map[op]["contract_id"],
                "required_test_reference": ipc_map[op]["test_id"],
            })
    if set(x["ipc_operation_id"] for x in needs_parent) != PARENT_BINDING_OPEN:
        raise ValueError("unresolved parent-binding set changed without review")
    if len(set(events)) != len(events):
        raise ValueError("audit event semantic IDs collide")
    if len(events) != 49:
        raise ValueError("unexpected audit event occurrence-type count")
    return {
        "security_ipc_operations_match": True,
        "security_operations": len(security),
        "ipc_operations": len(ipc),
        "active_permissions": len(known),
        "audit_event_type_references": len(events),
        "reboot_audit_lifecycle": MULTI_EVENT_LIFECYCLES["server.reboot"],
        "shutdown_audit_lifecycle": MULTI_EVENT_LIFECYCLES["server.shutdown"],
        "direct_security_permission_expressions": len(direct),
        "parent_binding_owner_decisions_required": len(needs_parent),
        "open_parent_bindings": needs_parent,
        "all_other_contracts_approved": False,
        "execution_permission_granted": False,
        "code_baseline": "NONE",
    }


def examine(root: Path) -> dict:
    def read(relative: str) -> str:
        path = root / relative
        if not path.is_file() or path.is_symlink():
            raise ValueError("canonical source missing or symlinked: " + relative)
        blob = path.read_bytes()
        if len(blob) > 500_000:
            raise ValueError("canonical source exceeds file-size bound")
        return blob.decode("utf-8")

    security = parse_security(read(SECURITY_PATH))
    ipc = parse_table(read(FILES["ipc"]), "ipc")
    permissions = parse_table(read(FILES["permissions"]), "permissions")
    return validate(security, ipc, permissions)


def main(argv: list[str] | None = None) -> int:
    ap = argparse.ArgumentParser(description=__doc__)
    ap.add_argument("--repo", type=Path, default=Path(__file__).resolve().parents[2])
    ap.add_argument("--json", action="store_true")
    args = ap.parse_args(argv)
    try:
        result = examine(args.repo.resolve())
    except (ValueError, OSError, UnicodeError) as exc:
        print("Security/IPC reference check FAILED: " + str(exc), file=sys.stderr)
        return 2
    if args.json:
        print(json.dumps(result, indent=2, sort_keys=True))
    else:
        print(
            "Security/IPC source map matches 47/47; "
            "35 direct security permission expressions, "
            "12 parent bindings requiring owner ratification; "
            "NO privileged execution authorization."
        )
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
