# Application runtime contexts

**Documentation only: Symfony does not automatically discover or enforce this directory.** The approved bootstrap/packaging must explicitly wire and test the separate contexts.

| Context | Isolation to preserve |
| :--- | :--- |
| ADMIN | Administrative origin, cookies/session claims, runtime identity, database grants and permitted operations |
| USER | Customer origin, independent cookies/sessions/runtime credentials and enforced tenant/resource scope |
| Worker | Dedicated identity, bounded jobs, reauthorization/revocation and approved datastore/transport access |
| Setup/bootstrap | Restricted one-time enrollment and controlled install handoff; revoke temporary access after verified completion |

Non-root wcpd and the minimal-root wcp-privd broker are separately supervised components under agents/. PHP contexts, plugins and Python tools have no direct privileged-broker socket.

Sharing source code or package configuration does not share cookies, secrets or Unix identities. Port differences alone do not isolate browser cookies. Resolve operational settings through approved per-context inputs; do not commit production credentials or one universal .env.

- [Configuration boundary](../README.md)
- [Agent boundaries](../../agents/README.md)
- [Core architecture and acceptance](../../../documentation/development/CORE-ENGINE.md)
