# V4.0.21.11 --- Post-install Readiness Certification

## Certification

Le serveur ne devient pas production-ready parce que l'installer
retourne 0.

Readiness minimale : control plane + daemon healthy, authentication/MFA
valid, SecretStore valid, filesystem permissions, cgroups/quotas,
selected web stack validation, DB selected backend, DNS path, TLS
management endpoint, firewall management path, backup policy + verified
artifact, update source, clock sync, monitoring and audit pipeline.

Si mail est activé : SMTP/IMAP/TLS/signing/scanner checks requis. Si DNS
authoritative est activé : zone/backend/DNSSEC capabilities selon
profile.

## Evidence

Chaque résultat conserve timestamp, checker version, resource, result,
safe evidence and expiry/freshness.
