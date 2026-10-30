# V4.0.21.18 --- DB Governor / Quota / Isolation Closure

Logical quotas (number of DBs/users/storage policy) are distinct from
runtime resource governance.

`DB-GOVERNOR-MAPPING-RECONCILE` maps WCP account/database principal to
provider governor identity when `DB_RESOURCE_GOVERNOR` exists.
CloudLinux MySQL Governor is an adapter, not canonical identity.

Cross-tenant shared DB principals are forbidden where accurate
accounting/ isolation requires unique mapping. Governor failure cannot
silently remove the platform security floor; capability/readiness
becomes degraded/unavailable.
