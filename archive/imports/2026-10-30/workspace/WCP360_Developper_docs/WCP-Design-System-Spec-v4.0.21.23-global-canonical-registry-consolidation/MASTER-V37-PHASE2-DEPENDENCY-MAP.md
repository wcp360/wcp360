# MASTER V3.7 Phase 2 Dependency Map

## Carte

``` text
Phase 1
Control Plane / API / System / Network / Web-PHP
        │
        ├── DNS/TLS 750–755
        │      └── ACME ↔ Web challenge / DNS provider
        │
        ├── Mail 760–765
        │      └── DNS/TLS + System + Storage
        │
        ├── DB/Cache 770–775
        │      └── System packages + Storage + Backup
        │
        ├── Backup/Storage 780–785
        │      └── SecretStore + Network + Jobs
        │
        └── Security/SSH 790–795
               └── Network anti-lockout + System + Audit
```

## Cross-dependencies critiques

-   Mail dépend de DNS/PTR/SPF/DKIM/DMARC et TLS.
-   ACME DNS-01 dépend d'un DNS adapter/provider autorisé.
-   DB restore dépend du Backup contract.
-   Security/SSH partage FirewallPolicy avec Network sans créer une
    seconde source de vérité.
-   Toutes les credentials passent par SecretStore.
