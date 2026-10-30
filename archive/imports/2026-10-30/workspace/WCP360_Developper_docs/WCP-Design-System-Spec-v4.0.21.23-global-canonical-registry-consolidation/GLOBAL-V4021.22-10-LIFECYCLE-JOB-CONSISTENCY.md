# V4.0.21.22 --- Lifecycle / Job / Reconciliation Consistency

Common lifecycle semantics are coherent: provisioning, active,
suspended/locked, error/degraded, deleting and observed noncompliance
are separated where needed.

Durable Job requirements are now present across system-backed families.
Global gap: lifecycle transition tables remain distributed. Before
GLOBAL READY, one authoritative transition registry must map each
canonical resource state, allowed transition Operation IDs, guards, Job
requirement and terminal/recovery state.
