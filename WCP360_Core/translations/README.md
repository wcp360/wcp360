# Localization

Versioned translation catalogues for UI/validation/messages. Keep security and operation codes stable; translate presentation text rather than canonical identifiers.

This is an implementation reservation. Follow the [Core status](../README.md) and [development/acceptance guide](../../documentation/development/CORE-ENGINE.md) before adding runtime code.

When implemented, use the selected Symfony catalogue naming and documented locale/fallback policy. Do not create empty language folders or machine-generated catalogue files merely to fill the tree. Reuse translated presentation strings in [shared components](../templates/components/README.md) and [error pages](../templates/errors/README.md); canonical permission, operation and error identifiers stay unchanged.
