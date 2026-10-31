# HTTP route wiring

Reserve this location for reviewed route imports and context-specific route exposure when the Symfony bootstrap exists. Controllers stay in their domain's UI/Http layer and call authorized application use cases.

An API path or menu item does not grant a capability. Preserve the accepted API identifiers, ADMIN/USER origin rules and permission registry; do not invent parallel endpoints from folder names. Route visibility and service access must be tested for the actual runtime context.

- [Configuration boundary](../README.md)
- [Module layout](../../src/Module/README.md)
