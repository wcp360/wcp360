#!/usr/bin/env python3
"""INERT C03/C06 research POC: bounded frame + synthetic trust-assertion checks.

NOT a production control protocol. No sockets, SO_PEERCRED, authentication,
authorization, database, jobs, daemon, Linux actions or privileged operations.
Peer names and context are SYNTHETIC TEST FIXTURES, never trusted production
input. Candidate field names, timeout/size values and operations are unapproved.
"""
from __future__ import annotations

import json
import re
import struct
import uuid
from typing import Any, Mapping

CANDIDATE_VERSION = "WCP360-CONTROL/1-CANDIDATE"
CANDIDATE_REQUEST_MAX = 32768
# These illustrative C06 operation IDs are NOT registered implementation IDs.
CANDIDATE_OPERATIONS = frozenset(("status.read", "job.intent.submit"))
PEER_TO_SURFACE_FIXTURE = {
    "wcp360-user": "WCP-USER",
    "wcp360-admin": "WCP-ADMIN",
}
REQUIRED = frozenset((
    "protocol_version", "request_id", "correlation_id", "origin_surface",
    "authorization_context_ref", "control_operation_id", "action_id",
    "resource_type", "resource_id", "payload",
))
OPTIONAL = frozenset(("idempotency_key", "expected_revision"))
FORBIDDEN_FIELDS = frozenset((
    "shell", "command", "argv", "environment", "env", "executable",
    "executable_path", "sudo", "systemctl", "ipc_operation_id",
    "password", "secret", "private_key", "token", "cookie", "bearer_token",
    "session", "role", "permission_id", "service_name", "signal",
    "absolute_path", "filesystem_path", "raw_sql",
))
TYPED_ID = re.compile(r"^[a-z][a-z0-9_.-]{1,127}$")


def _deny(reason: str) -> None:
    raise ValueError("REJECTED: " + reason)


def _parse_uuid(value: Any, field: str) -> None:
    if not isinstance(value, str):
        _deny(f"{field} is not a UUID string")
    try:
        uuid.UUID(value)
    except ValueError:
        _deny(f"{field} is not a valid UUID")


def _reject_duplicate_keys(pairs: list[tuple[str, Any]]) -> dict:
    out: dict[str, Any] = {}
    for key, value in pairs:
        if key in out:
            _deny("duplicate JSON key")
        out[key] = value
    return out


def _reject_nonjson_constant(value: str) -> None:
    _deny("non-JSON numeric constant")


def _reject_sensitive_fields(payload: Any) -> None:
    if isinstance(payload, dict):
        for key, value in payload.items():
            if key.lower() in FORBIDDEN_FIELDS:
                _deny("forbidden caller-controlled field")
            _reject_sensitive_fields(value)
    elif isinstance(payload, list):
        for value in payload:
            _reject_sensitive_fields(value)


def decode_candidate_frame(
    frame: bytes,
    *,
    peer_service_fixture: str,
    trusted_context_fixture: Mapping[str, Any],
    now_seconds_fixture: int,
) -> dict[str, Any]:
    """Parse a synthetic frame; return DATA ONLY, never a dispatch decision.

    The fixture-based peer/context checks are *not* production security.
    """
    if not isinstance(frame, bytes) or len(frame) < 4:
        _deny("missing frame header")
    n = struct.unpack(">I", frame[:4])[0]
    if n == 0 or n > CANDIDATE_REQUEST_MAX:
        _deny("invalid frame size")
    if len(frame) != 4 + n:
        _deny("truncated or trailing frame bytes")
    try:
        request = json.loads(
            frame[4:].decode("utf-8", "strict"),
            object_pairs_hook=_reject_duplicate_keys,
            parse_constant=_reject_nonjson_constant,
        )
    except (ValueError, UnicodeError, json.JSONDecodeError) as exc:
        _deny("malformed UTF-8/JSON: " + type(exc).__name__)
    if not isinstance(request, dict):
        _deny("top level is not a JSON object")

    actual_keys = set(request)
    if REQUIRED - actual_keys:
        _deny("required field missing")
    if actual_keys - (REQUIRED | OPTIONAL):
        _deny("unknown top-level field")
    if request["protocol_version"] != CANDIDATE_VERSION:
        _deny("unsupported proposed protocol version")
    for key in ("request_id", "correlation_id", "authorization_context_ref",
                "resource_id"):
        _parse_uuid(request[key], key)
    for key in ("control_operation_id", "action_id", "resource_type"):
        val = request[key]
        if not isinstance(val, str) or not TYPED_ID.fullmatch(val):
            _deny("invalid typed identifier")
    if request["control_operation_id"] not in CANDIDATE_OPERATIONS:
        _deny("unknown control operation: no fallback")
    if "expected_revision" in request:
        if type(request["expected_revision"]) is not int or request["expected_revision"] < 1:
            _deny("invalid expected_revision")

    _reject_sensitive_fields(request["payload"])
    peer_surface = PEER_TO_SURFACE_FIXTURE.get(peer_service_fixture)
    if peer_surface is None:
        _deny("unrecognized synthetic peer service")
    if request["origin_surface"] != peer_surface:
        _deny("synthetic peer-to-surface mismatch")
    context = trusted_context_fixture
    if not isinstance(context, Mapping) or context.get("decision") != "ALLOW":
        _deny("missing/denied synthetic authorization context")
    if context.get("revoked", False) is not False:
        _deny("revoked synthetic authorization context")
    expiry = context.get("expires_at_seconds")
    if type(expiry) is not int or type(now_seconds_fixture) is not int or expiry <= now_seconds_fixture:
        _deny("expired synthetic authorization context")
    for name in (
        "origin_surface", "authorization_context_ref", "action_id",
        "resource_type", "resource_id",
    ):
        if context.get(name) != request[name]:
            _deny("synthetic trusted-context binding mismatch")

    if request["control_operation_id"] == "status.read":
        if request["payload"] != {} or "idempotency_key" in request:
            _deny("read-only operation cannot carry mutation data")
    else:
        key = request.get("idempotency_key")
        if not isinstance(key, str) or not 1 <= len(key) <= 128:
            _deny("missing or unbounded candidate idempotency key")
        if not key.isascii() or not all(33 <= ord(ch) <= 126 for ch in key):
            _deny("non-printable idempotency key")
        if not isinstance(request["payload"], dict) or set(request["payload"]) != {"intent_ref"}:
            _deny("mutation accepts only registered intent_ref")
        _parse_uuid(request["payload"]["intent_ref"], "intent_ref")

    # Returning data DOES NOT grant any authority, run jobs, or dispatch IPC-B.
    return request


def make_candidate_fixture_frame(request: dict[str, Any]) -> bytes:
    """Test-only framing helper. Not part of any approved WCP360 contract."""
    payload = json.dumps(request, ensure_ascii=False, separators=(",", ":")).encode("utf-8")
    return struct.pack(">I", len(payload)) + payload
