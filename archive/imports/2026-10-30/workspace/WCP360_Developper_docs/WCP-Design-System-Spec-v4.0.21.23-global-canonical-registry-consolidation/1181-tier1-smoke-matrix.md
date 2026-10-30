# 1181 --- Tier 1 Smoke Matrix

## Statut

**CANONICAL --- V4.0 IMPLEMENTATION FOUNDATION --- SPRINT 0**

Ce document matérialise le Sprint 0 défini par `1090-sprint0-plan.md`.
Les normes `650–669` et contrats `700–1093` restent prioritaires en cas
de conflit.

OS : Debian 13 amd64, Ubuntu 24.04 LTS amd64, AlmaLinux 9 x86_64, Rocky
Linux 9 x86_64.

Smoke : package install, users/directories, PostgreSQL connectivity,
migrations, systemd start, admin health, local owner bootstrap,
authenticated `/me`, node inventory, no-op/diagnostic Job, IPC action,
audit correlation, restart persistence, uninstall/upgrade preflight
basics.

Un OS n'est `certified` qu'après matrice verte sur release candidate.
