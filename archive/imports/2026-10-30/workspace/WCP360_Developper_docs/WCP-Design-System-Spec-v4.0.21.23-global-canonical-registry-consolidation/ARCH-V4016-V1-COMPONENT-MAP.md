# V4.0.16 --- V1 Component Map

## Control-plane

-   `wcp-web`: WCP-ADMIN/WCP-USER/API.
-   `wcp-worker`: orchestration des Jobs non-root.
-   PostgreSQL WCP.
-   `wcpd`: privileged daemon.
-   scheduler/periodic reconciler logique.
-   audit/event/outbox subsystem.
-   health/readiness subsystem.

## Managed service adapters CORE

Nginx, PHP-FPM, PowerDNS, MariaDB, Postfix, Dovecot, Rspamd, nftables,
systemd, filesystem/quota, ACME/certificates, backup
local/S3-compatible.

## Optional adapters

Apache, PostgreSQL hosting, Redis/Memcached, WordPress, Git,
Node/Python, billing, transfer sources and external providers.

## Frontend

App-centric shell, global search, contextual resources, dark/light,
accessibility and role-aware navigation as specified by the Design
System corpus.
