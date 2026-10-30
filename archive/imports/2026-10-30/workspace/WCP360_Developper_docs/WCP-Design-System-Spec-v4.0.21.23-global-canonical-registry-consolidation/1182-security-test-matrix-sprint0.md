# 1182 --- Sprint 0 Security Test Matrix

## Statut

**CANONICAL --- V4.0 IMPLEMENTATION FOUNDATION --- SPRINT 0**

Ce document matérialise le Sprint 0 défini par `1090-sprint0-plan.md`.
Les normes `650–669` et contrats `700–1093` restent prioritaires en cas
de conflit.

Catégories obligatoires : unauthenticated API, CSRF, session fixation,
revoked session, RBAC deny, cross-tenant synthetic fixture, SQL
injection validation, mass assignment, rate limits, secret leakage, log
injection, IPC unauthorized peer, unknown action, oversized IPC, path
traversal, shell metacharacters, systemd socket permissions, release
immutability.

Chaque vulnérabilité corrigée ajoute un regression test.
