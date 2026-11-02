import { readFileSync, existsSync } from 'node:fs';
import vm from 'node:vm';
import assert from 'node:assert/strict';

const context = { window: {}, URL };
vm.runInNewContext(readFileSync(new URL('../assets/settings-model.js', import.meta.url), 'utf8'), context);
const model = context.window.WCPSettingsModel;
let checks = 0;
const test = (name, run) => { try { run(); checks++; } catch (error) { error.message = name + ': ' + error.message; throw error; } };
const fields = model.sections.flatMap(section => section.groups.flatMap(group => group.fields));
const changed = (path, value) => { const config = model.defaults(); model.set(config, path, value); return config; };
const errorsAt = (path, value) => model.validate(changed(path, value)).filter(issue => issue.path === path);

test('bounded declarative information architecture', () => {
  assert.deepEqual(Array.from(model.sections, section => section.id), ['server', 'network', 'access', 'resources', 'security', 'operations', 'branding', 'governance', 'integrations', 'runtimes', 'databases', 'mail', 'backups', 'storage', 'providers', 'ipam', 'livepatch', 'zerotrust', 'forensics', 'observability', 'aiops', 'edge', 'sustainability', 'whitelabel']);
  assert.ok(fields.length >= 248 && fields.length <= 320);
  assert.equal(new Set(fields.map(field => field.path)).size, fields.length);
  for (const section of model.sections) {
    assert.ok(section.title && section.description && section.icon && section.groups.length);
    for (const link of section.links || []) assert.ok(existsSync(new URL('../' + link.href, import.meta.url)), link.href);
  }
  for (const field of fields) assert.ok(model.get(model.defaults(), field.path) !== undefined, field.path);
  assert.ok(Object.isFrozen(model.sections[0].groups[0].fields));
});
test('specialized links stay within ROOT or the design reference portal', () => {
  const navigationContext = { document: { documentElement: { setAttribute() {} }, querySelectorAll: () => [], addEventListener() {}, body: { dataset: {} } }, window: { addEventListener() {} }, localStorage: { getItem: () => null }, location: { pathname: '/index.html', search: '', hash: '' }, URLSearchParams };
  vm.runInNewContext(readFileSync(new URL('../assets/foundation.js', import.meta.url), 'utf8'), navigationContext);
  navigationContext.window.WCPUI = navigationContext.WCPUI;
  vm.runInNewContext(readFileSync(new URL('../assets/reference-ui.js', import.meta.url), 'utf8'), navigationContext);
  const navigation = navigationContext.window.WCPNavigation;
  for (const section of model.sections) for (const link of section.links || []) {
    assert.ok(['root', 'design'].includes(navigation.owner(link.href)?.scope), link.href + ' leaves the platform portal');
  }
});

