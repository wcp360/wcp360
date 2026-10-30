# 212 --- Backup Destinations

## Statut et origine

\[SOURCE\] Remote Storage Targets : S3/SFTP/FTP/NFS.

## Objectif

Gérer destinations sans exposer credentials.

## Pages / sous-vues

Destinations; health/test; capacity if known.

## Ressources et données

type, endpoint redacted, bucket/path, credential reference, status.

## Actions opérateur

Create/edit/test/disable/delete.

## Monitoring, santé et logs

Reachability/last test/capacity if available.

## Sécurité, permissions et audit

Secrets write-only; SSRF/network validation.

## Contrat backend attendu

Provider adapters.

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

Test destination produit résultat distinct d'une sauvegarde réussie.
