# V4.0.21.21 --- Reboot / Power Loss / Durable Job Recovery

Jobs classify steps as retryable, idempotent, compensatable or
manual-recovery. Durable checkpoints persist before/after privileged
side effects.

On restart: recover locks/leases safely → inspect RUNNING jobs → compare
desired/ observed/action receipts → resume, retry, compensate or mark
RECONCILIATION_REQUIRED.

No Job is declared succeeded solely because the process that launched it
exited. Duplicate execution must not duplicate OS users, DBs, DNS
records or destructive side effects.
