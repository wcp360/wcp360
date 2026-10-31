# WCP --- Effective Entitlement Resolution

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

## Algorithme normatif

1.  Charger le Platform ceiling.
2.  Charger le Reseller ceiling applicable.
3.  Charger la version de plan affectée.
4.  Appliquer les overrides compte non expirés.
5.  Appliquer les overrides ressource uniquement pour les keys déclarées
    `resource_overridable`.
6.  Calculer les limites effectives par opérateur de composition.
7.  Appliquer l'Account Mode.
8.  Appliquer le Security Floor.
9.  Vérifier la disponibilité/health du module.
10. Produire `EffectiveEntitlementDecision`.

## Résultat

`decision_id, entitlement_key, effective_state/value, source_chain[], denied_by?, evaluated_at, account_version`.

## Composition

Pour les plafonds numériques : la valeur effective ne peut dépasser le
minimum des plafonds parents. Pour les ensembles : intersection. Pour
les booléens : un DENY d'un plafond non-overridable gagne. Le security
floor gagne toujours.

La décision est recalculée côté serveur ; le frontend ne fournit jamais
la valeur effective comme vérité.
