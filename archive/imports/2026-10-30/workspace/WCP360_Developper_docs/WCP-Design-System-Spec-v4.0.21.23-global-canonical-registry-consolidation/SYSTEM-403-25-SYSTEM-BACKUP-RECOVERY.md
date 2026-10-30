# V4.0.3 --- System Configuration Backup & Recovery

## Statut

**NORMATIVE --- V4.0.3 SYSTEM & OS DEEP SPECIFICATION --- NO CODE**

Cette spécification instancie les fondations V4.0.2 pour WCP-ADMIN. Elle
décrit le comportement attendu sans imposer de framework ni produire
d'artefact exécutable.

## Invariants communs

-   WCP-USER n'obtient jamais de primitives d'administration hôte.
-   Toute mutation système est autorisée, bornée, auditable et exécutée
    via une action privilégiée fermée.
-   Aucun champ UI/API ne devient une commande shell, un chemin absolu
    arbitraire, un nom d'unité arbitraire ou une liste d'arguments root.
-   Toute mutation suit **preflight → plan/diff → lock → apply → verify
    → reconcile/rollback → audit**.
-   Timeout ou perte de réponse = état ambigu jusqu'à vérification.
-   L'état affiché provient de l'observation effective et porte un
    timestamp/freshness.

## Scope

Séparer backup des données hébergées, backup du control-plane et
snapshot des configurations système WCP-owned.

## Contenu système

Inventaire de packages pertinent, WCP-managed config, resource
manifests, ownership/permissions/labels, service mappings et secrets
uniquement via mécanisme chiffré dédié.

## Restore

Sur même host et bare-metal/new-host sont des scénarios différents.
Restore ne réinjecte pas aveuglément paths/devices/UIDs d'un autre
serveur.

## Verification

Manifest/checksum, schema/version compatibility, OS profile, secrets
availability et post-restore reconciliation.

## Disaster scenario

Perte totale de `/var/lib/wcp` doit avoir une procédure documentée :
reconstruire control plane, importer manifests/backups, rediscover
system, comparer drift et réconcilier.

## V4.0.14 normative deepening

- `BOOT-4014-18-BACKUP-BOOTSTRAP.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
