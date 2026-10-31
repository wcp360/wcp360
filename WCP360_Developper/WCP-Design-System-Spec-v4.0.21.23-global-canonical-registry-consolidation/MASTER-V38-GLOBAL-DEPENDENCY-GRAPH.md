# MASTER V3.8 --- Global Dependency Graph

``` text
Secure Development 650–669
          ↓
Identity / RBAC 880–883
          ↓
Control Plane 700–706 ─── Scheduler/Workers 900–902
          │                         │
          ├── API 720–722           │
          ├── Audit 910–912 ←───────┘
          ├── System 710–713
          │     ├── Network 730–733
          │     ├── Security/SSH 790–795
          │     └── Web/PHP 740–745
          │             ├── Apps/CMS 830–834
          │             └── Dev/Runtimes 840–844
          ├── DNS/TLS 750–755
          ├── Mail 760–765
          ├── DB/Cache 770–775
          ├── Backup/Storage 780–785
          └── Monitoring 870–874 ─→ Notifications 890–892

Accounts/Plans/Reseller 810–814
          ├── Domains/Registrar 800–804
          ├── File Manager 820–823
          ├── Apps/Dev
          └── Billing/Provisioning 860–864

Migration 850–853 → target domain services
Support/KB 920–921 → Identity + File security + Impersonation
```

## Hors chemin critique V1

Licensing commercial, Marketplace tiers, HA cluster, Compute/Kubernetes
avancé et IA ne sont pas requis pour rendre le profil Standalone V1
implémentable.
