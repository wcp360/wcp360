# 98 --- Extension V3.1 : Sécurité, App Platform & Automation

## But

Cette extension complète la V3.0 avec les familles explicitement
demandées après revue : sécurité/conformité, isolation/quotas, runtimes
modernes, Git/CI-CD, dépendances, LiteSpeed/OpenLiteSpeed, caches,
outils DB SSO, workers/queues, billing/provisioning et File Manager
complet.

## Nouveaux blocs

-   `291–294` Sécurité & conformité.
-   `300–306` Application Runtime Platform / Dev.
-   `310` LiteSpeed / OpenLiteSpeed.
-   `320–322` Redis, Memcached, outils DB SSO.
-   `330–331` Workers / queues / RabbitMQ.
-   `340–341` Billing & Provisioning.
-   `350` File Manager complet.

## Architecture WCP-USER / WCP-ADMIN

Les technologies ne sont pas dupliquées : WCP-ADMIN configure moteurs,
capacités, politiques et limites ; WCP-USER consomme les fonctions qui
lui sont attribuées dans son scope.

## Principe fournisseur

ClamAV, Imunify360, Maldet, CloudLinux/LVE, CageFS, LiteSpeed, Redis,
Supervisor, RabbitMQ, WHMCS, Blesta, HostBill, phpMyAdmin, pgAdmin et
Adminer sont traités comme **adaptateurs/capabilities**, pas comme
dépendances obligatoires du cœur WCP.
