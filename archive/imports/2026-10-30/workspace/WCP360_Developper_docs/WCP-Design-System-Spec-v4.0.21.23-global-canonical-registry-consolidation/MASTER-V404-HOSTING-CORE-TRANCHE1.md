# MASTER V4.0.4 --- Hosting Core Deep Specification --- Tranche 1

## Portée

Accounts / Plans / Reseller → Domains → Website/Web stack →
Nginx/Apache/LiteSpeed → PHP/PHP-FPM.

## Décisions structurantes

-   HostingAccount est l'unité technique de délégation ; ResellerAccount
    est un scope parent.
-   Plan/entitlement/quota/runtime-limit sont distincts.
-   Domain, DNSZone, RegistrarDomain, Website, MailDomain et Certificate
    sont des ressources séparées.
-   Website est la source métier ; VirtualHost est une projection
    engine-specific.
-   Nginx/Apache/LiteSpeed sont des adapters avec matrices de
    capabilities.
-   Aucun textarea de configuration root arbitraire n'est une
    fonctionnalité tenant.
-   PHP runtime, pool, INI et extensions ont des lifecycles distincts.
-   Config web/PHP est appliquée atomiquement par génération avec
    last-known-good et reconciliation.
-   Suspension, termination et purge sont des états/workflows distincts.
-   WCP-USER ne contrôle jamais les services web globaux ni les packages
    PHP globaux.

## Prochaine tranche

**V4.0.5 --- Hosting Core Deep Specification --- Tranche 2 : DNS +
TLS/ACME + Databases + Redis/Memcached.**
