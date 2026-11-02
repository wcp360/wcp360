# Application recipe authoring template

**Status: non-executable authoring example. Not a catalogue application, package or release.**

Copy [recipe.template.json](recipe.template.json) and [configuration.schema.json](configuration.schema.json) only when preparing a specifically identified candidate. The template uses native_http as an authoring example, with no application association or provider selected. Choose exactly php, native_http or docker for the real recipe; these are application execution families, not current Core engine selectors.

Follow the [draft schema guide](../../schemas/README.md). Keep installable false, releases empty, unresolved pins null, every phase not_implemented and all qualification claims false. The local recipe authoring version does not select an upstream release. A source-reference URL records documentation; it is not a download instruction or proof of publisher trust.

Bind the exact catalogue name/category/nature/proposed_profile tuple. Add application-specific bounded settings and secret references, with no paths, backend URLs, ports, shell hooks, Compose documents or arbitrary expressions. Core resolves registry references and authorization; neither a pattern match nor a signed recipe grants effects.

## Phase authoring checklist

| Phase document | Application-specific decisions to record |
| :--- | :--- |
| lifecycle/install.md | Existing owners, preflight, owned resources, first-run admission, initialization and health. |
| lifecycle/configure.md | Typed settings, diff/revision, safe consumer reload and observed result. |
| lifecycle/update.md | Exact release/provider/plugin compatibility, backup, drain, data migration and recovery. |
| lifecycle/uninstall.md | Drain/revoke, remove only owned execution resources, retain data; purge separately. |
| lifecycle/backup.md | Consistent application/database/uploads/configuration and key references. |
| lifecycle/restore.md | Compatible fresh target, secret re-injection, verification and traffic handover. |
| lifecycle/inspect.md | Bounded read-only desired/observed state and missing evidence. |
| lifecycle/reconcile.md | Interruption, operation identity, fencing, uncertain effects and explicit operator recovery. |

The template's phase references remain null because no application-specific documents or adapter exist here. Pilot manifests link their own phase documents. Do not turn these Markdown plans into executable steps or invent agent operation IDs.

Use the existing [Core owners](../../../WCP360_Core/STRUCTURE.md) and [application contract preparation](../../../WCP360_Core/src/Module/Hosting/design/applications/README.md). Retain dependencies, publisher keys, real secrets, binaries, application data and build output outside this authoring tree.
