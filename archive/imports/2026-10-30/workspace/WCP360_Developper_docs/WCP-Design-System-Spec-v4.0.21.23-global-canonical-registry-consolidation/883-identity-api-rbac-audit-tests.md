# 883 --- Identity API / RBAC / Audit / Tests

## Statut

**CANONICAL --- V3.8 CLOSURE / IMPLEMENTATION CONTRACT**

Ce document complète le corpus existant. Les règles de sécurité
`650–669` et les contrats `700–874` restent normatifs.

Permissions : `identity.read/manage`, `roles.read/manage`, `sso.manage`,
`mfa.manage`, `sessions.manage`.

Tests : session fixation, MFA replay, recovery abuse, OIDC nonce/state,
invalid issuer/audience, role escalation via claims, SAML replay si
activé, LDAP TLS failure, cross-tenant identity access.

## V4.0.9 normative deepening

- `API-409-01-API-FIRST-CANONICAL-CONTRACT.md`
- `API-409-02-API-AUTH-SERVICE-ACCOUNTS.md`
- `API-409-05-IDEMPOTENCY-MUTATIONS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
