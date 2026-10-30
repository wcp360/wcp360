# 1003 --- Module Boundary Map

## Statut

**CANONICAL --- DEVELOPER BLUEPRINT --- V3.9**

Ce document traduit les contrats `IMPLEMENTATION-READY` V3.8 en
organisation de code et ordre de construction. Il ne modifie pas les
décisions normatives de sécurité `650–669`.

Modules métier V1 : `identity`, `accounts`, `plans`, `domains`, `dns`,
`tls`, `web`, `php`, `mail`, `databases`, `cache`, `files`, `backup`,
`security`, `ssh`, `apps`, `developer`, `migration`, `billing`,
`monitoring`, `notifications`, `audit`, `support`.

Chaque module possède : Domain, Application, Contracts, Infrastructure
et tests. Les dépendances cross-module passent par
services/interfaces/events documentés ; pas d'accès aux tables privées
d'un autre module par commodité.
