# 745 --- Web/PHP Acceptance Tests

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 1**

Ce document transforme les décisions V3.6 déjà acceptées en contrat de
développement. Il ne remplace pas les règles de
`MASTER-SECURE-DEVELOPMENT-STANDARD.md`.

-   Nginx-only baseline ;
-   Apache Tier 1 ;
-   PHP-FPM pool create/update ;
-   vhost invalid rejected ;
-   atomic config + successful reload ;
-   failed reload recovery ;
-   runtime removal blocked while referenced ;
-   WCP-USER cannot control global web service ;
-   generated config contains WCP ownership header ;
-   no runtime process can write `/opt/wcp/releases`;
-   audit/jobs/events and cross-tenant tests.

## V4.0.4 normative deepening

Les contrats approfondis suivants s'appliquent :

- `HOSTING-404-29-HOSTING-CORE-E2E-SCENARIOS.md`
- `HOSTING-404-30-HOSTING-CORE-TRANCHE1-READY-GATE.md`

En cas d'ambiguïté, la V4.0.4 précise la sémantique sans annuler les exigences de sécurité antérieures.
