# V4.0.21.1 --- Logical Schema Completeness Correction

## Finding

The V4.0.21 resource registry lists field names and several invariants
but does not consistently define logical type, nullable/required status,
enum domain, cardinality, foreign-key ownership semantics or mutation
authority for every field.

Therefore P0-001 cannot be certified PASS yet.

## Corrected status

**P0-001 = PARTIAL / REOPENED**

## Required closure

Each CORE resource needs a field dictionary:
`field_id, logical_type, required, nullable, immutable, default semantics, enum/range, reference target, cardinality, sensitive class, writer authority, invariant IDs`.

No SQL is required at this stage.
