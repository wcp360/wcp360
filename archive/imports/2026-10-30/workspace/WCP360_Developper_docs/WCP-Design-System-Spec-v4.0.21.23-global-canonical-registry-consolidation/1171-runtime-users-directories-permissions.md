# 1171 --- Runtime Users, Directories & Permissions

## Statut

**CANONICAL --- V4.0 IMPLEMENTATION FOUNDATION --- SPRINT 0**

Ce document matérialise le Sprint 0 défini par `1090-sprint0-plan.md`.
Les normes `650–669` et contrats `700–1093` restent prioritaires en cas
de conflit.

Comptes : `wcp-admin`, `wcp-user`, `wcp-daemon`, `wcp-worker`,
`wcp-update`; tous system/nologin.

`/run/wcp/privd.sock` : root + groupe IPC dédié à `wcp-daemon`, jamais
aux PHP users. `/etc/wcp/secrets/*` : compartimenté par consommateur.
`/var/lib/wcp` : sous-répertoires par composant ; aucun write global.
`/opt/wcp/releases/*` : root:root, non writable runtime. Logs : journald
ou `/var/log/wcp` selon packaging, avec redaction commune.
