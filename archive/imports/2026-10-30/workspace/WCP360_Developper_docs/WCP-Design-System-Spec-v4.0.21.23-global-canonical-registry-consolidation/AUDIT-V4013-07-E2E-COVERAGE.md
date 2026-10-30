# V4.0.13 --- End-to-End Coverage Gate

## Workflows devant exister dans la future suite de tests

Account provisioning, domain/site creation, DNS/SSL, PHP/runtime switch,
DB lifecycle, mail domain/mailbox/deliverability, backup/restore,
WordPress staging/deploy, Git deployment, API idempotency, billing
suspend/unsuspend/package change, security quarantine, node
enrollment/drain/failover, rolling update et disaster recovery.

## Pour chaque workflow

Happy path + validation error + permission denied + quota/entitlement
denial + timeout + ambiguous side effect + partial success + retry +
concurrent modification + dependency unavailable + recovery/reconcile +
audit/telemetry verification.

## Gate

Une fonctionnalité ne devient implementation READY que si son scénario
E2E et ses cas limites sont reliés à des critères d'acceptation
testables.
