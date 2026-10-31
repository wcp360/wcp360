# V4.0.21.21 --- Disaster Recovery / Bare Host Recovery

DR plan identifies control-plane state, SecretStore/key dependencies,
backup catalog, host profile, package/provider prerequisites and
restoration ordering.

Recovery ordering: bootstrap trusted host → WCP control state →
secrets/keys → accounts/system identities → storage/files → DB/cache →
DNS/TLS/web → mail → applications/jobs → observability → readiness
verification.

RPO/RTO are declared objectives, not promises inferred from schedule. DR
tests record achieved restore time/data point and unresolved gaps.