test('default profile is valid and cloned', () => {
  assert.equal(model.validate(model.defaults()).length, 0);
  const first = model.defaults(); model.set(first, 'branding.productName', 'Changed');
  assert.equal(model.defaults().branding.productName, 'WCP360');
  assert.equal(model.defaults().version, 0);
  assert.equal(model.defaults().schemaVersion, 3);
});
test('fresh load is clean', () => { const result = model.load(null); assert.equal(result.issues.length, 0); assert.equal(model.validate(result.config).length, 0); });
for (const raw of ['', '{broken', 'null', '[]', '42', '"text"']) test('malformed root ' + raw, () => {
  const result = model.load(raw); assert.ok(result.issues.length); assert.equal(model.validate(result.config).length, 0); assert.ok(result.issues.every(issue => typeof issue.message === 'string'));
});
test('typed legacy values are normalized', () => {
  const result = model.load(JSON.stringify({ resource: { ram: '8' }, network: { ipv6: 'false' }, access: { mfaRoot: 'true' } }));
  assert.equal(result.config.resource.ram, 8); assert.equal(result.config.network.ipv6, false); assert.equal(result.config.access.mfaRoot, true);
});
test('malformed field types recover with an issue', () => {
  const result = model.load(JSON.stringify({ resource: { ram: {} }, identity: { hostname: 42 }, access: { mfaRoot: [] } }));
  assert.equal(result.config.resource.ram, 4); assert.equal(result.config.identity.hostname, 'wcp-admin.example.com'); assert.equal(result.config.access.mfaRoot, true); assert.equal(result.issues.length, 3);
});
test('invalid semantic values stay visible for correction', () => {
  const result = model.load(JSON.stringify({ access: { mfaRoot: false }, resource: { diskSoft: 80, diskHard: 60 } }));
  assert.equal(result.config.access.mfaRoot, false); assert.ok(model.validate(result.config).some(issue => issue.path === 'access.mfaRoot')); assert.ok(model.validate(result.config).some(issue => issue.path === 'resource.diskSoft'));
});
test('legacy service blocks and unknown metadata survive a roundtrip', () => {
  const saved = { version: 42, schemaVersion: 1, runtime: { php: '8.3', phpMemory: 512 }, mail: { smtpPort: 465 }, database: { engine: 'PostgreSQL 17', password: 'secret://demo/db' }, storage: { secret: 'vault://backup/primary', snapshotPolicy: 'daily' }, customExtension: { feature: true, array: ['a', 3, { name: 'n' }] }, versions: [{ version: 42, summary: 'Legacy revision' }], settingsHistory: [{ version: 41, snapshot: { extra: { keep: true } } }] };
  const config = model.load(JSON.stringify(saved)).config;
  assert.equal(config.schemaVersion, 3); assert.equal(config.version, 42);
  model.set(config, 'identity.serverName', 'new-node');
  const output = JSON.parse(model.serialize(config));
  for (const key of ['runtime', 'mail', 'database', 'storage', 'customExtension']) for (const [name, value] of Object.entries(saved[key])) assert.deepEqual(output[key][name], value, key + '.' + name);
  for (const key of ['versions', 'settingsHistory']) assert.deepEqual(output[key], saved[key], key);
});
test('literals are removed recursively, references and policy booleans survive', () => {
  const saved = { access: { rootPassword: false }, security: { passwordMin: 14, passwordHistory: 12, passwordBreached: true }, providers: [{ id: 'p', secret: 'DO-NOT-STORE-SECRET', api_key: 'DO-NOT-STORE-TOKEN', secretRef: 'secret://demo/provider', user: 'provider-user' }], extensions: { clientSecret: 'DO-NOT-STORE-CLIENT', credentials: { value: 'DO-NOT-STORE-CREDS' }, private_key: ['DO-NOT-STORE-KEY'] }, settingsHistory: [{ snapshot: { database: { password: 'DO-NOT-STORE-PASS' } } }] };
  const result = model.load(JSON.stringify(saved));
  const serialized = model.serialize(result.config);
  assert.ok(!serialized.includes('DO-NOT-STORE')); assert.ok(serialized.includes('secret://demo/provider')); assert.equal(result.config.access.rootPassword, false); assert.equal(result.config.security.passwordBreached, true); assert.ok(result.issues.length >= 6);
});
test('exports sanitize newly injected literals and embedded URL credentials', () => {
  const config = model.defaults();
  config.legacy = { password: 'EXPORT-SECRET', endpoint: 'https://user:EXPORT-PASSWORD@example.com/hook?api_key=EXPORT-KEY&region=eu#access_token=EXPORT-TOKEN' };
  const serialized = model.serialize(config), result = JSON.parse(serialized);
  assert.ok(!serialized.includes('EXPORT-')); assert.equal(result.legacy.endpoint, 'https://example.com/hook?region=eu');
});
test('prototype keys are discarded without changing object prototypes', () => {
  const result = model.load('{"__proto__":{"polluted":true},"network":{"constructor":{"polluted":true}},"prototype":{"polluted":true}}');
  assert.equal({}.polluted, undefined); assert.ok(!model.serialize(result.config).includes('polluted'));
  assert.equal(model.get(result.config, '__proto__.polluted'), undefined);
  assert.throws(() => model.set(result.config, '__proto__.polluted', true));
  assert.throws(() => model.set(result.config, 'extensions.secret', 'literal'));
});
test('schema version and publication revision are separate', () => {
  for (const version of [-1, 1.5, '13', Number.MAX_SAFE_INTEGER + 1]) assert.equal(model.load(JSON.stringify({ version })).config.version, 0);
  const config = model.load('{"schemaVersion":1,"version":13,"versions":[{"version":13}]}').config;
  assert.equal(config.schemaVersion, 3); assert.equal(config.version, 13); assert.equal(config.versions[0].version, 13);
});
test('diff includes only editable effective fields with section label and risk', () => {
  const before = model.defaults(), after = model.defaults();
  after.version = 99; after.tab = 'network'; after.unknown = true; after.runtime.legacyMetadata = 'retained'; after.settingsHistory = [{ version: 1 }];
  assert.equal(model.diff(before, after).length, 0);
  model.set(after, 'access.sshPort', 22); model.set(after, 'branding.productName', 'Hosting');
  const changes = model.diff(before, after);
  assert.equal(changes.length, 2);
  const access = changes.find(change => change.path === 'access.sshPort'); assert.equal(access.before, 2222); assert.equal(access.after, 22); assert.equal(access.section, 'access'); assert.equal(access.risk, 'high'); assert.ok(access.label);
  assert.equal(changes.find(change => change.path === 'branding.productName').risk, 'low');
});

