/* Closed demo workflows for account/site metadata and retained archives. */
((global) => {
  'use strict';
  const UI = global.WCPUI, L = global.WCPResourceLifecycle, S = global.WCPHostingSchemas;
  const clone = value => structuredClone(value), same = (a, b) => UI.normalize(String(a || '').trim()) === UI.normalize(String(b || '').trim());
  const readySite = site => site && !site._pending && ['Online', 'Protected', 'Maintenance'].includes(site.status);
  const retryable = row => row && !row._pending && ['Provisioning', 'Provisioning failed', 'Provisioning cancelled'].includes(row.status);
  const reviewFields = () => [{name: 'health', label: 'Simulated result', type: 'select', required: true, options: ['Pass', 'Fail'], default: 'Pass'}];
  const validate = (fields, values) => {
    const result = UI.validateValues(fields, values);
    if (!result.valid) throw new Error(Object.values(result.errors)[0]);
    return result.values;
  };
  function policy() { try { return JSON.parse(global.localStorage.getItem('wcp360-runtime-center-v1') || 'null'); } catch { return {}; } }
  const versions = family => global.WCPApplicationSchemas.allowedVersions(policy(), family);
  const runtime = type => type === 'Node.js' ? 'Node.js' : type === 'Static' ? 'Static' : 'PHP';
  const versionLabel = site => site.runtime === 'Static' ? 'Static / no runtime' : site.runtime + ' ' + site.version;
  const pathIssue = value => UI.rules.absolutePath(value) && /^\/(public_html|sites|apps|staging)(\/|$)/.test(value) ? '' : 'Use a virtual account path under /public_html, /sites, /apps or /staging without traversal.';
  const rootField = (state, record, initial) => ({name: 'docroot', label: 'Virtual account document root', required: true, default: initial || record?.docroot || '/sites/newsite/public', validate: value => pathIssue(value) || (state.sites.some(site => site.id !== record?.id && site.docroot === value) ? 'This document root is already assigned to another website.' : '')});
  function domainField(sites, record, initial = '') {
    return {name: 'domain', label: 'Primary hostname', required: true, rule: 'domain', default: initial || record?.domain || '', validate: value => sites.some(site => site.id !== record?.id && [site.domain, ...(site.aliases || [])].some(name => same(name, value))) ? 'This hostname is already attached to a website or alias.' : ''};
  }
  function siteFields(state, record) {
    const options = ['PHP', 'Node.js', 'Static'].flatMap(family => versions(family).map(version => family === 'Static' ? 'Static / no runtime' : family + ' ' + version));
    return [domainField(state.sites, record, record ? '' : 'newsite.example.com'),
      {name: 'label', label: 'Website label', required: true, maxLength: 80, default: record?.label || 'New website'},
      {name: 'type', label: 'Workload metadata', type: 'select', required: true, options: ['PHP', 'WordPress', 'Node.js', 'Static'], default: record?.type || 'PHP'},
      {name: 'environment', label: 'Environment', type: 'select', required: true, options: ['Production', 'Staging', 'Development'], default: record?.environment || 'Production'},
      {name: 'version', label: 'ROOT-available runtime', type: 'select', required: true, options, default: record ? versionLabel(record) : options[0] || '', validate: (value, values) => value.startsWith(runtime(values.type) === 'Static' ? 'Static /' : runtime(values.type) + ' ') ? '' : 'Select a runtime matching the workload type.'},
      rootField(state, record), ...reviewFields()];
  }
  function siteValues(record) { return {domain: record.domain, label: record.label, type: record.type, environment: record.environment, version: versionLabel(record), docroot: record.docroot, health: 'Pass'}; }
  const manageSiteFields = (state, record) => [domainField(state.sites, record), rootField(state, record), ...reviewFields()];
  function cloneFields(state, source) {
    const choices = state.sites.filter(site => readySite(site) && (!site.owner || site.owner === 'example.com'));
    return [{name: 'sourceId', label: 'Source website metadata', type: 'select', required: true, options: choices.map(site => ({value: site.id, label: site.domain})), default: source?.id || choices[0]?.id || ''}, domainField(state.sites, null, source ? 'staging-' + source.domain : 'staging-new.example.com'),
      {name: 'label', label: 'Clone label', required: true, maxLength: 80, default: 'Staging metadata'},
      {name: 'environment', label: 'Environment', type: 'select', required: true, options: ['Staging', 'Development'], default: 'Staging'},
      rootField(state, null, '/sites/new-staging/public'), ...reviewFields()];
  }
  function siteRow(values, id, inherited) {
    const family = runtime(values.type), version = family === 'Static' ? 'None' : values.version.slice(family.length + 1);
    return {id, owner: 'example.com', domain: values.domain.toLowerCase(), label: values.label.trim(), type: values.type, framework: values.type, runtime: family, version, docroot: values.docroot, environment: values.environment,
      status: values.environment === 'Production' ? 'Online' : 'Protected', ssl: 'Not verified', sslDays: 0, storage: 0, requests: null, response: null,
      region: inherited?.region || 'Paris · EU', placement: 'Managed by ROOT', branch: 'main', lastDeploy: 'Not deployed', maintenance: false, cache: false, forceHttps: false, hsts: false, aliases: []};
  }
  function websites(model) {
    const recipes = {
      create: {collection: 'sites', effect: 'create', pending: 'Provisioning', retryable, title: 'Provision website metadata', context: (_state, values) => versions(runtime(values.type)), prepare(state, current, values, id) {
        if (current && !retryable(current)) throw new Error('This website cannot be reprovisioned.');
        const input = validate(siteFields({...state, sites: state.sites.filter(site => site.id !== id)}, current), values);
        if (state.sites.filter(site => site.id !== id).length >= 25) throw new Error('The demo website allowance is exhausted.');
        return siteRow(input, id, current);
      }},
      clone: {collection: 'sites', effect: 'create', pending: 'Provisioning', retryable, title: 'Clone website metadata', context: (state, values) => {
        const source = state.sites.find(site => site.id === values.sourceId && model.owned(site));
        return {source, versions: source ? versions(source.runtime) : []};
      }, prepare(state, current, values, id) {
        if (current && !retryable(current)) throw new Error('This clone cannot be retried.');
        const input = validate(cloneFields({...state, sites: state.sites.filter(site => site.id !== id)}), values), source = state.sites.find(site => site.id === input.sourceId && model.owned(site));
        if (!readySite(source)) throw new Error('The clone source is not ready.');
        if (!versions(source.runtime).includes(source.runtime === 'Static' ? 'None' : source.version)) throw new Error('The source runtime is no longer available.');
        if (state.sites.filter(site => site.id !== id).length >= 25) throw new Error('The demo website allowance is exhausted.');
        return {...siteRow({...input, type: source.type, version: versionLabel(source)}, id, source), sourceSiteId: source.id};
      }},
      configure: {collection: 'sites', effect: 'replace', pending: 'Updating', title: 'Apply website metadata', prepare(state, current, values) {
        if (!readySite(current)) throw new Error('The website is not ready for configuration.');
        const input = validate(manageSiteFields(state, current), values);
        return {...current, domain: input.domain.toLowerCase(), docroot: input.docroot};
      }},
      delete: {collection: 'sites', effect: 'remove', pending: 'Deleting', title: 'Delete website metadata', prepare(_state, current, values) {
        if (!current || current._pending || !readySite(current) && !retryable(current)) throw new Error('This website cannot be deleted now.');
        if (values.confirmWord !== current.domain) throw new Error('Type the exact hostname to delete this website metadata.');
        return null;
      }}
    };
    return L.create({namespace: 'website-resource', scope: 'user', read: model.read, write: model.write, recipes, owned: model.owned});
  }

  const available = state => ({plans: state.plans.filter(plan => plan.status === 'Active'), servers: state.servers.filter(server => server.status === 'Healthy')});
  const accountList = (state, id) => state.accounts.filter(account => account.id !== id && account.status !== 'Terminated');
  const accountFields = (state, record) => {
    const fields = S.account({accounts: accountList(state, record?.id), ...available(state)}), defaults = record ? accountValues(record) : {};
    return [...fields.map(field => Object.hasOwn(defaults, field.name) ? {...field, default: defaults[field.name]} : field), ...reviewFields()];
  };
  function accountValues(account) {
    return {domain: account.domain, owner: account.owner, email: account.email, username: account.username || account.domain.split('.')[0].replace(/[^a-z0-9_]/gi, '_').toLowerCase(), plan: account.plan, primary: account.primary, secondary: account.secondary || '', topology: account.topology || 'Single server', placementPolicy: account.placementPolicy || 'Auto-select best fit', reseller: account.reseller || 'ROOT owned', blueprint: account.blueprint || 'Business Standard · v18', featureList: account.featureList || 'Standard Web', defaultRuntime: account.defaultRuntime || 'PHP 8.4', locale: account.locale || 'English (United States)', autoDns: account.services?.dns ?? true, autoSsl: account.services?.autoSsl ?? true, mailService: account.services?.mail ?? true, welcome: account.services?.welcome ?? true, health: 'Pass'};
  }
  function accountRow(values, id) {
    return {id, domain: values.domain.toLowerCase(), owner: values.owner.trim(), email: values.email, username: values.username, plan: values.plan, primary: values.primary, secondary: values.topology === 'Single server' ? '' : values.secondary, topology: values.topology, cluster: values.topology === 'Cluster' ? 'EU-WEB-01' : '', placementPolicy: values.placementPolicy, reseller: values.reseller, blueprint: values.blueprint, featureList: values.featureList, defaultRuntime: values.defaultRuntime, locale: values.locale, services: {dns: values.autoDns, autoSsl: values.autoSsl, mail: values.mailService, welcome: values.welcome}, defaultPage: 'welcome', overrides: {cpu: 0, ram: 0, storage: 0, iops: 0, bandwidth: 0, transfer: 0}, usage: {cpu: 0, ram: 0, storage: 0, transfer: 0}, status: 'Active'};
  }
  const policyContext = state => available(state);
  function addonFields(state, accountId = '', addonId = '') {
    return [{name: 'accountId', label: 'Hosting account', type: 'select', required: true, options: state.accounts.filter(account => S.canAccountTransition(account, 'manage')).map(account => ({value: account.id, label: account.domain})), default: accountId},
      {name: 'addonId', label: 'Add-on', type: 'select', required: true, options: state.addons.map(addon => ({value: addon.id, label: addon.name})), default: addonId || state.addons[0]?.id || ''},
      {name: 'quantity', label: 'Quantity', type: 'number', required: true, min: 1, max: 20, default: 1, validate: (value, values) => !Number.isInteger(Number(value)) ? 'Use a whole quantity.' : ['secondary', 'cluster'].includes(values.addonId) && Number(value) !== 1 ? 'Topology add-ons require a quantity of one.' : ''}, ...reviewFields()];
  }
  function hosting(model) {
    const recipes = {
      provision: {collection: 'accounts', effect: 'create', pending: 'Provisioning', retryable, title: 'Provision hosting account', context: policyContext, prepare(state, current, values, id) {
        if (current && !retryable(current)) throw new Error('This account cannot be reprovisioned.');
        return accountRow(validate(accountFields({...state, accounts: state.accounts.filter(account => account.id !== id)}, current), values), id);
      }},
      configure: {collection: 'accounts', effect: 'replace', pending: 'Updating', title: 'Apply account capacity and placement', context: policyContext, prepare(state, current, values) {
        if (!S.canAccountTransition(current, 'manage')) throw new Error('The account is not ready for configuration.');
        const input = validate([{name: 'email', label: 'Contact email', required: true, type: 'email', default: current.email}, ...S.placement({...available(state), record: current}), ...reviewFields()], {...values, email: values.email || current.email});
        return {...current, email: input.email, plan: input.plan, primary: input.primary, secondary: input.topology === 'Single server' ? '' : input.secondary, topology: input.topology, cluster: input.topology === 'Cluster' ? 'EU-WEB-01' : '', overrides: Object.fromEntries(['cpu', 'ram', 'storage', 'iops', 'bandwidth', 'transfer'].map(key => [key, Number(input[key])]))};
      }},
      suspend: {collection: 'accounts', effect: 'replace', pending: 'Suspending', title: 'Suspend hosting account', prepare(_state, current, values) {
        if (!S.canAccountTransition(current, 'suspend')) throw new Error('This account cannot be suspended.');
        const input = validate([...S.lifecycle(current, 'suspend'), ...reviewFields()], values);
        return {...current, status: 'Suspended', defaultPage: 'suspended', suspension: {reason: input.reason, handling: input.handling, notified: input.notify, preserveJobs: input.preserveJobs, at: values.requestedAt, previousStatus: current.status}};
      }},
      resume: {collection: 'accounts', effect: 'replace', pending: 'Resuming', title: 'Unsuspend hosting account', prepare(_state, current) {
        if (!S.canAccountTransition(current, 'resume')) throw new Error('This account cannot be resumed.');
        return {...current, status: current.suspension?.previousStatus === 'Demo' ? 'Demo' : 'Active', defaultPage: 'welcome'};
      }},
      terminate: {collection: 'accounts', effect: 'replace', pending: 'Terminating', title: 'Terminate hosting account', prepare(_state, current, values) {
        if (!S.canAccountTransition(current, 'terminate')) throw new Error('This account cannot be terminated.');
        const input = validate([...S.lifecycle(current, 'terminate'), ...reviewFields()], values);
        return {...current, status: 'Terminated', defaultPage: 'unavailable', termination: {finalBackup: input.finalBackup, notified: input.notify, retentionDays: input.finalBackup ? 30 : 0, at: values.requestedAt}};
      }, applied(state, operation) {
        if (!operation.after.termination.finalBackup || state.archives.some(archive => archive.sourceOperationId === operation.id)) return;
        const before = operation.before, completed = operation.completedAt;
        state.archives.unshift({id: 'rec-' + operation.id, sourceOperationId: operation.id, domain: before.domain, owner: before.owner, email: before.email, plan: before.plan, archive: 'ARC-' + operation.id, size: Number(before.usage?.storage || 0).toFixed(1) + ' GB (demo metadata)', terminated: new Date(completed).toISOString().slice(0, 10), expires: new Date(completed + 30 * 86400000).toISOString().slice(0, 10), state: 'Restorable', accountSnapshot: clone(before)});
      }},
      addon: {collection: 'accounts', effect: 'replace', pending: 'Updating', title: 'Assign account add-on', context: state => ({...available(state), addons: state.addons}), prepare(state, current, values) {
        if (!S.canAccountTransition(current, 'manage')) throw new Error('The account is not ready for an add-on.');
        const candidate = {...state, accounts: state.accounts.map(account => account.id === current.id ? current : account)}, input = validate(addonFields(candidate), values);
        if (input.accountId !== current.id) throw new Error('The reviewed account changed.');
        const addon = state.addons.find(addon => addon.id === input.addonId), after = clone(current);
        if (['cpu', 'ram', 'storage', 'iops', 'bandwidth', 'transfer'].includes(addon.resource)) after.overrides[addon.resource] = Number(after.overrides[addon.resource] || 0) + addon.amount * Number(input.quantity);
        else if (addon.resource === 'secondary') {
          if (after.secondary) throw new Error('This account already has a secondary server.');
          after.secondary = available(state).servers.find(server => server.id !== current.primary)?.id;
          if (!after.secondary) throw new Error('A distinct healthy secondary is unavailable.');
          after.topology = 'Primary + secondary';
        } else if (addon.resource === 'cluster') {
          if (after.topology === 'Cluster') throw new Error('This account is already on a cluster.');
          after.primary = 'cluster-eu-web'; after.secondary = ''; after.cluster = 'EU-WEB-01'; after.topology = 'Cluster';
        } else throw new Error('Unknown capacity add-on.');
        return after;
      }},
      verify: {collection: 'archives', statusField: 'state', effect: 'replace', pending: 'Verifying', title: 'Verify retained archive metadata', prepare(_state, current) {
        if (!S.canRecovery(current, 'verify')) throw new Error('This archive cannot be verified now.');
        return {...current, state: 'Restorable'};
      }},
      purge: {collection: 'archives', statusField: 'state', effect: 'remove', pending: 'Purge queued', title: 'Purge retained archive metadata', prepare(_state, current, values) {
        if (!S.canRecovery(current, 'purge') || values.confirmWord !== 'PURGE ' + current.domain) throw new Error('The archive is held, unavailable or confirmation is missing.');
        return null;
      }},
      restore: {collection: 'archives', statusField: 'state', effect: 'replace', pending: 'Restore queued', title: 'Restore account metadata', context: (state, values) => ({policy: policyContext(state), target: state.accounts.find(account => account.id === values.targetId) || null}), prepare(state, current, values) {
        if (!S.canRecovery(current, 'restore')) throw new Error('The archive must be verified before restoration.');
        if (values.targetId !== (current.accountSnapshot?.id || 'acc-restored-' + current.id)) throw new Error('The archive restoration target changed.');
        const target = state.accounts.find(account => account.id === values.targetId);
        if (target && target.status !== 'Terminated') throw new Error('The target account already exists.');
        const input = validate(accountFields(state, target), values);
        if (!same(input.domain, current.domain)) throw new Error('Restoration must retain the archived hostname.');
        return {...current, state: 'Restored', restoredAccountId: values.targetId};
      }, applied(state, operation) {
        const archive = operation.before, values = operation.payload;
        const restored = archive.accountSnapshot ? {...clone(archive.accountSnapshot), ...accountRow(values, values.targetId), overrides: clone(archive.accountSnapshot.overrides), usage: clone(archive.accountSnapshot.usage), status: ['Suspended', 'Demo'].includes(archive.accountSnapshot.status) ? archive.accountSnapshot.status : 'Active'} : accountRow(values, values.targetId);
        if (restored.status === 'Suspended') restored.defaultPage = 'suspended';
        delete restored._pending; delete restored.lifecycleResult; delete restored.termination;
        restored.restoredFrom = archive.id;
        const target = state.accounts.find(account => account.id === values.targetId);
        if (target) state.accounts = state.accounts.map(account => account.id === values.targetId ? restored : account);
        else state.accounts.unshift(restored);
      }}
    };
    return L.create({namespace: 'hosting-resource', scope: 'root', read: model.read, write: model.write, recipes});
  }
  global.WCPHostingLifecycle = Object.freeze({websites, hosting, readySite, retryable, siteFields, siteValues, manageSiteFields, cloneFields, accountFields, accountValues, addonFields, reviewFields, stamp: L.stamp});
})(globalThis);
