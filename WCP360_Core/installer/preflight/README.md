# Installation preflight

**Status: candidate read-only checks are implemented; they perform no installation or host mutation.**

Read-only discovery and validation before selecting or applying an installation plan.

From the repository root:

~~~sh
php WCP360_Core/installer/preflight/platform.php
php WCP360_Core/installer/preflight/candidate.php --target --dependencies
~~~

`platform.php` reads actual OS identity without Composer or Symfony and admits the exact Debian 12/13 and Ubuntu 22.04/24.04/26.04 LTS targets from [the shared catalogue](../../config/platforms/targets.json). It reports `runtime_validation: not_checked`; recognizing an OS is not a complete readiness result. Unknown releases and derivatives are rejected rather than inferred from `ID_LIKE`.

`candidate.php --target --dependencies` checks PHP 8.4, required extensions, dependency presence, exact target identity and amd64 architecture. It does not connect to a database or verify its server version/service health. Native PHP 8.2 on Debian 12, 8.1 on Ubuntu 22.04, 8.3 on Ubuntu 24.04 and 8.5 on Ubuntu 26.04 do not satisfy Core's current `~8.4.0` constraint. Supply a separately qualified PHP 8.4 runtime and PostgreSQL 17 provider/service where native defaults differ; these checks never add repositories or replace packages.

Supported OS/architecture, installed package/engine versions, capacity, ownership, existing listeners, hostname/DNS/time and incompatible service topology. Report failed or unknown checks explicitly; do not silently repair the host or treat UI fixtures as real observations.

The five-platform native Nginx/PHP-FPM matrix tests its own limited assertions; the complete Core and privileged systemd/reboot VM remain Debian 13-specific. An expected prerequisite refusal is not permission to run Symfony on an unsupported PHP version. [Platform and evidence boundaries](../../../documentation/development/SUPPORTED-PLATFORMS.md)

Setup business state and the restricted wizard remain in the [Setup module](../../src/Module/Setup/README.md). Service configuration text has one source in [resources/services](../../resources/services/README.md); installer code consumes the accepted boundaries rather than creating a second generic system executor.

- [Installer source map](../README.md)
- [Installation and upgrade verification](../../tests/Installer/README.md)
- [Release and recovery acceptance](../../../documentation/development/DELIVERY-AND-RELEASES.md)
