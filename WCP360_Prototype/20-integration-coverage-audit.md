# Bilan de l’intégration WCP360 — 30 octobre 2026

> Suivi Sites/Comptes : [22 — Cycle de vie des ressources](22-resource-lifecycle-migration.md) traite I-04 sur les créations, changements de statut et archives. Les résultats sont rapprochés par ressource et job exacts ; le reliquat de pages à intégrer reste de 109.

> Suivi après intégration : voir [21 — Consolidation des règles mail](21-mail-rules-consolidation.md). Les écarts I-03 (doublon Mail Rules) et I-10 (définitions sans consommateur) ont été traités. Cinq pages statiques et une page générique sont devenues des redirections vers Email. Le reliquat passe de 115 à **109 pages** (90 statiques + 19 génériques), avec toujours 26 centres partiels. Les tableaux et empreintes JSON ci-dessous restent la photographie **avant** cette migration.

Périmètre : **tous les fichiers de `WCP360_Prototype`**, soit **343 fichiers sources et références examinés** : 232 HTML, 55 JavaScript, 32 CSS, 22 Markdown, 1 ICO et 1 PowerShell. Ce rapport généré est exclu de l’inventaire source ; le dossier contient donc 344 fichiers après son ajout. Les autres projets du workspace ne sont pas couverts.

**Il reste 115 pages produit à intégrer : 95 pages statiques et 20 maquettes à actions génériques.** En complément, 26 centres ont une intégration partielle à terminer. Les 15 pages classées « parcours migrés et testés » ont des scénarios de simulation vérifiés ; ce classement ne certifie pas l’achèvement de toutes les fonctions du domaine.

Les 58 redirections de compatibilité et les 13 pages d’accueil/conception sont intentionnelles. Elles ne doivent pas être supprimées comme du travail oublié. Aucun fichier applicatif n’a été supprimé ou modifié dans cette passe ; seul le README reçoit un lien vers ce bilan.

## Méthode et limites

Inventaire récursif complet, lecture automatique intégrale des 342 fichiers texte, vérification des métadonnées et de l’en-tête du favicon, extraction des scripts et des chargements dynamiques, exécution du registre de navigation sans initialiser le DOM, puis revue ciblée des contrôleurs et des parcours encore génériques. Les 343 entrées de cet inventaire historique figurent ci-dessous. Les résultats de la révision courante sont consignés dans le [bilan de vérification](24-verification-record.md). Le graphe de références est lexical : une référence ne prouve pas qu’un chemin est exécuté.

Une page statique peut avoir une sidebar, des onglets, une recherche ou un Job Center actifs grâce aux scripts communs. Elle reste classée statique lorsque son contenu métier fixe ne lit ni ne modifie l’état de son centre. Inversement, un centre partiel peut déjà disposer de données persistantes et de contrats ROOT → USER utiles.

Les priorités P0/P1/P2 ci-dessous désignent l’ordre proposé pour rendre la démonstration cohérente, pas une classification de vulnérabilités. L’analyse n’est ni une revue exhaustive ligne par ligne de chaque action, ni une validation visuelle. Aucun navigateur n’a été piloté ; les commandes système, services, DNS, Git et envois mail restent simulés.

## Répartition des 232 pages

| État | Pages | Interprétation |
|---|---:|---|
| Page produit statique à intégrer | 95 | 44 USER et 51 ROOT ; raccorder ou consolider |
| Maquette avec actions génériques à intégrer | 20 | 16 hosting-parity et 4 root-control |
| Centre partiellement intégré | 26 | 4 centres mixtes + 22 autres centres dédiés |
| Parcours migrés et testés | 15 | Scénarios locaux vérifiés, périmètre limité |
| Runtime partagé / accès de démonstration | 5 | 3 vues du runtime et 2 pages d’accès démo |
| Redirection de compatibilité intentionnelle | 58 | La maturité dépend de la cible |
| Accueil, galerie ou référence de conception | 13 | Galerie, accueil et références de validation |

## Ordre proposé pour les prochaines intégrations

| Priorité | Lot | Travail concret |
|---|---|---|
| P0 | Sites, Domaines, Comptes | Raccorder création → résultat du job ; relier Domaines et les vues de détail ; éviter les ressources bloquées en Provisioning. |
| P0 | Mail, Bases, accès fichiers | Un état par famille ; consolider Mail Rules ; rendre clone/import/export/téléchargements observables ; brancher les anciennes pages de détail. |
| P0 | IAM et sécurité de démonstration | Persister rôles/plans/identités et les utiliser dans les vues concernées ; alimenter sessions, tokens et révocations depuis un même modèle. |
| P0 | Approbations, audit, notifications | Créer des objets métier partagés ; relier une approbation à une opération et à son résultat ; afficher le vrai historique de simulation. |
| P1 | Infrastructure ROOT | Serveurs, services, réseau/IP/firewall, stockage, processus, sauvegardes et maintenance : fixtures → état partagé et résultats. |
| P1 | Centres ROOT déjà dédiés | Consolider les contrats et tests DNS/TLS/mail/runtime/catalogue/extensions/settings ; traiter ensuite HA/DR, providers, migrations et gouvernance. |
| P1/P2 | Dashboards, détails, aide et profil | Projections de données partagées ; routes de détail contextuelles ; profil, préférences et support ; validation navigateur. |

## Constats vérifiés

### I-01 — P0 — 95 pages produit sans contrôleur métier dédié

Les scripts communs assurent navigation, dialogues et jobs génériques. Aucun script dédié ou inline dans ces 95 pages ne synchronise leur contenu avec les états des centres. 44 pages USER et 51 ROOT.

À faire : Raccorder chaque famille à son modèle canonique ; convertir les détails concurrents en projections ou redirections ciblées.

Preuves : [assets/reference-ui.js](assets/reference-ui.js) (ligne 641), [assets/experience-runtime.js](assets/experience-runtime.js) (ligne 103).

### I-02 — P0 — 20 centres ne sauvegardent pas les valeurs de leurs formulaires

La soumission ferme le drawer et crée une opération à partir du titre ; les entrées ne sont pas appliquées à un modèle de ressources. File Access lit les protocoles ROOT, mais ne persiste pas ses identités.

À faire : Remplacer les fixtures par un état par famille, avec validation et résultat de job ; conserver les composants partagés.

Preuves : [assets/hosting-parity.js](assets/hosting-parity.js) (ligne 256), [assets/root-control.js](assets/root-control.js) (ligne 36).

### I-03 — P0 — Règles mail encore doublonnées

mail-rules utilise hosting-parity ; email possède déjà routes, catch-all et responders dans son propre stockage. Les deux surfaces ne partagent pas les modifications.

À faire : Faire de Mail Rules une vue du centre Email et ajouter uniquement les capacités encore absentes.

Preuves : [mail-rules.html](mail-rules.html) (ligne 1), [assets/email-center.js](assets/email-center.js) (ligne 104), [assets/hosting-parity.js](assets/hosting-parity.js) (ligne 256).

### I-04 — P0 — Sites et Comptes peuvent rester en Provisioning après la fin du job

Les créations enregistrent Provisioning puis émettent un job sans retenir son identifiant pour appliquer le résultat. D’autres changements sont immédiats avant la réussite du job.

À faire : Stocker la relation ressource/job ; gérer réussite, échec, annulation et reprise ; réutiliser le modèle déjà présent dans les centres plus récents.

Preuves : [assets/website-center.js](assets/website-center.js) (ligne 63), [assets/root-capacity.js](assets/root-capacity.js) (ligne 99), [assets/root-capacity.js](assets/root-capacity.js) (ligne 103).

### I-05 — P0 — Accès fichiers répartis entre trois surfaces

Sites conserve ses propres accès ; File Access affiche ses fixtures filtrées par les protocoles ROOT ; file-access.html est un détail statique.

À faire : Un inventaire USER d’identités, clés, racines et sessions ; Sites et les détails doivent en être des projections.

Preuves : [assets/website-center.js](assets/website-center.js) (ligne 63), [assets/hosting-parity.js](assets/hosting-parity.js) (ligne 202), [file-access.html](file-access.html) (ligne 1).

### I-06 — P0 — Le centre IAM perd les modifications au rechargement

Invitations, révocation et matrice modifient le DOM ; aucune sauvegarde métier dans ce contrôleur. Plans et permissions ne pilotent donc pas un modèle partagé de bout en bout.

À faire : Modèle IAM/plans persistant de démonstration et consommateurs explicites côté Comptes et Revendeur.

Preuves : [assets/identity-access.js](assets/identity-access.js) (ligne 201), [assets/identity-access.js](assets/identity-access.js) (ligne 221).

### I-07 — P0 — Bases de données : actions présentées comme réalisées mais sans résultat matérialisé

