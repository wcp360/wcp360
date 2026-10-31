# Audit et consolidation du Design System WCP360

Date de l’audit : 29 octobre 2026  
Baseline visuelle : V2.5.3.12, verrouillée par `STYLE_LOCK.md`  
Périmètre : 226 prototypes HTML, 29 feuilles de styles, 31 scripts et 12 documents Markdown dans le design system.

## Résultat

Le prototype couvre largement les familles fonctionnelles du panneau de contrôle et ses références locales sont cohérentes. L’audit automatisé ne relève aucun lien local cassé, identifiant dupliqué, image sans alternative, document sans langue, viewport, titre ou doctype.

## Fermeture de parité hébergement — 29 octobre 2026

La passe de consolidation concurrentielle ajoute sans dupliquer les centres existants :

- USER : FTP/SFTP/WebDAV, confidentialité des répertoires, consommation disque, Git, statistiques, journaux bruts, compatibilité web, règles mail, routage, catch-all, listes, délégation et rétention ;
- RESELLER : espace produit distinct, ACL par opération, packages, limites, overselling explicite, IP/nameservers, marque blanche et audit délégué ;
- ROOT : opérations de comptes, profils de serveurs, construction de pile web, cluster DNS, configuration mail, import cPanel/Plesk/DirectAdmin/Hestia/Linux, plateforme d'extensions et registre de readiness ;
- ROOT runtimes et exploitation : PHP, Node.js, Python et Ruby multi-version, modules natifs/licenciés, restauration de masse, compteurs de ressources et diagnostics processus/disque/inodes ;
- ROOT hébergement avancé : blueprints de provisionnement versionnés, moteur de tâches planifiées et gouvernance des services SFTP/FTPES/WebDAV ;
- ROOT configuration déclarative : modèles de zones DNS, hooks de cycle de vie et schémas de services avec provenance, drift, canary et rollback ;
- ROOT gouvernance : Approval Center avec séparation des rôles, routage/escalade des notifications et Audit Explorer avec intégrité WORM ;
- navigation : sidebars USER, RESELLER et ROOT indépendantes, avec commutateur uniquement dans le profil de démonstration ;
- interactions : vues, recherches, filtres, drawers, prévisualisation, validation, création de jobs et audit simulés de manière cohérente.

## Consolidation des centres ROOT — 29 octobre 2026

