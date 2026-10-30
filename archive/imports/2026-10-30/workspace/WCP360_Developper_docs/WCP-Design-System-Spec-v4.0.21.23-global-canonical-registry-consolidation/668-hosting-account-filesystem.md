# 668 --- Hosting Account Filesystem

## Statut

**CANONICAL / NORMATIVE --- V3.5**

Les termes **MUST**, **MUST NOT**, **SHOULD**, **SHOULD NOT** et **MAY**
définissent le niveau d'exigence WCP. Toute exception à une règle
MUST/MUST NOT exige une décision d'architecture documentée et une revue
sécurité.

## Layout par défaut

``` text
/home/<account>/
├── domains/<domain>/
│   ├── public_html/
│   ├── private/
│   ├── logs/
│   └── tmp/
├── mail/
├── backups/
└── tmp/
```

## Abstraction

Le modèle métier n'encode pas `/home` en dur. Un
`StorageLayoutAdapter`/PathResolver permet ultérieurement
`/srv/hosting`, datasets ZFS, NFS ou autre backend validé.

## Isolation

Les fichiers client ne sont jamais stockés sous `/opt/wcp` et un compte
ne peut résoudre/agir sur les chemins d'un autre tenant.

## V4.0.4 normative deepening

Les contrats approfondis suivants s'appliquent :

- `HOSTING-404-01-HOSTING-ACCOUNT-DEEP.md`
- `HOSTING-404-23-PHP-SECURITY-ISOLATION.md`

En cas d'ambiguïté, la V4.0.4 précise la sémantique sans annuler les exigences de sécurité antérieures.

## V4.0.7 normative deepening

- `SECOPS-407-01-FILE-ROOT-PATH-RESOLUTION.md`
- `SECOPS-407-07-PERMISSIONS-ACL-OWNERSHIP.md`

Ces contrats approfondissent cette surface et conservent les exigences antérieures compatibles.
