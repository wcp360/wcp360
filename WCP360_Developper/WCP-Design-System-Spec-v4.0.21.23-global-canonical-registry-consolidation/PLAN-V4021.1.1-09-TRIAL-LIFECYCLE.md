# WCP --- Trial Account Lifecycle

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

## TrialPolicy

`trial_policy_id, duration, allowed_plan_id, limits, outbound_mail_policy, payment_conversion_rule, grace_period, retention_period`.

## Sécurité

Par défaut : SSH désactivé, API tokens limités/désactivables, outbound
mail fortement limité, pas de délégation revendeur, quotas faibles, rate
limits renforcés et surveillance anti-abus.

## Expiration

Avant expiration : notifications configurables. À expiration : blocage
des nouvelles mutations, suspension contrôlée des services selon
politique, conservation temporaire des données, puis purge éligible.

## Conversion

La conversion vers STANDARD est transactionnelle côté control plane :
validation du nouveau plan → impact report → affectation →
reconciliation des limites → sortie de TRIAL. Aucune recréation
arbitraire des ressources n'est nécessaire si elles sont compatibles.
