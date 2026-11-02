# WCP360 Premium Catalogue

**141 planning proposals: 44 business modules and 97 provider connectors. Updated November 2, 2026.**

This is the root entry point for optional PRO packages. Each proposal has a README, a synchronized JSON record and acceptance criteria. All remain `design`, `installable: false` and `source_model: undecided`.

## Full premium inventories

| Catalogue | Scope | Authoritative data |
| :--- | :--- | :--- |
| [Business modules](WCP360_Modules/CATALOG.md) | **44 proposals / 16 categories**: advanced hosting, applications, storage, containers, Kubernetes, VM, performance, security and operational workflows. | [Module JSON](WCP360_Modules/CATALOG.json) |
| [Provider connectors](WCP360_Plugins/premium/CATALOG.md) | **97 proposals / 19 categories**: independent service, platform and protocol integration profiles. | [Connector JSON](WCP360_Plugins/premium/CATALOG.json) |

The two JSON catalogues own package IDs and metadata; this page provides navigation. The [149-entry general design catalogue](PLUGIN_CATALOG.md) is a separate mapped inventory and includes seven Core engine-adapter designs. Its count is not added to these 141 proposals.

## Named integrations added to the premium catalogue

| Area | Proposal records |
| :--- | :--- |
| Multi-server interop and policy | [Enhance](WCP360_Plugins/premium/enhance-interop-pro/README.md), [CSF/lfd — requested ClusterCSF behavior](WCP360_Plugins/premium/csf-lfd-policy-pro/README.md) |
| Containers and PaaS | [Portainer](WCP360_Plugins/premium/portainer-management-pro/README.md), [Coolify](WCP360_Plugins/premium/coolify-applications-pro/README.md), [Dokku](WCP360_Plugins/premium/dokku-applications-pro/README.md) |
| Storage | [GlusterFS](WCP360_Plugins/premium/glusterfs-storage-pro/README.md), [CephFS](WCP360_Plugins/premium/cephfs-storage-pro/README.md), [Ceph RBD](WCP360_Plugins/premium/ceph-rbd-storage-pro/README.md), [Nextcloud WebDAV](WCP360_Plugins/premium/nextcloud-webdav-pro/README.md) |
| Routing and proxy | [Traefik](WCP360_Plugins/premium/traefik-routing-pro/README.md), [Nginx Proxy Manager](WCP360_Plugins/premium/nginx-proxy-manager-pro/README.md) |
| Defensive security | [BitNinja](WCP360_Plugins/premium/bitninja-defense-pro/README.md), [Imunify360](WCP360_Plugins/premium/imunify360-defense-pro/README.md) |
| Notification providers | [Telegram](WCP360_Plugins/premium/telegram-notifications-pro/README.md), [Twilio SMS](WCP360_Plugins/premium/twilio-sms-pro/README.md), [Brevo email](WCP360_Plugins/premium/brevo-email-pro/README.md) |
| Database services | [MySQL/MariaDB](WCP360_Plugins/premium/external-mysql-mariadb-pro/README.md), [Galera](WCP360_Plugins/premium/galera-cluster-pro/README.md), [Redis](WCP360_Plugins/premium/redis-service-pro/README.md), [MongoDB](WCP360_Plugins/premium/mongodb-service-pro/README.md) |
| Certificate authority | [External ACME issuer](WCP360_Plugins/premium/external-acme-authority-pro/README.md) |

The [DNS/TLS orchestration module](WCP360_Modules/dns-tls-orchestration-pro/README.md) was also added. Existing Cloudflare, CrowdSec, Git delivery, monitoring, backup and other proposals are in the full inventories above.

## Scope and implementation status

Read the [eleven-area platform register](documentation/development/PLATFORM-INTEGRATIONS-COVERAGE.md) for the requested functionality and verified provider distinctions, and the [infrastructure register](documentation/development/INFRASTRUCTURE-PREMIUM-COVERAGE.md) for multi-server, storage, Docker, Kubernetes, VPS/VM, booster/compression, CDN, cybersecurity and DDoS.

The [edition strategy](documentation/development/EDITIONS-AND-PREMIUM.md) keeps essential hosting, reseller plans/quotas, DNS/TLS, security, local administration and recovery in the [AGPL Core](WCP360_Core/LICENSING.md). See the [Core reseller guide](WCP360_Core/src/Module/Tenancy/RESELLER-PACKAGES.md) for those free foundations. Premium rights never replace current permissions, publisher trust or provider qualification.

These records prepare development; they supply no executable package, qualified provider or production release. Package source/licensing models remain undecided. The [Core status](WCP360_Core/STATUS.md) owns current implementation evidence.
