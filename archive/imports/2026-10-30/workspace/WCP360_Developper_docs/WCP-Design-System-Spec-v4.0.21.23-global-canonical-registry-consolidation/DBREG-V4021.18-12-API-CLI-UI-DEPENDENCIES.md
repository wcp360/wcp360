# V4.0.21.18 --- DB/Cache Surface Parity & Dependencies

REST/CLI/UI map to the same Operation IDs. Destructive DB/cache
operations expose impact/confirmation consistently. Long
import/export/restore operations expose Job progress without leaking SQL
content or secrets.

Dependencies: hosting_account ACTIVE → system identity/isolation ready →
database/cache entitlement + provider capability. Backup/restore,
monitoring, audit, billing quotas and app runtimes consume these
resources through stable IDs.
