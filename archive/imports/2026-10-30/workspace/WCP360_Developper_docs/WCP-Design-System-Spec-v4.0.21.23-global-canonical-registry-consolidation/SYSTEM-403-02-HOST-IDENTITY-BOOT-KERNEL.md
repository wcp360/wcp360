# V4.0.3 --- Host Identity, Boot & Kernel Contract

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

`HostNode` : stable node ID, machine identity fingerprint non secret,
hostname, FQDN, OS, kernel release, architecture, boot ID, boot time,
virtualization/container signal et capabilities.

## Hostname/FQDN

Valider syntaxe, résolution et impact. Changement : preflight des
services dépendants → apply via adapter → relire hostname effectif →
vérifier `/etc/hosts`/resolver policy sans écrasement aveugle → signaler
impacts TLS/mail/DNS.

## Kernel

Lecture détaillée : release, taint si exposable, cmdline redacted,
modules/capabilities utiles. Aucun module kernel arbitraire chargé
depuis une chaîne utilisateur.

## Boot

Boot ID permet de distinguer reboot et simple restart de WCP. Après
reboot, les Jobs précédemment running sont reclassifiés puis
reconciliés.

## Edge cases

Hostname valide mais DNS incohérent ; FQDN pointe ailleurs ; machine-id
dupliqué après clonage ; kernel mis à jour mais reboot non effectué ;
boot incomplet.
