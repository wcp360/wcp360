# V4.0.17 — Database Integrity & Transaction Boundaries

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

## Transaction canonique de mutation
Authorization → validate request → lock/version check → write desired state → create Job → append outbox/audit intent → commit.

## Invariants
Aucun Job durable ne doit référencer une ressource inexistante ; aucun changement de desired state mutatif ne doit être validé sans trace de Job lorsque l’action système est requise ; idempotency key + request fingerprint ne peut être réutilisé pour une requête différente.

## Leases
Claim atomique, owner, acquired_at, heartbeat_at, expires_at. Un lease expiré autorise reprise seulement après inspection/reconciliation pour opérations ambiguës.

## Audit
Audit de décision et résultat sont corrélés au même request/job ID, sans dépendre d’une transaction distribuée impossible avec les services système.
