# WCP --- WCP-ADMIN Plan & Demo UX Contract

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

## Plan Builder

Interface par familles de modules, recherche, presets, dépendances
visibles, quotas, versions autorisées et aperçu des différences avant
publication.

## Effective Permissions Inspector

Pour un compte : valeur effective, source (`PLATFORM`, `RESELLER`,
`PLAN`, `ACCOUNT_OVERRIDE`, `RESOURCE_OVERRIDE`, `ACCOUNT_MODE`,
`SECURITY_FLOOR`), raison du deny et date d'expiration éventuelle.

## Demo Builder

WCP-ADMIN sélectionne les modules et leur état
HIDDEN/DISABLED/READ_ONLY/SIMULATED. Une alerte permanente indique
qu'aucune mutation réelle ne sera exécutée.

## Safety UX

Les actions sensibles demandent confirmation + impact preview. Les
réductions de quotas montrent les ressources over-limit. Aucun toggle UI
ne promet une activation si le serveur ou la politique parent la refuse.
