# 663 --- systemd Hardening

## Statut

**CANONICAL / NORMATIVE --- V3.5**

Les termes **MUST**, **MUST NOT**, **SHOULD**, **SHOULD NOT** et **MAY**
définissent le niveau d'exigence WCP. Toute exception à une règle
MUST/MUST NOT exige une décision d'architecture documentée et une revue
sécurité.

Chaque unit reçoit un profil adapté après test. Baseline à évaluer :
`NoNewPrivileges`, `PrivateTmp`, `PrivateDevices`, `ProtectSystem`,
`ProtectHome`, `ProtectKernelTunables`, `ProtectKernelModules`,
`ProtectKernelLogs`, `ProtectControlGroups`, `RestrictSUIDSGID`,
`LockPersonality`, `RestrictRealtime`.

## Filesystem

Préférer des `ReadWritePaths`/`ReadOnlyPaths`/`InaccessiblePaths`
explicites.

## Règle

Ne pas appliquer aveuglément la même sandbox à `wcp-privd`, `wcpd`,
workers et PHP ; chaque exception est justifiée.

## V4.0.3 normative deepening

Voir `SYSTEM-403-09-SYSTEMD-UNIT-LIFECYCLE.md`. Les exigences de ce contrat approfondi s'appliquent à cette spécification.
