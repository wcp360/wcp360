# 659 --- Command & Action Registry

## Statut

**CANONICAL / NORMATIVE --- V3.5**

Les termes **MUST**, **MUST NOT**, **SHOULD**, **SHOULD NOT** et **MAY**
définissent le niveau d'exigence WCP. Toute exception à une règle
MUST/MUST NOT exige une décision d'architecture documentée et une revue
sécurité.

## Registry

Les actions privilégiées sont enregistrées par identifiant stable, par
exemple `nginx.reload`, `php.pool.write`, `account.create`, `quota.set`.

Chaque entrée définit : permission, paramètres/schema, adapter,
executable/API connue, timeout, idempotence, locks, audit class,
validation, verify et recovery.

## Services

Si une action générique `service.restart` existe, `service_id` MUST
provenir d'un `ServiceRegistry` fermé ; jamais d'un nom d'unité fourni
librement.
