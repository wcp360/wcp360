# V4.0.21.1 --- Acceptance Oracle Correction

## Finding

The acceptance registry is materially better, but its
`Exact action family` column still contains descriptions such as
`zone/record create-update-delete-reconcile`,
`DB create + user + backup + restore + delete`, and
`create/suspend/resume/rotate/delete`.

These are not exact Operation IDs and do not bind every expected Job/RPC
sequence.

## Corrected status

**P0-008 = PARTIAL / REOPENED**

## Required closure

Every test must reference exact Operation IDs from the complete API
registry and exact Job IDs, plus the expected Decision IDs/RPC
sequence/adapter IDs for each branch.
