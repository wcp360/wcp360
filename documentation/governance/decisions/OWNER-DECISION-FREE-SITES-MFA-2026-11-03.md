# Owner product decision: Free websites and optional administrator MFA

**Recorded: 2026-11-03. Authority: explicit decisions supplied by the project owner during documentation consolidation. Scope: current product direction, not a runtime change or independent security acceptance.**

## Purpose and decision

The owner confirmed these two choices:

- **Free has no WCP360-imposed website-count limit.** The previous Free limit of 30 websites is superseded.
- **Administrator MFA is optional per administrator.** An administrator who enables MFA must continue to satisfy the enabled challenge and applicable controlled recovery rules. This product choice does not make an enabled factor optional at login.

These decisions apply to the current product documentation and presentation of WCP360. Existing runtime behavior, stored accounts, credentials, package entitlements and customer data are not changed by this document.

## Edition model and boundaries

| Offer | Website count | Included product scope |
| :--- | :--- | :--- |
| Free | Unlimited by WCP360 edition policy | Essential Core and base functionality, without optional premium modules. |
| PRO | Unlimited by WCP360 edition policy | Base functionality plus all eligible WCP360 premium modules as qualified packages become available. |

Physical capacity, storage, operating-system constraints, per-tenant resource plans and explicitly assigned customer quotas remain distinct from the WCP360 edition policy. Unlimited website count is not a promise of unlimited hardware resources or delivered functionality.

The confirmed PRO prices remain USD 6.50 monthly, USD 78 annually and USD 210 for thirty-six months. Product inclusion does not turn catalogue proposals into available, installable or independently licensed production packages. Trial duration, refund, renewal, offline entitlement and revocation policies require their own accepted contracts.

This document grants no new code licence, proprietary exception or commercial agreement. The original Core source remains governed by [its scoped licence policy](../../../WCP360_Core/LICENSING.md); every SDK, third-party component and optional package retains its applicable terms and status.

## MFA policy and contract reconciliation

The candidate already describes optional administrator MFA, disabled by default and enforced once an account enables it. The owner has selected this as the product direction to retain. Session/surface isolation, CSRF, current authorization, tenant and resource scope, revocation, action-bound reauthentication and audit remain applicable independently of this choice.

The selected historical [Security Baseline export](../../specifications/security/WCP360_Security_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md) contains mandatory-ADMIN-MFA language, including the authentication and sensitive-action passages. Preserve that exact owner artifact and its provenance. The current decision records the product-policy delta explicitly; a later reviewed contract amendment must define the resulting authentication/step-up behavior and acceptance cases.

This file is not a replacement canonical baseline, an amended frozen export, an independent security review or evidence that milestone M1 is complete. The existing [baseline manifest](../../manifests/WCP360-BM-0001.md), contract versions, evidence requirements and `CODE-BASELINE=NONE` remain recorded under their existing authority. The executable candidate and eventual production qualification are separate identities.

## Implementation and acceptance to reconcile

| Boundary | Required follow-up |
| :--- | :--- |
| Public pages and shop | Free must be described without the former 30-site edition limit; PRO inclusion and actual package availability must remain distinguishable. |
| Presentation metadata | Represent unlimited website count explicitly. A display flag is not a Core quota implementation, licensing proof or accepted entitlement protocol. |
| Licensing administration | Existing `max_instances` allocation capacity counts assigned installation identities. It is not a website limit and must not be cleared as a consequence of this decision. |
| Core and commercial integration | Keep entitlement, IAM authorization, package trust and installation consent separate. Preserve essential hosting, retained data and baseline local recovery across vendor failure or subscription expiry. |
| MFA enabled | Verify challenge enforcement, controlled recovery, revocation, races and sensitive-action behavior according to the reconciled contract. |
| MFA not enabled | Verify the explicitly selected password/reauthentication path and refusals without falsely claiming second-factor evidence. |
| Evidence | Bind every executed result to its exact revision and environment; retain unexecuted and unresolved cases as such. |

No Core, database, service, payment, installer or customer operation is executed by this decision. Documentation tests do not qualify these runtime behaviors.

## Related authorities

- [Current Core status](../../../WCP360_Core/STATUS.md) and [working guide](../../../WCP360_Core/WORKING-GUIDE.md).
- [Community/PRO strategy](../../development/EDITIONS-AND-PREMIUM.md).
- [Tenancy local licensing reservation](../../../WCP360_Core/src/Module/Tenancy/design/licensing/README.md).
- [Security policy](../../../SECURITY.md) and [canonical contract navigation](../../contracts/README.md).
- Hub `README_wcp_websited.md`, current edition pages and declarative presentation plan metadata are maintained in the separate Hub checkout.

The documentation consolidation records the decision and tracks its integration. It does not close the production gates or claim absence of vulnerabilities.
