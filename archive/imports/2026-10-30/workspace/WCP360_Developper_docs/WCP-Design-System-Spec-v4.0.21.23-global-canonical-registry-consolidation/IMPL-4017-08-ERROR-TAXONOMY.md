# V4.0.17 — Canonical Error Taxonomy

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

## Envelope
`error.code`, `error.message`, `error.fields[]`, `correlation_id`, `retryable`, `category`, optional `job_id`/`resource_id`/`remediation_key`.

## Catégories
`AUTHENTICATION`, `AUTHORIZATION`, `VALIDATION`, `CONFLICT`, `QUOTA`, `DEPENDENCY`, `EXTERNAL_PROVIDER`, `SYSTEM_STATE`, `SECURITY_POLICY`, `RATE_LIMIT`, `NOT_SUPPORTED`, `TRANSIENT`, `INTERNAL`.

## Codes stables
Exemples : `DOMAIN_ALREADY_OWNED`, `QUOTA_EXCEEDED`, `RESOURCE_VERSION_CONFLICT`, `DNS_DELEGATION_NOT_READY`, `CERTIFICATE_ISSUANCE_FAILED`, `JOB_RECONCILIATION_REQUIRED`, `SECRET_UNAVAILABLE`, `SERVICE_CONFIG_INVALID`.

## Sécurité
Message utilisateur safe ; détails sensibles uniquement dans logs sécurisés corrélés. Aucune commande, stack trace, secret ou chemin tenant voisin.
