# 657 --- Go Security Standard

## Statut

**CANONICAL / NORMATIVE --- V3.5**

Les termes **MUST**, **MUST NOT**, **SHOULD**, **SHOULD NOT** et **MAY**
définissent le niveau d'exigence WCP. Toute exception à une règle
MUST/MUST NOT exige une décision d'architecture documentée et une revue
sécurité.

## MUST

-   `context.Context` pour deadlines/cancellation des opérations
    externes.
-   tailles/timeouts/concurrence bornés aux frontières IPC/réseau ;
-   erreurs internes distinguées des messages externes ;
-   exécutables système résolus par adapter/registry et chemins connus ;
-   aucune construction `sh -c`/`bash -c` avec données externes ;
-   protocoles IPC versionnés et schémas validés.

## Dependencies

`go.mod` et `go.sum` versionnés ; revue des dépendances et
vulnérabilités dans CI.
