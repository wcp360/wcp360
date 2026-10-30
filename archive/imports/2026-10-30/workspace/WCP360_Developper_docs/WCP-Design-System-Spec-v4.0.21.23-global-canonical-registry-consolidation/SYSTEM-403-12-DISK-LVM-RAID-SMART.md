# V4.0.3 --- Disk, LVM, RAID & SMART Health

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

## Inventaire

Physical disks, virtual disks, partitions, MD RAID, LVM PV/VG/LV et
device mapper avec relations explicites.

## SMART

Capability selon device/virtualization. Afficher données brutes
seulement avec contexte ; produire health `unknown` si SMART
indisponible, jamais `healthy` par défaut.

## RAID

Level, members, degraded/rebuild state, progress seulement si source
fiable. Un rebuild déclenche alerte et évite opérations storage
concurrentes risquées.

## LVM

Observation des PV/VG/LV et free extents. Extension peut être future ;
réduction de filesystem/LV est destructive et hors action générique.

## Sécurité

Aucun device path arbitraire accepté d'un client. Devices sont
sélectionnés depuis inventory avec stable reference/evidence.
