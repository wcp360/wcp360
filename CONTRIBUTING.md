# Contributing to WCP360

WCP360 uses owner-domain governance and pull-request traceability.

## Core rule

No contributor or workstream may silently redefine a contract owned by another WCP360 authority.

Cross-domain impact requires a **WCP360 HANDOFF**.

Contradictions require a **WCP360 CONFLICT** routed to MASTER / Project Control and, when needed, AUDIT / ARCHITECTURE REVIEW.

## Canonical artifact publication

An owner artifact may be promoted to `specifications/` only when its owner publication is identifiable and final.

Do not reconstruct missing canonical artifacts from older audits, staging files or general knowledge.

## Secrets

Never commit:

- passwords;
- API tokens;
- private keys;
- production credentials;
- database dumps;
- MFA seeds;
- recovery codes;
- sensitive production logs;
- customer data.

## Current implementation gate

```text
SOURCE CODE = PAUSED
SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```

Repository, governance and documentation maintenance may continue. Broad application implementation must not start until MASTER closes the SC-M01 evidence gate.


## Review and merge evidence

Repository review evidence is defined in:

governance/REVIEW-MERGE-POLICY.md

Key rules:
- automated checks are not independent review;
- same-author/self-review is recorded as publication control, not Engineering independence;
- non-normative proposals may be preserved without becoming owner-approved contracts;
- implementation/security/repository tooling uses the stricter review requirements defined by its change class;
- no fake reviewer, fake team or sub-agent approval;
- CODEOWNERS assignment alone is not proof that a review occurred;
- branch-protection checks must only be required after the corresponding check exists reliably on main.

Current mono-maintainer status does not waive the first CODE-BASELINE independent-review requirement.
