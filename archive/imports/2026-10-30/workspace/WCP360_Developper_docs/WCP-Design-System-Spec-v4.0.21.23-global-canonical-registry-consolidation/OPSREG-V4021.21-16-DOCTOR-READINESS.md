# V4.0.21.21 --- Doctor / Readiness / Drift Diagnostics

Doctor checks host profile, filesystem/space/inodes, cgroups/quota,
time/DNS, hostname/network/IPs, certificates, DB, web/PHP, DNS, mail,
backup target, security providers, queues/jobs, updates, monitoring and
audit sink.

Results are `PASS`, `WARN`, `FAIL`, `NOT_APPLICABLE`, `UNKNOWN` with
evidence, remediation and whether READY is blocked.

Doctor distinguishes desired config, rendered config, running state and
external observed state; it is not merely a process status page.