Téléchargement = notification ; clone/SQLite = job sans nouvel objet ; les sept anciennes pages de détail Bases conservent des données fixes.

À faire : Relier imports, exports, sauvegardes, clone et détails au même état ; produire des fichiers de démonstration lorsque le bouton annonce un téléchargement.

Preuves : [assets/database-center.js](assets/database-center.js) (ligne 67), [assets/database-center.js](assets/database-center.js) (ligne 73), [assets/database-completion.js](assets/database-completion.js) (ligne 9).

### I-08 — P0 — Approbations, audit et routage des notifications restent déclaratifs

Les trois familles utilisent hosting-parity ; les formulaires ne créent ni approbation ni règle de routage persistante ni événement d’audit métier partagé.

À faire : Brancher approbation, exécution et preuve sur les mêmes opérations ; alimenter les listes depuis le runtime.

Preuves : [approvals.html](approvals.html) (ligne 1), [audit-explorer.html](audit-explorer.html) (ligne 1), [notification-routing.html](notification-routing.html) (ligne 1).

### I-09 — P1 — 22 autres centres dédiés ont une intégration partielle

Ces centres possèdent du code dédié, souvent du stockage et quelques vrais contrats ROOT → USER ; ils ne sont pas vierges. Les 4 centres mixtes Sites/Comptes/Bases/Mail portent le total partiel à 26.

À faire : Reprendre centre par centre les contrats publiés, états de résultat et tests ; ne pas réécrire les modules déjà utiles.

Preuves : [assets/governance-centers.js](assets/governance-centers.js) (ligne 36), [assets/app-installer.js](assets/app-installer.js) (ligne 44), [assets/control-toolbox.js](assets/control-toolbox.js) (ligne 28).

### I-10 — P1 — 7 définitions historiques sans page consommatrice

hosting-parity : account-operations, reseller-overview, reseller-governance. root-control : accounts, databases, serverconfig, software. Aucun body HTML ne les sélectionne actuellement.

À faire : Supprimer ces définitions lors d’une passe de consolidation, après vérification des contrats ; conserver les 58 redirections compatibles.

Preuves : [assets/hosting-parity.js](assets/hosting-parity.js) (ligne 3), [assets/root-control.js](assets/root-control.js) (ligne 3).

### I-11 — P1 — Les mentions CLOSED historiques ne mesurent pas l’intégration actuelle

Les clôtures antérieures concernent largement la couverture visuelle ou déclarative. Le registre annonce par exemple Mail Rules, Audit et Approvals clôturés alors que leurs actions restent génériques ; le périmètre de l’audit consolidé est ancien.

À faire : Utiliser ce bilan pour suivre l’intégration fonctionnelle et conserver les anciens documents comme historique de conception.

Preuves : [02-gap-register.md](02-gap-register.md) (ligne 30), [AUDIT-CONSOLIDATION.md](AUDIT-CONSOLIDATION.md) (ligne 7).

### I-12 — P1 — Vérification visuelle et interactions natives encore non établies

Les tests exécutés utilisent des adaptateurs de frontière UI ; aucun navigateur n’a été piloté pour cet audit. Les pages de validation sont des références, pas des preuves de conformité.

À faire : Vérifier les parcours critiques dans un navigateur : clavier, focus, dialogues, téléchargements et petits écrans.

Preuves : [validation-keyboard-focus.html](validation-keyboard-focus.html) (ligne 1), [validation-responsive.html](validation-responsive.html) (ligne 1).

## Définitions historiques à consolider

Ces **7 définitions** restent dans les moteurs génériques sans page HTML qui sélectionne leur identifiant. Il s’agit de blocs dans des fichiers utilisés, pas de sept fichiers orphelins. Aucune suppression n’a été réalisée.

| Fichier | Définitions sans consommateur HTML |
|---|---|
| [assets/hosting-parity.js](assets/hosting-parity.js) | `account-operations`, `reseller-overview`, `reseller-governance` |
| [assets/root-control.js](assets/root-control.js) | `accounts`, `databases`, `serverconfig`, `software` |

Les 87 ressources dans `assets/` ont toutes au moins une référence dans du HTML, JavaScript ou CSS. Les chargements dynamiques de `account-recovery`, `database-completion`, `runtime-security`, `auth-runtime`, `product-runtime` et `experience-runtime` ont été pris en compte. Leur absence des balises `<script>` d’une page ne suffit donc pas à les déclarer inutilisées.

## Inventaire HTML complet

### Page produit statique à intégrer — 95

Toutes les pages de cette section utilisent uniquement les interactions communes et ne chargent aucun contrôleur métier dédié ni script inline. Le propriétaire indique la cible à utiliser pour une projection ou une consolidation.

