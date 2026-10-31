# 1091 --- MVP Build Plan

## Statut

**CANONICAL --- DEVELOPER BLUEPRINT --- V3.9**

Ce document traduit les contrats `IMPLEMENTATION-READY` V3.8 en
organisation de code et ordre de construction. Il ne modifie pas les
décisions normatives de sécurité `650–669`.

Ordre : 1. Accounts/Plans + System inventory. 2. Web/PHP + Domain. 3.
DNS/TLS. 4. Database. 5. File Manager minimal. 6. Backup baseline. 7.
Security/SSH baseline. 8. Monitoring/notifications. 9. WCP-USER scoped
surface.

MVP prouve le cycle : créer account/domain → website/PHP → DNS/TLS → DB
→ fichiers → backup → monitoring, avec audit/RBAC/jobs. Mail, CMS
toolkit, reseller/billing/migration peuvent suivre avant V1 selon
capacité équipe.
