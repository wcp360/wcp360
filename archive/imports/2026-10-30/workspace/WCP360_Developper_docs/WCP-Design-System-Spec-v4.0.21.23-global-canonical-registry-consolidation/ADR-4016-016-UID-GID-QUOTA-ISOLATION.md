# ADR-016 --- Filesystem Ownership, UID/GID, Quotas & Tenant Isolation

## Statut

**ACCEPTED FOR V1 --- V4.0.16 --- NO CODE**

## Autorité

Cet ADR ferme un choix P0 identifié par `ADR-V4015-07-REGISTER.md`. Il
concrétise la baseline V4.0.15 sans élargir le scope fonctionnel.

## Règles

-   Toute implémentation incompatible exige un ADR de supersession.
-   Les secrets ne sont jamais placés en clair dans les arguments de
    processus, logs ou artefacts exportés.
-   Toute mutation privilégiée reste typée, auditable,
    idempotente/reconciliable et vérifiée par état observé.
-   Le V1 reste single-node-first tout en conservant des interfaces qui
    n'interdisent pas le cluster futur.

## Décision

Chaque hosting account possède un **Linux UID/GID dédié stable** ; les
sites héritent de cette frontière par défaut. Les services web accèdent
via sockets/permissions explicitement générés.

## Data root

`/srv/wcp-data/users/<account-id>/...` avec chemins internes canoniques
; display names/domains ne déterminent pas directement le chemin
système.

## Permissions

No world-writable defaults. Umask restrictive. ACL seulement lorsque
nécessaire et générée.

## Quotas

Filesystem project/user quota selon filesystem certifié + quota logique
WCP ; les deux états sont observés et reconciliés.

## Isolation

systemd resource controls/cgroups pour processus gérés ; PHP-FPM pools
séparés. Chroot/namespace renforcé peut être P1 après validation.

## Symlinks

Traversal et ownership checks obligatoires pour File Manager, archive
extraction, deploy et backup restore.
