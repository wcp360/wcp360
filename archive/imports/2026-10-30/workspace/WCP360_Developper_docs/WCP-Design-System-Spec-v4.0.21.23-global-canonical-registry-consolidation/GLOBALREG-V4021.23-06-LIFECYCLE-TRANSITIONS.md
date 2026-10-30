# V4.0.21.23 --- Global Lifecycle Transition Registry

## Canonical semantic states

Resource families may specialize names, but must map to: `PROVISIONING`,
`ACTIVE`, `SUSPENDED_OR_LOCKED`, `DEGRADED_OR_ERROR`, `DELETING`,
`DELETED_OR_ABSENT`, plus resource-specific transient states.

## Transition contract

Every transition row MUST contain: resource type; from-state; Operation
ID; guards; Job class; privileged action if any; expected observed
state; failure state; recovery path.

## Global invariants

-   suspension is not deletion;
-   ERROR does not silently erase desired state;
-   deletion observes retention/dependency policy;
-   unsupported capability blocks transition before privileged mutation;
-   recovery/reconcile is explicit after ambiguous interrupted side
    effects.