for (const [path, values] of [
  ['identity.hostname', ['localhost', '-bad.example.com', 'bad-.example.com', 'bad..example.com', '999.0.0.1']],
  ['identity.serverName', ['', '../root', 'space name']],
  ['identity.timezone', ['', 'Mars/Olympus']],
  ['identity.rootEmail', ['invalid', '.a@example.com', 'a..b@example.com', 'a@localhost', 'a@bad-.com']],
  ['network.primaryIpv4', ['999.1.1.1', '1.2.3', '01.2.3.4', '1.2.3.4/24']],
  ['network.primaryIpv6', ['12345::1', '1::2::3', '1:2:3:4:5:6:7', '::1:', ':1::', ':::1', '::ffff:999.1.1.1']],
  ['network.gateway', ['gw.example.com', '256.0.0.1']],
  ['network.resolverPrimary', ['resolver.example.com', '10.1.1.999']],
  ['network.managementCidr', ['', '10.20.0.0/33', '2001:db8::/129', '10.20.0.0', '0.0.0.0/0', '::/0', '10.1.0.0/-1']],
  ['access.trustedProxy', ['0.0.0.0/0', '::/0', '10.1.2.3', 'proxy.example.com']],
  ['access.mfaRoot', [false]], ['access.rootSsh', [true]], ['access.rootPassword', [true]], ['access.sshKeys', [false]], ['access.localFallback', [false]],
  ['branding.supportUrl', ['http://support.example.com', 'javascript:alert(1)', 'https://user:password@example.com', 'https://example.com/?token=x']],
  ['branding.logoUrl', ['javascript:alert(1)', '//example.com/logo.svg', '/assets/../logo.svg', '<svg>']],
  ['operations.syslogEndpoint', ['tls://logs.example.com', 'udp://logs.example.com:514', 'tls://user:password@logs.example.com:6514', 'tls://logs.example.com:65536']],
  ['environment', ['Unknown', '<script>']], ['security.tlsMinimum', ['TLS 1.0']]
]) for (const value of values) test('reject ' + path + ' = ' + String(value), () => assert.ok(errorsAt(path, value).length));

for (const item of fields.filter(field => field.type === 'number')) {
  test(item.path + ' rejects non-numeric or fractional input', () => { for (const value of ['', NaN, Infinity, null, 1.5, String(item.min)]) assert.ok(errorsAt(item.path, value).length, String(value)); });
  test(item.path + ' rejects values outside its bounds', () => { assert.ok(errorsAt(item.path, item.min - 1).length); assert.ok(errorsAt(item.path, item.max + 1).length); });
}
test('valid IPv6 addresses and CIDR lists are accepted', () => {
  for (const value of ['::', '::1', '2001:db8::10', '1:2:3:4:5:6:7:8', '::ffff:192.0.2.1']) assert.equal(errorsAt('network.primaryIpv6', value).length, 0, value);
  for (const value of ['10.20.0.0/16, 192.0.2.0/24', '2001:db8::/48\n10.20.0.0/16']) assert.equal(errorsAt('network.managementCidr', value).length, 0, value);
});
test('disabled optional integrations can have empty endpoints', () => {
  const config = model.defaults();
  model.set(config, 'observability.collector', ''); model.set(config, 'operations.syslogEndpoint', ''); model.set(config, 'branding.webhookUrl', '');
  assert.equal(model.validate(config).length, 0);
  for (const path of ['observability.otelEnabled', 'operations.remoteSyslog', 'branding.webhookEnabled']) model.set(config, path, true);
  for (const path of ['observability.collector', 'operations.syslogEndpoint', 'branding.webhookUrl']) assert.ok(model.validate(config).some(issue => issue.path === path));
});
test('NTP and access dependencies are checked', () => {
  assert.ok(errorsAt('identity.ntpServers', '').length);
  const config = changed('identity.ntpEnabled', false); model.set(config, 'identity.ntpServers', ''); assert.ok(!model.validate(config).some(issue => issue.path === 'identity.ntpServers'));
  model.set(config, 'access.localFallback', false); model.set(config, 'access.ssoEnabled', true); assert.ok(!model.validate(config).some(issue => issue.path === 'access.localFallback'));
});
test('cross-field thresholds and retention remain coherent', () => {
  assert.ok(errorsAt('resource.diskSoft', 61).length);
  const config = changed('retention.logs', 365); model.set(config, 'retention.audit', 30); assert.ok(model.validate(config).some(issue => issue.path === 'retention.audit'));
  model.set(config, 'retention.logs', 30); model.set(config, 'retention.jobs', 90); assert.ok(model.validate(config).some(issue => issue.path === 'retention.audit'));
});

