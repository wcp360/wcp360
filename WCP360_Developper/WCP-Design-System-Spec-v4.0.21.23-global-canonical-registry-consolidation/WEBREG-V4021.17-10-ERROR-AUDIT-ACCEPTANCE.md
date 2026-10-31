# V4.0.21.17 --- Web/PHP/TLS/DNS Errors & Acceptance

Additional stable errors: `DOMAIN_CONFLICT`,
`DOCUMENT_ROOT_OUTSIDE_MANAGED_ROOT`, `WEB_CONFIG_INVALID`,
`RUNTIME_UNAVAILABLE`, `FPM_POOL_INVALID`, `ACME_CHALLENGE_FAILED`,
`CERTIFICATE_INVALID`, `CERTIFICATE_KEY_MISMATCH`,
`DNS_PROVIDER_UNAVAILABLE`, `DNS_RECORD_INVALID`,
`DNSSEC_DELEGATION_PENDING`.

P0: cross-tenant domain mutation denied; traversal/symlink root escape
denied; invalid web config never replaces good config; FPM pool cannot
run as another tenant; certificate private key not exposed; renewal
failure alerts before expiry; DNS record validation prevents malformed
provider mutation; DNSSEC is not reported healthy with inconsistent DS;
duplicate Jobs remain idempotent.
