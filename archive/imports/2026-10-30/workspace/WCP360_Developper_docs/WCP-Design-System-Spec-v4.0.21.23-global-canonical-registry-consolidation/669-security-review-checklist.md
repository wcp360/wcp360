# 669 --- Security Review Checklist

## Statut

**CANONICAL / NORMATIVE --- V3.5**

Les termes **MUST**, **MUST NOT**, **SHOULD**, **SHOULD NOT** et **MAY**
définissent le niveau d'exigence WCP. Toute exception à une règle
MUST/MUST NOT exige une décision d'architecture documentée et une revue
sécurité.

## Checklist obligatoire

-   [ ] Actor et tenant identifiés
-   [ ] Permission/scopes définis
-   [ ] Linux identity connue
-   [ ] Root réellement nécessaire
-   [ ] Paramètres typés, longueurs et allowlists
-   [ ] Aucun shell/commande libre
-   [ ] Aucun chemin client direct
-   [ ] Symlink/TOCTOU évalués
-   [ ] Secrets minimisés et redacted
-   [ ] Écriture atomique/config validation
-   [ ] Idempotence et concurrence
-   [ ] Timeout/rate limit
-   [ ] Recovery/rollback
-   [ ] Audit avec request/correlation ID
-   [ ] Tests d'autorisation cross-tenant
-   [ ] Tests négatifs du broker/adapters
-   [ ] Dependencies/SBOM/scans passés
-   [ ] Acceptance criteria de sécurité satisfaits

Un échec MUST bloque la promotion `READY` lorsqu'il concerne une
exigence applicable.
