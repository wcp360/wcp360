# WCP --- Reseller Package Delegation

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

## Hiérarchie

`Platform → Reseller Ceiling → Reseller-created Plan → Customer Account → Account Override`.

## Le revendeur peut

Créer/dupliquer/archiver ses plans, choisir les modules dans son
plafond, fixer des quotas inférieurs ou égaux à son plafond, affecter
ses plans à ses descendants et créer des overrides autorisés.

## Le revendeur ne peut pas

Activer un module interdit par WCP-ADMIN, dépasser CPU/RAM/disque/bande
passante/objets autorisés, déléguer une capability qu'il ne possède pas,
désactiver un security floor, créer un mode DEMO qui permet des
mutations réelles non autorisées, ni voir les plans d'un autre arbre.

## Réduction du plafond

Une réduction produit un impact report. Les comptes devenus over-limit
passent en état `ENTITLEMENT_NONCOMPLIANT` sans suppression automatique.
Les créations supplémentaires sont bloquées jusqu'à remédiation.
