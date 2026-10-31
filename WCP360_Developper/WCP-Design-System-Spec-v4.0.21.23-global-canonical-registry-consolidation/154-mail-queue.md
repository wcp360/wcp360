# 154 --- Mail Queue

## Statut et origine

\[SOURCE\] Mail Queue Manager.

## Objectif

Inspecter et traiter la file mail.

## Pages / sous-vues

Queue; message detail; deferred reasons; bulk actions.

## Ressources et données

id, sender/recipient redacted per policy, age, size, state, reason.

## Actions opérateur

Retry; freeze/unfreeze if MTA supports; delete with confirmation.

## Monitoring, santé et logs

Queue size/age/deferred rate.

## Sécurité, permissions et audit

Sensitive message metadata; scoped permissions; bulk safety.

## Contrat backend attendu

MTA-neutral queue contract plus engine-specific actions.

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

Résultats bulk détaillent succès/échecs.

## V4.0.6 normative deepening

- `MAIL-406-17-MAIL-QUEUE-DEEP.md`
- `MAIL-406-18-BOUNCE-DSN-BACKSCATTER.md`

Ces contrats approfondissent cette surface sans remplacer les décisions antérieures compatibles.

## V4.0.14 normative deepening

- `BOOT-4014-12-MAIL-STACK-BOOTSTRAP.md`
- `BOOT-4014-13-MAIL-SECURITY-ANTISPAM.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
