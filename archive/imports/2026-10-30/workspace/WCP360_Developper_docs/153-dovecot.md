# 153 --- Dovecot Manager

## Statut et origine

\[SOURCE\] Dovecot.

## Objectif

Gérer services IMAP/POP et état d'authentification selon capacités.

## Pages / sous-vues

Overview; Protocols; Auth; Connections; Config; Logs.

## Ressources et données

version, state, enabled protocols, connection stats if available.

## Actions opérateur

Reload/restart; approved settings.

## Monitoring, santé et logs

Connections/auth errors if available.

## Sécurité, permissions et audit

Credential data never exposed; audit.

## Contrat backend attendu

Dovecot adapter.

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

Les protocoles non activés sont explicitement distingués d'une panne.
