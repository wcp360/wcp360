# WCP --- Demo Policy Model

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

## DemoPolicy

`demo_policy_id, name, version, module_rules[], dataset_profile, expires_at?, reset_strategy, branding_profile?`.

Chaque `module_rule` contient
`module_id, visibility, interaction_mode, allowed_demo_actions[]`.

## Interaction modes

-   `HIDDEN`: module absent de navigation/recherche/API discovery
    utilisateur.
-   `DISABLED`: visible mais actionnable seulement pour expliquer
    l'indisponibilité.
-   `READ_ONLY`: données de démonstration consultables.
-   `SIMULATED`: formulaires et workflows fonctionnent sur le store DEMO
    uniquement.
-   `ENABLED_REAL`: **interdit dans un DemoPolicy V1**.

## Administration

WCP-ADMIN peut activer/désactiver le mode DEMO par compte DEMO et
choisir/dupliquer une DemoPolicy. Un revendeur peut le faire uniquement
si `demo.manage` lui est délégué et dans ses politiques autorisées.
