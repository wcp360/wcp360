# V4.0.21.21 --- Capacity / Resource Pressure / Disk-Full Behavior

Platform monitors disk bytes, inodes, memory pressure, CPU/load,
process/PID pressure, DB/cache capacity, mail queue growth and backup
target capacity.

Low-space thresholds can gate high-risk writes/backups/deployments
before total exhaustion. Reserved/emergency operational capacity is a
Host Profile concern.

Disk-full/OOM scenarios must preserve control-plane/audit integrity as
far as possible and surface DEGRADED/RECOVERY_REQUIRED rather than
cascading silent corruption.
