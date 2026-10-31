# 1090 --- Sprint 0 --- Engineering Foundation

## Statut

**CANONICAL --- DEVELOPER BLUEPRINT --- V3.9**

Ce document traduit les contrats `IMPLEMENTATION-READY` V3.8 en
organisation de code et ordre de construction. Il ne modifie pas les
décisions normatives de sécurité `650–669`.

## Objectif

Obtenir un skeleton installable/testable sans fonctionnalités
d'hébergement complètes.

Livrables : monorepo, CI, PHP apps séparées, Go binaries skeleton,
PostgreSQL migrations, Identity/RBAC minimal, request/correlation IDs,
audit pipeline, Job/Outbox skeleton, Unix IPC authenticated, SecretStore
interface, package/release skeleton, Tier-1 VM CI smoke tests.

Exit : login admin sécurisé → API health → job no-op typé → wcpd →
broker test action non destructive → audit corrélé.
