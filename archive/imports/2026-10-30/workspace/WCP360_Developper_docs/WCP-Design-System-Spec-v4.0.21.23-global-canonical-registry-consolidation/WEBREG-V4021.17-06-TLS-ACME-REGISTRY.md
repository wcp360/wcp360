# V4.0.21.17 --- TLS / ACME Registry

Operations: `CERTIFICATE-REQUEST`, `CERTIFICATE-RENEW`,
`CERTIFICATE-DEPLOY`, `CERTIFICATE-REVOKE`, `CERTIFICATE-IMPORT`,
`ACME-ORDER-RETRY`.

Private keys are SecretStore/privileged-storage references with strict
file permissions and never returned to ordinary list APIs. Import
requires key/cert match, chain validation, hostname/SAN validation and
policy.

ACME challenge selection is capability-aware (HTTP-01/DNS-01 where
adapters support it). Renewal uses durable Jobs, backoff, expiry
alerting and deployment verification. Failed deployment keeps
last-known-good certificate where safe.
