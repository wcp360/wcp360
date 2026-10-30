# WCP --- Hosting Plan & Feature Matrix

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

## Types d'entitlement

-   `BOOLEAN`: autorisé/interdit.
-   `LIMIT`: entier, octets, débit, fréquence ou durée.
-   `ENUM_SET`: versions/runtimes/options autorisés.
-   `POLICY_REF`: politique contrôlée.
-   `FEATURE_STATE`: ENABLED/DISABLED pour les plans ; les états DEMO
    sont gérés séparément.

## Plan

Un plan possède
`plan_id, tenant_id, code, display_name, version, status, feature_set_version, limits, policy_refs`.

## Edition WCP-ADMIN

L'administrateur peut créer un plan depuis zéro, dupliquer un plan,
versionner ses paramètres, comparer deux versions, prévisualiser les
comptes impactés et publier la nouvelle version.

## Affectation

Une affectation de plan conserve
`account_id, plan_id, assigned_plan_version, assignment_state, effective_from, assigned_by`.

## Modification d'un plan

Une modification ne doit pas appliquer silencieusement une réduction
destructive. Le système calcule un `PlanChangeImpact` : compatible,
over-limit, requires-migration, blocked. Les ressources existantes ne
sont jamais supprimées pour satisfaire automatiquement une réduction.
