# V4.0.21.6 --- Identity & Authentication Resource Dictionaries

## Statut

**NORMATIVE B1 CLOSURE --- IDENTITY FAMILY**

### `principal`

  ---------------------------------------------------------------------------------
  Field            Type                   Required Mutability    Notes
  ---------------- ------------- ----------------- ------------- ------------------
  id               UUID                        yes immutable     opaque identifier

  tenant_id        UUID                        yes immutable     tenant boundary

  principal_type   ENUM                        yes immutable     HUMAN,
                                                                 SERVICE_ACCOUNT,
                                                                 SYSTEM

  login            STRING                      yes controlled    normalized; tenant
                                                                 uniqueness policy

  display_name     STRING                      yes mutable       UI label

  status           ENUM                        yes mutable       INVITED, ACTIVE,
                                                                 LOCKED, SUSPENDED,
                                                                 DISABLED

  locale           STRING                       no mutable       UI preference

  timezone         STRING                       no mutable       UI/audit display
                                                                 only

  created_at       TIMESTAMP                   yes immutable     system

  version          UINT64                      yes mutable       optimistic
                                                                 concurrency
  ---------------------------------------------------------------------------------

### `credential`

Stores metadata and **secret references only**. Fields: `id`,
`principal_id`, `credential_type`, `secret_ref`, `status`, `created_at`,
`rotated_at`, `expires_at`, `version`. Types include PASSWORD_HASH_REF,
PASSKEY, RECOVERY_CREDENTIAL and SERVICE_SECRET_REF.

### `mfa_factor`

Fields: `id`, `principal_id`, `factor_type`, `label`, `secret_ref`,
`public_credential_data`, `status`, `enrolled_at`, `last_used_at`,
`version`. Supported policy classes: TOTP, WEBAUTHN/PASSKEY,
RECOVERY_CODE_SET; optional adapters may add factors without changing
the core model.

### `session`

Fields: `id`, `principal_id`, `tenant_id`, `session_family_id`,
`created_at`, `last_seen_at`, `expires_at`, `absolute_expires_at`,
`authn_level`, `step_up_until`, `ip_metadata`, `device_metadata`,
`status`, `revoked_at`, `revoke_reason`.

### `api_token`

Fields: `id`, `principal_id`, `tenant_id`, `token_prefix`, `secret_ref`,
`scopes`, `expires_at`, `last_used_at`, `status`, `created_at`,
`version`. Raw token material is returned only at creation and never
stored retrievably.

### `service_account`

Fields: `id`, `tenant_id`, `name`, `status`, `owner_principal_id`,
`allowed_scopes`, `created_at`, `version`. No interactive UI login by
default.

### `authn_event`

Append-only authentication event: `id`, `tenant_id`, `principal_id?`,
`event_type`, `result`, `reason_code`, `network_metadata`,
`device_metadata`, `created_at`, `correlation_id`. Public responses must
not expose whether an account exists.

### `recovery_challenge`

Fields: `id`, `principal_id`, `challenge_type`, `secret_ref`,
`created_at`, `expires_at`, `consumed_at`, `attempt_count`, `status`.
Single-use and short-lived.

## Invariants

Secrets are never fields containing plaintext credentials. Cross-tenant
references are forbidden. Disabling a principal revokes or invalidates
active access according to policy. Authentication state is
server-authoritative.
