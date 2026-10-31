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
