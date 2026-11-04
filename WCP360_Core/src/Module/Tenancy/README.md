# Tenancy

**Status: documented module boundary; application source is not implemented by this directory.**

Tenant/account ownership, entitlements, subscriptions and resource ceilings remain the essential boundary. The [November 3 reseller-scope decision](../../../../documentation/governance/decisions/OWNER-DECISION-COMMUNITY-RESELLER-2026-11-03.md) excludes reseller management/delegation from Community; optional future commercial placement, including PRO inclusion, is unconfirmed. Retained reseller design does not create an available package or change AGPL rights.

Start here when changing: **Account ownership, plans and delegated quotas.**

The [local licensing design reservation](design/licensing/README.md) prepares commercial entitlement ownership without implementing a license client or changing Core availability. Package admission and lifecycle remain owned by ExtensionManagement. See the [Core structure](../../../STRUCTURE.md) and [extension resource index](../../../resources/extensions/README.md).

The [retained reseller-package guide](RESELLER-PACKAGES.md) prepares plan templates, immutable versions, quotas, aggregate reservations, assignment, overrides and downgrade/recovery acceptance. Its earlier blanket statement that all reseller features remain free Core duties is superseded by the dated scope decision. Essential customer workspaces, ownership, ordinary resource-plan assignment/quotas, IAM, isolation, audit and recovery remain Core; reseller portfolios, child-plan sales and reseller-specific delegation are reserved outside Community. No builder, quota engine or provisioning implementation is introduced. Hosting-plan labels remain distinct from optional PRO entitlements, and current M4 status is unchanged.

Use the common [module layout](../README.md) for Domain, Application, Infrastructure and UI code. Separate tenant identity from Linux process identity; delegated scope cannot widen ROOT policy.

- [Core navigation index](../../../NAVIGATION.md)
- [System-service resources](../../../resources/services/README.md)
- [Core implementation and acceptance](../../../../documentation/development/CORE-ENGINE.md)
