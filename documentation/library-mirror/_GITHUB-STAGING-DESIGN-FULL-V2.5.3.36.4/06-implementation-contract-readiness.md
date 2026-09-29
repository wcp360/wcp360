# Implementation Contract Readiness

| Contract area | Status | Freeze decision |
|---|---|---|
| Permission identifiers | READY FOR CONTRACT | Namespace frozen at design level; backend may refine only with one-to-one traceability. |
| Audit event schema | READY FOR CONTRACT | Actor/resource/action/result/correlation/reason/source required. |
| Job state + idempotency | READY FOR CONTRACT | Canonical states and unknown-outcome handling defined. |
| Approval state contract | READY FOR CONTRACT | Expiry/invalidation/consumption/recheck defined. |
| Notification contract | READY FOR CONTRACT | Severity/category/dedup/routing/delivery state defined. |
| API versioning/scopes | READY FOR CONTRACT | Scoped tokens and versioned contract required. |
| Service config schema model | READY FOR CONTRACT | Schema-first provenance/validation/diff/apply/result flow defined. |
| Accessibility acceptance | READY FOR IMPLEMENTATION TESTS | Final conformance must be measured in production UI. |
| CSS architecture | IMPLEMENTATION PREP | May refactor only without changing locked appearance. |

## Mandatory order before implementation

1. Freeze API/resource naming.
2. Freeze permission identifiers and action-to-permission mapping.
3. Freeze job/event/audit schemas.
4. Freeze service-config schema envelope.
5. Freeze daemon operation allowlist and Unix-socket request contract.
6. Only then prepare repository/application skeletons.