# Installer source and orchestration

Reviewed CLI/bootstrap/recipes and interrupted-install recovery belong here. Wizard UI lives in templates/setup and state/orchestration in the Setup domain. Existing scaffolds remain non-operational and preserve their guarded behavior.

This is an implementation reservation. Follow the [Core status](../README.md) and [development/acceptance guide](../../documentation/development/CORE-ENGINE.md) before adding runtime code.

Reviewed service templates/profiles have a single source under [resources/services](../resources/services/README.md). Bootstrap recipes select compatible resources and approved operations; they do not maintain a second copy of service defaults. Dependency/toolchain and package inputs are mapped in the [dependency guide](../DEPENDENCIES.md).
