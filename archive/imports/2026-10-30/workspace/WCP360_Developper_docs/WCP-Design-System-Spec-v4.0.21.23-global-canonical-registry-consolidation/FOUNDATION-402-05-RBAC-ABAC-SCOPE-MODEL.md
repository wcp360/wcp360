# V4.0.2 --- RBAC, Scope & Policy Model

## Statut

**NORMATIVE --- V4.0.2 FOUNDATIONS DEEP SPECIFICATION --- NO CODE**

Ce document approfondit les fondations transversales de WCP. Il ne
choisit pas de framework, de bibliothèque ni de format exécutable. Toute
implémentation future devra respecter ce contrat.

## Évaluation canonique

Authentication → account/session state → permission → tenant/resource
scope → entitlement → resource lifecycle → step-up/MFA → policy →
action.

## Principes

Deny by default. Une permission n'est pas un rôle. Un rôle est un
ensemble versionné de permissions. Les rôles système ne sont pas
réinterprétés par les modules.

## Scopes

`global`, `reseller`, `tenant/account`, `resource`, éventuellement
`self`. Toute requête doit résoudre un scope effectif avant action.

## Délégation revendeur

Un revendeur ne peut déléguer que ce qu'il possède et seulement les
permissions marquées délégables. La délégation ne permet jamais
d'élargir quota, scope ou privilège root.

## Impersonation

Conserver `real_actor` et `effective_actor`. Step-up requis pour
démarrer une impersonation sensible. Bannière UI permanente, durée
bornée, justification, audit début/fin et actions.

## Service accounts

Pas de login interactif ; permissions minimales ; credentials
rotatifs/révocables ; scope explicite ; audit distinct.
