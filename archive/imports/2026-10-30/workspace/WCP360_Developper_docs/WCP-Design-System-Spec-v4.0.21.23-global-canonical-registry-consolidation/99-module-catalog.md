# 99 --- Catalogue maître des modules techniques WCP-ADMIN

## But

Cette série complète la couche Design System par une **couche métier
système**. Les modules ci-dessous sont conçus à partir des fonctions
présentes dans les documents source WCP-ADMIN ; les extensions non
explicitement décrites sont marquées V3 dans leur fichier.

## Familles

-   `100–109` Système : OS, users, packages, process, services, storage,
    cron, logs, lifecycle.
-   `120–136` Web/PHP : stack, Apache, Nginx, PHP/FPM/extensions/INI.
-   `140–145` Bases : MariaDB/MySQL, PostgreSQL, users, remote access,
    replication.
-   `150–156` Mail : MTA, Dovecot, queue, délivrabilité,
    anti-spam/relay.
-   `170–176` DNS : BIND, zones, DNSSEC, cluster, PTR.
-   `180–186` Réseau/Sécurité : interfaces, routes, firewall, Fail2ban,
    WAF, diagnostics.
-   `190–194` Accès : SSH, sudo, FTP/SFTP.
-   `200–203` TLS : certificats, ACME, CSR/keys, policies.
-   `210–214` Backup/DR.
-   `220–232` Storage/Cluster.
-   `240–251` Compute/Containers/SDN/LB.
-   `260–290` Monitoring, intégrations, API, extensions, Security
    Center.

## Contrat documentaire

Chaque module définit : origine, objectif, pages, ressources, actions,
métriques/logs, permissions/audit, contrat backend, états UI et critères
d'acceptation.

## Extension V3.1

-   `291–294` Antivirus, isolation/quota, audit avancé, MFA.
-   `300–306` Node.js, Python, Ruby, Go, dependency managers, Git/CI-CD.
-   `310` LiteSpeed/OpenLiteSpeed.
-   `320–322` Redis/Memcached/DB web tools SSO.
-   `330–331` workers/queues/RabbitMQ.
-   `340–341` billing/provisioning.
-   `350` File Manager complet.

## Extension V3.2

-   `360–362` WordPress Toolkit, auto-installeur, Runtime GUI.
-   `363–364` File Manager UX et DB Tools SSO.
-   `365–367` Multi-tenant, Plans, Impersonation.
-   `368–371` API-first, GraphQL optionnel, Billing connectors, Event
    Automation.
-   `372–375` Webmail, Autoconfig, mail-domain bootstrap,
    Sieve/Vacation.
-   `376–378` White-label, Quick Launcher, Themes.

## Extension V3.3

-   `380–383` Domain/Registrar.
-   `390–395` Migration.
-   `400–404` Mail avancé.
-   `410–412` DB avancé.
-   `420–422` Backup assurance.
-   `430–433` IPAM/Metering.
-   `440–451` Host/Storage security & health.
-   `460–463` Enterprise SSO.
-   `470–473` Support.
-   `480–481` Licensing.

## Extension V3.4

-   `490–495` Linux networking / firewall / VPN extension.
-   `500–504` Authoritative DNS.
-   `510–515` Mail Deliverability.
-   `520–525` Telemetry/Monitoring adapters.
-   `530–533` Backup engines/destinations.
-   `540–542` Shared/Object storage.
-   `550–554` SSH management.
-   `560–561` MFA recovery / SCIM.
-   `570–572` Service Accounts / CLI / SDK-IaC.
-   `580–592` Datacenter, Kubernetes, Terraform/OpenTofu, Ansible
    extension points.
-   `600–611` Plugin trust / SIEM / audit integrity.
-   `620–621` Advanced ACME/TLS.

## Normalisation V3.5

-   `630–641` WCP Control Plane : architecture, agent, mTLS, HA, schema
    migrations, upgrades, secrets, DR, offline nodes, event/jobs,
    bootstrap, observability.
-   `MASTER-CANONICAL-SOURCE-MAP.md` : source de vérité par sujet.
-   `MASTER-IMPLEMENTATION-READINESS.md` : READY/PARTIAL/DECISION
    REQUIRED/OPTIONAL.
-   `MASTER-OPEN-DECISIONS.md` : décisions à fermer avant production.

## Série normative sécurité 650--669

Secure development, language policy, filesystem layout, naming, Linux
identities, WCP-ADMIN/WCP-USER isolation, PHP/Go security, privileged
broker, action registry, path security, atomic config, secrets, systemd,
supply chain, dependencies/SBOM, deployment, managed configs, hosting
filesystem et security review.

## V3.6 Readiness

Voir `MASTER-DOCUMENT-REGISTRY.md`, `MASTER-DEPENDENCY-GRAPH-V36.md`,
`MASTER-V1-STANDALONE-HOSTING-PROFILE.md` et ADR-001 à ADR-014.

## V3.7 Phase 1 --- Implementation Contracts

-   `700–706` Control Plane
-   `710–713` System/OS
-   `720–722` API Platform
-   `730–733` Network
-   `740–745` Web/PHP

## V3.7 Phase 2 --- Implementation Contracts

-   `750–755` DNS/TLS
-   `760–765` Mail
-   `770–775` Database/Redis/Memcached
-   `780–785` Backup/Storage
-   `790–795` Security/SSH

## V3.7 Phase 3 --- Implementation Contracts

-   `800–804` Domains / Registrar
-   `810–814` Accounts / Plans / Reseller
-   `820–823` File Manager
-   `830–834` Apps / CMS / WordPress
-   `840–844` Developer / Git / CI-CD / Runtimes
-   `850–853` Migration Center
-   `860–864` Billing / Provisioning
-   `870–874` Monitoring / Telemetry

## V3.8 Closure

-   `880–883` Identity / SSO / MFA
-   `890–892` Notifications
-   `900–902` Scheduler / Workers
-   `910–912` Audit / Logs
-   `920–921` Support / Knowledge Base
-   `930–931` Licensing / Marketplace gates
-   `940–942` HA / Compute / AI readiness gates

## V3.9 Developer Blueprint

-   `1000–1003` repository / PHP / Go / module boundaries
-   `1010–1011` PostgreSQL / IDs
-   `1020–1031` OpenAPI / routes / RBAC
-   `1040–1060` events / jobs / IPC / adapters
-   `1070–1081` tests / CI / dev / packaging
-   `1090–1093` Sprint 0 / MVP / V1 / Definition of Done

## V4.0 Implementation Foundation

-   `1100` Sprint 0 gates
-   `1110–1111` PostgreSQL
-   `1120–1121` permissions/auth bootstrap
-   `1130–1132` API/OpenAPI/HTTP
-   `1140–1151` IPC + privileged Action Registry
-   `1160–1171` PHP/Go/systemd/runtime
-   `1180–1182` CI/OS/security tests
-   `1190–1191` install/upgrade + acceptance

## V4.0.10 normative deepening

- `OPS-4010-06-LOGGING-CANONICAL.md`
- `OPS-4010-07-LOG-PIPELINE-RETENTION.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
