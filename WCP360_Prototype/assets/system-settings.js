(() => {
  'use strict';

  const host = document.querySelector('#systemSettingsCenter');
  if (!host) return;

  const storageKey = 'wcp360-system-settings-v1';
  const defaults = {
    tab: 'server', version: 13, environment: 'EU-PROD',
    identity: {
      hostname: 'wcp-admin.example.com', serverName: 'wcp-eu-01', role: 'Control plane + web node', assetTag: 'EU-WCP-0001',
      timezone: 'Europe/Paris', ntpEnabled: true, ntpServers: 'time.cloudflare.com, pool.ntp.org', locale: 'English (United States)',
      rootEmail: 'root@example.com', securityEmail: 'security@example.com', supportEmail: 'support@example.com', backupEmail: 'backup@example.com',
      abuseEmail: 'abuse@example.com', postmasterEmail: 'postmaster@example.com', emergencyContact: '+33 1 84 80 20 20'
    },
    network: {
      interface: 'ens18', primaryIpv4: '203.0.113.10', primaryIpv6: '2001:db8:360::10', gateway: '203.0.113.1', emergencyIp: '203.0.113.11',
      managementCidr: '10.20.0.0/16', resolverPrimary: '1.1.1.1', resolverSecondary: '9.9.9.9', resolverFallback: '8.8.8.8',
      reverseDns: 'wcp-admin.example.com', mtu: 1500, ipv6: true, outboundNat: true, proxyProtocol: false, strictManagementAcl: true
    },
    access: {
      sshPort: 2222, rootSsh: false, rootPassword: false, sshKeys: true, mfaRoot: true, cookieValidation: 'Strict', sessionMinutes: 30,
      concurrentSessions: 3, trustedProxy: '10.20.0.0/16', apiTokenDays: 30, ssoEnabled: false, localFallback: true, sudoRecording: true, idleLock: true
    },
    resource: {
      cpu: 2, cpuWeight: 100, ram: 4, virtualMemory: 6, swap: 1, io: 50, iops: 2000, inodes: 250000, diskSoft: 50, diskHard: 60,
      bandwidth: 1000, processes: 100, pids: 512, openFiles: 4096, emailsPerHour: 300, databases: 25, dbStorage: 20
    },
    runtime: {
      webEngine: 'Nginx + PHP-FPM', proxyMode: 'Nginx reverse proxy', http3: true, brotli: true, keepAlive: 65, maxConnections: 4096, workers: 0,
      php: '8.4', phpHandler: 'PHP-FPM', phpMemory: 256, upload: 128, post: 128, execution: 120, inputVars: 3000, fpmChildren: 10,
      phpEnabled: true, nodeEnabled: true, pythonEnabled: true, rubyEnabled: true,
      composer: 'Composer 2.x', node: '22 LTS', nodePackageManager: 'pnpm', nodeMemory: 512, nodeProcesses: 4,
      python: '3.13', pythonPackageManager: 'uv + pip', ruby: '3.3', rubyPackageManager: 'Bundler',
      perAccountRuntime: true, customModules: true, licensedModules: true, appInstallers: true
    },
    database: {
      mysqlEngine: 'MariaDB 11.4 LTS', postgresEngine: 'PostgreSQL 17', postgresEnabled: true, mysqlConnections: 300, postgresConnections: 200,
      accountDatabases: 25, accountUsers: 25, maxDatabaseSize: 20, maxPacket: 64, bufferPool: 4096, slowQueryLog: true, slowQuerySeconds: 2,
      remoteAccess: 'Explicit allowlist', remoteCidr: '10.20.0.0/16', phpMyAdmin: true, adminer: true, pgAdmin: true, externalSso: true, binaryLogs: true
    },
    mail: {
      mta: 'Exim', mailboxServer: 'Dovecot', smtpRelay: true, smtpHost: 'smtp-relay.example.net', smtpPort: 465, smtpTls: 'TLS verification required',
      smtpIdentity: 'wcp360-relay', domainHourly: 300, domainDaily: 3000, accountHourly: 500, maxRecipients: 50, maxMessage: 50, queueHours: 72,
      spamEngine: 'Rspamd', spamThreshold: 6, dkim: true, spf: true, dmarc: true, imap: true, pop3: false, catchAll: false, outboundMonitoring: true
    },
    dns: {
      mode: 'External API + hidden primary', clusterEnabled: true, primaryNs: 'ns1.example.com', secondaryNs: 'ns2.example.com', tertiaryNs: 'ns3.example.com',
      defaultTtl: 3600, negativeTtl: 300, serialFormat: 'Unix timestamp', transferAcl: '198.51.100.20/32, 198.51.100.21/32', dnssec: true,
      notify: true, recursion: false, glueValidation: true, reverseZones: true, healthSeconds: 60
    },
    providers: [
      { id: 'dns-primary', type: 'DNS', name: 'External DNS EU', icon: 'dns', endpoint: 'https://dns-api.example.net/v1', region: 'EU West', auth: 'API token', user: 'wcp360-root', secret: '••••••••', primary: true, enabled: true, status: 'Healthy' },
      { id: 'dns-secondary', type: 'DNS', name: 'Secondary DNS', icon: 'public', endpoint: 'https://secondary-dns.example.net/api', region: 'EU Central', auth: 'TSIG + API', user: 'wcp360-transfer', secret: '••••••••', primary: false, enabled: true, status: 'Healthy' },
      { id: 'mail-smtp', type: 'Mail', name: 'SMTP outbound relay', icon: 'outbox', endpoint: 'smtp-relay.example.net:465', region: 'EU West', auth: 'mTLS + credentials', user: 'wcp360-relay', secret: '••••••••', primary: true, enabled: true, status: 'Healthy' },
      { id: 'mail-imap', type: 'Mail', name: 'IMAP service gateway', icon: 'mail', endpoint: 'imap.example.net:993', region: 'EU West', auth: 'mTLS', user: 'wcp360-services', secret: '••••••••', primary: false, enabled: true, status: 'Healthy' },
      { id: 'backup-s3', type: 'Backup', name: 'S3 primary vault', icon: 'cloud', endpoint: 'https://s3.eu.example.net/wcp360', region: 'EU · immutable', auth: 'Access key + secret', user: 'AKIA••••EXAMPLE', secret: '••••••••', primary: true, enabled: true, status: 'Healthy' },
      { id: 'backup-gcs', type: 'Backup', name: 'GCS archive vault', icon: 'cloud_queue', endpoint: 'gs://wcp360-archive-example', region: 'EU multi-region', auth: 'Service account', user: 'wcp360-backup@example.com', secret: '••••••••', primary: false, enabled: true, status: 'Healthy' },
      { id: 'backup-sftp', type: 'Backup', name: 'SFTP secondary vault', icon: 'folder_lock', endpoint: 'sftp://vault.example.net:22/backups', region: 'EU Central', auth: 'SSH key', user: 'wcp360-backup', secret: '••••••••', primary: false, enabled: true, status: 'Healthy' },
      { id: 'backup-local', type: 'Backup', name: 'Local recovery repository', icon: 'hard_drive', endpoint: '/var/lib/wcp360/recovery', region: 'Control node', auth: 'Filesystem ACL', user: 'root:backup', secret: '••••••••', primary: false, enabled: true, status: 'Healthy' }
    ],
    backup: {
      strategy: 'Incremental forever + synthetic full', compression: 'Zstandard', compressionLevel: 6, bandwidth: 150, window: '01:00–06:00', parallelJobs: 2,
      encryption: 'AES-256 + KMS', immutable: true, verification: true, restoreTest: 'Weekly', includeSystem: true, includeHome: true,
      includeDatabases: true, includeMail: true, incremental: true, pauseOnLoad: true
    },
    retention: { daily: 7, weekly: 4, monthly: 12, yearly: 1, audit: 365, logs: 30, jobs: 90, supportBundles: 14, trash: 30 },
    security: {
      passwordMin: 14, passwordExpiry: 0, passwordHistory: 12, passwordHash: 'Argon2id', passwordBreached: true, passwordComplexity: true,
      tlsMinimum: 'TLS 1.2', tlsProfile: 'Modern · PCI-DSS', customCiphers: '', hsts: true, ocsp: true,
      pciMode: true, wafEnabled: true, wafLevel: 'Balanced', wafCustomRules: 8, sshAttempts: 5, ftpAttempts: 5, smtpAttempts: 8, adminAttempts: 5,
      loginWindow: 10, banMinutes: 30, progressiveBan: true, allowlist: '10.20.0.0/16, 127.0.0.1/32', firewall: 'nftables + WCP Guard',
      ids: 'Fail2ban + behavioral detection', malware: true, antivirus: true
    },
    secrets: {
      vaultProvider: 'WCP Vault + envelope encryption', kmsProvider: 'External KMS · EU region', keyAlias: 'alias/wcp360-production',
      certificateAuthority: 'Public ACME + private internal CA', acmeDirectory: 'https://acme-v02.api.letsencrypt.org/directory',
      rotationDays: 90, automaticRotation: true, hsmRequired: false, secretScanning: true, emergencyAccess: true
    },
    behavior: {
      autoAccounts: false, overselling: false, isolation: 'Containers', chroot: true, autoDns: true, autoSsl: true, ipv6: true, shell: false,
      maintenance: true, quotaEnforcement: true, telemetry: false, hostnameSubdomains: false, accountPolicy: 'ROOT and authorized resellers',
      domainOwnershipCheck: true, autoSuspendOverage: false, installerSandbox: true, resellerDelegation: true
    },
    branding: {
      productName: 'WCP360', logoUrl: '/assets/wcp360-logo.svg', primaryColor: '#0b7cff', accentColor: '#ff8a00', defaultTheme: 'System preference',
      loginMessage: 'Authorized access only. Activity is monitored.', supportUrl: 'https://support.example.com', statusUrl: 'https://status.example.com',
      senderName: 'WCP360 Operations', senderEmail: 'notifications@example.com', emailFooter: 'Example Hosting · Infrastructure Operations',
      criticalEmail: true, warningEmail: true, dailyDigest: true, webhookEnabled: true, webhookUrl: 'https://hooks.example.com/wcp360', notifyResellers: true
    },
    operations: {
      updateChannel: 'Stable', maintenanceWindow: 'Sunday · 02:00–05:00', stagedUpdates: true, automaticSecurity: true, automaticFeature: false,
      rebootPolicy: 'Approval required', maxConcurrentJobs: 8, jobTimeout: 120, logLevel: 'Information', remoteSyslog: true,
      syslogEndpoint: 'tls://logs.example.net:6514', metricsRetention: 90, crashReports: false
    },
    livePatch: {
      provider: 'KernelCare Enterprise', kernelStream: 'Production stable', kernelEnabled: true, userspaceEnabled: true, autoCritical: true,
      canaryNodes: 1, checkMinutes: 30, deploymentSla: 4, rebootAfterDays: 30, rollbackEnabled: true, maintenanceFallback: true,
      openssl: true, glibc: true, zlib: true, kernelCveOnly: true
    },
    ipam: {
      authority: 'WCP360 integrated IPAM', ipv4Pool: '203.0.113.0/24', ipv6Pool: '2001:db8:360::/48', allocation: 'Dynamic with reservations',
      gateway: '203.0.113.1', vlan: 360, dedicatedSsl: true, autoReleaseDays: 7, ptrProvider: 'Datacenter rDNS API', autoPtr: true,
      ptrTemplate: '{account}.host.example.com', interfaceCapacity: 10000, accountEgress: 1000, burstPercent: 120, ingressShaping: true,
      abuseNullRoute: true, anycastEligible: false
    },
    forensics: {
      wormEnabled: true, wormProvider: 'S3 Object Lock · Compliance', retentionYears: 7, hashChain: 'SHA-512 + Merkle chain', externalAnchor: true,
      fimEnabled: true, fimPaths: '/bin, /sbin, /usr/bin, /etc, /boot', fimInterval: 5, rootkitEngine: 'AIDE + rkhunter + eBPF signals',
      quarantineEnabled: true, quarantineEgress: 'DNS + security mirrors only', readonlyWebsite: true, preserveMemory: true,
      captureNetwork: true, evidenceRetention: 365, legalHold: true
    },
    storage: {
      filesystem: 'ZFS', pool: 'tank-hosting', raid: 'RAIDZ2', compression: 'zstd', compressionLevel: 6, deduplication: false,
      snapshotPolicy: 'Hourly 24 · Daily 30 · Monthly 12', scrubSchedule: 'Sunday · 03:00', arcLimit: 25, thinProvisioning: true,
      nfsEnabled: true, nfsEndpoint: 'nfs.example.net:/wcp360', cifsEnabled: false, cifsEndpoint: '//nas.example.net/archive',
      mountOptions: 'noexec,nosuid,nodev', inodeWarning: 80, inodeCritical: 90, quotaWarning: 80, quotaCritical: 95, hardBlock: 100,
      emergencyReserve: 5
    },
    whiteLabel: {
      multiBrand: true, resellerThemes: true, customDomain: 'panel.example.com', certificateAutomation: true, faviconUrl: '/assets/favicon.ico',
      customCss: ':root { --brand-radius: 10px; }', defaultLanguage: 'English', fallbackLanguage: 'English', languagePacks: 12,
      dictionaryOverrides: true, rtlSupport: true, bannerEnabled: true, bannerAudience: 'All customers and resellers', bannerSeverity: 'Maintenance',
      bannerTitle: 'Scheduled infrastructure maintenance', bannerMessage: 'A maintenance window is planned. No customer action is required.',
      bannerWindow: 'Sunday · 02:00–05:00', dismissible: true
    },
    aiOps: {
      enabled: true, model: 'Local secured operations LLM', autonomy: 'Recommend + approval', selfHealing: true,
      managedServices: 'Nginx, PHP-FPM, MariaDB, PostgreSQL, Exim, Dovecot', maxActions: 3, cooldownMinutes: 15,
      anomalyDetection: true, predictiveAlerts: true, learningWindow: 30, confidence: 92, naturalCli: true, cliApproval: true,
      outboundInference: false, redactSecrets: true, evidenceRetention: 90
    },
    edge: {
      provider: 'WCP Edge + Cloudflare', anycastDns: true, edgeCaching: true, defaultTtl: 3600, staleSeconds: 86400,
      cacheAuthenticated: false, imageOptimization: true, originShield: true, tieredCache: true, wasmEnabled: true,
      wasmMemory: 128, wasmCpu: 50, wasmTimeout: 30, signedDeployments: true, edgeRegions: 'EU, North America, Asia-Pacific',
      purgePropagation: 15, ddosIntegration: true
    },
    sustainability: {
      enabled: true, powerSource: 'Datacenter PDU + cloud telemetry', regionFactor: 56, pue: 1.18, accountAttribution: true,
      realtimePower: true, carbonReports: true, greenRouting: true, renewableThreshold: 80, flexibleWindow: '00:00–06:00',
      maxJobDelay: 6, movableJobs: 'Backups, malware scans, log compaction, image optimization', customerBadges: true,
      annualTarget: 25, offsetDisclosure: true
    },
    observability: {
      otelEnabled: true, collector: 'https://otel.example.com:4318', protocol: 'OTLP/HTTP + TLS', traceSampling: 10,
      metricsInterval: 15, logExport: true, traceExport: true, metricsExport: true, profiling: true, slowQuery: true,
      slowQueryMs: 500, codeProfiling: 'On-demand + anomaly trigger', apmOverhead: 2, prometheus: true, grafana: true,
      datadog: false, tenantIsolation: true, telemetryRetention: 30
    },
    zeroTrust: {
      sessionRecording: true, recordingMode: 'Terminal text + privileged UI events', recordingRetention: 365, recordingWorm: true,
      jitEnabled: true, defaultDuration: 15, maxDuration: 60, approvalRequired: true, dualApproval: true, stepUpMfa: true,
      devicePosture: true, managedDevice: true, networkContext: true, riskThreshold: 'Low or medium', commandPolicy: 'Allowlist + approval',
      clipboardRestricted: true, fileTransferRestricted: true, breakGlassApproval: true, inactiveRevoke: 5
    },
    versions: [
      { version: 13, actor: 'Yann · ROOT', date: 'Today · 14:22', summary: 'Server governance baseline expanded', status: 'Active' },
      { version: 12, actor: 'Amélie · Platform Admin', date: '28 Oct · 11:42', summary: 'Backup retention and WAF policy', status: 'Superseded' },
      { version: 11, actor: 'WCP360 Setup Wizard', date: '21 Oct · 09:14', summary: 'Production baseline generated', status: 'Superseded' },
      { version: 10, actor: 'Yann · ROOT', date: '19 Oct · 14:30', summary: 'External DNS provider failover', status: 'Superseded' }
    ]
  };

  const categories = [
    ['Foundation', 'server', 'dns', 'Server identity', 'hostname fqdn timezone ntp contact root email locale'],
    ['Foundation', 'network', 'lan', 'Network & resolvers', 'ip ipv4 ipv6 gateway resolver emergency routing interface'],
    ['Foundation', 'access', 'admin_panel_settings', 'Access & sessions', 'ssh root mfa 2fa cookie session sso api keys'],
    ['Hosting stack', 'resources', 'memory', 'Resource defaults', 'cpu ram memory disk quota inode bandwidth iops cgroup lve'],
    ['Hosting stack', 'runtimes', 'web', 'Web & runtimes', 'nginx apache litespeed php node python ruby http3'],
    ['Hosting stack', 'databases', 'database', 'Database services', 'mysql mariadb postgres phpmyadmin adminer pgadmin sso'],
    ['Hosting stack', 'mail', 'mail', 'Mail services', 'smtp imap pop dovecot exim relay spam dkim dmarc'],
    ['Infrastructure', 'providers', 'hub', 'DNS & providers', 'dns cluster nameserver api provider s3 sftp gcs storage'],
    ['Infrastructure', 'backups', 'backup', 'Backup & recovery', 'retention compression throttle restore immutable encryption'],
    ['Governance', 'security', 'shield', 'Security, TLS & WAF', 'password hash ssl tls cipher waf brute force firewall malware'],
    ['Governance', 'behavior', 'tune', 'Platform behavior', 'overselling account creation isolation chroot container autossl'],
    ['Governance', 'branding', 'palette', 'Branding & notifications', 'logo color notification email webhook support status'],
    ['Governance', 'operations', 'settings_suggest', 'Operations & lifecycle', 'update maintenance logs jobs license trial telemetry syslog'],
    ['Advanced infrastructure', 'livepatch', 'deployed_code_update', 'Live patching', 'kernelcare kernel live patch openssl glibc zlib reboot zero day'],
    ['Advanced infrastructure', 'ipam', 'lan', 'IPAM & traffic', 'ip pool ipv4 ipv6 reverse ptr interface shaping bandwidth vlan'],
    ['Advanced infrastructure', 'forensics', 'policy', 'Forensics & quarantine', 'worm immutable audit fim rootkit sandbox quarantine evidence'],
    ['Advanced infrastructure', 'storage', 'storage', 'Storage & filesystems', 'zfs btrfs raid snapshot nfs cifs quota inode nas san'],
    ['Experience', 'whitelabel', 'format_paint', 'Multi-brand & localization', 'white label reseller theme dictionary language banner notice'],
    ['Intelligence & edge', 'aiops', 'neurology', 'AI Ops & self-healing', 'ai anomaly predictive repair natural language cli llm'],
    ['Intelligence & edge', 'edge', 'hub', 'Edge & CDN', 'cdn anycast cache wasm workers varnish cloudflare edge'],
    ['Intelligence & edge', 'sustainability', 'eco', 'Green hosting', 'carbon energy renewable pue power emissions routing'],
    ['Intelligence & edge', 'observability', 'monitoring', 'Observability & APM', 'opentelemetry otel tracing profiling slow query prometheus grafana datadog'],
    ['Enterprise security', 'zerotrust', 'encrypted', 'Zero-Trust & JIT', 'rbac jit privilege elevation recording auditing device posture approval'],
    ['Change management', 'changes', 'difference', 'Change control', 'diff publish version rollback approval audit']
  ].map(([group, id, icon, label, keywords]) => ({ group, id, icon, label, keywords }));

  const clone = value => JSON.parse(JSON.stringify(value));
  const merge = (base, extra) => {
    if (!extra || typeof extra !== 'object') return base;
    Object.keys(extra).forEach(name => {
      if (Array.isArray(extra[name])) base[name] = extra[name];
      else if (extra[name] && typeof extra[name] === 'object') base[name] = merge({ ...base[name] }, extra[name]);
      else base[name] = extra[name];
    });
    return base;
  };
  const read = () => { try { return JSON.parse(localStorage.getItem(storageKey)) || {}; } catch (error) { return {}; } };
  const state = merge(clone(defaults), read());
  const requestedTab = new URLSearchParams(window.location.search).get('section') || window.location.hash.replace(/^#/, '');
  if (requestedTab && categories.some(category => category.id === requestedTab)) state.tab = requestedTab;
  else if (!categories.some(category => category.id === state.tab)) state.tab = 'server';
  let baseline = clone(state);
  let dirty = false;
  let drawerMode = '';
  let selectedId = '';

  const escape = value => String(value ?? '').replace(/[&<>'"]/g, char => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[char]));
  const getPath = (object, path) => path.split('.').reduce((value, name) => value?.[name], object);
  const setPath = (object, path, value) => { const parts = path.split('.'); const last = parts.pop(); const parent = parts.reduce((item, name) => item[name], object); parent[last] = value; };
  const save = () => { try { localStorage.setItem(storageKey, JSON.stringify(state)); } catch (error) {} };
  const operation = (name, kind = 'system-settings', risk = 'high') => document.dispatchEvent(new CustomEvent('wcp:operation', { detail: { name, scope: 'root', kind, risk, actor: 'Yann · ROOT' } }));
  const status = (label, tone = '') => `<span class="wcp-status ${tone || (/healthy|enabled|active|passed|compliant/i.test(label) ? 'success' : /warning|pending|review/i.test(label) ? 'warning' : '')}">${escape(label)}</span>`;

  host.innerHTML = `
    <div class="sys-page">
      <div class="wcp-page-head"><div><div class="wcp-page-eyebrow">WCP-ADMIN · GOVERNANCE & SYSTEM CONFIGURATION</div><h2 class="wcp-page-heading">System Settings</h2><p class="wcp-page-sub">Authoritative server, hosting stack, infrastructure and security policy. Every effective change is validated, versioned and auditable.</p></div><div class="wcp-page-actions"><button class="wcp-action-btn" type="button" data-sys-export><span class="material-symbols-rounded">download</span>Export redacted</button><a class="wcp-action-btn primary" href="setup-wizard.html"><span class="material-symbols-rounded">auto_awesome</span>Setup wizard</a></div></div>
      <div class="sys-feedback" role="status" aria-live="polite"></div>
      <div class="sys-metrics" data-sys-metrics></div>
      <div class="sys-console"><aside class="sys-category-panel" aria-label="System Settings categories"><label class="sys-search"><span class="material-symbols-rounded">search</span><input type="search" placeholder="Find a setting" data-sys-search aria-label="Find a System Settings category"><kbd>/</kbd></label><label class="sys-category-picker"><span class="material-symbols-rounded">tune</span><span><strong>Settings category</strong><small>Choose a global policy area</small></span><select data-sys-category-select aria-label="System Settings category"></select></label><nav class="sys-category-nav" data-sys-nav></nav><div class="sys-category-foot"><span class="material-symbols-rounded">verified_user</span><div><strong>ROOT governance</strong><small>Draft → validate → publish → audit</small></div></div></aside><section class="sys-workspace" data-sys-view></section></div>
    </div>
    <button class="sys-drawer-backdrop" type="button" aria-label="Close settings drawer"></button>
    <aside class="sys-drawer" aria-hidden="true"><div class="sys-drawer-head"><div><strong id="sysDrawerTitle">Configuration</strong><small data-sys-drawer-sub></small></div><button class="sys-close" type="button" aria-label="Close"><span class="material-symbols-rounded">close</span></button></div><form class="sys-drawer-form"></form></aside>`;

  const metrics = host.querySelector('[data-sys-metrics]');
  const nav = host.querySelector('[data-sys-nav]');
  const categorySelect = host.querySelector('[data-sys-category-select]');
  const view = host.querySelector('[data-sys-view]');
  const drawer = document.querySelector('.sys-drawer');
  const backdrop = document.querySelector('.sys-drawer-backdrop');
  const drawerForm = drawer.querySelector('.sys-drawer-form');

  const validations = () => {
    const issues = [];
    const add = (severity, area, message) => issues.push({ severity, area, message });
    if (!/^[a-z0-9][a-z0-9.-]+\.[a-z]{2,}$/i.test(state.identity.hostname)) add('critical', 'Server identity', 'The management hostname must be a valid FQDN.');
    if (!state.network.resolverPrimary || !state.network.resolverSecondary) add('critical', 'Network', 'Two upstream DNS resolvers are required.');
    if (Number(state.resource.diskSoft) > Number(state.resource.diskHard)) add('critical', 'Resources', 'Disk soft quota cannot exceed the hard quota.');
    if (state.access.rootPassword) add('critical', 'Access', 'Direct ROOT password authentication is not production-safe.');
    if (['TLS 1.0', 'TLS 1.1'].includes(state.security.tlsMinimum)) add('critical', 'TLS', 'Obsolete TLS protocols violate the platform baseline.');
    if (!state.secrets.vaultProvider || !state.secrets.kmsProvider || !state.secrets.keyAlias) add('critical', 'Secrets', 'Vault, KMS provider and primary key alias are required.');
    if (state.secrets.hsmRequired && !/HSM|PKCS#11/i.test(state.secrets.kmsProvider)) add('critical', 'Secrets', 'HSM-backed ROOT keys require an HSM or PKCS#11 key provider.');
    if (state.mail.smtpRelay && !state.mail.smtpHost) add('critical', 'Mail', 'The enabled SMTP relay requires a hostname.');
    if (state.dns.clusterEnabled && (!state.dns.primaryNs || !state.dns.secondaryNs)) add('critical', 'DNS', 'The DNS cluster requires at least two nameservers.');
    if (!state.providers.some(item => item.type === 'Backup' && item.enabled)) add('critical', 'Backup', 'At least one enabled backup destination is required.');
    if (state.livePatch.kernelEnabled && state.livePatch.provider === 'Disabled') add('critical', 'Live patching', 'Kernel live patching requires an enabled patch provider.');
    if (!state.ipam.ipv4Pool || !state.ipam.ipv6Pool) add('critical', 'IPAM', 'Managed address allocation requires declared IPv4 and IPv6 pools.');
    if (Number(state.storage.inodeWarning) >= Number(state.storage.inodeCritical)) add('critical', 'Storage', 'The inode warning threshold must be lower than the critical threshold.');
    if (Number(state.storage.quotaWarning) >= Number(state.storage.quotaCritical) || Number(state.storage.quotaCritical) > Number(state.storage.hardBlock)) add('critical', 'Storage', 'Quota thresholds must increase from warning to critical to hard block.');
    if (state.edge.wasmEnabled && !state.edge.signedDeployments) add('critical', 'Edge', 'WebAssembly edge workloads require signed deployment artifacts.');
    if (state.observability.otelEnabled && !state.observability.collector) add('critical', 'Observability', 'OpenTelemetry export requires a collector endpoint.');
    if (Number(state.zeroTrust.defaultDuration) > Number(state.zeroTrust.maxDuration)) add('critical', 'Zero-Trust', 'Default JIT duration cannot exceed the maximum elevation duration.');
    if (!state.backup.immutable) add('warning', 'Backup', 'No immutable recovery copy is enforced.');
    if (state.access.rootSsh) add('warning', 'Access', 'Direct ROOT SSH access increases privileged access risk.');
    if (state.behavior.overselling) add('warning', 'Capacity', 'Overselling requires continuous admission and exhaustion monitoring.');
    if (!state.security.pciMode) add('warning', 'TLS', 'PCI-DSS cipher enforcement is disabled.');
    if (!state.operations.remoteSyslog) add('warning', 'Operations', 'Audit evidence is stored only on the control node.');
    if (!state.forensics.wormEnabled) add('warning', 'Forensics', 'Privileged audit evidence is not protected by WORM retention.');
    if (state.aiOps.autonomy === 'Auto-execute low risk') add('warning', 'AI Ops', 'Autonomous remediation requires continuous playbook and evidence review.');
    if (!state.zeroTrust.sessionRecording) add('warning', 'Zero-Trust', 'Privileged session recording is disabled.');
    if (!state.secrets.secretScanning) add('warning', 'Secrets', 'Configuration secret scanning is disabled.');
    return issues;
  };

  const notify = (message, warning = false) => {
    const feedback = host.querySelector('.sys-feedback');
    feedback.textContent = message;
    feedback.classList.toggle('warning', warning);
    feedback.classList.add('show');
    document.dispatchEvent(new CustomEvent('wcp:notification', { detail: { title: 'System Settings', detail: message } }));
    window.setTimeout(() => feedback.classList.remove('show'), 5200);
  };
  const renderMetrics = () => {
    const healthy = state.providers.filter(item => item.enabled && item.status === 'Healthy').length;
    const enabled = state.providers.filter(item => item.enabled).length;
    const issues = validations();
    const critical = issues.filter(item => item.severity === 'critical').length;
    metrics.innerHTML = `<article><span>Configuration version</span><strong>v${state.version}</strong><small>${escape(state.environment)} · immutable revision</small></article><article><span>Server identity</span><strong>${escape(state.identity.serverName)}</strong><small>${escape(state.identity.hostname)}</small></article><article><span>Provider health</span><strong class="${healthy === enabled ? 'success' : 'warning'}">${healthy} / ${enabled}</strong><small>DNS · mail · backup endpoints</small></article><article><span>Governance posture</span><strong class="${critical ? 'warning' : 'success'}">${critical ? `${critical} blockers` : 'Compliant'}</strong><small>${issues.length - critical} advisory findings · ${dirty ? 'draft pending' : 'synchronized'}</small></article>`;
  };
  const renderNav = () => {
    const groups = [...new Set(categories.map(item => item.group))];
    nav.innerHTML = groups.map(group => `<div class="sys-nav-group" data-sys-group><span>${escape(group)}</span>${categories.filter(item => item.group === group).map(item => `<button type="button" class="${state.tab === item.id ? 'active' : ''}" data-sys-tab="${item.id}" data-keywords="${escape(`${item.label} ${item.keywords}`.toLowerCase())}"><span class="material-symbols-rounded">${item.icon}</span><b>${escape(item.label)}</b>${item.id === 'changes' && dirty ? '<i></i>' : ''}</button>`).join('')}</div>`).join('');
    categorySelect.innerHTML = groups.map(group => `<optgroup label="${escape(group)}">${categories.filter(item => item.group === group).map(item => `<option value="${item.id}" data-keywords="${escape(`${item.label} ${item.keywords}`.toLowerCase())}" ${state.tab === item.id ? 'selected' : ''}>${escape(item.label)}${item.id === 'changes' && dirty ? ' · Draft' : ''}</option>`).join('')}</optgroup>`).join('');
    nav.querySelectorAll('[data-sys-tab]').forEach(button => button.addEventListener('click', () => setTab(button.dataset.sysTab)));
    categorySelect.onchange = () => setTab(categorySelect.value);
  };

  const categoryHead = (title, copy, actions = '') => `<div class="sys-section-head"><div><strong>${title}</strong><small>${copy}</small></div><div class="sys-tools">${actions}<span class="sys-dirty ${dirty ? 'changed' : ''}"><i></i>${dirty ? 'Unpublished draft' : 'Synchronized'}</span>${state.tab !== 'changes' ? '<button class="wcp-action-btn primary" type="button" data-sys-review><span class="material-symbols-rounded">difference</span>Review changes</button>' : ''}</div></div>`;
  const card = (title, copy, icon, content, classes = '') => `<section class="sys-card ${classes}"><div class="sys-card-head"><div><strong>${title}</strong><small>${copy}</small></div><span class="material-symbols-rounded">${icon}</span></div>${content}</section>`;
  const field = (label, path, options = {}) => {
    const { type = 'text', unit = '', min = '', max = '', step = '', wide = false, placeholder = '' } = options;
    return `<label class="sys-field ${unit ? 'sys-unit' : ''} ${wide ? 'wide' : ''}"><span><strong>${label}</strong><small>${escape(path)}</small></span><input type="${type}" value="${escape(getPath(state, path))}" ${min !== '' ? `min="${min}"` : ''} ${max !== '' ? `max="${max}"` : ''} ${step !== '' ? `step="${step}"` : ''} ${placeholder ? `placeholder="${escape(placeholder)}"` : ''} data-sys-path="${path}">${unit ? `<b>${unit}</b>` : ''}</label>`;
  };
  const selectField = (label, path, options, wide = false) => `<label class="sys-field ${wide ? 'wide' : ''}"><span><strong>${label}</strong><small>${escape(path)}</small></span><select data-sys-path="${path}">${options.map(option => `<option ${String(getPath(state, path)) === String(option) ? 'selected' : ''}>${escape(option)}</option>`).join('')}</select></label>`;
  const textArea = (label, path, wide = true) => `<label class="sys-field ${wide ? 'wide' : ''}"><span><strong>${label}</strong><small>${escape(path)}</small></span><textarea data-sys-path="${path}">${escape(getPath(state, path))}</textarea></label>`;
  const switchField = (path, title, copy, tone = '') => `<label class="sys-switch ${tone}"><input type="checkbox" data-sys-path="${path}" ${getPath(state, path) ? 'checked' : ''}><span><strong>${title}</strong><small>${copy}</small></span></label>`;
  const note = (title, copy, icon = 'info', tone = '') => `<div class="sys-note ${tone}"><span class="material-symbols-rounded">${icon}</span><div><strong>${title}</strong><small>${copy}</small></div></div>`;
  const grid = (content, columns = 2) => `<div class="sys-field-grid cols-${columns}">${content}</div>`;
  const operationButton = (id, label, icon) => `<button class="wcp-action-btn" type="button" data-sys-op="${id}"><span class="material-symbols-rounded">${icon}</span>${label}</button>`;
  const settingsJump = (tab, label, icon = 'arrow_forward') => `<button class="sys-jump" type="button" data-sys-jump="${tab}"><span class="material-symbols-rounded">${icon}</span>${label}</button>`;
  const consoleLink = (href, label, copy, icon) => `<a class="sys-console-link" href="${href}"><span class="material-symbols-rounded">${icon}</span><span><strong>${label}</strong><small>${copy}</small></span><span class="material-symbols-rounded">arrow_forward</span></a>`;
  const advancedOperations = {
    livepatch: ['Scan & stage live patches', 'live-patching', 'high', 'Kernel and userspace patch scan was added to the ROOT Job Center.'],
    ipam: ['Reconcile IPAM pools and rPTR', 'ipam-reconciliation', 'high', 'IP allocation and reverse-DNS reconciliation was queued.'],
    forensic: ['Capture immutable forensic evidence', 'forensic-capture', 'critical', 'A signed forensic snapshot was added to the ROOT Job Center.'],
    snapshot: ['Create storage consistency snapshot', 'storage-snapshot', 'medium', 'A crash-consistent storage snapshot was queued.'],
    banner: ['Preview targeted notice campaign', 'notice-preview', 'low', 'Banner audience and localization preview was generated.'],
    aiops: ['Simulate AI remediation plan', 'aiops-simulation', 'high', 'AI Ops generated a read-only remediation plan for approval.'],
    edge: ['Purge and verify global edge cache', 'edge-cache-purge', 'high', 'Global cache purge and propagation verification was queued.'],
    green: ['Optimize movable job schedule', 'green-scheduling', 'medium', 'Flexible jobs were rescheduled against renewable-energy forecasts.'],
    otel: ['Test OpenTelemetry pipeline', 'observability-test', 'low', 'Trace, metric and log test signals were sent to the collector.'],
    jit: ['Request 15-minute privileged elevation', 'jit-elevation', 'critical', 'A JIT privilege request was submitted for dual approval.']
  };

  const renderServer = () => {
    view.innerHTML = `${categoryHead('Server identity, time and contacts', 'Canonical host identity used by certificates, DNS, alerts, cluster membership and audit evidence.')}
      <div class="sys-grid">
        ${card('Hostname management', 'Changing the FQDN requires certificate, callback and agent trust rotation.', 'badge', grid(
          field('Control-plane FQDN', 'identity.hostname') + field('Server name', 'identity.serverName') +
          selectField('Server role', 'identity.role', ['Control plane + web node', 'Dedicated control plane', 'Web workload node', 'Database node', 'Mail node', 'DNS node']) +
          field('Asset / inventory tag', 'identity.assetTag') + selectField('Default locale', 'identity.locale', ['English (United States)', 'Français (France)', 'Deutsch (Deutschland)', 'Español (España)'])
        ) + note('Hostname dependency graph', 'AutoSSL, reverse DNS, mail HELO, service callbacks and cluster certificates are checked before publication.', 'account_tree'))}
        ${card('Timezone & NTP synchronization', 'All jobs, certificates and audit events depend on a trustworthy system clock.', 'schedule', grid(
          selectField('System timezone', 'identity.timezone', ['UTC', 'Europe/Paris', 'Europe/London', 'America/New_York', 'Asia/Singapore']) + field('NTP sources', 'identity.ntpServers', { wide: true })
        ) + `<div class="sys-switch-list">${switchField('identity.ntpEnabled', 'Automatic NTP synchronization', 'Monitor offset and block certificate-sensitive jobs if drift exceeds policy.')}</div>`)}
        ${card('ROOT contact information', 'Routing targets for critical infrastructure and security notifications.', 'contact_mail', grid(
          field('ROOT administrator email', 'identity.rootEmail', { type: 'email' }) + field('Security operations email', 'identity.securityEmail', { type: 'email' }) +
          field('Customer support email', 'identity.supportEmail', { type: 'email' }) + field('Emergency contact', 'identity.emergencyContact')
        ) + note('Delivery is continuously verified', 'Critical contacts receive a signed verification message and delivery failures open an incident.', 'mark_email_read'), 'wide')}
        ${card('Default server-generated email routing', 'Explicit recipients for messages emitted by the operating system, control plane and managed services.', 'alternate_email', grid(
          field('Backup & restore alerts', 'identity.backupEmail', { type: 'email' }) + field('Abuse and reputation alerts', 'identity.abuseEmail', { type: 'email' }) +
          field('Postmaster / mail service notices', 'identity.postmasterEmail', { type: 'email' }) + field('Platform sender address', 'branding.senderEmail', { type: 'email' })
        ) + `<div class="sys-route-summary"><div><span>System, service and capacity</span><strong>${escape(state.identity.rootEmail)}</strong></div><div><span>Authentication, firewall and malware</span><strong>${escape(state.identity.securityEmail)}</strong></div><div><span>Backup jobs and restore verification</span><strong>${escape(state.identity.backupEmail)}</strong></div><div><span>Mail queue, reputation and abuse</span><strong>${escape(state.identity.abuseEmail)} · ${escape(state.identity.postmasterEmail)}</strong></div></div><div class="sys-inline-actions"><a class="wcp-action-btn" href="notification-routing.html"><span class="material-symbols-rounded">route</span>Open routing & escalation</a><span>ROOT mail is a core fallback. SMS, chat and incident-provider delivery are optional connectors.</span></div>`, 'wide')}
      </div>`;
  };

  const renderNetwork = () => {
    view.innerHTML = `${categoryHead('Network, addressing and upstream resolution', 'Management exposure, emergency access, IP stack and resolver policy for the WCP360 host.')}
      <div class="sys-grid">
        ${card('Primary addressing', 'Interface and routable addresses used by management and hosted services.', 'lan', grid(
          field('Primary interface', 'network.interface') + field('Primary IPv4', 'network.primaryIpv4') + field('Primary IPv6', 'network.primaryIpv6') +
          field('Default gateway', 'network.gateway') + field('Reverse DNS hostname', 'network.reverseDns') + field('Interface MTU', 'network.mtu', { type: 'number', unit: 'bytes', min: 576, max: 9000 })
        ))}
        ${card('Resolver IPs', 'Ordered upstream resolvers used by platform services and customer workload checks.', 'travel_explore', grid(
          field('Primary resolver', 'network.resolverPrimary') + field('Secondary resolver', 'network.resolverSecondary') + field('Fallback resolver', 'network.resolverFallback'), 1
        ) + note('Split-horizon aware', 'Internal zones are resolved through declared private views; public fallback never receives private names.', 'security'))}
        ${card('Management & emergency routing', 'Separate recovery access from tenant service traffic.', 'route', grid(
          field('Emergency / secondary IP', 'network.emergencyIp') + field('Management allowlist', 'network.managementCidr')
        ) + `<div class="sys-switch-list">${switchField('network.strictManagementAcl', 'Enforce management network ACL', 'Restrict WCP-ADMIN, SSH and cluster ports to declared sources.')}${switchField('network.outboundNat', 'Managed outbound NAT', 'Use the platform egress address for providers and repositories.')}${switchField('network.proxyProtocol', 'Accept PROXY protocol', 'Trust source metadata only from declared load balancers.')}</div>`, 'wide')}
        ${card('Host IP stack', 'Network capability of this server; account provisioning is governed separately.', 'language', `<div class="sys-switch-list">${switchField('network.ipv6', 'Enable host IPv6', 'Configure the host IPv6 stack and service listeners.')}</div><div class="sys-related"><span>Account allocation belongs to Platform behavior.</span>${settingsJump('behavior', 'Open provisioning policy', 'account_tree')}</div>`)}
      </div>`;
  };

  const renderAccess = () => {
    view.innerHTML = `${categoryHead('Privileged access, authentication and sessions', 'ROOT access defaults are minimum controls and cannot be weakened by plans or resellers.', '<button class="wcp-action-btn" type="button" data-sys-rotate-session><span class="material-symbols-rounded">vpn_key</span>Rotate session keys</button>')}
      <div class="sys-grid">
        ${card('SSH hardening', 'Named identities, keys and audited privilege elevation.', 'terminal', grid(
          field('SSH port', 'access.sshPort', { type: 'number', unit: 'TCP', min: 1, max: 65535 }) + field('Trusted management CIDR', 'access.trustedProxy')
        ) + `<div class="sys-switch-list">${switchField('access.sshKeys', 'Require SSH public keys', 'Password-only SSH authentication is rejected.')}${switchField('access.rootSsh', 'Permit direct ROOT SSH', 'Prefer named sudo identities with attributable audit trails.', 'danger')}${switchField('access.rootPassword', 'Permit ROOT password authentication', 'Unsafe for production; publication is blocked while enabled.', 'danger')}${switchField('access.sudoRecording', 'Record SSH and sudo terminal commands', 'Capture terminal commands and metadata. UI and API recording is governed in Zero-Trust.')}</div>`)}
        ${card('WCP-ADMIN sessions', 'Session binding, idle timeout and concurrent access policy.', 'passkey', grid(
          selectField('Cookie IP validation', 'access.cookieValidation', ['Strict', 'Flexible · /24 or /64', 'Disabled']) +
          field('Idle timeout', 'access.sessionMinutes', { type: 'number', unit: 'min', min: 5, max: 1440 }) +
          field('Concurrent ROOT sessions', 'access.concurrentSessions', { type: 'number', unit: 'sessions', min: 1, max: 20 }) +
          field('API token maximum age', 'access.apiTokenDays', { type: 'number', unit: 'days', min: 1, max: 365 })
        ) + `<div class="sys-switch-list">${switchField('access.idleLock', 'Lock idle privileged sessions', 'Require re-authentication before sensitive actions resume.')}</div>`)}
        ${card('Identity provider policy', 'Single source of truth for ROOT MFA, SSO and protected fallback access.', 'verified_user', `<div class="sys-switch-list">${switchField('access.mfaRoot', 'Require phishing-resistant MFA for ROOT', 'WebAuthn/passkeys or documented break-glass recovery; lower scopes cannot weaken this baseline.')}${switchField('access.ssoEnabled', 'Enable external SSO', 'Allow OIDC/SAML identities after explicit role mapping.')}${switchField('access.localFallback', 'Keep protected local fallback', 'Maintain an offline break-glass identity.')}</div>`)}
        ${card('Session trust boundary', 'Cookie theft and proxy header controls.', 'cookie', note('Strict validation is recommended', 'Strict mode binds privileged sessions to the originating IP. Flexible mode tolerates controlled NAT changes.', 'policy') + note('Reverse proxies must be declared', 'Forwarded client addresses are ignored unless the source is trusted.', 'shield'))}
      </div>`;
  };

  const renderResources = () => {
    view.innerHTML = `${categoryHead('Default account resources and quotas', 'Applied when a package omits a value. Effective limits remain traceable through global → plan → account inheritance.')}
      <div class="sys-grid">
        ${card('Compute & process envelope', 'cgroups/LVE-compatible guarantees and concurrency ceilings.', 'memory', grid(
          field('Guaranteed CPU', 'resource.cpu', { type: 'number', unit: 'vCPU', min: 1, max: 64 }) + field('CPU weight', 'resource.cpuWeight', { type: 'number', unit: 'shares', min: 1, max: 10000 }) +
          field('Physical RAM', 'resource.ram', { type: 'number', unit: 'GB', min: 1, max: 512 }) + field('Virtual memory ceiling', 'resource.virtualMemory', { type: 'number', unit: 'GB', min: 1, max: 1024 }) +
          field('Swap ceiling', 'resource.swap', { type: 'number', unit: 'GB', min: 0, max: 512 }) + field('Process limit', 'resource.processes', { type: 'number', unit: 'proc', min: 10, max: 10000 }) +
          field('PID namespace limit', 'resource.pids', { type: 'number', unit: 'PIDs', min: 64, max: 65536 }) + field('Open file descriptors', 'resource.openFiles', { type: 'number', unit: 'files', min: 256, max: 1048576 })
        ))}
        ${card('Storage, I/O & inodes', 'Hard limits are enforced by quota and workload isolation backends.', 'hard_drive', grid(
          field('Disk soft quota', 'resource.diskSoft', { type: 'number', unit: 'GB', min: 1, max: 100000 }) + field('Disk hard quota', 'resource.diskHard', { type: 'number', unit: 'GB', min: 1, max: 100000 }) +
          field('Inode limit', 'resource.inodes', { type: 'number', unit: 'files', min: 1000, max: 100000000 }) + field('I/O throughput', 'resource.io', { type: 'number', unit: 'MB/s', min: 1, max: 10000 }) +
          field('Guaranteed IOPS', 'resource.iops', { type: 'number', unit: 'IOPS', min: 100, max: 1000000 })
        ) + note('Admission control uses physical capacity', 'Guaranteed allocations are rejected when the selected node or storage pool lacks reservable capacity.', 'data_usage'))}
        ${card('Transfer, mail & database defaults', 'Monthly and service-specific limits for new accounts.', 'speed', grid(
          field('Monthly bandwidth', 'resource.bandwidth', { type: 'number', unit: 'GB', min: 1, max: 1000000 }) + field('Outbound mail rate', 'resource.emailsPerHour', { type: 'number', unit: '/hour', min: 1, max: 100000 }) +
          field('Database count', 'resource.databases', { type: 'number', unit: 'DBs', min: 0, max: 10000 }) + field('Database storage', 'resource.dbStorage', { type: 'number', unit: 'GB', min: 1, max: 100000 })
        ))}
        ${card('Enforcement ownership', 'This section defines numeric defaults only; enforcement and exhaustion actions have one authoritative location.', 'rule_settings', `<div class="sys-ownership-list"><div><span class="material-symbols-rounded">tune</span><span><strong>Platform behavior</strong><small>Hard enforcement, overselling and automatic suspension.</small></span>${settingsJump('behavior', 'Open')}</div><div><span class="material-symbols-rounded">storage</span><span><strong>Storage & filesystems</strong><small>Warning, critical and hard-block thresholds for disk and inodes.</small></span>${settingsJump('storage', 'Open')}</div></div>`)}
      </div>`;
  };

  const renderRuntimes = () => {
    view.innerHTML = `${categoryHead('Web server engines and application runtimes', 'Global runtime defaults, protocol capabilities and service ceilings for newly created websites.')}
      <div class="sys-grid">
        ${card('Web server engine', 'Apache, Nginx and LiteSpeed-compatible deployment models.', 'web', grid(
          selectField('Primary web stack', 'runtime.webEngine', ['Nginx + PHP-FPM', 'Apache 2.4 + PHP-FPM', 'Nginx → Apache reverse proxy', 'LiteSpeed Enterprise', 'OpenLiteSpeed']) +
          selectField('Proxy / serving mode', 'runtime.proxyMode', ['Nginx reverse proxy', 'Native event server', 'Apache direct', 'LiteSpeed native']) +
          field('Keep-alive timeout', 'runtime.keepAlive', { type: 'number', unit: 'sec', min: 5, max: 300 }) + field('Maximum connections', 'runtime.maxConnections', { type: 'number', unit: 'conn', min: 128, max: 1000000 }) +
          field('Worker count', 'runtime.workers', { type: 'number', unit: 'auto=0', min: 0, max: 256 })
        ) + `<div class="sys-switch-list">${switchField('runtime.http3', 'Enable HTTP/3 / QUIC', 'Advertise HTTP/3 after UDP and certificate readiness checks.')}${switchField('runtime.brotli', 'Enable Brotli compression', 'Use Brotli for compressible HTTPS responses.')}</div>`)}
        ${card('PHP defaults', 'PHP-FPM pools, Composer and php.ini safety limits.', 'code', grid(
          selectField('Default PHP version', 'runtime.php', ['8.2', '8.3', '8.4']) + selectField('PHP handler', 'runtime.phpHandler', ['PHP-FPM', 'LSAPI', 'FastCGI']) +
          field('memory_limit', 'runtime.phpMemory', { type: 'number', unit: 'MB', min: 64, max: 4096 }) + field('upload_max_filesize', 'runtime.upload', { type: 'number', unit: 'MB', min: 1, max: 10240 }) +
          field('post_max_size', 'runtime.post', { type: 'number', unit: 'MB', min: 1, max: 10240 }) + field('max_execution_time', 'runtime.execution', { type: 'number', unit: 'sec', min: 10, max: 3600 }) +
          field('max_input_vars', 'runtime.inputVars', { type: 'number', unit: 'vars', min: 1000, max: 100000 }) + field('FPM max children', 'runtime.fpmChildren', { type: 'number', unit: 'workers', min: 1, max: 500 }) +
          selectField('Composer toolchain', 'runtime.composer', ['Composer 2.x', 'Composer 2.x · security-only updates', 'Disabled'])
        ) + `<div class="sys-switch-list">${switchField('runtime.phpEnabled', 'Enable managed PHP', 'Expose approved PHP-FPM and CLI versions to plans and accounts.')}</div>`)}
        ${card('Application runtimes', 'Supervised Node.js, Python and Ruby application defaults.', 'deployed_code', grid(
          selectField('Default Node.js', 'runtime.node', ['20 LTS', '22 LTS', '24 Current']) + selectField('Node package manager', 'runtime.nodePackageManager', ['pnpm', 'npm', 'Yarn']) +
          field('V8 memory ceiling', 'runtime.nodeMemory', { type: 'number', unit: 'MB', min: 128, max: 16384 }) +
          field('Managed Node processes', 'runtime.nodeProcesses', { type: 'number', unit: 'proc', min: 1, max: 128 }) + selectField('Default Python', 'runtime.python', ['3.11', '3.12', '3.13']) +
          selectField('Python package manager', 'runtime.pythonPackageManager', ['uv + pip', 'Poetry + pip', 'pip only']) + selectField('Default Ruby', 'runtime.ruby', ['3.2', '3.3', '3.4']) +
          selectField('Ruby package manager', 'runtime.rubyPackageManager', ['Bundler', 'Bundler · locked mode'])
        ) + `<div class="sys-switch-list">${switchField('runtime.nodeEnabled', 'Enable managed Node.js', 'Expose approved Node.js versions and supervised application processes.')}${switchField('runtime.pythonEnabled', 'Enable managed Python', 'Provision isolated virtual environments and approved WSGI / ASGI profiles.')}${switchField('runtime.rubyEnabled', 'Enable managed Ruby', 'Provision Bundler environments and approved Rails process profiles.')}</div>`)}
        ${card('Runtime governance', 'Global feature exposure; operational assignments remain in dedicated consoles.', 'policy', `<div class="sys-switch-list">${switchField('runtime.perAccountRuntime', 'Allow per-account runtime selection', 'Plans define the allowlist; accounts choose only compatible published versions.')}${switchField('runtime.customModules', 'Enable approved runtime modules', 'Expose only extensions published through the runtime module catalog.')}${switchField('runtime.licensedModules', 'Permit licensed modules', 'Require available entitlement before activation on a node or account.')}${switchField('runtime.appInstallers', 'Enable application installers', 'Expose approved one-click applications through isolated jobs.')}</div>`)}
        ${card('Operational ownership', 'Use System Settings for global defaults, then operate versions, modules, stacks and application templates in their authoritative consoles.', 'account_tree', `<div class="sys-ownership-list"><div><span class="material-symbols-rounded">deployed_code</span><span><strong>Language runtimes</strong><small>PHP, Node.js, Python, Ruby, assignments and lifecycle.</small></span><a class="sys-jump" href="language-runtimes.html"><span class="material-symbols-rounded">arrow_forward</span>Open</a></div><div><span class="material-symbols-rounded">extension</span><span><strong>Runtime modules</strong><small>imagick, ionCube, redis, bcmath and native dependencies.</small></span><a class="sys-jump" href="runtime-modules.html"><span class="material-symbols-rounded">arrow_forward</span>Open</a></div><div><span class="material-symbols-rounded">build_circle</span><span><strong>Web stack builder</strong><small>Server profile composition and staged rollout.</small></span><a class="sys-jump" href="web-stack-builder.html"><span class="material-symbols-rounded">arrow_forward</span>Open</a></div><div><span class="material-symbols-rounded">apps</span><span><strong>Application catalog</strong><small>Approved application templates and update policy.</small></span><a class="sys-jump" href="application-catalog.html"><span class="material-symbols-rounded">arrow_forward</span>Open</a></div></div>`, 'wide')}
        ${card('Runtime migration policy', 'Existing applications stay pinned until reviewed.', 'conversion_path', note('Defaults affect new deployments', 'Changing a global runtime never performs an unaudited in-place upgrade.', 'warning', 'warning') + note('Compatibility inventory', 'Extensions, modules and application constraints are checked before migration.', 'fact_check'))}
      </div>`;
  };

  const renderDatabases = () => {
    view.innerHTML = `${categoryHead('Database engines, limits and administration', 'ROOT policy for MySQL/MariaDB, PostgreSQL, remote access and SSO-launched management tools.')}
      <div class="sys-grid">
        ${card('Database engines', 'Global service versions and memory/concurrency ceilings.', 'database', grid(
          selectField('MySQL-compatible engine', 'database.mysqlEngine', ['MariaDB 11.4 LTS', 'MySQL 8.4 LTS', 'Disabled']) + selectField('PostgreSQL engine', 'database.postgresEngine', ['PostgreSQL 16', 'PostgreSQL 17']) +
          field('MySQL max connections', 'database.mysqlConnections', { type: 'number', unit: 'conn', min: 10, max: 100000 }) + field('PostgreSQL max connections', 'database.postgresConnections', { type: 'number', unit: 'conn', min: 10, max: 100000 }) +
          field('InnoDB buffer pool', 'database.bufferPool', { type: 'number', unit: 'MB', min: 128, max: 1048576 }) + field('max_allowed_packet', 'database.maxPacket', { type: 'number', unit: 'MB', min: 4, max: 1024 })
        ) + `<div class="sys-switch-list">${switchField('database.postgresEnabled', 'Enable PostgreSQL service', 'Allow plans to provision PostgreSQL databases.')}${switchField('database.binaryLogs', 'Retain binary / WAL logs', 'Required for point-in-time recovery and replication.')}</div>`)}
        ${card('Account database defaults', 'Limits inherited by hosting plans unless explicitly overridden.', 'table_chart', grid(
          field('Databases per account', 'database.accountDatabases', { type: 'number', unit: 'DBs', min: 0, max: 10000 }) + field('Database users per account', 'database.accountUsers', { type: 'number', unit: 'users', min: 0, max: 10000 }) +
          field('Maximum database size', 'database.maxDatabaseSize', { type: 'number', unit: 'GB', min: 1, max: 100000 }) + field('Slow query threshold', 'database.slowQuerySeconds', { type: 'number', unit: 'sec', min: 1, max: 300 })
        ) + `<div class="sys-switch-list">${switchField('database.slowQueryLog', 'Enable slow query logging', 'Send sampled slow-query metadata to observability.')}</div>`)}
        ${card('Remote access policy', 'Database listeners remain private unless an explicit allowlist applies.', 'vpn_lock', grid(
          selectField('Remote access', 'database.remoteAccess', ['Disabled', 'Explicit allowlist', 'Private network only']) + field('Allowed source CIDRs', 'database.remoteCidr')
        ) + note('Per-database grants still apply', 'Network admission never replaces authentication, grants or TLS verification.', 'shield'))}
        ${card('Administration tools via SSO', 'ROOT activates tools; users launch only those allowed by effective capabilities.', 'open_in_new', `<div class="sys-switch-list">${switchField('database.phpMyAdmin', 'phpMyAdmin', 'MySQL/MariaDB administration through short-lived external SSO.')}${switchField('database.pgAdmin', 'pgAdmin', 'PostgreSQL administration through short-lived external SSO.')}${switchField('database.adminer', 'Adminer', 'Optional lightweight multi-engine administration tool.')}${switchField('database.externalSso', 'Require external-tool SSO', 'Never expose database credentials in URLs or browser storage.')}</div>${note('Launch policy', 'Signed one-time tokens expire after 60 seconds and bind account, engine and source session.', 'key')}`)}
      </div>`;
  };

  const renderMail = () => {
    view.innerHTML = `${categoryHead('Mail transport, mailbox and anti-abuse policy', 'Global SMTP/IMAP configuration, relay routing, rate limits and domain authentication.')}
      <div class="sys-grid">
        ${card('Mail server stack', 'Transport and mailbox services used by managed accounts.', 'mail', grid(
          selectField('Mail transfer agent', 'mail.mta', ['Exim', 'Postfix']) + selectField('Mailbox server', 'mail.mailboxServer', ['Dovecot', 'Courier IMAP']) +
          selectField('Spam filtering engine', 'mail.spamEngine', ['Rspamd', 'SpamAssassin']) + field('Spam rejection threshold', 'mail.spamThreshold', { type: 'number', unit: 'score', min: 1, max: 30, step: 0.1 }) +
          field('Queue retention', 'mail.queueHours', { type: 'number', unit: 'hours', min: 1, max: 720 }) + field('Maximum message size', 'mail.maxMessage', { type: 'number', unit: 'MB', min: 1, max: 1024 })
        ))}
        ${card('External SMTP smart host', 'Authenticated relay to protect deliverability and centralize reputation.', 'outbox', grid(
          field('Relay hostname', 'mail.smtpHost') + field('Relay TLS port', 'mail.smtpPort', { type: 'number', unit: 'TCP', min: 1, max: 65535 }) +
          selectField('TLS policy', 'mail.smtpTls', ['TLS verification required', 'Opportunistic TLS', 'mTLS required']) + field('Service identity', 'mail.smtpIdentity')
        ) + `<div class="sys-switch-list">${switchField('mail.smtpRelay', 'Route outbound mail through smart host', 'Direct delivery requires an explicitly approved route.')}</div>`)}
        ${card('Outbound rate limits', 'Protect IP reputation and contain compromised applications.', 'speed', grid(
          field('Per-domain hourly limit', 'mail.domainHourly', { type: 'number', unit: 'messages', min: 1, max: 1000000 }) + field('Per-domain daily limit', 'mail.domainDaily', { type: 'number', unit: 'messages', min: 1, max: 10000000 }) +
          field('Per-account hourly limit', 'mail.accountHourly', { type: 'number', unit: 'messages', min: 1, max: 1000000 }) + field('Recipients per message', 'mail.maxRecipients', { type: 'number', unit: 'recipients', min: 1, max: 10000 })
        ) + `<div class="sys-switch-list">${switchField('mail.outboundMonitoring', 'Detect anomalous sending', 'Throttle and open an incident when volume or bounce signals exceed baseline.')}</div>`)}
        ${card('Mailbox protocols & domain trust', 'Secure mailbox access and automatic DNS authentication.', 'alternate_email', `<div class="sys-switch-list">${switchField('mail.imap', 'Enable IMAP over TLS', 'Expose secure mailbox access through the managed endpoint.')}${switchField('mail.pop3', 'Enable POP3 over TLS', 'Disabled by default for modern mailbox workflows.')}${switchField('mail.dkim', 'Sign outbound mail with DKIM', 'Generate and rotate domain-specific signing keys.')}${switchField('mail.spf', 'Manage SPF records', 'Publish authorized relay and server sources.')}${switchField('mail.dmarc', 'Manage DMARC policy', 'Support staged enforcement and reporting.')}${switchField('mail.catchAll', 'Allow catch-all mailboxes', 'Increases abuse and backscatter risk.', 'danger')}</div>`)}
      </div>`;
  };

  const providerCard = item => `<article class="sys-provider" data-sys-provider="${escape(item.id)}"><div class="sys-provider-head"><span class="material-symbols-rounded">${item.icon || (item.type === 'DNS' ? 'dns' : item.type === 'Mail' ? 'mail' : 'backup')}</span><div><strong>${escape(item.name)}</strong><small>${escape(item.endpoint)}</small></div>${status(item.enabled ? item.status : 'Disabled', item.enabled ? '' : 'neutral')}</div><div class="sys-provider-meta"><span>Location <b>${escape(item.region)}</b></span><span>Authentication <b>${escape(item.auth)}</b></span><span>Role <b>${item.primary ? 'Primary' : 'Secondary'}</b></span></div><div class="sys-provider-actions"><button type="button" data-sys-edit-provider="${escape(item.id)}">Configure</button><button type="button" data-sys-test-provider="${escape(item.id)}">Test</button><button type="button" data-sys-remove-provider="${escape(item.id)}">Remove</button></div></article>`;

  const renderProviders = () => {
    const groups = ['DNS', 'Mail', 'Backup'];
    view.innerHTML = `${categoryHead('DNS cluster and infrastructure providers', 'Authoritative nameservers, external endpoints, health checks and protected credentials.', '<button class="wcp-action-btn" type="button" data-sys-test-all><span class="material-symbols-rounded">health_and_safety</span>Test all</button><button class="wcp-action-btn" type="button" data-sys-add-provider><span class="material-symbols-rounded">add</span>Add provider</button>')}
      <div class="sys-grid">
        ${card('DNS clustering', 'Synchronize zones across remote authoritative nameservers.', 'hub', grid(
          selectField('DNS operating mode', 'dns.mode', ['External API + hidden primary', 'Integrated authoritative cluster', 'External primary', 'Manual zones']) +
          field('Primary nameserver', 'dns.primaryNs') + field('Secondary nameserver', 'dns.secondaryNs') + field('Tertiary nameserver', 'dns.tertiaryNs') +
          field('Zone health interval', 'dns.healthSeconds', { type: 'number', unit: 'sec', min: 15, max: 3600 })
        ) + `<div class="sys-switch-list">${switchField('dns.clusterEnabled', 'Enable DNS clustering', 'Publish and reconcile zones through healthy cluster members.')}${switchField('dns.notify', 'Send NOTIFY after zone changes', 'Prompt authorized secondaries to transfer the new serial.')}${switchField('dns.recursion', 'Permit public recursion', 'Must remain disabled on authoritative nameservers.', 'danger')}</div>`)}
        ${card('Zone defaults & DNSSEC', 'Applied to newly created forward and reverse zones.', 'domain', grid(
          field('Default TTL', 'dns.defaultTtl', { type: 'number', unit: 'sec', min: 60, max: 86400 }) + field('Negative cache TTL', 'dns.negativeTtl', { type: 'number', unit: 'sec', min: 30, max: 86400 }) +
          selectField('SOA serial format', 'dns.serialFormat', ['Unix timestamp', 'YYYYMMDDnn', 'Monotonic integer']) + field('Zone transfer allowlist', 'dns.transferAcl', { wide: true })
        ) + `<div class="sys-switch-list">${switchField('dns.dnssec', 'Enable DNSSEC automation', 'Generate, publish and rotate signing keys.')}${switchField('dns.glueValidation', 'Validate nameserver glue', 'Block activation when parent glue disagrees.')}${switchField('dns.reverseZones', 'Manage reverse DNS zones', 'Create PTR records where delegated space permits it.')}</div>`)}
        <section class="sys-card wide sys-provider-section"><div class="sys-card-head"><div><strong>Provider registry</strong><small>DNS APIs, mail gateways and backup destinations. Secrets remain write-only.</small></div><span class="material-symbols-rounded">account_tree</span></div>
          ${groups.map(type => { const items = state.providers.filter(item => item.type === type); return `<div class="sys-provider-group"><div class="sys-provider-title"><strong>${type} providers</strong><span>${items.filter(item => item.enabled).length} enabled · ${items.filter(item => item.primary).length} primary</span></div><div class="sys-provider-grid">${items.map(providerCard).join('')}</div></div>`; }).join('')}
        </section>
      </div>`;
  };

  const renderBackups = () => {
    view.innerHTML = `${categoryHead('Backup, retention and disaster recovery', 'Encrypted, bandwidth-aware backups with immutable copies and verified restore paths.', '<button class="wcp-action-btn" type="button" data-sys-backup-drill><span class="material-symbols-rounded">restore</span>Run restore drill</button>')}
      <div class="sys-grid">
        ${card('Backup execution policy', 'Archive strategy, compression and resource throttling.', 'backup', grid(
          selectField('Backup strategy', 'backup.strategy', ['Incremental forever + synthetic full', 'Daily incremental + weekly full', 'Full snapshots only']) +
          selectField('Compression', 'backup.compression', ['Zstandard', 'gzip', 'No compression']) +
          field('Compression level', 'backup.compressionLevel', { type: 'number', unit: 'level', min: 1, max: 19 }) + field('Bandwidth ceiling', 'backup.bandwidth', { type: 'number', unit: 'MB/s', min: 1, max: 10000 }) +
          field('Backup window', 'backup.window') + field('Parallel jobs', 'backup.parallelJobs', { type: 'number', unit: 'jobs', min: 1, max: 64 }) +
          selectField('Encryption', 'backup.encryption', ['AES-256 + KMS', 'AES-256 local key', 'Provider-managed + client-side']) +
          selectField('Automated restore test', 'backup.restoreTest', ['Daily', 'Weekly', 'Monthly', 'Disabled'])
        ) + `<div class="sys-switch-list">${switchField('backup.pauseOnLoad', 'Pause on critical server load', 'Defer non-urgent work when service latency exceeds policy.')}${switchField('backup.incremental', 'Enable block-level incremental backups', 'Transfer only changed data after the protected baseline.')}</div>`)}
        ${card('Retention schedules', 'Generational restore points retained after successful verification.', 'history', `<div class="sys-retention-visual"><span>Daily<b>${state.retention.daily}</b>copies</span><span>Weekly<b>${state.retention.weekly}</b>copies</span><span>Monthly<b>${state.retention.monthly}</b>copies</span><span>Yearly<b>${state.retention.yearly}</b>copies</span></div>${grid(
          field('Daily copies', 'retention.daily', { type: 'number', unit: 'copies', min: 1, max: 365 }) + field('Weekly copies', 'retention.weekly', { type: 'number', unit: 'copies', min: 0, max: 104 }) +
          field('Monthly copies', 'retention.monthly', { type: 'number', unit: 'copies', min: 0, max: 120 }) + field('Yearly copies', 'retention.yearly', { type: 'number', unit: 'copies', min: 0, max: 20 })
        )}`)}
        ${card('Protected data scope', 'Dataset selection for account and platform recovery.', 'inventory_2', `<div class="sys-switch-list">${switchField('backup.includeSystem', 'System configuration', 'Control-plane configuration and encrypted secret references.')}${switchField('backup.includeHome', 'Account home directories', 'Websites, application data and user content.')}${switchField('backup.includeDatabases', 'Managed databases', 'Engine-native dumps plus point-in-time logs.')}${switchField('backup.includeMail', 'Mailboxes and mail configuration', 'Messages, indexes, filters, aliases and metadata.')}</div>`)}
        ${card('Recovery integrity', 'Controls that make a backup eligible for production recovery.', 'health_and_safety', `<div class="sys-switch-list">${switchField('backup.immutable', 'Require an immutable copy', 'Object lock or append-only remote storage protects against compromise.')}${switchField('backup.verification', 'Verify every completed backup', 'Validate manifests, checksums, encryption and database consistency.')}</div>${note('Last restore drill passed', 'Sample account, database, mailbox and DNS data restored in isolation 2 days ago.', 'check_circle')}`)}
        ${card('Operational data retention', 'Audit, logs and recoverable deletion windows.', 'manage_history', grid(
          field('Audit events', 'retention.audit', { type: 'number', unit: 'days', min: 30, max: 3650 }) + field('Service logs', 'retention.logs', { type: 'number', unit: 'days', min: 1, max: 365 }) +
          field('Job history', 'retention.jobs', { type: 'number', unit: 'days', min: 7, max: 730 }) + field('Support bundles', 'retention.supportBundles', { type: 'number', unit: 'days', min: 1, max: 90 }) +
          field('File trash', 'retention.trash', { type: 'number', unit: 'days', min: 1, max: 365 })
        ), 'wide')}
      </div>`;
  };

  const renderSecurity = () => {
    view.innerHTML = `${categoryHead('Security baseline, TLS and application firewall', 'Platform-wide minimums for identities, transport encryption, intrusion prevention and workload protection.')}
      <div class="sys-grid">
        ${card('Password & identity policy', 'Modern hashing and quality controls for local identities.', 'password', grid(
          field('Minimum password length', 'security.passwordMin', { type: 'number', unit: 'chars', min: 12, max: 128 }) + field('Maximum password age', 'security.passwordExpiry', { type: 'number', unit: 'days', min: 0, max: 365 }) +
          field('Password history', 'security.passwordHistory', { type: 'number', unit: 'hashes', min: 0, max: 50 }) + selectField('Password hashing', 'security.passwordHash', ['Argon2id', 'yescrypt', 'SHA-512 crypt'])
        ) + `<div class="sys-switch-list">${switchField('security.passwordBreached', 'Block breached passwords', 'Check candidates against the offline breach corpus.')}${switchField('security.passwordComplexity', 'Require passphrase quality', 'Reject weak, contextual and repeated patterns.')}</div><div class="sys-related"><span>ROOT MFA and cookie binding are owned by Access & sessions.</span>${settingsJump('access', 'Open access policy', 'passkey')}</div>`)}
        ${card('Brute-force protection', 'Service-specific thresholds enforced by firewall and identity controls.', 'policy', grid(
          field('SSH failed attempts', 'security.sshAttempts', { type: 'number', unit: 'tries', min: 2, max: 100 }) + field('FTP failed attempts', 'security.ftpAttempts', { type: 'number', unit: 'tries', min: 2, max: 100 }) +
          field('SMTP failed attempts', 'security.smtpAttempts', { type: 'number', unit: 'tries', min: 2, max: 100 }) + field('WCP-ADMIN attempts', 'security.adminAttempts', { type: 'number', unit: 'tries', min: 2, max: 100 }) +
          field('Observation window', 'security.loginWindow', { type: 'number', unit: 'min', min: 1, max: 1440 }) + field('Initial ban duration', 'security.banMinutes', { type: 'number', unit: 'min', min: 1, max: 10080 }) +
          field('Administrative allowlist', 'security.allowlist', { wide: true })
        ) + `<div class="sys-switch-list">${switchField('security.progressiveBan', 'Progressive bans', 'Increase duration for repeated source or identity violations.')}</div>`)}
        ${card('SSL/TLS policy', 'Obsolete protocols are unavailable; modern suites are centrally controlled.', 'lock', grid(
          selectField('Minimum TLS version', 'security.tlsMinimum', ['TLS 1.3', 'TLS 1.2', 'TLS 1.1', 'TLS 1.0']) + selectField('Cipher profile', 'security.tlsProfile', ['Modern · PCI-DSS', 'Intermediate compatibility', 'Custom']) +
          field('Custom cipher suites', 'security.customCiphers', { wide: true, placeholder: 'Used only with the Custom profile' })
        ) + `<div class="sys-switch-list">${switchField('security.hsts', 'Enable HSTS by default', 'Add Strict-Transport-Security after HTTPS validation.')}${switchField('security.ocsp', 'Enable OCSP stapling', 'Serve fresh certificate status proofs.')}${switchField('security.pciMode', 'Enforce PCI-DSS transport baseline', 'Reject obsolete protocols, ciphers and weak certificates.')}</div><div class="sys-related"><span>Automatic certificate provisioning is owned by Platform behavior.</span>${settingsJump('behavior', 'Open AutoSSL policy', 'verified_user')}</div>`)}
        ${card('WAF, firewall & malware defense', 'Layered host and application protection.', 'security', grid(
          selectField('WAF baseline', 'security.wafLevel', ['Detection only', 'Balanced', 'Strict']) + field('Custom WAF rules', 'security.wafCustomRules', { type: 'number', unit: 'rules', min: 0, max: 10000 }) +
          selectField('Host firewall', 'security.firewall', ['nftables + WCP Guard', 'CSF + iptables', 'External firewall only']) + selectField('Intrusion detection', 'security.ids', ['Fail2ban + behavioral detection', 'Fail2ban', 'External SIEM only'])
        ) + `<div class="sys-switch-list">${switchField('security.wafEnabled', 'Enable global WAF baseline', 'Attach managed rules to new websites.')}${switchField('security.malware', 'Enable malware scanning', 'Scan changed web files and application packages.')}${switchField('security.antivirus', 'Enable mail antivirus', 'Scan messages and attachments before delivery.')}</div><div class="sys-related"><span>FIM paths, rootkit detection and evidence handling are owned by Forensics.</span>${settingsJump('forensics', 'Open forensic policy', 'policy')}</div>`)}
        ${card('Secrets, KMS & certificate authority', 'Global trust boundary for encryption keys, protected values and certificate issuance.', 'key', grid(
          selectField('Secrets vault', 'secrets.vaultProvider', ['WCP Vault + envelope encryption', 'HashiCorp Vault', 'Cloud provider secrets manager', 'External enterprise vault']) +
          selectField('Key management provider', 'secrets.kmsProvider', ['External KMS · EU region', 'Local HSM / PKCS#11', 'Cloud KMS', 'Vault transit engine']) +
          field('Primary key alias', 'secrets.keyAlias') + selectField('Certificate authority policy', 'secrets.certificateAuthority', ['Public ACME + private internal CA', 'Public ACME only', 'Enterprise CA only']) +
          field('ACME directory', 'secrets.acmeDirectory', { type: 'url', wide: true }) + field('Secret rotation interval', 'secrets.rotationDays', { type: 'number', unit: 'days', min: 1, max: 3650 })
        ) + `<div class="sys-switch-list">${switchField('secrets.automaticRotation', 'Enable automatic secret rotation', 'Rotate eligible provider, database and service credentials through audited jobs.')}${switchField('secrets.hsmRequired', 'Require HSM-backed ROOT keys', 'Prevent export of high-value signing and encryption keys.')}${switchField('secrets.secretScanning', 'Scan configuration for exposed secrets', 'Block publication when plaintext credentials or private keys are detected.')}${switchField('secrets.emergencyAccess', 'Maintain dual-control emergency recovery', 'Require two authorized operators to recover protected key material.')}</div>${note('Operational certificates stay in Certificate Management', 'System Settings defines trust policy; issuance, renewal failures and individual certificates remain in the dedicated console.', 'account_tree')}`, 'wide')}
      </div>`;
  };

  const behaviorItem = (icon, title, copy, path) => `<article class="sys-behavior"><span class="material-symbols-rounded">${icon}</span><div><strong>${title}</strong><small>${copy}</small></div><label class="sys-toggle"><input type="checkbox" data-sys-path="${path}" ${getPath(state, path) ? 'checked' : ''}><span></span></label></article>`;
  const renderBehavior = () => {
    view.innerHTML = `${categoryHead('Platform behavior and provisioning guardrails', 'Global automation, isolation, domain ownership and capacity decisions for account lifecycle operations.')}
      <div class="sys-grid">
        ${card('Account provisioning policy', 'Who may create accounts and what must be validated first.', 'person_add', grid(
          selectField('Account creation authority', 'behavior.accountPolicy', ['ROOT only', 'ROOT and authorized resellers', 'Approved API identities']), 1
        ) + `<div class="sys-switch-list">${switchField('behavior.autoAccounts', 'Automatic account creation', 'Allow approved billing/API workflows after admission checks.')}${switchField('behavior.resellerDelegation', 'Permit reseller delegation', 'Resellers create accounts only within capacity.')}${switchField('behavior.domainOwnershipCheck', 'Verify domain ownership', 'Require DNS or account proof before activation.')}${switchField('behavior.hostnameSubdomains', 'Allow customer subdomains under server hostname', 'Keep management identity separate from tenant content.', 'danger')}</div>`)}
        ${card('User isolation', 'Default security boundary for hosting workloads.', 'deployed_code', `<div class="sys-isolation">${['Containers', 'Chroot + systemd', 'Filesystem permissions only'].map(option => `<label class="sys-choice"><input type="radio" name="isolation" value="${option}" data-sys-path="behavior.isolation" ${state.behavior.isolation === option ? 'checked' : ''}><strong>${option}</strong><small>${option === 'Containers' ? 'Namespaces, cgroups and seccomp boundary.' : option === 'Chroot + systemd' ? 'Filesystem jail with supervised services.' : 'Compatibility mode with reduced isolation.'}</small></label>`).join('')}</div><div class="sys-switch-list" style="margin-top:9px">${switchField('behavior.chroot', 'Chroot interactive shells', 'Restrict filesystem visibility for shell users.')}${switchField('behavior.shell', 'Allow shell by default', 'Expose only when the effective plan permits it.', 'danger')}${switchField('behavior.installerSandbox', 'Sandbox application installers', 'Run installers in temporary constrained jobs.')}</div>`)}
        <section class="sys-card wide"><div class="sys-card-head"><div><strong>Global automation switches</strong><small>Capabilities inherited by new hosting plans and accounts.</small></div><span class="material-symbols-rounded">toggle_on</span></div><div class="sys-behavior-grid">
          ${behaviorItem('dns', 'Automatic DNS zones', 'Create and reconcile zones through the selected provider.', 'behavior.autoDns')}
          ${behaviorItem('verified_user', 'Automatic SSL', 'Issue and renew certificates after domain validation.', 'behavior.autoSsl')}
          ${behaviorItem('language', 'IPv6 provisioning', 'Allocate IPv6 where node and plan capacity allow.', 'behavior.ipv6')}
          ${behaviorItem('construction', 'Maintenance mode capability', 'Serve controlled maintenance responses.', 'behavior.maintenance')}
          ${behaviorItem('data_usage', 'Hard quota enforcement', 'Apply disk, inode and workload ceilings.', 'behavior.quotaEnforcement')}
          ${behaviorItem('monitoring', 'Anonymous telemetry', 'Opt-in aggregate metrics without tenant content.', 'behavior.telemetry')}
          ${behaviorItem('stacked_line_chart', 'Capacity overselling', 'Allocate beyond physical capacity with monitoring.', 'behavior.overselling')}
          ${behaviorItem('pause_circle', 'Automatic overage suspension', 'Suspend after grace and notification policy.', 'behavior.autoSuspendOverage')}
        </div></section>
      </div>`;
  };

  const renderBranding = () => {
    view.innerHTML = `${categoryHead('Branding, customer communication and alerts', 'Global identity and notification routes used by WCP-ADMIN and customer messages.', '<button class="wcp-action-btn" type="button" data-sys-test-alert><span class="material-symbols-rounded">send</span>Send test alert</button>')}
      <div class="sys-grid">
        ${card('Interface branding', 'Product identity shown on login, portals and generated pages.', 'palette', grid(
          field('Product name', 'branding.productName') + field('Logo asset URL', 'branding.logoUrl') + field('Primary color', 'branding.primaryColor', { type: 'color' }) +
          field('Accent color', 'branding.accentColor', { type: 'color' }) + selectField('Default theme', 'branding.defaultTheme', ['System preference', 'Light', 'Dark']) +
          field('Support URL', 'branding.supportUrl', { type: 'url' }) + field('Public status URL', 'branding.statusUrl', { type: 'url' }) + textArea('Login security notice', 'branding.loginMessage')
        ))}
        ${card('Live identity preview', 'Representative ROOT header and notification treatment.', 'preview', `<div class="sys-brand-preview" style="--brand-primary:${escape(state.branding.primaryColor)};--brand-accent:${escape(state.branding.accentColor)}"><div><span>360</span><strong>${escape(state.branding.productName)}</strong><b>ROOT</b></div><p>${escape(state.branding.loginMessage)}</p><button type="button">Open control center</button></div>${note('Security context stays platform-controlled', 'ROOT scope, environment and destructive warnings cannot be hidden by branding.', 'verified_user')}`)}
        ${card('Email identity', 'Sender and footer applied to platform-generated messages.', 'forward_to_inbox', grid(
          field('Sender name', 'branding.senderName') + field('Sender email', 'branding.senderEmail', { type: 'email' }) + textArea('Email footer', 'branding.emailFooter')
        ))}
        ${card('Notification routing', 'Choose which operational events leave the platform.', 'notifications_active', grid(
          field('Webhook endpoint', 'branding.webhookUrl', { type: 'url', wide: true })
        ) + `<div class="sys-switch-list">${switchField('branding.criticalEmail', 'Email critical incidents', 'Service, capacity, security and backup failures.')}${switchField('branding.warningEmail', 'Email warning events', 'Degraded providers and approaching quotas.')}${switchField('branding.dailyDigest', 'Send daily operations digest', 'Summarize health, jobs and unresolved warnings.')}${switchField('branding.webhookEnabled', 'Enable signed webhooks', 'Deliver authenticated payloads to the endpoint.')}${switchField('branding.notifyResellers', 'Notify affected resellers', 'Route scoped incidents and maintenance.')}</div>`)}
      </div>`;
  };

  const renderOperations = () => {
    view.innerHTML = `${categoryHead('Operations, updates and platform execution', 'Control-plane lifecycle, evidence export and boundaries with dedicated operational consoles.')}
      <div class="sys-grid">
        ${card('Platform update policy', 'Staged security and feature lifecycle across WCP360 nodes.', 'system_update_alt', grid(
          selectField('Release channel', 'operations.updateChannel', ['Stable', 'Extended support', 'Preview']) + field('Maintenance window', 'operations.maintenanceWindow') +
          selectField('Reboot policy', 'operations.rebootPolicy', ['Approval required', 'Inside maintenance window', 'Never automatic'])
        ) + `<div class="sys-switch-list">${switchField('operations.stagedUpdates', 'Stage updates before production', 'Canary nodes validate health before rollout.')}${switchField('operations.automaticSecurity', 'Automatically stage security fixes', 'Staging remains separate from activation.')}${switchField('operations.automaticFeature', 'Automatically activate feature updates', 'Not recommended for production.', 'danger')}</div>`)}
        ${card('Jobs & control-plane execution', 'Concurrency and timeout limits for privileged operations.', 'pending_actions', grid(
          field('Maximum concurrent jobs', 'operations.maxConcurrentJobs', { type: 'number', unit: 'jobs', min: 1, max: 128 }) + field('Default job timeout', 'operations.jobTimeout', { type: 'number', unit: 'min', min: 5, max: 1440 }) +
          selectField('Platform log level', 'operations.logLevel', ['Error', 'Warning', 'Information', 'Debug']) + field('Control-plane metrics retention', 'operations.metricsRetention', { type: 'number', unit: 'days', min: 7, max: 730 })
        ))}
        ${card('Remote logging & diagnostics', 'Preserve operational evidence outside the managed host.', 'receipt_long', grid(
          field('Remote syslog endpoint', 'operations.syslogEndpoint', { wide: true })
        ) + `<div class="sys-switch-list">${switchField('operations.remoteSyslog', 'Forward logs over TLS', 'Send audit and system events to the remote collector.')}${switchField('operations.crashReports', 'Send crash diagnostics', 'Exclude tenant content and secrets.')}</div><div class="sys-related"><span>Anonymous product telemetry has one owner: Platform behavior.</span>${settingsJump('behavior', 'Open telemetry policy', 'monitoring')}</div>`)}
        ${card('Specialist operational consoles', 'System Settings keeps global defaults and policy; live resources and inventories remain in their dedicated workspaces.', 'account_tree', `<div class="sys-console-links">${consoleLink('updates.html', 'Software lifecycle', 'Packages, plugins, repositories and staged releases.', 'deployed_code_update')}${consoleLink('cluster.html', 'Cluster & high availability', 'Nodes, replication, failover and maintenance.', 'hub')}${consoleLink('network-interfaces.html', 'Network interfaces', 'Live links, routes, ports, bonds and diagnostics.', 'lan')}${consoleLink('storage-volumes.html', 'Storage volumes', 'Pools, volumes, mounts, snapshots and health.', 'storage')}${consoleLink('certificates.html', 'Certificate management', 'Issuance, renewal, inventory and failures.', 'workspace_premium')}${consoleLink('license-management.html', 'License management', 'Trial, grace, entitlements and activation.', 'license')}</div>${note('No duplicated ownership', 'These consoles consume System Settings policy but keep their own operational state, jobs and audit trail.', 'rule_settings')}`, 'wide')}
      </div>`;
  };

  const renderLivePatch = () => {
    view.innerHTML = `${categoryHead('KernelCare and rebootless security patching', 'Continuously patch the Linux kernel and critical userspace libraries without interrupting hosted workloads.', operationButton('livepatch', 'Scan & stage patches', 'system_update_alt'))}
      <div class="sys-insight-grid"><article><span class="material-symbols-rounded">memory</span><div><strong>6.8.0-52 generic</strong><small>Kernel currently running</small></div></article><article><span class="material-symbols-rounded">verified</span><div><strong>0 exposed CVEs</strong><small>Live patch coverage current</small></div></article><article><span class="material-symbols-rounded">timer</span><div><strong>214 days</strong><small>Rebootless uptime</small></div></article><article><span class="material-symbols-rounded">restart_alt</span><div><strong>Not required</strong><small>Next reboot assessment</small></div></article></div>
      <div class="sys-grid">
        ${card('Patch delivery policy', 'Provider, release stream and rollout service-level objective.', 'deployed_code_update', grid(
          selectField('Live patch provider', 'livePatch.provider', ['KernelCare Enterprise', 'Canonical Livepatch', 'kpatch / kGraft managed', 'Disabled']) +
          selectField('Patch stream', 'livePatch.kernelStream', ['Production stable', 'Staged production', 'Extended support']) +
          field('Update check interval', 'livePatch.checkMinutes', { type: 'number', unit: 'min', min: 5, max: 1440 }) +
          field('Critical CVE deployment SLA', 'livePatch.deploymentSla', { type: 'number', unit: 'hours', min: 1, max: 72 }) +
          field('Canary nodes', 'livePatch.canaryNodes', { type: 'number', unit: 'nodes', min: 0, max: 100 })
        ) + `<div class="sys-switch-list">${switchField('livePatch.autoCritical', 'Automatically deploy critical fixes', 'Stage through canary health checks before fleet activation.')}${switchField('livePatch.kernelCveOnly', 'Security patches only', 'Exclude non-security kernel changes from the live stream.')}</div>`)}
        ${card('Live patch scope', 'Kernel and userspace components eligible for rebootless remediation.', 'security_update_good', `<div class="sys-switch-list">${switchField('livePatch.kernelEnabled', 'Linux kernel live patching', 'Apply supported kernel CVE fixes directly in memory.')}${switchField('livePatch.userspaceEnabled', 'Userspace live patching', 'Patch supported processes without full service interruption.')}${switchField('livePatch.openssl', 'OpenSSL process patching', 'Remediate supported cryptographic library CVEs in memory.')}${switchField('livePatch.glibc', 'glibc process patching', 'Update supported C library functions in running processes.')}${switchField('livePatch.zlib', 'zlib process patching', 'Apply supported compression library security fixes.')}</div>`)}
        ${card('Rollback & reboot fallback', 'Safe reversal and planned reboot policy when a fix cannot be applied live.', 'settings_backup_restore', grid(
          field('Maximum reboot deferral', 'livePatch.rebootAfterDays', { type: 'number', unit: 'days', min: 0, max: 365 })
        , 1) + `<div class="sys-switch-list">${switchField('livePatch.rollbackEnabled', 'Keep reversible patch layers', 'Retain signed rollback metadata until the superseding baseline is verified.')}${switchField('livePatch.maintenanceFallback', 'Create maintenance fallback job', 'Generate an approval-gated reboot plan for unsupported fixes.')}</div>${note('Availability guardrail', 'A patch is withdrawn automatically if canary nodes fail service, latency, memory or kernel-integrity checks.', 'health_and_safety')}`)}
        ${card('Coverage & compliance', 'Evidence exported to vulnerability and audit workflows.', 'fact_check', note('Signed patch provenance', 'WCP360 verifies vendor signatures, CVE coverage, kernel symbol compatibility and target process maps.', 'verified') + note('No silent permanent drift', 'Live-patched state is reconciled with the next installed kernel and package baseline.', 'sync'))}
      </div>`;
  };

  const renderIpam = () => {
    view.innerHTML = `${categoryHead('IPAM, reverse DNS and traffic governance', 'Manage address pools, account assignments, PTR automation and network shaping from the ROOT control plane.', operationButton('ipam', 'Reconcile pools & rPTR', 'sync_alt'))}
      <div class="sys-insight-grid"><article><span class="material-symbols-rounded">public</span><div><strong>74 / 254 IPv4</strong><small>29% allocated</small></div></article><article><span class="material-symbols-rounded">language</span><div><strong>186 IPv6</strong><small>/64 assignments active</small></div></article><article><span class="material-symbols-rounded">alternate_email</span><div><strong>100% PTR</strong><small>Forward-confirmed rDNS</small></div></article><article><span class="material-symbols-rounded">speed</span><div><strong>3.8 Gbit/s</strong><small>Current interface throughput</small></div></article></div>
      <div class="sys-grid">
        ${card('Address pool management', 'Dynamic allocation with reservations and explicit ownership history.', 'lan', grid(
          selectField('IPAM authority', 'ipam.authority', ['WCP360 integrated IPAM', 'NetBox external IPAM', 'phpIPAM external', 'Datacenter API']) +
          field('Primary IPv4 pool', 'ipam.ipv4Pool') + field('Primary IPv6 pool', 'ipam.ipv6Pool') +
          selectField('Allocation policy', 'ipam.allocation', ['Dynamic with reservations', 'Static assignments only', 'Plan-driven automatic']) +
          field('Pool gateway', 'ipam.gateway') + field('VLAN identifier', 'ipam.vlan', { type: 'number', unit: 'VLAN', min: 1, max: 4094 }) +
          field('Release quarantine', 'ipam.autoReleaseDays', { type: 'number', unit: 'days', min: 0, max: 365 })
        ) + `<div class="sys-switch-list">${switchField('ipam.dedicatedSsl', 'Allow dedicated SSL addresses', 'Reserve dedicated IPv4/IPv6 assignments where SNI is insufficient.')}${switchField('ipam.anycastEligible', 'Permit Anycast assignments', 'Restrict to edge-capable services and routed prefixes.')}</div>`)}
        ${card('Reverse DNS automation', 'Create and reconcile PTR records through the address provider.', 'quick_reference_all', grid(
          selectField('rPTR provider', 'ipam.ptrProvider', ['Datacenter rDNS API', 'Integrated reverse zones', 'Manual approval workflow']) + field('PTR hostname template', 'ipam.ptrTemplate')
        ) + `<div class="sys-switch-list">${switchField('ipam.autoPtr', 'Automatically manage PTR records', 'Validate forward-confirmed rDNS before activating mail routes.')}${switchField('ipam.abuseNullRoute', 'Permit emergency null-route', 'Contain abusive addresses through a time-limited, audited network job.')}</div>${note('Mail deliverability protection', 'SMTP identities are blocked from activation until HELO, A/AAAA and PTR records agree.', 'mark_email_read')}`)}
        ${card('Traffic shaping', 'Prevent account or service traffic from exhausting datacenter uplinks.', 'network_check', grid(
          field('Interface capacity', 'ipam.interfaceCapacity', { type: 'number', unit: 'Mbit/s', min: 10, max: 1000000 }) +
          field('Default account egress', 'ipam.accountEgress', { type: 'number', unit: 'Mbit/s', min: 1, max: 100000 }) +
          field('Permitted burst', 'ipam.burstPercent', { type: 'number', unit: '%', min: 100, max: 1000 })
        ) + `<div class="sys-switch-list">${switchField('ipam.ingressShaping', 'Shape inbound traffic', 'Apply fair-queue policy before tenant workload saturation.')}</div>`)}
        ${card('Allocation guardrails', 'Every address remains attributable and recoverable.', 'rule', note('Lifecycle-linked assignments', 'Suspension, migration and termination workflows preserve or quarantine addresses according to policy.', 'account_tree') + note('Conflict detection', 'ARP/NDP observation and provider inventory prevent duplicate or externally claimed allocations.', 'radar'))}
      </div>`;
  };

  const renderForensics = () => {
    view.innerHTML = `${categoryHead('Disaster recovery and forensic evidence', 'Preserve immutable audit trails, detect system tampering and quarantine compromised accounts without destroying evidence.', operationButton('forensic', 'Capture forensic snapshot', 'camera'))}
      <div class="sys-insight-grid"><article><span class="material-symbols-rounded">lock</span><div><strong>WORM locked</strong><small>7-year audit retention</small></div></article><article><span class="material-symbols-rounded">fingerprint</span><div><strong>1.42M files</strong><small>FIM baseline verified</small></div></article><article><span class="material-symbols-rounded">bug_report</span><div><strong>0 rootkits</strong><small>Last scan 18 minutes ago</small></div></article><article><span class="material-symbols-rounded">science</span><div><strong>2 sandboxes</strong><small>Active investigations</small></div></article></div>
      <div class="sys-grid">
        ${card('Immutable audit evidence', 'Append-only records resistant to local ROOT compromise.', 'history_edu', grid(
          selectField('WORM destination', 'forensics.wormProvider', ['S3 Object Lock · Compliance', 'Remote immutable syslog vault', 'Offline evidence appliance']) +
          field('Evidence retention', 'forensics.retentionYears', { type: 'number', unit: 'years', min: 1, max: 25 }) +
          selectField('Cryptographic chain', 'forensics.hashChain', ['SHA-512 + Merkle chain', 'SHA-256 + external timestamp'])
        ) + `<div class="sys-switch-list">${switchField('forensics.wormEnabled', 'Enforce WORM audit storage', 'Neither local ROOT nor WCP360 can shorten active retention.')}${switchField('forensics.externalAnchor', 'Anchor hashes externally', 'Publish signed chain roots to a separate trust domain.')}${switchField('forensics.legalHold', 'Support legal holds', 'Freeze selected evidence beyond normal lifecycle policy.')}</div>`)}
        ${card('Rootkit & file-integrity monitoring', 'Continuous cryptographic verification of protected system paths.', 'fingerprint', grid(
          field('Protected paths', 'forensics.fimPaths', { wide: true }) + field('FIM scan interval', 'forensics.fimInterval', { type: 'number', unit: 'min', min: 1, max: 1440 }) +
          selectField('Detection engine', 'forensics.rootkitEngine', ['AIDE + rkhunter + eBPF signals', 'AIDE + chkrootkit', 'External EDR agent'])
        ) + `<div class="sys-switch-list">${switchField('forensics.fimEnabled', 'Enable continuous FIM', 'Compare metadata and content hashes against signed baselines.')}${switchField('forensics.captureNetwork', 'Capture incident network metadata', 'Retain flow and DNS metadata without tenant payload content.')}</div>`)}
        ${card('Account quarantine sandbox', 'Contain compromise while preserving evidence and controlled read-only access.', 'science', grid(
          selectField('Allowed quarantine egress', 'forensics.quarantineEgress', ['DNS + security mirrors only', 'No outbound access', 'Forensic proxy only']) +
          field('Incident evidence retention', 'forensics.evidenceRetention', { type: 'number', unit: 'days', min: 30, max: 3650 })
        ) + `<div class="sys-switch-list">${switchField('forensics.quarantineEnabled', 'Automatic quarantine capability', 'Move compromised workloads to an isolated network and filesystem policy.')}${switchField('forensics.readonlyWebsite', 'Keep website readable', 'Serve a read-only snapshot when safe for investigation continuity.')}${switchField('forensics.preserveMemory', 'Preserve process and memory evidence', 'Capture volatile metadata before workload termination.')}</div>`)}
        ${card('Chain of custody', 'Evidence remains attributable from capture through export.', 'gavel', note('Signed acquisition manifest', 'Every artifact records source, time, operator, hash, tool version and custody transitions.', 'verified') + note('Original evidence stays sealed', 'Analysts work from verified copies; exports are watermarked and audited.', 'inventory_2'))}
      </div>`;
  };

  const renderStorage = () => {
    view.innerHTML = `${categoryHead('Advanced storage and filesystem governance', 'Operate ZFS/Btrfs pools, snapshots, remote mounts and preventive quota thresholds.', operationButton('snapshot', 'Create consistency snapshot', 'camera_alt'))}
      <div class="sys-insight-grid"><article><span class="material-symbols-rounded">storage</span><div><strong>72.4 TB</strong><small>tank-hosting usable</small></div></article><article><span class="material-symbols-rounded">data_saver_on</span><div><strong>1.74×</strong><small>Compression ratio</small></div></article><article><span class="material-symbols-rounded">health_and_safety</span><div><strong>ONLINE</strong><small>RAIDZ2 · 0 errors</small></div></article><article><span class="material-symbols-rounded">photo_camera</span><div><strong>1,248</strong><small>Recoverable snapshots</small></div></article></div>
      <div class="sys-grid">
        ${card('Filesystem pool', 'Advanced checksumming, compression and redundancy policy.', 'storage', grid(
          selectField('Filesystem backend', 'storage.filesystem', ['ZFS', 'Btrfs', 'XFS + LVM', 'ext4 + LVM']) + field('Primary pool', 'storage.pool') +
          selectField('Redundancy profile', 'storage.raid', ['RAIDZ2', 'RAIDZ1', 'Mirrored vdevs', 'Btrfs RAID10', 'External SAN redundancy']) +
          selectField('Transparent compression', 'storage.compression', ['zstd', 'lz4', 'gzip', 'Disabled']) +
          field('Compression level', 'storage.compressionLevel', { type: 'number', unit: 'level', min: 1, max: 19 }) + field('ARC / cache limit', 'storage.arcLimit', { type: 'number', unit: '% RAM', min: 5, max: 80 })
        ) + `<div class="sys-switch-list">${switchField('storage.deduplication', 'Enable block deduplication', 'Requires explicit memory and workload suitability review.', 'danger')}${switchField('storage.thinProvisioning', 'Enable thin provisioning', 'Monitor physical headroom and reserve emergency capacity.')}</div>`)}
        ${card('Snapshots & integrity', 'Instant recovery points and scheduled media verification.', 'camera_alt', grid(
          field('Snapshot schedule', 'storage.snapshotPolicy', { wide: true }) + field('Pool scrub schedule', 'storage.scrubSchedule', { wide: true })
        ) + note('Snapshots are not backups', 'Retention and replication policies ensure critical restore points leave the source pool.', 'warning', 'warning'))}
        ${card('Remote mounts', 'Attach NAS, SAN, NFS and SMB destinations with hardened mount options.', 'folder_shared', grid(
          field('NFS endpoint', 'storage.nfsEndpoint', { wide: true }) + field('SMB/CIFS endpoint', 'storage.cifsEndpoint', { wide: true }) + field('Security mount options', 'storage.mountOptions', { wide: true })
        ) + `<div class="sys-switch-list">${switchField('storage.nfsEnabled', 'Enable managed NFS mount', 'Health-check and fence stale mounts before dependent jobs start.')}${switchField('storage.cifsEnabled', 'Enable managed SMB/CIFS mount', 'Credentials remain in the platform vault and never enter fstab.')}</div>`)}
        ${card('Quota and inode thresholds', 'Warn early, preserve database headroom and enforce hard write limits.', 'data_usage', grid(
          field('Inode warning', 'storage.inodeWarning', { type: 'number', unit: '%', min: 1, max: 99 }) + field('Inode critical', 'storage.inodeCritical', { type: 'number', unit: '%', min: 1, max: 99 }) +
          field('Disk quota warning', 'storage.quotaWarning', { type: 'number', unit: '%', min: 1, max: 99 }) + field('Disk quota critical', 'storage.quotaCritical', { type: 'number', unit: '%', min: 1, max: 99 }) +
          field('Hard write block', 'storage.hardBlock', { type: 'number', unit: '%', min: 90, max: 100 }) + field('Emergency reserve', 'storage.emergencyReserve', { type: 'number', unit: '% pool', min: 1, max: 25 })
        ) + note('Database-aware enforcement', 'Before hard blocking, WCP360 reserves space for transaction logs and clean shutdown metadata.', 'database'))}
      </div>`;
  };

  const renderWhiteLabel = () => {
    view.innerHTML = `${categoryHead('White-label, reseller brands and localization', 'Operate multiple branded portals, language packs and targeted notices from one ROOT governance layer.', operationButton('banner', 'Preview notice campaign', 'campaign'))}
      <div class="sys-grid">
        ${card('Multi-brand tenancy', 'Custom domains and inherited visual identities for reseller portals.', 'format_paint', grid(
          field('Default panel domain', 'whiteLabel.customDomain') + field('Favicon asset', 'whiteLabel.faviconUrl') + textArea('Approved custom CSS variables', 'whiteLabel.customCss')
        ) + `<div class="sys-switch-list">${switchField('whiteLabel.multiBrand', 'Enable multiple brand profiles', 'Assign a validated brand to reseller trees and customer portals.')}${switchField('whiteLabel.resellerThemes', 'Allow reseller theme overrides', 'Restrict overrides to safe tokens, assets and approved templates.')}${switchField('whiteLabel.certificateAutomation', 'Automate custom-domain certificates', 'Validate ownership and issue certificates for branded panel domains.')}</div>`)}
        ${card('Language packs & dictionary', 'Translate and override interface strings without modifying core packages.', 'translate', grid(
          selectField('Default language', 'whiteLabel.defaultLanguage', ['English', 'Français', 'Deutsch', 'Español', 'العربية']) +
          selectField('Fallback language', 'whiteLabel.fallbackLanguage', ['English', 'Français']) + field('Installed language packs', 'whiteLabel.languagePacks', { type: 'number', unit: 'packs', min: 1, max: 250 })
        ) + `<div class="sys-switch-list">${switchField('whiteLabel.dictionaryOverrides', 'Enable dictionary overrides', 'Maintain version-aware custom terminology per brand and locale.')}${switchField('whiteLabel.rtlSupport', 'Enable RTL layouts', 'Validate navigation and components for right-to-left languages.')}</div>${note('Upgrade-safe translations', 'Overrides are diffed against each release and flagged when source strings change.', 'sync')}`)}
        ${card('Notice & banner injector', 'Target maintenance, incident and informational campaigns.', 'campaign', grid(
          selectField('Audience', 'whiteLabel.bannerAudience', ['All customers and resellers', 'Customers only', 'Resellers only', 'Selected plans or regions']) +
          selectField('Severity', 'whiteLabel.bannerSeverity', ['Information', 'Maintenance', 'Warning', 'Critical']) +
          field('Banner title', 'whiteLabel.bannerTitle', { wide: true }) + textArea('Localized source message', 'whiteLabel.bannerMessage') + field('Display window', 'whiteLabel.bannerWindow', { wide: true })
        ) + `<div class="sys-switch-list">${switchField('whiteLabel.bannerEnabled', 'Enable scheduled banner', 'Publish through the normal approval and audit workflow.')}${switchField('whiteLabel.dismissible', 'Allow user dismissal', 'Critical alerts can remain pinned until ROOT resolves them.')}</div>`, 'wide')}
      </div>`;
  };

  const renderAiOps = () => {
    view.innerHTML = `${categoryHead('AI-powered operations and self-healing', 'Analyze operational evidence, predict incidents and execute only governed, reversible remediation.', operationButton('aiops', 'Simulate remediation', 'psychology'))}
      <div class="sys-insight-grid"><article><span class="material-symbols-rounded">neurology</span><div><strong>Local inference</strong><small>No tenant data leaves the host</small></div></article><article><span class="material-symbols-rounded">auto_fix_high</span><div><strong>18 remediations</strong><small>Approved this month</small></div></article><article><span class="material-symbols-rounded">crisis_alert</span><div><strong>3 predictions</strong><small>Incidents prevented</small></div></article><article><span class="material-symbols-rounded">verified_user</span><div><strong>Approval gated</strong><small>Current autonomy mode</small></div></article></div>
      <div class="sys-grid">
        ${card('AI control plane', 'Model location, autonomy and protected-data boundary.', 'psychology', grid(
          selectField('Operations model', 'aiOps.model', ['Local secured operations LLM', 'Private cluster model', 'External approved model endpoint']) +
          selectField('Autonomy policy', 'aiOps.autonomy', ['Recommend only', 'Recommend + approval', 'Auto-execute low risk']) +
          field('Evidence retention', 'aiOps.evidenceRetention', { type: 'number', unit: 'days', min: 7, max: 730 })
        ) + `<div class="sys-switch-list">${switchField('aiOps.enabled', 'Enable AI operations analysis', 'Correlate metrics, logs, topology and recent configuration changes.')}${switchField('aiOps.outboundInference', 'Permit external inference', 'Requires an approved processor, redaction policy and data residency review.', 'danger')}${switchField('aiOps.redactSecrets', 'Redact secrets and tenant content', 'Apply deterministic filtering before any model context is created.')}</div>`)}
        ${card('Self-healing services', 'Bounded and reversible playbooks for common infrastructure failures.', 'auto_fix_high', grid(
          field('Managed services', 'aiOps.managedServices', { wide: true }) + field('Maximum actions per incident', 'aiOps.maxActions', { type: 'number', unit: 'actions', min: 1, max: 20 }) +
          field('Remediation cooldown', 'aiOps.cooldownMinutes', { type: 'number', unit: 'min', min: 1, max: 1440 })
        ) + `<div class="sys-switch-list">${switchField('aiOps.selfHealing', 'Enable governed self-healing', 'Execute signed playbooks only inside the selected autonomy policy.')}</div>${note('No invented shell commands', 'The model selects versioned playbooks with declared preflight, impact and rollback steps.', 'rule')}`)}
        ${card('Anomaly & predictive detection', 'Behavioral baselines for resource, traffic and service health.', 'monitor_heart', grid(
          field('Learning window', 'aiOps.learningWindow', { type: 'number', unit: 'days', min: 7, max: 365 }) +
          field('Minimum confidence', 'aiOps.confidence', { type: 'number', unit: '%', min: 50, max: 100 })
        ) + `<div class="sys-switch-list">${switchField('aiOps.anomalyDetection', 'Enable anomaly detection', 'Detect workload, network and authentication deviations.')}${switchField('aiOps.predictiveAlerts', 'Enable predictive alerts', 'Forecast disk, database, certificate and capacity failures.')}</div>`)}
        ${card('Natural-language ROOT CLI', 'Translate intent into a reviewable execution plan.', 'terminal', `<div class="sys-switch-list">${switchField('aiOps.naturalCli', 'Enable natural-language CLI', 'Generate a structured plan, never an unreviewed direct shell command.')}${switchField('aiOps.cliApproval', 'Require explicit command approval', 'Show resolved targets, exact actions, impact and rollback before execution.')}</div>${note('Example governed intent', '“Block the source attacking SSH for 10 minutes and export the related logs” becomes a firewall job plus signed evidence export.', 'chat')}`)}
      </div>`;
  };

  const renderEdge = () => {
    view.innerHTML = `${categoryHead('Edge computing and CDN governance', 'Control Anycast DNS, global caching, origin protection and signed WebAssembly workloads.', operationButton('edge', 'Purge global cache', 'cached'))}
      <div class="sys-insight-grid"><article><span class="material-symbols-rounded">public</span><div><strong>31 edge regions</strong><small>Global service footprint</small></div></article><article><span class="material-symbols-rounded">speed</span><div><strong>84% hit ratio</strong><small>Last 24 hours</small></div></article><article><span class="material-symbols-rounded">bolt</span><div><strong>42 ms</strong><small>Global median TTFB</small></div></article><article><span class="material-symbols-rounded">deployed_code</span><div><strong>12 Wasm apps</strong><small>Signed deployments</small></div></article></div>
      <div class="sys-grid">
        ${card('Global edge control', 'Provider, service regions and propagation policy.', 'hub', grid(
          selectField('Edge provider', 'edge.provider', ['WCP Edge + Cloudflare', 'WCP managed Varnish mesh', 'External CDN APIs']) + field('Enabled regions', 'edge.edgeRegions', { wide: true }) +
          field('Cache purge propagation SLA', 'edge.purgePropagation', { type: 'number', unit: 'sec', min: 1, max: 300 })
        ) + `<div class="sys-switch-list">${switchField('edge.anycastDns', 'Enable Anycast DNS', 'Advertise authoritative DNS through healthy global points of presence.')}${switchField('edge.ddosIntegration', 'Integrate DDoS mitigation', 'Share approved signals and shift traffic to protected edge capacity.')}</div>`)}
        ${card('Caching & origin protection', 'Safe cache defaults for public content and degraded-origin continuity.', 'cached', grid(
          field('Default cache TTL', 'edge.defaultTtl', { type: 'number', unit: 'sec', min: 0, max: 31536000 }) + field('Serve-stale window', 'edge.staleSeconds', { type: 'number', unit: 'sec', min: 0, max: 604800 })
        ) + `<div class="sys-switch-list">${switchField('edge.edgeCaching', 'Enable edge caching', 'Apply plan and website cache policies at global points of presence.')}${switchField('edge.cacheAuthenticated', 'Cache authenticated responses', 'Requires explicit application-safe cache keys.', 'danger')}${switchField('edge.originShield', 'Enable origin shield', 'Collapse requests through a protected regional tier.')}${switchField('edge.tieredCache', 'Enable tiered caching', 'Reduce repeated origin fetches across nearby edge regions.')}${switchField('edge.imageOptimization', 'Optimize images at the edge', 'Transform supported images within account policy limits.')}</div>`)}
        ${card('WebAssembly edge runtime', 'Signed serverless workloads with strict resource ceilings.', 'deployed_code', grid(
          field('Memory ceiling', 'edge.wasmMemory', { type: 'number', unit: 'MB', min: 16, max: 1024 }) + field('CPU time ceiling', 'edge.wasmCpu', { type: 'number', unit: 'ms', min: 1, max: 1000 }) +
          field('Execution timeout', 'edge.wasmTimeout', { type: 'number', unit: 'sec', min: 1, max: 300 })
        ) + `<div class="sys-switch-list">${switchField('edge.wasmEnabled', 'Enable Wasm edge deployments', 'Expose only approved APIs and account-scoped secrets.')}${switchField('edge.signedDeployments', 'Require signed edge artifacts', 'Reject unsigned or provenance-incomplete bundles.')}</div>`)}
        ${card('Edge trust boundary', 'Origin and tenant isolation remain authoritative.', 'shield', note('No control-plane credentials at edge', 'Edge workloads receive short-lived, least-privilege account capabilities only.', 'key') + note('Deterministic rollback', 'Every deployment retains the previous signed artifact and traffic weights.', 'undo'))}
      </div>`;
  };

  const renderSustainability = () => {
    view.innerHTML = `${categoryHead('Green hosting and carbon-aware operations', 'Measure account-level impact and schedule flexible work against lower-carbon energy availability.', operationButton('green', 'Optimize job schedule', 'eco'))}
      <div class="sys-insight-grid"><article><span class="material-symbols-rounded">electric_bolt</span><div><strong>18.6 kW</strong><small>Current facility power</small></div></article><article><span class="material-symbols-rounded">co2</span><div><strong>1.04 kgCO₂e/h</strong><small>Estimated operational carbon</small></div></article><article><span class="material-symbols-rounded">energy_savings_leaf</span><div><strong>86% renewable</strong><small>Current energy mix</small></div></article><article><span class="material-symbols-rounded">trending_down</span><div><strong>−22%</strong><small>Year-over-year intensity</small></div></article></div>
      <div class="sys-grid">
        ${card('Energy & carbon measurement', 'Calibrate infrastructure power and regional carbon intensity.', 'electric_meter', grid(
          selectField('Power telemetry source', 'sustainability.powerSource', ['Datacenter PDU + cloud telemetry', 'IPMI / Redfish sensors', 'Provider sustainability API']) +
          field('Regional carbon factor', 'sustainability.regionFactor', { type: 'number', unit: 'gCO₂/kWh', min: 0, max: 1500 }) +
          field('Datacenter PUE', 'sustainability.pue', { type: 'number', unit: 'PUE', min: 1, max: 5, step: 0.01 }) + field('Annual reduction target', 'sustainability.annualTarget', { type: 'number', unit: '%', min: 0, max: 100 })
        ) + `<div class="sys-switch-list">${switchField('sustainability.enabled', 'Enable sustainability accounting', 'Calculate energy and carbon estimates with declared methodology.')}${switchField('sustainability.realtimePower', 'Collect real-time power telemetry', 'Prefer measured data over static server estimates.')}</div>`)}
        ${card('Account footprint attribution', 'Estimate customer impact from reserved and consumed resources.', 'pie_chart', `<div class="sys-switch-list">${switchField('sustainability.accountAttribution', 'Attribute carbon to accounts', 'Use CPU time, memory, storage, network and shared-service allocation.')}${switchField('sustainability.carbonReports', 'Generate customer carbon reports', 'Expose methodology, confidence and energy mix with each report.')}${switchField('sustainability.customerBadges', 'Offer verified green-hosting badges', 'Publish only when evidence and renewable thresholds are satisfied.')}${switchField('sustainability.offsetDisclosure', 'Disclose offsets separately', 'Never represent offsets as direct operational emission reductions.')}</div>`)}
        ${card('Green energy routing', 'Shift flexible work without violating operational service levels.', 'energy_savings_leaf', grid(
          field('Renewable threshold', 'sustainability.renewableThreshold', { type: 'number', unit: '%', min: 0, max: 100 }) + field('Preferred execution window', 'sustainability.flexibleWindow') +
          field('Maximum job delay', 'sustainability.maxJobDelay', { type: 'number', unit: 'hours', min: 0, max: 72 }) + field('Movable workload classes', 'sustainability.movableJobs', { wide: true })
        ) + `<div class="sys-switch-list">${switchField('sustainability.greenRouting', 'Enable carbon-aware scheduling', 'Move eligible jobs across time or approved regions when cleaner energy is forecast.')}</div>`)}
        ${card('Operational guardrail', 'Availability and recovery objectives always take precedence.', 'health_and_safety', note('Never delay critical protection', 'Security patches, recovery, active incident response and customer restore jobs are never deferred for carbon optimization.', 'verified_user'))}
      </div>`;
  };

  const renderObservability = () => {
    view.innerHTML = `${categoryHead('Advanced observability and application performance', 'Collect isolated traces, metrics, logs and profiles through open standards.', operationButton('otel', 'Test telemetry pipeline', 'monitoring'))}
      <div class="sys-insight-grid"><article><span class="material-symbols-rounded">timeline</span><div><strong>2.8M spans/min</strong><small>Trace ingestion</small></div></article><article><span class="material-symbols-rounded">query_stats</span><div><strong>184 slow queries</strong><small>Last 24 hours</small></div></article><article><span class="material-symbols-rounded">speed</span><div><strong>1.3% overhead</strong><small>Measured APM cost</small></div></article><article><span class="material-symbols-rounded">check_circle</span><div><strong>Healthy</strong><small>OTel collector pipeline</small></div></article></div>
      <div class="sys-grid">
        ${card('OpenTelemetry pipeline', 'Standards-based export with tenant-aware resource attributes.', 'monitoring', grid(
          field('Collector endpoint', 'observability.collector', { type: 'url', wide: true }) + selectField('OTLP protocol', 'observability.protocol', ['OTLP/HTTP + TLS', 'OTLP/gRPC + TLS']) +
          field('Trace sampling', 'observability.traceSampling', { type: 'number', unit: '%', min: 0, max: 100 }) + field('Metrics interval', 'observability.metricsInterval', { type: 'number', unit: 'sec', min: 5, max: 300 }) +
          field('Telemetry retention', 'observability.telemetryRetention', { type: 'number', unit: 'days', min: 1, max: 365 })
        ) + `<div class="sys-switch-list">${switchField('observability.otelEnabled', 'Enable OpenTelemetry', 'Inject approved resource identity and export through the platform collector.')}${switchField('observability.traceExport', 'Export application traces', 'Respect per-plan sampling and tenant isolation.')}${switchField('observability.metricsExport', 'Export application metrics', 'Apply cardinality and label safety limits.')}${switchField('observability.logExport', 'Export structured logs', 'Redact secrets and sensitive request attributes.')}</div>`)}
        ${card('APM & code profiling', 'Find slow code paths and database queries without application plugins.', 'query_stats', grid(
          selectField('Profiling policy', 'observability.codeProfiling', ['On-demand + anomaly trigger', 'Continuous low-overhead', 'On-demand only']) +
          field('Slow query threshold', 'observability.slowQueryMs', { type: 'number', unit: 'ms', min: 10, max: 60000 }) + field('Maximum APM overhead', 'observability.apmOverhead', { type: 'number', unit: '%', min: 0.1, max: 10, step: 0.1 })
        ) + `<div class="sys-switch-list">${switchField('observability.profiling', 'Enable code profiling', 'Capture CPU and wall-time profiles within declared overhead budgets.')}${switchField('observability.slowQuery', 'Track slow database queries', 'Correlate normalized queries with website traces and engine metrics.')}</div>`)}
        ${card('External observability integrations', 'Control supported downstream analysis platforms.', 'hub', `<div class="sys-switch-list">${switchField('observability.prometheus', 'Prometheus-compatible metrics', 'Expose authenticated, account-isolated scrape endpoints.')}${switchField('observability.grafana', 'Grafana dashboards', 'Provision curated dashboards and data-source permissions.')}${switchField('observability.datadog', 'Datadog export', 'Forward through an approved regional integration.')}</div>`)}
        ${card('Telemetry trust boundary', 'Prevent cross-tenant data disclosure.', 'shield', `<div class="sys-switch-list">${switchField('observability.tenantIsolation', 'Enforce tenant telemetry isolation', 'Bind every signal to an immutable account and service identity.')}</div>${note('Sensitive values are filtered', 'Authorization headers, cookies, passwords, query literals and configured personal-data fields are redacted before export.', 'privacy_tip')}`)}
      </div>`;
  };

  const renderZeroTrust = () => {
    view.innerHTML = `${categoryHead('Zero-Trust IAM and just-in-time privilege', 'Record privileged activity and grant narrowly scoped, expiring access only after policy evaluation.', operationButton('jit', 'Request JIT elevation', 'approval'))}
      <div class="sys-insight-grid"><article><span class="material-symbols-rounded">badge</span><div><strong>0 standing ROOT</strong><small>Named break-glass excluded</small></div></article><article><span class="material-symbols-rounded">timer</span><div><strong>15 minutes</strong><small>Default elevation</small></div></article><article><span class="material-symbols-rounded">videocam</span><div><strong>100% recorded</strong><small>Privileged sessions</small></div></article><article><span class="material-symbols-rounded">approval</span><div><strong>Dual approval</strong><small>Production elevation</small></div></article></div>
      <div class="sys-grid">
        ${card('Privileged session recording', 'Tamper-evident evidence for support and administrator activity.', 'videocam', grid(
          selectField('Recording mode', 'zeroTrust.recordingMode', ['Terminal text + privileged UI events', 'Terminal video + UI events', 'Commands and API calls only']) +
          field('Recording retention', 'zeroTrust.recordingRetention', { type: 'number', unit: 'days', min: 30, max: 3650 })
        ) + `<div class="sys-switch-list">${switchField('zeroTrust.sessionRecording', 'Record privileged sessions', 'Capture terminal, UI mutation and API execution evidence.')}${switchField('zeroTrust.recordingWorm', 'Store recordings in WORM storage', 'Protect session evidence from local administrator deletion.')}</div>`)}
        ${card('Just-in-time elevation', 'Time-bound roles with approval, MFA and automatic revocation.', 'approval', grid(
          field('Default elevation duration', 'zeroTrust.defaultDuration', { type: 'number', unit: 'min', min: 5, max: 240 }) + field('Maximum elevation duration', 'zeroTrust.maxDuration', { type: 'number', unit: 'min', min: 5, max: 480 }) +
          selectField('Maximum accepted risk', 'zeroTrust.riskThreshold', ['Low only', 'Low or medium', 'Policy-dependent']) + field('Inactive revocation', 'zeroTrust.inactiveRevoke', { type: 'number', unit: 'min', min: 1, max: 60 })
        ) + `<div class="sys-switch-list">${switchField('zeroTrust.jitEnabled', 'Enable JIT privilege elevation', 'No standing privileged role outside protected break-glass identities.')}${switchField('zeroTrust.approvalRequired', 'Require approval', 'A separate authorized operator validates scope and justification.')}${switchField('zeroTrust.dualApproval', 'Require dual approval for production', 'Two independent approvers authorize critical roles.')}${switchField('zeroTrust.stepUpMfa', 'Require step-up MFA', 'Re-authenticate with phishing-resistant MFA before activation.')}</div>`)}
        ${card('Zero-Trust context evaluation', 'Continuously evaluate device, network and session risk.', 'policy', `<div class="sys-switch-list">${switchField('zeroTrust.devicePosture', 'Evaluate device posture', 'Require supported OS, encryption, endpoint health and screen lock.')}${switchField('zeroTrust.managedDevice', 'Require managed devices for ROOT', 'Restrict privileged access to enrolled administrative endpoints.')}${switchField('zeroTrust.networkContext', 'Evaluate network context', 'Score source network, geo-velocity, proxy and threat intelligence.')}</div>`)}
        ${card('Privileged action restrictions', 'Limit commands, clipboard and file movement during elevation.', 'block', grid(
          selectField('Command execution policy', 'zeroTrust.commandPolicy', ['Allowlist + approval', 'Role-based allowlist', 'Observe only'])
        , 1) + `<div class="sys-switch-list">${switchField('zeroTrust.clipboardRestricted', 'Restrict clipboard transfer', 'Block secret and bulk-data movement in privileged sessions.')}${switchField('zeroTrust.fileTransferRestricted', 'Restrict file transfer', 'Require approved evidence or deployment workflows.')}${switchField('zeroTrust.breakGlassApproval', 'Audit break-glass activation', 'Trigger incident notification and mandatory post-access review.')}</div>`)}
      </div>`;
  };

  const flatten = (object, prefix = '', output = {}) => {
    Object.entries(object || {}).forEach(([key, value]) => {
      const path = prefix ? `${prefix}.${key}` : key;
      if (['tab', 'version', 'versions'].includes(path)) return;
      if (path === 'providers') { output['providers.configured'] = `${value.length} providers · ${value.filter(item => item.enabled).length} enabled`; return; }
      if (value && typeof value === 'object' && !Array.isArray(value)) flatten(value, path, output);
      else output[path] = value;
    });
    return output;
  };
  const humanize = path => path.split('.').pop().replace(/([A-Z])/g, ' $1').replace(/^./, value => value.toUpperCase());
  const diffEntries = () => {
    const before = flatten(baseline);
    const after = flatten(state);
    return [...new Set([...Object.keys(before), ...Object.keys(after)])].filter(path => String(before[path]) !== String(after[path])).map(path => ({ path, label: humanize(path), before: before[path], after: after[path] }));
  };

  const renderChanges = () => {
    const diffs = diffEntries();
    const issues = validations();
    const critical = issues.filter(item => item.severity === 'critical').length;
    view.innerHTML = `${categoryHead('Configuration validation and change control', 'Review differences, resolve blockers, publish an immutable revision or restore a previous version.', `<button class="wcp-action-btn primary" type="button" data-sys-publish ${!dirty || critical ? 'disabled' : ''}><span class="material-symbols-rounded">deployed_code_update</span>Publish configuration</button>`)}
      <div class="sys-change-summary"><article><span class="material-symbols-rounded">difference</span><div><strong>${diffs.length} effective changes</strong><small>${dirty ? 'Pending publication' : 'No unpublished draft'}</small></div></article><article class="${critical ? 'danger' : ''}"><span class="material-symbols-rounded">${critical ? 'block' : 'verified_user'}</span><div><strong>${critical ? `${critical} publication blockers` : 'Schema validation passed'}</strong><small>${issues.length - critical} advisory findings</small></div></article><article><span class="material-symbols-rounded">approval</span><div><strong>Production approval</strong><small>High-risk changes require an authorized reviewer</small></div></article></div>
      <div class="sys-change-grid">
        <section class="sys-card" style="padding:0"><div class="sys-card-head inset"><div><strong>Pending effective diff</strong><small>Secrets are represented only by protected references.</small></div>${status(dirty ? 'Pending review' : 'Synchronized')}</div><div class="sys-diff">${diffs.length ? diffs.slice(0, 20).map(item => `<div class="sys-diff-row"><div><strong>${escape(item.label)}</strong><small>${escape(item.path)}</small></div><code>${escape(item.before)}</code><span class="material-symbols-rounded">arrow_forward</span><code>${escape(item.after)}</code></div>`).join('') : '<div class="sys-empty">No unpublished global configuration changes.</div>'}${diffs.length > 20 ? `<div class="sys-more">${diffs.length - 20} additional changes are included.</div>` : ''}</div></section>
        <section class="sys-card" style="padding:0"><div class="sys-card-head inset"><div><strong>Validation results</strong><small>Cross-setting safety and dependency checks</small></div>${status(critical ? 'Blocked' : 'Passed', critical ? 'danger' : 'success')}</div><div class="sys-validation-list">${issues.length ? issues.map(item => `<article class="${item.severity}"><span class="material-symbols-rounded">${item.severity === 'critical' ? 'error' : 'warning'}</span><div><strong>${escape(item.area)}</strong><small>${escape(item.message)}</small></div></article>`).join('') : '<div class="sys-empty">All governance validations passed.</div>'}</div></section>
      </div>
      <section class="sys-card" style="padding:0;margin-top:11px"><div class="sys-card-head inset"><div><strong>Immutable version history</strong><small>Published revisions and protected rollback actions</small></div><a href="audit-explorer.html">Open audit trail</a></div><div class="sys-version-list">${state.versions.slice(0, 8).map(item => `<article class="sys-version" data-sys-version="${item.version}"><span class="material-symbols-rounded">history</span><div><strong>v${item.version} · ${escape(item.summary)}</strong><small>${escape(item.actor)} · ${escape(item.date)}</small></div>${item.status === 'Active' ? status('Active') : `<button type="button" data-sys-rollback="${item.version}">Restore</button>`}</article>`).join('')}</div></section>
      <section class="sys-card" style="padding:0;margin-top:11px"><div class="sys-governance"><article><span class="material-symbols-rounded">fact_check</span><div><strong>Schema & dependency validation</strong><small>Reject invalid ranges and unsafe combinations.</small></div></article><article><span class="material-symbols-rounded">approval</span><div><strong>Separation of duties</strong><small>Risky production changes can require a second operator.</small></div></article><article><span class="material-symbols-rounded">undo</span><div><strong>Recoverable rollout</strong><small>Previous versions and secret references remain restorable.</small></div></article></div></section>`;
  };

  const renderView = () => {
    const renderers = {
      server: renderServer, network: renderNetwork, access: renderAccess, resources: renderResources, runtimes: renderRuntimes,
      databases: renderDatabases, mail: renderMail, providers: renderProviders, backups: renderBackups, security: renderSecurity,
      behavior: renderBehavior, branding: renderBranding, operations: renderOperations, livepatch: renderLivePatch, ipam: renderIpam,
      forensics: renderForensics, storage: renderStorage, whitelabel: renderWhiteLabel, aiops: renderAiOps, edge: renderEdge,
      sustainability: renderSustainability, observability: renderObservability, zerotrust: renderZeroTrust, changes: renderChanges
    };
    renderers[state.tab]();
    bindView();
  };

  const updateDirtyUi = () => {
    host.querySelectorAll('.sys-dirty').forEach(element => {
      element.classList.toggle('changed', dirty);
      element.innerHTML = `<i></i>${dirty ? 'Unpublished draft' : 'Synchronized'}`;
    });
    renderMetrics();
    renderNav();
  };

  const bindView = () => {
    view.querySelectorAll('[data-sys-path]').forEach(control => control.addEventListener('change', () => {
      const value = control.type === 'checkbox' ? control.checked : control.type === 'radio' ? control.value : control.type === 'number' || control.type === 'range' ? Number(control.value) : control.value;
      setPath(state, control.dataset.sysPath, value);
      dirty = true;
      updateDirtyUi();
    }));
    view.querySelectorAll('[data-sys-op]').forEach(button => button.addEventListener('click', () => {
      const definition = advancedOperations[button.dataset.sysOp];
      if (!definition) return;
      operation(definition[0], definition[1], definition[2]);
      notify(definition[3]);
    }));
    view.querySelectorAll('[data-sys-jump]').forEach(button => button.addEventListener('click', () => setTab(button.dataset.sysJump)));
    view.querySelector('[data-sys-review]')?.addEventListener('click', () => setTab('changes'));
    view.querySelector('[data-sys-publish]')?.addEventListener('click', publish);
    view.querySelector('[data-sys-add-provider]')?.addEventListener('click', () => openProvider());
    view.querySelector('[data-sys-test-all]')?.addEventListener('click', () => {
      operation('Test all infrastructure providers', 'provider-health', 'medium');
      notify('Provider checks succeeded: DNS, mail and backup capabilities are healthy.');
    });
    view.querySelectorAll('[data-sys-edit-provider]').forEach(button => button.addEventListener('click', () => openProvider(button.dataset.sysEditProvider)));
    view.querySelectorAll('[data-sys-test-provider]').forEach(button => button.addEventListener('click', () => {
      const item = state.providers.find(provider => provider.id === button.dataset.sysTestProvider);
      operation(`Test provider · ${item.name}`, 'provider-health', 'medium');
      notify(`${item.name} connection and required capabilities succeeded.`);
    }));
    view.querySelectorAll('[data-sys-remove-provider]').forEach(button => button.addEventListener('click', () => openDelete(button.dataset.sysRemoveProvider)));
    view.querySelectorAll('[data-sys-rollback]').forEach(button => button.addEventListener('click', () => openRollback(button.dataset.sysRollback)));
    view.querySelector('[data-sys-rotate-session]')?.addEventListener('click', () => {
      operation('Rotate privileged session signing keys', 'security-rotation', 'high');
      notify('Session-key rotation was added to the ROOT Job Center.');
    });
    view.querySelector('[data-sys-backup-drill]')?.addEventListener('click', () => {
      operation('Run isolated backup restore drill', 'backup-restore-drill', 'high');
      notify('Restore drill was added to the ROOT Job Center.');
    });
    view.querySelector('[data-sys-test-alert]')?.addEventListener('click', () => {
      operation('Send System Settings test notification', 'notification-test', 'low');
      notify('Test notification queued for email and signed webhook routes.');
    });
  };

  const setTab = tab => {
    state.tab = tab;
    window.history.replaceState(null, '', `${window.location.pathname}#${tab}`);
    renderNav();
    renderView();
    document.querySelector('.wcp-dashboard-content')?.scrollTo({ top: 0, behavior: 'smooth' });
  };

  function publish() {
    const blockers = validations().filter(item => item.severity === 'critical');
    if (blockers.length) {
      notify(`Publication blocked: ${blockers[0].message}`, true);
      renderView();
      return;
    }
    state.version = Number(state.version || 0) + 1;
    state.versions = [{ version: state.version, actor: 'Yann · ROOT', date: 'Just now', summary: 'System Settings governance update', status: 'Active' }, ...state.versions.map(item => ({ ...item, status: 'Superseded' }))];
    state.tab = 'changes';
    save();
    baseline = clone(state);
    dirty = false;
    operation(`Publish System Settings v${state.version}`, 'configuration-publication', 'high');
    renderMetrics();
    renderNav();
    renderView();
    notify(`System Settings v${state.version} was validated and added to the ROOT Job Center.`);
  }

  const openDrawer = () => {
    drawer.classList.add('open');
    backdrop.classList.add('open');
    drawer.setAttribute('aria-hidden', 'false');
    window.setTimeout(() => drawer.querySelector('input,select,button')?.focus(), 80);
  };
  const closeDrawer = () => {
    drawer.classList.remove('open');
    backdrop.classList.remove('open');
    drawer.setAttribute('aria-hidden', 'true');
    drawerForm.innerHTML = '';
    drawerMode = '';
    selectedId = '';
  };
  const drawerField = (label, name, value = '', type = 'text') => `<label class="sys-field"><span><strong>${label}</strong></span><input type="${type}" name="${name}" value="${escape(value)}" required></label>`;
  const drawerSelect = (label, name, options, value) => `<label class="sys-field"><span><strong>${label}</strong></span><select name="${name}">${options.map(option => `<option ${option === value ? 'selected' : ''}>${escape(option)}</option>`).join('')}</select></label>`;
  const drawerActions = (label, danger = false) => `<div class="sys-form-actions"><button class="wcp-action-btn" type="button" data-sys-cancel>Cancel</button><button class="wcp-action-btn ${danger ? 'danger' : 'primary'}" type="submit" ${danger ? 'data-sys-danger-submit disabled' : ''}>${label}</button></div>`;

  function openProvider(id = '') {
    drawerMode = id ? 'edit-provider' : 'add-provider';
    selectedId = id;
    const item = state.providers.find(entry => entry.id === id) || { type: 'DNS', name: 'New provider', endpoint: 'https://provider.example.com/api', region: 'EU West', auth: 'API token', user: 'wcp360', secret: '', primary: false, enabled: true };
    drawer.querySelector('#sysDrawerTitle').textContent = id ? `Configure ${item.name}` : 'Add infrastructure provider';
    drawer.querySelector('[data-sys-drawer-sub]').textContent = 'Connection, credential reference and routing role';
    drawerForm.innerHTML = `<div class="sys-field-grid cols-2">${drawerSelect('Provider type', 'type', ['DNS', 'Mail', 'Backup'], item.type)}${drawerField('Display name', 'name', item.name)}</div>${drawerField('Endpoint / path', 'endpoint', item.endpoint)}<div class="sys-field-grid cols-2">${drawerField('Region / location', 'region', item.region)}${drawerSelect('Authentication', 'auth', ['API token', 'TSIG + API', 'mTLS', 'mTLS + credentials', 'Access key + secret', 'Service account', 'SSH key', 'Filesystem ACL'], item.auth)}</div><div class="sys-field-grid cols-2">${drawerField('Service identity', 'user', item.user)}${drawerField('Secret / private key', 'secret', item.secret || '', 'password')}</div><label class="sys-check"><input type="checkbox" name="enabled" ${item.enabled ? 'checked' : ''}><span><strong>Provider enabled</strong><small>Include this provider in health checks and eligible service routing.</small></span></label><label class="sys-check"><input type="checkbox" name="primary" ${item.primary ? 'checked' : ''}><span><strong>Primary provider for this service</strong><small>Publication validates service continuity and primary routing.</small></span></label><div class="sys-impact"><span class="material-symbols-rounded">key</span><div><strong>Credentials are write-only</strong><small>Leaving the masked value unchanged preserves the current vault reference.</small></div></div>${drawerActions(id ? 'Save provider' : 'Add provider')}`;
    openDrawer();
  }

  function openDelete(id) {
    drawerMode = 'delete-provider';
    selectedId = id;
    const item = state.providers.find(entry => entry.id === id);
    drawer.querySelector('#sysDrawerTitle').textContent = `Remove ${item.name}`;
    drawer.querySelector('[data-sys-drawer-sub]').textContent = 'Provider dependency and failover review';
    drawerForm.innerHTML = `<div class="sys-impact danger"><span class="material-symbols-rounded">warning</span><div><strong>Provider routing and credentials will be removed</strong><small>Publication is blocked if an active service lacks a healthy replacement.</small></div></div><label class="sys-field"><span><strong>Type ${escape(item.name)} to confirm</strong></span><input name="confirm" data-sys-confirm="${escape(item.name)}" autocomplete="off" required></label>${drawerActions('Remove provider', true)}`;
    const input = drawerForm.querySelector('[data-sys-confirm]');
    const submit = drawerForm.querySelector('[data-sys-danger-submit]');
    input.addEventListener('input', () => { submit.disabled = input.value !== input.dataset.sysConfirm; });
    openDrawer();
  }

  function openRollback(version) {
    drawerMode = 'rollback';
    selectedId = String(version);
    const item = state.versions.find(entry => entry.version === Number(version));
    drawer.querySelector('#sysDrawerTitle').textContent = `Restore System Settings v${version}`;
    drawer.querySelector('[data-sys-drawer-sub]').textContent = 'Protected global configuration rollback';
    drawerForm.innerHTML = `<div class="sys-impact danger"><span class="material-symbols-rounded">undo</span><div><strong>Restore a previous global configuration</strong><small>Current provider compatibility and secret references are validated before activation.</small></div></div><div class="sys-note"><span class="material-symbols-rounded">history</span><div><strong>${escape(item.summary)}</strong><small>${escape(item.actor)} · ${escape(item.date)}</small></div></div><label class="sys-field"><span><strong>Type RESTORE v${version} to confirm</strong></span><input name="confirm" data-sys-confirm="RESTORE v${version}" autocomplete="off" required></label>${drawerActions('Queue restoration', true)}`;
    const input = drawerForm.querySelector('[data-sys-confirm]');
    const submit = drawerForm.querySelector('[data-sys-danger-submit]');
    input.addEventListener('input', () => { submit.disabled = input.value !== input.dataset.sysConfirm; });
    openDrawer();
  }

  drawerForm.addEventListener('submit', event => {
    event.preventDefault();
    const data = Object.fromEntries(new FormData(drawerForm));
    if (drawerMode === 'add-provider' || drawerMode === 'edit-provider') {
      const existing = state.providers.find(item => item.id === selectedId);
      const next = {
        id: existing?.id || `${String(data.type).toLowerCase()}-${Date.now()}`,
        type: data.type, name: data.name, endpoint: data.endpoint, region: data.region, auth: data.auth, user: data.user,
        secret: data.secret && !data.secret.includes('•') ? '••••••••' : (existing?.secret || '••••••••'),
        primary: data.primary === 'on', enabled: data.enabled === 'on', status: 'Healthy',
        icon: data.type === 'DNS' ? 'dns' : data.type === 'Mail' ? 'mail' : 'backup'
      };
      if (existing) Object.assign(existing, next); else state.providers.push(next);
      dirty = true;
      closeDrawer(); renderMetrics(); renderNav(); renderView();
      notify(`${next.name} was added to the unpublished configuration draft.`);
      return;
    }
    if (drawerMode === 'delete-provider') {
      const item = state.providers.find(entry => entry.id === selectedId);
      state.providers = state.providers.filter(entry => entry.id !== selectedId);
      dirty = true;
      closeDrawer(); renderMetrics(); renderNav(); renderView();
      notify(`${item.name} was removed from the draft; dependency validation will run before publication.`, true);
      return;
    }
    if (drawerMode === 'rollback') {
      operation(`Restore System Settings v${selectedId}`, 'configuration-rollback', 'critical');
      closeDrawer();
      notify(`System Settings v${selectedId} restoration was added to the ROOT Job Center.`);
    }
  });

  backdrop.addEventListener('click', closeDrawer);
  drawer.querySelector('.sys-close').addEventListener('click', closeDrawer);
  drawerForm.addEventListener('click', event => { if (event.target.closest('[data-sys-cancel]')) closeDrawer(); });
  document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && drawer.classList.contains('open')) closeDrawer();
    if (event.key === '/' && !/INPUT|TEXTAREA|SELECT/.test(document.activeElement?.tagName || '')) {
      event.preventDefault();
      host.querySelector('[data-sys-search]')?.focus();
    }
  });

  const search = host.querySelector('[data-sys-search]');
  search.addEventListener('input', () => {
    const query = search.value.trim().toLowerCase();
    nav.querySelectorAll('[data-sys-tab]').forEach(button => { button.hidden = Boolean(query && !button.dataset.keywords.includes(query)); });
    nav.querySelectorAll('[data-sys-group]').forEach(group => { group.hidden = ![...group.querySelectorAll('[data-sys-tab]')].some(button => !button.hidden); });
    categorySelect.querySelectorAll('option').forEach(option => {
      const matches = !query || option.dataset.keywords.includes(query);
      option.hidden = !matches;
      option.disabled = !matches;
    });
  });

  host.querySelector('[data-sys-export]').addEventListener('click', () => {
    const redacted = clone(state);
    redacted.providers = redacted.providers.map(item => ({ ...item, secret: 'vault://redacted' }));
    const blob = new Blob([JSON.stringify(redacted, null, 2)], { type: 'application/json' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `wcp360-system-settings-v${state.version}-redacted.json`;
    link.click();
    URL.revokeObjectURL(link.href);
    notify('Redacted System Settings export generated without provider secrets.');
  });

  renderMetrics();
  renderNav();
  renderView();
})();
