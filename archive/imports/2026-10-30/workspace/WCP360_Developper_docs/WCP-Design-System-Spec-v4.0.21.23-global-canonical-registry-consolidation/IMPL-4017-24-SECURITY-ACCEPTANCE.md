# V4.0.17 — Security Acceptance Test Specification

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

## Frontières obligatoires
Tenant A ne lit/modifie jamais Tenant B ; reseller ne sort pas de son arbre ; WCP web ne peut exécuter commande root arbitraire ; wcpd refuse méthode/path/service non allowlisté.

## Auth
Session fixation, CSRF, MFA bypass, recovery replay, API token scope, brute-force/rate-limit, impersonation real/effective actor.

## Files
Traversal, symlink escape, archive traversal, permission escalation, upload executable policy, secret file access.

## API/Jobs
IDOR, idempotency collision, lost-update, duplicate Job, timeout ambiguity, forged webhook, SSRF provider endpoints.

## Supply chain/recovery
Unsigned/tampered artifact rejected ; backup corruption detected ; restore authorization ; recovery credential one-time/revoked.

## Gate
Toute violation de frontière tenant/root/secret est release-blocking.
