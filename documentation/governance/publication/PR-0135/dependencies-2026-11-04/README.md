# Dependency remediation — exact generated lock, no audit suppression

Date: 2026-11-04. Decision: D-DEP-REMEDIATE-0001.
This is a source/test candidate until the final combined commit passes CI and
receives its own integration decision. It does not complete MFA implementation.

## Reconciled evidence

The earlier Core log named Foundation 7.4.12 and Twig 3.22.0 with CVE identifiers
that could not be corroborated against primary public records. Those names and
identifiers are not adopted as verified project facts.

A fresh resolver checked the exact committed composer.json and lock blob hashes.
The inspected original lock actually contains Foundation 7.4.22 and Twig 3.30.0.
The first two-package proposal left eleven advisories across nine packages and
was rejected. Its archive13363593121 has SHA-256
49d0a6f6c7df2e0f36e45f0d9f22ff0fdc2e0097ea45d1330fd27e4310300643.

The subsequent Composer audit identifies fifteen advisories across ten Symfony
packages. Composer regenerated those ten packages at7.4.23 with the same root
requirements, no added/removed package, no script/plugin execution, and no audit
or platform exception. Twig was already current and is unchanged.
The exact versions and source references are in EVIDENCE.json.

Resolver41916582081 completed resolution and a clean final audit, but its proposal
checker initially refused Composer2.11's extra published-time metadata. That run
is not relabeled successful. Publisher41917254640 then verified the immutable
artifact, unchanged111 package identities/groups/types, exactly ten version
changes,29 metadata-only additions and plugin-api-version2.9-to2.11. It stored
only the exact unreferenced lock blob; no Git ref was updated by that workflow.
Both artifact archives and their hashes were independently inspected.

The current lock is the complete Composer-produced file, not manually edited
versions or invented distribution references. The clean audit JSON is preserved
next to this document. The temporary content-write proposal workflow is removed
from the final candidate; its reviewed history and artifact evidence remain.

## Acceptance and limitations

A clean dependency audit is not an application test. Final PHPStan/PHPUnit,
Symfony/container/Twig checks, supported OS lanes, PostgreSQL, browser and worker
checks remain required on the actual combined PR HEAD. Earlier green runs and
old metrics-only results do not qualify this dependency change.

The read-only runtime feature and engineering tools may be integrated as their
own clearly bounded delivery once their checks/review succeed. The old blanket
PR135 draft wording must not be read as requiring the unpublished MFA adapter to
be secretly included or treated as complete before a separate eligible delivery.
A distinct exact-SHA MASTER decision is still required before any merge.

No custom SessionBoundary patch, factor/session atomic adapter, USER MFA support,
privileged operation, database migration, canonical amendment, production release
or main Ruleset activation is supplied by this dependency patch. The earlier
blocked source write is not retried or included in another form.

Rollback uses a reviewed dependency correction/revert through the same existing
work branch; restoring a known vulnerable lock is not a production remedy.
Everything required for review is in this repository or its pinned CI artifacts;
no owner download or manual transfer is required.
