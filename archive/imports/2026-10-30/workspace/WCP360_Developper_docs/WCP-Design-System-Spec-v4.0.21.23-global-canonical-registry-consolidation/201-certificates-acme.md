# 201 --- Certificates & ACME

## Statut et origine

\[SOURCE\] Let's Encrypt / AutoSSL.

## Objectif

Gérer émission et renouvellement automatisé.

## Pages / sous-vues

Requests; challenges; certificates; renewal jobs.

## Ressources et données

domains, challenge type, issuer, status, expiry.

## Actions opérateur

Issue; renew; retry; switch supported challenge.

## Monitoring, santé et logs

Renewal success/failure.

## Sécurité, permissions et audit

DNS/API credentials redacted; domain authorization.

## Contrat backend attendu

ACME workflow jobs and challenge evidence.

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

Le statut Pending n'est pas affiché comme certificat actif.
