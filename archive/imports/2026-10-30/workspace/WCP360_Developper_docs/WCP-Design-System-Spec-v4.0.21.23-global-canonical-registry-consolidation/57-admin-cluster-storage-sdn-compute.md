# 57 --- WCP-ADMIN : Cluster, Storage, SDN & Compute

## Objectif

Prévoir Cluster Manager, nodes, heartbeat/fencing, placement, live
migration, rolling updates, multi-tenant RBAC, ZFS/Ceph/Gluster/NFS/S3,
storage classes, replication, backup orchestration, VXLAN/WireGuard,
load balancing/Anycast, DNS HA, KVM/QEMU, LXC/Docker et GPU/SR-IOV.

## Règles obligatoires

-   Densité élevée autorisée, jamais au détriment de la compréhension du
    risque.
-   Toute action root affiche cible et scope exacts.
-   Start/stop/restart, update, firewall, migration et destruction sont
    audités.
-   Prévisualiser diff/impact avant changements de configuration quand
    possible.
-   Les tâches longues passent par Task Center.

## Composants / anatomie

Topology view complémentaire à une table accessible ; node resource page
; placement/migration wizard ; storage health/capacity; network
overlays; VM/container lifecycle. Ces écrans peuvent être phase 4/5 mais
les patterns sont réservés dès v1.

## États à prévoir

healthy, degraded, critical, unknown, maintenance, disconnected, quorum
risk, pending, running, failed.

## Responsive

Desktop prioritaire ; mobile destiné surtout à supervision et actions
d'urgence sûres.

## RBAC et sécurité

Scopes serveur/cluster/projet. Step-up auth recommandé pour opérations
critiques. Impersonation et sudo-like elevation toujours visibles et
audités.

## Definition of Done locale

-   Tous les états sont maquettés et testables.
-   Light/Dark/System utilisent les mêmes tokens sémantiques.
-   Clavier, focus, lecteurs d'écran et zoom 200 % sont vérifiés.
-   Les permissions sont contrôlées côté backend et reflétées
    correctement dans l'UI.
-   Les actions mutantes produisent feedback, audit et gestion d'erreur.
-   Aucun écran ne dépend uniquement de la couleur pour communiquer un
    état.

## V4.0.11 normative deepening

- `CLUSTER-4011-01-CLUSTER-NODE-MODEL.md`
- `CLUSTER-4011-10-COORDINATION-QUORUM.md`
- `CLUSTER-4011-27-CLUSTER-OBSERVABILITY.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
