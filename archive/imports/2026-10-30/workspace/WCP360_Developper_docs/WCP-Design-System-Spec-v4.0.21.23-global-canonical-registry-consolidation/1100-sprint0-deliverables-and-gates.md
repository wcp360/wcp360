# 1100 --- Sprint 0 Deliverables & Gates

## Statut

**CANONICAL --- V4.0 IMPLEMENTATION FOUNDATION --- SPRINT 0**

Ce document matérialise le Sprint 0 défini par `1090-sprint0-plan.md`.
Les normes `650–669` et contrats `700–1093` restent prioritaires en cas
de conflit.

## Résultat attendu

Une installation WCP vide mais sécurisée doit pouvoir : démarrer
WCP-ADMIN, authentifier un administrateur local, autoriser une requête
API, créer un Job typé non destructif, le traiter via `wcpd`, appeler
une action de diagnostic fermée dans `wcp-privd`, retourner un résultat
et produire un `AuditEvent` corrélé.

## Gates

1.  Repository/CI vert.
2.  Migrations PostgreSQL initiales appliquées et rollback de
    développement testé.
3.  Aucun accès shell depuis PHP.
4.  IPC inaccessible aux pools PHP.
5.  Permission Registry chargé et refus par défaut.
6.  Secrets absents des logs/fixtures.
7.  Units systemd hardenées.
8.  Tests cross-tenant et broker négatifs verts.
9.  Package installable sur au moins une VM Tier 1, puis smoke matrix
    sur les quatre OS Tier 1.
