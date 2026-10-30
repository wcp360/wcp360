# V4.0.21.22 --- P0 Security Traceability

P0 scenarios now exist across tenant isolation, path/archive escape,
open relay, DB cross-tenant grants, DKIM/secret exposure, unsafe Redis
exposure, deployment rollback, backup corruption, firewall lockout, WAF
floor bypass, malware quarantine, update interruption, installer
idempotency and recovery.

Gap before GLOBAL READY: consolidate them into one numbered global P0
catalog and map each P0 to Resource + Operation + expected
error/decision + audit event + acceptance evidence. Distributed P0 prose
is not yet sufficient as a final implementation acceptance ledger.
