# 200 --- SSL/TLS Overview

## Statut et origine

\[SOURCE\] AutoSSL, Let's Encrypt, wildcard, CSR/CRT.

## Objectif

Vue certificats et couverture des domaines.

## Pages / sous-vues

Certificates; AutoSSL; ACME; CSR; Policies; Renewal.

## Ressources et données

certificate subject/SAN, issuer, validity, fingerprint, bindings,
renewal state.

## Actions opérateur

Request/renew/revoke/unbind according to capability.

## Monitoring, santé et logs

Expiry timeline, renewal failures.

## Sécurité, permissions et audit

Private keys protected; certificate actions audited.

## Contrat backend attendu

Certificate/ACME adapter.

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

Expiration calculée depuis certificat réel et horodatée.
