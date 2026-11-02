# Caddy service resources

**Status: documentation reservation. No production configuration or new runtime capability is implemented here.**

Owner: [Hosting](../../../src/Module/Hosting/README.md), with Platform for provider supervision.

Standalone Caddy ingress, Caddy/PHP-FPM and HTTP-backend profiles have a different lifecycle from [Caddy embedded in FrankenPHP](../frankenphp/README.md). An embedded runtime does not require another Caddy daemon.

Current candidate sources remain the [application Caddy/FPM template](../../../agents/wcpd/candidate/planner/templates/applications/Caddyfile-fpm.tmpl), [FrankenPHP classic template](../../../agents/wcpd/candidate/planner/templates/applications/Caddyfile-classic.tmpl) and [Modern Worker template](../../../agents/wcpd/candidate/planner/templates/Caddyfile-modern.tmpl), beside the Go renderer. Do not copy them here.

Future profiles must declare provider/module versions, listener allocation, forwarding trust, administration access, TLS ownership, state paths and reload/recovery. The existing preview disables administration and automatic HTTPS; a production profile needs its own reviewed policy.

Caddy does not interpret native .htaccess. Adaptation does not provision modules/TLS, reserve ports or prove application compatibility. Follow the [adopted strategy](../../../src/Module/Hosting/WEB-SERVER-STRATEGY.md), [web-server roadmap](../../../src/Module/Hosting/WEB-SERVER-ROADMAP.md) and [official options](https://caddyserver.com/docs/caddyfile/options).

See the [resource index](../../README.md), [Core structure](../../../STRUCTURE.md) and existing owning use cases before adding real source. This README does not select a package, create a machine-path allowlist or authorize privileged operations.
