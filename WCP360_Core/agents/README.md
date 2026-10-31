# System agent sources

Separate Go modules/processes for non-root wcpd and minimal-root wcp-privd. The only broker client is the approved wcpd path; extensions, PHP and ordinary workers have no direct privileged IPC access.

This is an implementation reservation. Follow the [Core status](../README.md) and [development/acceptance guide](../../documentation/development/CORE-ENGINE.md) before adding runtime code.

Versioned Linux configuration text belongs in [resources/services](../resources/services/README.md); owning domain rules remain in PHP modules. Agent operations implement only the accepted typed boundary. Installation of a resource directory never adds a new privileged operation or an arbitrary service/command name.
