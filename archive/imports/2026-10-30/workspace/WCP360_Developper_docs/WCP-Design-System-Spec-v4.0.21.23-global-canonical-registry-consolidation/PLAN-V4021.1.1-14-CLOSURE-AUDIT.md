# WCP --- Plan/Entitlement/Demo Closure Audit

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

## Résultat

La lacune identifiée avant V4.0.21.2 est désormais spécifiée : -
plans/packages administrables ; - feature lists par module ; -
quotas/limites ; - overrides par compte ; - plafonds revendeur ; -
résolution effective déterministe ; - modes
STANDARD/DEMO/TRIAL/SUSPENDED ; - DemoPolicy par module ; - isolation
forte de la simulation ; - lifecycle TRIAL ; - RBAC/API/Audit IDs
réservés ; - tests d'acceptation ; - UX WCP-ADMIN ; - dépendances et
sémantique de désactivation.

## Important

Ces documents **réservent** plusieurs Operation IDs mais ne prétendent
pas fermer P0-002. Ils doivent être intégrés dans l'inventaire API
exhaustif de V4.0.21.2.

## Verdict

Cette famille fonctionnelle est **READY FOR V4.0.21.2 DETERMINISTIC
INTEGRATION**, mais le repository reste bloqué.