| Fichier | Portail | Propriétaire / cible | Priorité |
|---|---|---|---|
| [admin-backups.html](admin-backups.html) | root | [admin-backups.html](admin-backups.html) | P0 |
| [admin-monitoring.html](admin-monitoring.html) | root | [admin-monitoring.html](admin-monitoring.html) | P1 |
| [admin-security.html](admin-security.html) | root | [admin-security.html](admin-security.html) | P0 |
| [api-webhooks.html](api-webhooks.html) | user | [api-webhooks.html](api-webhooks.html) | P0 |
| [audit.html](audit.html) | root | [audit-explorer.html](audit-explorer.html) | P1 |
| [automations.html](automations.html) | user | [api-webhooks.html](api-webhooks.html) | P0 |
| [break-glass.html](break-glass.html) | root | [break-glass.html](break-glass.html) | P0 |
| [canonical-admin-dashboard.html](canonical-admin-dashboard.html) | root | [canonical-admin-dashboard.html](canonical-admin-dashboard.html) | P0 |
| [canonical-security.html](canonical-security.html) | user | [canonical-security.html](canonical-security.html) | P0 |
| [canonical-server-overview.html](canonical-server-overview.html) | root | [servers.html](servers.html) | P1 |
| [canonical-user-dashboard.html](canonical-user-dashboard.html) | user | [canonical-user-dashboard.html](canonical-user-dashboard.html) | P0 |
| [canonical-website-overview.html](canonical-website-overview.html) | user | [websites.html](websites.html) | P0 |
| [cluster-failover.html](cluster-failover.html) | root | [cluster.html](cluster.html) | P1 |
| [cluster-maintenance.html](cluster-maintenance.html) | root | [cluster.html](cluster.html) | P1 |
| [cluster-nodes.html](cluster-nodes.html) | root | [cluster.html](cluster.html) | P1 |
| [cluster-replication.html](cluster-replication.html) | root | [cluster.html](cluster.html) | P1 |
| [cluster-scenarios.html](cluster-scenarios.html) | root | [cluster.html](cluster.html) | P1 |
| [cluster-storage.html](cluster-storage.html) | root | [cluster.html](cluster.html) | P1 |
| [cluster.html](cluster.html) | root | [cluster.html](cluster.html) | P1 |
| [database-access.html](database-access.html) | user | [databases.html](databases.html) | P0 |
| [database-backups.html](database-backups.html) | user | [databases.html](databases.html) | P0 |
| [database-detail.html](database-detail.html) | user | [databases.html](databases.html) | P0 |
| [database-diagnostics.html](database-diagnostics.html) | user | [databases.html](databases.html) | P0 |
| [database-transfer.html](database-transfer.html) | user | [databases.html](databases.html) | P0 |
| [database-user-detail.html](database-user-detail.html) | user | [databases.html](databases.html) | P0 |
| [database-users.html](database-users.html) | user | [databases.html](databases.html) | P0 |
| [delete-resource.html](delete-resource.html) | user | [canonical-user-dashboard.html](canonical-user-dashboard.html) | P0 |
| [domain-add.html](domain-add.html) | user | [domains.html](domains.html) | P0 |
| [domain-detail.html](domain-detail.html) | user | [domains.html](domains.html) | P0 |
| [domains.html](domains.html) | user | [domains.html](domains.html) | P0 |
| [error-remediation.html](error-remediation.html) | root | [logs-diagnostics.html](logs-diagnostics.html) | P1 |
| [file-access.html](file-access.html) | user | [file-access-center.html](file-access-center.html) | P0 |
| [file-editor.html](file-editor.html) | user | [file-manager.html](file-manager.html) | P0 |
| [file-permissions.html](file-permissions.html) | user | [file-manager.html](file-manager.html) | P0 |
| [firewall-rules.html](firewall-rules.html) | root | [firewall.html](firewall.html) | P1 |
| [firewall.html](firewall.html) | root | [firewall.html](firewall.html) | P1 |
| [help-support.html](help-support.html) | user | [help-support.html](help-support.html) | P2 |
| [impersonation.html](impersonation.html) | root | [impersonation.html](impersonation.html) | P0 |
| [incident-detail.html](incident-detail.html) | root | [incidents.html](incidents.html) | P0 |
| [incidents.html](incidents.html) | root | [incidents.html](incidents.html) | P0 |
| [job-queues.html](job-queues.html) | root | [jobs.html](jobs.html) | P0 |
| [mail-antispam.html](mail-antispam.html) | user | [email.html](email.html) | P0 |
| [mail-authentication.html](mail-authentication.html) | user | [email.html](email.html) | P0 |
| [mail-deliverability.html](mail-deliverability.html) | user | [email.html](email.html) | P0 |
| [mail-domain-add.html](mail-domain-add.html) | user | [email.html](email.html) | P0 |
| [mail-domain-detail.html](mail-domain-detail.html) | user | [email.html](email.html) | P0 |
| [mail-logs.html](mail-logs.html) | user | [email.html](email.html) | P0 |
| [mailbox-detail.html](mailbox-detail.html) | user | [email.html](email.html) | P0 |
| [maintenance.html](maintenance.html) | root | [maintenance.html](maintenance.html) | P1 |
| [monitoring-alerts.html](monitoring-alerts.html) | user | [app-health.html](app-health.html) | P0 |
| [monitoring-capacity.html](monitoring-capacity.html) | user | [app-health.html](app-health.html) | P0 |
| [monitoring-correlation.html](monitoring-correlation.html) | user | [app-health.html](app-health.html) | P0 |
| [monitoring-metrics.html](monitoring-metrics.html) | user | [app-health.html](app-health.html) | P0 |
| [monitoring-slo.html](monitoring-slo.html) | user | [app-health.html](app-health.html) | P0 |
| [monitoring.html](monitoring.html) | user | [app-health.html](app-health.html) | P0 |
| [network-diagnostics.html](network-diagnostics.html) | root | [network-interfaces.html](network-interfaces.html) | P1 |
| [network-interfaces.html](network-interfaces.html) | root | [network-interfaces.html](network-interfaces.html) | P1 |
| [network-nat.html](network-nat.html) | root | [network-routes.html](network-routes.html) | P1 |
| [network-ports.html](network-ports.html) | root | [network-interfaces.html](network-interfaces.html) | P1 |
| [network-routes.html](network-routes.html) | root | [network-routes.html](network-routes.html) | P1 |
| [notification-delivery.html](notification-delivery.html) | root | [notification-routing.html](notification-routing.html) | P1 |
| [notification-preferences.html](notification-preferences.html) | user | [notifications-center.html](notifications-center.html) | P0 |
| [privileged-session.html](privileged-session.html) | root | [privileged-session.html](privileged-session.html) | P0 |
| [process-detail.html](process-detail.html) | root | [processes-workers.html](processes-workers.html) | P1 |
| [processes-workers.html](processes-workers.html) | root | [processes-workers.html](processes-workers.html) | P1 |
| [profile-account-settings.html](profile-account-settings.html) | user | [profile-account-settings.html](profile-account-settings.html) | P2 |
| [restore-tests.html](restore-tests.html) | root | [restore-tests.html](restore-tests.html) | P1 |
| [security-audit.html](security-audit.html) | user | [canonical-security.html](canonical-security.html) | P0 |
| [security-auth.html](security-auth.html) | user | [canonical-security.html](canonical-security.html) | P0 |
| [security-keys.html](security-keys.html) | user | [canonical-security.html](canonical-security.html) | P0 |
| [security-network.html](security-network.html) | user | [canonical-security.html](canonical-security.html) | P0 |
| [security-sessions.html](security-sessions.html) | user | [canonical-security.html](canonical-security.html) | P0 |
| [security-tokens.html](security-tokens.html) | user | [canonical-security.html](canonical-security.html) | P0 |
| [security-waf.html](security-waf.html) | user | [canonical-security.html](canonical-security.html) | P0 |
| [server-actions.html](server-actions.html) | root | [servers.html](servers.html) | P1 |
| [server-maintenance.html](server-maintenance.html) | root | [servers.html](servers.html) | P1 |
| [server-resources.html](server-resources.html) | root | [servers.html](servers.html) | P1 |
| [server-runtime.html](server-runtime.html) | root | [servers.html](servers.html) | P1 |
| [server-services.html](server-services.html) | root | [servers.html](servers.html) | P1 |
| [servers.html](servers.html) | root | [servers.html](servers.html) | P1 |
| [service-detail.html](service-detail.html) | root | [service-manager.html](service-manager.html) | P1 |
| [storage-volumes.html](storage-volumes.html) | root | [storage-volumes.html](storage-volumes.html) | P1 |
| [support-bundle.html](support-bundle.html) | root | [support-bundle.html](support-bundle.html) | P1 |
| [terminal.html](terminal.html) | root | [terminal.html](terminal.html) | P2 |
| [updates-batch.html](updates-batch.html) | root | [updates.html](updates.html) | P1 |
| [updates-exclusions.html](updates-exclusions.html) | root | [updates.html](updates.html) | P1 |
| [updates-history.html](updates-history.html) | root | [updates.html](updates.html) | P1 |
| [updates-kernel.html](updates-kernel.html) | root | [updates.html](updates.html) | P1 |
| [updates-packages.html](updates-packages.html) | root | [updates.html](updates.html) | P1 |
| [updates-schedule.html](updates-schedule.html) | root | [updates.html](updates.html) | P1 |
| [updates.html](updates.html) | root | [updates.html](updates.html) | P1 |
| [volume-detail.html](volume-detail.html) | root | [storage-volumes.html](storage-volumes.html) | P1 |
| [webhook-deliveries.html](webhook-deliveries.html) | user | [api-webhooks.html](api-webhooks.html) | P0 |
| [webhooks.html](webhooks.html) | user | [api-webhooks.html](api-webhooks.html) | P0 |
| [worker-pools.html](worker-pools.html) | root | [processes-workers.html](processes-workers.html) | P1 |

### Maquette avec actions génériques à intégrer — 20

Le formulaire générique crée une opération nommée sans appliquer ses valeurs à l’inventaire affiché. Les boutons secondaires tels que les exports utilisent le même mécanisme.

| Fichier | Portail | Propriétaire / cible | Priorité |
|---|---|---|---|
| [api-automation.html](api-automation.html) | root | [api-automation.html](api-automation.html) | P0 |
| [approval-detail.html](approval-detail.html) | root | [approvals.html](approvals.html) | P0 |
| [approvals.html](approvals.html) | root | [approvals.html](approvals.html) | P0 |
| [audit-explorer.html](audit-explorer.html) | root | [audit-explorer.html](audit-explorer.html) | P0 |
| [bulk-restore.html](bulk-restore.html) | root | [bulk-restore.html](bulk-restore.html) | P0 |
| [extension-platform.html](extension-platform.html) | root | [extension-platform.html](extension-platform.html) | P0 |
| [file-access-center.html](file-access-center.html) | user | [file-access-center.html](file-access-center.html) | P0 |
| [ip-management.html](ip-management.html) | root | [ip-management.html](ip-management.html) | P0 |
| [lifecycle-hooks.html](lifecycle-hooks.html) | root | [lifecycle-hooks.html](lifecycle-hooks.html) | P0 |
| [mail-rules.html](mail-rules.html) | user | [mail-rules.html](mail-rules.html) | P0 |
| [notification-routing.html](notification-routing.html) | root | [notification-routing.html](notification-routing.html) | P0 |
| [platform-readiness.html](platform-readiness.html) | root | [platform-readiness.html](platform-readiness.html) | P2 |
| [provisioning-blueprints.html](provisioning-blueprints.html) | root | [provisioning-blueprints.html](provisioning-blueprints.html) | P0 |
| [resource-diagnostics.html](resource-diagnostics.html) | root | [resource-diagnostics.html](resource-diagnostics.html) | P0 |
| [server-profiles.html](server-profiles.html) | root | [server-profiles.html](server-profiles.html) | P0 |
| [service-config-schemas.html](service-config-schemas.html) | root | [service-config-schemas.html](service-config-schemas.html) | P0 |
| [service-manager.html](service-manager.html) | root | [service-manager.html](service-manager.html) | P0 |
| [transfer-importer.html](transfer-importer.html) | root | [transfer-importer.html](transfer-importer.html) | P0 |
| [transfers.html](transfers.html) | root | [transfers.html](transfers.html) | P0 |
| [usage-metering.html](usage-metering.html) | root | [usage-metering.html](usage-metering.html) | P0 |

### Centre partiellement intégré — 26

