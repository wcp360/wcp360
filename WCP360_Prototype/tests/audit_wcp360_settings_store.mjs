import { readFileSync } from 'node:fs';
import vm from 'node:vm';
import assert from 'node:assert/strict';

const context = { window: {}, URL, structuredClone };
for (const name of ['settings-model.js', 'settings-store.js']) vm.runInNewContext(readFileSync(new URL('../assets/' + name, import.meta.url), 'utf8'), context);
const model = context.window.WCPSettingsModel, stores = context.WCPSettingsStore;
const { key, draftKey } = stores;
let checks = 0;
const test = (name, run) => { try { run(); checks++; } catch (error) { error.message = name + ': ' + error.message; throw error; } };
const plain = value => JSON.parse(JSON.stringify(value));
function harness(initial = {}) {
  const data = new Map(Object.entries(initial)), writes = [], attempts = [], removals = [];
  let deniedWrite = '', deniedRead = '';
  const storage = {
    getItem(name) { if (deniedRead === '*' || deniedRead === name) throw new Error('Read denied'); return data.get(name) ?? null; },
    setItem(name, value) { attempts.push({ name, value }); if (deniedWrite === '*' || deniedWrite === name) throw new Error('Write denied'); data.set(name, String(value)); writes.push({ name, value }); },
    removeItem(name) { if (deniedWrite === '*' || deniedWrite === name) throw new Error('Remove denied'); data.delete(name); removals.push(name); }
  };
  const open = () => stores.create({ model, storage, now: () => '2026-11-01T14:00:00.000Z' });
  const active = () => writes.filter(write => write.name === key);
  return { data, writes, attempts, removals, storage, open, active, denyWrite: name => { deniedWrite = name; }, denyRead: name => { deniedRead = name; } };
}
const raw = mutate => { const config = model.defaults(); mutate?.(config); return model.serialize(config); };
const reason = 'Ajuster la configuration locale';
const publishName = (store, name) => { store.update('branding.productName', name); return store.publish({ reason }); };

