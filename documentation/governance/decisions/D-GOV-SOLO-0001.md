# D-GOV-SOLO-0001 — Single-maintainer candidate development

Date: 2026-11-04. Authority: explicit project-owner instruction in MASTER V2.
GitHub record: https://github.com/wcp360/wcp360/issues/131#issuecomment-6629184744

The owner confirmed that bylkaboy and azhosty are alternate accounts of the same
person and requested continued development without artificial cross-account
approvals. Their historic reviews remain attributed to their actual accounts;
none proves independent Engineering E3. Assistant review is same-author review.

## Replacement controls

For every bounded candidate, record the actual diff, exact HEAD/base, technical
and security findings, contract impacts, terminal applicable CI, remaining risks,
rollback and a separate MASTER integration decision. No automatic merge follows
from a green check. Real findings, failed checks and privileged-operation safety
remain blocking. Recheck evidence when source or the integration base changes.

The GitHub `develop` template uses zero required second-person approvals and no
last-push approval requirement. It retains PRs, strict required checks bound to
GitHub Actions, resolved discussions, no deletion, no force push and no bypass.
The main/release approval threshold is not relaxed by this candidate-development
decision. Its unchanged policy stays a frozen promotion gate, not alias approval.

## Current owner's branch-consolidation instruction

Process the open issues/PRs and preserve work before retiring the three named
existing branches. PR #133 packages the existing documentation branch; it does
not create a new branch. The route exception is exact PR + branch + target only.
Deletion requires verified integration and exact current-head preservation.
Never delete work simply to make the open count smaller.

## Limits

No new branch, direct permanent-branch push, generic privileged shell operation,
unapproved database/API/IPC changes, cancelled Sprint 0 or whole-corpus audit.
Code and technical documentation are English. MFA enrollment is optional on both
surfaces; enabled MFA remains enforced. Domain contracts are changed only by
explicit deltas, not invented owner approvals.

DOC v4.0.21.23 / DESIGN V2.5.3.36.4 / WCP360-BM-0001 FINAL are preserved.
CODE-BASELINE remains NONE. Candidate integration, branch protection activation,
release and deployment are different decisions with different evidence.
