# Inventaire de vérification page par page — 1er novembre 2026

Cet inventaire complète [24-verification-record.md](24-verification-record.md) et [25-ui-ux-remediation.md](25-ui-ux-remediation.md). Il ne remplace ni les décisions de périmètre ni les preuves historiques. Les 232 pages présentes sont examinées individuellement dans leur HTML initial. Une URL répondant en HTTP, un fichier JavaScript présent et un test de composant partagé ne prouvent pas le fonctionnement de la page.

Sur 232 pages : **32 ont une portée de contrôleur explicitement exercée par un adaptateur**, 0 chargent un contrôleur partiellement exercé ailleurs et **200 n'ont pas de preuve de contrôleur ciblé dans les suites référencées**. **0 page vérifiée visuellement ou dans un navigateur par cet inventaire**.

Nature : 64 redirection ; 92 maquette statique / shell ; 59 centre avec contrôleur ; 13 fiche / opération ; 4 entrée. 64 redirections de compatibilité restent distinctes des pages métier.

La colonne « structure » signifie uniquement analyse automatisée du HTML : dépendances locales (paramètres séparés des chemins), présence main/h1, identifiants, ordre Foundation, labels explicites et cibles ARIA initiales. Les fragments avec un id réel sont résolus ; ceux seulement reconnus comme littéraux par un contrôleur restent des candidats. Les paramètres métier, le montage dynamique, la validation native, le clavier, les lecteurs d’écran, le responsive et les contrastes rendus ne sont pas certifiés.

Les suites liées sont recensées par leur exécution explicite du contrôleur dans un adaptateur VM ; cet outil ne les exécute pas. « Partiel » couvre un module présent mais ne prouve pas le parcours de cette page. Les suites partagées Foundation, shell et moteur de tâches ne sont jamais comptées comme validation métier de toutes leurs pages consommatrices. Les grilles et formulaires injectés par JavaScript ne figurent pas dans les nombres initiaux F/C/B/A (formulaires/champs/boutons/éléments à marqueur d’action). Les boutons avec icône que reference-ui.js nomme explicitement au montage ne sont pas signalés à nouveau comme défaut certain ; leur nombre figure dans le CSV sous runtimeNamedButtons.

Pour System Settings, les preuves modèle et stockage sont mentionnées séparément du contrôleur : validation des valeurs et migrations de données, persistance/révisions/concurrence, puis interactions du contrôleur avec un DOM adapté. Un test de modèle ou de stockage ne permet pas de marquer le contrôleur ni le rendu comme vérifié. Les suites de modèles partagés des autres familles sont conservées dans leurs rapports historiques et ne sont pas réattribuées aux pages sans preuve de contrôleur.

## Corrections fonctionnelles complémentaires

Les quatre modules [Service Manager](service-manager.html), [IP Management](ip-management.html), [API & Automation](api-automation.html) et [Transfers](transfers.html) avaient des onglets qui ne modifiaient que leur résumé, des formulaires ignorant les saisies et une cible Manage reprenant le premier exemple. [root-control.js](assets/root-control.js) fournit désormais quatre panneaux distincts, des cibles réelles de ligne, un filtre excluant les états inactifs des résultats sains et un historique de brouillons locaux avec les champs saisis. Les valeurs persistent séparément par module et cible dans `wcp360-root-control-drafts-v1:<module>`. Une erreur de stockage garde l’éditeur ouvert et ne crée ni message de réussite ni tâche. Ces comportements sont exercés par [audit_wcp360_root_control.mjs](tests/audit_wcp360_root_control.mjs). Les inventaires restent fictifs et les tâches simulées.

Les centres [Continuité](continuity-control.html), [Gouvernance des données](privacy-governance.html) et [Plateforme de livraison](delivery-platform.html) récupèrent désormais des collections locales nulles ou malformées sans interrompre leur rendu. Les adresses IPv4/IPv6 sont validées et normalisées avant vérification des doublons ; noms et sujets sont validés, les valeurs de tableau sont rendues comme texte et les requêtes absentes ou déjà soumises sont bloquées. Un échec de persistance annule la mutation et ne crée pas de tâche. [audit_wcp360_governance.mjs](tests/audit_wcp360_governance.mjs) exerce ces régressions avec le véritable contrôleur et des frontières DOM/stockage adaptées.

Le [catalogue d’applications](application-catalog.html), le [gestionnaire de logiciels](software-manager.html) et la [messagerie ROOT](admin-mail.html) disposent aussi d’une suite ciblée : [audit_wcp360_root_centers.mjs](tests/audit_wcp360_root_centers.mjs). Les nouveaux éléments du catalogue restent non publiés jusqu’à une revue locale explicite ; les versions, politiques, profils et demandes conservent les saisies. La recherche avancée filtre la file de courrier locale. Les échecs de stockage ne doivent ni fermer le formulaire ni annoncer une réussite ou créer une tâche. Les contrôles des sources, installations et services restent simulés. Les repères main imbriqués des vues dynamiques Catalogue, DNS ROOT et Services de transfert ont été remplacés par des conteneurs ordinaires ; cette correction HTML ne certifie pas les parcours DNS ou de transfert.

## Constats reproductibles dans le HTML initial

Résultat : 0 erreur(s) structurelle(s) certaine(s), 0 point(s) à revoir (une cible ARIA ou un nom peut être créé au montage). Les lignes concernent les sources, souvent minifiées ; ce ne sont pas des observations de rendu.

| Page : ligne | Niveau | Règle | Constat |
| :--- | :--- | :--- | :--- |
| Toutes | — | — | Aucun défaut détecté par ces règles limitées. |

## Couverture de chaque page

