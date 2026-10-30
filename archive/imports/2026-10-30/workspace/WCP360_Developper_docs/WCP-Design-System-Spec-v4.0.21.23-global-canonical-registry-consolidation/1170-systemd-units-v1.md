# 1170 --- systemd Units V1

## Statut

**CANONICAL --- V4.0 IMPLEMENTATION FOUNDATION --- SPRINT 0**

Ce document matérialise le Sprint 0 défini par `1090-sprint0-plan.md`.
Les normes `650–669` et contrats `700–1093` restent prioritaires en cas
de conflit.

## Units

`wcp-privd.service`, `wcpd.service`, `wcp-worker.service`, PHP-FPM
pools/units selon packaging, et migration/update units si nécessaires.

## Dépendances

`wcpd` démarre après réseau local requis et PostgreSQL disponible selon
architecture ; `wcp-privd` n'a pas de dépendance vers l'UI. Worker
dépend du control-plane store.

## Hardening baseline

`NoNewPrivileges` partout sauf incompatibilité documentée ;
`PrivateTmp`; `ProtectSystem`; `ProtectHome`; `ProtectKernelTunables`;
`ProtectKernelModules`; `ProtectControlGroups`; `RestrictSUIDSGID`;
`LockPersonality`; `RestrictRealtime`; `SystemCallArchitectures=native`;
filesystem read/write allowlists.

`wcp-privd` root reçoit une policy spécifique : ne pas activer
mécaniquement un hardening qui casse les actions nécessaires ;
documenter chaque writable path/capability.
