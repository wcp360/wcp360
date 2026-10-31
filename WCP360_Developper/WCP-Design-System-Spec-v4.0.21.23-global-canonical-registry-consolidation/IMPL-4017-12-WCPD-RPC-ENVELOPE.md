# V4.0.17 — wcpd RPC Envelope & Authorization Contract

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

## Requête logique
protocol_version, method, request_id, job_id, actor_context_ref, tenant/resource refs, typed payload, deadline.

## Réponse
request_id, status, typed result, observed evidence summary, safe error code, retry classification.

## Validation
Version de protocole, peer credential, payload size, canonical IDs, path policy, enum allowlists, deadline et capability mapping.

## Audit
Le daemon produit un résultat auditable corrélé au Job ; il ne reçoit pas le mot de passe utilisateur final comme preuve d’autorisation.

## Compatibilité
N-1/N rolling compatibility seulement si explicitement requise par future update policy ; V1 single-node peut exiger versions app/daemon compatibles strictement.
