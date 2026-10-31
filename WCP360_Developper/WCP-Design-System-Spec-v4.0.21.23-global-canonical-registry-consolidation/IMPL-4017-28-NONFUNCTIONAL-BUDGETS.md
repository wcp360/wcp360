# V4.0.17 — Non-functional Budgets & Limits Contract

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

## Principe
Toute collection/input/Job possède limites explicites avant code : taille request, upload, pagination, timeout, retries, concurrency, retention, log payload, webhook payload.

## Ressources
Le panel doit rester administrable sous pression ; réserves/headroom et backpressure empêchent une tâche tenant de saturer le control-plane.

## Performance
Pas de chiffre arbitraire figé ici : les valeurs numériques seront définies dans le profil de capacité après benchmark, puis versionnées. L’absence de benchmark ne permet pas de prétendre un SLA.

## Availability
Single-node V1 implique qu’aucun SLA HA multi-node n’est revendiqué.