| Fichier | Portail | Priorité | Reste à traiter / limite observée |
|---|---|---|---|
| [admin-dns.html](admin-dns.html) | root | P1 | État ROOT persistant et politique partagée avec DNS USER ; mutations et historique à raccorder aux résultats des jobs ; composants historiques. |
| [admin-mail.html](admin-mail.html) | root | P1 | Inventaire ROOT et contrat Webmail persistants ; cycle des changements de service/file et résultats à unifier avec les jobs et la messagerie USER. |
| [app-installer.html](app-installer.html) | user | P1 | Catalogue ROOT effectivement lu ; historique d’installation séparé de Sites/Applications ; soumission enregistre immédiatement puis crée un job. |
| [application-catalog.html](application-catalog.html) | root | P1 | Catalogue ROOT persistant et contrat USER réel ; rapprocher les installations et rollouts avec les états finaux des jobs et les inventaires applicatifs. |
| [certificates.html](certificates.html) | root | P1 | Inventaire ROOT persistant ; contrôleur distinct de SSL USER ; relier publication, génération et résultats des opérations avant de parler de cycle unifié. |
| [continuity-control.html](continuity-control.html) | root | P1 | Listes locales et confirmations ; failover/exercice = job générique ; pas de machine d’état de reprise raccordée. |
| [credential-security.html](credential-security.html) | root | P1 | Formulaires de démonstration créant des jobs ; ni identité/session partagée ni modification d’un modèle de credentials simulés. |
| [database-services.html](database-services.html) | root | P1 | Configuration persistante des outils d’administration ; inventaire nœuds/connexions et changements du service encore à unifier. |
| [databases.html](databases.html) | user | P0 | Grille commune ; import/export/restauration et téléchargement encore génériques ; clone/SQLite créent seulement un job via database-completion.js ; détails historiques non reliés. |
| [delivery-platform.html](delivery-platform.html) | root | P1 | Providers/runners locaux ; webhooks de démonstration fixes ; clés/staging = jobs génériques ; aucun contrat publié consommé par Delivery USER. |
| [dns-toolbox.html](dns-toolbox.html) | root | P1 | Réponses diagnostics de démonstration ; résultats à distinguer de mesures observées ; rapports persistants/export à unifier. |
| [email.html](email.html) | user | P0 | Boîtes et routes persistantes avec validation ; imports CSV et profils client = notifications ; mot de passe applicatif = job générique ; doublon avec mail-rules.html. |
| [file-transfer-services.html](file-transfer-services.html) | root | P1 | Publication ROOT des protocoles consommée par USER ; endpoints/sessions persistants ; identités et opérations USER restent dans la maquette générique. |
| [hosting-accounts.html](hosting-accounts.html) | root | P0 | Formulaires, capacité et récupération testés ; nouveau compte Provisioning sans transition liée au job ; suspension/réactivation et placement appliqués immédiatement ; plans séparés de l’interface IAM. |
| [license-management.html](license-management.html) | root | P1 | État de licence local et formulaires dédiés ; rapprochement des opérations et scénarios d’expiration à compléter. |
| [privacy-governance.html](privacy-governance.html) | root | P1 | Demandes locales et états partiels ; effacement/hold = jobs génériques ; absence de résultat appliqué au modèle partagé. |
| [provider-adapters.html](provider-adapters.html) | root | P1 | Activation/configuration locale des providers ; santé et tests à relier à des observations/versionnements cohérents. |
| [setup-wizard.html](setup-wizard.html) | root | P1 | Brouillon persistant du wizard et de System Settings ; complétions/erreurs des jobs et reprise du parcours à unifier. |
| [software-manager.html](software-manager.html) | root | P1 | Registre et cycle des extensions persistants ; contrats/menus partagés réels ; validation complète des transitions et UI commune encore partielle. |
| [system-settings.html](system-settings.html) | root | P1 | État persistant et formulaires dédiés ; séparer brouillon/publié/résultat des jobs et vérifier les consommateurs effectifs de chaque réglage. |
| [users-roles.html](users-roles.html) | root | P0 | Onglets et filtres dédiés ; invitations et matrice modifient le DOM ; absence de sauvegarde durable des rôles/plans/permissions ; plusieurs actions = notification. |
| [virtualization.html](virtualization.html) | root | P1 | Activation de l’extension persistante ; inventaire et opérations de VM de démonstration à relier à un état de cycle de vie. |
| [web-default-pages.html](web-default-pages.html) | root | P1 | Templates et affectations persistants ; application des versions aux comptes/sites et résultats des opérations à relier. |
| [web-runtime.html](web-runtime.html) | root | P1 | Inventaire ROOT persistant lu par Applications ; installations/affectations et profils de sécurité historiques à raccorder aux résultats des jobs. |
| [webmail.html](webmail.html) | user | P1 | Préférences et historique local de sessions ; aucun moteur de courrier ; conserver une seule projection du contrat Webmail ROOT/USER. |
| [websites.html](websites.html) | user | P0 | Grille et formulaire communs ; création/clonage restent Provisioning sans rapprochement du job ; suppression immédiate ; aperçu = notification ; accès stockés séparément de File Access. |

### Parcours migrés et testés — 15

Ces pages sont déjà traitées sur les parcours décrits dans les migrations 13 à 19. Fichiers = métadonnées ; DNS/TLS = observations simulées ; Git = métadonnées de livraison ; journaux = échantillons fictifs ; règles web = sous-ensemble explicite. La qualité native de l’interface reste à vérifier.

