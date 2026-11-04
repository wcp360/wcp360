# Repository-first delivery

Decision: D-GOV-REPOSITORY-FIRST-0001.
Date: 2026-11-04. Authority: explicit product-owner instruction.
Repository: `wcp360/wcp360`.

The owner must not download conversation attachments, unpack local archives,
copy review templates between accounts, or upload our deliverables into GitHub.
Deliver development work directly through the existing repository workflow.

## Storage and traceability

- Source, tests, specifications, decisions and readable engineering reports belong
  in versioned repository files. Preserve each domain's authority and status.
- Store essential test results, source identities, limitations and evidence hashes
  in readable repository reports. Link full CI runs and artifacts to their exact
  source commit. Generated binaries and large raw CI logs remain repository CI
  artifacts or approved release assets; a chat ZIP is never their only record.
- Keep historical evidence immutable and clearly separated from the current
  checkpoint. A passed run certifies only its tested source and stated scope.
- User-facing delivery is a GitHub location and an accurate status summary, not a
  request to download a sandbox file. The owner need not handle files manually.
- Report a missing source, denied write or unavailable administration capability
  explicitly; do not substitute a download request, a fictitious upload or a
  different route around a security control.

## Unchanged safeguards

Use existing branches only. Do not write directly to permanent branches, weaken
protections, silently rewrite another domain's contract or merge incomplete work.
Keep code, tests and developer documentation in English. Optional MFA enrollment
never bypasses an enabled factor. Repository publication is not implementation
acceptance, independent review or production qualification.

## First migration under this decision

[PR #135 evidence index](publication/PR-0135/README.md) preserves the previously
shared documentary review dossier and the essential CI MFA report as readable
Git files. The historical dossier contains no executable patch. Application code
whose publication was blocked remains unpublished; this decision does not alter
that boundary. Large, regenerable publication inventories are identified by exact
hashes and their repository artifact rather than duplicated as source code.
