# WCP --- Plan, Entitlement, Demo & Trial Acceptance Tests

## Statut

**NORMATIVE ADDENDUM --- V4.0.21.1.1 --- NO CODE --- REPOSITORY
BLOCKED**

## Objet

Cette passe ferme la conception fonctionnelle et de sécurité de la
gestion des plans, privilèges fonctionnels, modules, overrides,
revendeurs, comptes DEMO et TRIAL. Elle ne remplace pas RBAC : elle
ajoute la couche d'entitlements/features nécessaire à un panneau
d'hébergement comparable aux mécanismes de packages/feature lists des
panels professionnels.

## Principe fondamental

L'accès effectif à une action est accordé uniquement si **toutes** les
couches nécessaires l'autorisent :
`RBAC capability ∩ resolved scope ∩ module availability ∩ effective entitlement ∩ account mode ∩ resource state ∩ security policy`.

## Tests P0 réservés

-   `E2E-PLAN-001`: plan default est appliqué à un nouveau compte.
-   `E2E-PLAN-002`: account override modifie uniquement le compte ciblé.
-   `SEC-PLAN-003`: reseller ne dépasse jamais son ceiling.
-   `SEC-PLAN-004`: override ne contourne jamais security floor.
-   `E2E-PLAN-005`: réduction de plan sous usage produit NONCOMPLIANT
    sans suppression.
-   `E2E-DEMO-001`: DEMO SIMULATED produit une réponse simulée et zéro
    Job réel.
-   `SEC-DEMO-002`: appel API direct DEMO vers mutation réelle est
    refusé.
-   `SEC-DEMO-003`: aucun RPC wcpd/provider mutation n'est émis depuis
    DEMO.
-   `SEC-DEMO-004`: IDs DEMO sont refusés par les services réels.
-   `E2E-DEMO-005`: reset restaure uniquement le Demo Store.
-   `E2E-TRIAL-001`: TRIAL provisionne de vraies ressources dans ses
    limites.
-   `SEC-TRIAL-002`: expiration bloque les mutations et applique la
    politique de suspension.
-   `E2E-TRIAL-003`: conversion STANDARD conserve les ressources
    compatibles.
-   `SEC-TRIAL-004`: outbound mail/rate limits TRIAL restent effectifs.
-   `AUD-PLAN-001`: toute modification de plan/override/ceiling/mode
    produit l'audit attendu.

Chaque test devra être lié aux Operation IDs exacts de V4.0.21.2 avant
le Repository Entry Gate.
