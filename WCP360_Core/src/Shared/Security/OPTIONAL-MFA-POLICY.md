# Optional MFA login-policy derivative — Core M1-S2

Date: 2026-11-04. Status: development candidate, not production qualification.
Authorization: [D-CORE-M1-S2](https://github.com/wcp360/wcp360/issues/132#issuecomment-6630205789), following the explicit owner direction in #132 and the recorded November 3 product policy.

The Core `SessionLifetimePolicy::mfaMandatoryAtLogin()` now returns false for both
USER and ADMIN. It describes the absence of a blanket enrollment prerequisite,
not permission to bypass a factor that an account has already enabled. It does
not authenticate a principal, grant roles or declare strength-2 authentication.
The existing candidate admission already supports optional administrator MFA;
this increment aligns a contradictory policy helper, not a new login endpoint.

`SessionBoundary`, `CandidateMfa`, pending-factor admission, encryption failure
handling, account/factor revisions, recovery, session rotation/revocation and
operation-specific step-up checks are not changed by this increment. Remember-me
remains disabled and all ordinary, privileged and emergency session durations
remain unchanged. A password-only session must never be relabeled MFA-proven.

The [new unit regression](../../../tests/Unit/OptionalLoginMfaPolicyTest.php)
checks both ordinary-login policy values, disabled remember-me and exact USER/
ADMIN idle and absolute boundaries. Existing MFA/session integration and browser
suites must pass on the exact combined candidate. Static code or an older green
run is not execution evidence for this revision.

The [provenance record](CANDIDATE-PROVENANCE.json) preserves the original source
blob identities and adds this explicit semantic derivative; it no longer claims
namespace-only equivalence for the changed helper. Historical SOURCE/platform
and its tests are intentionally untouched. The frozen Security v1.0 export is
also untouched: its mandatory-ADMIN passage and any affected high-risk contracts
remain an explicit #132 canonical-alignment task, not silently ratified here.

No API/IPC identifier, database schema, migration, permission, dependency, runtime
installer or privileged operation changes. Integration still needs exact-source
CI, substantive same-author review and the recorded MASTER/protection gates.
This note qualifies the older shared README's unchanged-policy wording for this
one helper only; its session-lifetime and enabled-factor descriptions still apply.
DOC/DESIGN/BM remain unchanged; CODE-BASELINE NONE.
