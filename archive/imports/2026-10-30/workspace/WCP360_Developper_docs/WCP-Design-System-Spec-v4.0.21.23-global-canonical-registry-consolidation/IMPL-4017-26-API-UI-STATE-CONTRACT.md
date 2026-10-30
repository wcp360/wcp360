# V4.0.17 — UI/API State & Async Operation Contract

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

## UI
Toute mutation async affiche Job/progress, état précédent, état demandé et résultat observé. Aucun toast “success” définitif avant vérification pour une action système.

## Etats visuels
loading, empty, ready, queued, running, verifying, degraded, failed, blocked, suspended, recovery-required, stale-observation.

## Optimistic UI
Autorisé uniquement pour préférences sans effet système critique. Provisioning/security/destructive actions restent server-confirmed.

## Errors
La UI mappe error code → message/remediation sans parser des strings serveur.
