# MASTER V4.0.21.6 --- Identity, Authentication & RBAC B1/B2 Closure

## Scope

This pass closes the canonical resource and operation contracts for the
Identity/Auth/RBAC family before moving to hosting service resources.

## New canonical resources

`principal`, `credential`, `mfa_factor`, `session`, `api_token`,
`service_account`, `authn_event`, `recovery_challenge`, `role`,
`permission`, `role_permission`, `role_binding`, `delegation`,
`impersonation_session`, `authorization_decision`.

## Normative compatibility

This deepens, rather than replaces, the existing corpus rules in
`02-rbac-contextes-permissions.md`,
`04-authentication-sessions-step-up.md`,
`294-mfa-2fa-access-security.md`,
`365-access-hierarchy-multitenancy.md`,
`367-impersonation-support-mode.md`, `703-control-plane-rbac.md`,
`1030-rbac-permission-registry.md` and
`1031-policy-evaluation-order.md`.

## Gate

Identity/Auth/RBAC family: **READY FOR CROSS-REGISTRY INTEGRATION**.
Global B1/B2: **NOT YET COMPLETE**. Repository/code: **BLOCKED**.
