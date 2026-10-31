# V4.0.17 — Events, Outbox & Webhook Delivery Contract

## Statut
**NORMATIVE — V4.0.17 — CANONICAL IMPLEMENTATION CONTRACT — NO CODE**

## Portée
Ce document traduit les décisions gelées en V4.0.16 en contrat d’implémentation non exécutable. Il ne crée ni SQL, ni OpenAPI, ni protobuf, ni unit systemd installable, ni code.

## Invariants transversaux
- IDs internes stables et non dérivés d’un nom affiché.
- `desired_state`, `observed_state` et état d’exécution restent distincts.
- Toute mutation privilégiée passe par Job puis `wcpd`, sauf lecture explicitement non privilégiée.
- RBAC, tenant scope, audit, idempotency, secrets et correlation ID s’appliquent à chaque surface.
- Suppression logique, rétention et destruction physique sont des étapes distinctes lorsque des données client existent.

## Event
`event_id`, type versionné, occurred_at, tenant/resource refs, actor refs si pertinent, correlation/job IDs, safe payload.

## Outbox
Écrit transactionnellement avec l’état métier. Dispatcher marque tentative/livraison sans supprimer l’historique nécessaire à la fiabilité.

## Webhook
HTTPS, secret/signature, timestamp/event ID anti-replay, retry borné, exponential backoff, disable/quarantine endpoint défaillant selon policy.

## Privacy
Payload minimal ; secrets et données sensibles exclus par défaut. Les consumers doivent pouvoir dédupliquer par event ID.
