# 360 --- WordPress Toolkit

## Origine

**\[V3.2 --- exigence ajoutée par le projet\]** Toolkit WordPress de
niveau hébergeur.

## Pages

Fleet; Sites; Install; Staging/Clone; Updates; Plugins; Themes;
Security/Hardening; Vulnerabilities; Backups; Logs.

## Installation 1-click

Domaine/sous-domaine, chemin, version disponible, langue, titre, compte
administrateur, PHP/runtime, base DB, HTTPS, politique de mises à jour.
Les secrets sont générés/affichés selon la politique WCP et jamais
journalisés.

## Fleet Management

Vue centralisée de toutes les installations accessibles : version Core,
PHP, SSL, plugins/thèmes à mettre à jour, santé, staging, vulnérabilités
connues si une source de vulnérabilités est configurée.

## Staging / Clone

Production → staging ou clone → vérification capacité/espace → copie
fichiers + DB → réécriture URL via mécanisme WordPress fiable →
isolation/indexation désactivée selon policy → résultat. La
synchronisation staging→production est un workflow séparé avec
prévisualisation d'impact.

## Updates

Core/plugins/themes : manuel, planifié ou policy-driven. Support
maintenance window, backup préalable si configuré, résultat par
composant et rollback uniquement si réellement supporté.

## Vulnerability / Quarantine

Une vulnérabilité détectée est un finding. La quarantaine/désactivation
d'un plugin vulnérable n'est automatique que si une politique explicite
l'autorise. Sinon WCP recommande et demande approbation.

## Hardening

Checklist capability-driven : permissions fichiers, debug, XML-RPC,
protections login, clés/salts, indexation, versions exposées, règles
serveur. Ne pas appliquer aveuglément une règle incompatible.

## WP-CLI

Peut être utilisé côté backend comme adaptateur d'exécution, mais
WCP-USER ne reçoit pas un shell root implicite.

## Contrat WCP

-   **WCP-ADMIN** configure moteurs, catalogues, politiques, capacités
    et limites.
-   **WCP-USER** expose uniquement les fonctions attribuées au
    client/revendeur.
-   Toute mutation sensible est vérifiée côté backend, auditée et
    corrélée à un job/événement.
-   Les fonctionnalités sont pilotées par `capabilities`; aucune
    intégration tierce n'est supposée installée.
-   États obligatoires : loading, empty, denied, unavailable, partial,
    stale, pending, failed, success confirmé.
