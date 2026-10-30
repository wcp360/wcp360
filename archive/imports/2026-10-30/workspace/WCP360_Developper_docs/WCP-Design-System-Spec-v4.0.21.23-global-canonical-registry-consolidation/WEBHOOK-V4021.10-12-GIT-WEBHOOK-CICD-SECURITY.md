# V4.0.21.10 --- Webhook / CI-CD Trigger Security

## Webhook

Provider-independent endpoint, secret/signature verification,
timestamp/replay window, event allowlist, repository/ref binding, rate
limit and idempotency.

## Push deploy

Un push n'autorise jamais directement une commande privilégiée. Il crée
une demande de déploiement pour un repository/ref autorisé.

## Protections

Replay rejection, secret rotation overlap, payload size limits,
branch/tag allowlists, commit pinning, duplicate event deduplication,
audit correlation.

## External CI

API tokens/service accounts doivent avoir une permission de deploy
limitée à l'application/site ciblé.
