# 1060 --- Adapter SDK Blueprint

## Statut

**CANONICAL --- DEVELOPER BLUEPRINT --- V3.9**

Ce document traduit les contrats `IMPLEMENTATION-READY` V3.8 en
organisation de code et ordre de construction. Il ne modifie pas les
décisions normatives de sécurité `650–669`.

Familles d'adapters : OS/package/service, network/firewall, webserver,
PHP, DNS/provider, ACME, mail, database, cache, backup/destination,
SSH/security scanner, registrar, runtime/process, migration source,
billing connector, telemetry/notification.

Tous exposent metadata/version/capabilities/health et les opérations
spécifiques contractées. `detected` n'implique jamais `certified`.
