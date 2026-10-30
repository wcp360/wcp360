# WCP --- Demo Simulation Security Boundary

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

## Pipeline

`Request → AuthN → RBAC/Scope → AccountModeGuard → DemoPolicy → Simulation Router → Demo Store → Demo Audit`.

## Invariant critique

Si `account_mode=DEMO`, le dispatcher réel de Jobs refuse toute
opération marquée `SYSTEM_MUTATION`, `PROVIDER_MUTATION`,
`SECRET_MATERIAL_CREATE` ou `EXTERNAL_DELIVERY`.

## Interdictions

Un compte DEMO ne peut pas déclencher : création Linux, écriture
Nginx/PHP-FPM réelle, systemd mutation, PowerDNS mutation, ACME réel, DB
réelle, mailbox réelle, S3 réel, SSH/SFTP réel, cron réel, secret de
production, webhook externe ou email sortant réel.

## Anti-contournement

Le contrôle est serveur-side et commun à l'UI, REST API, GraphQL futur,
webhooks internes et automation. Un appel API direct ne contourne pas le
DemoPolicy.

## Simulation

Les identifiants simulés portent un namespace/type distinct. Ils ne sont
jamais acceptés comme IDs de ressources réelles par les services métier.

## Reset

Le Demo Store peut être remis à un snapshot de référence sans toucher
aux données STANDARD/TRIAL.
