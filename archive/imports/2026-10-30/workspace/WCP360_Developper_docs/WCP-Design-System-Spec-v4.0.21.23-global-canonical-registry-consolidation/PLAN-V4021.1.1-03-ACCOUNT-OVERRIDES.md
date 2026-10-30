# WCP --- Account Entitlement Overrides

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

## But

Autoriser WCP-ADMIN, et le revendeur dans son plafond, à personnaliser
un compte sans dupliquer tout son plan.

## Override

`override_id, account_id, entitlement_key, operation, value, reason, created_by, created_at, expires_at?, version`.

`operation ∈ {GRANT, DENY, SET_LIMIT, SET_ENUM_SET, RESET_TO_PARENT}`.

## Contraintes

-   un override ne dépasse jamais le Platform/Reseller ceiling ;
-   un override de sécurité ne peut pas affaiblir le security floor ;
-   une expiration retourne automatiquement à la valeur héritée ;
-   toute modification est auditée ;
-   l'UI affiche toujours **Effective value + Source + Parent value**.

## Exemple

`web.ssh = ENABLED`, source `ACCOUNT_OVERRIDE`, parent
`PLAN_STARTER=DISABLED`, autorisé seulement si le plafond revendeur et
plateforme permettent SSH.
