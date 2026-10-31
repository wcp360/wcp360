# WCP360 Design System

Prototype HTML interactif du Web Control Panel WCP360.

## Utiliser le prototype

Depuis la racine du workspace, démarrez le serveur local sans installer de dépendance :

```powershell
powershell.exe -NoProfile -ExecutionPolicy Bypass -File ".\WCP360_Design_System\serve-prototype.ps1"
```

Ouvrez ensuite **http://127.0.0.1:8790/**. Gardez la console ouverte ; `Ctrl+C` arrête le serveur. Le paramètre `-Port 8792` permet de choisir un autre port.

L’accueil propose les portails USER, RESELLER et ROOT, un catalogue par catégorie et une recherche. Les données sont fictives et les actions restent simulées dans le navigateur. Utilisez le même hôte et le même port pour conserver les données entre les pages ; l’ouverture directe en `file://` ne garantit pas un stockage partagé.

Les détails de cette intégration et ses limites de vérification figurent dans [10-prototype-integration.md](10-prototype-integration.md).

## Bibliothèque réutilisable

[WCP360 UI Foundation](11-shared-ui-library.md) fournit des composants sans dépendance pour Core, Internal et les extensions. Les listes et formulaires de Sites, Bases de données, Messagerie et Comptes l’utilisent déjà ; les archives de récupération, le gestionnaire de fichiers, le centre revendeur, les sauvegardes, la planification USER/ROOT, le DNS du compte, les certificats USER, les parcours Git/déploiement et applicatifs, les journaux USER/ROOT et les règles web partagent aussi ces composants. La migration des fichiers et offres est détaillée dans [13-file-reseller-migration.md](13-file-reseller-migration.md). [La galerie](components.html) permet de tester pagination, tri, filtres, exports, vues enregistrées, formulaires et dialogues.

## Baseline active

- Baseline visuelle : **V2.5.3.12**
- Règles de verrouillage : [STYLE_LOCK.md](STYLE_LOCK.md)
- Audit consolidé : [AUDIT-CONSOLIDATION.md](AUDIT-CONSOLIDATION.md)
- Registre des écarts : [02-gap-register.md](02-gap-register.md)
- Gates de gel : [06-design-freeze-gates.md](06-design-freeze-gates.md)

Les pages `canonical-*.html` représentent les surfaces de référence. `index.html` est le point d’entrée du prototype et de son catalogue. Les anciens dashboards, vues DNS/sécurité, sous-centres IAM/runtime/mail et formulaires de création conservés comme routes historiques renvoient vers leur centre canonique.

## Structure

