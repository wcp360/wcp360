# V4.0.17 — Secret Reference & Rotation Contract

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

## Secret object
ID opaque, type/purpose, owner scope, cipher metadata/version, created/rotated timestamps, status ; valeur jamais renvoyée par endpoints standards.

## Access
Services demandent une capability/purpose précise ; accès enregistré sans loguer la valeur.

## Rotation
Create new version → validate consumer compatibility → switch references → verify → retire old version après grace/recovery policy.

## Exposure
Une valeur générée destinée à l’utilisateur peut être montrée une seule fois si le workflow l’exige ; elle n’est pas récupérable ensuite, seulement rotatable.
