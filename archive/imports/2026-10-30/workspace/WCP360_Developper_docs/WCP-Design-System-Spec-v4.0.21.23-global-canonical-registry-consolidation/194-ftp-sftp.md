# 194 --- FTP / SFTP Manager

## Statut et origine

\[SOURCE\] FTP/SFTP manager, security rules, quotas.

## Objectif

Gérer service et comptes de transfert.

## Pages / sous-vues

Services; Accounts; Roots; Quotas; Sessions/logs if available.

## Ressources et données

account, protocol, root, quota, state.

## Actions opérateur

Create/edit/disable/delete account; rotate credential; service control.

## Monitoring, santé et logs

Sessions/transfer metrics if available.

## Sécurité, permissions et audit

Path confinement; secret handling; tenant scope.

## Contrat backend attendu

FTP/SFTP adapters; SFTP may map to SSH capabilities.

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

L'UI n'assimile pas SFTP à FTP si les backends diffèrent.

## V4.0.7 normative deepening

- `SECOPS-407-09-SFTP-FTP-ACCOUNT-DEEP.md`

Ces contrats approfondissent cette surface et conservent les exigences antérieures compatibles.
