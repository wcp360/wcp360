# V4.0.21.22 --- Error / Audit Consistency

The registry-era corpus contains **53** extracted stable error-like
identifiers.

Semantic error classes are strong, but family-specific errors remain
distributed. Before GLOBAL READY, a global error namespace/ownership
table must prevent two families from assigning different semantics or
HTTP/CLI behavior to the same code.

Audit semantics are coherent across families: initiator/effective actor,
operation/resource, decision/reason, correlation/job, safe before/after
metadata, outcome; secret values are excluded.
