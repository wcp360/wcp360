# 150 --- Mail Stack Overview

## Statut et origine

\[SOURCE\] Exim ou Postfix + Dovecot + anti-spam + relay; Email &
Collaboration.

## Objectif

Vue cohérente de la pile mail.

## Pages / sous-vues

Services; Domains; Accounts; Queue; Routing; Anti-spam; Deliverability;
Logs.

## Ressources et données

MTA, IMAP/POP service, versions, queue size, domains, health.

## Actions opérateur

Open modules; coordinated service controls where supported.

## Monitoring, santé et logs

Queue, delivery success/failure, service health.

## Sécurité, permissions et audit

Mail admin permissions; PII/log redaction.

## Contrat backend attendu

Mail adapters declare MTA/Dovecot capabilities.

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

Exim et Postfix ne sont jamais supposés simultanément requis.

## V4.0.6 normative deepening

- `MAIL-406-01-MAIL-RESOURCE-MODEL-DEEP.md`
- `MAIL-406-03-POSTFIX-MTA-DEEP.md`
- `MAIL-406-05-DOVECOT-IMAP-POP3-DEEP.md`

Ces contrats approfondissent cette surface sans remplacer les décisions antérieures compatibles.

## V4.0.14 normative deepening

- `BOOT-4014-12-MAIL-STACK-BOOTSTRAP.md`
- `BOOT-4014-13-MAIL-SECURITY-ANTISPAM.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
