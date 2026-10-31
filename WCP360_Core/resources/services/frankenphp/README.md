# FrankenPHP Worker service profile

Owner: Hosting. Status: optional experimental plan generator; not an installed or production-qualified service.

The [application-server guide](../../../../documentation/development/APPLICATION-SERVERS.md) defines the global default, per-site choice, compatibility acknowledgement, isolation requirements and platform boundaries. PHP-FPM remains the initial default.

The executable candidate keeps the actual templates together with their Go renderer under [agents/wcpd/candidate/planner/templates](../../../agents/wcpd/candidate/planner/templates): Caddyfile.tmpl, nginx-frankenphp.conf.tmpl and frankenphp.service.tmpl. Do not copy them here or create a second renderer. The planner emits inert text only.

Each site requires a separate unprivileged process/Unix identity, a site-owned Unix socket, bounded resources and an explicitly compatible worker application. Caddy automatic HTTPS and admin access are disabled in this edge-proxied profile. wcpd/wcp-privd remain separate system-operation components.

Debian container probes are candidate evidence only. Ubuntu and AlmaLinux adapters, package/extension matrices, SELinux integration, live switching, health/drain/rollback and full host qualification remain to implement.
