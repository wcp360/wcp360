# Nginx resources

**Status: documentation reservation with external candidate templates. No production configuration or native provider is implemented in this directory.**

| Responsibility | Location or rule |
| :--- | :--- |
| Owning domain | [Hosting](../../../src/Module/Hosting/README.md) |
| Versioned contents | Reverse proxy, managed virtual hosts, upstreams, TLS references and FastCGI integration. |
| Typical Debian destination | /etc/nginx/ |
| Adapter code | The owning module's Infrastructure layer; invoke only accepted typed agent operations. |
| Verification | Renderer/escaping tests in tests/Unit; synthetic fixtures in tests/Fixtures; service syntax, failed-apply and observed-health checks in tests/Integration on a disposable target. |

The intended baseline web server. Keep panel ADMIN/USER origins and tenant virtual hosts separate; do not overwrite operator-owned configuration.

Current inert sources live beside the [Go planner](../../../agents/wcpd/candidate/planner/README.md): Classic/Advanced templates plus application FPM/proxy variants under templates/applications. Keep a single source; do not copy them here. The [target default](../../../src/Module/Hosting/WEB-SERVER-STRATEGY.md) adds a private Apache event backend after explicit implementation; current durable Classic remains Nginx/FPM.

When implemented, keep package/version-specific templates and profiles here. Add templates/ or profiles/ only with actual reviewed source. Test fixtures have a single home under tests/Fixtures; application validation stays in the owning domain. A candidate settings renderer is not the production validator for the Linux service.

Paths above are conventional examples, not a deployment allowlist. Resolve the installed package, version, instance, ownership and approved WCP-managed include before applying. Follow the [shared configuration lifecycle](../README.md#configuration-change-lifecycle) and [source/runtime boundary](../../README.md).
