/* Deterministic demo contracts. No host inspection, network calls or secret storage. */
(() => {
  'use strict';
  const secretPaths = ['database.password', 'storage.secret'];
  const reference = value => /^secret:\/\/[a-z0-9][a-z0-9/_-]{0,150}$/i.test(String(value));
  const fqdn = value => typeof value === 'string' && value.length <= 253 && value.includes('.') && value.split('.').every(label => /^(?!-)[a-z0-9-]{1,63}(?<!-)$/i.test(label));
  const endpoint = (value, protocols) => { try { const url = new URL(value); return protocols.includes(url.protocol) && !!url.hostname && !url.username && !url.password && !url.search && !url.hash; } catch { return false; } };
  const at = (object, path) => path.split('.').reduce((value, key) => value?.[key], object);
  const ipv4 = value => typeof value === 'string' && /^(?:\d{1,3}\.){3}\d{1,3}$/.test(value) && value.split('.').every(part => Number(part) <= 255);
  function restore(defaults, saved) {
    const result = {};
    for (const [group, fields] of Object.entries(defaults)) {
      result[group] = {};
      for (const [name, fallback] of Object.entries(fields)) {
        const value = saved?.[group]?.[name];
        result[group][name] = typeof value === typeof fallback && (typeof value !== 'number' || Number.isFinite(value)) ? value : fallback;
      }
    }
    // Migrate old prototype drafts: discard literal credentials, including masked strings.
    for (const path of secretPaths) { const [group, name] = path.split('.'); if (!reference(result[group][name])) result[group][name] = defaults[group][name]; }
    return result;
  }
  function errors(config) {
    const result = [];
    const require = (path, valid, message) => { if (!valid) result.push({path, message}); };
    require('identity.hostname', fqdn(at(config, 'identity.hostname')), 'Use a fully qualified management hostname, for example panel.example.com.');
    require('identity.adminEmail', /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(at(config, 'identity.adminEmail')), 'Use a valid administrative contact address.');
    require('database.engine', at(config, 'database.engine') === 'PostgreSQL 17', 'The control-plane database target is PostgreSQL 17; tenant engines belong in Database Services.');
    require('database.host', (fqdn(at(config, 'database.host')) && !/^[\d.]+$/.test(at(config, 'database.host'))) || at(config, 'database.host') === 'localhost' || ipv4(at(config, 'database.host')), 'Use a database hostname or IPv4 address.');
    require('network.publicIp', ipv4(at(config, 'network.publicIp')), 'Use a valid IPv4 management address for this demonstration profile.');
    require('network.nameservers', String(at(config, 'network.nameservers') || '').split(',').length >= 2 && String(at(config, 'network.nameservers') || '').split(',').every(name => fqdn(name.trim())), 'Declare at least two fully qualified nameservers, separated by commas.');
    const allowed = {
      'identity.environment':['Production','Staging','Lab'], 'identity.timezone':['Europe/Paris','UTC','Europe/London','America/New_York'], 'identity.region':['EU West','EU Central','US East','Custom'],
      'database.ssl':['Required','Verify CA','Verify full'], 'storage.backupType':['S3-compatible','SFTP','Local filesystem'], 'storage.encryption':['AES-256 + KMS','AES-256 local key','Provider-managed + client-side'], 'network.dnsMode':['External API','Integrated authoritative cluster','Manual'],
      'runtime.php':['8.2','8.3','8.4'], 'runtime.composer':['Composer 2.x','Composer 2.x · security-only updates','Disabled'], 'runtime.node':['20 LTS','22 LTS','24 Current'], 'runtime.nodePackageManager':['pnpm','npm','Yarn'], 'runtime.python':['3.11','3.12','3.13'], 'runtime.pythonPackageManager':['uv + pip','Poetry + pip','pip only'], 'runtime.ruby':['3.2','3.3','3.4'], 'runtime.rubyPackageManager':['Bundler','Bundler · locked mode']
    };
    for (const [path, values] of Object.entries(allowed)) require(path, values.includes(at(config, path)), `Select a supported value for ${path}.`);
    for (const path of ['database.name', 'database.user']) require(path, /^[a-z][a-z0-9_]{0,62}$/.test(at(config, path)) && !['root', 'postgres'].includes(at(config, path)), 'Use a dedicated lowercase PostgreSQL identifier, not root or postgres.');
    for (const path of secretPaths) require(path, reference(at(config, path)), 'Enter a synthetic secret:// reference. Do not enter a password, token or private key.');
    require('storage.dataRoot', /^\/(?:[a-z0-9_-]+\/)*[a-z0-9_-]+$/i.test(at(config, 'storage.dataRoot')) && !/^\/(etc|proc|sys|dev|boot|root)(\/|$)/.test(at(config, 'storage.dataRoot')), 'Use a dedicated absolute data directory without traversal, for example /var/lib/wcp360.');
    const storage = at(config, 'storage.backupType');
    require('storage.endpoint', storage === 'Local filesystem' ? /^\/(?!.*(?:^|\/)\.\.(?:\/|$)).+/.test(at(config, 'storage.endpoint')) : endpoint(at(config, 'storage.endpoint'), storage === 'SFTP' ? ['sftp:'] : ['https:']), 'Use a credential-free HTTPS/SFTP endpoint or an absolute local backup path matching the provider.');
    if (at(config, 'network.dnsMode') === 'External API') require('network.dnsEndpoint', endpoint(at(config, 'network.dnsEndpoint'), ['https:']), 'The DNS API endpoint must use HTTPS and contain no credentials.');
    for (const path of ['network.smtpHost', 'network.imapHost']) require(path, fqdn(at(config, path)), 'Use a fully qualified mail service hostname.');
    for (const [path, min, max] of [['database.port',1,65535],['database.pool',5,500],['network.smtpPort',1,65535],['network.imapPort',1,65535],['security.sshPort',1,65535],['security.passwordMin',12,128],['security.loginAttempts',2,100],['security.banMinutes',1,10080],['resource.cpu',1,64],['resource.ram',1,512],['resource.io',1,10000],['resource.iops',100,1000000],['resource.inodes',1000,100000000],['resource.diskHard',1,100000],['resource.phpMemory',64,4096],['resource.nodeMemory',128,16384]]) {
      const value = at(config, path); require(path, Number.isInteger(value) && value >= min && value <= max, `${path}: enter a whole number between ${min} and ${max}.`);
    }
    require('security.mfaRoot', at(config, 'security.mfaRoot') === true, 'ROOT MFA is required by the demonstration baseline.');
    require('security.firewall', at(config, 'security.firewall') === true, 'The host firewall is required by the demonstration baseline.');
    require('security.rootSsh', at(config, 'security.rootSsh') === false, 'Direct ROOT SSH is incompatible with the baseline; use a named, audited administrative identity.');
    require('services.autoSsl', !at(config, 'services.autoSsl') || at(config, 'services.web'), 'Automatic SSL requires web hosting to be enabled.');
    return result;
  }
  const fixtures = Object.freeze({
    ready: {label:'Debian 13 / amd64 · ready',disk:80,clock:0,dns:true,ports:true},
    disk: {label:'Insufficient disk space',disk:8,clock:0,dns:true,ports:true},
    dns: {label:'Management DNS unresolved',disk:80,clock:0,dns:false,ports:true},
    ports: {label:'Management port conflict',disk:80,clock:0,dns:true,ports:false},
    clock: {label:'System clock drift',disk:80,clock:180,dns:true,ports:true}
  });
  function preflight(config, fixture = 'ready') {
    const host = fixtures[fixture] || fixtures.ready;
    const issues = errors(config);
    const checks = [
      {id:'config',label:'Configuration and service dependencies',ok:!issues.length,detail:issues.length ? `${issues.length} configuration issue(s); review the form.` : 'Schema, references, resource bounds and baseline validated.'},
      {id:'os',label:'Operating system and architecture',ok:true,detail:'Fixture target: Debian 13 / amd64. No operating system was detected.'},
      {id:'disk',label:'Available installation space',ok:host.disk >= 20,detail:`Fixture: ${host.disk} GiB available; demonstration threshold: 20 GiB. Tenant capacity is separate.`},
      {id:'dns',label:'Management DNS',ok:host.dns,detail:host.dns ? 'Fixture: management hostname resolves.' : 'Fixture: management hostname has no address record.'},
      {id:'ports',label:'Management port availability',ok:host.ports,detail:host.ports ? 'Fixture: management listener is available.' : 'Fixture: an existing listener occupies the management port.'},
      {id:'clock',label:'Time synchronization',ok:host.clock <= 30,detail:`Fixture: clock drift ${host.clock} seconds; demonstration threshold: 30 seconds.`}
    ];
    return {ok:checks.every(check => check.ok),checks,issues,fixture:fixtures[fixture] ? fixture : 'ready',simulated:true};
  }
  window.WCPSetup = Object.freeze({restore,errors,preflight,fixtures,reference});
})();
