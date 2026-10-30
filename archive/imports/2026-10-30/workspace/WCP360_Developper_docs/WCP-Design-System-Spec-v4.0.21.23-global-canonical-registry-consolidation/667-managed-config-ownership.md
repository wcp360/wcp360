# 667 --- Managed Configuration Ownership

## Statut

**CANONICAL / NORMATIVE --- V3.5**

Les termes **MUST**, **MUST NOT**, **SHOULD**, **SHOULD NOT** et **MAY**
définissent le niveau d'exigence WCP. Toute exception à une règle
MUST/MUST NOT exige une décision d'architecture documentée et une revue
sécurité.

WCP possède seulement les zones explicitement gérées par ses adapters,
par exemple `/etc/nginx/wcp/` lorsqu'il est supporté.

## Header

``` text
# Managed by WCP
# Resource-Type: <type>
# Resource-ID: <id>
# Generation: <n>
# Config-Schema: <version>
# DO NOT EDIT MANUALLY
```

## MUST NOT

Considérer tout `/etc/nginx`, `/etc/postfix`, `/etc/bind`, etc. comme
propriété WCP. Les fichiers externes sont préservés sauf contrat
d'adapter explicite.