test('initialization and state inspection never write active or draft settings', () => {
  const h = harness(), store = h.open();
  assert.equal(store.getState().version, 0); assert.equal(store.getState().dirty, false);
  store.review(); store.history(); store.exportConfig(); store.getState();
  assert.equal(h.writes.length, 0); assert.equal(h.removals.length, 0); assert.equal(h.data.has(key), false);
});
test('editing writes only a separate draft', () => {
  const initial = raw(), h = harness({ [key]: initial }), store = h.open();
  store.update('identity.serverName', 'new-server');
  assert.equal(h.active().length, 0); assert.equal(h.data.get(key), initial); assert.equal(h.writes.length, 1); assert.equal(h.writes[0].name, draftKey);
  assert.equal(store.getState().draftStatus, 'saved'); assert.equal(store.getState().dirty, true); assert.equal(store.getState().version, 0);
});
test('matching draft restores after a new store session', () => {
  const h = harness({ [key]: raw() }), first = h.open(); first.update('branding.productName', 'Restored draft');
  const second = h.open(), state = second.getState();
  assert.equal(state.draft.branding.productName, 'Restored draft'); assert.equal(state.baseline.branding.productName, 'WCP360'); assert.equal(state.draftStatus, 'restored'); assert.equal(state.dirty, true); assert.equal(h.active().length, 0);
});
test('discard clears the draft and restores the exact active configuration', () => {
  const initial = raw(config => { config.customMetadata = { owner: 'Platform' }; }), h = harness({ [key]: initial }), store = h.open();
  store.update('resource.ram', 8); store.discard();
  assert.equal(store.getState().dirty, false); assert.equal(store.getState().draft.resource.ram, 4); assert.deepEqual(plain(store.getState().draft.customMetadata), { owner: 'Platform' }); assert.equal(h.data.has(draftKey), false); assert.equal(h.data.get(key), initial); assert.equal(h.active().length, 0);
});
test('returning an edited field to baseline removes the unused draft', () => {
  const h = harness(), store = h.open(); store.update('resource.ram', 8); store.update('resource.ram', 4);
  assert.equal(store.getState().dirty, false); assert.equal(h.data.has(draftKey), false); assert.equal(h.active().length, 0);
});
test('validation blocks publication before any active write', () => {
  const h = harness(), store = h.open(); store.update('access.mfaRoot', false);
  assert.ok(store.getState().issues.some(issue => issue.path === 'access.mfaRoot'));
  assert.throws(() => store.publish({ reason, acknowledged: true })); assert.equal(h.active().length, 0); assert.equal(store.getState().version, 0); assert.equal(store.getState().dirty, true);
});
test('publication requires actual effective changes', () => {
  const h = harness(), store = h.open(); assert.throws(() => store.publish({ reason, acknowledged: true })); assert.equal(h.active().length, 0);
});
test('publication requires an adequate reason', () => {
  const h = harness(), store = h.open(); store.update('branding.productName', 'Hosting');
  for (const value of ['', 'short', '       ']) assert.throws(() => store.publish({ reason: value }));
  assert.equal(h.active().length, 0); assert.equal(store.getState().version, 0);
});
test('sensitive changes require an explicit acknowledgement', () => {
  const h = harness(), store = h.open(); store.update('access.sshPort', 22);
  assert.throws(() => store.publish({ reason })); assert.throws(() => store.publish({ reason, acknowledged: false })); assert.equal(h.active().length, 0);
  const result = store.publish({ reason, acknowledged: true }); assert.equal(result.version, 1); assert.equal(result.state.baseline.access.sshPort, 22);
});
test('successful publication atomically writes configuration revision and history once', () => {
  const h = harness(), store = h.open(), result = publishName(store, 'Hosting');
  assert.equal(h.active().length, 1); assert.equal(result.version, 1); assert.equal(result.changes, 1); assert.equal(result.state.version, 1); assert.equal(result.state.dirty, false); assert.equal(h.data.has(draftKey), false);
  const saved = JSON.parse(h.data.get(key));
  assert.equal(saved.version, 1); assert.equal(saved.schemaVersion, 3); assert.equal(saved.branding.productName, 'Hosting'); assert.equal(saved.settingsHistory[0].version, 1); assert.equal(saved.settingsHistory[0].snapshot.version, 1); assert.equal(saved.settingsHistory[0].reason, reason); assert.equal(saved.versions[0].version, 1); assert.equal(saved.versions[0].status, 'Active');
});
test('a successful publication cannot be submitted a second time', () => {
  const h = harness(), store = h.open(); publishName(store, 'Hosting'); assert.throws(() => store.publish({ reason })); assert.equal(h.active().length, 1); assert.equal(store.getState().version, 1);
});
test('active write denial never advances baseline or reports success', () => {
  const initial = raw(config => { config.version = 7; }), h = harness({ [key]: initial }), store = h.open(); store.update('branding.productName', 'Unsaved'); h.denyWrite(key);
  assert.throws(() => store.publish({ reason }));
  const state = store.getState(); assert.equal(state.version, 7); assert.equal(state.baseline.branding.productName, 'WCP360'); assert.equal(state.draft.branding.productName, 'Unsaved'); assert.equal(state.dirty, true); assert.ok(state.writeError); assert.equal(h.data.get(key), initial); assert.equal(h.active().length, 0); assert.ok(h.data.has(draftKey));
});
test('an active write can be retried after storage recovers', () => {
  const h = harness(), store = h.open(); store.update('branding.productName', 'Retry'); h.denyWrite(key); assert.throws(() => store.publish({ reason })); h.denyWrite('');
  assert.equal(store.publish({ reason }).version, 1); assert.equal(h.active().length, 1); assert.equal(store.getState().writeError, '');
});
test('draft write failure stays visible and never mutates active settings', () => {
  const h = harness({ [key]: raw() }), store = h.open(); h.denyWrite(draftKey); store.update('resource.ram', 8);
  const state = store.getState(); assert.equal(state.draftStatus, 'unsaved'); assert.ok(state.writeError); assert.equal(state.draft.resource.ram, 8); assert.equal(state.baseline.resource.ram, 4); assert.equal(h.active().length, 0);
});
test('unavailable storage prevents publication and recovery', () => {
  const store = stores.create({ model, storage: null }); assert.ok(store.getState().readError); store.update('branding.productName', 'Offline'); assert.throws(() => store.publish({ reason })); assert.throws(() => store.recover());
});
test('read denial is visible and reload can recover it without an active write', () => {
  const h = harness({ [key]: raw() }); h.denyRead(key); const store = h.open(); assert.ok(store.getState().readError); assert.throws(() => store.recover()); h.denyRead(''); assert.equal(store.reload().readError, ''); assert.equal(h.active().length, 0);
});
test('external writes are detected and publication cannot overwrite them', () => {
  const h = harness({ [key]: raw() }), store = h.open(); store.update('resource.ram', 8);
  const external = raw(config => { config.version = 3; config.branding.productName = 'Other tab'; }); h.data.set(key, external);
  assert.equal(store.checkExternal().conflict, true); assert.throws(() => store.publish({ reason })); assert.equal(store.getState().conflict, true); assert.equal(h.data.get(key), external); assert.equal(h.active().length, 0);
});
test('rebase preserves independent external changes and applies the local draft', () => {
  const h = harness({ [key]: raw() }), store = h.open(); store.update('resource.ram', 8);
  h.data.set(key, raw(config => { config.version = 3; config.branding.productName = 'Other tab'; config.extensionMetadata = { retain: true }; }));
  const result = store.rebase(); assert.equal(result.collisions.length, 0); assert.equal(result.state.version, 3); assert.equal(result.state.draft.resource.ram, 8); assert.equal(result.state.draft.branding.productName, 'Other tab'); assert.deepEqual(plain(result.state.draft.extensionMetadata), { retain: true }); assert.equal(result.state.conflict, false); assert.equal(h.active().length, 0);
  assert.equal(store.publish({ reason }).version, 4); assert.equal(JSON.parse(h.data.get(key)).branding.productName, 'Other tab');
});
test('rebase reports colliding paths and keeps the explicitly local field value', () => {
  const h = harness({ [key]: raw() }), store = h.open(); store.update('resource.ram', 8); h.data.set(key, raw(config => { config.resource.ram = 16; }));
  const result = store.rebase(); assert.deepEqual(plain(result.collisions), ['resource.ram']); assert.equal(result.state.baseline.resource.ram, 16); assert.equal(result.state.draft.resource.ram, 8); assert.equal(h.active().length, 0);
});
test('a draft based on another active value is flagged stale after reload', () => {
  const h = harness({ [key]: raw() }), first = h.open(); first.update('resource.ram', 8); h.data.set(key, raw(config => { config.version = 2; config.branding.productName = 'External'; }));
  const second = h.open(), state = second.getState(); assert.equal(state.draftStatus, 'stale'); assert.equal(state.staleDraft, true); assert.equal(state.dirty, false); assert.equal(state.draft.resource.ram, 4); assert.equal(state.draft.branding.productName, 'External'); assert.equal(h.active().length, 0);
});
test('stale draft restoration stages values without reverting active metadata', () => {
  const h = harness({ [key]: raw() }), first = h.open(); first.update('resource.ram', 8); h.data.set(key, raw(config => { config.version = 2; config.extensionMetadata = { current: true }; }));
  const second = h.open(), state = second.restoreStaleDraft(); assert.equal(state.draft.resource.ram, 8); assert.equal(state.version, 2); assert.deepEqual(plain(state.draft.extensionMetadata), { current: true }); assert.equal(state.staleDraft, false); assert.equal(h.active().length, 0);
});
test('invalid saved draft is ignored and reported without touching active settings', () => {
  const initial = raw(), h = harness({ [key]: initial, [draftKey]: '{broken' }), store = h.open(); assert.equal(store.getState().draftStatus, 'invalid'); assert.equal(store.getState().dirty, false); assert.equal(h.data.get(key), initial); assert.equal(h.active().length, 0);
});
test('published snapshots contain no nested history and preserve earlier versions', () => {
  const h = harness(), store = h.open(); publishName(store, 'First'); publishName(store, 'Second');
  const history = store.history(); assert.equal(history[0].version, 2); assert.equal(history[0].current, true); assert.equal(history[1].version, 1); assert.equal(history[1].snapshot.branding.productName, 'First');
  for (const row of history) assert.equal(Object.hasOwn(row.snapshot, 'settingsHistory'), false);
  assert.equal(h.active().length, 2);
});
test('restoring a snapshot prepares a new draft without changing active revision', () => {
  const h = harness(), store = h.open(); publishName(store, 'First'); publishName(store, 'Second'); const row = store.history().find(item => item.version === 1), before = h.data.get(key), count = h.active().length;
  const state = store.stageHistory(row.id); assert.equal(state.draft.branding.productName, 'First'); assert.equal(state.baseline.branding.productName, 'Second'); assert.equal(state.version, 2); assert.equal(state.dirty, true); assert.equal(h.data.get(key), before); assert.equal(h.active().length, count);
  assert.equal(store.publish({ reason: 'Restaurer le premier profil' }).version, 3); assert.equal(JSON.parse(h.data.get(key)).branding.productName, 'First');
});
test('unknown snapshot IDs do not alter a draft', () => {
  const h = harness(), store = h.open(); assert.throws(() => store.stageHistory('missing')); assert.equal(store.getState().dirty, false); assert.equal(h.writes.length, 0);
});
test('partial import changes only present recognized fields', () => {
  const h = harness({ [key]: raw(config => { config.resource.ram = 16; config.extensionMetadata = { keep: true }; }) }), store = h.open();
  const state = store.stageImport(JSON.stringify({ branding: { productName: 'Imported' }, version: 999, extensionMetadata: { keep: false } }));
  assert.equal(state.draft.branding.productName, 'Imported'); assert.equal(state.draft.resource.ram, 16); assert.equal(state.version, 0); assert.deepEqual(plain(state.draft.extensionMetadata), { keep: true }); assert.equal(state.changes.length, 1); assert.equal(h.active().length, 0);
});
test('invalid import documents are refused without changing either configuration', () => {
  const h = harness({ [key]: raw() }), store = h.open(), before = model.serialize(store.getState().draft);
  for (const value of ['{broken', 'null', '[]', '42', '{"unknown":true}', '{"resource":{"ram":{}}}', '{"branding":{"productName":"Imported"},"secret":"DO-NOT-STORE"}']) assert.throws(() => store.stageImport(value), value);
  assert.equal(model.serialize(store.getState().draft), before); assert.equal(h.writes.length, 0); assert.equal(h.active().length, 0);
});
test('imported semantic validation errors remain reviewable and block publication', () => {
  const h = harness(), store = h.open(); store.stageImport('{"resource":{"diskSoft":80}}'); assert.ok(store.getState().issues.some(issue => issue.path === 'resource.diskSoft')); assert.throws(() => store.publish({ reason })); assert.equal(h.active().length, 0);
});
test('legacy metadata and service configuration survive publication', () => {
  const initial = raw(config => { config.version = 13; config.runtime = { php: '8.3', node: '22 LTS' }; config.mail = { mta: 'Exim', smtpPort: 465 }; config.providerMetadata = [{ name: 'Legacy provider', region: 'EU' }]; config.versions = [{ version: 13, actor: 'Legacy operator', summary: 'Previous config', status: 'Active' }]; }), h = harness({ [key]: initial }), store = h.open(); publishName(store, 'Modern panel');
  const saved = JSON.parse(h.data.get(key)); assert.equal(saved.version, 14); assert.equal(saved.runtime.php, '8.3'); assert.equal(saved.runtime.node, '22 LTS'); assert.equal(saved.mail.mta, 'Exim'); assert.equal(saved.mail.smtpPort, 465); assert.deepEqual(saved.providerMetadata, [{ name: 'Legacy provider', region: 'EU' }]); assert.equal(saved.versions[1].actor, 'Legacy operator'); assert.equal(saved.versions[1].status, 'Superseded');
});
test('exports redact legacy literal secrets but retain references and harmless state', () => {
  const config = model.defaults(); config.providers = [{ name: 'Legacy', secret: 'DO-NOT-EXPORT', endpoint: 'https://u:DO-NOT-EXPORT@example.com/api?token=DO-NOT-EXPORT', secretRef: 'secret://demo/provider' }]; config.settingsHistory = [{ id: 'legacy', version: 1, snapshot: { database: { password: 'DO-NOT-EXPORT' } } }];
  const initial = JSON.stringify(config), h = harness({ [key]: initial }), store = h.open();
  assert.equal(h.data.get(key), initial); assert.equal(h.active().length, 0);
  for (const exported of [store.exportConfig(), store.exportConfig({ draft: true })]) { assert.ok(!exported.includes('DO-NOT-EXPORT')); assert.ok(exported.includes('secret://demo/provider')); assert.equal(JSON.parse(exported).access.rootPassword, false); }
});
test('explicit migration repair preserves legacy settings metadata and sanitized snapshots', () => {
  const config = model.defaults(); config.version = 13; config.identity.hostname = 'legacy-panel.example.net'; config.resource.ram = 16;
  config.runtime = { php: '8.3', phpMemory: 512 }; config.extensionMetadata = { owner: 'Platform', feature: true };
  config.providers = [{ id: 'legacy-dns', name: 'Legacy DNS', endpoint: 'https://dns.example.net/api', secret: 'LEGACY-LITERAL', enabled: true }];
  config.versions = [{ version: 13, actor: 'Legacy operator', summary: 'Previous profile', status: 'Active' }];
  config.settingsHistory = [{ id: 'legacy-12', version: 12, createdAt: '2026-10-31T12:00:00Z', reason: 'Previous valid snapshot', snapshot: { ...model.defaults(), version: 12, runtime: { php: '8.2' }, extensionMetadata: { keepSnapshot: true }, providers: [{ id: 'legacy-dns', secret: 'HISTORY-LITERAL' }] } }];
  const original = JSON.stringify(config), h = harness({ [key]: original }), first = h.open();
  assert.ok(first.getState().loadIssues.length); first.update('branding.productName', 'Attempt'); assert.throws(() => first.publish({ reason, acknowledged: true }));
  first.recover();
  const second = h.open(), repaired = second.getState();
  assert.equal(repaired.recovering, true); assert.equal(repaired.draft.identity.hostname, 'legacy-panel.example.net'); assert.equal(repaired.draft.resource.ram, 16); assert.equal(repaired.draft.runtime.php, '8.3'); assert.deepEqual(plain(repaired.draft.extensionMetadata), { owner: 'Platform', feature: true }); assert.equal(repaired.draft.providers[0].name, 'Legacy DNS'); assert.equal(repaired.history[0].id, 'legacy-12'); assert.equal(repaired.history[0].snapshot.runtime.php, '8.2'); assert.equal(repaired.history[0].snapshot.extensionMetadata.keepSnapshot, true);
  assert.ok(!second.exportConfig({ draft: true }).includes('LITERAL')); assert.equal(h.data.get(key), original); assert.equal(h.active().length, 0);
  second.publish({ reason: 'Migrer la configuration existante', acknowledged: true });
  const saved = JSON.parse(h.data.get(key));
  assert.equal(saved.version, 14); assert.equal(saved.identity.hostname, 'legacy-panel.example.net'); assert.equal(saved.runtime.php, '8.3'); assert.equal(saved.extensionMetadata.owner, 'Platform'); assert.equal(saved.providers[0].id, 'legacy-dns'); assert.equal(saved.settingsHistory.find(row => row.id === 'legacy-12').snapshot.extensionMetadata.keepSnapshot, true); assert.ok(saved.versions.some(row => row.actor === 'Legacy operator')); assert.ok(!h.data.get(key).includes('LITERAL')); assert.equal(h.active().length, 1);
});
test('draft export is separate from the active export', () => {
  const h = harness(), store = h.open(); store.update('branding.productName', 'Unpublished'); assert.equal(JSON.parse(store.exportConfig()).branding.productName, 'WCP360'); assert.equal(JSON.parse(store.exportConfig({ draft: true })).branding.productName, 'Unpublished'); assert.equal(h.active().length, 0);
});
test('corrupt active JSON requires an explicit recovery before publication', () => {
  const h = harness({ [key]: '{broken' }), store = h.open(); assert.ok(store.getState().loadIssues.length); store.update('branding.productName', 'Attempt'); assert.throws(() => store.publish({ reason, acknowledged: true })); assert.equal(h.data.get(key), '{broken'); assert.equal(h.active().length, 0);
  const state = store.recover(); assert.equal(state.recovering, true); assert.equal(state.dirty, true); assert.equal(state.draft.branding.productName, 'WCP360'); assert.equal(h.data.get(key), '{broken'); assert.equal(h.active().length, 0); assert.throws(() => store.publish({ reason }));
});
test('explicit recovery draft survives reload and can replace corrupt storage once', () => {
  const h = harness({ [key]: '{broken' }), first = h.open(); first.recover(); first.update('branding.productName', 'Recovered');
  const second = h.open(), state = second.getState(); assert.equal(state.recovering, true); assert.equal(state.draftStatus, 'restored'); assert.equal(state.draft.branding.productName, 'Recovered'); assert.ok(state.loadIssues.length); assert.equal(h.active().length, 0);
  const result = second.publish({ reason: 'Réparer les données locales', acknowledged: true }); assert.equal(result.version, 1); assert.equal(result.state.recovering, false); assert.equal(result.state.loadIssues.length, 0); assert.equal(JSON.parse(h.data.get(key)).branding.productName, 'Recovered'); assert.equal(h.active().length, 1);
});
test('recovery can be discarded without overwriting the corrupt original', () => {
  const h = harness({ [key]: '{broken' }), store = h.open(); store.recover(); store.discard(); assert.equal(store.getState().recovering, false); assert.equal(store.getState().dirty, false); assert.equal(h.data.get(key), '{broken'); assert.equal(h.data.has(draftKey), false); assert.equal(h.active().length, 0);
});
test('rebase refuses a corrupt external configuration without losing local edits', () => {
  const h = harness({ [key]: raw() }), store = h.open(); store.update('resource.ram', 8); h.data.set(key, '{broken'); assert.throws(() => store.rebase()); assert.equal(store.getState().draft.resource.ram, 8); assert.equal(store.getState().baseline.resource.ram, 4); assert.equal(h.data.get(key), '{broken'); assert.equal(h.active().length, 0);
});
test('history remains bounded and snapshots never accumulate recursive history', () => {
  const h = harness(), store = h.open(); for (let index = 1; index <= 15; index++) publishName(store, 'Revision ' + index);
  const saved = JSON.parse(h.data.get(key)); assert.equal(saved.version, 15); assert.equal(saved.settingsHistory.length, 12); assert.equal(saved.versions.length, 12); assert.equal(store.history().length, 12); for (const row of saved.settingsHistory) assert.equal(Object.hasOwn(row.snapshot, 'settingsHistory'), false); assert.equal(h.active().length, 15);
});
test('state objects are defensive copies of baseline draft and history', () => {
  const h = harness(), store = h.open(); publishName(store, 'Current'); const state = store.getState(); state.baseline.branding.productName = 'Wrong'; state.draft.branding.productName = 'Wrong'; state.history[0].snapshot.branding.productName = 'Wrong';
  assert.equal(store.getState().baseline.branding.productName, 'Current'); assert.equal(store.getState().draft.branding.productName, 'Current'); assert.equal(store.history()[0].snapshot.branding.productName, 'Current');
});

