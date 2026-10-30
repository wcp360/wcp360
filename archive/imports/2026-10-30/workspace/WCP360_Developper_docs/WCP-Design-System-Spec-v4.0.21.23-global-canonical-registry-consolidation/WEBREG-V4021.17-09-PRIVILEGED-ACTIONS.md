# V4.0.21.17 --- Web/PHP/TLS/DNS Privileged Actions

Closed actions include: `WEB-CONFIG-STAGE`, `WEB-CONFIG-VALIDATE`,
`WEB-CONFIG-PUBLISH`, `WEB-SERVICE-RELOAD`, `PHP-FPM-POOL-WRITE`,
`PHP-FPM-SERVICE-RELOAD`, `CERT-KEY-MATERIAL-WRITE`,
`CERT-DEPLOY-ATOMIC`, `DNS-ZONE-APPLY`, `DNSSEC-KEY-APPLY`.

Inputs are typed and generated from canonical resources. Arbitrary path,
arbitrary command, raw config body and unbounded service name are
forbidden. Each action declares allowed Job callers, timeout,
verification and rollback/ compensation semantics.
