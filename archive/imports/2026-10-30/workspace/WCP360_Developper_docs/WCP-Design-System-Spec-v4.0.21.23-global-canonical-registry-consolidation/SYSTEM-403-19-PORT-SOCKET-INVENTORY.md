# V4.0.3 --- Listening Ports, Unix Sockets & Exposure Inventory

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

Protocol, local address, port, family, process identity, systemd
unit/cgroup, exposure classification et owning WCP module si connu.

## Unix sockets

Path canonical, owner/group/mode, listener identity et expected peer
policy.

## Corrélation

Web/mail/DB/control-plane modules déclarent leurs expected listeners. Un
listener inattendu devient finding, pas automatiquement incident.

## Sécurité

Ne pas exposer arguments/process secrets. WCP-USER voit uniquement les
listeners de son scope si une vue est proposée.

## Conflicts

Avant provisionnement, détecter port already bound, wildcard conflicts
IPv4/IPv6 et socket path collision.
