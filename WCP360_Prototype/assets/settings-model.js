/* System Settings contract. Pure data and validation; no DOM, storage or network access. */
(() => {
  'use strict';

  const blockedKeys = new Set(['__proto__', 'prototype', 'constructor']);
  const field = (path, label, type = 'text', extra = {}) => ({ path, label, type, ...extra });
  const number = (path, label, min, max, unit, extra = {}) => field(path, label, 'number', { min, max, step: 1, ...(unit ? { unit } : {}), ...extra });
  const check = (path, label, help) => field(path, label, 'checkbox', help ? { help } : {});
  const select = (path, label, options, help) => field(path, label, 'select', { options: options.map(option => typeof option === 'string' ? { value: option, label: option } : option), ...(help ? { help } : {}) });
  const link = (href, label, description) => ({ href, label, description });
  const sections = [
    { id: 'server', title: 'Identité du serveur', icon: 'dns', description: 'Identifier le plan de contrôle et les contacts responsables de son exploitation.', groups: [
      { id: 'identity', title: 'Identité et région', fields: [
        field('identity.hostname', 'Nom de domaine du panneau', 'text', { required: true, help: 'FQDN de gestion, par exemple panel.example.com. Le changement peut affecter les certificats et les agents.' }),
        field('identity.serverName', 'Nom du serveur', 'text', { required: true }),
        select('environment', 'Environnement', [{ value: 'EU-PROD', label: 'Production — Europe' }, { value: 'Production', label: 'Production' }, { value: 'Staging', label: 'Préproduction' }, { value: 'Lab', label: 'Laboratoire' }]),
        field('identity.timezone', 'Fuseau horaire', 'text', { required: true, help: 'Identifiant IANA, par exemple Europe/Paris ou UTC.' }),
        select('identity.locale', 'Langue par défaut', [{ value: 'French (France)', label: 'Français (France)' }, { value: 'English (United States)', label: 'English (United States)' }])
      ] },
      { id: 'contacts', title: 'Contacts et synchronisation', fields: [
        field('identity.rootEmail', 'Contact administratif ROOT', 'email', { required: true }),
        field('identity.securityEmail', 'Contact sécurité', 'email', { required: true }),
        field('identity.supportEmail', 'Contact assistance', 'email', { required: true }),
        field('identity.backupEmail', 'Contact sauvegardes', 'email', { required: true }),
        field('identity.abuseEmail', 'Contact abus', 'email', { required: true }),
        field('identity.postmasterEmail', 'Contact postmaster', 'email', { required: true }),
        check('identity.ntpEnabled', 'Activer la synchronisation horaire', 'La fiabilité des journaux et de l’authentification dépend d’une horloge exacte.'),
        field('identity.ntpServers', 'Serveurs de temps', 'text', { help: 'Noms de domaine ou adresses IP séparés par des virgules.' })
      ] }
    ], links: [link('server-overview.html', 'Vue du serveur', 'Consulter l’état et les capacités du serveur.'), link('setup-wizard.html', 'Assistant d’installation', 'Revoir le profil initial et les dépendances de déploiement.')] },
    { id: 'network', title: 'Réseau de gestion', icon: 'lan', description: 'Définir l’adressage et la résolution utilisés par le plan de contrôle.', groups: [
      { id: 'addressing', title: 'Interface et adressage', fields: [
        field('network.interface', 'Interface principale', 'text', { required: true }),
        field('network.primaryIpv4', 'Adresse IPv4 principale', 'text', { required: true }),
        field('network.primaryIpv6', 'Adresse IPv6 principale', 'text', { help: 'Obligatoire si IPv6 est activé.' }),
        field('network.gateway', 'Passerelle IPv4', 'text', { required: true }),
        check('network.ipv6', 'Activer IPv6'),
        number('network.mtu', 'MTU', 1280, 9000, 'octets')
      ] },
      { id: 'resolvers', title: 'Résolution et périmètre autorisé', fields: [
        field('network.resolverPrimary', 'Résolveur principal', 'text', { required: true, help: 'Adresse IPv4 ou IPv6.' }),
        field('network.resolverSecondary', 'Résolveur secondaire', 'text', { required: true }),
        check('network.strictManagementAcl', 'Restreindre les réseaux de gestion', 'La configuration est simulée dans ce prototype ; aucune règle réseau réelle n’est appliquée.'),
        field('network.managementCidr', 'Réseaux de gestion autorisés', 'textarea', { help: 'Un ou plusieurs CIDR IPv4/IPv6, séparés par des virgules ou des retours à la ligne.' })
      ] }
    ], links: [link('network-interfaces.html', 'Interfaces réseau', 'Gérer les interfaces et leurs adresses.'), link('network-routes.html', 'Routage', 'Consulter les routes et les passerelles.'), link('dns-cluster.html', 'Cluster DNS', 'Gérer les serveurs et les fournisseurs DNS.')] },
    { id: 'access', title: 'Accès et sessions', icon: 'admin_panel_settings', description: 'Fixer les règles communes aux accès privilégiés et aux sessions du panneau.', groups: [
      { id: 'ssh', title: 'Accès privilégiés', fields: [
        number('access.sshPort', 'Port SSH', 1, 65535),
        check('access.sshKeys', 'Exiger une clé SSH', 'Les identités nominatives et les clés évitent de partager un mot de passe administrateur.'),
        check('access.rootSsh', 'Autoriser une connexion SSH directe ROOT', 'La politique du prototype exige que cette option reste désactivée.'),
        check('access.rootPassword', 'Autoriser le mot de passe SSH ROOT', 'La politique du prototype exige que cette option reste désactivée.'),
        check('access.mfaRoot', 'Exiger le MFA pour ROOT', 'Obligatoire ; les procédures de secours sont gérées dans le centre des accès.')
      ] },
      { id: 'sessions', title: 'Sessions et fédération', fields: [
        number('access.sessionMinutes', 'Expiration après inactivité', 5, 480, 'min'),
        number('access.concurrentSessions', 'Sessions simultanées par identité', 1, 20),
        number('access.apiTokenDays', 'Durée maximale des jetons API', 1, 365, 'jours'),
        select('access.cookieValidation', 'Politique SameSite des cookies', ['Strict', 'Lax'], 'Les cookies de session restent soumis au transport sécurisé.'),
        check('access.ssoEnabled', 'Activer l’authentification SSO'),
        check('access.localFallback', 'Conserver un accès local de secours', 'Obligatoire lorsque le SSO est désactivé. Tester le secours dans le centre des accès.'),
        field('access.trustedProxy', 'Proxys de confiance', 'textarea', { help: 'CIDR uniquement. Laisser vide si aucun proxy de confiance n’est utilisé.' })
      ] }
    ], links: [link('credential-security.html', 'Identifiants et accès', 'Gérer les exigences et références des accès privilégiés.'), link('privileged-session.html', 'Sessions privilégiées', 'Examiner les accès opérateur et leur traçabilité.'), link('break-glass.html', 'Accès de secours', 'Contrôler les procédures et les identités de secours.')] },
    { id: 'resources', title: 'Ressources par défaut', icon: 'memory', description: 'Définir le socle des nouveaux comptes. Les offres et dérogations restent gérées dans leurs espaces.', groups: [
      { id: 'compute', title: 'Calcul et entrées/sorties', fields: [
        number('resource.cpu', 'vCPU', 1, 64),
        number('resource.ram', 'Mémoire vive', 1, 512, 'Gio'),
        number('resource.swap', 'Swap', 0, 512, 'Gio'),
        number('resource.io', 'Débit disque', 1, 10000, 'Mio/s'),
        number('resource.iops', 'Opérations disque', 100, 1000000, 'IOPS')
      ] },
      { id: 'quotas', title: 'Quotas et limites', fields: [
        number('resource.diskSoft', 'Seuil disque d’avertissement', 1, 100000, 'Gio'),
        number('resource.diskHard', 'Quota disque maximal', 1, 100000, 'Gio'),
        number('resource.inodes', 'Inodes', 1000, 100000000),
        number('resource.bandwidth', 'Trafic mensuel', 1, 1000000, 'Gio'),
        number('resource.pids', 'Processus simultanés', 32, 65536)
      ] }
    ], links: [link('plans.html', 'Offres d’hébergement', 'Gérer les limites commerciales et les profils de ressources.'), link('resource-diagnostics.html', 'Diagnostic des ressources', 'Examiner la capacité et les ressources de la plateforme.'), link('language-runtimes.html', 'Langages et environnements', 'Gérer les versions et les limites des moteurs applicatifs.')] },
    { id: 'security', title: 'Socle de sécurité', icon: 'shield', description: 'Définir les exigences d’authentification et de transport communes à la plateforme.', groups: [
      { id: 'passwords', title: 'Mots de passe', fields: [
        number('security.passwordMin', 'Longueur minimale', 12, 128, 'caractères'),
        number('security.passwordExpiry', 'Expiration du mot de passe', 0, 365, 'jours', { help: '0 : pas d’expiration périodique. Le renouvellement après compromission reste requis.' }),
        number('security.passwordHistory', 'Anciens mots de passe interdits', 1, 24),
        check('security.passwordBreached', 'Refuser les mots de passe compromis')
      ] },
      { id: 'protection', title: 'Transport et tentatives de connexion', fields: [
        select('security.tlsMinimum', 'Version TLS minimale', ['TLS 1.2', 'TLS 1.3']),
        check('security.hsts', 'Activer HSTS', 'Prévoir un certificat valide avant toute application sur une infrastructure réelle.'),
        number('security.adminAttempts', 'Échecs de connexion avant blocage', 2, 100),
        number('security.loginWindow', 'Fenêtre de détection', 1, 120, 'min'),
        number('security.banMinutes', 'Durée initiale du blocage', 1, 10080, 'min'),
        check('security.progressiveBan', 'Augmenter les blocages répétés')
      ] }
    ], links: [link('firewall.html', 'Pare-feu de la plateforme', 'Gérer les règles et les protections réseau.'), link('admin-security.html', 'Sécurité de la plateforme', 'Consulter les politiques et protections communes.'), link('certificates.html', 'Certificats', 'Gérer les certificats et leur renouvellement.')] },
    { id: 'operations', title: 'Exploitation', icon: 'settings_suggest', description: 'Encadrer les mises à jour et les tâches. Une révision locale ne lance aucune opération système.', groups: [
      { id: 'lifecycle', title: 'Mises à jour et maintenance', fields: [
        select('operations.updateChannel', 'Canal de mise à jour', [{ value: 'Stable', label: 'Stable' }, { value: 'Security only', label: 'Correctifs de sécurité uniquement' }]),
        field('operations.maintenanceWindow', 'Fenêtre de maintenance', 'text', { required: true, help: 'Documenter le jour, les horaires et le fuseau, par exemple dimanche 02:00–05:00, Europe/Paris.' }),
        check('operations.stagedUpdates', 'Exiger un déploiement progressif'),
        check('operations.automaticSecurity', 'Autoriser les correctifs de sécurité automatiques'),
        select('operations.rebootPolicy', 'Politique de redémarrage', [{ value: 'Approval required', label: 'Approbation requise' }, { value: 'Maintenance window only', label: 'Uniquement pendant la maintenance' }, { value: 'Never automatic', label: 'Aucun redémarrage automatique' }])
      ] },
      { id: 'jobs', title: 'Tâches et journaux', fields: [
        number('operations.maxConcurrentJobs', 'Tâches simultanées', 1, 64),
        number('operations.jobTimeout', 'Délai maximal par tâche', 1, 1440, 'min'),
        select('operations.logLevel', 'Niveau des journaux', [{ value: 'Warning', label: 'Avertissements' }, { value: 'Information', label: 'Informations' }, { value: 'Debug', label: 'Débogage' }])
      ] }
    ], links: [link('updates.html', 'Centre des mises à jour', 'Planifier, suivre et vérifier les déploiements.'), link('jobs.html', 'Centre des tâches', 'Suivre les opérations et leurs résultats.'), link('admin-backups.html', 'Sauvegardes', 'Gérer les politiques, destinations, rétentions et tests de restauration.')] },
    { id: 'branding', title: 'Identité et assistance', icon: 'palette', description: 'Présenter un panneau cohérent et des points de contact clairs.', groups: [
      { id: 'appearance', title: 'Présentation du panneau', fields: [
        field('branding.productName', 'Nom du produit', 'text', { required: true }),
        field('branding.logoUrl', 'Adresse du logo', 'text', { help: 'URL HTTPS ou chemin local /assets/. Aucun code personnalisé.' }),
        select('branding.defaultTheme', 'Thème par défaut', [{ value: 'System preference', label: 'Préférence du système' }, { value: 'Light', label: 'Clair' }, { value: 'Dark', label: 'Sombre' }]),
        field('branding.loginMessage', 'Message de connexion', 'textarea', { help: 'Texte simple affiché sur l’écran de connexion.' })
      ] },
      { id: 'support', title: 'Assistance et communications', fields: [
        field('branding.supportUrl', 'Site d’assistance', 'url', { required: true }),
        field('branding.statusUrl', 'Page d’état', 'url'),
        field('branding.senderEmail', 'Adresse des notifications', 'email', { required: true })
      ] }
    ], links: [link('theme-system.html', 'Système de thème', 'Consulter les composants et les états du thème.'), link('notification-routing.html', 'Routage des notifications', 'Gérer les destinataires, événements et canaux.')] },
    { id: 'governance', title: 'Gouvernance et conservation', icon: 'policy', description: 'Encadrer les changements et les règles communes aux comptes.', groups: [
      { id: 'account-policy', title: 'Politique des comptes', fields: [
        select('behavior.isolation', 'Mode d’isolation par défaut', [{ value: 'Containers', label: 'Conteneurs' }, { value: 'Chroot + systemd', label: 'Chroot et systemd' }]),
        check('behavior.quotaEnforcement', 'Appliquer les quotas'),
        check('behavior.overselling', 'Autoriser la surallocation', 'Les garanties des comptes restent soumises au contrôle de capacité.'),
        check('behavior.telemetry', 'Partager la télémétrie produit', 'Consentement désactivé par défaut ; aucune collecte réelle dans ce prototype.')
      ] },
      { id: 'change-control', title: 'Traçabilité des changements', fields: [
        check('governance.requireApproval', 'Approbation des changements à risque sur la plateforme', 'Préférence de déploiement enregistrée localement ; aucun circuit d’approbation distant n’est connecté. La revue du brouillon reste obligatoire ici.'),
        check('governance.changeReasonRequired', 'Motif obligatoire pour les changements de la plateforme', 'Préférence de déploiement enregistrée localement. Un motif reste toujours requis pour tracer les révisions locales de ce prototype.'),
        number('retention.audit', 'Conservation du journal d’audit', 30, 3650, 'jours'),
        number('retention.logs', 'Conservation des journaux techniques', 1, 365, 'jours'),
        number('retention.jobs', 'Conservation de l’historique des tâches', 1, 730, 'jours')
      ] }
    ], links: [link('audit-explorer.html', 'Journal d’audit', 'Examiner les actions, les auteurs et les preuves.'), link('users-roles.html', 'Utilisateurs et rôles', 'Gérer les autorisations et la délégation.'), link('admin-backups.html', 'Sauvegardes de la plateforme', 'Gérer la conservation des sauvegardes dans son centre dédié.')] },
    { id: 'integrations', title: 'Intégrations', icon: 'hub', description: 'Définir les points de sortie techniques. Les identifiants et les services sont gérés dans leurs centres dédiés.', groups: [
      { id: 'telemetry', title: 'Observabilité technique', fields: [
        check('observability.otelEnabled', 'Exporter vers OpenTelemetry'),
        field('observability.collector', 'Collecteur OpenTelemetry', 'url', { help: 'Endpoint HTTPS sans identifiants dans l’URL.' }),
        number('observability.traceSampling', 'Échantillonnage des traces', 0, 100, '%'),
        check('operations.remoteSyslog', 'Exporter vers un serveur syslog'),
        field('operations.syslogEndpoint', 'Destination syslog TLS', 'text', { help: 'Format tls://logs.example.com:6514, sans identifiants.' })
      ] },
      { id: 'webhook', title: 'Webhook des notifications', fields: [
        check('branding.webhookEnabled', 'Activer le webhook de notifications'),
        field('branding.webhookUrl', 'Endpoint du webhook', 'url', { help: 'URL HTTPS sans identifiants, jeton ou paramètre secret. Utiliser le centre des clés pour l’authentification.' })
      ] }
    ], links: [link('admin-monitoring.html', 'Supervision', 'Gérer les sources, alertes et objectifs de service.'), link('database-services.html', 'Services de données', 'Gérer les moteurs de bases de données.'), link('admin-mail.html', 'Services de messagerie', 'Gérer les relais, domaines et files de courrier.'), link('credential-security.html', 'Clés et références', 'Gérer les clés et leurs références sans les recopier dans cette page.'), link('api-automation.html', 'API et automatisation ROOT', 'Gérer les intégrations de la plateforme et leurs politiques.')] }
  ];

  // Keep the v2 snapshot contract so existing drafts can migrate additively.
  const legacyDraftPaths = sections.flatMap(section => section.groups.flatMap(group => group.fields.map(item => item.path)));
  const text = (path, label, value, extra = {}) => field(path, label, 'text', { defaultValue: value, ...extra });
  const numeric = (path, label, value, min, max, unit) => number(path, label, min, max, unit, { defaultValue: value });
  const flag = (path, label, value = true, help) => ({ ...check(path, label, help), defaultValue: value });
  const choice = (path, label, value, options) => ({ ...select(path, label, options), defaultValue: value });
  const collection = (path, label, columns, value = []) => field(path, label, 'collection', { columns, defaultValue: value, help: 'Ajoutez, modifiez ou retirez des entrées dans le brouillon. La revue enregistre leur configuration locale sans contacter de service.' });
  const destinationColumns = [
    { key: 'name', label: 'Nom', required: true },
    { key: 'endpoint', label: 'Endpoint HTTPS', type: 'url', required: true },
    { key: 'region', label: 'Région' },
    { key: 'secretRef', label: 'Référence de clé', format: 'reference', help: 'Référence fictive secret://demo/… ; aucun secret en clair.' },
    { key: 'enabled', label: 'Activer dans la configuration', type: 'checkbox' }
  ];
  const telemetryGroup = sections.find(section => section.id === 'integrations').groups.shift();
  sections.find(section => section.id === 'integrations').groups.push({ id: 'delivery', title: 'Livraison des événements', fields: [
    numeric('integrations.webhookRetries', 'Tentatives de livraison', 5, 0, 20),
    numeric('integrations.webhookTimeout', 'Délai de livraison', 30, 1, 120, 's'),
    flag('integrations.signedEvents', 'Exiger la signature des événements'),
    flag('integrations.deadLetter', 'Conserver les événements non livrés'),
    numeric('integrations.deadLetterDays', 'Conservation des échecs de livraison', 14, 1, 90, 'jours')
  ] });
  sections.push(
    { id: 'runtimes', title: 'Moteurs applicatifs', icon: 'code_blocks', description: 'Configurer les moteurs, versions de référence et limites héritées par les nouveaux sites.', groups: [
      { id: 'engines', title: 'Serveur web et langages', fields: [
        choice('runtime.webEngine', 'Serveur web par défaut', 'Nginx', ['Nginx', 'Apache', 'OpenLiteSpeed']),
        text('runtime.php', 'Version PHP de référence', '8.3', { required: true, help: 'Version déclarée dans ce prototype ; les versions installables sont gérées dans le catalogue des moteurs.' }),
        text('runtime.node', 'Version Node.js de référence', '22 LTS', { required: true }),
        text('runtime.python', 'Version Python de référence', '3.12', { required: true }),
        text('runtime.ruby', 'Version Ruby de référence', '3.3', { required: true }),
        choice('runtime.packageSource', 'Origine des environnements', 'Catalogue approuvé', ['Catalogue approuvé', 'Dépôt interne'])
      ] },
      { id: 'limits', title: 'Exécution et isolation', fields: [
        numeric('runtime.phpMemory', 'Mémoire PHP par processus', 256, 32, 8192, 'Mio'),
        numeric('runtime.phpExecution', 'Durée maximale d’exécution PHP', 60, 1, 3600, 's'),
        numeric('runtime.phpUpload', 'Taille maximale d’envoi PHP', 64, 1, 4096, 'Mio'),
        flag('runtime.opcache', 'Activer le cache OPcache'),
        flag('runtime.dedicatedPools', 'Isoler les pools par compte'),
        flag('runtime.allowCustom', 'Autoriser les environnements personnalisés', false),
        numeric('runtime.reloadGrace', 'Délai de rechargement progressif', 30, 1, 300, 's')
      ] }
    ], links: [link('web-runtime.html', 'Catalogue des moteurs', 'Gérer les versions, modules et profils disponibles.'), link('web-stack-builder.html', 'Composition des piles web', 'Assembler les profils autorisés.')] },
    { id: 'databases', title: 'Bases de données', icon: 'database', description: 'Définir les valeurs communes des services de données, de leurs connexions et de leur maintenance.', groups: [
      { id: 'engine', title: 'Moteur et capacité', fields: [
        text('database.engine', 'Moteur de référence', 'PostgreSQL', { required: true }),
        numeric('database.port', 'Port de référence', 5432, 1, 65535),
        numeric('database.maxConnections', 'Connexions simultanées', 200, 10, 10000),
        numeric('database.bufferPercent', 'Budget mémoire des tampons', 25, 5, 80, '%'),
        numeric('database.idleTimeout', 'Expiration des connexions inactives', 300, 10, 86400, 's'),
        numeric('database.slowQueryMs', 'Seuil des requêtes lentes', 1000, 10, 600000, 'ms')
      ] },
      { id: 'protection', title: 'Accès et cohérence', fields: [
        flag('database.requireTls', 'Exiger TLS pour les connexions distantes'),
        flag('database.loopbackOnly', 'Limiter l’écoute à la boucle locale'),
        flag('database.publicAccess', 'Autoriser une exposition publique', false),
        flag('database.encryptionAtRest', 'Exiger le chiffrement au repos'),
        flag('database.consistentBackups', 'Exiger des sauvegardes cohérentes'),
        flag('database.maintenanceChecks', 'Vérifier l’intégrité pendant la maintenance')
      ] }
    ], links: [link('database-services.html', 'Services de données', 'Gérer les moteurs et les nœuds de bases de données.')] },
    { id: 'mail', title: 'Messagerie', icon: 'mail', description: 'Définir les paramètres de transport, les limites et les exigences de protection des nouveaux domaines mail.', groups: [
      { id: 'transport', title: 'Transport et files', fields: [
        text('mail.mta', 'Agent de transport', 'Postfix', { required: true }),
        numeric('mail.smtpPort', 'Port de soumission SMTP', 587, 1, 65535),
        numeric('mail.imapPort', 'Port IMAP sécurisé', 993, 1, 65535),
        numeric('mail.maxMessage', 'Taille maximale d’un message', 25, 1, 200, 'Mio'),
        numeric('mail.domainHourlyLimit', 'Envois par domaine et par heure', 100, 1, 100000),
        numeric('mail.queueRetentionDays', 'Conservation maximale en file', 5, 1, 30, 'jours'),
        numeric('mail.maxRetries', 'Tentatives de remise', 12, 1, 100)
      ] },
      { id: 'authentication', title: 'Authentification et filtrage', fields: [
        flag('mail.requireTls', 'Exiger le transport chiffré'),
        flag('mail.dkim', 'Signer les nouveaux domaines avec DKIM'),
        choice('mail.dmarcPolicy', 'Politique DMARC par défaut', 'quarantine', ['none', 'quarantine', 'reject']),
        flag('mail.antivirus', 'Analyser les pièces jointes'),
        numeric('mail.spamThreshold', 'Seuil antispam', 5, 1, 20),
        flag('mail.allowCatchAll', 'Autoriser les adresses attrape-tout', false),
        choice('mail.webmail', 'Client webmail de référence', 'Roundcube', ['Roundcube', 'SnappyMail', 'Désactivé'])
      ] }
    ], links: [link('admin-mail.html', 'Infrastructure mail', 'Gérer les profils, nœuds, relais et files.'), link('webmail-services.html', 'Services webmail', 'Gérer les clients autorisés et leurs endpoints.')] },
    { id: 'backups', title: 'Sauvegardes et restauration', icon: 'backup', description: 'Configurer la stratégie globale de protection, de conservation et de vérification des sauvegardes.', groups: [
      { id: 'schedule', title: 'Planification et conservation', fields: [
        flag('backups.enabled', 'Activer la politique de sauvegarde'),
        choice('backups.schedule', 'Fréquence', 'Quotidienne', ['Quotidienne', 'Toutes les 6 heures', 'Hebdomadaire']),
        text('backups.startTime', 'Heure de départ', '02:00', { format: 'time', required: true }),
        text('backups.timezone', 'Fuseau de planification', 'Europe/Paris', { format: 'timezone', required: true }),
        numeric('backups.retentionDays', 'Conservation quotidienne', 14, 1, 3650, 'jours'),
        numeric('backups.weeklyCopies', 'Copies hebdomadaires', 8, 0, 520),
        numeric('backups.monthlyCopies', 'Copies mensuelles', 12, 0, 120),
        text('backups.targetId', 'Identifiant de la destination', '', { help: 'Identifiant d’une destination déclarée dans Stockage. Vide : choix dans le centre des sauvegardes.' })
      ] },
      { id: 'verification', title: 'Protection et tests', fields: [
        flag('backups.encryption', 'Exiger le chiffrement des sauvegardes'),
        flag('backups.immutable', 'Exiger des copies immuables'),
        flag('backups.verify', 'Vérifier les sommes de contrôle'),
        numeric('backups.restoreTestDays', 'Intervalle des tests de restauration', 30, 1, 365, 'jours'),
        numeric('backups.maxConcurrent', 'Sauvegardes simultanées', 2, 1, 32),
        numeric('backups.bandwidth', 'Débit maximal des sauvegardes', 100, 1, 10000, 'Mio/s')
      ] }
    ], links: [link('admin-backups.html', 'Centre des sauvegardes', 'Gérer les opérations et leurs résultats.'), link('restore-tests.html', 'Tests de restauration', 'Examiner les exercices de récupération.')] },
    { id: 'storage', title: 'Stockage et destinations', icon: 'storage', description: 'Configurer les chemins, les seuils et les destinations de stockage utilisées par les politiques.', groups: [
      { id: 'policy', title: 'Politique des volumes', fields: [
        choice('storage.driver', 'Pilote par défaut', 'Local', ['Local', 'S3 compatible', 'NFS']),
        text('storage.mountRoot', 'Racine des volumes', '/srv/wcp360', { format: 'path', required: true }),
        text('storage.snapshotPolicy', 'Politique d’instantané', 'daily', { required: true }),
        flag('storage.encryption', 'Chiffrer les nouveaux volumes'),
        flag('storage.compression', 'Activer la compression lorsque disponible'),
        numeric('storage.reservePercent', 'Espace réservé', 10, 0, 40, '%'),
        numeric('storage.warningPercent', 'Seuil d’avertissement', 80, 40, 99, '%'),
        numeric('storage.criticalPercent', 'Seuil critique', 90, 50, 100, '%'),
        numeric('storage.ioTimeout', 'Délai maximal des entrées/sorties', 30, 1, 300, 's')
      ] },
      { id: 'destinations', title: 'Destinations configurées', fields: [collection('storage.targets', 'Destinations de stockage', [
        { key: 'id', label: 'Identifiant', required: true, format: 'identifier' }, ...destinationColumns,
        { key: 'bucket', label: 'Conteneur ou bucket', required: true }
      ])] }
    ], links: [link('storage-volumes.html', 'Volumes de la plateforme', 'Consulter l’inventaire et les opérations sur les volumes.')] },
    { id: 'providers', title: 'Fournisseurs et connecteurs', icon: 'hub', description: 'Déclarer les connecteurs, leurs endpoints et leurs références de clé dans une configuration unique.', groups: [
      { id: 'clients', title: 'Comportement des connecteurs', fields: [
        numeric('providerPolicy.timeout', 'Délai de connexion', 30, 1, 300, 's'),
        numeric('providerPolicy.retries', 'Tentatives après échec', 3, 0, 20),
        numeric('providerPolicy.backoff', 'Attente initiale entre tentatives', 5, 1, 300, 's'),
        flag('providerPolicy.verifyTls', 'Vérifier les certificats TLS'),
        flag('providerPolicy.requireSignedResponses', 'Exiger la vérification des réponses lorsque disponible')
      ] },
      { id: 'registry', title: 'Registre des connecteurs', fields: [collection('providers', 'Connecteurs déclarés', [
        { key: 'kind', label: 'Usage', type: 'select', options: ['DNS', 'Stockage', 'Mail', 'SSO', 'ACME', 'Supervision'] }, ...destinationColumns.map(column => column.key === 'endpoint' ? { ...column, label: 'Endpoint du fournisseur', type: 'text', help: 'HTTPS pour une API ; smtps:// ou imaps://hôte:port pour le mail ; sftp://hôte:port ou chemin absolu pour le stockage.' } : column)
      ])] }
    ], links: [link('provider-adapters.html', 'Adaptateurs disponibles', 'Consulter les capacités des adaptateurs et les opérations de démonstration.')] },
    { id: 'ipam', title: 'Adressage IP / IPAM', icon: 'lan', description: 'Définir les pools d’adresses, les règles d’attribution et la traçabilité de leur utilisation.', groups: [
      { id: 'allocation', title: 'Attribution et réservation', fields: [
        choice('ipam.strategy', 'Stratégie d’allocation', 'Séquentielle', ['Séquentielle', 'Réservation manuelle']),
        flag('ipam.autoAllocate', 'Attribuer automatiquement aux nouveaux comptes', false),
        flag('ipam.reserveGateway', 'Réserver les adresses de passerelle'),
        flag('ipam.enforceUnique', 'Interdire les allocations dupliquées'),
        flag('ipam.reverseDns', 'Préparer le DNS inverse', false),
        numeric('ipam.reservationMinutes', 'Durée d’une réservation provisoire', 30, 5, 1440, 'min')
      ] },
      { id: 'pools', title: 'Pools configurés', fields: [collection('ipam.pools', 'Pools d’adresses', [
        { key: 'name', label: 'Nom', required: true },
        { key: 'cidr', label: 'Réseau CIDR IPv4 ou IPv6', required: true, format: 'cidr' },
        { key: 'gateway', label: 'Passerelle', format: 'ip' },
        { key: 'vlan', label: 'VLAN (0 : aucun)', type: 'number', min: 0, max: 4094 },
        { key: 'enabled', label: 'Autoriser l’allocation', type: 'checkbox' }
      ])] }
    ], links: [link('ip-management.html', 'Inventaire des IP', 'Suivre les adresses attribuées et leurs demandes.')] },
    { id: 'livepatch', title: 'Noyau et Livepatch', icon: 'healing', description: 'Définir la politique des correctifs du noyau, des redémarrages et du déploiement progressif.', groups: [
      { id: 'kernel', title: 'Correctifs à chaud', fields: [
        flag('livepatch.enabled', 'Prévoir les correctifs à chaud', false),
        choice('livepatch.provider', 'Mécanisme déclaré', 'Non configuré', ['Non configuré', 'kpatch', 'Livepatch', 'KernelCare']),
        choice('livepatch.channel', 'Canal de politique', 'Stable', ['Stable', 'Sécurité uniquement', 'Validation interne']),
        numeric('livepatch.maxDelayDays', 'Délai maximal d’application', 7, 1, 90, 'jours'),
        numeric('livepatch.canaryNodes', 'Nœuds de validation', 1, 1, 100),
        flag('livepatch.requireApproval', 'Exiger une approbation'),
        flag('livepatch.maintenanceOnly', 'Respecter les fenêtres de maintenance'),
        flag('livepatch.autoRollback', 'Prévoir un retour arrière'),
        flag('livepatch.rebootFallback', 'Autoriser le redémarrage de secours après revue')
      ] }
    ], links: [link('updates-kernel.html', 'Mises à jour du noyau', 'Consulter les correctifs et les opérations de maintenance.')] },
    { id: 'zerotrust', title: 'Zero Trust', icon: 'verified_user', description: 'Définir les exigences d’identité des services, de réévaluation des accès et de preuve des appareils.', groups: [
      { id: 'access-policy', title: 'Politique d’accès continue', fields: [
        flag('zeroTrust.enabled', 'Activer la politique Zero Trust', false),
        flag('zeroTrust.defaultDeny', 'Refuser les accès non explicitement autorisés'),
        flag('zeroTrust.requireDevice', 'Exiger une preuve de conformité de l’appareil', false),
        numeric('zeroTrust.sessionRecheck', 'Réévaluation de session', 15, 1, 240, 'min'),
        numeric('zeroTrust.policyTtl', 'Durée du cache de politique', 60, 1, 3600, 's'),
        flag('zeroTrust.bindSession', 'Lier la session à son contexte')
      ] },
      { id: 'service-identities', title: 'Identité des services', fields: [
        flag('zeroTrust.mtls', 'Exiger mTLS entre les services'),
        flag('zeroTrust.serviceIdentity', 'Attribuer une identité par service'),
        numeric('zeroTrust.certificateDays', 'Durée des certificats internes', 30, 1, 365, 'jours'),
        text('zeroTrust.issuer', 'Émetteur d’identité', 'https://identity.example.com', { type: 'url' }),
        text('zeroTrust.audience', 'Audience autorisée', 'wcp360', { required: true }),
        numeric('zeroTrust.breakGlassHours', 'Durée maximale du secours', 1, 1, 24, 'h')
      ] }
    ], links: [link('credential-security.html', 'Identités privilégiées', 'Gérer les accès et références de clé.'), link('privileged-session.html', 'Sessions opérateur', 'Examiner la traçabilité des accès.')] },
    { id: 'forensics', title: 'Forensics et preuves', icon: 'policy', description: 'Encadrer la collecte de preuves et leur conservation lors d’un incident.', groups: [
      { id: 'evidence', title: 'Constitution du dossier', fields: [
        text('forensics.casePrefix', 'Préfixe des dossiers', 'INC', { format: 'identifier', required: true }),
        numeric('forensics.retentionDays', 'Conservation des preuves', 90, 7, 3650, 'jours'),
        numeric('forensics.maxBundle', 'Taille maximale d’un dossier', 1024, 64, 102400, 'Mio'),
        choice('forensics.hashAlgorithm', 'Empreinte de contrôle', 'SHA-256', ['SHA-256', 'SHA-512']),
        flag('forensics.chainOfCustody', 'Tracer la chaîne de conservation'),
        flag('forensics.freezeAfterCapture', 'Rendre les dossiers capturés non modifiables'),
        flag('forensics.redactSecrets', 'Masquer les secrets dans les exports'),
        flag('forensics.captureMemory', 'Prévoir une capture mémoire après autorisation', false),
        flag('forensics.captureNetwork', 'Prévoir une capture réseau après autorisation', false),
        flag('forensics.exportApproval', 'Exiger une revue avant export')
      ] }
    ], links: [link('incidents.html', 'Incidents', 'Gérer les dossiers et les actions de suivi.'), link('support-bundle.html', 'Dossier de diagnostic', 'Préparer un dossier de démonstration.')] },
    { id: 'observability', title: 'Observabilité', icon: 'monitoring', description: 'Définir la collecte, l’échantillonnage, l’export et la conservation des signaux techniques.', groups: [
      telemetryGroup,
      { id: 'signals', title: 'Collecte et conservation', fields: [
        flag('observability.metricsEnabled', 'Prévoir la collecte des métriques'),
        numeric('observability.metricsRetention', 'Conservation des métriques', 30, 1, 365, 'jours'),
        numeric('observability.tracesRetention', 'Conservation des traces', 7, 1, 90, 'jours'),
        numeric('observability.logSampling', 'Échantillonnage des journaux', 100, 1, 100, '%'),
        numeric('observability.alertCooldown', 'Délai entre alertes répétées', 10, 1, 1440, 'min'),
        numeric('observability.healthInterval', 'Intervalle des sondes de santé', 30, 5, 3600, 's'),
        flag('observability.redactPersonalData', 'Masquer les données personnelles des signaux'),
        flag('observability.correlateAudit', 'Relier les signaux aux identifiants d’audit')
      ] }
    ], links: [link('admin-monitoring.html', 'Supervision de la plateforme', 'Consulter les métriques et les alertes.'), link('logs-diagnostics.html', 'Journaux et diagnostic', 'Examiner les journaux du périmètre ROOT.')] },
    { id: 'aiops', title: 'AIOps et automatisation', icon: 'psychology', description: 'Définir les limites des recommandations et des actions automatisées. Aucun modèle distant n’est interrogé ici.', groups: [
      { id: 'recommendations', title: 'Recommandations encadrées', fields: [
        flag('aiops.enabled', 'Prévoir l’assistance AIOps', false),
        choice('aiops.mode', 'Mode de décision', 'Conseils uniquement', ['Conseils uniquement', 'Actions après approbation']),
        choice('aiops.provider', 'Origine des analyses', 'Local', ['Local', 'Endpoint interne']),
        text('aiops.endpoint', 'Endpoint interne', '', { type: 'url' }),
        numeric('aiops.minConfidence', 'Confiance minimale déclarée', 85, 50, 100, '%'),
        numeric('aiops.maxActionsHour', 'Actions autorisées par heure', 3, 1, 100),
        numeric('aiops.dataRetentionDays', 'Conservation des analyses', 7, 1, 90, 'jours'),
        flag('aiops.requireApproval', 'Exiger une approbation humaine'),
        flag('aiops.explainDecisions', 'Conserver les explications des recommandations'),
        flag('aiops.redactPersonalData', 'Masquer les données personnelles'),
        flag('aiops.learnFromSignals', 'Autoriser l’apprentissage depuis les signaux', false)
      ] }
    ], links: [link('api-automation.html', 'Automatisation ROOT', 'Gérer les politiques et les demandes automatisées.')] },
    { id: 'edge', title: 'Edge, cache et CDN', icon: 'public', description: 'Configurer les règles de cache, de transport et de connexion aux origines.', groups: [
      { id: 'delivery', title: 'Diffusion et cache', fields: [
        flag('edge.enabled', 'Prévoir un service Edge', false),
        choice('edge.mode', 'Mode de diffusion', 'DNS uniquement', ['DNS uniquement', 'Proxy inverse', 'Cache CDN']),
        numeric('edge.cacheTtl', 'Durée de cache par défaut', 300, 0, 2592000, 's'),
        numeric('edge.staleSeconds', 'Tolérance de contenu périmé', 60, 0, 86400, 's'),
        numeric('edge.maxObject', 'Taille maximale d’un objet en cache', 64, 1, 2048, 'Mio'),
        numeric('edge.originTimeout', 'Délai de réponse de l’origine', 30, 1, 300, 's'),
        text('edge.healthPath', 'Chemin de la sonde de santé', '/health', { format: 'path', required: true }),
        flag('edge.http2', 'Autoriser HTTP/2'),
        flag('edge.http3', 'Autoriser HTTP/3', false),
        flag('edge.forceHttps', 'Exiger HTTPS vers les origines'),
        flag('edge.purgeApproval', 'Exiger une revue pour les purges globales')
      ] }
    ], links: [link('provider-adapters.html', 'Adaptateurs Edge', 'Consulter les connecteurs disponibles.'), link('web-runtime.html', 'Transport web', 'Gérer les profils des serveurs web.')] },
    { id: 'sustainability', title: 'Énergie et sobriété', icon: 'eco', description: 'Définir les objectifs de consommation et de consolidation, sous réserve des objectifs de disponibilité.', groups: [
      { id: 'budgets', title: 'Budget et consolidation', fields: [
        flag('sustainability.enabled', 'Prévoir le suivi énergétique', false),
        text('sustainability.carbonRegion', 'Région de référence', 'FR', { required: true }),
        numeric('sustainability.monthlyBudget', 'Budget énergétique mensuel', 1000, 1, 10000000, 'kWh'),
        numeric('sustainability.targetUtilization', 'Utilisation cible des nœuds', 65, 20, 95, '%'),
        numeric('sustainability.idleThreshold', 'Seuil d’inactivité', 15, 1, 50, '%'),
        numeric('sustainability.powerCap', 'Plafond par nœud (0 : non défini)', 0, 0, 10000, 'W'),
        numeric('sustainability.maxConsolidations', 'Consolidations simultanées', 2, 1, 20),
        flag('sustainability.respectSlo', 'Préserver les objectifs de disponibilité'),
        flag('sustainability.monthlyReports', 'Préparer des rapports mensuels')
      ] }
    ], links: [link('resource-diagnostics.html', 'Capacité et ressources', 'Examiner la capacité déclarée de la plateforme.')] },
    { id: 'whitelabel', title: 'Marque blanche', icon: 'design_services', description: 'Définir les possibilités de personnalisation déléguées aux partenaires et les mentions du panneau.', groups: [
      { id: 'delegation', title: 'Personnalisation des partenaires', fields: [
        flag('whiteLabel.enabled', 'Prévoir la marque blanche', false),
        flag('whiteLabel.partnerBranding', 'Autoriser l’identité visuelle des partenaires'),
        flag('whiteLabel.customDomains', 'Autoriser les domaines personnalisés', false),
        text('whiteLabel.panelDomain', 'Domaine de référence du panneau', 'panel.example.com', { format: 'fqdn', required: true }),
        text('whiteLabel.accentColor', 'Couleur d’accent', '#0866ff', { format: 'color', help: 'Couleur hexadécimale. Son contraste doit être vérifié dans le thème avant un déploiement réel.' }),
        choice('whiteLabel.supportMode', 'Assistance déléguée', 'Héritée de ROOT', ['Héritée de ROOT', 'Par partenaire']),
        text('whiteLabel.footer', 'Mention de pied de page', 'WCP360 · Plateforme de démonstration')
      ] },
      { id: 'legal', title: 'Mentions et consentement', fields: [
        text('whiteLabel.legalUrl', 'Mentions légales', 'https://panel.example.com/legal', { type: 'url' }),
        text('whiteLabel.privacyUrl', 'Politique de confidentialité', 'https://panel.example.com/privacy', { type: 'url' }),
        text('whiteLabel.termsUrl', 'Conditions d’utilisation', 'https://panel.example.com/terms', { type: 'url' }),
        flag('whiteLabel.consentBanner', 'Prévoir une bannière de consentement', false)
      ] }
    ], links: [link('users-roles.html', 'Revendeurs et délégation', 'Gérer les partenaires et leurs autorisations.'), link('theme-system.html', 'Référence visuelle', 'Vérifier les composants et états du thème.')] }
  );
  const navigationGroups = [
    { title: 'Socle de la plateforme', sections: ['server', 'network', 'access', 'resources', 'security', 'governance'] },
    { title: 'Services d’hébergement', sections: ['runtimes', 'databases', 'mail', 'backups'] },
    { title: 'Infrastructure', sections: ['storage', 'ipam', 'livepatch', 'edge'] },
    { title: 'Connectivité et signaux', sections: ['providers', 'integrations', 'observability'] },
    { title: 'Protection avancée', sections: ['zerotrust', 'forensics'] },
    { title: 'Exploitation et optimisation', sections: ['operations', 'aiops', 'sustainability'] },
    { title: 'Identité visuelle', sections: ['branding', 'whitelabel'] }
  ];
  const template = {
    schemaVersion: 3, version: 0, versions: [], tab: 'server', environment: 'EU-PROD',
    identity: { hostname: 'wcp-admin.example.com', serverName: 'wcp-eu-01', timezone: 'Europe/Paris', locale: 'French (France)', rootEmail: 'root@example.com', securityEmail: 'security@example.com', supportEmail: 'support@example.com', backupEmail: 'backup@example.com', abuseEmail: 'abuse@example.com', postmasterEmail: 'postmaster@example.com', ntpEnabled: true, ntpServers: 'time.cloudflare.com, pool.ntp.org' },
    network: { interface: 'ens18', primaryIpv4: '203.0.113.10', primaryIpv6: '2001:db8:360::10', gateway: '203.0.113.1', ipv6: true, mtu: 1500, resolverPrimary: '1.1.1.1', resolverSecondary: '9.9.9.9', strictManagementAcl: true, managementCidr: '10.20.0.0/16' },
    access: { sshPort: 2222, sshKeys: true, rootSsh: false, rootPassword: false, mfaRoot: true, sessionMinutes: 30, concurrentSessions: 3, apiTokenDays: 30, cookieValidation: 'Strict', ssoEnabled: false, localFallback: true, trustedProxy: '10.20.0.0/16' },
    resource: { cpu: 2, ram: 4, swap: 1, io: 50, iops: 2000, diskSoft: 50, diskHard: 60, inodes: 250000, bandwidth: 1000, pids: 512 },
    security: { passwordMin: 14, passwordExpiry: 0, passwordHistory: 12, passwordBreached: true, tlsMinimum: 'TLS 1.2', hsts: true, adminAttempts: 5, loginWindow: 10, banMinutes: 30, progressiveBan: true },
    operations: { updateChannel: 'Stable', maintenanceWindow: 'Dimanche 02:00–05:00, Europe/Paris', stagedUpdates: true, automaticSecurity: true, rebootPolicy: 'Approval required', maxConcurrentJobs: 8, jobTimeout: 120, logLevel: 'Information', remoteSyslog: false, syslogEndpoint: 'tls://logs.example.net:6514' },
    branding: { productName: 'WCP360', logoUrl: '', defaultTheme: 'System preference', loginMessage: 'Accès réservé aux personnes autorisées. Les activités sont journalisées.', supportUrl: 'https://support.example.com', statusUrl: 'https://status.example.com', senderEmail: 'notifications@example.com', webhookEnabled: false, webhookUrl: 'https://hooks.example.com/wcp360' },
    behavior: { isolation: 'Containers', quotaEnforcement: true, overselling: false, telemetry: false },
    governance: { requireApproval: true, changeReasonRequired: true },
    retention: { audit: 365, logs: 30, jobs: 90 },
    observability: { otelEnabled: false, collector: 'https://otel.example.com:4318', traceSampling: 10 }
  };
  const fieldList = sections.flatMap(section => section.groups.flatMap(group => group.fields.map(item => ({ ...item, section: section.id }))));
  const byPath = new Map(fieldList.map(item => [item.path, item]));
  const safePath = path => typeof path === 'string' && path.split('.').every(key => key && !blockedKeys.has(key));
  const get = (config, path) => safePath(path) ? path.split('.').reduce((value, key) => value && Object.prototype.hasOwnProperty.call(value, key) ? value[key] : undefined, config) : undefined;
  const put = (config, path, value) => {
    const parts = path.split('.'), last = parts.pop();
    let parent = config;
    for (const key of parts) { if (!parent[key] || typeof parent[key] !== 'object' || Array.isArray(parent[key])) parent[key] = {}; parent = parent[key]; }
    parent[last] = value;
    return config;
  };
  const migrationAliases = { 'runtime.phpExecution': 'runtime.execution', 'runtime.phpUpload': 'runtime.upload', 'runtime.webEngine': 'runtime.web', 'backups.retentionDays': 'retention.daily', 'backups.weeklyCopies': 'retention.weekly', 'backups.monthlyCopies': 'retention.monthly' };
  const set = (config, path, value) => {
    if (!byPath.has(path)) throw new TypeError('Champ de configuration inconnu.');
    put(config, path, value);
    const alias = migrationAliases[path]; if (alias && get(config, alias) !== undefined) put(config, alias, value);
    return config;
  };
  const clone = value => value === undefined ? undefined : JSON.parse(JSON.stringify(value));
  for (const item of fieldList) if (item.defaultValue !== undefined && get(template, item.path) === undefined) put(template, item.path, clone(item.defaultValue));
  const defaults = () => clone(template);
  const sectionFor = path => byPath.get(path)?.section || fieldList.find(item => item.type === 'collection' && path.startsWith(item.path + '.'))?.section || ({ identity: 'server', environment: 'server', network: 'network', access: 'access', resource: 'resources', security: 'security', operations: 'operations', branding: 'branding', behavior: 'governance', governance: 'governance', retention: 'governance' }[path.split('.')[0]] || 'integrations');
  const issue = (path, message) => ({ path, message, section: sectionFor(path) });
  const secretKey = key => {
    const normalized = key.replace(/[^a-z]/gi, '').toLowerCase();
    return /^(?:password|passwd|secret|token|apikey|accesskey|privatekey|clientsecret|credentials?|authorization|signingkey|refreshtoken|bearer|secretref|secretreference|credentialref)$/.test(normalized) || /(?:secret|password|privatekey|apikey|accesstoken|secretref|secretreference)$/.test(normalized);
  };
  const reference = value => /^(?:secret|vault|kms):\/\/[a-z0-9][a-z0-9/_.-]{0,250}$/i.test(value);
  const cleanUrl = value => {
    if (!/^[a-z][a-z0-9+.-]*:\/\//i.test(value)) return value;
    try {
      const url = new URL(value);
      let changed = !!url.username || !!url.password;
      url.username = ''; url.password = '';
      for (const key of [...url.searchParams.keys()]) if (secretKey(key)) { url.searchParams.delete(key); changed = true; }
      if (url.hash && /(?:password|secret|token|api[_-]?key|authorization)=/i.test(url.hash)) { url.hash = ''; changed = true; }
      return changed ? url.toString() : value;
    } catch { return value.replace(/^(\w+:\/\/)[^/@\s]+@/, '$1'); }
  };
  function sanitize(value, path = '', issues = [], depth = 0) {
    if (depth > 40) { issues.push(issue(path, 'Une structure trop profonde a été ignorée.')); return undefined; }
    if (Array.isArray(value)) return value.map((item, index) => sanitize(item, `${path}.${index}`, issues, depth + 1)).filter(item => item !== undefined);
    if (value && typeof value === 'object') {
      const result = {};
      for (const [key, entry] of Object.entries(value)) {
        const next = path ? `${path}.${key}` : key;
        if (blockedKeys.has(key)) { issues.push(issue(next, 'Une clé de structure interdite a été ignorée.')); continue; }
        if (secretKey(key) && entry !== null && typeof entry !== 'boolean' && !(typeof entry === 'string' && reference(entry))) { issues.push(issue(next, 'Une ancienne valeur sensible a été retirée. Utiliser une référence de clé dans le centre dédié.')); continue; }
        const cleaned = sanitize(entry, next, issues, depth + 1);
        if (cleaned !== undefined) result[key] = cleaned;
      }
      return result;
    }
    if (typeof value === 'string') { const cleaned = cleanUrl(value); if (cleaned !== value) issues.push(issue(path, 'Les identifiants ou paramètres sensibles intégrés dans une URL ont été retirés.')); return cleaned; }
    return value === null || typeof value === 'boolean' || typeof value === 'number' && Number.isFinite(value) ? value : undefined;
  }
  function load(raw) {
    const issues = [];
    let saved = {};
    if (raw !== null && raw !== undefined) {
      try { saved = JSON.parse(raw); if (!saved || typeof saved !== 'object' || Array.isArray(saved)) throw new TypeError(); }
      catch { return { config: defaults(), issues: [issue('', 'La configuration locale est illisible. Les valeurs par défaut ont été chargées sans écraser le stockage.')] }; }
    }
    const config = sanitize(saved, '', issues);
    for (const [path, alias] of Object.entries(migrationAliases)) if (get(config, path) === undefined && get(config, alias) !== undefined) put(config, path, get(config, alias));
    for (const item of fieldList) {
      const fallback = get(template, item.path), value = get(config, item.path);
      let normalized = value;
      if (value === undefined) normalized = clone(fallback);
      else if (item.type === 'collection') {
        if (!Array.isArray(value)) { normalized = clone(fallback); issues.push(issue(item.path, `La liste « ${item.label} » était invalide et doit être revue.`)); }
        else {
          normalized = value.filter(row => row && typeof row === 'object' && !Array.isArray(row));
          if (normalized.length !== value.length) issues.push(issue(item.path, 'Des entrées illisibles ont été retirées de la liste à réparer.'));
          normalized = normalized.map((row, index) => {
            const record = { ...row };
            if (!record.id) record.id = 'legacy-' + item.path.replace(/\./g, '-') + '-' + index;
            for (const column of item.columns) if (record[column.key] === undefined) record[column.key] = item.path === 'providers' && column.key === 'kind' ? ({ Backup: 'Stockage', DNS: 'DNS', Mail: 'Mail', SSO: 'SSO' }[record.type] || 'DNS') : column.type === 'checkbox' ? false : column.type === 'number' ? column.min || 0 : column.type === 'select' ? column.options[0] : '';
            return record;
          });
        }
      }
      else if (typeof fallback === 'number' && typeof value === 'string' && value.trim() !== '' && Number.isFinite(Number(value))) normalized = Number(value);
      else if (typeof fallback === 'boolean' && (value === 'true' || value === 'false')) normalized = value === 'true';
      else if (typeof value !== typeof fallback || typeof value === 'number' && !Number.isFinite(value)) {
        normalized = clone(fallback); issues.push(issue(item.path, `Le type de « ${item.label} » était invalide ; sa valeur par défaut a été rétablie.`));
      }
      put(config, item.path, normalized);
    }
    config.schemaVersion = template.schemaVersion;
    if (!Number.isSafeInteger(config.version) || config.version < 0) { if (config.version !== undefined) issues.push(issue('version', 'Le numéro de révision invalide a été réinitialisé.')); config.version = 0; }
    if (!Array.isArray(config.versions)) config.versions = [];
    if (!sections.some(section => section.id === config.tab)) config.tab = 'server';
    return { config, issues };
  }

  const fqdn = value => typeof value === 'string' && value.length <= 253 && value.includes('.') && !/^[\d.]+$/.test(value) && value.split('.').every(label => /^(?!-)[a-z0-9-]{1,63}(?<!-)$/i.test(label));
  const ipv4 = value => typeof value === 'string' && /^(?:(?:0|[1-9]\d{0,2})\.){3}(?:0|[1-9]\d{0,2})$/.test(value) && value.split('.').every(part => Number(part) <= 255);
  function ipv6(value) {
    if (typeof value !== 'string' || !value.includes(':') || /[^0-9a-f:.]/i.test(value) || (value.match(/::/g) || []).length > 1) return false;
    let normalized = value;
    if (value.includes('.')) { const last = value.slice(value.lastIndexOf(':') + 1); if (!ipv4(last)) return false; normalized = value.slice(0, value.lastIndexOf(':') + 1) + 'ffff:ffff'; }
    const compressed = normalized.includes('::');
    const parts = normalized.split(':').filter(Boolean);
    if (!parts.every(part => /^[a-f0-9]{1,4}$/i.test(part))) return false;
    if (normalized.startsWith(':') && !normalized.startsWith('::') || normalized.endsWith(':') && !normalized.endsWith('::')) return false;
    if (normalized.startsWith(':::') || normalized.endsWith(':::') || normalized.includes(':::')) return false;
    return compressed ? parts.length < 8 : parts.length === 8;
  }
  const ip = value => ipv4(value) || ipv6(value);
  const cidr = value => {
    const parts = String(value).split('/');
    if (parts.length !== 2 || !/^(0|[1-9]\d*)$/.test(parts[1])) return false;
    return ipv4(parts[0]) ? Number(parts[1]) <= 32 : ipv6(parts[0]) && Number(parts[1]) <= 128;
  };
  const tokens = value => String(value || '').split(/[,\s]+/).filter(Boolean);
  function addressNumber(value) {
    if (ipv4(value)) return { bits: 32, number: value.split('.').reduce((result, octet) => (result << 8n) + BigInt(octet), 0n) };
    if (!ipv6(value)) return null;
    let normalized = value;
    if (normalized.includes('.')) { const tail = normalized.slice(normalized.lastIndexOf(':') + 1).split('.').map(Number); normalized = normalized.slice(0, normalized.lastIndexOf(':') + 1) + ((tail[0] << 8) + tail[1]).toString(16) + ':' + ((tail[2] << 8) + tail[3]).toString(16); }
    const halves = normalized.split('::'), left = halves[0].split(':').filter(Boolean), right = (halves[1] || '').split(':').filter(Boolean);
    const parts = halves.length === 2 ? [...left, ...Array(8 - left.length - right.length).fill('0'), ...right] : left;
    return { bits: 128, number: parts.reduce((result, part) => (result << 16n) + BigInt('0x' + part), 0n) };
  }
  function networkRange(value) {
    if (!cidr(value)) return null;
    const [address, prefix] = value.split('/'), parsed = addressNumber(address), shift = BigInt(parsed.bits - Number(prefix));
    const first = (parsed.number >> shift) << shift;
    return { bits: parsed.bits, first, last: first + (1n << shift) - 1n };
  }
  const email = value => typeof value === 'string' && value.length <= 254 && value.split('@').length === 2 && /^[^\s@<>]+@[^\s@<>]+$/.test(value) && value.split('@')[0].length <= 64 && !/(^\.|\.\.|\.@)/.test(value) && fqdn(value.split('@')[1]);
  function endpoint(value, protocol = 'https:') {
    try { const url = new URL(value); return url.protocol === protocol && !!url.hostname && !url.username && !url.password && !url.search && !url.hash && (protocol !== 'tls:' || !!url.port && Number(url.port) >= 1 && Number(url.port) <= 65535) && (fqdn(url.hostname) || ip(url.hostname.replace(/^\[|\]$/g, '')) || url.hostname === 'localhost'); } catch { return false; }
  }
  function formatValid(value, format) {
    if (!format || value === '') return true;
    if (typeof value !== 'string') return false;
    if (format === 'time') return /^([01]\d|2[0-3]):[0-5]\d$/.test(value);
    if (format === 'timezone') { try { new Intl.DateTimeFormat('fr', { timeZone: value }).format(); return true; } catch { return false; } }
    if (format === 'path') return /^\/(?!\/)[a-z0-9_./-]*$/i.test(value) && !value.split('/').includes('..');
    if (format === 'identifier') return /^[a-z0-9][a-z0-9_-]{0,62}$/i.test(value);
    if (format === 'color') return /^#[a-f0-9]{6}$/i.test(value);
    if (format === 'fqdn') return fqdn(value);
    if (format === 'cidr') return cidr(value);
    if (format === 'ip') return ip(value);
    if (format === 'reference') return reference(value);
    return false;
  }
  function providerEndpoint(value, kind) {
    if (endpoint(value)) return true;
    if (kind === 'Mail') {
      const scheme = String(value).match(/^([a-z]+):\/\//i)?.[1]?.toLowerCase();
      if (['smtps', 'imaps'].includes(scheme)) { try { const url = new URL(value); return endpoint(value, scheme + ':') && Number(url.port) >= 1 && Number(url.port) <= 65535; } catch { return false; } }
      // Older wizard snapshots used host:port for their implicit-TLS services.
      const match = String(value).match(/^(\[[0-9a-f:]+\]|[^:/\s]+):(\d+)$/i);
      return !!match && (fqdn(match[1]) || ip(match[1].replace(/^\[|\]$/g, ''))) && Number(match[2]) >= 1 && Number(match[2]) <= 65535;
    }
    if (kind === 'Stockage') {
      if (formatValid(value, 'path') && String(value).startsWith('/')) return true;
      try { const url = new URL(value); return endpoint(value, 'sftp:') && Number(url.port) >= 1 && Number(url.port) <= 65535; } catch { return false; }
    }
    return false;
  }
  function validateCollection(path, records) {
    const definition = byPath.get(path), messages = [];
    if (!definition || definition.type !== 'collection') return ['Liste de configuration inconnue.'];
    if (!Array.isArray(records) || records.length > 100) return ['Déclarer une liste de 100 entrées maximum.'];
    const seen = new Set(), names = new Set(), endpoints = new Set(), networks = [];
    for (const [index, record] of records.entries()) {
      const label = `Entrée ${index + 1}`;
      if (!record || typeof record !== 'object' || Array.isArray(record)) { messages.push(label + ' : structure invalide.'); continue; }
      if (typeof record.id !== 'string' || !record.id.trim() || seen.has(record.id)) messages.push(label + ' : identifiant absent ou dupliqué.');
      seen.add(record.id);
      for (const column of definition.columns) {
        const value = record[column.key], prefix = label + ' · ' + column.label + ' : ';
        if (column.type === 'checkbox') { if (typeof value !== 'boolean') messages.push(prefix + 'choisir un état.'); continue; }
        if (column.type === 'number') { if (!Number.isInteger(value) || value < column.min || value > column.max) messages.push(prefix + `saisir un entier de ${column.min} à ${column.max}.`); continue; }
        if (column.required && (typeof value !== 'string' || !value.trim())) { messages.push(prefix + 'valeur obligatoire.'); continue; }
        if (value === undefined && !column.required) continue;
        if (typeof value !== 'string' || value.length > 512) { messages.push(prefix + 'texte de 512 caractères maximum.'); continue; }
        if (column.type === 'select' && !column.options.includes(value)) messages.push(prefix + 'choisir une valeur proposée.');
        if (column.type === 'url' && value && !endpoint(value)) messages.push(prefix + 'utiliser un endpoint HTTPS sans identifiants ni paramètres.');
        if (path === 'providers' && column.key === 'endpoint' && value && !providerEndpoint(value, record.kind)) messages.push(prefix + 'utiliser un endpoint valide pour cet usage, sans identifiants ni paramètres.');
        if (!formatValid(value, column.format)) messages.push(prefix + 'format invalide.');
      }
      const name = String(record.name || '').trim().toLocaleLowerCase('fr');
      if (names.has(name)) messages.push(label + ' : ce nom existe déjà.'); names.add(name);
      if (record.endpoint) {
        let address = record.endpoint.toLowerCase(); try { if (record.endpoint.includes('://')) { const url = new URL(record.endpoint); address = url.protocol.toLowerCase() + '//' + url.host.toLowerCase() + url.pathname.replace(/\/+$/, ''); } } catch {}
        const id = (record.kind || '') + ':' + address;
        if (endpoints.has(id)) messages.push(label + ' : cet endpoint est déjà déclaré pour cet usage.'); endpoints.add(id);
      }
      if (record.cidr) {
        const range = networkRange(record.cidr), gateway = addressNumber(record.gateway);
        if (range) {
          if (networks.some(existing => existing.bits === range.bits && range.first <= existing.last && existing.first <= range.last)) messages.push(label + ' : ce pool chevauche un réseau déjà déclaré.');
          networks.push(range);
          if (gateway && (gateway.bits !== range.bits || gateway.number < range.first || gateway.number > range.last)) messages.push(label + ' : la passerelle doit appartenir au réseau déclaré.');
        }
      }
    }
    return [...new Set(messages)];
  }
  function validate(config) {
    const issues = [];
    const require = (path, valid, message) => { if (!valid) issues.push(issue(path, message)); };
    for (const item of fieldList) {
      const value = get(config, item.path);
      if (item.type === 'collection') { for (const message of validateCollection(item.path, value)) issues.push(issue(item.path, message)); }
      else if (item.type === 'checkbox') require(item.path, typeof value === 'boolean', 'Choisir un état activé ou désactivé.');
      else if (item.type === 'number') require(item.path, Number.isFinite(value) && Number.isInteger(value) && value >= item.min && value <= item.max, `Saisir un nombre entier entre ${item.min} et ${item.max}.`);
      else if (item.type === 'select') require(item.path, item.options.some(option => option.value === value), 'Choisir une valeur dans la liste.');
      else {
        require(item.path, typeof value === 'string', 'Saisir une valeur textuelle.');
        if (item.required) require(item.path, typeof value === 'string' && value.trim().length > 0, 'Ce champ est obligatoire.');
        if (typeof value === 'string') require(item.path, value.length <= (item.type === 'textarea' ? 4000 : 512), 'La valeur dépasse la longueur maximale autorisée.');
        if (item.type === 'email') require(item.path, !value && !item.required || email(value), 'Saisir une adresse e-mail valide.');
        if (item.type === 'url') require(item.path, !value && !item.required || endpoint(value), 'Utiliser une URL HTTPS sans identifiants, paramètres ni fragment.');
        if (item.format) require(item.path, formatValid(value, item.format), 'Le format attendu n’est pas respecté.');
      }
    }
    require('identity.hostname', fqdn(get(config, 'identity.hostname')), 'Saisir un nom de domaine complet, par exemple panel.example.com.');
    require('identity.serverName', /^[a-z0-9][a-z0-9._-]{0,62}$/i.test(get(config, 'identity.serverName') || ''), 'Utiliser de 1 à 63 lettres, chiffres, points, tirets ou traits de soulignement.');
    try { new Intl.DateTimeFormat('fr', { timeZone: get(config, 'identity.timezone') }).format(); if (!get(config, 'identity.timezone')) throw new TypeError(); } catch { issues.push(issue('identity.timezone', 'Utiliser un fuseau IANA valide, par exemple Europe/Paris ou UTC.')); }
    if (get(config, 'identity.ntpEnabled')) require('identity.ntpServers', tokens(get(config, 'identity.ntpServers')).length > 0 && tokens(get(config, 'identity.ntpServers')).every(value => fqdn(value) || ip(value)), 'Déclarer au moins un serveur NTP avec un nom complet ou une adresse IP valide.');
    require('network.interface', /^[a-z0-9][a-z0-9_.:@-]{0,31}$/i.test(get(config, 'network.interface') || ''), 'Saisir un nom d’interface valide, par exemple ens18.');
    for (const path of ['network.primaryIpv4', 'network.gateway']) require(path, ipv4(get(config, path)), 'Saisir une adresse IPv4 valide.');
    if (get(config, 'network.ipv6') || get(config, 'network.primaryIpv6')) require('network.primaryIpv6', ipv6(get(config, 'network.primaryIpv6')), 'Saisir une adresse IPv6 valide ou désactiver IPv6 et laisser le champ vide.');
    for (const path of ['network.resolverPrimary', 'network.resolverSecondary']) require(path, ip(get(config, path)), 'Saisir une adresse IPv4 ou IPv6 valide.');
    require('network.resolverSecondary', get(config, 'network.resolverPrimary') !== get(config, 'network.resolverSecondary'), 'Le résolveur secondaire doit être distinct du résolveur principal.');
    for (const path of ['network.managementCidr', 'access.trustedProxy']) {
      const list = tokens(get(config, path));
      require(path, list.every(cidr), 'Utiliser des réseaux CIDR valides, par exemple 10.20.0.0/16 ou 2001:db8::/48.');
      if (path === 'network.managementCidr' && get(config, 'network.strictManagementAcl')) { require(path, list.length > 0, 'Déclarer au moins un réseau avant de restreindre les accès.'); require(path, !list.some(value => /\/0$/.test(value)), 'Une restriction de gestion ne peut pas autoriser l’ensemble d’Internet (/0).'); }
      if (path === 'access.trustedProxy') require(path, !list.some(value => /\/0$/.test(value)), 'Un proxy de confiance ne peut pas couvrir l’ensemble d’Internet (/0).');
    }
    require('access.mfaRoot', get(config, 'access.mfaRoot') === true, 'Le MFA est obligatoire pour ROOT.');
    require('access.rootSsh', get(config, 'access.rootSsh') === false, 'Utiliser une identité SSH nominative ; la connexion directe ROOT doit rester désactivée.');
    require('access.rootPassword', get(config, 'access.rootPassword') === false, 'Le mot de passe SSH ROOT doit rester désactivé.');
    require('access.sshKeys', get(config, 'access.sshKeys') === true, 'L’authentification SSH par clé est obligatoire.');
    require('access.localFallback', get(config, 'access.ssoEnabled') === true || get(config, 'access.localFallback') === true, 'Conserver un accès local lorsque le SSO est désactivé.');
    require('resource.diskSoft', get(config, 'resource.diskSoft') <= get(config, 'resource.diskHard'), 'Le seuil d’avertissement doit être inférieur ou égal au quota disque maximal.');
    require('retention.audit', get(config, 'retention.audit') >= get(config, 'retention.logs'), 'Conserver l’audit au moins aussi longtemps que les journaux techniques.');
    require('retention.audit', get(config, 'retention.audit') >= get(config, 'retention.jobs'), 'Conserver l’audit au moins aussi longtemps que l’historique des tâches.');
    const logo = get(config, 'branding.logoUrl');
    require('branding.logoUrl', !logo || typeof logo === 'string' && (/^\/assets\/[a-z0-9/_-]+\.(?:svg|png|webp|jpe?g)$/i.test(logo) && !logo.includes('..') || endpoint(logo)), 'Utiliser une image dans /assets/ ou une URL HTTPS sans identifiants.');
    for (const [enabled, path, protocol] of [['observability.otelEnabled', 'observability.collector', 'https:'], ['operations.remoteSyslog', 'operations.syslogEndpoint', 'tls:'], ['branding.webhookEnabled', 'branding.webhookUrl', 'https:']]) {
      const value = get(config, path);
      if (get(config, enabled) || value) require(path, endpoint(value, protocol), protocol === 'tls:' ? 'Définir une destination tls://hôte:port valide, sans identifiants ni paramètres.' : 'Définir un endpoint HTTPS sans identifiants, paramètres ni fragment.');
    }
    require('database.publicAccess', !get(config, 'database.publicAccess') || !get(config, 'database.loopbackOnly'), 'Désactiver l’écoute locale exclusive avant une exposition publique.');
    require('database.requireTls', !get(config, 'database.publicAccess') || get(config, 'database.requireTls'), 'TLS est obligatoire pour une base exposée publiquement.');
    require('storage.warningPercent', get(config, 'storage.warningPercent') < get(config, 'storage.criticalPercent'), 'Le seuil d’avertissement doit être inférieur au seuil critique.');
    require('sustainability.idleThreshold', get(config, 'sustainability.idleThreshold') < get(config, 'sustainability.targetUtilization'), 'Le seuil d’inactivité doit rester inférieur à l’utilisation cible.');
    require('livepatch.provider', !get(config, 'livepatch.enabled') || get(config, 'livepatch.provider') !== 'Non configuré', 'Choisir un mécanisme avant d’activer cette politique.');
    require('zeroTrust.defaultDeny', !get(config, 'zeroTrust.enabled') || get(config, 'zeroTrust.defaultDeny'), 'La politique Zero Trust exige un refus par défaut.');
    require('zeroTrust.mtls', !get(config, 'zeroTrust.enabled') || get(config, 'zeroTrust.mtls'), 'La politique Zero Trust exige mTLS entre les services.');
    require('aiops.endpoint', !get(config, 'aiops.enabled') || get(config, 'aiops.provider') !== 'Endpoint interne' || endpoint(get(config, 'aiops.endpoint')), 'Déclarer un endpoint HTTPS pour les analyses internes.');
    require('aiops.requireApproval', !get(config, 'aiops.enabled') || get(config, 'aiops.requireApproval'), 'Les actions AIOps de ce prototype nécessitent une approbation humaine.');
    const target = get(config, 'backups.targetId');
    require('backups.targetId', !target || (get(config, 'storage.targets') || []).some(row => row.id === target && row.enabled), 'Choisir l’identifiant d’une destination de stockage activée.');
    // Cross-field checks may overlap the field schema; show each message once.
    return issues.filter((entry, index) => issues.findIndex(other => other.path === entry.path && other.message === entry.message) === index);
  }
  function diff(before, after) {
    return fieldList.filter(item => JSON.stringify(get(before, item.path)) !== JSON.stringify(get(after, item.path))).map(item => ({ path: item.path, label: item.label, section: item.section, before: clone(get(before, item.path)), after: clone(get(after, item.path)), risk: ['network', 'access', 'security', 'providers', 'storage', 'ipam', 'zerotrust', 'forensics', 'backups', 'livepatch'].includes(item.section) || item.path === 'identity.hostname' || item.path === 'behavior.isolation' || item.path === 'behavior.quotaEnforcement' ? 'high' : ['server', 'branding', 'whitelabel'].includes(item.section) ? 'low' : 'medium' }));
  }
  const serialize = config => JSON.stringify(sanitize(config), null, 2);
  const freeze = value => { if (value && typeof value === 'object') { Object.values(value).forEach(freeze); Object.freeze(value); } return value; };
  window.WCPSettingsModel = Object.freeze({ sections: freeze(sections), navigationGroups: freeze(navigationGroups), legacyDraftPaths: freeze(legacyDraftPaths), schemaVersion: 3, defaults, load, validate, validateCollection, diff, get, set, serialize });
})();
