# V4.0.21.18 --- Database / Cache Errors & P0 Acceptance

Stable errors include `DATABASE_CONFLICT`,
`DATABASE_PRINCIPAL_CONFLICT`, `DATABASE_GRANT_DENIED`,
`DATABASE_IMPORT_INVALID`, `DATABASE_RESTORE_CONFLICT`,
`DB_PROVIDER_UNAVAILABLE`, `DB_GOVERNOR_MAPPING_ERROR`,
`CACHE_PROVIDER_UNAVAILABLE`, `CACHE_EXPOSURE_DENIED`,
`SSO_TICKET_EXPIRED`, `SSO_TICKET_REPLAY`.

P0: cross-tenant grant denied; DB root secret never exposed; principal
rotation does not leak old/new secret; import cannot escape staging;
restore cannot target wrong tenant; Redis public unauthenticated
exposure denied; cache flush audited; governor mapping cannot cross
tenant; SSO ticket is single-use and scoped; duplicate mutation Jobs are
idempotent.
