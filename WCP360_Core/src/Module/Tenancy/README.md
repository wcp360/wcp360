# Tenancy

**Status: documented module boundary; application source is not implemented by this directory.**

Tenant/account ownership, reseller delegation, entitlements, subscriptions and resource ceilings.

Start here when changing: **Account ownership, plans and delegated quotas.**

The [local licensing design reservation](design/licensing/README.md) prepares commercial entitlement ownership without implementing a license client or changing Core availability. Package admission and lifecycle remain owned by ExtensionManagement. See the [Core structure](../../../STRUCTURE.md) and [extension resource index](../../../resources/extensions/README.md).

Use the common [module layout](../README.md) for Domain, Application, Infrastructure and UI code. Separate tenant identity from Linux process identity; delegated scope cannot widen ROOT policy.

- [Core navigation index](../../../NAVIGATION.md)
- [System-service resources](../../../resources/services/README.md)
- [Core implementation and acceptance](../../../../documentation/development/CORE-ENGINE.md)
