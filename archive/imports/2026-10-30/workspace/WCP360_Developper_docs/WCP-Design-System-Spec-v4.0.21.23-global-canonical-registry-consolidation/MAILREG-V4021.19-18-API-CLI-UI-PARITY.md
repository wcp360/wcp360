# V4.0.21.19 --- Mail API / CLI / UI Parity

REST, CLI, WCP-ADMIN and WCP-USER use the same Operation IDs/policy
engine. UI deliverability wizard composes diagnostics but cannot bypass
DNS/TLS/mail contracts.

Sensitive trace/quarantine/queue fields are permission-filtered
server-side. Long Jobs expose stages and reason codes without message
bodies, secrets or raw privileged configuration.
