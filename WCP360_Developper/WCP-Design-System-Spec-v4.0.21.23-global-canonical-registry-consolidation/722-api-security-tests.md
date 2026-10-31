# 722 --- API Security Acceptance Tests

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 1**

Ce document transforme les décisions V3.6 déjà acceptées en contrat de
développement. Il ne remplace pas les règles de
`MASTER-SECURE-DEVELOPMENT-STANDARD.md`.

-   BOLA/IDOR cross-tenant sur chaque ressource ;
-   scope escalation refusée ;
-   mass assignment refusé ;
-   pagination/filter allowlists ;
-   rate limits ;
-   replay idempotency ;
-   expired/revoked credentials ;
-   credential rotation ;
-   CSRF pour sessions Web ;
-   CORS/origin selon surface ;
-   secrets absents des erreurs/audit/logs.

## V4.0.9 normative deepening

- `API-409-01-API-FIRST-CANONICAL-CONTRACT.md`
- `API-409-02-API-AUTH-SERVICE-ACCOUNTS.md`
- `API-409-05-IDEMPOTENCY-MUTATIONS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
