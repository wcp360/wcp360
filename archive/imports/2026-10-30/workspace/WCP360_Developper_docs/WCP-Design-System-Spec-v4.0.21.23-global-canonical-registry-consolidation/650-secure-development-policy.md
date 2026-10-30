# 650 --- Secure Development Policy

## Statut

**CANONICAL / NORMATIVE --- V3.5**

Les termes **MUST**, **MUST NOT**, **SHOULD**, **SHOULD NOT** et **MAY**
définissent le niveau d'exigence WCP. Toute exception à une règle
MUST/MUST NOT exige une décision d'architecture documentée et une revue
sécurité.

## Gate obligatoire par fonctionnalité

Avant merge : acteur, permission, tenant/scope, Linux identity, besoin
root, paramètres/types/tailles, allowlists, chemins, symlinks/TOCTOU,
secrets, audit, rate limit, concurrence, idempotence, rollback/recovery
et tests négatifs doivent être évalués.

## MUST

-   Threat model proportionné pour toute nouvelle surface privilégiée ou
    exposée.
-   Code review obligatoire.
-   Tests d'autorisation inter-tenant.
-   Aucun secret dans code, fixtures publiques, logs ou messages
    d'erreur.
-   Les erreurs externes ne révèlent pas de détails système inutiles.

## Merge gate

Une fonctionnalité privilégiée ne peut être marquée `READY` sans
acceptance criteria de sécurité.
