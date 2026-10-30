# 1160 --- Exact PHP Package Tree --- Sprint 0

## Statut

**CANONICAL --- V4.0 IMPLEMENTATION FOUNDATION --- SPRINT 0**

Ce document matérialise le Sprint 0 défini par `1090-sprint0-plan.md`.
Les normes `650–669` et contrats `700–1093` restent prioritaires en cas
de conflit.

``` text
apps/wcp-admin/
├── public/index.php
├── src/Http/
├── src/Bootstrap/
└── config/

apps/wcp-user/
├── public/index.php
├── src/Http/
├── src/Bootstrap/
└── config/

packages/php/
├── domain/
│   ├── Identity/
│   ├── Authorization/
│   ├── ControlPlane/
│   └── Audit/
├── application/
│   ├── Identity/
│   ├── Nodes/
│   ├── Jobs/
│   └── ServiceAccounts/
├── contracts/
│   ├── Persistence/
│   ├── Daemon/
│   ├── Clock/
│   └── Secrets/
└── infrastructure/
    ├── PostgreSQL/
    ├── Http/
    ├── Session/
    ├── DaemonClient/
    └── Logging/
```

Composer PSR-4 et dependency direction sont contrôlés en CI. `domain` ne
dépend pas d'`infrastructure`.

## V4.0.12 normative deepening

- `LIFE-4012-08-OS-PACKAGE-LIFECYCLE.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.
