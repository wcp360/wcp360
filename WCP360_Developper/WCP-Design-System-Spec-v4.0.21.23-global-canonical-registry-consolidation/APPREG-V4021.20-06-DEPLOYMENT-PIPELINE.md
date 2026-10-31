# V4.0.21.20 --- Deployment Pipeline / Release Model

Pipeline: source revision → immutable deployment intent → preflight →
fetch/build in bounded execution context → dependency policy →
artifact/release staging → config/secrets binding → optional migrations
→ atomic/current release switch → health probes → observed state →
commit or rollback.

Operations: `DEPLOY-PLAN`, `DEPLOY-RUN`, `DEPLOY-ROLLBACK`,
`DEPLOY-HISTORY-READ`, `DEPLOY-CANCEL`.

Concurrency uses application deployment lock and idempotency. A failed
deployment does not overwrite the last-known-good release. DB migration
rollback capability must be declared honestly; WCP never claims
automatic rollback if migration is irreversible.
