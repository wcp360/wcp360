# MASTER V3.7 Phase 3 Dependency Map

## Carte principale

``` text
Accounts / Plans / Reseller 810–814
            │
            ├─────────────→ Domains 800–804
            │                    ├→ DNS/TLS
            │                    ├→ Web/PHP
            │                    ├→ Mail
            │                    └→ Registrar
            │
            ├→ File Manager 820–823
            │        └→ Path/File security
            │
            ├→ Apps/CMS 830–834
            │        ├→ Web/PHP + DB + TLS + Backup
            │        └→ File Manager
            │
            ├→ Dev/Git/Runtimes 840–844
            │        ├→ System + Web/PHP
            │        └→ Jobs/Secrets
            │
            ├→ Migration 850–853
            │        └→ all target domain services
            │
            └→ Billing/Provisioning 860–864
                     └→ Accounts/Plans/Domain orchestration

All families ─────────→ Monitoring/Telemetry 870–874
```

## Règles

Aucun module Phase 3 ne contourne les contrats Phase 1/2. Le
provisioning orchestre des services de domaine ; il ne reconstruit pas
directement des configs Linux.
