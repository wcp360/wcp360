# WCP --- Plan/Demo API, RBAC & Audit Contract

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

## Opérations à intégrer au registry API complet V4.0.21.2

IDs réservés : `PLAN-CREATE`, `PLAN-UPDATE`, `PLAN-CLONE`,
`PLAN-ARCHIVE`, `PLAN-ASSIGN`, `ENTITLEMENT-OVERRIDE-SET`,
`ENTITLEMENT-OVERRIDE-RESET`, `RESELLER-CEILING-UPDATE`,
`DEMO-POLICY-CREATE`, `DEMO-POLICY-UPDATE`, `DEMO-ACCOUNT-CREATE`,
`DEMO-ACCOUNT-RESET`, `TRIAL-ACCOUNT-CREATE`, `TRIAL-CONVERT`,
`TRIAL-EXTEND`.

## Capabilities

`plan.manage`, `plan.assign`, `entitlement.override`,
`reseller.ceiling.manage`, `demo.manage`, `trial.manage`.

Toutes les opérations de modification de plafond, override, mode ou
politique DEMO/TRIAL sont auditables. Step-up est obligatoire pour :
réduction de reseller ceiling, override accordant une fonctionnalité
sensible, conversion TRIAL→STANDARD par administrateur, modification
d'une DemoPolicy globale.

## Audit

Actions réservées : `plan.created`, `plan.changed`, `plan.assigned`,
`entitlement.override.changed`, `reseller.ceiling.changed`,
`demo.policy.changed`, `demo.account.reset`, `trial.created`,
`trial.extended`, `trial.converted`, `trial.expired`.

Les audits enregistrent ancienne/nouvelle valeur sûre, source de
décision, actor/effective actor, scope, reason lorsque requis, sans
secret.