test('empty numeric edits survive matching draft reload and still block publication', () => {
  const h = harness({ [key]: raw(config => { config.resource.ram = 16; }) }), first = h.open();
  first.update('resource.ram', '');
  const second = h.open(), state = second.getState();
  assert.equal(state.draft.resource.ram, ''); assert.equal(state.baseline.resource.ram, 16); assert.equal(state.draftStatus, 'restored');
  assert.ok(state.issues.some(issue => issue.path === 'resource.ram')); assert.throws(() => second.publish({ reason })); assert.equal(h.active().length, 0);
});
test('empty numeric edits also survive explicit stale draft restoration', () => {
  const h = harness({ [key]: raw() }), first = h.open(); first.update('resource.ram', '');
  h.data.set(key, raw(config => { config.version = 2; config.resource.ram = 16; }));
  const second = h.open(), state = second.restoreStaleDraft();
  assert.equal(state.draft.resource.ram, ''); assert.equal(state.baseline.resource.ram, 16); assert.ok(state.issues.length); assert.equal(h.active().length, 0);
});
test('corrupt structured drafts are reported without staging valid-looking default resets', () => {
  const invalidRam = value => { const config = model.defaults(); config.resource.ram = value; return config; };
  for (const corrupt of [[], null, {}, { branding: { productName: 'Partial draft' } }, invalidRam({}), invalidRam(null)]) {
    const initial = raw(config => { config.resource.ram = 16; config.identity.serverName = 'keep-server'; }), h = harness({ [key]: initial }), first = h.open();
    first.update('resource.ram', 8); const envelope = JSON.parse(h.data.get(draftKey)); envelope.config = corrupt; h.data.set(draftKey, JSON.stringify(envelope));
    const state = h.open().getState(); assert.equal(state.draftStatus, 'invalid'); assert.equal(state.dirty, false); assert.equal(state.draft.resource.ram, 16); assert.equal(state.draft.identity.serverName, 'keep-server'); assert.equal(h.data.get(key), initial);
  }
});
test('draft deletion failures remain visible before and after a successful revision', () => {
  const h = harness(), store = h.open(); store.update('resource.ram', 8); h.denyWrite(draftKey);
  assert.equal(store.discard().draftStatus, 'cleanup-error'); assert.equal(h.data.has(draftKey), true); assert.equal(store.getState().dirty, false);
  h.denyWrite(''); assert.equal(store.discard().draftStatus, 'empty'); assert.equal(h.data.has(draftKey), false); assert.equal(h.active().length, 0);
  store.update('resource.ram', 8); h.denyWrite(draftKey);
  const result = store.publish({ reason }); assert.equal(result.version, 1); assert.equal(result.state.draftStatus, 'cleanup-error'); assert.equal(result.state.dirty, false); assert.equal(h.active().length, 1);
  h.denyWrite(''); store.discard(); assert.equal(h.data.has(draftKey), false); assert.equal(h.active().length, 1);
});

