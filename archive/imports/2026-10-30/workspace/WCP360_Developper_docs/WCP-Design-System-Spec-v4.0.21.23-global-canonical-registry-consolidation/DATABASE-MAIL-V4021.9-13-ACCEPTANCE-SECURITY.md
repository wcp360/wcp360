# V4.0.21.9 --- Database / Cache / Mail P0 Acceptance Matrix

## Database/cache

-   account A cannot enumerate/use DB, DB user, grant or cache of B;
-   generated DB identities cannot collide cross-account;
-   DB admin/root credentials are never exposed to WCP-USER;
-   typed grant cannot escalate outside allowed privilege profile;
-   restore cannot target unauthorized account/database;
-   Redis/Memcached is not publicly exposed by default;
-   cache memory/connection limits are observed and reconcilable;
-   generic SQL/Redis command execution is absent.

## Mail/security

-   account A cannot manage mailbox/domain/filter of B;
-   mailbox password is never retrievable after set/rotation;
-   alias/forwarding loops are detected;
-   quota exhaustion has explicit delivery behavior;
-   Sieve invalid script cannot activate;
-   antispam/AV outage follows declared failure policy;
-   DKIM private key is never returned/logged;
-   DKIM rotation preserves overlap/rollback policy;
-   DNS auth UI distinguishes desired from observed;
-   SSO ticket is single-use, short-lived and mailbox-scoped;
-   queue support does not expose message body by default;
-   UI/API/CLI resolve identical authorization and entitlement
    decisions.
