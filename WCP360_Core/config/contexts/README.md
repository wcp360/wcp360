# Application runtime contexts

**Runtime enforcement remains unfinished: Symfony does not automatically discover or enforce this directory.** A standalone [identity inspection](../../installer/preflight/README.md#runtime-identity-inspection) can now compare the calling Linux process with the accepted identity profiles; it does not wire or activate them. Approved bootstrap/packaging must explicitly implement and test the separate contexts.

| Context | Isolation to preserve |
| :--- | :--- |
| ADMIN | Administrative origin, cookies/session claims, runtime identity, database grants and permitted operations |
| USER | Customer origin, independent cookies/sessions/runtime credentials and enforced tenant/resource scope |
| Worker | Dedicated identity, bounded jobs, reauthorization/revocation and approved datastore/transport access |
| Setup/bootstrap | Restricted one-time enrollment and controlled install handoff; revoke temporary access after verified completion |

Non-root wcpd and the minimal-root wcp-privd broker are separately supervised components under agents/. PHP contexts, plugins and Python tools have no direct privileged-broker socket.

## Candidate database access

The opt-in [database profile](DATABASE-PROFILES.md) now selects a context-specific PostgreSQL credential and checks effective session-table privileges on new application connections. The default shared development profile retains its existing behavior. An explicit [candidate-only maintenance recipe](../../installer/recipes/CANDIDATE-DATABASE-ROLES.md) creates fresh development roles and exact direct grants. Native production provisioning, full runtime packaging and production admission remain unfinished.

## Inspect the calling identity

The profile names below select fixed expectations from [RuntimeIdentity](../../src/Shared/Platform/RuntimeIdentity.php), following [Security section 4](../../../documentation/specifications/security/WCP360_Security_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md#4-unix-identity-model).

| Inspection profile | Account and primary group | Required supplementary access |
| :--- | :--- | :--- |
| `user` | `wcp360-user:wcp360-user` | `wcp360-control` |
| `admin` | `wcp360-admin:wcp360-admin` | `wcp360-control` |
| `worker` | `wcp360-worker:wcp360-worker` | None |
| `wcpd` | `wcpd:wcpd` | `wcp360-priv` |
| `wcp-privd` | `root:root` | None required by this identity inspection |

Run `php WCP360_Core/installer/preflight/runtime-identity.php worker` from the repository root under the identity and confinement settings to inspect. The command observes itself, not an arbitrary PID or an already running service. `WCP_CONTEXT`, `.env` and custom UID/user overrides cannot alter the profile. Setup/updater root is transient lifecycle authority, so there is no steady-state `setup` or general `root` profile.

All four real/effective/saved/filesystem UID and GID values must match. The primary GID may appear in the kernel supplementary list; every required supplementary group must appear and all other groups are refused. All five canonical accounts and seven groups must resolve with distinct UID/GID assignments and matching primary groups. Non-root service accounts need a recognized no-login shell. This does not inspect shadow passwords or prove that credentials are locked.

The diagnostic also requires `NoNewPrivs=1`; non-root profiles require empty inheritable, permitted, effective and ambient capability sets. These are conservative inspection checks, not a new production contract or an automatic Symfony gate. The capability bounding set is reported but not qualified. The root profile deliberately makes no minimal-capability or privileged-operation acceptance claim: `minimal_root_qualified` and `privileged_effects_allowed` always remain false.

Namespace-local observations do not establish host-wide isolation. Production service-context verification, writable-path/ACL/MAC checks, database credentials/grants and runtime admission remain open. Current development/CI accounts are not automatically converted into canonical identities. See the [remaining M1 work](../../ROADMAP.md#m1).

Sharing source code or package configuration does not share cookies, secrets or Unix identities. Port differences alone do not isolate browser cookies. Resolve operational settings through approved per-context inputs; do not commit production credentials or one universal .env.

- [Configuration boundary](../README.md)
- [Agent boundaries](../../agents/README.md)
- [Core architecture and acceptance](../../../documentation/development/CORE-ENGINE.md)
