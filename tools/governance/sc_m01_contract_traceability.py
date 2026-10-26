#!/usr/bin/env python3
"""Read-only SC-M01 API/IPC/Permission test-reference drift verifier.

Checks normative *identifier bindings*, not runnable tests, owner approval,
security correctness, E3 review, or SC-M01 implementation authorization.
"""
from __future__ import annotations

import argparse
import hashlib
import json
import re
import sys
from pathlib import Path

MAIN_SHA = "e1fd356f5aa6bc89e1a9316442f6ed033e90ed14"
FILES = {
    "api": "specifications/api/WCP360_API_Contract_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md",
    "ipc": "specifications/ipc/WCP360_IPC_Contract_WCP360-IPC-B-1_CANONICAL_IMPLEMENTATION_EXPORT.md",
    "permissions": "specifications/security/WCP360_Permission_Registry_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md",
}
VERSIONS = {"api": "1.0", "ipc": "WCP360-IPC-B/1", "permissions": "v1.0"}
COUNTS = {"api_contracts": 86, "ipc_operations": 47, "active_permissions": 82}
SNAPSHOT = "governance/verification/SC-M01-CONTRACT-TRACEABILITY-2026-10-26.json"
TABLES = {
    "api": (
        "## 8. API Contract Registry",
        "contract_id version producer surface authentication permission_id action_id "
        "resource_type tenant_scope request_schema response_schema errors timeout "
        "retry idempotency audit security compatibility test_id".split(),
        r"API-WCP-[0-9]{4}",
    ),
    "ipc": (
        "## 6. Complete 47-operation registry",
        "contract_id action_id job_type ipc_operation_id resource_identifier "
        "validated_input_schema bounded_primitive timeout idempotency "
        "concurrency_scope unknown_outcome_reconciliation test_id".split(),
        r"IPC-WCP-[0-9]{4}",
    ),
    "permissions": (
        "## Canonical Registry — 82 permission_id",
        ["permission_id", "surface", "status"],
        r"[a-z][a-z0-9_.-]{1,127}",
    ),
}
SURFACES = {
    "WCP-USER": {"WCP-USER"},
    "WCP-ADMIN": {"WCP-ADMIN"},
    "WCP-ADMIN / OWNER": {"WCP-ADMIN"},
    "BOTH / scope-bound": {"BOTH"},
    "POLICY / privileged": {"POLICY", "EMERGENCY"},
    "POLICY": {"POLICY"},
}
AUTH = {
    "WCP-USER": {"AUTH-U"},
    "WCP-ADMIN": {"AUTH-A", "AUTH-PRIV"},
    "BOTH": {"AUTH-B"},
    "POLICY": {"AUTH-P"},
    "EMERGENCY": {"AUTH-E"},
}
FORBIDDEN = re.compile(
    r"shell[.]exec|arbitrary.?command|generic.?sudo|generic.?systemctl|"
    r"/bin/sh|/bin/bash|run.?arbitrary|^sudo$|^systemctl$",
    re.IGNORECASE,
)


def git_sha(blob: bytes) -> str:
    return hashlib.sha1(
        b"blob " + str(len(blob)).encode("ascii") + b"\0" + blob
    ).hexdigest()


def unquote(text: str) -> str:
    text = text.strip()
    return text[1:-1] if len(text) >= 2 and text[0] == text[-1] == chr(96) else text


def parse_table(text: str, kind: str) -> list[dict[str, str]]:
    title, columns, id_pattern = TABLES[kind]
    lines = text.splitlines()
    found = [i for i, line in enumerate(lines) if line.strip() == title]
    if len(found) != 1:
        raise ValueError(f"{kind}: canonical table section not found exactly once")
    items = []
    header = False
    separator = False
    for line in lines[found[0] + 1:]:
        if line.startswith("## "):
            break
        if not line.startswith("|"):
            continue
        if not line.endswith("|"):
            raise ValueError(f"{kind}: malformed table row")
        values = [unquote(v) for v in line[1:-1].split("|")]
        if len(values) != len(columns):
            raise ValueError(f"{kind}: expected {len(columns)} cells; got {len(values)}")
        if not header:
            expected = "Permission ID" if kind == "permissions" else "contract_id"
            if values[0] != expected:
                raise ValueError(f"{kind}: wrong header")
            header = True
            continue
        if not separator:
            if not all(re.fullmatch(r"[-: ]+", v) for v in values):
                raise ValueError(f"{kind}: missing separator")
            separator = True
            continue
        if any(not v for v in values):
            raise ValueError(f"{kind}: empty source cell")
        if not re.fullmatch(id_pattern, values[0]):
            raise ValueError(f"{kind}: malformed canonical identifier")
        items.append(dict(zip(columns, values)))
    if not header or not separator or not items:
        raise ValueError(f"{kind}: incomplete canonical source table")
    return items


def unique(items: list[dict[str, str]], key: str) -> None:
    vals = [r[key] for r in items]
    if len(vals) != len(set(vals)):
        raise ValueError(f"duplicate {key}")