| Page | Nature | Portail déclaré → registre ; destination canonique | Contrôleurs HTML initiaux | F/C/B/A | Structure | Adaptateur ciblé existant (portée) | Navigateur / visuel |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| [account-operations.html](account-operations.html) | redirection | non déclaré → root ; hosting-accounts.html#accounts | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [admin-backups.html](admin-backups.html) | maquette statique / shell | non déclaré → root ; admin-backups.html | — | 0/2/8/0 | automatisée | Aucun | **NON vérifié** |
| [admin-dashboard.html](admin-dashboard.html) | redirection | non déclaré → root ; canonical-admin-dashboard.html | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [admin-dns.html](admin-dns.html) | centre avec contrôleur | non déclaré → root ; admin-dns.html | dns-center.js | 0/0/3/0 | automatisée | Aucun | **NON vérifié** |
| [admin-mail.html](admin-mail.html) | centre avec contrôleur | non déclaré → root ; admin-mail.html | mail-infrastructure.js | 0/0/3/0 | automatisée | [root_centers](tests/audit_wcp360_root_centers.mjs) | **NON vérifié** |
| [admin-monitoring.html](admin-monitoring.html) | maquette statique / shell | non déclaré → root ; admin-monitoring.html | — | 0/2/8/0 | automatisée | Aucun | **NON vérifié** |
| [admin-security.html](admin-security.html) | maquette statique / shell | non déclaré → root ; admin-security.html | — | 0/2/8/0 | automatisée | Aucun | **NON vérifié** |
| [api-automation.html](api-automation.html) | centre avec contrôleur | non déclaré → root ; api-automation.html | root-control.js | 0/0/3/0 | automatisée | [root_control](tests/audit_wcp360_root_control.mjs) | **NON vérifié** |
| [api-webhooks.html](api-webhooks.html) | maquette statique / shell | non déclaré → user ; api-webhooks.html | — | 0/2/7/0 | automatisée | Aucun | **NON vérifié** |
| [app-health.html](app-health.html) | centre avec contrôleur | user → user ; app-health.html | application-center.js | 0/2/6/0 | automatisée | [applications](tests/audit_wcp360_applications.mjs) | **NON vérifié** |
| [app-installer.html](app-installer.html) | centre avec contrôleur | non déclaré → user ; app-installer.html | app-installer.js | 0/0/3/0 | automatisée | Aucun | **NON vérifié** |
| [app-lifecycle.html](app-lifecycle.html) | centre avec contrôleur | user → user ; app-lifecycle.html | application-center.js | 0/2/6/0 | automatisée | [applications](tests/audit_wcp360_applications.mjs) | **NON vérifié** |
| [app-maintenance.html](app-maintenance.html) | redirection | non déclaré → user ; app-lifecycle.html#maintenance | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [app-runtime.html](app-runtime.html) | redirection | non déclaré → user ; app-lifecycle.html#runtime | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [application-catalog.html](application-catalog.html) | centre avec contrôleur | non déclaré → root ; application-catalog.html | application-catalog.js | 0/0/3/0 | automatisée | [root_centers](tests/audit_wcp360_root_centers.mjs) | **NON vérifié** |
| [approval-detail.html](approval-detail.html) | fiche / opération | root → root ; approval-detail.html | hosting-parity.js | 0/0/3/0 | automatisée | Aucun | **NON vérifié** |
| [approvals.html](approvals.html) | centre avec contrôleur | root → root ; approvals.html | hosting-parity.js | 0/0/3/0 | automatisée | Aucun | **NON vérifié** |
| [audit-explorer.html](audit-explorer.html) | centre avec contrôleur | root → root ; audit-explorer.html | hosting-parity.js | 0/0/3/0 | automatisée | Aucun | **NON vérifié** |
| [audit.html](audit.html) | maquette statique / shell | non déclaré → root ; audit.html | — | 0/6/12/3 | automatisée | Aucun | **NON vérifié** |
| [automations.html](automations.html) | maquette statique / shell | non déclaré → user ; automations.html | — | 0/2/7/0 | automatisée | Aucun | **NON vérifié** |
| [backup-activity.html](backup-activity.html) | redirection | non déclaré → user ; backups.html#activity | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [backup-policy.html](backup-policy.html) | redirection | non déclaré → user ; backups.html#policy | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [backup-storage.html](backup-storage.html) | redirection | non déclaré → user ; backups.html#storage | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [backups.html](backups.html) | centre avec contrôleur | user → user ; backups.html | backup-center.js | 0/2/6/0 | automatisée | [protection](tests/audit_wcp360_protection.mjs) | **NON vérifié** |
| [break-glass.html](break-glass.html) | maquette statique / shell | non déclaré → root ; break-glass.html | — | 0/6/7/0 | automatisée | Aucun | **NON vérifié** |
| [builds.html](builds.html) | redirection | non déclaré → user ; deployments.html#builds | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [bulk-restore.html](bulk-restore.html) | centre avec contrôleur | root → root ; bulk-restore.html | hosting-parity.js | 0/0/3/0 | automatisée | Aucun | **NON vérifié** |
| [canonical-admin-dashboard.html](canonical-admin-dashboard.html) | maquette statique / shell | non déclaré → root ; canonical-admin-dashboard.html | — | 0/2/9/0 | automatisée | Aucun | **NON vérifié** |
| [canonical-dns.html](canonical-dns.html) | centre avec contrôleur | user → user ; canonical-dns.html | account-dns.js | 0/2/6/0 | automatisée | [dns_tls](tests/audit_wcp360_dns_tls.mjs) | **NON vérifié** |
| [canonical-security.html](canonical-security.html) | maquette statique / shell | non déclaré → user ; canonical-security.html | — | 0/2/8/0 | automatisée | Aucun | **NON vérifié** |
| [canonical-server-overview.html](canonical-server-overview.html) | maquette statique / shell | non déclaré → root ; canonical-server-overview.html | — | 0/2/8/0 | automatisée | Aucun | **NON vérifié** |
| [canonical-user-dashboard.html](canonical-user-dashboard.html) | maquette statique / shell | non déclaré → user ; canonical-user-dashboard.html | — | 0/2/10/0 | automatisée | Aucun | **NON vérifié** |
| [canonical-website-overview.html](canonical-website-overview.html) | maquette statique / shell | non déclaré → user ; canonical-website-overview.html | — | 0/2/14/0 | automatisée | Aucun | **NON vérifié** |
| [certificates.html](certificates.html) | centre avec contrôleur | non déclaré → root ; certificates.html | certificate-center.js | 0/0/3/0 | automatisée | Aucun | **NON vérifié** |
| [cluster-failover.html](cluster-failover.html) | maquette statique / shell | non déclaré → root ; cluster-failover.html | — | 0/2/8/0 | automatisée | Aucun | **NON vérifié** |
| [cluster-maintenance.html](cluster-maintenance.html) | maquette statique / shell | non déclaré → root ; cluster-maintenance.html | — | 0/2/6/0 | automatisée | Aucun | **NON vérifié** |
| [cluster-nodes.html](cluster-nodes.html) | maquette statique / shell | non déclaré → root ; cluster-nodes.html | — | 0/2/9/0 | automatisée | Aucun | **NON vérifié** |
| [cluster-replication.html](cluster-replication.html) | maquette statique / shell | non déclaré → root ; cluster-replication.html | — | 0/2/9/0 | automatisée | Aucun | **NON vérifié** |
| [cluster-scenarios.html](cluster-scenarios.html) | maquette statique / shell | non déclaré → root ; cluster-scenarios.html | — | 0/2/6/0 | automatisée | Aucun | **NON vérifié** |
| [cluster-storage.html](cluster-storage.html) | maquette statique / shell | non déclaré → root ; cluster-storage.html | — | 0/2/6/0 | automatisée | Aucun | **NON vérifié** |
| [cluster.html](cluster.html) | maquette statique / shell | non déclaré → root ; cluster.html | — | 0/2/8/0 | automatisée | Aucun | **NON vérifié** |
| [components.html](components.html) | centre avec contrôleur | design → design ; components.html | foundation-gallery.js | 0/1/11/0 | automatisée | Aucun | **NON vérifié** |
| [continuity-control.html](continuity-control.html) | centre avec contrôleur | root → root ; continuity-control.html | governance-centers.js | 0/0/2/0 | automatisée | [governance](tests/audit_wcp360_governance.mjs) | **NON vérifié** |
| [create-website.html](create-website.html) | redirection | non déclaré → user ; websites.html?action=create | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [credential-security.html](credential-security.html) | centre avec contrôleur | root → root ; credential-security.html | control-toolbox.js | 0/0/2/0 | automatisée | Aucun | **NON vérifié** |
| [cron.html](cron.html) | centre avec contrôleur | user → user ; cron.html | schedule-center.js | 0/2/6/0 | automatisée | [protection](tests/audit_wcp360_protection.mjs) | **NON vérifié** |
| [database-access.html](database-access.html) | maquette statique / shell | non déclaré → user ; database-access.html | — | 0/2/9/0 | automatisée | Aucun | **NON vérifié** |
| [database-backups.html](database-backups.html) | maquette statique / shell | non déclaré → user ; database-backups.html | — | 0/2/10/0 | automatisée | Aucun | **NON vérifié** |
| [database-create.html](database-create.html) | redirection | non déclaré → user ; databases.html?action=new-database | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [database-detail.html](database-detail.html) | fiche / opération | non déclaré → user ; database-detail.html | — | 0/2/11/0 | automatisée | Aucun | **NON vérifié** |
| [database-diagnostics.html](database-diagnostics.html) | maquette statique / shell | non déclaré → user ; database-diagnostics.html | — | 0/2/7/0 | automatisée | Aucun | **NON vérifié** |
| [database-services.html](database-services.html) | centre avec contrôleur | non déclaré → root ; database-services.html | database-services.js | 0/0/3/0 | automatisée | Aucun | **NON vérifié** |
| [database-transfer.html](database-transfer.html) | maquette statique / shell | non déclaré → user ; database-transfer.html | — | 0/8/8/0 | automatisée | Aucun | **NON vérifié** |
| [database-user-create.html](database-user-create.html) | redirection | non déclaré → user ; databases.html?action=new-user | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [database-user-detail.html](database-user-detail.html) | fiche / opération | non déclaré → user ; database-user-detail.html | — | 0/2/11/0 | automatisée | Aucun | **NON vérifié** |
| [database-users.html](database-users.html) | maquette statique / shell | non déclaré → user ; database-users.html | — | 0/2/6/0 | automatisée | Aucun | **NON vérifié** |
| [databases.html](databases.html) | centre avec contrôleur | non déclaré → user ; databases.html | database-center.js | 0/1/3/0 | automatisée | Aucun | **NON vérifié** |
| [delete-resource.html](delete-resource.html) | fiche / opération | non déclaré → user ; delete-resource.html | — | 0/6/12/3 | automatisée | Aucun | **NON vérifié** |
| [delivery-platform.html](delivery-platform.html) | centre avec contrôleur | root → root ; delivery-platform.html | governance-centers.js | 0/0/2/0 | automatisée | [governance](tests/audit_wcp360_governance.mjs) | **NON vérifié** |
| [demo-accounts.html](demo-accounts.html) | redirection | non déclaré → root ; users-roles.html#demo | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [deployments.html](deployments.html) | centre avec contrôleur | user → user ; deployments.html | delivery-center.js | 0/2/6/0 | automatisée | [delivery](tests/audit_wcp360_delivery.mjs) | **NON vérifié** |
| [dns-cluster.html](dns-cluster.html) | redirection | non déclaré → root ; admin-dns.html#cluster | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [dns-propagation.html](dns-propagation.html) | redirection | non déclaré → user ; canonical-dns.html#propagation | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [dns-record-add.html](dns-record-add.html) | redirection | non déclaré → user ; canonical-dns.html?action=add | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [dns-record-edit.html](dns-record-edit.html) | redirection | non déclaré → user ; canonical-dns.html?action=edit | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [dns-toolbox.html](dns-toolbox.html) | centre avec contrôleur | root → root ; dns-toolbox.html | control-toolbox.js | 0/0/2/0 | automatisée | Aucun | **NON vérifié** |
| [dns-zone-templates.html](dns-zone-templates.html) | redirection | non déclaré → root ; admin-dns.html#templates | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [dns-zone.html](dns-zone.html) | redirection | non déclaré → user ; canonical-dns.html | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [dns.html](dns.html) | redirection | non déclaré → user ; canonical-dns.html | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [dnssec.html](dnssec.html) | redirection | non déclaré → user ; canonical-dns.html#dnssec | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [domain-add.html](domain-add.html) | fiche / opération | non déclaré → user ; domain-add.html | — | 0/8/7/0 | automatisée | Aucun | **NON vérifié** |
| [domain-detail.html](domain-detail.html) | fiche / opération | non déclaré → user ; domain-detail.html | — | 0/2/9/0 | automatisée | Aucun | **NON vérifié** |
| [domains.html](domains.html) | maquette statique / shell | non déclaré → user ; domains.html | — | 0/3/13/0 | automatisée | Aucun | **NON vérifié** |
| [effective-permissions.html](effective-permissions.html) | redirection | non déclaré → root ; users-roles.html#permissions | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [email.html](email.html) | centre avec contrôleur | non déclaré → user ; email.html | mail-rules-center.js, email-center.js | 0/1/3/0 | automatisée | [hosting](tests/audit_wcp360_hosting.mjs) | **NON vérifié** |
| [environment-variables.html](environment-variables.html) | redirection | non déclaré → user ; app-lifecycle.html#variables | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [environments.html](environments.html) | redirection | non déclaré → user ; app-lifecycle.html#environments | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [error-remediation.html](error-remediation.html) | maquette statique / shell | non déclaré → root ; error-remediation.html | — | 0/2/9/0 | automatisée | Aucun | **NON vérifié** |
| [extension-platform.html](extension-platform.html) | centre avec contrôleur | root → root ; extension-platform.html | hosting-parity.js | 0/0/3/0 | automatisée | Aucun | **NON vérifié** |
| [feature-lists.html](feature-lists.html) | redirection | non déclaré → root ; users-roles.html#features | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [file-access-center.html](file-access-center.html) | centre avec contrôleur | user → user ; file-access-center.html | hosting-parity.js | 0/0/3/0 | automatisée | Aucun | **NON vérifié** |
| [file-access.html](file-access.html) | maquette statique / shell | non déclaré → user ; file-access.html | — | 0/2/10/0 | automatisée | Aucun | **NON vérifié** |
| [file-editor.html](file-editor.html) | maquette statique / shell | non déclaré → user ; file-editor.html | — | 0/3/8/0 | automatisée | Aucun | **NON vérifié** |
| [file-manager.html](file-manager.html) | centre avec contrôleur | non déclaré → user ; file-manager.html | file-manager.js | 0/1/3/0 | automatisée | [workspace](tests/audit_wcp360_workspace.mjs) | **NON vérifié** |
| [file-permissions.html](file-permissions.html) | maquette statique / shell | non déclaré → user ; file-permissions.html | — | 0/2/7/0 | automatisée | Aucun | **NON vérifié** |
| [file-search.html](file-search.html) | redirection | non déclaré → user ; file-manager.html#files | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [file-settings.html](file-settings.html) | redirection | non déclaré → user ; file-manager.html#settings | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [file-transfer-services.html](file-transfer-services.html) | centre avec contrôleur | non déclaré → root ; file-transfer-services.html | file-transfer-center.js | 0/0/3/0 | automatisée | Aucun | **NON vérifié** |
| [file-transfers.html](file-transfers.html) | redirection | non déclaré → user ; file-manager.html#transfers | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [file-trash.html](file-trash.html) | redirection | non déclaré → user ; file-manager.html#trash | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [file-upload.html](file-upload.html) | redirection | non déclaré → user ; file-manager.html?action=upload | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [firewall-rules.html](firewall-rules.html) | maquette statique / shell | non déclaré → root ; firewall-rules.html | — | 0/2/12/0 | automatisée | Aucun | **NON vérifié** |
| [firewall.html](firewall.html) | maquette statique / shell | non déclaré → root ; firewall.html | — | 0/2/8/0 | automatisée | Aucun | **NON vérifié** |
| [git-repositories.html](git-repositories.html) | centre avec contrôleur | user → user ; git-repositories.html | delivery-center.js | 0/2/6/0 | automatisée | [delivery](tests/audit_wcp360_delivery.mjs) | **NON vérifié** |
| [help-support.html](help-support.html) | maquette statique / shell | non déclaré → user ; help-support.html | — | 0/2/7/0 | automatisée | Aucun | **NON vérifié** |
| [hosting-accounts.html](hosting-accounts.html) | centre avec contrôleur | non déclaré → root ; hosting-accounts.html | resource-lifecycle.js, hosting-lifecycle.js, root-capacity.js | 0/0/3/0 | automatisée | [hosting](tests/audit_wcp360_hosting.mjs) | **NON vérifié** |
| [impersonation.html](impersonation.html) | maquette statique / shell | non déclaré → root ; impersonation.html | — | 0/6/7/0 | automatisée | Aucun | **NON vérifié** |
| [incident-detail.html](incident-detail.html) | fiche / opération | non déclaré → root ; incident-detail.html | — | 0/2/9/0 | automatisée | Aucun | **NON vérifié** |
| [incidents.html](incidents.html) | maquette statique / shell | non déclaré → root ; incidents.html | — | 0/2/7/0 | automatisée | Aucun | **NON vérifié** |
| [index.html](index.html) | entrée | design → design ; index.html | prototype-home.js | 0/1/9/0 | automatisée | Aucun | **NON vérifié** |
| [interaction-lab.html](interaction-lab.html) | maquette statique / shell | non déclaré → design ; interaction-lab.html | — | 0/3/13/1 | automatisée | Aucun | **NON vérifié** |
| [ip-management.html](ip-management.html) | centre avec contrôleur | non déclaré → root ; ip-management.html | root-control.js | 0/0/3/0 | automatisée | [root_control](tests/audit_wcp360_root_control.mjs) | **NON vérifié** |
| [job-detail.html](job-detail.html) | fiche / opération | root → root ; job-detail.html | — | 0/0/6/0 | automatisée | Aucun | **NON vérifié** |
| [job-queues.html](job-queues.html) | maquette statique / shell | non déclaré → root ; job-queues.html | — | 0/2/6/0 | automatisée | Aucun | **NON vérifié** |
| [jobs.html](jobs.html) | maquette statique / shell | root → root ; jobs.html | — | 0/1/12/0 | automatisée | Aucun | **NON vérifié** |
| [language-runtimes.html](language-runtimes.html) | redirection | non déclaré → root ; web-runtime.html#languages | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [license-management.html](license-management.html) | centre avec contrôleur | non déclaré → root ; license-management.html | license-center.js | 0/0/3/0 | automatisée | Aucun | **NON vérifié** |
| [lifecycle-hooks.html](lifecycle-hooks.html) | centre avec contrôleur | root → root ; lifecycle-hooks.html | hosting-parity.js | 0/0/3/0 | automatisée | Aucun | **NON vérifié** |
| [login.html](login.html) | entrée | non déclaré → auth ; login.html | auth-runtime.js | 1/2/1/0 | automatisée | [auth_html](tests/audit_wcp360_auth_html.mjs) | **NON vérifié** |
| [logs-diagnostics.html](logs-diagnostics.html) | centre avec contrôleur | root → root ; logs-diagnostics.html | observability-center.js | 0/0/3/0 | automatisée | [observability](tests/audit_wcp360_observability.mjs) | **NON vérifié** |
| [mail-alias-create.html](mail-alias-create.html) | redirection | non déclaré → user ; email.html?action=new-alias | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [mail-aliases.html](mail-aliases.html) | redirection | non déclaré → user ; email.html#routing | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [mail-antispam.html](mail-antispam.html) | redirection | non déclaré → user ; email.html#security | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [mail-authentication.html](mail-authentication.html) | redirection | non déclaré → user ; email.html#security | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [mail-deliverability.html](mail-deliverability.html) | redirection | non déclaré → user ; email.html#trace | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [mail-domain-add.html](mail-domain-add.html) | fiche / opération | non déclaré → user ; mail-domain-add.html | — | 0/9/7/0 | automatisée | Aucun | **NON vérifié** |
| [mail-domain-detail.html](mail-domain-detail.html) | fiche / opération | non déclaré → user ; mail-domain-detail.html | — | 0/2/8/0 | automatisée | Aucun | **NON vérifié** |
| [mail-logs.html](mail-logs.html) | redirection | non déclaré → user ; email.html#trace | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [mail-queue.html](mail-queue.html) | redirection | non déclaré → root ; admin-mail.html#queue | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [mail-rules.html](mail-rules.html) | redirection | non déclaré → user ; email.html#rules | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [mail-server-config.html](mail-server-config.html) | redirection | non déclaré → root ; admin-mail.html#smtp | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [mailbox-create.html](mailbox-create.html) | redirection | non déclaré → user ; email.html?action=new-mailbox | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [mailbox-detail.html](mailbox-detail.html) | redirection | non déclaré → user ; email.html#mailboxes | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [mailboxes.html](mailboxes.html) | redirection | non déclaré → user ; email.html#mailboxes | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [maintenance.html](maintenance.html) | maquette statique / shell | non déclaré → root ; maintenance.html | — | 0/5/11/3 | automatisée | Aucun | **NON vérifié** |
| [mfa.html](mfa.html) | entrée | non déclaré → auth ; mfa.html | auth-runtime.js | 1/1/2/0 | automatisée | [auth_html](tests/audit_wcp360_auth_html.mjs) | **NON vérifié** |
| [monitoring-alerts.html](monitoring-alerts.html) | maquette statique / shell | non déclaré → user ; monitoring-alerts.html | — | 0/2/9/0 | automatisée | Aucun | **NON vérifié** |
| [monitoring-capacity.html](monitoring-capacity.html) | maquette statique / shell | non déclaré → user ; monitoring-capacity.html | — | 0/2/6/0 | automatisée | Aucun | **NON vérifié** |
| [monitoring-correlation.html](monitoring-correlation.html) | maquette statique / shell | non déclaré → user ; monitoring-correlation.html | — | 0/2/6/0 | automatisée | Aucun | **NON vérifié** |
| [monitoring-metrics.html](monitoring-metrics.html) | maquette statique / shell | non déclaré → user ; monitoring-metrics.html | — | 0/2/10/0 | automatisée | Aucun | **NON vérifié** |
| [monitoring-slo.html](monitoring-slo.html) | maquette statique / shell | non déclaré → user ; monitoring-slo.html | — | 0/2/6/0 | automatisée | Aucun | **NON vérifié** |
| [monitoring.html](monitoring.html) | maquette statique / shell | non déclaré → user ; monitoring.html | — | 0/2/9/0 | automatisée | Aucun | **NON vérifié** |
| [network-diagnostics.html](network-diagnostics.html) | maquette statique / shell | non déclaré → root ; network-diagnostics.html | — | 0/2/11/0 | automatisée | Aucun | **NON vérifié** |
| [network-interfaces.html](network-interfaces.html) | maquette statique / shell | non déclaré → root ; network-interfaces.html | — | 0/2/10/0 | automatisée | Aucun | **NON vérifié** |
| [network-nat.html](network-nat.html) | maquette statique / shell | non déclaré → root ; network-nat.html | — | 0/2/9/0 | automatisée | Aucun | **NON vérifié** |
| [network-ports.html](network-ports.html) | maquette statique / shell | non déclaré → root ; network-ports.html | — | 0/2/6/0 | automatisée | Aucun | **NON vérifié** |
| [network-routes.html](network-routes.html) | maquette statique / shell | non déclaré → root ; network-routes.html | — | 0/2/6/0 | automatisée | Aucun | **NON vérifié** |
| [notification-delivery.html](notification-delivery.html) | maquette statique / shell | non déclaré → root ; notification-delivery.html | — | 0/2/6/0 | automatisée | Aucun | **NON vérifié** |
| [notification-preferences.html](notification-preferences.html) | maquette statique / shell | non déclaré → user ; notification-preferences.html | — | 0/2/6/0 | automatisée | Aucun | **NON vérifié** |
| [notification-routing.html](notification-routing.html) | centre avec contrôleur | root → root ; notification-routing.html | hosting-parity.js | 0/0/3/0 | automatisée | Aucun | **NON vérifié** |
| [notifications-center.html](notifications-center.html) | maquette statique / shell | user → user ; notifications-center.html | — | 0/0/7/0 | automatisée | Aucun | **NON vérifié** |
| [plans.html](plans.html) | redirection | non déclaré → root ; users-roles.html#plans | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [platform-readiness.html](platform-readiness.html) | centre avec contrôleur | root → root ; platform-readiness.html | hosting-parity.js | 0/0/3/0 | automatisée | Aucun | **NON vérifié** |
| [privacy-governance.html](privacy-governance.html) | centre avec contrôleur | root → root ; privacy-governance.html | governance-centers.js | 0/0/2/0 | automatisée | [governance](tests/audit_wcp360_governance.mjs) | **NON vérifié** |
| [privileged-session.html](privileged-session.html) | maquette statique / shell | non déclaré → root ; privileged-session.html | — | 0/2/7/0 | automatisée | Aucun | **NON vérifié** |
| [process-detail.html](process-detail.html) | fiche / opération | non déclaré → root ; process-detail.html | — | 0/2/8/0 | automatisée | Aucun | **NON vérifié** |
| [processes-workers.html](processes-workers.html) | maquette statique / shell | non déclaré → root ; processes-workers.html | — | 0/2/7/0 | automatisée | Aucun | **NON vérifié** |
| [profile-account-settings.html](profile-account-settings.html) | maquette statique / shell | non déclaré → user ; profile-account-settings.html | — | 0/2/9/0 | automatisée | Aucun | **NON vérifié** |
| [provider-adapters.html](provider-adapters.html) | centre avec contrôleur | root → root ; provider-adapters.html | control-toolbox.js | 0/0/2/0 | automatisée | Aucun | **NON vérifié** |
| [provisioning-blueprints.html](provisioning-blueprints.html) | centre avec contrôleur | root → root ; provisioning-blueprints.html | hosting-parity.js | 0/0/3/0 | automatisée | Aucun | **NON vérifié** |
| [quotas.html](quotas.html) | redirection | non déclaré → root ; users-roles.html#quotas | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [release-rollback.html](release-rollback.html) | redirection | non déclaré → user ; deployments.html?action=rollback#releases | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [releases.html](releases.html) | redirection | non déclaré → user ; deployments.html#releases | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [reseller-governance.html](reseller-governance.html) | redirection | non déclaré → reseller ; reseller.html#governance | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [reseller.html](reseller.html) | centre avec contrôleur | reseller → reseller ; reseller.html | reseller-center.js | 0/0/3/0 | automatisée | [workspace](tests/audit_wcp360_workspace.mjs) | **NON vérifié** |
| [resellers-access.html](resellers-access.html) | redirection | non déclaré → root ; users-roles.html#resellers | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [resource-diagnostics.html](resource-diagnostics.html) | centre avec contrôleur | root → root ; resource-diagnostics.html | hosting-parity.js | 0/0/3/0 | automatisée | Aucun | **NON vérifié** |
| [restore-backup.html](restore-backup.html) | redirection | non déclaré → user ; backups.html?action=restore | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [restore-points.html](restore-points.html) | redirection | non déclaré → user ; backups.html#points | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [restore-review.html](restore-review.html) | redirection | non déclaré → user ; backups.html?action=restore | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [restore-tests.html](restore-tests.html) | maquette statique / shell | non déclaré → root ; restore-tests.html | — | 0/2/9/0 | automatisée | Aucun | **NON vérifié** |
| [restore-wizard.html](restore-wizard.html) | redirection | non déclaré → user ; backups.html?action=restore | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [roles.html](roles.html) | redirection | non déclaré → root ; users-roles.html#roles | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [runtime-modules.html](runtime-modules.html) | redirection | non déclaré → root ; web-runtime.html#modules | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [scheduled-tasks.html](scheduled-tasks.html) | centre avec contrôleur | root → root ; scheduled-tasks.html | schedule-center.js | 0/0/3/0 | automatisée | [protection](tests/audit_wcp360_protection.mjs) | **NON vérifié** |
| [security-audit.html](security-audit.html) | maquette statique / shell | non déclaré → user ; security-audit.html | — | 0/3/12/0 | automatisée | Aucun | **NON vérifié** |
| [security-auth.html](security-auth.html) | maquette statique / shell | non déclaré → user ; security-auth.html | — | 0/2/10/0 | automatisée | Aucun | **NON vérifié** |
| [security-keys.html](security-keys.html) | maquette statique / shell | non déclaré → user ; security-keys.html | — | 0/2/9/0 | automatisée | Aucun | **NON vérifié** |
| [security-network.html](security-network.html) | maquette statique / shell | non déclaré → user ; security-network.html | — | 0/2/10/0 | automatisée | Aucun | **NON vérifié** |
| [security-sessions.html](security-sessions.html) | maquette statique / shell | non déclaré → user ; security-sessions.html | — | 0/2/9/0 | automatisée | Aucun | **NON vérifié** |
| [security-tokens.html](security-tokens.html) | maquette statique / shell | non déclaré → user ; security-tokens.html | — | 0/2/9/0 | automatisée | Aucun | **NON vérifié** |
| [security-waf.html](security-waf.html) | maquette statique / shell | non déclaré → user ; security-waf.html | — | 0/2/11/0 | automatisée | Aucun | **NON vérifié** |
| [security.html](security.html) | redirection | non déclaré → user ; canonical-security.html | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [server-actions.html](server-actions.html) | maquette statique / shell | non déclaré → root ; server-actions.html | — | 0/2/9/0 | automatisée | Aucun | **NON vérifié** |
| [server-configuration.html](server-configuration.html) | redirection | non déclaré → root ; system-settings.html#server | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [server-logs.html](server-logs.html) | redirection | non déclaré → root ; logs-diagnostics.html#logs | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [server-maintenance.html](server-maintenance.html) | maquette statique / shell | non déclaré → root ; server-maintenance.html | — | 0/2/7/0 | automatisée | Aucun | **NON vérifié** |
| [server-overview.html](server-overview.html) | redirection | non déclaré → root ; canonical-server-overview.html | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [server-profiles.html](server-profiles.html) | centre avec contrôleur | root → root ; server-profiles.html | hosting-parity.js | 0/0/3/0 | automatisée | Aucun | **NON vérifié** |
| [server-resources.html](server-resources.html) | maquette statique / shell | non déclaré → root ; server-resources.html | — | 0/2/6/0 | automatisée | Aucun | **NON vérifié** |
| [server-runtime.html](server-runtime.html) | maquette statique / shell | non déclaré → root ; server-runtime.html | — | 0/2/6/0 | automatisée | Aucun | **NON vérifié** |
| [server-services.html](server-services.html) | maquette statique / shell | non déclaré → root ; server-services.html | — | 0/2/10/0 | automatisée | Aucun | **NON vérifié** |
| [servers.html](servers.html) | maquette statique / shell | non déclaré → root ; servers.html | — | 0/3/11/0 | automatisée | Aucun | **NON vérifié** |
| [service-config-schemas.html](service-config-schemas.html) | centre avec contrôleur | root → root ; service-config-schemas.html | hosting-parity.js | 0/0/3/0 | automatisée | Aucun | **NON vérifié** |
| [service-detail.html](service-detail.html) | fiche / opération | non déclaré → root ; service-detail.html | — | 0/2/8/0 | automatisée | Aucun | **NON vérifié** |
| [service-manager.html](service-manager.html) | centre avec contrôleur | non déclaré → root ; service-manager.html | root-control.js | 0/0/3/0 | automatisée | [root_control](tests/audit_wcp360_root_control.mjs) | **NON vérifié** |
| [setup-wizard.html](setup-wizard.html) | entrée | root → root ; setup-wizard.html | setup-wizard.js | 0/0/0/0 | automatisée | [setup](tests/audit_wcp360_setup.mjs) | **NON vérifié** |
| [signature-index.html](signature-index.html) | maquette statique / shell | non déclaré → design ; signature-index.html | — | 0/4/7/1 | automatisée | Aucun | **NON vérifié** |
| [site-analytics.html](site-analytics.html) | centre avec contrôleur | user → user ; site-analytics.html | observability-center.js | 0/0/3/0 | automatisée | [observability](tests/audit_wcp360_observability.mjs) | **NON vérifié** |
| [software-manager.html](software-manager.html) | centre avec contrôleur | non déclaré → root ; software-manager.html | platform-software.js | 0/0/3/0 | automatisée | [root_centers](tests/audit_wcp360_root_centers.mjs) | **NON vérifié** |
| [ssl-certificate-detail.html](ssl-certificate-detail.html) | redirection | non déclaré → user ; ssl.html?action=inspect | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [ssl-tls.html](ssl-tls.html) | redirection | non déclaré → user ; ssl.html#certificates | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [ssl.html](ssl.html) | centre avec contrôleur | user → user ; ssl.html | account-certificates.js | 0/2/6/0 | automatisée | [dns_tls](tests/audit_wcp360_dns_tls.mjs) | **NON vérifié** |
| [storage-volumes.html](storage-volumes.html) | maquette statique / shell | non déclaré → root ; storage-volumes.html | — | 0/2/7/0 | automatisée | Aucun | **NON vérifié** |
| [support-bundle.html](support-bundle.html) | maquette statique / shell | non déclaré → root ; support-bundle.html | — | 0/2/7/0 | automatisée | Aucun | **NON vérifié** |
| [system-settings.html](system-settings.html) | centre avec contrôleur | root → root ; system-settings.html | system-settings.js | 0/0/6/0 | automatisée | [settings_controller](tests/audit_wcp360_settings_controller.mjs) ; modèle/stockage uniquement : [settings_model](tests/audit_wcp360_settings_model.mjs), [settings_store](tests/audit_wcp360_settings_store.mjs) | **NON vérifié** |
| [terminal.html](terminal.html) | maquette statique / shell | non déclaré → root ; terminal.html | — | 0/5/10/3 | automatisée | Aucun | **NON vérifié** |
| [theme-system.html](theme-system.html) | maquette statique / shell | non déclaré → design ; theme-system.html | — | 0/1/10/0 | automatisée | Aucun | **NON vérifié** |
| [transfer-importer.html](transfer-importer.html) | centre avec contrôleur | root → root ; transfer-importer.html | hosting-parity.js | 0/0/3/0 | automatisée | Aucun | **NON vérifié** |
| [transfers.html](transfers.html) | centre avec contrôleur | non déclaré → root ; transfers.html | root-control.js | 0/0/3/0 | automatisée | [root_control](tests/audit_wcp360_root_control.mjs) | **NON vérifié** |
| [updates-batch.html](updates-batch.html) | maquette statique / shell | non déclaré → root ; updates-batch.html | — | 0/2/10/0 | automatisée | Aucun | **NON vérifié** |
| [updates-exclusions.html](updates-exclusions.html) | maquette statique / shell | non déclaré → root ; updates-exclusions.html | — | 0/2/9/0 | automatisée | Aucun | **NON vérifié** |
| [updates-history.html](updates-history.html) | maquette statique / shell | non déclaré → root ; updates-history.html | — | 0/2/9/0 | automatisée | Aucun | **NON vérifié** |
| [updates-kernel.html](updates-kernel.html) | maquette statique / shell | non déclaré → root ; updates-kernel.html | — | 0/2/6/0 | automatisée | Aucun | **NON vérifié** |
| [updates-packages.html](updates-packages.html) | maquette statique / shell | non déclaré → root ; updates-packages.html | — | 0/2/10/0 | automatisée | Aucun | **NON vérifié** |
| [updates-schedule.html](updates-schedule.html) | maquette statique / shell | non déclaré → root ; updates-schedule.html | — | 0/6/7/0 | automatisée | Aucun | **NON vérifié** |
| [updates.html](updates.html) | maquette statique / shell | non déclaré → root ; updates.html | — | 0/2/8/0 | automatisée | Aucun | **NON vérifié** |
| [usage-metering.html](usage-metering.html) | centre avec contrôleur | root → root ; usage-metering.html | hosting-parity.js | 0/0/3/0 | automatisée | Aucun | **NON vérifié** |
| [user-dashboard.html](user-dashboard.html) | redirection | non déclaré → user ; canonical-user-dashboard.html | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [users-roles.html](users-roles.html) | centre avec contrôleur | non déclaré → root ; users-roles.html | identity-access.js | 0/2/8/0 | automatisée | Aucun | **NON vérifié** |
| [validation-accessibility.html](validation-accessibility.html) | maquette statique / shell | non déclaré → design ; validation-accessibility.html | — | 0/2/6/0 | automatisée | Aucun | **NON vérifié** |
| [validation-checklist.html](validation-checklist.html) | maquette statique / shell | non déclaré → design ; validation-checklist.html | — | 0/2/6/0 | automatisée | Aucun | **NON vérifié** |
| [validation-contrast.html](validation-contrast.html) | maquette statique / shell | non déclaré → design ; validation-contrast.html | — | 0/2/6/0 | automatisée | Aucun | **NON vérifié** |
| [validation-keyboard-focus.html](validation-keyboard-focus.html) | maquette statique / shell | non déclaré → design ; validation-keyboard-focus.html | — | 0/2/6/0 | automatisée | Aucun | **NON vérifié** |
| [validation-reduced-motion.html](validation-reduced-motion.html) | maquette statique / shell | non déclaré → design ; validation-reduced-motion.html | — | 0/2/6/0 | automatisée | Aucun | **NON vérifié** |
| [validation-responsive.html](validation-responsive.html) | maquette statique / shell | non déclaré → design ; validation-responsive.html | — | 0/2/6/0 | automatisée | Aucun | **NON vérifié** |
| [validation-rtl-i18n.html](validation-rtl-i18n.html) | maquette statique / shell | non déclaré → design ; validation-rtl-i18n.html | — | 0/2/8/0 | automatisée | Aucun | **NON vérifié** |
| [validation-screen-reader.html](validation-screen-reader.html) | maquette statique / shell | non déclaré → design ; validation-screen-reader.html | — | 0/2/6/0 | automatisée | Aucun | **NON vérifié** |
| [virtualization.html](virtualization.html) | centre avec contrôleur | root → root ; virtualization.html | control-toolbox.js | 0/0/2/0 | automatisée | Aucun | **NON vérifié** |
| [volume-detail.html](volume-detail.html) | fiche / opération | non déclaré → root ; volume-detail.html | — | 0/2/8/0 | automatisée | Aucun | **NON vérifié** |
| [web-compatibility.html](web-compatibility.html) | centre avec contrôleur | user → user ; web-compatibility.html | web-rules-center.js | 0/0/3/0 | automatisée | [web_rules](tests/audit_wcp360_web_rules.mjs) | **NON vérifié** |
| [web-default-pages.html](web-default-pages.html) | centre avec contrôleur | non déclaré → root ; web-default-pages.html | default-pages.js | 0/0/3/0 | automatisée | Aucun | **NON vérifié** |
| [web-routing.html](web-routing.html) | centre avec contrôleur | root → root ; web-routing.html | web-rules-center.js | 0/0/3/0 | automatisée | [web_rules](tests/audit_wcp360_web_rules.mjs) | **NON vérifié** |
| [web-runtime.html](web-runtime.html) | centre avec contrôleur | non déclaré → root ; web-runtime.html | runtime-center.js | 0/0/3/0 | automatisée | Aucun | **NON vérifié** |
| [web-stack-builder.html](web-stack-builder.html) | redirection | non déclaré → root ; web-runtime.html#profiles | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [webhook-deliveries.html](webhook-deliveries.html) | maquette statique / shell | non déclaré → user ; webhook-deliveries.html | — | 0/2/6/0 | automatisée | Aucun | **NON vérifié** |
| [webhooks.html](webhooks.html) | maquette statique / shell | non déclaré → user ; webhooks.html | — | 0/2/9/0 | automatisée | Aucun | **NON vérifié** |
| [webmail-services.html](webmail-services.html) | redirection | non déclaré → root ; admin-mail.html#webmail | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [webmail.html](webmail.html) | centre avec contrôleur | non déclaré → user ; webmail.html | webmail.js | 0/0/3/0 | automatisée | Aucun | **NON vérifié** |
| [website-overview.html](website-overview.html) | redirection | non déclaré → user ; canonical-website-overview.html | — | 0/0/0/0 | automatisée | Aucun | **NON vérifié** |
| [websites.html](websites.html) | centre avec contrôleur | non déclaré → user ; websites.html | resource-lifecycle.js, hosting-lifecycle.js, website-center.js | 0/0/3/0 | automatisée | [delivery](tests/audit_wcp360_delivery.mjs) | **NON vérifié** |
| [worker-pools.html](worker-pools.html) | maquette statique / shell | non déclaré → root ; worker-pools.html | — | 0/2/6/0 | automatisée | Aucun | **NON vérifié** |

## Reproduction

`node tests/audit_wcp360_page_coverage.mjs` affiche le résumé et les constats sans modifier les artefacts. `node tests/audit_wcp360_page_coverage.mjs --write` régénère ce document et [26-page-verification.csv](26-page-verification.csv). `node tests/run.mjs` exécute séparément les suites statiques et les adaptateurs recensés. Le CSV expose les valeurs techniques pour trier les pages et préparer une revue navigateur suivie.
