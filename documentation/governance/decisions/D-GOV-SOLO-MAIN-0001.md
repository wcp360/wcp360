# D-GOV-SOLO-MAIN-0001 — main protection for the single maintainer

Date: 2026-11-04. Authority: explicit owner request to protect main and the
previously adopted single-maintainer governance D-GOV-SOLO-0001.
Status: configuration decision and implementation candidate; NOT live activation.

The owner confirmed that the review accounts are the same person. Extend the
zero-other-person-approval configuration from develop to main: no fabricated
independent vote and no last-push second-person requirement. This supersedes the
old two-approval frozen-main proposal, not the release/promotion gates.

Preserve PR admission, resolved review conversations, both required GitHub Actions
checks bound to app15368, strict current-base checks, no bypass actors, no forced
history rewrite and no deletion. Scope remains refs/heads/main only. Develop and
its live Ruleset26531327 are unchanged. No direct main push is authorized.
The allowed source route remains stable -> main; no stable/production branch,
release, CODE-BASELINE or promotion is created by this change.

The disabled template and ready-to-activate JSON differ only by enforcement.
The template validator checks that equality; the live readback checker retains
strict unknown-field handling and distinguishes configuration from real refusal
proof. Actual live tests are not inferred from offline fixtures.

Files are delivered in the repository, not as a required owner download.
An authorized repository administrator can enter the reviewed main-only settings
in Settings -> Rules -> Rulesets. No credentials should be pasted into a chat.

The current integration's branch-protection endpoint returned403 Resource not
accessible by integration, and it exposes no Ruleset mutation capability.
The ready payload is NOT activation, cannot grant administration to a workflow,
and must not be routed through an unauthorized token or another write surface.
Only a later successful Settings change and API readback may mark main protected.

Retain the existing DEFAULT ruleset without blindly retargeting it. Check any
inherited rules before activation. Production/readiness assertions remain separate.
