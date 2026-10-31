# Plugin authoring template

**Status: documentation-only template, not an executable or installable plugin.**

Copy this layout into a new plugin directory, choose a stable identity and complete [PLUGIN.md](PLUGIN.md). Remove this template's example wording when implementing the package.

The src, config, templates, tests and docs folders reserve separate plugin responsibilities. Add a real runtime/dependency lock and the owner-approved production manifest only when those contracts/toolchains are selected. The folder itself must not be auto-discovered as an enabled extension.

Develop against the [SDK](../../WCP360_SDK/README.md), then prove the [complete lifecycle and failure cases](../../documentation/development/PLUGIN-LIFECYCLE.md). Existing prototype manifest formats are not a production schema to copy blindly.

No Core internals, shared superuser DB credentials, direct broker client, root hook, universal shell function or Core vendor mutation belongs in a detached plugin.