const provider = extra => ({ id: 'dns-main', kind: 'DNS', name: 'DNS principal', endpoint: 'https://dns.example.com/api', region: 'EU', secretRef: 'secret://demo/dns', enabled: true, ...extra });
const pool = extra => ({ id: 'pool-main', name: 'Documentation IPv4', cidr: '192.0.2.0/24', gateway: '192.0.2.1', vlan: 0, enabled: true, ...extra });
test('navigation families cover every restored domain once', () => {
  const ids = Array.from(model.navigationGroups).flatMap(group => Array.from(group.sections));
  assert.equal(ids.length, model.sections.length); assert.equal(new Set(ids).size, ids.length);
  for (const section of model.sections) assert.ok(ids.includes(section.id));
  assert.equal(model.legacyDraftPaths.length, 86);
});
test('collection snapshots compare by content and diffs retain actual records', () => {
  const before = model.defaults(), after = model.defaults();
  model.set(before, 'providers', [provider()]); model.set(after, 'providers', [provider()]); assert.equal(model.diff(before, after).length, 0);
  model.set(after, 'providers', [provider({ endpoint: 'https://dns2.example.com/api' })]);
  const delta = model.diff(before, after); assert.equal(delta.length, 1); assert.equal(delta[0].risk, 'high'); assert.equal(delta[0].after[0].endpoint, 'https://dns2.example.com/api');
  after.providers[0].name = 'Changed later'; assert.equal(delta[0].after[0].name, 'DNS principal');
});
test('provider records validate references and reject duplicate endpoints or names', () => {
  assert.equal(model.validateCollection('providers', [provider()]).length, 0);
  for (const values of [[provider(), provider({ id: 'second' })], [provider({ endpoint: 'http://dns.example.com' })], [provider({ secretRef: 'literal-password' })], [provider({ kind: 'Unsupported' })]]) assert.ok(model.validateCollection('providers', values).length);
  assert.ok(model.validateCollection('providers', [provider(), provider({ id: 'second', name: 'Other DNS', endpoint: 'https://dns.example.com/api/' })]).length);
});
test('storage destination identifiers and backup references remain coherent', () => {
  const config = model.defaults(); model.set(config, 'backups.targetId', 'primary'); assert.ok(model.validate(config).some(issue => issue.path === 'backups.targetId'));
  model.set(config, 'storage.targets', [{ id: 'primary', name: 'Stockage démo', endpoint: 'https://s3.example.com', region: 'EU', bucket: 'backups', secretRef: 'secret://demo/s3', enabled: true }]);
  assert.equal(model.validate(config).length, 0); config.storage.targets[0].enabled = false; assert.ok(model.validate(config).some(issue => issue.path === 'backups.targetId'));
});
test('IP pools reject overlapping networks and gateways outside the subnet', () => {
  assert.equal(model.validateCollection('ipam.pools', [pool()]).length, 0);
  assert.ok(model.validateCollection('ipam.pools', [pool(), pool({ id: 'other', name: 'Overlap', cidr: '192.0.2.128/25', gateway: '' })]).some(message => message.includes('chevauche')));
  assert.ok(model.validateCollection('ipam.pools', [pool({ gateway: '198.51.100.1' })]).length);
  assert.ok(model.validateCollection('ipam.pools', [pool({ gateway: '2001:db8::1' })]).length);
  const ipv6Pool = pool({ cidr: '2001:db8::/48', gateway: '2001:db8::1' });
  assert.equal(model.validateCollection('ipam.pools', [ipv6Pool]).length, 0);
  assert.ok(model.validateCollection('ipam.pools', [ipv6Pool, pool({ id: 'v6-other', name: 'IPv6 overlap', cidr: '2001:0db8:0::/64', gateway: '' })]).length);
});
test('advanced dependencies prevent contradictory deployment preferences', () => {
  for (const [path, value, issuePath] of [['database.publicAccess', true, 'database.publicAccess'], ['storage.warningPercent', 95, 'storage.warningPercent'], ['livepatch.enabled', true, 'livepatch.provider']]) {
    const config = changed(path, value); assert.ok(model.validate(config).some(issue => issue.path === issuePath));
  }
  const energy = changed('sustainability.targetUtilization', 40); model.set(energy, 'sustainability.idleThreshold', 50); assert.ok(model.validate(energy).some(issue => issue.path === 'sustainability.idleThreshold'));
  const config = changed('zeroTrust.enabled', true); model.set(config, 'zeroTrust.mtls', false); assert.ok(model.validate(config).some(issue => issue.path === 'zeroTrust.mtls'));
  model.set(config, 'zeroTrust.mtls', true); model.set(config, 'aiops.enabled', true); model.set(config, 'aiops.provider', 'Endpoint interne'); assert.ok(model.validate(config).some(issue => issue.path === 'aiops.endpoint'));
});
test('advanced formats and malformed collections remain reviewable', () => {
  for (const [path, value] of [['backups.startTime', '25:61'], ['backups.timezone', 'Mars/Olympus'], ['storage.mountRoot', '/srv/../root'], ['whiteLabel.panelDomain', 'localhost'], ['whiteLabel.accentColor', 'red']]) assert.ok(errorsAt(path, value).length, path);
  const loaded = model.load('{"providers":{},"ipam":{"pools":[null]},"storage":{"targets":null}}'); assert.ok(loaded.issues.length >= 3); assert.equal(model.validate(loaded.config).length, 0);
});

