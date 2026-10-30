# 1141 --- IPC Error / Timeout Semantics

## Statut

**CANONICAL --- V4.0 IMPLEMENTATION FOUNDATION --- SPRINT 0**

Ce document matérialise le Sprint 0 défini par `1090-sprint0-plan.md`.
Les normes `650–669` et contrats `700–1093` restent prioritaires en cas
de conflit.

Classes : protocol error, unauthorized peer, unknown action, invalid
params, precondition failed, execution failed, verification failed,
timeout, internal broker error.

Timeout client n'implique pas automatiquement que l'action n'a pas été
exécutée. `wcpd` réconcilie via Job/evidence avant retry lorsque
l'action peut avoir produit un effet.

Le broker tue/termine proprement les sous-processus qu'il possède selon
policy de l'action et borne stdout/stderr.
