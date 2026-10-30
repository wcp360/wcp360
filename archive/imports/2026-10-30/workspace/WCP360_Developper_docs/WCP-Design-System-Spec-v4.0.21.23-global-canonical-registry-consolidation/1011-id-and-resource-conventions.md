# 1011 --- ID & Resource Conventions

## Statut

**CANONICAL --- DEVELOPER BLUEPRINT --- V3.9**

Ce document traduit les contrats `IMPLEMENTATION-READY` V3.8 en
organisation de code et ordre de construction. Il ne modifie pas les
décisions normatives de sécurité `650–669`.

IDs publics opaques avec préfixes de type (`acc_`, `dom_`, `web_`,
`job_`, etc.). La valeur interne peut être UUID/ULID mais n'est pas un
contrat API.

Toutes ressources tenant-scoped portent un tenant/account scope
explicite. Linux username, domain name, email ou path ne servent jamais
de clé d'autorisation primaire.