- `users-roles.html` est désormais le centre canonique **Identity & Access** avec huit vues internes : Users, Roles, Plans, Feature lists, Effective permissions, Demo accounts, Resellers et Quotas ;
- les sept anciennes routes spécialisées restent compatibles et redirigent vers l'ancre canonique correspondante, sans dupliquer le renderer ni les workflows ;
- la sidebar ROOT n'affiche plus ces sous-vues comme huit produits indépendants ;
- `server-configuration.html` est conservée comme route de compatibilité vers `system-settings.html#server`, qui devient l'unique propriétaire des réglages globaux du serveur ;
- `account-operations.html` devient une route de compatibilité vers `hosting-accounts.html#accounts` : modification, placement, suspension, réactivation et résiliation vivent désormais avec l'inventaire des comptes ;
- l'onglet Plans de Hosting Accounts renvoie au catalogue canonique `users-roles.html#plans` afin d'éviter deux éditeurs de plans concurrents ;
- les textes du centre IAM ont été relevés à une taille exploitable, les tableaux sont scrollables et les cartes, filtres, évaluateur de permissions et quotas disposent de comportements responsive dédiés ;
- l'audit navigateur vérifie les huit onglets, leurs états ARIA, l'ouverture du drawer, la création d'une invitation, les anciennes routes et l'absence de texte inférieur au seuil.
- `web-runtime.html` devient l'unique **Runtime & Web Stack Center** ROOT avec neuf vues : topologie, moteurs web, PHP/FPM, Node.js/Python/Ruby, modules, profils, affectations, cycle de vie et historique/récupération ;
- `language-runtimes.html`, `runtime-modules.html` et `web-stack-builder.html` restent compatibles et redirigent vers les vues canoniques, tandis que la sidebar ne conserve qu'une entrée Runtime & Web Stack ;
- les installations, modules, profils et affectations utilisent des drawers persistants et créent des opérations dans le Job Center avec validation, canary, health gates et rollback explicités ;
- les anciens modèles `runtime`, `web-stack`, `language-runtimes` et `runtime-modules` ont été retirés des renderers génériques pour supprimer la seconde source de données devenue inaccessible ;
- l'audit navigateur vérifie les neuf vues, les dix modules, les profils, affectations, créations persistantes, jobs, tailles de texte et débordements responsive du centre runtime.
- `admin-mail.html` devient l'unique **Mail Infrastructure Center** ROOT avec neuf vues : topologie, nœuds/profils, SMTP, IMAP/stockage, sécurité, réputation, Webmail/SSO, file d'attente et historique forensique ;
- `mail-server-config.html`, `webmail-services.html` et `mail-queue.html` redirigent vers leurs onglets canoniques et la sidebar ne conserve qu'une entrée Mail Infrastructure ;
- l'activation de Roundcube, SnappyMail ou SOGo reste stockée dans le contrat partagé consommé par WCP-USER, tandis que la suppression d'un message en file exige la saisie exacte de son identifiant ;
- les anciens modèles Mail Server et Mail Queue ainsi que l'ancien renderer Webmail ROOT ont été retirés pour conserver une seule source de vérité.
- `certificates.html` devient le **SSL & AutoSSL Center** ROOT avec neuf vues couvrant AutoSSL, inventaire, TLS des services, autorités publiques/privées, comptes ACME, DCV/DNS, politiques, renouvellements et historique ;
- la révocation est protégée par la saisie exacte du nom du certificat, les certificats externes ne sont jamais remplacés silencieusement et les clés privées restent hors du navigateur ;
- l'ancien modèle générique Certificates a été retiré de `root-control.js` et l'audit vérifie émission persistante, révocation, jobs, couverture, services, autorités, adaptateurs DCV et renouvellements.
- `application-catalog.html` devient l'unique **Application Catalog & Lifecycle Center** ROOT avec neuf vues : overview, catalogue, sources, templates, mises à jour, politiques, installations, licences et historique ;
- signatures, checksums, provenance, SBOM/CVE, validation éphémère, canary, sauvegarde, health gates et rollback structurent la chaîne de publication ;
- le contrat persistant `wcp360-application-catalog-v1` alimente réellement `app-installer.html` : ROOT publie ou retire une application, RESELLER ne peut que réduire l'offre et USER reçoit uniquement le catalogue effectif de son plan ;
- l'ancien modèle `appcatalog` a été retiré de `root-control.js`, tandis que l'audit navigateur vérifie création, persistance, jobs, neuf vues et propagation ROOT → USER.
- `file-transfer-services.html` devient l'unique **File Transfer Services Center** ROOT avec neuf vues : overview, endpoints, protocoles, profils, politiques, sessions, réseau, sécurité et historique ;
- les plans client et management SFTP sont séparés, FTPES explicite remplace toute ambiguïté FTP, Plain FTP est un deny verrouillé, et les plages passives, NAT, certificats, clés hôte, chroot, symlinks, quota, inodes, malware et écritures atomiques sont couverts ;
- le contrat `wcp360-file-transfer-center-v1` pilote les protocoles réellement proposés par `file-access-center.html`, où le formulaire USER ne peut sélectionner que SFTP, FTPES ou WebDAV effectivement publiés ;
- l'ancien module ROOT `file-transfer-services` a été retiré de `hosting-parity.js`; la déconnexion d'une session exige la saisie exacte de son identifiant et l'audit vérifie création d'endpoint, persistance, jobs et propagation WebDAV ROOT → USER.
- `admin-dns.html` devient l'unique **DNS Infrastructure Center** ROOT avec neuf vues : overview, zones, templates, cluster, nameservers, DNSSEC, providers, policies et history ;
- `dns-zone-templates.html` et `dns-cluster.html` redirigent vers leurs vues canoniques, tandis que `dns-zone.html` devient la compatibilité USER vers `canonical-dns.html` et que la sidebar ROOT ne conserve qu'une entrée DNS Infrastructure ;
- les anciens modèles DNS des renderers génériques ont été retirés : zones et suppressions gardées, templates immuables, nœuds, providers, clés/rollovers, glue/délégation, dix politiques et neuf types de records disposent d'une source persistante unique ;
- le contrat `wcp360-dns-center-v1` alimente `dns-record-add.html` et `canonical-dns.html` : ROOT publie le plafond, RESELLER peut seulement le réduire et USER ne crée que des types effectifs dans sa zone.
- `software-manager.html` sépare désormais explicitement un noyau obligatoire de 12 domaines et seize extensions/adaptateurs optionnels, dont le WCP360 Insights Connector distribué comme paquet détaché ;
- les extensions déclarent publisher, compatibilité, permissions, dépendances, identité d'exécution, namespace de données, comportement de panne, surfaces UI et état de couverture ; six contrats imposent signature, capability tokens, exécution hors processus, données possédées, circuit breaker et cycle de vie réversible ;
- le catalogue ROOT distingue maintenant les états disponible, installé, actif, arrêté, mis en quarantaine et désinstallé ; les workflows couvrent préflight signé, permission diff, canary, santé, retrait des routes, blocage réseau, rollback de slot, conservation/export/purge des données et confirmation typée des opérations de sécurité ;
- un registre interactif consolide 26 capacités selon la règle « une capacité, un propriétaire d'écriture » et sépare explicitement les valeurs par défaut de System Settings, le centre opérationnel canonique, la délégation RESELLER, l'espace USER et l'adaptateur optionnel ;
- App Installer constitue le premier contrat exécutable : sa désactivation retire les routes ROOT/USER de la navigation, présente un état dégradé déterministe sur accès direct et laisse toutes les fonctions du noyau disponibles ;
- `08-core-extension-architecture.md` devient la décision d'architecture de référence et classe HA/DR et CI/CD en couverture partielle, Green/Forensics en couverture prototype, et Privacy/GDPR en manque prioritaire.

