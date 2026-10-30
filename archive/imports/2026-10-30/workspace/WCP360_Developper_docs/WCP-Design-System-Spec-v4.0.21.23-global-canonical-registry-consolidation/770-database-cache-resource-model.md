# 770 --- Database / Cache Resource Model

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 2**

Ce document dérive des décisions et contrats déjà présents dans la
V3.5/V3.6/V3.7. Les règles `650–669` restent normatives et prioritaires.

## Ressources

`DatabaseServer`, `Database`, `DatabaseUser`, `DatabaseGrant`,
`DatabaseBackupRef`, `RedisInstance`, `MemcachedInstance`,
`CacheCredentialRef`.

## Isolation

Database ownership et grants sont liés à Account/Tenant. Redis/Memcached
exposent uniquement les modes d'isolation réellement supportés par
l'adapter.

## Secrets

DB/cache passwords write-only et référencés via SecretStore.