test('wizard mail and backup endpoints retain their declared provider usage', () => {
  const loaded = model.load(JSON.stringify({ providers: [
    { id: 'smtp', type: 'Mail', name: 'Relais SMTP', endpoint: 'smtp.example.net:465', enabled: true },
    { id: 'imap', type: 'Mail', name: 'Accès IMAP', endpoint: 'imaps://imap.example.net:993', enabled: true },
    { id: 'backup', type: 'Backup', name: 'Sauvegarde SFTP', endpoint: 'sftp://backup.example.net:22/archive', enabled: true }
  ] }));
  assert.equal(loaded.issues.length, 0); assert.equal(loaded.config.providers[0].kind, 'Mail'); assert.equal(loaded.config.providers[2].kind, 'Stockage'); assert.equal(model.validate(loaded.config).length, 0);
  assert.ok(model.validateCollection('providers', [provider({ kind: 'Mail', endpoint: 'smtps://user:password@smtp.example.net:465' })]).length);
  assert.equal(model.validateCollection('providers', [provider({ kind: 'Mail', endpoint: 'smtp1.example.net:465' }), provider({ id: 'smtp-2', name: 'SMTP 2', kind: 'Mail', endpoint: 'smtp2.example.net:465' })]).length, 0);
});
test('previous runtime and backup keys migrate and remain synchronized after edits', () => {
  const loaded = model.load('{"runtime":{"web":"Apache","execution":120,"upload":128},"retention":{"daily":7,"weekly":4,"monthly":6}}').config;
  assert.equal(loaded.runtime.webEngine, 'Apache'); assert.equal(loaded.runtime.phpExecution, 120); assert.equal(loaded.runtime.phpUpload, 128); assert.equal(loaded.backups.retentionDays, 7);
  model.set(loaded, 'runtime.phpExecution', 90); model.set(loaded, 'backups.retentionDays', 21);
  assert.equal(loaded.runtime.execution, 90); assert.equal(loaded.retention.daily, 21); assert.equal(model.validate(loaded).length, 0);
});

console.log(JSON.stringify({ suite: 'System Settings model', sections: model.sections.length, fields: fields.length, behaviorChecks: checks, status: 'passed', coverage: 'Pure schema, migration, preservation, redaction, validation and effective diff; no browser rendering or real infrastructure checks.' }, null, 2));