## Séparation INTERNAL / STABLE PUBLIC — 29 octobre 2026

- `../WCP360 Internal/index.html` fournit une connexion fournisseur dédiée, puis `internal-distribution.html` consolide onze vues privées dans une application physiquement séparée ;
- INTERNAL est un produit vendeur distinct, pas un rôle ROOT, pas un plugin et pas un accès Linux universel ; il est absent de toutes les sidebars USER, RESELLER et ROOT ;
- la publication utilise une allowlist positive de composants versionnés, puis les gates Select → Test → Build → Audit → Sign → Publish ; les services privés ne peuvent pas être ajoutés à la distribution ;
- Build Factory modélise les commits exacts, runners signés, dépendances verrouillées, builds reproductibles, SBOM, provenance et preuves d'audit ;
- Build Factory sépare désormais deux voies isolées : les éditions du cœur utilisent exclusivement l'allowlist publique, tandis que le WCP360 Insights Connector produit un paquet, un manifeste, un SBOM, une provenance et un index d'update indépendants ;
- Releases sépare Alpha privé, Beta et RC opt-in, puis Stable à double approbation ; Distribution expose ZIP, SHA-256, signature, manifeste atomique, rollback et révocation avec saisie obligatoire de `REVOKE` ;
- la révocation est limitée à l'identité d'un artefact : une extension compromise peut être bloquée sans révoquer les ZIP du cœur, et le filtre d'export exclut systématiquement les extensions détachées et tous les services INTERNAL ;
- Licensing sépare l'autorité privée du client vérificateur public et impose un mode de grâce non destructif ; Marketplace sépare le publisher privé du client catalogue public ; Extensions gouverne les paquets qui étendent WCP360, tandis que Customers & Commerce possède seulement organisations, abonnements, contacts et renouvellements ;
- les responsabilités restent non dupliquées : Licensing possède clés et entitlements, Fleet possède les installations consenties, Customers possède le contrat commercial, et le WCP-ADMIN client reste seul propriétaire des données d'hébergement ;
- Security consolide désormais les opérateurs workforce, FIDO2/mTLS, rôles effectifs, sessions temporaires et demandes JIT ; la saisie de l'identifiant d'approbation active un jeton à durée limitée et la séparation des tâches bloque l'auto-approbation ;
- Fleet limite l'inventaire aux installations consenties et reçoit les audits, failles normalisées et bugs uniquement via le connecteur optionnel ; domaines, fichiers, emails, bases, secrets et accès Linux restent interdits ;
- `../WCP360 Extensions/wcp360-insights-connector/` constitue un artifact signé indépendant : désactivé par défaut, scopes de collecte explicites, mTLS sortant uniquement, audit local, rapports exportables, confirmation DETACH et aucune capacité de commande distante ;
- `09-internal-public-distribution-architecture.md` impose en production deux dépôts physiques, deux identités CI et l'absence de secrets dans Git ;
- la validation sépare désormais l'audit des pages publiques, l'authentification INTERNAL et le contrat fonctionnel du connecteur ; la frontière classe 19 composants, dont le connecteur détaché qui ne peut pas entrer dans l'allowlist du cœur.

## Séparation des portails et navigation légère — 29 octobre 2026

