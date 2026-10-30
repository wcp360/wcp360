# MASTER --- WCP Secure Development Standard

## Statut

**CANONICAL / NORMATIVE --- V3.5**

Les termes **MUST**, **MUST NOT**, **SHOULD**, **SHOULD NOT** et **MAY**
définissent le niveau d'exigence WCP. Toute exception à une règle
MUST/MUST NOT exige une décision d'architecture documentée et une revue
sécurité.

## Principes non négociables

1.  Least privilege et séparation stricte WCP-USER / WCP-ADMIN / wcpd /
    wcp-privd.
2.  Aucune interface Web ou PHP ne s'exécute en root.
3.  Aucune commande shell arbitraire ne traverse une API WCP.
4.  Le code installé est immuable pour les comptes runtime.
5.  Code, configuration, secrets, état, cache, logs, runtime et backups
    sont séparés.
6.  Seuls les répertoires `public/` des applications Web peuvent être
    servis par HTTP.
7.  Toute donnée externe est non fiable jusqu'à validation positive.
8.  Toute action sensible suit Authenticate → Authorize → Validate →
    Resolve → Execute → Verify → Audit.
9.  Les mutations critiques sont atomiques ou disposent d'un
    recovery/rollback explicite.
10. Une nouvelle capability root exige une revue sécurité.

## Architecture de référence

``` text
Internet
  ↓
Reverse proxy / HTTPS
  ├── WCP-ADMIN (PHP-FPM non-root)
  └── WCP-USER  (PHP-FPM non-root)
            ↓
     Domain services / RBAC
            ↓
        Typed API
            ↓
     wcpd (Go non-root)
            ↓
 Policy / Validation / Jobs
            ↓
         Adapters
            ↓
 wcp-privd (root minimal) → Linux
```

## Documents normatifs

`650–669` détaillent cette norme. En cas de conflit, le document le plus
spécifique s'applique, sauf s'il réduit une exigence MUST de ce MASTER
sans ADR accepté.