- `assets/styles.css` : styles historiques et composants initiaux ;
- `assets/reference-ui.css` : shell et composants de la baseline courante ;
- `assets/app.js` : compatibilité des interactions historiques ;
- `assets/foundation.js` et `.css` : bibliothèque UI commune (grille, formulaires, validation, dialogues, notifications, thèmes et préférences par contexte) ;
- `components.html` + `assets/foundation-gallery.js` : démonstration interactive du socle commun ;
- `assets/hosting-schemas.js` : règles métier communes aux formulaires mail, comptes, placement et cycle de vie ;
- [12-mail-hosting-migration.md](12-mail-hosting-migration.md) : périmètre, contrats et vérification de la migration mail/comptes/récupération ;
- `assets/reference-ui.js` : navigation, registre des routes et interactions canoniques ;
- `assets/prototype-home.js` et `.css` : accueil par portail, catalogue, catégories et recherche ;
- `assets/product-runtime.js` : état commun des tâches et notifications, progression, attente, annulation et reprise ;
- `jobs.html`, `job-detail.html` et `notifications-center.html` : vues du runtime partagé ;
- `serve-prototype.ps1` : serveur HTTP local PowerShell limité à ce dossier ;
- `users-roles.html` + `assets/identity-access.js` : centre ROOT canonique Identity & Access ; les anciennes routes Roles, Plans, Feature lists, Effective permissions, Demo accounts, Resellers et Quotas sont des points de compatibilité ;
- `system-settings.html#server` : propriétaire canonique de la configuration globale du serveur ; `server-configuration.html` est une route de compatibilité ;
- `hosting-accounts.html#accounts` : inventaire, capacité, suspension, résiliation et récupération des comptes avec archive finale, rétention, restauration et purge confirmée ; `account-operations.html` est une route de compatibilité ;
- `web-runtime.html` + `assets/runtime-center.js` : centre ROOT canonique des moteurs web, langages, modules, profils, affectations et profils PHP Security ; les anciennes routes Language Runtimes, Runtime Modules et Web Stack Builder sont des points de compatibilité ;
- `credential-security.html` : changement du mot de passe WCP-ADMIN, rotation du secret Linux root en Vault, reset/renommage des comptes et révocation de sessions ;
- `dns-toolbox.html` : diagnostics WHOIS, DNS, délégation, propagation, ping et traceroute en lecture seule ;
- `provider-adapters.html` : activation, configuration, santé et exclusions des connecteurs Cloudflare, Dynadot, Spaceship, ResellerClub et Brevo ;
- `continuity-control.html` : console ROOT HA/DR pour IP flottantes, VRRP, fencing, réplication inter-datacenter, objectifs RPO/RTO et exercices de reprise ;
- `privacy-governance.html` : demandes RGPD, découverte des données, effacement/anonymisation, legal holds, résidence et preuves signées ;
- `delivery-platform.html` : gouvernance ROOT des fournisseurs Git, webhooks signés, pools de runners, clones de staging et politiques de déploiement ;
- `virtualization.html` : extension optionnelle Proxmox/libvirt, images, plans VPS et inventaire des machines virtuelles ;
- `admin-mail.html` + `assets/mail-infrastructure.js` : centre ROOT canonique de SMTP, IMAP, sécurité, réputation, Webmail/SSO et file d'attente ; les anciennes routes Mail Server Config, Webmail Services et Mail Queue sont des points de compatibilité ;
- `certificates.html` + `assets/certificate-center.js` : centre ROOT canonique d'AutoSSL, certificats de comptes et services, autorités, validation, politiques, renouvellements et révocation ;
- `application-catalog.html` + `assets/application-catalog.js` : centre ROOT canonique des applications, sources signées, blueprints d'installation, politiques, rollouts, inventaire et licences ; son contrat partagé alimente `app-installer.html` côté USER ;
- `file-transfer-services.html` + `assets/file-transfer-center.js` : centre ROOT canonique des endpoints SFTP/FTPES/WebDAV, profils, politiques, sessions, réseau et sécurité ; son contrat partagé limite réellement `file-access-center.html` côté USER ;
- `admin-dns.html` + `assets/dns-center.js` : centre ROOT canonique des zones autoritatives, templates, cluster, nameservers, DNSSEC, providers et politiques ; les routes DNS Cluster et DNS Zone Templates deviennent des points de compatibilité, et `dns-record-add.html` consomme les types d'enregistrements effectivement publiés ;
- `web-routing.html`, `web-compatibility.html` + `assets/web-rules-center.js` et `assets/web-rules-schemas.js` : politique ROOT publiée, redirections exactes, MIME, contenu/HTTPS et versions USER ; le statut SSL est partagé avec Sites via `WCPDNSTLSSchemas.bindingStatus` ; voir [19-web-rules-policy-migration.md](19-web-rules-policy-migration.md) ;
- `system-settings.html#server` + `notification-routing.html` : contacts ROOT vérifiés, destinataires par défaut des messages système, sécurité, sauvegarde, abus et postmaster ; l'e-mail ROOT et les notifications intégrées restent un fallback du noyau ;
- `software-manager.html` + `assets/platform-software.js` : séparation canonique entre les 12 domaines du noyau obligatoire et les extensions optionnelles WCP360/externes ; registre de propriété de 27 capacités avec un seul propriétaire d'écriture, responsabilités ROOT/RESELLER/USER, frontière System Settings, frontière d'extension et fallback ; contrats de manifeste, permissions, isolation, données, santé, circuit breaker et cycle de vie réversible ;
- `../WCP360_Internal_controle/` : application vendeur privée physiquement séparée, connexion workforce spéciale, build/release, licensing, publication, commerce, Fleet, rapports consentis et IAM/JIT ; elle n'est jamais incluse dans un ZIP public ;
- `../WCP360_Extensions/wcp360-insights-connector/` : plugin optionnel distribué séparément pour audits locaux, failles, rapports et bugs consentis, sans shell ni accès au contenu hébergé ;
- `*.html` : 232 pages publiques dans ce dossier ; INTERNAL possède 2 pages privées et le connecteur possède 1 console séparée ;
- `site-analytics.html`, `logs-diagnostics.html` + `assets/observability-center.js` et `assets/observability-schemas.js` : journaux, échantillons de trafic, diagnostics et rapports simulés par portail ; `server-logs.html` est une redirection ROOT ; voir [18-scoped-observability-migration.md](18-scoped-observability-migration.md) ;
- `app-lifecycle.html`, `app-health.html`, `assets/application-center.js` et `assets/application-schemas.js` : runtime, variables, maintenance et santé des applications ; `assets/website-model.js` mutualise leur inventaire avec Sites ; voir [17-application-workspace-migration.md](17-application-workspace-migration.md) ;
- `git-repositories.html`, `deployments.html` + `assets/delivery-center.js` et `assets/delivery-schemas.js` : sources, builds, versions, promotion et retour arrière simulés ; voir [16-account-delivery-migration.md](16-account-delivery-migration.md) ;
- `assets/dns-tls-schemas.js`, `assets/account-dns.js` et `assets/account-certificates.js` : brouillons DNS et cycle SSL du compte ; voir [15-account-dns-tls-migration.md](15-account-dns-tls-migration.md) ;
- `assets/operations-core.js`, `assets/protection-schemas.js`, `assets/backup-center.js` et `assets/schedule-center.js` : sauvegardes et planification simulées ; voir [14-protection-scheduling-migration.md](14-protection-scheduling-migration.md) ;
- `assets/workspace-schemas.js` : règles métier des arbres de fichiers et des offres revendeur, partagées avec les tests ;
- `assets/hosting-parity.css` et `assets/hosting-parity.js` : centres de parité cPanel/WHM, workflows et garde-fous partagés ;
- `01-*.md` à `06-*.md` : couverture, écarts, sécurité, états et critères de gel.
- `07-scope-route-ownership.md` : propriété ROOT/RESELLER/USER, centres canoniques, règles de sidebar et workflows entre portails.
- `08-core-extension-architecture.md` : architecture du noyau, familles d'extensions, manifeste minimal, frontières de panne et ordre de développement.
- `09-internal-public-distribution-architecture.md` : séparation physique public/privé, clients publics de services privés, allowlist positive et pipeline signé de publication Stable.

