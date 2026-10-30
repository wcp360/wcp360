# 202 --- CSR & Private Key Handling

## Statut et origine

\[SOURCE\] CSR/CRT.

## Objectif

Créer/importer CSR/certificats tout en protégeant les clés privées.

## Pages / sous-vues

CSRs; Certificates; Key references/fingerprints.

## Ressources et données

subject/SAN, algorithm metadata, fingerprint, storage reference.

## Actions opérateur

Generate CSR/key; import cert; key rotation workflow if supported.

## Monitoring, santé et logs

Expiry/validation.

## Sécurité, permissions et audit

Private key never logged; reveal/download only if explicit policy and
secure flow.

## Contrat backend attendu

Key material handled server-side/secure secret channel.

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

Les écrans standards n'affichent pas la clé privée en clair.
