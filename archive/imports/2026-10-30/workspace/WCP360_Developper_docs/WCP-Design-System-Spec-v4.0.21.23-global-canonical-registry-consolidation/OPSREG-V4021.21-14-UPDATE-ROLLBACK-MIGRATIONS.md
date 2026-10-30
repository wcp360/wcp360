# V4.0.21.21 --- Update Rollback / Schema Migration Safety

Every WCP release declares minimum/maximum compatible schema/provider
versions, migration sequence, reversibility and rollback boundary.

Before irreversible migration, recovery path/checkpoint is required. WCP
does not advertise rollback past an irreversible schema boundary.

Interrupted updates are durable state machines. Restart/reboot resumes
or enters explicit RECOVERY_REQUIRED rather than assuming success.
