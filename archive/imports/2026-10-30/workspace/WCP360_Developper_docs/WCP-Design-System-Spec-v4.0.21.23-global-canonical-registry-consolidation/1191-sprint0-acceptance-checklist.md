# 1191 --- Sprint 0 Acceptance Checklist

## Statut

**CANONICAL --- V4.0 IMPLEMENTATION FOUNDATION --- SPRINT 0**

Ce document matérialise le Sprint 0 défini par `1090-sprint0-plan.md`.
Les normes `650–669` et contrats `700–1093` restent prioritaires en cas
de conflit.

Sprint 0 est accepté uniquement si : - WCP-ADMIN et WCP-USER sont des
surfaces séparées, même si WCP-USER reste fonctionnellement vide ; -
premier owner créé sans credential par défaut ; - login/session/RBAC
fonctionnent ; - PostgreSQL migrations reproductibles ; - Job/outbox et
audit corrélés ; - `wcpd` authentifie le peer broker et inversement
selon contrat local ; - action registry ne contient aucune primitive
shell libre ; - PHP ne peut ouvrir le socket privilégié ; - package
s'installe proprement ; - CI et security matrix vertes ; - au moins une
VM Tier 1 exécute le scénario complet avant merge de fondation, puis les
quatre avant certification release.
