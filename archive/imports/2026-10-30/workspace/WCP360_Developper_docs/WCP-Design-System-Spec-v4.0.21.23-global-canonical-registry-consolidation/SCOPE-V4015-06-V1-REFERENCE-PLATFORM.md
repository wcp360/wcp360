# V4.0.15 --- Recommended V1 Reference Platform Boundary

## But

Réduire la première implémentation sans supprimer l'architecture
extensible.

## Profil de référence à figer par ADR

-   une distribution Linux et versions explicitement supportées ;
-   systemd ;
-   Nginx + PHP-FPM comme web baseline ;
-   MariaDB/MySQL comme DB baseline ;
-   Postfix + Dovecot + Rspamd comme mail baseline ;
-   DNS authoritative baseline choisi par ADR ;
-   ACME provider baseline ;
-   local filesystem + quotas selon filesystem certifié ;
-   backup local + au moins un remote adapter ;
-   single-node d'abord, interfaces cluster conservées.

## Principe

Le corpus reste plus large que la V1. La V1 n'a pas à implémenter tous
les adapters pour respecter l'architecture.
