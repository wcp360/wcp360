# WCP --- STANDARD, DEMO, TRIAL & SUSPENDED Account Modes

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

## STANDARD

Ressources réelles ; opérations permises par RBAC + entitlements +
quotas.

## DEMO

Présentation/simulation. Aucun Job produisant une mutation
système/provider n'est autorisé. Les données de démonstration sont
isolées des ressources réelles.

## TRIAL

Vrai compte d'hébergement avec ressources réelles, limites fortes, date
d'expiration et politiques de risque spécifiques.

## SUSPENDED

Données conservées selon politique ; opérations de mutation client
bloquées sauf actions explicitement nécessaires à la
récupération/gestion administrative.

## Transitions

`DEMO → STANDARD` ne convertit pas implicitement les objets simulés en
ressources réelles. Une action explicite d'import/provisioning avec
prévisualisation est requise.

`TRIAL → STANDARD` peut conserver les ressources réelles après
validation d'un plan compatible.

`STANDARD → DEMO` est interdit pour un compte possédant des ressources
réelles ; créer un environnement DEMO distinct.

`TRIAL expiry → SUSPENDED_TRIAL → RETENTION → PURGE_ELIGIBLE`, avec
délais définis par politique.
