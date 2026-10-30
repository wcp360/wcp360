# 155 --- Mail Deliverability, SPF/DKIM/DMARC

## Statut et origine

\[SOURCE\] Email authentication + deliverability reports.

## Objectif

Diagnostic de délivrabilité et authentification domaine.

## Pages / sous-vues

Domain status; SPF; DKIM; DMARC; DNS evidence; reports.

## Ressources et données

domain, records expected/observed, validation state, timestamp.

## Actions opérateur

Generate/rotate DKIM if supported; copy DNS record; recheck.

## Monitoring, santé et logs

Validation freshness and errors.

## Sécurité, permissions et audit

Private DKIM key never displayed unless explicit secure workflow exists.

## Contrat backend attendu

DNS/mail coordination endpoints.

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

Observation DNS est horodatée et ne confond pas propagation avec
configuration locale.
