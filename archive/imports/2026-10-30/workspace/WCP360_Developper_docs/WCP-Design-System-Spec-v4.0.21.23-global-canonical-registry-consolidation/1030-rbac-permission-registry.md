# 1030 --- RBAC Permission Registry

## Statut

**CANONICAL --- DEVELOPER BLUEPRINT --- V3.9**

Ce document traduit les contrats `IMPLEMENTATION-READY` V3.8 en
organisation de code et ordre de construction. Il ne modifie pas les
décisions normatives de sécurité `650–669`.

Le registre central versionné contient les permissions déjà définies par
les contrats : identity, accounts, plans, domains, system, network,
web/php, dns/tls, mail, database/cache, backup, security/ssh, apps,
developer, migration, billing, monitoring, notifications, audit,
support.

Chaque permission déclare : ID stable, description, resource scope,
surfaces autorisées, délégabilité reseller, step-up éventuel.

Un rôle est une collection de permissions ; le code métier ne teste pas
des noms de rôles codés en dur lorsque la permission est le vrai
contrat.
