# WCP --- Module Dependency & Disable Semantics

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

## Dépendances

Un module peut déclarer `requires[]`, `conflicts[]`,
`system_requirements[]`, `soft_dependencies[]`.

Exemples : WordPress Toolkit requiert site + PHP + base de données
compatible ; phpMyAdmin SSO requiert MariaDB + SSO bridge ; mailbox
requiert mail-domain + auth/routing services.

## Désactivation

Désactiver un entitlement empêche les nouvelles actions mais ne supprime
jamais automatiquement les ressources existantes. Le module peut devenir
`EXISTING_ONLY` : consultation/gestion minimale nécessaire à
migration/suppression, sans création.

## Health

Un module entitlement ENABLED mais service indisponible devient
`UNAVAILABLE`, pas `DISABLED`. L'UI doit distinguer contrat commercial
et état opérationnel.
