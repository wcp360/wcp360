# MASTER V4.0.21.1.1 --- Plan, Entitlement, Demo & Trial Addendum

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

## Décision

WCP adopte un modèle comparable aux packages/feature lists des grands
panels, mais plus strictement séparé du RBAC et des limites de sécurité.

Le mode DEMO est un environnement de simulation contrôlé et **n'est
jamais un simple flag frontend**. Le mode TRIAL est au contraire un
véritable hébergement limité.

## Baseline

V4.0.21.1.1 complète V4.0.21.1. La prochaine passe reste V4.0.21.2, qui
devra intégrer ces contrats dans les registries exhaustifs
API/Data/Job/RBAC/Audit/Acceptance.

**NO CODE --- REPOSITORY BLOCKED.**