test('complete v2 drafts migrate without losing edits or inventing an active write', () => {
  const h = harness({ [key]: raw() }), first = h.open(); first.update('branding.productName', 'Ancien brouillon'); first.update('resource.ram', '');
  const envelope = JSON.parse(h.data.get(draftKey)), legacy = { schemaVersion: 2, version: 0 };
  for (const path of model.legacyDraftPaths) model.set(legacy, path, model.get(envelope.config, path));
  envelope.config = legacy; h.data.set(draftKey, JSON.stringify(envelope));
  const state = h.open().getState(); assert.equal(state.draftStatus, 'restored'); assert.equal(state.draft.branding.productName, 'Ancien brouillon'); assert.equal(state.draft.resource.ram, ''); assert.equal(state.draft.runtime.php, '8.3'); assert.equal(state.draft.schemaVersion, 3); assert.equal(h.active().length, 0);
});
test('truncated v2 drafts remain invalid after additive migration', () => {
  const h = harness({ [key]: raw() }), first = h.open(); first.update('branding.productName', 'Draft');
  const envelope = JSON.parse(h.data.get(draftKey)); envelope.config.schemaVersion = 2; delete envelope.config.access; h.data.set(draftKey, JSON.stringify(envelope));
  const state = h.open().getState(); assert.equal(state.draftStatus, 'invalid'); assert.equal(state.dirty, false); assert.equal(h.active().length, 0);
});
test('provider records publish atomically and are restored through reviewed snapshots', () => {
  const h = harness(), store = h.open(), provider = { id: 'dns', kind: 'DNS', name: 'DNS démo', endpoint: 'https://dns.example.com', region: 'EU', secretRef: 'secret://demo/dns', enabled: true };
  store.update('providers', [provider]); assert.equal(h.active().length, 0); assert.equal(store.getState().changes.length, 1); assert.throws(() => store.publish({ reason }));
  store.publish({ reason, acknowledged: true }); assert.equal(h.active().length, 1); assert.equal(JSON.parse(h.data.get(key)).providers[0].id, 'dns');
  store.update('providers', []); store.publish({ reason, acknowledged: true }); const row = store.history().find(record => record.version === 1);
  store.stageHistory(row.id); assert.equal(store.getState().draft.providers.length, 1); assert.equal(store.getState().baseline.providers.length, 0); assert.equal(h.active().length, 2);
});
test('collection imports preserve independent settings and invalid entries block publication', () => {
  const h = harness({ [key]: raw(config => { config.runtime.phpMemory = 512; }) }), store = h.open();
  store.stageImport('{"providers":[{"id":"dns","kind":"DNS","name":"DNS démo","endpoint":"https://dns.example.com","enabled":true}]}'); assert.equal(store.getState().draft.runtime.phpMemory, 512); assert.equal(store.getState().draft.providers[0].name, 'DNS démo');
  store.update('providers', [{ id: 'invalid', kind: 'DNS', name: 'Bad', endpoint: 'http://bad.example.com', enabled: true }]); assert.throws(() => store.publish({ reason, acknowledged: true })); assert.equal(h.active().length, 0);
});

console.log(JSON.stringify({ suite: 'System Settings local lifecycle', behaviorChecks: checks, status: 'passed', coverage: 'Real model and store with instrumented localStorage; active/draft isolation, validation, atomic publication, conflicts, history, import/export and explicit corruption recovery. No DOM or real infrastructure.' }, null, 2));
