# 666 --- Production Deployment Standard

## Statut

**CANONICAL / NORMATIVE --- V3.5**

Les termes **MUST**, **MUST NOT**, **SHOULD**, **SHOULD NOT** et **MAY**
définissent le niveau d'exigence WCP. Toute exception à une règle
MUST/MUST NOT exige une décision d'architecture documentée et une revue
sécurité.

## Releases

Déployer dans `/opt/wcp/releases/<version>` puis basculer atomiquement
`current` après preflight. Conserver `previous` lorsque le rollback est
compatible.

## Étapes

Verify artifact → extract → owner/modes → dependency/config
compatibility → migration preflight → maintenance/drain si requis →
activate → health checks → commit.

## Rollback

Distinguer binaire, configuration et données. Ne jamais promettre un
rollback binaire si la migration de données n'est pas
backward-compatible.
