# V4.0.17 — Canonical Naming & Identifier Contract

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

## Séparation
`display_name` humain, `slug` optionnel, ID canonique opaque, `system_name` dérivé/généré sous règles strictes, nom externe (domain/DB/mailbox) validé selon son protocole.

## Fichiers/code futurs
Noms techniques en anglais, ASCII, cohérents par langage ; pas d’espaces/accents dans package/module/path système. Les documents produit peuvent rester en français.

## Ressources Linux
UID/GID alloués par registre interne/OS policy ; username système non construit naïvement depuis email/domaine.

## Collisions
Normalisation et uniqueness contextuelle testées ; rename ne recycle pas automatiquement anciens identifiants sensibles.
