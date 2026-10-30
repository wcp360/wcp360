# 1051 --- Privileged Action Registry Code Layout

## Statut

**CANONICAL --- DEVELOPER BLUEPRINT --- V3.9**

Ce document traduit les contrats `IMPLEMENTATION-READY` V3.8 en
organisation de code et ordre de construction. Il ne modifie pas les
décisions normatives de sécurité `650–669`.

``` text
services/wcp-privd/internal/actions/
├── accounts/
├── filesystem/
├── packages/
├── services/
├── network/
├── web/
├── mail/
├── database/
└── security/
```

Chaque action contient schema validator, authorization/context
assertions pertinentes, executor, verify et tests négatifs. Ajouter une
action root exige review sécurité.
