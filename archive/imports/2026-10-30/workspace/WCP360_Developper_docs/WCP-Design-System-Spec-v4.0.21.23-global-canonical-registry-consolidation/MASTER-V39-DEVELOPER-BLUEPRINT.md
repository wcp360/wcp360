# MASTER V3.9 --- Developer Blueprint

## But

Passer du référentiel d'architecture à un plan de construction sans
inventer une nouvelle architecture parallèle.

## Chemin de construction

``` text
Sprint 0
 Identity/RBAC + PostgreSQL + Audit + Jobs + IPC + packaging
        ↓
System/OS + Adapter SDK
        ↓
Accounts/Plans + Domains
        ↓
Web/PHP + DNS/TLS + Database
        ↓
Files + Backup + Security + Monitoring
        ↓
WCP-USER
        ↓
Mail + Reseller + Apps + Developer tooling
        ↓
Migration + Billing + Support + SSO
        ↓
V1 Standalone
```

## Source de vérité

Les documents `650–669`, `700–942` restent les exigences. Les documents
`1000–1093` disent où les implémenter et dans quel ordre.
