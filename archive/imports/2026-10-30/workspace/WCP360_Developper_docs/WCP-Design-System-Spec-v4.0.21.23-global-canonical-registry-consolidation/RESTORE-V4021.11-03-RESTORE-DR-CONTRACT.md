# V4.0.21.11 --- Restore / Disaster Recovery Contract

## Restore workflow

Select artifact → authorize → verify artifact/manifest → compatibility
check → dependency graph → target validation → conflict plan → restore
sandbox/staging where needed → restore → service reconcile →
integrity/health checks → commit.

## Modes

`IN_PLACE`, `ALTERNATE_TARGET`, `SELECTIVE`, `FULL_ACCOUNT`,
`DISASTER_RECOVERY`.

## Sécurité

Restore cross-tenant interdit sans permission explicite WCP-ADMIN et
mapping de destination. Secrets restaurés suivent SecretStore policy. Un
restore ne peut écraser silencieusement une ressource plus récente sans
conflict decision.

## DR

RPO/RTO sont des objectifs de policy, pas des promesses automatiques. DR
inclut control-plane DB, secrets/key recovery, config registry, service
reconstruction, backup target access, DNS/TLS dependencies et validation
post-recovery.
