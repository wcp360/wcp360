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
