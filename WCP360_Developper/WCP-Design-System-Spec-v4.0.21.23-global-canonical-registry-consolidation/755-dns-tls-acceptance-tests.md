# 755 --- DNS / TLS Acceptance Tests

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 2**

Ce document dérive des décisions et contrats déjà présents dans la
V3.5/V3.6/V3.7. Les règles `650–669` restent normatives et prioritaires.

-   zone/record CRUD scoped ;
-   invalid zone rejected before reload ;
-   primary/secondary + TSIG authorization ;
-   DNSSEC lifecycle selon capability ;
-   HTTP-01 et DNS-01 ;
-   wildcard only with compatible challenge ;
-   renewal and deployment verification ;
-   expired/failed renewal health ;
-   cross-tenant denial ;
-   secrets absent from API/log/audit.
