# 1080 --- Local Development Environment

## Statut

**CANONICAL --- DEVELOPER BLUEPRINT --- V3.9**

Ce document traduit les contrats `IMPLEMENTATION-READY` V3.8 en
organisation de code et ordre de construction. Il ne modifie pas les
décisions normatives de sécurité `650–669`.

Le dev local peut containeriser PostgreSQL et dépendances non
privilégiées. Les tests root/systemd/network réels s'exécutent dans VM
dédiées : un container de dev ne prouve pas la certification OS.

Fixtures utilisent domaines/IPs de documentation et secrets factices.
