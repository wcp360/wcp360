# V4.0.21.7 --- Account / Reseller / Plan / Quota Acceptance Matrix

## P0

-   reseller cannot create account above account-count ceiling;
-   reseller cannot assign plan outside its allowed ceiling;
-   account override cannot exceed parent ceiling;
-   resource override cannot weaken security floor;
-   plan downgrade below usage never silently deletes;
-   suspension is reversible and preserves data;
-   account deletion cannot bypass retention/legal hold;
-   reseller closure cannot cascade-delete accounts implicitly;
-   Unix UID/GID allocation cannot collide with another managed
    identity;
-   WCP-USER cannot choose arbitrary system UID/GID/home;
-   quota reports `HARD` only after backend readback verifies
    enforcement;
-   unavailable cgroup/quota controller reports `UNAVAILABLE`;
-   plan assignment is idempotent;
-   repeated suspend/resume is convergent;
-   reconciliation detects manual drift;
-   DEMO never provisions system identity/cgroup;
-   TRIAL cannot exceed trial ceiling;
-   API/UI/CLI resolve identical effective entitlement/quota decisions.
