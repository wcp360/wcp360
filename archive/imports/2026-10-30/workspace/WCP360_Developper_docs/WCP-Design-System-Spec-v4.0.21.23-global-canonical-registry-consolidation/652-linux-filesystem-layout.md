# 652 --- Linux Filesystem Layout

## Statut

**CANONICAL / NORMATIVE --- V3.5**

Les termes **MUST**, **MUST NOT**, **SHOULD**, **SHOULD NOT** et **MAY**
définissent le niveau d'exigence WCP. Toute exception à une règle
MUST/MUST NOT exige une décision d'architecture documentée et une revue
sécurité.

## Layout officiel

``` text
/opt/wcp/releases/<version>/   code immutable
/opt/wcp/current -> releases/<version>
/opt/wcp/previous -> releases/<version>

/etc/wcp/                      configuration hôte
/etc/wcp/secrets/              secrets, accès compartimenté
/var/lib/wcp/                  état persistant
/var/cache/wcp/                cache reconstructible
/var/log/wcp/                  logs
/run/wcp/                      sockets/PID/locks
/var/backups/wcp/              backup du control plane
/home/<account>/               données d'hébergement par défaut
```

## MUST NOT

Code WCP dans `/root`, `/home`, `/var/www`, document root générique ou
home d'un compte d'hébergement.

## Release

Les processus runtime MUST NOT écrire dans `/opt/wcp/releases/*`.
