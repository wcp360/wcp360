# 775 --- Database / Cache Acceptance Tests

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 2**

Ce document dérive des décisions et contrats déjà présents dans la
V3.5/V3.6/V3.7. Les règles `650–669` restent normatives et prioritaires.

-   DB/user/grant isolation ;
-   password write-only ;
-   engine-specific capability handling ;
-   import/export bounded and audited ;
-   destructive drop confirmation/recovery policy ;
-   Redis/Memcached not public by default ;
-   quota/plan enforcement ;
-   cross-tenant denial ;
-   backup/restore integration.
