# MASTER V3.7 Phase 1 Dependency Map

# Scope

Cette passe transforme cinq familles V1 en contrats de développement.

``` text
Secure Development 650–669
        ↓
Control Plane 700–706
        ├─────────────→ API Platform 720–722
        ↓                       ↓
System/OS 710–713 ──────────────┤
        ↓                       │
Network 730–733                 │
        ↓                       │
Web/PHP 740–745 ← Package layer ┘
```

## Dépendances critiques

-   Control Plane dépend de PostgreSQL (ADR-003), SecretStore et
    Jobs/Outbox.
-   System/OS dépend du Control Plane, Action Registry,
    OS/package/service adapters.
-   API dépend Auth/RBAC, Service Accounts, Audit et Jobs.
-   Network dépend System/OS + anti-lockout/recovery.
-   Web/PHP dépend System packages, filesystem/path security, network
    ports, TLS et adapters Web/PHP.

## Gate

Aucune famille suivante ne doit contourner ces contrats en accédant
directement à Linux ou à PostgreSQL depuis l'UI.
