# WCP --- Plan, Entitlement, Feature & Account Mode Model

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

## Séparation des responsabilités

-   **RBAC** : qui peut demander l'action.
-   **Scope** : sur quelles ressources.
-   **Module availability** : le module est-il installé, sain et
    administrativement disponible.
-   **Entitlement** : le plan autorise-t-il la fonctionnalité.
-   **Quota/limit** : dans quelle quantité.
-   **Account override** : exception explicitement accordée ou retirée.
-   **Account mode** : STANDARD, DEMO, TRIAL ou SUSPENDED.
-   **Security floor** : interdictions non contournables par
    plan/revendeur/client.

## Résolution

`Platform ceiling → Reseller ceiling → Plan → Account override → Resource override autorisé → Account mode → Security floor`.

Une couche enfant ne peut jamais réactiver une fonctionnalité interdite
par un plafond parent ou par la sécurité plateforme.
