# V4.0.21.22 --- Global Cross-Registry Audit Method

## Scope

Corpus audited: **1416 Markdown documents** from V4.0.21.21.

This pass does not declare old literal markers open merely because they
still exist. Historical/audit documents are preserved. The audit
separates source history from current canonical registry contracts.

## Dimensions

Resource → Operation → Permission → Scope → Entitlement → Capability →
Lifecycle → Job → Privileged Action → Audit → Error → Acceptance.

Also checked: cross-family dependency direction, tenant isolation,
secret boundaries, desired/observed state, idempotency/reconciliation,
API/CLI/UI parity, installer/recovery and negative P0 cases.

## Conservative rule

A marker is only closed when a newer canonical contract actually covers
its subject. Ambiguous items remain manual review; they are not silently
normalized.
