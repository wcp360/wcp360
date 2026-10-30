# 1161 --- Exact Go Package Tree --- Sprint 0

## Statut

**CANONICAL --- V4.0 IMPLEMENTATION FOUNDATION --- SPRINT 0**

Ce document matérialise le Sprint 0 défini par `1090-sprint0-plan.md`.
Les normes `650–669` et contrats `700–1093` restent prioritaires en cas
de conflit.

``` text
services/wcpd/
├── cmd/wcpd/main.go
└── internal/
    ├── controlplane/
    ├── jobs/
    ├── outbox/
    ├── policy/
    ├── inventory/
    ├── ipcclient/
    ├── health/
    └── store/

services/wcp-privd/
├── cmd/wcp-privd/main.go
└── internal/
    ├── protocol/
    ├── peercred/
    ├── registry/
    ├── actions/
    │   ├── systemidentity/
    │   ├── servicestatus/
    │   └── controlhealth/
    ├── executor/
    ├── verify/
    └── redaction/
```

Pas de package générique `utils`. Les interfaces sont placées près du
consommateur sauf contrat partagé explicitement stable.

## V4.0.12 normative deepening

- `LIFE-4012-08-OS-PACKAGE-LIFECYCLE.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.
