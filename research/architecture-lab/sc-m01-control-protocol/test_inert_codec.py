"""Inert negative parser probes only — not production security tests."""
import copy
import json
import struct
import unittest

from inert_codec import (
    CANDIDATE_REQUEST_MAX,
    CANDIDATE_VERSION,
    decode_candidate_frame,
    make_candidate_fixture_frame,
)

REQ_UUID = "11111111-1111-4111-8111-111111111111"
CORR_UUID = "22222222-2222-4222-8222-222222222222"
CTX_UUID = "33333333-3333-4333-8333-333333333333"
RES_UUID = "44444444-4444-4444-8444-444444444444"
INTENT_UUID = "55555555-5555-4555-8555-555555555555"


class InertControlCandidateTests(unittest.TestCase):
    def setUp(self):
        self.req = {
            "protocol_version": CANDIDATE_VERSION,
            "request_id": REQ_UUID,
            "correlation_id": CORR_UUID,
            "origin_surface": "WCP-USER",
            "authorization_context_ref": CTX_UUID,
            "control_operation_id": "status.read",
            "action_id": "applications.read",
            "resource_type": "application",
            "resource_id": RES_UUID,
            "payload": {},
        }
        self.ctx = {
            "decision": "ALLOW",
            "expires_at_seconds": 101,
            "revoked": False,
            "origin_surface": self.req["origin_surface"],
            "authorization_context_ref": CTX_UUID,
            "action_id": self.req["action_id"],
            "resource_type": self.req["resource_type"],
            "resource_id": RES_UUID,
        }

    def submit_context(self):
        return dict(self.ctx, action_id="applications.create")

    def submit_request(self, **updates):
        request = dict(
            self.req,
            action_id="applications.create",
            control_operation_id="job.intent.submit",
            payload={"intent_ref": INTENT_UUID},
        )
        request.update(updates)
        return request

    def run_decode(self, request=None, *, peer="wcp360-user", context=None, now=100):
        return decode_candidate_frame(
            make_candidate_fixture_frame(self.req if request is None else request),
            peer_service_fixture=peer,
            trusted_context_fixture=self.ctx if context is None else context,
            now_seconds_fixture=now,
        )

    def bad(self, pattern, request=None, **kwargs):
        with self.assertRaisesRegex(ValueError, pattern):
            self.run_decode(request, **kwargs)

    def test_safe_read_is_parsed_not_dispatched(self):
        result = self.run_decode()
        self.assertEqual(result["control_operation_id"], "status.read")

    def test_admin_peer_and_matching_admin_context(self):
        req = copy.deepcopy(self.req)
        req["origin_surface"] = "WCP-ADMIN"
        context = dict(self.ctx, origin_surface="WCP-ADMIN")
        result = self.run_decode(req, peer="wcp360-admin", context=context)
        self.assertEqual(result["origin_surface"], "WCP-ADMIN")

    def test_missing_peer_is_rejected(self):
        self.bad("unrecognized synthetic peer", peer="wcp360-worker")

    def test_user_peer_admin_surface_is_rejected(self):
        req = dict(self.req, origin_surface="WCP-ADMIN")
        self.bad("peer-to-surface mismatch", req)

    def test_admin_peer_user_surface_is_rejected(self):
        self.bad("peer-to-surface mismatch", peer="wcp360-admin")

    def test_context_denied_is_rejected(self):
        self.bad("missing/denied", context=dict(self.ctx, decision="DENY"))

    def test_context_expired_is_rejected(self):
        self.bad("expired", now=101)

    def test_context_revoked_is_rejected(self):
        self.bad("revoked", context=dict(self.ctx, revoked=True))

    def test_origin_surface_context_binding_is_rejected(self):
        self.bad("trusted-context binding mismatch",
                 context=dict(self.ctx, origin_surface="WCP-ADMIN"))

    def test_action_context_binding_is_rejected(self):
        self.bad("trusted-context binding mismatch",
                 context=dict(self.ctx, action_id="servers.reboot"))

    def test_resource_context_binding_is_rejected(self):
        self.bad("trusted-context binding mismatch",
                 context=dict(self.ctx, resource_id=REQ_UUID))

    def test_unknown_control_operation_is_rejected(self):
        self.bad("unknown control operation",
                 dict(self.req, control_operation_id="shell.exec"))

    def test_caller_supplied_ipc_operation_is_rejected(self):
        self.bad("unknown top-level field",
                 dict(self.req, ipc_operation_id="linux_user.create"))

    def test_caller_supplied_permission_is_rejected(self):
        self.bad("unknown top-level field",
                 dict(self.req, permission_id="servers.reboot"))

    def test_typed_action_id_syntax_is_enforced(self):
        self.bad("invalid typed identifier",
                 dict(self.req, action_id="/bin/sh"))

    def test_bad_version_is_rejected(self):
        self.bad("unsupported proposed protocol",
                 dict(self.req, protocol_version="WCP360-IPC-B/1"))

    def test_missing_field_is_rejected(self):
        req = dict(self.req)
        req.pop("authorization_context_ref")
        self.bad("required field missing", req)

    def test_bad_uuid_is_rejected(self):
        self.bad("not a valid UUID", dict(self.req, resource_id="not-a-uuid"))

    def test_malformed_utf8_is_rejected(self):
        self.assert_reject_frame(struct.pack(">I", 2) + b"\xff\xfe")

    def test_duplicate_json_key_is_rejected(self):
        text = '{"protocol_version":"x","protocol_version":"y"}'.encode()
        self.assert_reject_frame(struct.pack(">I", len(text)) + text)

    def test_nan_is_rejected(self):
        raw = json.dumps(dict(self.req, expected_revision=float("nan"))).encode()
        self.assert_reject_frame(struct.pack(">I", len(raw)) + raw)

    def test_top_level_array_is_rejected(self):
        self.assert_reject_frame(struct.pack(">I", 2) + b"[]")

    def test_zero_length_frame_is_rejected(self):
        self.assert_reject_frame(b"\0\0\0\0")

    def test_oversized_frame_is_rejected(self):
        self.assert_reject_frame(struct.pack(">I", CANDIDATE_REQUEST_MAX + 1))

    def test_truncated_frame_is_rejected(self):
        raw = make_candidate_fixture_frame(self.req)
        self.assert_reject_frame(raw[:-1])

    def test_trailing_bytes_are_rejected(self):
        raw = make_candidate_fixture_frame(self.req)
        self.assert_reject_frame(raw + b"X")

    def test_wrong_revision_type_is_rejected(self):
        self.bad("invalid expected_revision",
                 dict(self.req, expected_revision=True))

    def test_read_cannot_have_idempotency_key(self):
        self.bad("cannot carry mutation data",
                 dict(self.req, idempotency_key="some-key"))

    def test_read_cannot_have_payload(self):
        self.bad("cannot carry mutation data",
                 dict(self.req, payload={"something": "data"}))

    def test_submit_requires_idempotency(self):
        self.bad("missing or unbounded candidate idempotency",
                 self.submit_request(), context=self.submit_context())

    def test_submit_denies_mismatched_action_binding(self):
        candidate = self.submit_request(action_id="applications.read",
                                        idempotency_key="key")
        self.bad("unapproved synthetic action-to-operation binding",
                 candidate, context=self.ctx)

    def test_submit_rejects_caller_command(self):
        self.bad("forbidden caller-controlled field",
                 self.submit_request(
                     idempotency_key="key",
                     payload={"intent_ref": INTENT_UUID, "command": "echo"},
                 ), context=self.submit_context())

    def test_submit_rejects_unregistered_input(self):
        self.bad("registered intent_ref",
                 self.submit_request(
                     idempotency_key="key",
                     payload={"intent_ref": INTENT_UUID, "new_field": True},
                 ), context=self.submit_context())

    def test_submit_accepts_only_fixture_intent_reference(self):
        parsed = self.run_decode(
            self.submit_request(idempotency_key="key"),
            context=self.submit_context(),
        )
        self.assertEqual(parsed["payload"]["intent_ref"], INTENT_UUID)

    def test_nested_sensitive_field_fails(self):
        self.bad("forbidden caller-controlled field",
                 self.submit_request(
                     idempotency_key="key",
                     payload={"intent_ref": INTENT_UUID,
                              "data": {"sudo": "systemctl"}},
                 ), context=self.submit_context())

    def test_submit_idempotency_too_long_fails(self):
        self.bad("missing or unbounded candidate idempotency",
                 self.submit_request(idempotency_key="k" * 129),
                 context=self.submit_context())

    def test_submit_rejects_nonascii_idempotency(self):
        self.bad("non-printable idempotency",
                 self.submit_request(idempotency_key="clé"),
                 context=self.submit_context())

    def assert_reject_frame(self, raw):
        with self.assertRaises(ValueError):
            decode_candidate_frame(
                raw, peer_service_fixture="wcp360-user",
                trusted_context_fixture=self.ctx, now_seconds_fixture=100,
            )


if __name__ == "__main__":
    unittest.main()
