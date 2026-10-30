# V4.0.21.14 --- Multi-node Primitives / HA Future Contract

## Baseline decision

WCP 1.0 normative baseline: **single production node**. Multi-node/HA is
not required for 1.0 readiness.

## Primitives required now

stable node ID, node enrollment certificate, node registry, heartbeat,
capability advertisement, maintenance/drain state, resource placement
metadata, failure-domain metadata and control-plane ownership.

## Future

scheduler/placement, replicated control plane, fencing, leader election,
rolling upgrades and service-specific clustering remain future
contracts. Single-node code must not encode `localhost == owner` as a
permanent invariant.
