# V4.0.21.23 --- Global Permission / Scope / Step-up Registry

## Global decision formula

`ALLOW = permission ∩ scope ∩ entitlement ∩ capability ∩ lifecycle ∩ security_floor`

## Scope grammar

`platform`, `host:<id>`, `reseller:<id>`, `account:<id>`, `site:<id>`,
`domain:<id>`, `resource:<type>:<id>` with ownership-derived narrowing.

## Actor classes

WCP-ADMIN, reseller/admin delegated actor, WCP-USER account actor,
API/service principal and internal system actor. Impersonation preserves
initiator and effective actor.

## Risk classes

-   R0 read/non-sensitive;
-   R1 ordinary tenant mutation;
-   R2 destructive/sensitive mutation;
-   R3 privileged/security/recovery mutation.

R2/R3 may require explicit confirmation, step-up, approval or
maintenance state according to the operation-specific contract.

## Closure rule

No implementation endpoint may invent a permission string. Every
Operation ID must be assigned to this registry before coding.
