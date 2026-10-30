# 713 --- System/OS Acceptance Tests

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 1**

Ce document transforme les décisions V3.6 déjà acceptées en contrat de
développement. Il ne remplace pas les règles de
`MASTER-SECURE-DEVELOPMENT-STANDARD.md`.

Tests par OS Tier 1 applicable : inventory OS/kernel, services,
packages, user/group read; config/service preflight; package mutation;
service reload/restart; maintenance; permissions négatives; absence de
shell libre; audit; job states; stale telemetry.

Reboot/shutdown doivent être testés dans un environnement de validation
dédié avec confirmation renforcée.

## V4.0.3 normative deepening

Voir `SYSTEM-403-30-SYSTEM-ACCEPTANCE-READINESS.md`. Les exigences de ce contrat approfondi s'appliquent à cette spécification.
