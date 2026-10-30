# 733 --- Network Acceptance Tests

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 1**

Ce document transforme les décisions V3.6 déjà acceptées en contrat de
développement. Il ne remplace pas les règles de
`MASTER-SECURE-DEVELOPMENT-STANDARD.md`.

-   inventaire interfaces/adresses/routes ;
-   backend correctement détecté ;
-   config invalide rejetée avant apply ;
-   anti-lockout sur management route/IP ;
-   firewall change avec recovery ;
-   collision backend détectée ;
-   IPv4/IPv6 selon capability ;
-   audit/job/event complets ;
-   aucune entrée API transformée en commande libre.

## V4.0.14 normative deepening

- `BOOT-4014-07-NETWORK-IPAM-BOOTSTRAP.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
