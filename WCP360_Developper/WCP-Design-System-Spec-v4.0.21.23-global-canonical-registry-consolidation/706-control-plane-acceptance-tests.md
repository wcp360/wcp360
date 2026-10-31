# 706 --- Control Plane Acceptance Tests

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 1**

Ce document transforme les décisions V3.6 déjà acceptées en contrat de
développement. Il ne remplace pas les règles de
`MASTER-SECURE-DEVELOPMENT-STANDARD.md`.

## Obligatoire

-   création/lecture d'un nœud et inventaire capability ;
-   job async success/failure/timeout/cancel ;
-   idempotency replay ;
-   optimistic concurrency conflict ;
-   outbox atomique avec mutation ;
-   audit créé pour toute mutation ;
-   service account credential affiché une seule fois puis non
    récupérable en clair ;
-   agent offline produit `stale/unknown`, jamais faux `healthy`;
-   aucune donnée cross-tenant via filtres/IDs devinés.