- la sidebar est désormais un accordéon à groupe unique sur les shells canoniques et historiques ; seul le domaine actif reste développé ;
- USER, RESELLER et ROOT disposent tous d'une recherche qui indexe aussi les fonctions rangées dans les groupes fermés ;
- le Command Center et la sidebar sont contrôlés par le même audit anti-mélange de périmètre ;
- le doublon Approval Center a été retiré de la section System, car Approvals appartient au domaine Command ;
- le nouveau `reseller.html` regroupe dix vues : Overview, Customers, Packages, Capacity, Services, Governance, Brand, Transfers, Notifications et Audit & Support ;
- les anciens liens RESELLER vers Hosting Accounts, Plans, Quotas, Certificates, Transfers et Audit Explorer ont été supprimés : ces routes sont ROOT et ne doivent pas servir de pages revendeur ;
- `reseller-governance.html` reste une route de compatibilité vers `reseller.html#governance` ;
- la chaîne ROOT → RESELLER → USER, la propriété des routes et les workflows transverses sont définis dans `07-scope-route-ownership.md` ;
- l'audit navigateur impose 4 groupes USER, 3 groupes RESELLER et 8 groupes ROOT, exactement un groupe ouvert, une recherche disponible et zéro route croisée.

Cette fermeture concerne la **couverture fonctionnelle du prototype**. Elle ne constitue pas une preuve d'implémentation du control plane, de l'agent privilégié, des adaptateurs système ou des garanties de production.

Corrections communes appliquées :

- suppression du conflit entre les trois gestionnaires de `Ctrl/Cmd + K` ;
- désignation du Command Center V2.5 comme implémentation canonique ;
- restauration du focus après fermeture d’un dialogue ou drawer ;
- piégeage du focus clavier dans le Command Center ;
- synchronisation de `aria-hidden` pour les surfaces superposées ;
- ajout d’un nom accessible aux drawers et de `role="status"` aux toasts ;
- activation réelle de la copie des secrets avec retour d’échec ;
- activation des résultats du Command Center au clic et au clavier ;
- correction des titres `h1` et noms accessibles manquants ;
- amélioration des champs de connexion et MFA (`for`/`id`, autocomplete, clavier numérique et format attendu).

## Ce qui manque encore

### P0 — avant passage en production

1. **Tests réels d’accessibilité.** La cible WCAG 2.2 AA est documentée, mais aucune preuve axe/Playwright ni campagne lecteur d’écran n’est présente.
2. **Tests responsive réels.** Il manque une matrice vérifiée mobile/tablette/desktop, navigateurs, clavier et tactile.
3. **Composants exécutables.** Une partie des composants reste du HTML dupliqué dans 226 pages ; Identity & Access et les nouvelles familles utilisent désormais des renderers partagés, mais aucune application source ne garantit encore l'ensemble.
4. **Tests des workflows sensibles.** Confirmations, permissions, approbations, jobs et actions destructives restent des démonstrations statiques sans machine d’état testée.

### P1 — consolidation

1. **Tokens exportables.** Les variables CSS doivent devenir une source de vérité JSON versionnée avec génération CSS.
2. **Architecture CSS.** Les deux grandes feuilles cumulent styles historiques et canoniques. La dette G-018 reste ouverte.
3. **Internationalisation.** Le prototype mélange français et anglais. Catalogue de chaînes, formats et validation RTL manquent ; G-013 reste ouverte.
4. **Navigation et recherche.** Le Command Center filtre une démo ; index réel, permissions et états vide/erreur manquent. G-016 reste ouverte.
5. **Contrats de composants.** API, variantes, états, clavier, contenu autorisé et contre-exemples restent à formaliser.

### P2 — gouvernance

1. Regrouper les notes `README-V*.md` dans un changelog unique et archiver les pages historiques.
2. Ajouter versionnage sémantique, dépréciation, propriétaires, contribution et critères de release.
3. Ajouter une CI : audit HTML, tests JS, captures visuelles, contrastes, liens et budgets CSS.
4. Finaliser les vues sauvegardées (G-015) et machines d’état transverses (G-017).

## Validation reproductible

```powershell
node tools/audit_wcp360_design_system.mjs "WCP360 Design System"
node tools/runtime_audit_wcp360.mjs "WCP360 Design System"
node --check "WCP360 Design System/assets/app.js"
node --check "WCP360 Design System/assets/reference-ui.js"
```

Le premier validateur contrôle les sources. Le second charge les 226 pages dans Chrome en desktop et mobile et vérifie les exceptions, débordements, contrôles accessibles, liens factices, navigation active et composants dupliqués. Les deux renvoient un code non nul en présence d’une erreur.
