/* Retained archives share the hosting model and exact ROOT-job lifecycle. */
(() => {
  'use strict';
  const host = document.querySelector('#capacityCenter'); if (!host) return;
  const UI = window.WCPUI, P = window.WCPPrototypeOperations, H = window.WCPHostingLifecycle, model = window.WCPHostingModel, engine = H.hosting(model);
  const preferences = UI.createStore({app: 'core', scope: 'root', owner: 'fleet', module: 'account-recovery-grid'});
  let state = model.read(), grid = null;
  const can = (item, action) => WCPHostingSchemas.canRecovery(item, action);
  const tab = document.createElement('button'); tab.type = 'button'; tab.dataset.accountRecovery = ''; tab.setAttribute('aria-selected', 'false'); host.querySelector('.cap-tabs')?.append(tab);
  const active = () => tab.classList.contains('active');
  function badge() { tab.innerHTML = '<span class="material-symbols-rounded">restore_from_trash</span>Recovery <b>' + state.archives.length + '</b>'; }
  function refresh() { state = engine.sync().state; badge(); if (active()) grid?.setRows(state.archives); }
  function queue(action, item, payload = {}) {
    const result = engine.queue({action, id: item.id, payload, expected: H.stamp(item)});
    state = result.state; badge(); grid?.setRows(state.archives);
    document.dispatchEvent(new CustomEvent('wcp:hosting-resource-change'));
  }
  function verify(items) {
    for (const item of items.filter(item => can(item, 'verify'))) queue('verify', item);
    UI.notify({title: 'Archive metadata verification', message: 'Results are applied only when each matching ROOT job succeeds. No archive bytes are read.'});
  }
  function restore(item) {
    refresh(); const archive = state.archives.find(row => row.id === item.id);
    if (!can(archive, 'restore')) throw new Error('The archive is unavailable or not verified.');
    const targetId = archive.accountSnapshot?.id || 'acc-restored-' + archive.id;
    const record = archive.accountSnapshot || {id: targetId, domain: archive.domain, owner: archive.owner, email: archive.email, plan: archive.plan, primary: state.servers.find(server => server.status === 'Healthy')?.id, topology: 'Single server'};
    const fields = H.accountFields(state, record).map(field => field.name === 'domain' ? {...field, validate: value => value.toLowerCase() === archive.domain.toLowerCase() ? field.validate?.(value) || '' : 'Keep the archived hostname.'} : field);
    P.form({title: 'Restore account metadata: ' + archive.domain, description: 'Review identity and placement explicitly. A verified metadata snapshot is retained; no files, databases or mail are recovered by this prototype.', fields, submitLabel: 'Queue restoration simulation', onSubmit: values => { queue('restore', archive, {...values, targetId}); }});
  }
  async function purge(item) {
    refresh(); const archive = state.archives.find(row => row.id === item.id);
    if (!can(archive, 'purge')) throw new Error('The archive is held or unavailable.');
    const expected = H.stamp(archive);
    if (!await UI.Dialog.confirm({title: 'Purge retained archive metadata', message: archive.domain + '. The metadata record remains until the matching ROOT job succeeds.', typed: 'PURGE ' + archive.domain, confirmLabel: 'Queue purge'})) return;
    state = engine.queue({action: 'purge', id: archive.id, payload: {confirmWord: 'PURGE ' + archive.domain}, expected}).state;
    badge(); grid?.setRows(state.archives);
  }
  function render() {
    refresh(); grid?.destroy(); grid = null;
    state = model.read(); state.tab = 'recovery'; model.write(state);
    document.dispatchEvent(new CustomEvent('wcp:capacity-view', {detail: {view: 'recovery'}}));
    host.querySelectorAll('.cap-tabs button').forEach(button => {const selected = button === tab; button.classList.toggle('active', selected); button.setAttribute('aria-selected', String(selected));});
    history.replaceState(null, '', '#recovery');
    const view = host.querySelector('[data-cap-view]');
    view.innerHTML = '<div class="cap-section-head"><div><strong>Terminated account recovery</strong><small>Canonical retained metadata, verified state and reviewed ROOT jobs.</small></div><button type="button" class="wcp-ui-button" data-recovery-verify>Verify available metadata</button></div><div data-recovery-grid></div><div class="cap-recovery-policy"><span class="material-symbols-rounded">policy</span><div><strong>Final metadata retention: 30 days</strong><small>Legal holds block purge. Verification, restoration and purge never complete before their matching job.</small></div><a href="hosting-accounts.html#activity">Operation results</a></div>';
    grid = UI.DataGrid.mount(view.querySelector('[data-recovery-grid]'), {id: 'account-recovery-grid', title: 'Retained account metadata', rows: state.archives, store: preferences, columns: [
      {key: 'domain', label: 'Account'}, {key: 'owner', label: 'Owner'}, {key: 'email', label: 'Contact', hidden: true}, {key: 'archive', label: 'Archive reference'}, {key: 'size', label: 'Recorded size'},
      {key: 'terminated', label: 'Terminated', type: 'date'}, {key: 'expires', label: 'Expires', type: 'date'}, {key: 'legalHold', label: 'Legal hold', value: row => row.legalHold ? 'Held' : 'None'}, {key: 'state', label: 'State'}
    ], rowActions: [{label: 'Verify', when: row => can(row, 'verify'), run: P.safe(row => verify([row]))}, {label: 'Restore', when: row => can(row, 'restore'), run: P.safe(restore)}, {label: 'Purge', when: row => can(row, 'purge'), run: P.safe(purge)}], bulkActions: [{label: 'Verify selected metadata', run: P.safe(verify)}]});
    view.querySelector('[data-recovery-verify]').addEventListener('click', P.safe(() => verify(state.archives))); badge();
  }
  tab.addEventListener('click', P.safe(render));
  document.addEventListener('wcp:capacity-view', event => {if (event.detail?.view === 'recovery') return; grid?.destroy(); grid = null; tab.classList.remove('active'); tab.setAttribute('aria-selected', 'false');});
  document.addEventListener('wcp:capacity-refresh', P.safe(refresh));
  document.addEventListener('wcp:runtime-change', P.safe(refresh)); document.addEventListener('wcp:runtime-ready', P.safe(refresh));
  window.addEventListener('storage', event => {if (event.key === model.key) refresh();});
  model.write(state); badge(); if (location.hash === '#recovery' || state.tab === 'recovery') render();
})();
