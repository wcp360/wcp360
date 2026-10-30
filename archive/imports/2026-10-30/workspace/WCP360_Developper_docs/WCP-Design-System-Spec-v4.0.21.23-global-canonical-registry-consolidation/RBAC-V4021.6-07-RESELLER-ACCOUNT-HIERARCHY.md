# V4.0.21.6 --- Platform → Reseller → Account Authorization Hierarchy

## Hierarchy

`Platform Admin → Reseller → Hosting Account → Resource`.

Authority is the intersection of: platform policy ∩ reseller ceiling ∩
role permissions ∩ binding scope ∩ plan/entitlement ∩ resource state ∩
security floor.

## Reseller

A reseller may create custom roles/packages only inside its delegated
ceilings. It cannot grant itself or a customer a capability that
WCP-ADMIN withheld.

## Account

An account administrator can delegate account-scoped roles but cannot
cross account/tenant boundaries.

## Support

Support roles are explicit and narrow. Read-only support, billing
support and technical support are distinct permission bundles;
impersonation is not a substitute for broad permanent rights.