## État de l’intégration fonctionnelle

Le [cycle de vie Sites/Comptes/Archives](22-resource-lifecycle-migration.md) applique désormais les créations, changements, résiliations et restaurations après le résultat de leur job précis. Le modèle Comptes possède aussi les archives et expose une activité partagée.

La [consolidation des règles mail](21-mail-rules-consolidation.md) ajoute filtres, réponses automatiques, catch-all, rétention, prévisualisation et publication simulée dans un seul centre Email. Six anciennes pages y renvoient ; huit définitions génériques obsolètes ont été retirées.

Le [bilan initial fichier par fichier du 30 octobre 2026](20-integration-coverage-audit.md) distingue les parcours testés, les centres partiels, les pages encore statiques et les routes de compatibilité. Les anciennes mentions « CLOSED » du registre des écarts décrivent la couverture de conception ; elles ne certifient pas que chaque action modifie un état partagé.

## Validation

```powershell
node tools/audit_wcp360_resource_lifecycle.mjs
node tools/audit_wcp360_mail_rules.mjs
node tools/audit_wcp360_web_rules.mjs
node tools/audit_wcp360_observability.mjs
node tools/audit_wcp360_applications.mjs
node tools/audit_wcp360_delivery.mjs
node tools/audit_wcp360_dns_tls.mjs
node tools/audit_wcp360_protection.mjs
node tools/audit_wcp360_workspace.mjs
node tools/audit_wcp360_hosting.mjs
node tools/audit_wcp360_foundation.mjs
node tools/audit_wcp360_integration.mjs
node tools/audit_wcp360_design_system.mjs "WCP360_Design_System"
node tools/runtime_audit_wcp360.mjs "WCP360_Design_System"
node tools/runtime_audit_private_boundary.mjs "."
node tools/runtime_audit_extension_lifecycle.mjs "WCP360_Design_System"
node --check "WCP360_Design_System/assets/app.js"
node --check "WCP360_Design_System/assets/reference-ui.js"
node --check "WCP360_Design_System/assets/hosting-parity.js"
node --check "WCP360_Design_System/assets/runtime-center.js"
node --check "WCP360_Design_System/assets/mail-infrastructure.js"
node --check "WCP360_Design_System/assets/certificate-center.js"
node --check "WCP360_Design_System/assets/application-catalog.js"
node --check "WCP360_Design_System/assets/app-installer.js"
node --check "WCP360_Design_System/assets/file-transfer-center.js"
node --check "WCP360_Design_System/assets/dns-center.js"
node --check "WCP360_Design_System/assets/account-dns.js"
node --check "WCP360_Design_System/assets/account-certificates.js"
node --check "WCP360_Design_System/assets/delivery-schemas.js"
node --check "WCP360_Design_System/assets/delivery-center.js"
node --check "WCP360_Design_System/assets/website-model.js"
node --check "WCP360_Design_System/assets/application-schemas.js"
node --check "WCP360_Design_System/assets/application-center.js"
node --check "WCP360_Design_System/assets/observability-schemas.js"
node --check "WCP360_Design_System/assets/observability-center.js"
node --check "WCP360_Design_System/assets/web-rules-schemas.js"
node --check "WCP360_Design_System/assets/web-rules-center.js"
node --check "WCP360_Design_System/assets/platform-software.js"
node --check "WCP360_Design_System/assets/extension-guard.js"
node --check "WCP360_Design_System/assets/hosting-parity.js"
node --check "WCP360_Internal_controle/assets/internal-auth.js"
node --check "WCP360_Internal_controle/assets/internal-distribution.js"
node --check "WCP360_Extensions/wcp360-insights-connector/assets/connector-core.js"
node --check "WCP360_Extensions/wcp360-insights-connector/assets/connector-views-overview.js"
node --check "WCP360_Extensions/wcp360-insights-connector/assets/connector-views-evidence.js"
node --check "WCP360_Extensions/wcp360-insights-connector/assets/connector-actions.js"
```

Le prototype est une référence de conception, pas une implémentation de production. Les écarts prioritaires et éléments manquants sont recensés dans l’audit consolidé.

## Périmètres d'accès

- **USER** : ressources d'un compte d'hébergement, sans outils d'infrastructure ;
- **RESELLER** : administration déléguée des comptes clients, packages, quotas et services autorisés par ROOT ;
- **ROOT** : gouvernance du control plane, des serveurs, services, réseaux et politiques globales ;
- le sélecteur de profil visible dans le prototype est une fonction de démonstration et ne contourne aucune permission de production.
