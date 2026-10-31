# Contributing to WCP360

WCP360 uses owner-domain governance and pull-request traceability.

## Core rule

No contributor or workstream may silently redefine a contract owned by another WCP360 authority.

Cross-domain impact requires a **WCP360 HANDOFF**.

Contradictions require a **WCP360 CONFLICT** routed to MASTER / Project Control and, when needed, AUDIT / ARCHITECTURE REVIEW.

## Repository locations

Use [documentation/README.md](documentation/README.md) for specifications, contracts, governance and manifests. Follow the [development strategy](documentation/development/README.md): Core application/modules/agents in WCP360_Core, detached packages in WCP360_Plugins, the public client boundary in WCP360_SDK, construction/qualification in WCP360_Build and qualified records in WCP360_Releases. These directories currently provide architecture scaffolds; runtime implementation and review gates remain explicit. The [path migration map](documentation/PATH-MIGRATION.json) translates earlier root locators; update active consumers and rerun relevant checks before merging an older branch. Keep immutable citations and historical evidence intact.

## Canonical artifact publication

An owner artifact may be promoted to `documentation/specifications/` only when its owner publication is identifiable and final.

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
CANONICAL SPECIFICATION INPUTS = 8/8 ACCEPTED
SC-M01 = LIMITED RESUME — INERT DAEMON FOUNDATION ONLY
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```

Repository, governance and documentation maintenance may continue. The [current manifest](documentation/manifests/WCP360-BM-0001.md) selects only the inert daemon foundation; additional runtime slices need explicit owner-backed authorization and the applicable reviews.


## Review and merge evidence

Repository review evidence is defined in:

documentation/governance/REVIEW-MERGE-POLICY.md

Key rules:
- automated checks are not independent review;
- same-author/self-review is recorded as publication control, not Engineering independence;
- non-normative proposals may be preserved without becoming owner-approved contracts;
- implementation/security/repository tooling uses the stricter review requirements defined by its change class;
- no fake reviewer, fake team or sub-agent approval;
- CODEOWNERS assignment alone is not proof that a review occurred;
- branch-protection checks must only be required after the corresponding check exists reliably on main.

Current mono-maintainer status does not waive the first CODE-BASELINE independent-review requirement.
