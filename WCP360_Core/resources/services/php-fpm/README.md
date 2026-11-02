# PHP-FPM resources

**Status: documentation reservation with external candidate templates. No production configuration or native provider is implemented in this directory.**

| Responsibility | Location or rule |
| :--- | :--- |
| Owning domain | [Hosting](../../../src/Module/Hosting/README.md) |
| Versioned contents | Per-version pool models, PHP directives, process limits and socket ownership. |
| Typical Debian destination | /etc/php/<version>/fpm/ |
| Adapter code | The owning module's Infrastructure layer; invoke only accepted typed agent operations. |
| Verification | Renderer/escaping tests in tests/Unit; synthetic fixtures in tests/Fixtures; service syntax, failed-apply and observed-health checks in tests/Integration on a disposable target. |

Separate panel ADMIN, panel USER and tenant pools/identities. Validate extension/directive choices and bound memory, children and timeouts.

The generic [candidate pool](../../../agents/wcpd/candidate/planner/templates/php-fpm.conf.tmpl) and [application pool](../../../agents/wcpd/candidate/planner/templates/applications/php-fpm.conf.tmpl) stay with the Go renderer. Do not duplicate them here. Synthetic preview identities do not admit a native FPM pool; actual identities, extensions and sockets must be provisioned and qualified separately.

When implemented, keep package/version-specific templates and profiles here. Add templates/ or profiles/ only with actual reviewed source. Test fixtures have a single home under tests/Fixtures; application validation stays in the owning domain. A candidate settings renderer is not the production validator for the Linux service.

Paths above are conventional examples, not a deployment allowlist. Resolve the installed package, version, instance, ownership and approved WCP-managed include before applying. Follow the [shared configuration lifecycle](../README.md#configuration-change-lifecycle) and [source/runtime boundary](../../README.md).