| Fichier | Portail | Propriétaire / cible | Priorité |
|---|---|---|---|
| [app-health.html](app-health.html) | user | [app-health.html](app-health.html) | — |
| [app-lifecycle.html](app-lifecycle.html) | user | [websites.html](websites.html) | — |
| [backups.html](backups.html) | user | [backups.html](backups.html) | — |
| [canonical-dns.html](canonical-dns.html) | user | [domains.html](domains.html) | — |
| [cron.html](cron.html) | user | [cron.html](cron.html) | — |
| [deployments.html](deployments.html) | user | [deployments.html](deployments.html) | — |
| [file-manager.html](file-manager.html) | user | [file-manager.html](file-manager.html) | — |
| [git-repositories.html](git-repositories.html) | user | [git-repositories.html](git-repositories.html) | — |
| [logs-diagnostics.html](logs-diagnostics.html) | root | [logs-diagnostics.html](logs-diagnostics.html) | — |
| [reseller.html](reseller.html) | reseller | [reseller.html#overview](reseller.html#overview) | — |
| [scheduled-tasks.html](scheduled-tasks.html) | root | [scheduled-tasks.html](scheduled-tasks.html) | — |
| [site-analytics.html](site-analytics.html) | user | [site-analytics.html](site-analytics.html) | — |
| [ssl.html](ssl.html) | user | [ssl.html](ssl.html) | — |
| [web-compatibility.html](web-compatibility.html) | user | [web-compatibility.html](web-compatibility.html) | — |
| [web-routing.html](web-routing.html) | root | [web-routing.html](web-routing.html) | — |

### Runtime partagé / accès de démonstration — 5

| Fichier | Portail | Propriétaire / cible | Priorité |
|---|---|---|---|
| [job-detail.html](job-detail.html) | root | [jobs.html](jobs.html) | — |
| [jobs.html](jobs.html) | root | [jobs.html](jobs.html) | — |
| [login.html](login.html) | auth_demo | Flux partagé | — |
| [mfa.html](mfa.html) | auth_demo | Flux partagé | — |
| [notifications-center.html](notifications-center.html) | user | [notifications-center.html](notifications-center.html) | — |

### Redirection de compatibilité intentionnelle — 58

| Fichier | Cible canonique |
|---|---|
| [account-operations.html](account-operations.html) | [hosting-accounts.html#accounts](hosting-accounts.html#accounts) |
| [admin-dashboard.html](admin-dashboard.html) | [canonical-admin-dashboard.html](canonical-admin-dashboard.html) |
| [app-maintenance.html](app-maintenance.html) | [app-lifecycle.html#maintenance](app-lifecycle.html#maintenance) |
| [app-runtime.html](app-runtime.html) | [app-lifecycle.html#runtime](app-lifecycle.html#runtime) |
| [backup-activity.html](backup-activity.html) | [backups.html#activity](backups.html#activity) |
| [backup-policy.html](backup-policy.html) | [backups.html#policy](backups.html#policy) |
| [backup-storage.html](backup-storage.html) | [backups.html#storage](backups.html#storage) |
| [builds.html](builds.html) | [deployments.html#builds](deployments.html#builds) |
| [create-website.html](create-website.html) | [websites.html?action=create](websites.html?action=create) |
| [database-create.html](database-create.html) | [databases.html?action=new-database](databases.html?action=new-database) |
| [database-user-create.html](database-user-create.html) | [databases.html?action=new-user](databases.html?action=new-user) |
| [demo-accounts.html](demo-accounts.html) | [users-roles.html#demo](users-roles.html#demo) |
| [dns-cluster.html](dns-cluster.html) | [admin-dns.html#cluster](admin-dns.html#cluster) |
| [dns-propagation.html](dns-propagation.html) | [canonical-dns.html#propagation](canonical-dns.html#propagation) |
| [dns-record-add.html](dns-record-add.html) | [canonical-dns.html?action=add](canonical-dns.html?action=add) |
| [dns-record-edit.html](dns-record-edit.html) | [canonical-dns.html?action=edit](canonical-dns.html?action=edit) |
| [dns-zone-templates.html](dns-zone-templates.html) | [admin-dns.html#templates](admin-dns.html#templates) |
| [dns-zone.html](dns-zone.html) | [canonical-dns.html](canonical-dns.html) |
| [dns.html](dns.html) | [canonical-dns.html](canonical-dns.html) |
| [dnssec.html](dnssec.html) | [canonical-dns.html#dnssec](canonical-dns.html#dnssec) |
| [effective-permissions.html](effective-permissions.html) | [users-roles.html#permissions](users-roles.html#permissions) |
| [environment-variables.html](environment-variables.html) | [app-lifecycle.html#variables](app-lifecycle.html#variables) |
| [environments.html](environments.html) | [app-lifecycle.html#environments](app-lifecycle.html#environments) |
| [feature-lists.html](feature-lists.html) | [users-roles.html#features](users-roles.html#features) |
| [file-search.html](file-search.html) | [file-manager.html#files](file-manager.html#files) |
| [file-settings.html](file-settings.html) | [file-manager.html#settings](file-manager.html#settings) |
| [file-transfers.html](file-transfers.html) | [file-manager.html#transfers](file-manager.html#transfers) |
| [file-trash.html](file-trash.html) | [file-manager.html#trash](file-manager.html#trash) |
| [file-upload.html](file-upload.html) | [file-manager.html?action=upload](file-manager.html?action=upload) |
| [language-runtimes.html](language-runtimes.html) | [web-runtime.html#languages](web-runtime.html#languages) |
| [mail-alias-create.html](mail-alias-create.html) | [email.html?action=new-alias](email.html?action=new-alias) |
| [mail-aliases.html](mail-aliases.html) | [email.html#routing](email.html#routing) |
| [mail-queue.html](mail-queue.html) | [admin-mail.html#queue](admin-mail.html#queue) |
| [mail-server-config.html](mail-server-config.html) | [admin-mail.html#smtp](admin-mail.html#smtp) |
| [mailbox-create.html](mailbox-create.html) | [email.html?action=new-mailbox](email.html?action=new-mailbox) |
| [mailboxes.html](mailboxes.html) | [email.html#mailboxes](email.html#mailboxes) |
| [plans.html](plans.html) | [users-roles.html#plans](users-roles.html#plans) |
| [quotas.html](quotas.html) | [users-roles.html#quotas](users-roles.html#quotas) |
| [release-rollback.html](release-rollback.html) | [deployments.html?action=rollback#releases](deployments.html?action=rollback#releases) |
| [releases.html](releases.html) | [deployments.html#releases](deployments.html#releases) |
| [reseller-governance.html](reseller-governance.html) | [reseller.html#governance](reseller.html#governance) |
| [resellers-access.html](resellers-access.html) | [users-roles.html#resellers](users-roles.html#resellers) |
| [restore-backup.html](restore-backup.html) | [backups.html?action=restore](backups.html?action=restore) |
| [restore-points.html](restore-points.html) | [backups.html#points](backups.html#points) |
| [restore-review.html](restore-review.html) | [backups.html?action=restore](backups.html?action=restore) |
| [restore-wizard.html](restore-wizard.html) | [backups.html?action=restore](backups.html?action=restore) |
| [roles.html](roles.html) | [users-roles.html#roles](users-roles.html#roles) |
| [runtime-modules.html](runtime-modules.html) | [web-runtime.html#modules](web-runtime.html#modules) |
| [security.html](security.html) | [canonical-security.html](canonical-security.html) |
| [server-configuration.html](server-configuration.html) | [system-settings.html#server](system-settings.html#server) |
| [server-logs.html](server-logs.html) | [logs-diagnostics.html#logs](logs-diagnostics.html#logs) |
| [server-overview.html](server-overview.html) | [canonical-server-overview.html](canonical-server-overview.html) |
| [ssl-certificate-detail.html](ssl-certificate-detail.html) | [ssl.html?action=inspect](ssl.html?action=inspect) |
| [ssl-tls.html](ssl-tls.html) | [ssl.html#certificates](ssl.html#certificates) |
| [user-dashboard.html](user-dashboard.html) | [canonical-user-dashboard.html](canonical-user-dashboard.html) |
| [web-stack-builder.html](web-stack-builder.html) | [web-runtime.html#profiles](web-runtime.html#profiles) |
| [webmail-services.html](webmail-services.html) | [admin-mail.html#webmail](admin-mail.html#webmail) |
| [website-overview.html](website-overview.html) | [canonical-website-overview.html](canonical-website-overview.html) |

### Accueil, galerie ou référence de conception — 13

| Fichier | Portail | Propriétaire / cible | Priorité |
|---|---|---|---|
| [components.html](components.html) | design | [components.html](components.html) | — |
| [index.html](index.html) | design | [index.html](index.html) | — |
| [interaction-lab.html](interaction-lab.html) | design | [interaction-lab.html](interaction-lab.html) | — |
| [signature-index.html](signature-index.html) | design | [signature-index.html](signature-index.html) | — |
| [theme-system.html](theme-system.html) | design | [theme-system.html](theme-system.html) | — |
| [validation-accessibility.html](validation-accessibility.html) | design | [validation-checklist.html](validation-checklist.html) | — |
| [validation-checklist.html](validation-checklist.html) | design | [validation-checklist.html](validation-checklist.html) | — |
| [validation-contrast.html](validation-contrast.html) | design | [validation-checklist.html](validation-checklist.html) | — |
| [validation-keyboard-focus.html](validation-keyboard-focus.html) | design | [validation-checklist.html](validation-checklist.html) | — |
| [validation-reduced-motion.html](validation-reduced-motion.html) | design | [validation-checklist.html](validation-checklist.html) | — |
| [validation-responsive.html](validation-responsive.html) | design | [validation-checklist.html](validation-checklist.html) | — |
| [validation-rtl-i18n.html](validation-rtl-i18n.html) | design | [validation-checklist.html](validation-checklist.html) | — |
| [validation-screen-reader.html](validation-screen-reader.html) | design | [validation-checklist.html](validation-checklist.html) | — |

## Inventaire des 55 scripts

Tous les scripts passent la compilation syntaxique. La colonne « Suites citées » indique les suites de comportement réexécutées qui mentionnent le fichier ; elle ne représente pas une mesure de couverture de branches. Les autres outils runtime historiques n’ont pas été exécutés dans cet audit. Les centres partiels sont détaillés dans la section HTML.

| Fichier | Rôle | Suites citées |
|---|---|
| [assets/account-certificates.js](assets/account-certificates.js) | Contrôleur dédié : voir ses pages consommatrices | dns_tls |
| [assets/account-dns.js](assets/account-dns.js) | Contrôleur dédié : voir ses pages consommatrices | dns_tls |
| [assets/account-recovery.js](assets/account-recovery.js) | Récupération des comptes, chargée par reference-ui | hosting |
| [assets/app-installer.js](assets/app-installer.js) | Contrôleur dédié : voir ses pages consommatrices | — |
| [assets/app.js](assets/app.js) | Compatibilité des interactions historiques | workspace |
| [assets/application-catalog.js](assets/application-catalog.js) | Contrôleur dédié : voir ses pages consommatrices | — |
| [assets/application-center.js](assets/application-center.js) | Contrôleur dédié : voir ses pages consommatrices | applications |
| [assets/application-schemas.js](assets/application-schemas.js) | Contrats et validation réutilisables | applications |
| [assets/auth-runtime.js](assets/auth-runtime.js) | Accès de démonstration et choix USER/ROOT, chargé dynamiquement | — |
| [assets/backup-center.js](assets/backup-center.js) | Contrôleur dédié : voir ses pages consommatrices | protection |
| [assets/certificate-center.js](assets/certificate-center.js) | Contrôleur dédié : voir ses pages consommatrices | — |
| [assets/control-toolbox.js](assets/control-toolbox.js) | Contrôleur dédié : voir ses pages consommatrices | — |
| [assets/database-center.js](assets/database-center.js) | Contrôleur dédié : voir ses pages consommatrices | — |
| [assets/database-completion.js](assets/database-completion.js) | Clone/SQLite historiques : formulaires et émission de job seulement | — |
| [assets/database-services.js](assets/database-services.js) | Contrôleur dédié : voir ses pages consommatrices | — |
| [assets/default-pages.js](assets/default-pages.js) | Contrôleur dédié : voir ses pages consommatrices | — |
| [assets/delivery-center.js](assets/delivery-center.js) | Contrôleur dédié : voir ses pages consommatrices | delivery |
| [assets/delivery-schemas.js](assets/delivery-schemas.js) | Contrats et validation réutilisables | delivery |
| [assets/dns-center.js](assets/dns-center.js) | Contrôleur dédié : voir ses pages consommatrices | — |
| [assets/dns-tls-schemas.js](assets/dns-tls-schemas.js) | Contrats et validation réutilisables | dns_tls, delivery, web_rules |
| [assets/email-center.js](assets/email-center.js) | Contrôleur dédié : voir ses pages consommatrices | hosting |
| [assets/experience-runtime.js](assets/experience-runtime.js) | Favoris, contexte et revue générique ; insights/score fixes | — |
| [assets/extension-guard.js](assets/extension-guard.js) | Disponibilité des extensions et états de repli | — |
| [assets/file-manager.js](assets/file-manager.js) | Contrôleur dédié : voir ses pages consommatrices | workspace |
| [assets/file-transfer-center.js](assets/file-transfer-center.js) | Contrôleur dédié : voir ses pages consommatrices | — |
| [assets/foundation-gallery.js](assets/foundation-gallery.js) | Démonstration du socle UI | foundation |
| [assets/foundation.js](assets/foundation.js) | Composants UI et moteurs communs testés | foundation, hosting, workspace, protection, dns_tls, delivery, applications, observability, web_rules |
| [assets/governance-centers.js](assets/governance-centers.js) | Contrôleur dédié : voir ses pages consommatrices | — |
| [assets/hosting-parity.js](assets/hosting-parity.js) | Moteur de maquettes génériques à remplacer progressivement | — |
| [assets/hosting-schemas.js](assets/hosting-schemas.js) | Contrats et validation réutilisables | hosting |
| [assets/identity-access.js](assets/identity-access.js) | Contrôleur dédié : voir ses pages consommatrices | — |
| [assets/license-center.js](assets/license-center.js) | Contrôleur dédié : voir ses pages consommatrices | — |
| [assets/mail-infrastructure.js](assets/mail-infrastructure.js) | Contrôleur dédié : voir ses pages consommatrices | — |
| [assets/observability-center.js](assets/observability-center.js) | Contrôleur dédié : voir ses pages consommatrices | observability |
| [assets/observability-schemas.js](assets/observability-schemas.js) | Contrats et validation réutilisables | observability |
| [assets/operations-core.js](assets/operations-core.js) | Formulaires, stockage et opérations partagés | protection, dns_tls, delivery, applications, observability, web_rules |
| [assets/platform-software.js](assets/platform-software.js) | Contrôleur dédié : voir ses pages consommatrices | — |
| [assets/product-runtime.js](assets/product-runtime.js) | Jobs et notifications persistants de simulation | — |
| [assets/protection-schemas.js](assets/protection-schemas.js) | Contrats et validation réutilisables | protection |
| [assets/prototype-home.js](assets/prototype-home.js) | Accueil et catalogue par portail | — |
| [assets/reference-ui.js](assets/reference-ui.js) | Navigation, alias, shell et chargements dynamiques | foundation, delivery, applications, observability, web_rules |
| [assets/reseller-center.js](assets/reseller-center.js) | Contrôleur dédié : voir ses pages consommatrices | workspace |
| [assets/root-capacity.js](assets/root-capacity.js) | Contrôleur dédié : voir ses pages consommatrices | hosting |
| [assets/root-control.js](assets/root-control.js) | Moteur de maquettes génériques à remplacer progressivement | — |
| [assets/runtime-center.js](assets/runtime-center.js) | Contrôleur dédié : voir ses pages consommatrices | — |
| [assets/runtime-security.js](assets/runtime-security.js) | Complément historique de profils PHP Security, chargé dynamiquement | — |
| [assets/schedule-center.js](assets/schedule-center.js) | Contrôleur dédié : voir ses pages consommatrices | protection |
| [assets/setup-wizard.js](assets/setup-wizard.js) | Contrôleur dédié : voir ses pages consommatrices | — |
| [assets/system-settings.js](assets/system-settings.js) | Contrôleur dédié : voir ses pages consommatrices | — |
| [assets/web-rules-center.js](assets/web-rules-center.js) | Contrôleur dédié : voir ses pages consommatrices | web_rules |
| [assets/web-rules-schemas.js](assets/web-rules-schemas.js) | Contrats et validation réutilisables | web_rules |
| [assets/webmail.js](assets/webmail.js) | Contrôleur dédié : voir ses pages consommatrices | — |
| [assets/website-center.js](assets/website-center.js) | Contrôleur dédié : voir ses pages consommatrices | delivery |
| [assets/website-model.js](assets/website-model.js) | Inventaire partagé Sites/Applications | delivery, applications, observability, web_rules |
| [assets/workspace-schemas.js](assets/workspace-schemas.js) | Contrats et validation réutilisables | workspace |

## Inventaire des 32 feuilles de styles

Chaque feuille possède au moins une référence dans les sources. Elles ont été lues pour l’inventaire des ressources ; leur présence et leur syntaxe textuelle ne prouvent pas un rendu correct. Aucune refonte du shell V2.5.3.12 n’est demandée par cet audit.

| Fichier | Sources qui le référencent |
|---|---|
| [assets/application-catalog.css](assets/application-catalog.css) | [application-catalog.html](application-catalog.html) |
| [assets/certificate-center.css](assets/certificate-center.css) | [certificates.html](certificates.html) |
| [assets/control-toolbox.css](assets/control-toolbox.css) | [assets/database-completion.js](assets/database-completion.js), [assets/runtime-security.js](assets/runtime-security.js), [continuity-control.html](continuity-control.html), [credential-security.html](credential-security.html), [delivery-platform.html](delivery-platform.html), [dns-toolbox.html](dns-toolbox.html), [privacy-governance.html](privacy-governance.html), [provider-adapters.html](provider-adapters.html), [virtualization.html](virtualization.html) |
| [assets/database-center.css](assets/database-center.css) | [databases.html](databases.html) |
| [assets/database-services.css](assets/database-services.css) | [database-services.html](database-services.html) |
| [assets/default-pages.css](assets/default-pages.css) | [web-default-pages.html](web-default-pages.html) |
| [assets/dns-center.css](assets/dns-center.css) | [admin-dns.html](admin-dns.html) |
| [assets/email-center.css](assets/email-center.css) | [email.html](email.html) |
| [assets/experience-runtime.css](assets/experience-runtime.css) | [assets/reference-ui.js](assets/reference-ui.js) |
| [assets/file-manager.css](assets/file-manager.css) | [file-manager.html](file-manager.html) |
| [assets/file-transfer-center.css](assets/file-transfer-center.css) | [file-transfer-services.html](file-transfer-services.html) |
| [assets/foundation.css](assets/foundation.css) | [assets/foundation.js](assets/foundation.js), [components.html](components.html) |
| [assets/hosting-parity.css](assets/hosting-parity.css) | [approval-detail.html](approval-detail.html), [approvals.html](approvals.html), [audit-explorer.html](audit-explorer.html), [bulk-restore.html](bulk-restore.html), [extension-platform.html](extension-platform.html), [file-access-center.html](file-access-center.html), [lifecycle-hooks.html](lifecycle-hooks.html), [mail-rules.html](mail-rules.html), [notification-routing.html](notification-routing.html), [platform-readiness.html](platform-readiness.html), [provisioning-blueprints.html](provisioning-blueprints.html), [resource-diagnostics.html](resource-diagnostics.html), [server-profiles.html](server-profiles.html), [service-config-schemas.html](service-config-schemas.html), [transfer-importer.html](transfer-importer.html), [usage-metering.html](usage-metering.html) |
| [assets/identity-access.css](assets/identity-access.css) | [users-roles.html](users-roles.html) |
| [assets/license-center.css](assets/license-center.css) | [license-management.html](license-management.html) |
| [assets/mail-infrastructure.css](assets/mail-infrastructure.css) | [admin-mail.html](admin-mail.html) |
| [assets/operations.css](assets/operations.css) | [app-health.html](app-health.html), [app-lifecycle.html](app-lifecycle.html), [backups.html](backups.html), [canonical-dns.html](canonical-dns.html), [cron.html](cron.html), [deployments.html](deployments.html), [git-repositories.html](git-repositories.html), [logs-diagnostics.html](logs-diagnostics.html), [scheduled-tasks.html](scheduled-tasks.html), [site-analytics.html](site-analytics.html), [ssl.html](ssl.html), [web-compatibility.html](web-compatibility.html), [web-routing.html](web-routing.html) |
| [assets/platform-software.css](assets/platform-software.css) | [software-manager.html](software-manager.html) |
| [assets/product-runtime.css](assets/product-runtime.css) | [assets/reference-ui.js](assets/reference-ui.js), [job-detail.html](job-detail.html), [jobs.html](jobs.html), [notifications-center.html](notifications-center.html) |
| [assets/prototype-home.css](assets/prototype-home.css) | [index.html](index.html) |
| [assets/readable-type.css](assets/readable-type.css) | [assets/reference-ui.js](assets/reference-ui.js) |
| [assets/reference-ui.css](assets/reference-ui.css) | [account-operations.html](account-operations.html), [admin-backups.html](admin-backups.html), [admin-dashboard.html](admin-dashboard.html), [admin-dns.html](admin-dns.html), [admin-mail.html](admin-mail.html), [admin-monitoring.html](admin-monitoring.html), [admin-security.html](admin-security.html), [api-automation.html](api-automation.html), [api-webhooks.html](api-webhooks.html), [app-health.html](app-health.html), [app-installer.html](app-installer.html), [app-lifecycle.html](app-lifecycle.html), [app-maintenance.html](app-maintenance.html), [app-runtime.html](app-runtime.html), [application-catalog.html](application-catalog.html), [approval-detail.html](approval-detail.html), [approvals.html](approvals.html), [audit-explorer.html](audit-explorer.html), [audit.html](audit.html), [automations.html](automations.html), [backup-activity.html](backup-activity.html), [backup-policy.html](backup-policy.html), [backup-storage.html](backup-storage.html), [backups.html](backups.html), [break-glass.html](break-glass.html), [builds.html](builds.html), [bulk-restore.html](bulk-restore.html), [canonical-admin-dashboard.html](canonical-admin-dashboard.html), [canonical-dns.html](canonical-dns.html), [canonical-security.html](canonical-security.html), [canonical-server-overview.html](canonical-server-overview.html), [canonical-user-dashboard.html](canonical-user-dashboard.html), [canonical-website-overview.html](canonical-website-overview.html), [certificates.html](certificates.html), [cluster-failover.html](cluster-failover.html), [cluster-maintenance.html](cluster-maintenance.html), [cluster-nodes.html](cluster-nodes.html), [cluster-replication.html](cluster-replication.html), [cluster-scenarios.html](cluster-scenarios.html), [cluster-storage.html](cluster-storage.html), [cluster.html](cluster.html), [components.html](components.html), [continuity-control.html](continuity-control.html), [create-website.html](create-website.html), [credential-security.html](credential-security.html), [cron.html](cron.html), [database-access.html](database-access.html), [database-backups.html](database-backups.html), [database-create.html](database-create.html), [database-detail.html](database-detail.html), [database-diagnostics.html](database-diagnostics.html), [database-services.html](database-services.html), [database-transfer.html](database-transfer.html), [database-user-create.html](database-user-create.html), [database-user-detail.html](database-user-detail.html), [database-users.html](database-users.html), [databases.html](databases.html), [delete-resource.html](delete-resource.html), [delivery-platform.html](delivery-platform.html), [demo-accounts.html](demo-accounts.html), [deployments.html](deployments.html), [dns-cluster.html](dns-cluster.html), [dns-propagation.html](dns-propagation.html), [dns-record-add.html](dns-record-add.html), [dns-record-edit.html](dns-record-edit.html), [dns-toolbox.html](dns-toolbox.html), [dns-zone-templates.html](dns-zone-templates.html), [dns-zone.html](dns-zone.html), [dns.html](dns.html), [dnssec.html](dnssec.html), [domain-add.html](domain-add.html), [domain-detail.html](domain-detail.html), [domains.html](domains.html), [effective-permissions.html](effective-permissions.html), [email.html](email.html), [environment-variables.html](environment-variables.html), [environments.html](environments.html), [error-remediation.html](error-remediation.html), [extension-platform.html](extension-platform.html), [feature-lists.html](feature-lists.html), [file-access-center.html](file-access-center.html), [file-access.html](file-access.html), [file-editor.html](file-editor.html), [file-manager.html](file-manager.html), [file-permissions.html](file-permissions.html), [file-search.html](file-search.html), [file-settings.html](file-settings.html), [file-transfer-services.html](file-transfer-services.html), [file-transfers.html](file-transfers.html), [file-trash.html](file-trash.html), [file-upload.html](file-upload.html), [firewall-rules.html](firewall-rules.html), [firewall.html](firewall.html), [git-repositories.html](git-repositories.html), [help-support.html](help-support.html), [hosting-accounts.html](hosting-accounts.html), [impersonation.html](impersonation.html), [incident-detail.html](incident-detail.html), [incidents.html](incidents.html), [index.html](index.html), [interaction-lab.html](interaction-lab.html), [ip-management.html](ip-management.html), [job-detail.html](job-detail.html), [job-queues.html](job-queues.html), [jobs.html](jobs.html), [language-runtimes.html](language-runtimes.html), [license-management.html](license-management.html), [lifecycle-hooks.html](lifecycle-hooks.html), [login.html](login.html), [logs-diagnostics.html](logs-diagnostics.html), [mail-alias-create.html](mail-alias-create.html), [mail-aliases.html](mail-aliases.html), [mail-antispam.html](mail-antispam.html), [mail-authentication.html](mail-authentication.html), [mail-deliverability.html](mail-deliverability.html), [mail-domain-add.html](mail-domain-add.html), [mail-domain-detail.html](mail-domain-detail.html), [mail-logs.html](mail-logs.html), [mail-queue.html](mail-queue.html), [mail-rules.html](mail-rules.html), [mail-server-config.html](mail-server-config.html), [mailbox-create.html](mailbox-create.html), [mailbox-detail.html](mailbox-detail.html), [mailboxes.html](mailboxes.html), [maintenance.html](maintenance.html), [mfa.html](mfa.html), [monitoring-alerts.html](monitoring-alerts.html), [monitoring-capacity.html](monitoring-capacity.html), [monitoring-correlation.html](monitoring-correlation.html), [monitoring-metrics.html](monitoring-metrics.html), [monitoring-slo.html](monitoring-slo.html), [monitoring.html](monitoring.html), [network-diagnostics.html](network-diagnostics.html), [network-interfaces.html](network-interfaces.html), [network-nat.html](network-nat.html), [network-ports.html](network-ports.html), [network-routes.html](network-routes.html), [notification-delivery.html](notification-delivery.html), [notification-preferences.html](notification-preferences.html), [notification-routing.html](notification-routing.html), [notifications-center.html](notifications-center.html), [plans.html](plans.html), [platform-readiness.html](platform-readiness.html), [privacy-governance.html](privacy-governance.html), [privileged-session.html](privileged-session.html), [process-detail.html](process-detail.html), [processes-workers.html](processes-workers.html), [profile-account-settings.html](profile-account-settings.html), [provider-adapters.html](provider-adapters.html), [provisioning-blueprints.html](provisioning-blueprints.html), [quotas.html](quotas.html), [release-rollback.html](release-rollback.html), [releases.html](releases.html), [reseller-governance.html](reseller-governance.html), [reseller.html](reseller.html), [resellers-access.html](resellers-access.html), [resource-diagnostics.html](resource-diagnostics.html), [restore-backup.html](restore-backup.html), [restore-points.html](restore-points.html), [restore-review.html](restore-review.html), [restore-tests.html](restore-tests.html), [restore-wizard.html](restore-wizard.html), [roles.html](roles.html), [runtime-modules.html](runtime-modules.html), [scheduled-tasks.html](scheduled-tasks.html), [security-audit.html](security-audit.html), [security-auth.html](security-auth.html), [security-keys.html](security-keys.html), [security-network.html](security-network.html), [security-sessions.html](security-sessions.html), [security-tokens.html](security-tokens.html), [security-waf.html](security-waf.html), [security.html](security.html), [server-actions.html](server-actions.html), [server-configuration.html](server-configuration.html), [server-logs.html](server-logs.html), [server-maintenance.html](server-maintenance.html), [server-overview.html](server-overview.html), [server-profiles.html](server-profiles.html), [server-resources.html](server-resources.html), [server-runtime.html](server-runtime.html), [server-services.html](server-services.html), [servers.html](servers.html), [service-config-schemas.html](service-config-schemas.html), [service-detail.html](service-detail.html), [service-manager.html](service-manager.html), [setup-wizard.html](setup-wizard.html), [signature-index.html](signature-index.html), [site-analytics.html](site-analytics.html), [software-manager.html](software-manager.html), [ssl-certificate-detail.html](ssl-certificate-detail.html), [ssl-tls.html](ssl-tls.html), [ssl.html](ssl.html), [storage-volumes.html](storage-volumes.html), [support-bundle.html](support-bundle.html), [system-settings.html](system-settings.html), [terminal.html](terminal.html), [theme-system.html](theme-system.html), [transfer-importer.html](transfer-importer.html), [transfers.html](transfers.html), [updates-batch.html](updates-batch.html), [updates-exclusions.html](updates-exclusions.html), [updates-history.html](updates-history.html), [updates-kernel.html](updates-kernel.html), [updates-packages.html](updates-packages.html), [updates-schedule.html](updates-schedule.html), [updates.html](updates.html), [usage-metering.html](usage-metering.html), [user-dashboard.html](user-dashboard.html), [users-roles.html](users-roles.html), [validation-accessibility.html](validation-accessibility.html), [validation-checklist.html](validation-checklist.html), [validation-contrast.html](validation-contrast.html), [validation-keyboard-focus.html](validation-keyboard-focus.html), [validation-reduced-motion.html](validation-reduced-motion.html), [validation-responsive.html](validation-responsive.html), [validation-rtl-i18n.html](validation-rtl-i18n.html), [validation-screen-reader.html](validation-screen-reader.html), [virtualization.html](virtualization.html), [volume-detail.html](volume-detail.html), [web-compatibility.html](web-compatibility.html), [web-default-pages.html](web-default-pages.html), [web-routing.html](web-routing.html), [web-runtime.html](web-runtime.html), [web-stack-builder.html](web-stack-builder.html), [webhook-deliveries.html](webhook-deliveries.html), [webhooks.html](webhooks.html), [webmail-services.html](webmail-services.html), [webmail.html](webmail.html), [website-overview.html](website-overview.html), [websites.html](websites.html), [worker-pools.html](worker-pools.html) |
| [assets/reseller-center.css](assets/reseller-center.css) | [reseller.html](reseller.html) |
| [assets/root-capacity.css](assets/root-capacity.css) | [hosting-accounts.html](hosting-accounts.html) |
| [assets/root-control.css](assets/root-control.css) | [api-automation.html](api-automation.html), [database-services.html](database-services.html), [hosting-accounts.html](hosting-accounts.html), [ip-management.html](ip-management.html), [license-management.html](license-management.html), [service-manager.html](service-manager.html), [software-manager.html](software-manager.html), [system-settings.html](system-settings.html), [transfers.html](transfers.html), [web-default-pages.html](web-default-pages.html) |
| [assets/runtime-center.css](assets/runtime-center.css) | [web-runtime.html](web-runtime.html) |
| [assets/setup-wizard.css](assets/setup-wizard.css) | [setup-wizard.html](setup-wizard.html) |
| [assets/styles.css](assets/styles.css) | [audit.html](audit.html), [delete-resource.html](delete-resource.html), [login.html](login.html), [maintenance.html](maintenance.html), [mfa.html](mfa.html), [signature-index.html](signature-index.html), [terminal.html](terminal.html), [theme-system.html](theme-system.html) |
| [assets/system-settings.css](assets/system-settings.css) | [system-settings.html](system-settings.html) |
| [assets/user-services.css](assets/user-services.css) | [app-installer.html](app-installer.html), [webmail.html](webmail.html) |
| [assets/webmail-services.css](assets/webmail-services.css) | [database-services.html](database-services.html) |
| [assets/website-center.css](assets/website-center.css) | [websites.html](websites.html) |

## Inventaire des 22 documents et des 2 autres fichiers

| Fichier | Statut / rôle |
|---|---|
| [01-functional-coverage-matrix.md](01-functional-coverage-matrix.md) | Audit de conception historique ; ne vaut pas preuve de traitement fonctionnel actuel |
| [02-gap-register.md](02-gap-register.md) | Audit de conception historique ; ne vaut pas preuve de traitement fonctionnel actuel |
| [03-cross-family-dependency-matrix.md](03-cross-family-dependency-matrix.md) | Audit de conception historique ; ne vaut pas preuve de traitement fonctionnel actuel |
| [04-permission-security-audit.md](04-permission-security-audit.md) | Audit de conception historique ; ne vaut pas preuve de traitement fonctionnel actuel |
| [05-state-error-edge-case-audit.md](05-state-error-edge-case-audit.md) | Audit de conception historique ; ne vaut pas preuve de traitement fonctionnel actuel |
| [06-design-freeze-gates.md](06-design-freeze-gates.md) | Audit de conception historique ; ne vaut pas preuve de traitement fonctionnel actuel |
| [07-scope-route-ownership.md](07-scope-route-ownership.md) | Architecture et propriété des routes |
| [08-core-extension-architecture.md](08-core-extension-architecture.md) | Architecture et propriété des routes |
| [09-internal-public-distribution-architecture.md](09-internal-public-distribution-architecture.md) | Architecture et propriété des routes |
| [10-prototype-integration.md](10-prototype-integration.md) | Documentation de migration ; périmètre et compteurs datés à lire avec ce bilan |
| [11-shared-ui-library.md](11-shared-ui-library.md) | Documentation de migration ; périmètre et compteurs datés à lire avec ce bilan |
| [12-mail-hosting-migration.md](12-mail-hosting-migration.md) | Documentation de migration ; périmètre et compteurs datés à lire avec ce bilan |
| [13-file-reseller-migration.md](13-file-reseller-migration.md) | Documentation de migration ; périmètre et compteurs datés à lire avec ce bilan |
| [14-protection-scheduling-migration.md](14-protection-scheduling-migration.md) | Documentation de migration ; périmètre et compteurs datés à lire avec ce bilan |
| [15-account-dns-tls-migration.md](15-account-dns-tls-migration.md) | Documentation de migration ; périmètre et compteurs datés à lire avec ce bilan |
| [16-account-delivery-migration.md](16-account-delivery-migration.md) | Documentation de migration ; périmètre et compteurs datés à lire avec ce bilan |
| [17-application-workspace-migration.md](17-application-workspace-migration.md) | Documentation de migration ; périmètre et compteurs datés à lire avec ce bilan |
| [18-scoped-observability-migration.md](18-scoped-observability-migration.md) | Documentation de migration ; périmètre et compteurs datés à lire avec ce bilan |
| [19-web-rules-policy-migration.md](19-web-rules-policy-migration.md) | Documentation de migration ; périmètre et compteurs datés à lire avec ce bilan |
| [AUDIT-CONSOLIDATION.md](AUDIT-CONSOLIDATION.md) | Audit de conception historique ; ne vaut pas preuve de traitement fonctionnel actuel |
| [README.md](README.md) | Point d’entrée et liens de validation actualisés |
| [STYLE_LOCK.md](STYLE_LOCK.md) | Règles visuelles, baseline V2.5.3.12 |
| [favicon.ico](favicon.ico) | Favicon ; en-tête ICO vérifié, aucune logique métier |
| [serve-prototype.ps1](serve-prototype.ps1) | Serveur local PowerShell ; outil d’aperçu, pas un module produit |

## Contrôles réexécutés

| Contrôle | Résultat |
|---|---|
| `../WCP360_Prototype/tests/audit_wcp360_foundation.mjs` | 29 contrôles réussis |
| `../WCP360_Prototype/tests/audit_wcp360_hosting.mjs` | 27 contrôles réussis |
| `../WCP360_Prototype/tests/audit_wcp360_workspace.mjs` | 38 contrôles réussis |
| `../WCP360_Prototype/tests/audit_wcp360_protection.mjs` | 42 contrôles réussis |
| `../WCP360_Prototype/tests/audit_wcp360_dns_tls.mjs` | 44 contrôles réussis |
| `../WCP360_Prototype/tests/audit_wcp360_delivery.mjs` | 45 contrôles réussis |
| `../WCP360_Prototype/tests/audit_wcp360_applications.mjs` | 49 contrôles réussis |
| `../WCP360_Prototype/tests/audit_wcp360_observability.mjs` | 44 contrôles réussis |
| `../WCP360_Prototype/tests/audit_wcp360_web_rules.mjs` | 51 contrôles réussis |
| Total comportement | **369 contrôles réussis** |
| Intégration statique | **4 064 assertions**, 232 HTML, 2 795 références HTML locales, 58 redirections : réussi |
| Audit de structure du Design System | Aucun écart remonté |
| Compilation JavaScript | 55 / 55 scripts valides |
| Vérification visuelle / navigateur | Non exécutée dans cet audit |

Ces résultats vérifient les contrats testés et la cohérence des fichiers. Ils ne contredisent pas les 115 pages à intégrer : un lien valide ou un job qui progresse ne garantit pas que la ressource concernée change d’état.
