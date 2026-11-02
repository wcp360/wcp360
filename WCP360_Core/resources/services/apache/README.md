# Apache HTTP Server resources

**Status: documentation reservation with external candidate templates. No production configuration or native provider is implemented in this directory.**

| Responsibility | Location or rule |
| :--- | :--- |
| Owning domain | [Hosting](../../../src/Module/Hosting/README.md) |
| Versioned contents | Virtual hosts, approved modules, reverse-proxy integration and per-site policy. |
| Typical Debian destination | /etc/apache2/ |
| Adapter code | The owning module's Infrastructure layer; invoke only accepted typed agent operations. |
| Verification | Renderer/escaping tests in tests/Unit; synthetic fixtures in tests/Fixtures; service syntax, failed-apply and observed-health checks in tests/Integration on a disposable target. |

The [adopted web-server strategy](../../../src/Module/Hosting/WEB-SERVER-STRATEGY.md) targets Apache event as a private compatibility backend between Nginx and PHP-FPM for new general-purpose installations. Apache/FPM also remains a dedicated alternative. This directory is still a source reservation; no native provider or changed installation requirement is implemented here. Follow the [web-server roadmap](../../../src/Module/Hosting/WEB-SERVER-ROADMAP.md) and prevent listener/TLS ownership conflicts.

Existing [application preview templates](../../../agents/wcpd/candidate/planner/templates/applications) provide Apache/FPM and Apache-to-FrankenPHP HTTP proxy text. The seventh [application recipe](../../../src/Module/Hosting/APPLICATION-PROFILES.md), `nginx_apache_php_fpm`, now reuses the Apache/FPM and fixed .htaccess sources behind Nginx in application-plan/2 and application-preview/2. All content requests use the private Apache backend in that inert preview. The six original recipes retain their version-1 meanings and bytes. Durable selection/default changes, native provider installation, apply and Apache/event/FPM/HTTP qualification remain open. Proxying HTTP to FrankenPHP alone does not provide native .htaccess processing. Keep these sources with the Go renderer; do not copy them here.

When implemented, keep package/version-specific templates and profiles here. Add templates/ or profiles/ only with actual reviewed source. Test fixtures have a single home under tests/Fixtures; application validation stays in the owning domain. A candidate settings renderer is not the production validator for the Linux service.

Paths above are conventional examples, not a deployment allowlist. Resolve the installed package, version, instance, ownership and approved WCP-managed include before applying. Follow the [shared configuration lifecycle](../README.md#configuration-change-lifecycle) and [source/runtime boundary](../../README.md).

Use the [working guide](../../../WORKING-GUIDE.md) to choose the owning source location and [current status](../../../STATUS.md) to distinguish implemented candidates from planned or unqualified behavior.
