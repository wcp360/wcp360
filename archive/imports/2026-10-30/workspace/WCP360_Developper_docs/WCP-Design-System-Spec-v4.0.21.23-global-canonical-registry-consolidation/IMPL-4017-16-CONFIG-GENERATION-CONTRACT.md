# V4.0.17 — Managed Configuration Generation Contract

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

## Pipeline
Canonical desired model → validate → render generation → static/syntax validation → stage → atomic activate → service reload/restart selon adapter → functional verify → commit generation metadata.

## Ownership
Chaque generator possède un namespace/include/path défini. Les fichiers unmanaged sont observés mais jamais réécrits silencieusement.

## Fingerprint
Template version + normalized desired input + generated content digest + active generation ID.

## Rollback
Si activation/reload/verify échoue : réactiver dernière génération valide lorsque sûr, vérifier, puis marquer Job failed/recovery-required.

## Secrets
Injection via fichiers/refs permissions strictes ; aucune valeur secrète dans manifest de génération exportable.
