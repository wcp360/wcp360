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

## V4.0.3 — Deep Specification normative

Ce document de surface doit être interprété avec les contrats approfondis suivants :

- `SYSTEM-403-06-PACKAGE-REPOSITORIES-TRANSACTIONS.md`
- `SYSTEM-403-07-UPDATE-PATCH-REBOOT-POLICY.md`

Les règles V4.0.3 prévalent lorsqu'une ancienne formulation est moins précise. Le statut de cette surface reste **à valider contre le READY Gate System/OS**.

## V4.0.12 normative deepening

- `LIFE-4012-04-UPDATE-CHANNELS.md`
- `LIFE-4012-05-UPDATE-POLICY-SCHEDULING.md`
- `LIFE-4012-08-OS-PACKAGE-LIFECYCLE.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.
