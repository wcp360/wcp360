# V4.0.21.18 --- Redis / Memcached Registry

## Resources and operations

`CACHE-CREATE`, `CACHE-UPDATE`, `CACHE-ROTATE-CREDENTIAL`,
`CACHE-FLUSH`, `CACHE-RESTART`, `CACHE-DELETE`, `CACHE-RECONCILE`.

Cache is tenant-scoped and entitlement/quota/capability gated. Network
exposure defaults to private/local service policy; public
unauthenticated exposure is forbidden.

Redis logical DB numbers are not considered a strong tenant isolation
boundary. Strong multi-tenant isolation requires
provider/process/container/account boundary according to the certified
host profile. `CACHE-FLUSH` is destructive and confirmation/step-up
policy applies.
