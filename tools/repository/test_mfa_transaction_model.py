#!/usr/bin/env python3
"""Executable review model for the proposed factor/session transaction contract.

This is NOT the WCP360 database adapter or authentication implementation. It uses
symbolic states and no SQL, credential, network, filesystem or runtime operation.
Passing it cannot qualify PostgreSQL locks, live sessions or application behavior.
"""
from __future__ import annotations

from dataclasses import dataclass, replace
from itertools import combinations
import unittest


@dataclass(frozen=True)
class State:
    factor_revision: int = 1
    factor_enabled: bool = True
    factor_configured: bool = True
    session_strength: int = 1
    session_revision: int | None = None
    session_revoked: bool = False
    session_binding: str = "original-symbolic-binding"
    audit_count: int = 0
    other_account_revision: int = 7


def invariant(state: State) -> bool:
    return state.session_revoked or state.session_strength != 2 or (
        state.factor_enabled and state.factor_configured
        and state.session_revision == state.factor_revision
    )


def schedules() -> list[str]:
    """All 70 order-preserving shuffles of four upgrade and four mutation steps."""
    result = []
    for upgrade_positions in combinations(range(8), 4):
        result.append("".join("U" if i in upgrade_positions else "M" for i in range(8)))
    return result


def evaluate(order: str, mutation: str, *, check_revision: bool = True,
             revoke_on_mutation: bool = True, proof_revision: int = 1,
             fail_actor: str | None = None) -> tuple[str, State, list[State]]:
    """LOCK -> READ/VALIDATE -> STAGE -> COMMIT; only COMMIT changes visible state.

    A schedule attempting an already-held account lock is classified BLOCKED,
    not executed past the lock. The model assumes all writers use that lock.
    """
    assert order.count("U") == 4 and order.count("M") == 4
    assert mutation in {"disable", "replace", "recovery"}
    visible = State()
    history = [visible]
    positions = {"U": 0, "M": 0}
    staged: dict[str, State] = {}
    eligible: dict[str, bool] = {}
    lock: str | None = None
    for actor in order:
        step = positions[actor]
        if step == 0:
            if lock is not None:
                return "BLOCKED", visible, history
            lock = actor
        elif step == 1:
            assert lock == actor
            staged[actor] = visible
            eligible[actor] = actor == "M" or (
                visible.factor_enabled and visible.factor_configured
                and not visible.session_revoked and visible.session_strength == 1
                and (not check_revision or proof_revision == visible.factor_revision)
            )
        elif step == 2:
            assert lock == actor
            if actor == "U" and eligible[actor]:
                staged[actor] = replace(staged[actor], session_strength=2,
                                        session_revision=proof_revision,
                                        session_binding="rotated-symbolic-binding",
                                        audit_count=visible.audit_count + 1)
            elif actor == "M":
                staged[actor] = replace(staged[actor], factor_revision=visible.factor_revision + 1,
                                        factor_enabled=mutation != "disable",
                                        session_revoked=revoke_on_mutation or visible.session_revoked,
                                        audit_count=visible.audit_count + 1)
        else:
            assert lock == actor
            if actor != fail_actor:
                visible = staged[actor]
            lock = None
        positions[actor] += 1
        history.append(visible)
    return "COMPLETE", visible, history


class FactorSessionContractModelTests(unittest.TestCase):
    def test_serialization_explores_all_order_preserving_schedules(self) -> None:
        self.assertEqual(70, len(schedules()))
        for mutation in ["disable", "replace", "recovery"]:
            complete = []
            blocked = 0
            for order in schedules():
                result, state, history = evaluate(order, mutation)
                self.assertTrue(all(invariant(item) for item in history), (order, mutation))
                self.assertEqual(7, state.other_account_revision)
                if result == "COMPLETE":
                    complete.append(order)
                    self.assertTrue(state.session_revoked)
                else:
                    blocked += 1
            self.assertEqual(["UUUUMMMM", "MMMMUUUU"], complete)
            self.assertEqual(68, blocked)

    def test_factor_change_before_completion_prevents_upgrade(self) -> None:
        for mutation in ["disable", "replace", "recovery"]:
            _, state, _ = evaluate("MMMMUUUU", mutation)
            self.assertEqual(1, state.session_strength)
            self.assertEqual("original-symbolic-binding", state.session_binding)
            self.assertEqual(1, state.audit_count)

    def test_completion_before_change_is_invalidated_atomically(self) -> None:
        _, state, history = evaluate("UUUUMMMM", "replace")
        self.assertEqual(2, history[4].session_strength)
        self.assertFalse(history[4].session_revoked)
        self.assertTrue(state.session_revoked)
        self.assertEqual(2, state.audit_count)
        self.assertTrue(all(invariant(item) for item in history))

    def test_uncommitted_upgrade_does_not_change_visible_authority(self) -> None:
        _, _, history = evaluate("UUUUMMMM", "replace")
        self.assertEqual([State()] * 4, history[:4])
        self.assertEqual(2, history[4].session_strength)

    def test_stale_proof_is_denied_under_the_account_lock(self) -> None:
        _, _, history = evaluate("UUUUMMMM", "replace", proof_revision=0)
        self.assertEqual(1, history[4].session_strength)
        self.assertEqual(0, history[4].audit_count)

    def test_missing_revision_check_mutant_has_a_counterexample(self) -> None:
        _, _, history = evaluate("UUUUMMMM", "replace", check_revision=False, proof_revision=0)
        self.assertFalse(invariant(history[4]))

    def test_missing_revocation_mutant_has_a_counterexample(self) -> None:
        for mutation in ["disable", "replace", "recovery"]:
            _, state, _ = evaluate("UUUUMMMM", mutation, revoke_on_mutation=False)
            self.assertFalse(invariant(state))

    def test_definite_upgrade_commit_failure_rolls_back_authority_and_audit(self) -> None:
        _, _, history = evaluate("UUUUMMMM", "replace", fail_actor="U")
        self.assertEqual(State(), history[4])

    def test_definite_factor_commit_failure_preserves_prior_generation(self) -> None:
        _, state, history = evaluate("MMMMUUUU", "replace", fail_actor="M")
        self.assertEqual(State(), history[4])
        self.assertEqual(1, state.factor_revision)
        self.assertEqual(2, state.session_strength)
        self.assertTrue(invariant(state))

    def test_uncertain_commit_cannot_be_classified_as_definite_rollback(self) -> None:
        _, _, committed = evaluate("UUUUMMMM", "replace")
        _, _, rolled_back = evaluate("UUUUMMMM", "replace", fail_actor="U")
        possible_observed_states = {committed[4], rolled_back[4]}
        self.assertEqual(2, len(possible_observed_states))
        self.assertTrue(all(invariant(state) for state in possible_observed_states))
        # The adapter must not choose either outcome from transport failure alone.
        # Required response: no secret/authority disclosure, invalidate local state,
        # and re-observe; this model deliberately does not simulate the adapter.


if __name__ == "__main__":
    unittest.main(verbosity=2)
