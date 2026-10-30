# V4.0.17 — Canonical Resource Model

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

## Ressource canonique
Chaque ressource WCP possède au minimum : `id`, `type`, `tenant_id` nullable selon scope, `owner_scope`, `display_name`, `desired_state`, `observed_state`, `lifecycle_state`, `version`, timestamps, labels/metadata bornés et références de dépendances.

## Identifiants
UUID/identifiant opaque au niveau API ; identifiants OS/service séparés. Un rename utilisateur ne change jamais l’ID canonique.

## Etats
`desired_state` exprime l’intention. `observed_state` exprime la dernière observation vérifiée. `lifecycle_state` suit provisioning/suspension/deletion/recovery. Aucun booléen `active` universel ne remplace ces dimensions.

## Relations
Les relations sont explicites : tenant→account→site/domain ; site→runtime/certificate ; domain→DNS zone/mail domain ; account→quota/filesystem identity ; backup→dataset/resource snapshot.

## Ownership
`managed-local`, `managed-remote`, `external-integrated`, `external-unmanaged`, `disabled` sont des modes d’ownership/service, pas des hacks UI.
