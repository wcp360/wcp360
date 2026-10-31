# V4.0.17 — wcpd Privileged RPC Catalog

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

## Identité système
`UserCreate`, `UserLock`, `UserUnlock`, `UserDelete`, `GroupEnsure`, `OwnershipApply`, `QuotaApply`.

## Fichiers/config
`ManagedFileInstall`, `ManagedGenerationActivate`, `DirectoryEnsure`, `PermissionReconcile`. Aucun RPC arbitraire de lecture/écriture de chemin non autorisé.

## Services
`ServiceReload`, `ServiceRestart`, `ServiceEnable`, `ServiceDisable`, `ServiceStatus` uniquement pour allowlist WCP.

## Réseau/firewall
`NftablesValidateApply`, `ListenerInspect`, actions réseau explicitement bornées.

## Certificats
Installation/permissions/deployment des artefacts déjà validés selon scope.

## Règle absolue
Aucun `Exec`, `Shell`, `RunCommand`, `WriteAnyFile`, `ReadAnyFile` générique.
