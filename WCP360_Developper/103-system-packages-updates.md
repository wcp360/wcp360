# 103 --- Packages & Updates

## Statut et origine

\[SOURCE\] Package & Update Manager; repository manager; security
updates.

## Objectif

Gérer dépôts, paquets et mises à jour avec visibilité de l'impact.

## Pages / sous-vues

Updates; Installed; Repositories; History; Security advisories.

## Ressources et données

package, version installed/candidate, repo, advisory, reboot-required
signal.

## Actions opérateur

Refresh metadata; install/update/remove where allowed; schedule/update
job.

## Monitoring, santé et logs

Counts, failed transactions, pending security updates.

## Sécurité, permissions et audit

`packages.read/write`; confirmation pour remove; audit + job; pas de
commande shell assemblée côté UI.

## Contrat backend attendu

Package adapter abstracts distro tooling; transaction result and
reboot-required.

## États UI obligatoires

Loading, skeleton, empty, no-results, partial data, stale telemetry,
permission denied, service unavailable, maintenance, action pending,
action failed, success confirmé.

## Règles UX communes

-   Afficher serveur/nœud, scope et cible avant toute mutation.
-   Distinguer `Save`, `Apply`, `Reload` et `Restart`.
-   Une modification de configuration sensible présente un diff si
    possible.
-   Toute opération longue crée un job dans Task Center.
-   Ne jamais afficher un pourcentage de progression artificiel.
-   Les commandes/configurations proposées par l'UI restent validées et
    autorisées côté backend.
-   Les secrets sont redacted dans logs, audit et notifications.

## Critères d'acceptation

Échec transactionnel clairement distingué d'un succès partiel.
