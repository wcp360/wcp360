# V4.0.3 --- Filesystem, Mounts, Capacity & Inodes

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

## Ressource

Block device → partition/LV → filesystem → mount point, sans supposer
relation 1:1.

## Observation

fstype, source, mountpoint, options redacted, total/free/reserved, inode
total/free, read-only, errors si exposés.

## Mount ownership

Distinguer OS-managed, operator-managed et WCP-managed. WCP ne modifie
pas `/etc/fstab` génériquement sans ressource structurée et ownership.

## Santé

Bytes libres et inodes libres ont des seuils séparés. Read-only remount,
I/O errors et stale network mount sont critiques.

## Path

Canonicalisation et mount boundaries doivent être connues du File
Manager/backup/quota modules.

## Mutations

Le V1 peut rester principalement read-only pour partition/format/mount
destructive ; toute création/formatage futur exige workflow storage
dédié et confirmation critique.

## V4.0.11 normative deepening

- `CLUSTER-4011-01-CLUSTER-NODE-MODEL.md`
- `CLUSTER-4011-02-NODE-ENROLLMENT-TRUST.md`
- `CLUSTER-4011-05-NODE-CORDON-DRAIN.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
