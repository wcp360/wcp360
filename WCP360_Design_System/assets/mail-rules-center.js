/* Views plugged into Email Center; one shared account state and one renderer. */
((global) => {
  'use strict';
  global.WCPMailRules = Object.freeze({create(bridge) {
    const UI = global.WCPUI, P = global.WCPPrototypeOperations, S = global.WCPMailRuleSchemas, esc = UI.escape;
    const tabs = ['rules', 'retention', 'trace', 'mail-activity'];
    let grid = null;
    const data = () => bridge.state;
    function config() {
      const state = data();
      if (state.mailPolicy === undefined) state.mailPolicy = S.seed(state.mailboxes, state.responders, state.settings);
      const value = state.mailPolicy;
      const shape = snapshot => snapshot && Array.isArray(snapshot.rules) && snapshot.rules.every(row => row && typeof row === 'object' && typeof row.id === 'string') && snapshot.options && typeof snapshot.options === 'object';
      if (!value || value.schema !== 1 || !Number.isInteger(value.revision) || value.revision < 1 || !Number.isInteger(value.generation) || value.generation < 0 || !Array.isArray(value.operations) || !Array.isArray(value.history) || !shape(value.draft) || !shape(value.published) || !value.operations.every(item => item && typeof item.jobId === 'string' && ['publish', 'rollback'].includes(item.kind) && shape(item.snapshot)) || !value.history.every(item => item && typeof item.id === 'string' && shape(item.snapshot))) throw new Error('Mail policy storage is invalid. Use the explicit repair action.');
      return value;
    }
    const context = () => S.context(data().mailboxes);
    const busy = () => config().operations.some(P.pending);
    const fingerprint = value => JSON.stringify(value);
    function sync() {
      let changed = bridge.refresh();
      const cfg = config(), jobs = P.jobs();
      for (const operation of cfg.operations) {
        if (operation.applied) continue;
        const job = jobs.find(job => job.id === operation.jobId && job.scope === 'user' && job.kind === 'mail-policy-' + operation.kind);
        if (!job) continue;
        if (operation.status !== job.status || operation.progress !== job.progress) {
          operation.status = job.status; operation.progress = job.progress; changed = true;
        }
        if (!['completed', 'failed', 'cancelled'].includes(job.status)) continue;
        operation.applied = true; changed = true;
        if (job.status !== 'completed') { operation.result = 'No policy change: ' + job.status; continue; }
        if (operation.baseGeneration !== cfg.generation || operation.basePublished !== fingerprint(cfg.published) || operation.context !== context()) {
          operation.result = 'Stale: mailbox inventory or published version changed'; continue;
        }
        const issue = S.issue(operation.snapshot, data().mailboxes);
        if (issue || operation.health !== 'Pass') { operation.result = issue || 'Simulated activation failed; published policy retained'; continue; }
        cfg.history.unshift({id: P.uid(), generation: cfg.generation, revision: cfg.publishedRevision, snapshot: structuredClone(cfg.published)});
        cfg.history = cfg.history.slice(0, 20);
        cfg.published = structuredClone(operation.snapshot); cfg.publishedRevision = operation.revision; cfg.generation++;
        // Compatibility projections. These are no longer competing editors.
        data().settings.catchAll = cfg.published.options.catchAll;
        data().settings.catchAllMailbox = cfg.published.options.catchAllMailbox;
        data().settings.quarantineDays = cfg.published.options.quarantineDays;
        data().responders = cfg.published.rules.filter(rule => rule.enabled && rule.type === 'Autoresponder').map(rule => ({id: rule.id, mailbox: data().mailboxes.find(box => box.id === rule.mailboxId)?.address, subject: rule.subject, schedule: rule.starts + ' → ' + rule.ends + ' UTC', status: 'Published (demo)'}));
        operation.result = 'Published demo mail policy generation ' + cfg.generation;
      }
      if (changed) bridge.save();
      return changed;
    }
    const prepare = () => { sync(); return config(); };
    const changed = cfg => { cfg.revision++; cfg.evidence = null; bridge.save(); bridge.render(); };
    const guard = (revision, captured) => {
      const cfg = prepare();
      if (cfg.revision !== revision || context() !== captured) throw new Error('The draft or mailbox inventory changed. Reopen the form.');
      return cfg;
    };

    function edit(type, record) {
      const cfg = prepare(), revision = cfg.revision, captured = context();
      if (type === 'Autoresponder' && !data().mailboxes.some(box => box.status === 'Active')) throw new Error('An active mailbox is required for an automatic response.');
      const current = record && cfg.draft.rules.find(rule => rule.id === record.id);
      if (record && !current) throw new Error('This rule no longer exists.');
      const fields = S.ruleFields({type, record: current, mailboxes: data().mailboxes});
      P.form({title: (current ? 'Edit ' : 'Add ') + type.toLowerCase(), description: 'Draft only. Preview and publish to apply this metadata policy. Schedule times are UTC.', fields, onSubmit: values => {
        const latest = guard(revision, captured);
        P.validate(S.ruleFields({type, record: current, mailboxes: data().mailboxes}), values);
        const row = S.normalizeRule(type, values, current?.id || P.uid());
        const candidate = {...latest.draft, rules: current ? latest.draft.rules.map(rule => rule.id === row.id ? row : rule) : [...latest.draft.rules, row]};
        const issue = S.issue(candidate, data().mailboxes); if (issue) throw new Error(issue);
        latest.draft = candidate; changed(latest);
      }});
    }
    async function remove(record) {
      const cfg = prepare(), revision = cfg.revision, captured = context();
      if (!cfg.draft.rules.some(rule => rule.id === record.id)) throw new Error('This draft rule no longer exists.');
      if (!await UI.Dialog.confirm({title: 'Remove draft rule', message: record.name + '. The published version is retained until publication.'})) return;
      const latest = guard(revision, captured);
      latest.draft.rules = latest.draft.rules.filter(rule => rule.id !== record.id); changed(latest);
    }
    function options() {
      const cfg = prepare(), revision = cfg.revision, captured = context(), fields = S.optionFields(cfg.draft.options, data().mailboxes);
      P.form({title: 'Catch-all and retention draft', description: 'Local policy metadata only. This prototype never archives or deletes mail. ROOT legal holds and transport configuration are outside this account preview.', fields, onSubmit: values => {
        const latest = guard(revision, captured); P.validate(S.optionFields(latest.draft.options, data().mailboxes), values);
        const candidate = {...latest.draft, options: S.normalizeOptions(values)};
        const issue = S.issue(candidate, data().mailboxes); if (issue) throw new Error(issue);
        latest.draft = candidate; changed(latest);
      }});
    }
    function validate() {
      const cfg = prepare(), issue = S.issue(cfg.draft, data().mailboxes); if (issue) throw new Error(issue);
      cfg.evidence = {revision: cfg.revision, context: context(), snapshot: fingerprint(cfg.draft)};
      bridge.save(); bridge.render(); bridge.notify('Draft validated locally. Publication remains a separate simulated operation.');
    }
    function preview(published = false) {
      const cfg = prepare(), snapshot = structuredClone(published ? cfg.published : cfg.draft), revision = published ? cfg.publishedRevision : cfg.revision, captured = context();
      if (!data().mailboxes.some(box => box.status === 'Active')) throw new Error('An active mailbox is required for a message preview.');
      const issue = S.issue(snapshot, data().mailboxes); if (issue) throw new Error(issue);
      const fields = S.messageFields(data().mailboxes);
      P.form({title: 'Preview ' + (published ? 'published policy' : 'draft revision ' + revision), description: 'An invented message, evaluated locally. No delivery, SMTP, DNS or authentication observation is collected.', fields, submitLabel: 'Calculate preview', onSubmit: values => {
        sync(); if (context() !== captured) throw new Error('The mailbox inventory changed. Preview again.');
        P.validate(S.messageFields(data().mailboxes), values);
        const result = S.preview(snapshot, data().mailboxes, values), content = document.createElement('pre');
        content.className = 'mail-policy-preview'; content.textContent = JSON.stringify({revision, ...result}, null, 2);
        UI.Dialog.open({title: 'Calculated mail policy preview', content});
      }});
    }
    function publish(version) {
      const cfg = prepare(); if (busy()) throw new Error('A mail policy publication is already pending.');
      const retained = version && cfg.history.find(item => item.id === version.id);
      if (version && !retained) throw new Error('The retained version is unavailable.');
      const snapshot = structuredClone(retained ? retained.snapshot : cfg.draft), revision = retained ? retained.revision : cfg.revision;
      const issue = S.issue(snapshot, data().mailboxes); if (issue) throw new Error(issue);
      if (fingerprint(snapshot) === fingerprint(cfg.published)) throw new Error('There are no changes to publish.');
      const captured = context(), generation = cfg.generation, base = fingerprint(cfg.published), stamp = fingerprint(snapshot);
      if (!retained && (!cfg.evidence || cfg.evidence.revision !== revision || cfg.evidence.context !== captured || cfg.evidence.snapshot !== stamp)) throw new Error('Validate this exact draft with the current mailboxes first.');
      const fields = [{name: 'reason', label: 'Change reason', type: 'textarea', required: true, minLength: 5, maxLength: 300}, {name: 'health', label: 'Simulated activation result', type: 'select', required: true, options: ['Pass', 'Fail'], default: 'Pass'}];
      P.form({title: retained ? 'Restore retained mail policy' : 'Review mail policy publication', description: 'Revision ' + revision + ', ' + snapshot.rules.filter(rule => rule.enabled).length + ' enabled rules. Unknown recipients: ' + snapshot.options.catchAll + '. Only this snapshot is queued; later draft edits are preserved.', fields, submitLabel: 'Queue publication simulation', onSubmit: values => {
        const latest = prepare(); P.validate(fields, values);
        if (busy() || latest.generation !== generation || fingerprint(latest.published) !== base || context() !== captured || !retained && (latest.revision !== revision || fingerprint(latest.draft) !== stamp)) throw new Error('The draft, inventory or published version changed. Review again.');
        if (retained && !latest.history.some(item => item.id === retained.id && fingerprint(item.snapshot) === stamp)) throw new Error('The retained version changed. Review again.');
        const problem = S.issue(snapshot, data().mailboxes); if (problem) throw new Error(problem);
        const kind = retained ? 'rollback' : 'publish', job = P.job({name: 'Publish mail policy revision ' + revision, kind: 'mail-policy-' + kind, scope: 'user'});
        latest.operations.unshift({id: P.uid(), jobId: job.id, kind, revision, snapshot, context: captured, baseGeneration: generation, basePublished: base, reason: values.reason.trim(), health: values.health, status: job.status, progress: job.progress, applied: false, createdAt: new Date().toISOString()});
        latest.operations = latest.operations.slice(0, 100); bridge.save(); bridge.render();
      }});
    }
    async function discard() {
      const cfg = prepare(), revision = cfg.revision, generation = cfg.generation, captured = context();
      if (busy()) throw new Error('Wait for the pending publication first.');
      if (!await UI.Dialog.confirm({title: 'Discard mail policy draft', message: 'Restore the published rules and retention settings.'})) return;
      const latest = guard(revision, captured); if (busy() || latest.generation !== generation) throw new Error('The published version changed. Review again.');
      latest.draft = structuredClone(latest.published); changed(latest);
    }
    async function repair() {
      bridge.refresh();
      if (!await UI.Dialog.confirm({title: 'Reset invalid mail policy storage', message: 'This replaces the invalid policy section with a disabled migration draft. Mailboxes, routes and preferences are preserved.', typed: 'RESET MAIL POLICY'})) return;
      bridge.refresh();
      // Do not erase a valid policy repaired in another tab while the dialog was open.
      try { config(); throw new Error('Mail policy is now valid. Use Discard draft if needed.'); } catch (error) { if (!error.message.includes('storage is invalid')) throw error; }
      data().mailPolicy = S.seed(data().mailboxes, data().responders, data().settings); bridge.save(); bridge.render();
    }

    const button = (action, label, primary = false) => `<button type="button" class="wcp-action-btn ${primary ? 'primary' : ''}" data-mail-policy-action="${action}">${label}</button>`;
    function render(tab, host) {
      destroy(); let cfg;
      try { sync(); cfg = config(); } catch (error) { host.innerHTML = `<section class="mail-card"><h3>Policy unavailable</h3><p>${esc(error.message)}</p>${button('repair', 'Repair policy storage')}</section>`; bind(host); return; }
      const valid = cfg.evidence?.revision === cfg.revision && cfg.evidence.context === context() && cfg.evidence.snapshot === fingerprint(cfg.draft);
      host.innerHTML = `<div class="mail-policy-summary"><span>Draft revision <strong>${cfg.revision}</strong></span><span>Published generation <strong>${cfg.generation}</strong></span><span>${valid ? 'Draft validated locally' : 'Draft needs validation'}</span><span>${busy() ? 'Publication pending' : 'No pending publication'}</span></div><div class="mail-policy-actions">${button('validate', 'Validate draft')}${button('publish', 'Review & publish', true)}${button('discard', 'Discard draft')}</div><p class="mail-policy-note">Local simulation. Published policies change only after the matching account job succeeds. Filters support literal subject/sender matches and numeric spam scores; no regular expressions, real message content or mail transport operations.</p><div data-mail-policy-content></div>`;
      const content = host.querySelector('[data-mail-policy-content]');
      if (tab === 'rules') {
        content.innerHTML = `<div class="mail-section-head"><div><strong>Draft filters & automatic responses</strong><small>Ordered filters; one enabled responder per mailbox. Disabled legacy responses need message text and UTC dates.</small></div><div class="mail-policy-actions">${button('filter', 'Add filter')}${button('responder', 'Add response')}</div></div><div data-mail-policy-grid></div>`;
        grid = UI.DataGrid.mount(content.querySelector('[data-mail-policy-grid]'), {id: 'mail-policy-rules', title: 'Draft mail rules', rows: cfg.draft.rules, store: UI.createStore({app: 'core', scope: 'user', owner: 'example.com', module: 'mail-policy-grid'}), columns: [{key: 'name', label: 'Name'}, {key: 'type', label: 'Type'}, {key: 'mailboxId', label: 'Mailbox', value: row => row.mailboxId === '*' ? 'All active mailboxes' : data().mailboxes.find(box => box.id === row.mailboxId)?.address || 'Unavailable mailbox'}, {key: 'priority', label: 'Priority', type: 'number'}, {key: 'enabled', label: 'Enabled'}, {key: 'migrationNote', label: 'Migration note', hidden: true}], rowActions: [{label: 'Edit', run: P.safe(row => edit(row.type, row))}, {label: 'Remove', run: P.safe(remove)}]});
      } else if (tab === 'retention') {
        const describe = options => options.catchAll === 'reject' ? 'Reject unknown recipients' : 'Deliver to ' + (data().mailboxes.find(box => box.id === options.catchAllMailbox)?.address || 'unavailable mailbox');
        content.innerHTML = `<div class="mail-section-head"><div><strong>Catch-all & retention</strong><small>One published policy, no background message deletion in this prototype.</small></div>${button('options', 'Edit draft options')}</div><div class="mail-routing-grid">${[['Draft', cfg.draft.options], ['Published', cfg.published.options]].map(([label, options]) => `<section class="mail-card"><h3>${label}</h3><p>${esc(describe(options))}</p><p>Archive after: ${esc(options.archiveAfterDays)} days</p><p>Trash: ${esc(options.trashAfterDays)} days · Quarantine: ${esc(options.quarantineDays)} days</p></section>`).join('')}</div>`;
      } else if (tab === 'trace') {
        content.innerHTML = `<section class="mail-card"><h3>Message decision preview</h3><p>Choose a draft or published snapshot and enter an invented message. The result explains matching filters, response suppression and retention metadata. It is not a delivery log.</p><div class="mail-policy-actions">${button('preview-draft', 'Preview draft message')}${button('preview-published', 'Preview published message')}</div><p>Delivery samples, SMTP replies, SPF, DKIM and DMARC observations are not collected here.</p></section>`;
      } else {
        content.innerHTML = '<h3>Publication activity</h3><div data-mail-policy-grid></div><h3>Retained published versions</h3><div data-mail-policy-history></div>';
        grid = UI.DataGrid.mount(content.querySelector('[data-mail-policy-grid]'), {id: 'mail-policy-activity', title: 'Mail policy operations', rows: cfg.operations, columns: [{key: 'createdAt', label: 'Created'}, {key: 'revision', label: 'Revision', type: 'number'}, {key: 'status', label: 'Job status'}, {key: 'result', label: 'Policy result'}, {key: 'reason', label: 'Reason'}]});
        const history = content.querySelector('[data-mail-policy-history]');
        history.innerHTML = cfg.history.length ? cfg.history.map(item => `<article class="responder-row"><span class="material-symbols-rounded">history</span><div><strong>Generation ${esc(item.generation)}</strong><small>Revision ${esc(item.revision)}</small></div><button type="button" class="wcp-action-btn" data-mail-policy-restore="${esc(item.id)}">Review restore</button></article>`).join('') : '<p>No earlier published version retained yet.</p>';
        history.querySelectorAll('[data-mail-policy-restore]').forEach(control => control.addEventListener('click', P.safe(() => publish(cfg.history.find(item => item.id === control.dataset.mailPolicyRestore)))));
      }
      bind(host);
    }
    function bind(host) { host.querySelectorAll('[data-mail-policy-action]').forEach(control => control.addEventListener('click', P.safe(() => action(control.dataset.mailPolicyAction)))); }
    function action(id) {
      if (id === 'filter') return edit('Filter'); if (id === 'responder') return edit('Autoresponder');
      if (id === 'options') return options(); if (id === 'validate') return validate(); if (id === 'publish') return publish();
      if (id === 'discard') return discard(); if (id === 'repair') return repair();
      if (id === 'preview-draft' || id === 'preview-published') return preview(id === 'preview-published');
    }
    function destroy() { grid?.destroy(); grid = null; }
    // An invalid saved policy must not prevent the mailbox workspace from opening.
    try { config(); } catch {}
    return Object.freeze({tabs, render, destroy, sync, action, edit, remove, options, validate, preview, publish, discard});
  }});
})(globalThis);
