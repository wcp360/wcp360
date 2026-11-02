# WCP360 retained source candidates

This directory preserves the standalone implementation candidates from PRs #108, #109, #110 and #114. The active Symfony application and its Go candidate renderer are maintained in [WCP360_Core](../WCP360_Core/README.md). SOURCE is not a second active Core or an installable WCP360 release.

| Retained directory | Scope | Integration boundary |
| :--- | :--- | :--- |
| [platform](platform/README.md) | Dependency-free PHP domain/application candidate, generated registries and 86 explicit handler boundaries | No active Core autoload, HTTP framework, concrete persistence or host executor |
| [daemon](daemon/README.md) | Inert Go CLI, embedded contract catalogue and pure policies | No listener, database, privileged action or Core daemon replacement |
| [ui](ui/) | Historical static USER/ADMIN shells | No production authentication or current Core frontend integration |

Retention preserves the original module identities, candidate source and contract fingerprints. It does not promote a production baseline, select an additional runtime stack or change licensing. Historical session/MFA policies in these candidates do not override the active Core's current policies.

## Source provenance

| Branch | Original reviewed head | Contribution retained |
| :--- | :--- | :--- |
| source/sc-m01-inert-daemon | 71deefa2976a7005983d410907dc2050287be05f | Original inert daemon candidate, also present unchanged in the full-platform base |
| source/full-platform-candidate | 284daabc8d57a65d3ad11e326baee32898e64254 | PHP platform candidate, static UI and daemon foundation |
| source/sc-m01-domain-foundation | 31fd3fe4d9203d9e5dbc2b43843c6c9ec74f2940 | Pure domain policies, embedded catalogue and --contracts CLI report |
| test/sc-m01-cli-process-contract | 462792110330f00b2a1911dd03175e8257379b3a | Process-level CLI and deliberate-invalid gate fixtures |

The original limited-start authorization at commit 3fd2f8007baa49c2454cc89c0a5488a57c77911b and manifests/WCP360-BM-0001.md are historical provenance. They are not new authorization to activate a listener, IPC, privileged operation or deployment. The original [full-platform candidate record](FULL-PLATFORM-CANDIDATE.md) is preserved with that historical scope.

## Canonical input locations

The five historical specifications/ source paths and their original Git blob fingerprints remain recorded in the derived data. Explicit allowlists resolve those paths to the existing files under documentation/specifications/; no second specification copy or new registry authority is introduced.

| Current repository input | Original Git blob |
| :--- | :--- |
| [API contract](../documentation/specifications/api/WCP360_API_Contract_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md) | 4893c5191b4928ecfa5a70f4dc14b39a8939fcea |
| [Permission registry](../documentation/specifications/security/WCP360_Permission_Registry_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md) | 81f3d425f36470167aba4bfb7ba069268626342f |
| [IPC contract](../documentation/specifications/ipc/WCP360_IPC_Contract_WCP360-IPC-B-1_CANONICAL_IMPLEMENTATION_EXPORT.md) | fec9c74e8413db822662e47c76c40486e282334f |
| [Database model](../documentation/specifications/database/WCP360_DB_Model_v1.0_Canonical_Implementation_Contract.md) | 2660e057bad1d00592c57f354b1f3745ccccbe7d |
| [Security baseline](../documentation/specifications/security/WCP360_Security_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md) | 6d74f2f363524511424f839ca7262143c2af95ab |

The integrated candidate still requires execution of its remote CI gates against the exact integrated revision. No local runtime, compiler, test or vulnerability scan was executed during reconciliation. Existing upstream PR results apply only to their original source revisions; they do not qualify the combined tree or the active Core.

For current development, use the Core [working guide](../WCP360_Core/WORKING-GUIDE.md) and [status](../WCP360_Core/STATUS.md).
