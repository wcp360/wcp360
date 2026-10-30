# V4.0.3 --- Resolver, /etc/hosts & Name Service

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

Resolver local du serveur ≠ DNS authoritative géré pour les domaines
clients.

## Observation

Resolver backend, nameservers/search domains, systemd-resolved state si
applicable, `/etc/resolv.conf` ownership/symlink et NSS hosts order.

## Ownership

Ne jamais écraser un fichier généré par
NetworkManager/systemd-resolved/cloud-init sans passer par son
adapter/source d'autorité.

## /etc/hosts

Entrées WCP uniquement dans zone/ownership contrôlé ou via modèle
complet avec conflit detection. Préserver localhost et lignes externes.

## Diagnostics

Résolution A/AAAA, DNSSEC signal si disponible, latency/timeouts et
split-horizon. Ne pas confondre réponse NXDOMAIN et resolver
unavailable.
