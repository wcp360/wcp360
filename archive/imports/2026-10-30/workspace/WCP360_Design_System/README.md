# WCP360 Design System

Prototype de référence du Web Control Panel WCP360.

## Baseline active

- Baseline visuelle : **V2.5.3.12**
- Règles de verrouillage : [STYLE_LOCK.md](STYLE_LOCK.md)
- Audit consolidé : [AUDIT-CONSOLIDATION.md](AUDIT-CONSOLIDATION.md)
- Registre des écarts : [02-gap-register.md](02-gap-register.md)
- Gates de gel : [06-design-freeze-gates.md](06-design-freeze-gates.md)

Les pages `canonical-*.html` représentent les surfaces de référence. `index.html` est le point d’entrée du prototype. Les pages `v2.*` et fichiers `README-V*.md` sont historiques et ne constituent pas une nouvelle baseline.

## Structure

- `assets/styles.css` : styles historiques et composants initiaux ;
- `assets/reference-ui.css` : shell et composants de la baseline courante ;
- `assets/app.js` : compatibilité des interactions historiques ;
- `assets/reference-ui.js` : interactions canoniques ;
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
- `web-routing.html` + `assets/hosting-parity.js` : gouvernance ROOT des sous-domaines, alias, redirections, hôtes canoniques, cibles proxy, labels réservés et limites de document-root ; les pages USER consomment cette politique sans exposer l'infrastructure ;
- `system-settings.html#server` + `notification-routing.html` : contacts ROOT vérifiés, destinataires par défaut des messages système, sécurité, sauvegarde, abus et postmaster ; l'e-mail ROOT et les notifications intégrées restent un fallback du noyau ;
- `software-manager.html` + `assets/platform-software.js` : séparation canonique entre les 12 domaines du noyau obligatoire et les extensions optionnelles WCP360/externes ; registre de propriété de 27 capacités avec un seul propriétaire d'écriture, responsabilités ROOT/RESELLER/USER, frontière System Settings, frontière d'extension et fallback ; contrats de manifeste, permissions, isolation, données, santé, circuit breaker et cycle de vie réversible ;
- `../WCP360 Internal/` : application vendeur privée physiquement séparée, connexion workforce spéciale, build/release, licensing, publication, commerce, Fleet, rapports consentis et IAM/JIT ; elle n'est jamais incluse dans un ZIP public ;
- `../WCP360 Extensions/wcp360-insights-connector/` : plugin optionnel distribué séparément pour audits locaux, failles, rapports et bugs consentis, sans shell ni accès au contenu hébergé ;
- `*.html` : 232 pages publiques dans ce dossier ; INTERNAL possède 2 pages privées et le connecteur possède 1 console séparée ;
- `assets/hosting-parity.css` et `assets/hosting-parity.js` : centres de parité cPanel/WHM, workflows et garde-fous partagés ;
- `01-*.md` à `06-*.md` : couverture, écarts, sécurité, états et critères de gel.
- `07-scope-route-ownership.md` : propriété ROOT/RESELLER/USER, centres canoniques, règles de sidebar et workflows entre portails.
- `08-core-extension-architecture.md` : architecture du noyau, familles d'extensions, manifeste minimal, frontières de panne et ordre de développement.
- `09-internal-public-distribution-architecture.md` : séparation physique public/privé, clients publics de services privés, allowlist positive et pipeline signé de publication Stable.

## Validation

```powershell
node tools/audit_wcp360_design_system.mjs "WCP360 Design System"
node tools/runtime_audit_wcp360.mjs "WCP360 Design System"
node tools/runtime_audit_private_boundary.mjs "."
node tools/runtime_audit_extension_lifecycle.mjs "WCP360 Design System"
node --check "WCP360 Design System/assets/app.js"
node --check "WCP360 Design System/assets/reference-ui.js"
node --check "WCP360 Design System/assets/hosting-parity.js"
node --check "WCP360 Design System/assets/runtime-center.js"
node --check "WCP360 Design System/assets/mail-infrastructure.js"
node --check "WCP360 Design System/assets/certificate-center.js"
node --check "WCP360 Design System/assets/application-catalog.js"
node --check "WCP360 Design System/assets/app-installer.js"
node --check "WCP360 Design System/assets/file-transfer-center.js"
node --check "WCP360 Design System/assets/dns-center.js"
node --check "WCP360 Design System/assets/dns-user-policy.js"
node --check "WCP360 Design System/assets/platform-software.js"
node --check "WCP360 Design System/assets/extension-guard.js"
node --check "WCP360 Design System/assets/hosting-parity.js"
node --check "WCP360 Internal/assets/internal-auth.js"
node --check "WCP360 Internal/assets/internal-distribution.js"
node --check "WCP360 Extensions/wcp360-insights-connector/assets/connector-core.js"
node --check "WCP360 Extensions/wcp360-insights-connector/assets/connector-views-overview.js"
node --check "WCP360 Extensions/wcp360-insights-connector/assets/connector-views-evidence.js"
node --check "WCP360 Extensions/wcp360-insights-connector/assets/connector-actions.js"
```

Le prototype est une référence de conception, pas une implémentation de production. Les écarts prioritaires et éléments manquants sont recensés dans l’audit consolidé.

## Périmètres d'accès

- **USER** : ressources d'un compte d'hébergement, sans outils d'infrastructure ;
- **RESELLER** : administration déléguée des comptes clients, packages, quotas et services autorisés par ROOT ;
- **ROOT** : gouvernance du control plane, des serveurs, services, réseaux et politiques globales ;
- le sélecteur de profil visible dans le prototype est une fonction de démonstration et ne contourne aucune permission de production.
