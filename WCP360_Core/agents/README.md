# System agent sources

The production architecture separates a non-root `wcpd` agent from a minimal-root `wcp-privd` broker. The approved agent path is the broker client; extensions, PHP and ordinary workers do not receive direct privileged IPC access.

## Executable development candidates

| Source | Actual behavior and boundary |
| :--- | :--- |
| [wcpd/candidate/planner](wcpd/candidate/planner/README.md) | Bounded, standard-library Go CLI that renders Nginx/PHP-FPM or FrankenPHP Worker plans. It has no listener, database, privileged operation or host mutation. |
| [wcpd/candidate/provisioning](wcpd/candidate/provisioning/go.mod) | Isolated experiment containing separate non-root agent and root broker process roles, bounded Unix-socket messages and peer-UID checks. It provisions a limited PHP-FPM demonstration, journals outcomes, inspects uncertain results and supports tested crash/boot recovery. |
| [Candidate systemd units](../resources/services/systemd/README.md) | Disposable supervision and boot-recovery profiles; their presence does not install a production service. |

Both process roles of the provisioning experiment currently live under `wcpd/candidate/provisioning`; that source location does not merge their privilege boundaries. These candidates are not the accepted production IPC protocol or a complete production daemon pair. The [wcpd](wcpd/README.md) and [wcp-privd](wcp-privd/README.md) production source layouts remain reservations.

Provisioning requires an explicitly prepared Debian 13 disposable container or VM. The VM gate binds its marker to the current boot and expected machine profile. Debian 12 and Ubuntu runtime/preflight verification does not widen this privileged admission gate.

## Remaining scope

Production protocol/operation acceptance, general resource adapters, durable recovery and upgrade compatibility, operational limits and privileged qualification on each supported OS remain open. Follow the [Core roadmap](../ROADMAP.md), [Core status](../README.md) and [development/acceptance guide](../../documentation/development/CORE-ENGINE.md) before extending runtime behavior.

Versioned Linux configuration text belongs in [resources/services](../resources/services/README.md); owning domain rules remain in PHP modules. Agent operations implement only the accepted typed boundary. Installation of a resource directory never adds a new privileged operation or an arbitrary service/command name.
