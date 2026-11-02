# Apache HTTP Server resources

**Status: source reservation. No deployable configuration or service integration is implemented here.**

| Responsibility | Location or rule |
| :--- | :--- |
| Owning domain | [Hosting](../../../src/Module/Hosting/README.md) |
| Versioned contents | Virtual hosts, approved modules, reverse-proxy integration and per-site policy. |
| Typical Debian destination | /etc/apache2/ |
| Adapter code | The owning module's Infrastructure layer; invoke only accepted typed agent operations. |
| Verification | Renderer/escaping tests in tests/Unit; synthetic fixtures in tests/Fixtures; service syntax, failed-apply and observed-health checks in tests/Integration on a disposable target. |

The [adopted web-server strategy](../../../src/Module/Hosting/WEB-SERVER-STRATEGY.md) targets Apache event as a private compatibility backend between Nginx and PHP-FPM for new general-purpose installations. Apache/FPM also remains a dedicated alternative. This directory is still a source reservation; no native provider or changed installation requirement is implemented here. Follow the [web-server roadmap](../../../src/Module/Hosting/WEB-SERVER-ROADMAP.md) and prevent listener/TLS ownership conflicts.

When implemented, keep package/version-specific templates and profiles here. Add templates/ or profiles/ only with actual reviewed source. Test fixtures have a single home under tests/Fixtures; application validation stays in the owning domain. A candidate settings renderer is not the production validator for the Linux service.

Paths above are conventional examples, not a deployment allowlist. Resolve the installed package, version, instance, ownership and approved WCP-managed include before applying. Follow the [shared configuration lifecycle](../README.md#configuration-change-lifecycle) and [source/runtime boundary](../../README.md).
