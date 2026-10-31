# Plugin implementation record

This is an authoring checklist, not a runtime manifest or permission grant. Replace each unfilled item with reviewed evidence. Production manifest schema/fields belong to the accepted extension contract.

| Field | Required record |
| :--- | :--- |
| Identity and publisher | Stable identifier, owner/contact, publication/trust identity and package name. |
| Purpose | One bounded responsibility and its distinction from Core ownership. |
| Version and compatibility | Plugin version, supported Core/API/SDK/runtime ranges and tested matrix. |
| Dependencies/conflicts | Required plugin/capability versions, cycles/conflicts and active-resource exit plan. |
| Capabilities | Exact approved action/permission relationships, tenant/resource scope and capability-change behavior. |
| Runtime/isolation | Process identity, supervision, CPU/memory/process/disk/queue budgets and shutdown behavior. |
| Network | Approved destinations/protocols, failure/SSRF controls and bounded buffering. |
| Configuration/secrets | Validated non-secret settings, secret references, rotation and redaction. |
| Owned data | Store/schema ownership, migrations, export, retention, reinstall and explicit purge policy. |
| Contributions | Approved menus/routes/jobs/events and isolated UI presentation; no Core template injection. |
| Health and failure | Readiness, crash/timeout/dependency behavior, quarantine, recovery and effect reconciliation. |
| Lifecycle | Install-disabled, enable, disable/drain, update, uninstall and retained-data reinstall procedures. |
| Artifact and license | Reviewed source/lock revisions, dependency licenses, build provenance, SBOM, digest/signature/trust evidence. |
| Tests | Unit, contract, integration, tenant/surface/security, lifecycle, migration and failure assertions. |
| Execution evidence | Candidate/merge SHA, environment, command/assertions, actual result and durable evidence/digests. |
| Reviews | Required owner decisions and exact-SHA independent review; same-author checks remain E2. |

Leave unexecuted cases PLANNED/READY/BLOCKED rather than PASS. A standalone test suite and a version string do not establish integrated or production-ready status.
