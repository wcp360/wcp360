# 841 --- Runtime & Process Manager Contract

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 3**

Ce document complète les contrats existants. Les règles de sécurité
`650–669` et les contrats systèmes `700–795` restent normatifs.

Contrat : versions disponibles/certifiées, app entrypoint relative au
app root, env refs, working dir, start/restart/stop, health, logs.
Process backend peut être systemd user-service/Supervisor/autre adapter
décidé par capability. Aucun `.env` secret n'est renvoyé en lecture
claire.
