# V4.0.21.7 --- Ownership, Delete, Retention & Non-destructive Downgrade

## Ownership graph

Reseller owns/delegates accounts; accounts own hosting resources; plans
do not own customer data. Plan deletion therefore cannot cascade to
sites, databases, mail or files.

## Downgrade

If observed usage exceeds a new limit: 1. accept/reject according to
policy; 2. mark `NONCOMPLIANT` when accepted; 3. prevent new allocations
that increase violation; 4. preserve existing data; 5. provide
remediation/impact report.

## Account deletion

Deletion requires inventory freeze, dependency graph,
retention/legal-hold evaluation, backup policy decision, ordered child
disposition, irreversible boundary and post-delete absence/retention
verification.

## Reseller closure

No implicit customer deletion. Accounts must be transferred, explicitly
terminated, or otherwise disposed through a named policy.