def validate(api: list[dict[str, str]],
             ipc: list[dict[str, str]],
             perms: list[dict[str, str]]) -> None:
    if (len(api), len(ipc), len(perms)) != (86, 47, 82):
        raise ValueError("canonical inventory counts drifted (86/47/82)")
    for group, field in ((api, "contract_id"), (api, "test_id"),
                         (ipc, "contract_id"), (ipc, "test_id"),
                         (ipc, "ipc_operation_id"), (perms, "permission_id")):
        unique(group, field)
    permissions = {p["permission_id"]: p for p in perms}
    if any(p["status"] != "ACTIVE" for p in perms):
        raise ValueError("non-ACTIVE permission in canonical active table")
    used = set()
    for a in api:
        cid = a["contract_id"]
        if a["version"] != "1.0":
            raise ValueError(f"{cid}: API contract version drift")
        if a["test_id"] != cid.replace("API-WCP-", "TEST-API-"):
            raise ValueError(f"{cid}: TEST-API binding mismatch")
        p = permissions.get(a["permission_id"])
        if p is None:
            raise ValueError(f"{cid}: unknown permission")
        used.add(a["permission_id"])
        if a["surface"] not in SURFACES.get(p["surface"], set()):
            raise ValueError(f"{cid}: permission/surface mismatch")
        if a["authentication"] not in AUTH.get(a["surface"], set()):
            raise ValueError(f"{cid}: authentication/surface mismatch")
        if not (
            (a["retry"] == "RR" and a["idempotency"] == "none")
            or (
                a["retry"] in ("RK", "RN")
                and a["idempotency"] in ("key-required", "key when mutating")
            )
        ):
            raise ValueError(f"{cid}: retry/idempotency reference mismatch")
    if used != set(permissions):
        raise ValueError(f"active permissions not referenced: {sorted(set(permissions)-used)}")
    for i in ipc:
        cid = i["contract_id"]
        if i["test_id"] != cid.replace("IPC-WCP-", "TEST-IPC-"):
            raise ValueError(f"{cid}: TEST-IPC binding mismatch")
        if i["bounded_primitive"] in ("N/A", "*") or FORBIDDEN.search(i["bounded_primitive"]):
            raise ValueError(f"{cid}: unbounded primitive")
        if i["ipc_operation_id"] in ("*", "shell.exec", "run.command"):
            raise ValueError(f"{cid}: forbidden generic IPC operation")


def read_canonical(repo: Path) -> tuple[dict[str, str], dict[str, list[dict[str, str]]]]:
    digests, contents = {}, {}
    for key, relative in FILES.items():
        path = repo / relative
        if not path.is_file() or path.is_symlink():
            raise ValueError(f"{key}: canonical file missing or symlinked")
        blob = path.read_bytes()
        if len(blob) > 500_000:
            raise ValueError(f"{key}: oversized canonical file")
        digests[key] = git_sha(blob)
        contents[key] = parse_table(blob.decode("utf-8"), key)
    validate(contents["api"], contents["ipc"], contents["permissions"])
    return digests, contents


def build(repo: Path) -> dict:
    hashes, rows = read_canonical(repo)
    return {
        "schema_version": 1,
        "authority": "DERIVED_NONCANONICAL_REFERENCE_ONLY",
        "source_main_sha": MAIN_SHA,
        "scope": "SC-M01 contract identifiers only; not executable test cases",
        "counts": COUNTS,
        "source_files": {
            key: {"path": FILES[key], "version": VERSIONS[key],
                  "git_blob_sha": hashes[key]}
            for key in ("api", "ipc", "permissions")
        },
        "api_test_bindings": [
            {**r, "execution_state": "NOT_EXECUTED"} for r in rows["api"]
        ],
        "ipc_test_bindings": [
            {**r, "execution_state": "NOT_EXECUTED"} for r in rows["ipc"]
        ],
        "permission_bindings": rows["permissions"],
        "test_results": {"executed": 0, "passed": 0, "failed": 0},
        "owner_approval": "NOT_GRANTED_BY_THIS_ARTIFACT",
        "e3_review": "NOT_GRANTED_BY_THIS_ARTIFACT",
        "implementation_gate": "SC-M01_PAUSED_CODE_BASELINE_NONE",
    }


def check(repo: Path) -> dict:
    target = repo / SNAPSHOT
    if not target.is_file() or target.is_symlink():
        raise ValueError("derived reference snapshot missing or symlinked")
    saved = json.loads(target.read_text(encoding="utf-8"))
    derived = build(repo)
    if saved != derived:
        raise ValueError(
            "snapshot does not match canonical sources; owner review required"
        )
    return derived


def main(argv: list[str] | None = None) -> int:
    ap = argparse.ArgumentParser(description=__doc__)
    ap.add_argument("--repo", type=Path, default=Path(__file__).resolve().parents[2])
    ap.add_argument("--mode", choices=("check", "summary", "print-json"), default="check")
    args = ap.parse_args(argv)
    try:
        result = (
            check(args.repo.resolve())
            if args.mode == "check" else build(args.repo.resolve())
        )
    except (OSError, ValueError, UnicodeError, json.JSONDecodeError) as error:
        print(f"SC-M01 traceability FAILED: {error}", file=sys.stderr)
        return 2
    if args.mode == "print-json":
        print(json.dumps(result, indent=2, ensure_ascii=False))
    elif args.mode == "summary":
        print("Derived reference coverage (NOT executable QA):")
        print(json.dumps(result["counts"], sort_keys=True))
        print("CODE-BASELINE NONE; no owner approval or test PASS")
    else:
        print("REFERENCE PASS: 86 API, 47 IPC, 82 active permissions; "
              "source/snapshot byte-consistent. NOT A QA OR IMPLEMENTATION GO.")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
