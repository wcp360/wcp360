/* Account mail policies. Local metadata and deterministic previews only. */
((global) => {
  'use strict';
  const UI = global.WCPUI;
  const active = boxes => boxes.filter(box => box.status === 'Active');
  const text = value => String(value ?? '').trim();
  const utc = value => /^\d{4}-\d\d-\d\dT\d\d:\d\d$/.test(value) && Number.isFinite(Date.parse(value + 'Z')) && new Date(value + 'Z').toISOString().slice(0, 16) === value;
  const defaults = Object.freeze({catchAll: 'reject', catchAllMailbox: '', archiveAfterDays: 365, trashAfterDays: 30, quarantineDays: 30});

  function seed(mailboxes, responders = [], settings = {}) {
    const rules = responders.map(item => ({
      id: item.id, type: 'Autoresponder', name: item.subject || 'Legacy response',
      mailboxId: mailboxes.find(box => box.address === item.mailbox)?.id || '', enabled: false,
      subject: item.subject || '', message: '', starts: '', ends: '', intervalHours: 24,
      migrationNote: 'Legacy schedule has no complete message or UTC dates. Complete and enable this draft explicitly.'
    }));
    // Old responders did not retain a message or usable schedule. Do not fabricate an active version.
    const catchAllBox = settings.catchAll === 'contact' ? active(mailboxes).find(box => box.address === 'contact@example.com') : settings.catchAll === 'mailbox' ? active(mailboxes).find(box => box.id === settings.catchAllMailbox) : null;
    const options = {...defaults, catchAll: catchAllBox ? 'mailbox' : 'reject', catchAllMailbox: catchAllBox?.id || '', quarantineDays: [7, 30, 60].includes(settings.quarantineDays) ? settings.quarantineDays : 30};
    return {schema: 1, revision: 1, publishedRevision: 0, generation: 0,
      draft: {rules, options}, published: {rules: [], options: {...options}}, evidence: null, history: [], operations: []};
  }

  function ruleFields({type = 'Filter', record, mailboxes = []} = {}) {
    const boxes = active(mailboxes), r = record || {};
    return [
      {name: 'name', label: 'Rule name', required: true, maxLength: 80, default: r.name || ''},
      {name: 'mailboxId', label: 'Mailbox', type: 'select', required: true,
        options: [...(type === 'Filter' ? [{value: '*', label: 'All active mailboxes'}] : []), ...boxes.map(box => ({value: box.id, label: box.address}))],
        default: r.mailboxId || (type === 'Filter' ? '*' : boxes[0]?.id || ''),
        validate: value => (type === 'Filter' && value === '*') || boxes.some(box => box.id === value) ? '' : 'Select an active mailbox.'},
      {name: 'enabled', label: 'Enable in the next published version', type: 'checkbox', default: r.enabled ?? true},
      ...(type === 'Filter' ? [
        {name: 'priority', label: 'Priority (lowest first)', type: 'number', required: true, min: 1, max: 1000, default: r.priority || 10, validate: value => Number.isInteger(Number(value)) ? '' : 'Use a whole-number priority.'},
        {name: 'field', label: 'Message field', type: 'select', required: true, options: ['subject', 'sender', 'spamScore'], default: r.field || 'subject'},
        {name: 'operator', label: 'Match', type: 'select', required: true, options: ['contains', 'equals', 'atLeast'], default: r.operator || 'contains'},
        {name: 'value', label: 'Match value', required: true, maxLength: 200, default: r.value || '', help: 'Subject/sender: contains or equals. Spam score: atLeast, from 0 to 100.'},
        {name: 'action', label: 'Action', type: 'select', required: true, options: ['Move to folder', 'Tag', 'Quarantine', 'Reject'], default: r.action || 'Move to folder'},
        {name: 'target', label: 'Folder or tag', maxLength: 80, default: r.target || '', help: 'Required for folder/tag actions; no filesystem paths.'},
        {name: 'stop', label: 'Stop after this matching filter', type: 'checkbox', default: r.stop ?? true}
      ] : [
        {name: 'subject', label: 'Response subject', required: true, maxLength: 150, default: r.subject || ''},
        {name: 'message', label: 'Response text', type: 'textarea', required: true, maxLength: 4000, default: r.message || ''},
        {name: 'starts', label: 'Starts (UTC)', type: 'datetime-local', required: true, default: r.starts || '', validate: value => utc(value) ? '' : 'Enter a valid UTC date and time.'},
        {name: 'ends', label: 'Ends (UTC)', type: 'datetime-local', required: true, default: r.ends || '', validate: (value, values) => utc(value) && value > values.starts ? '' : 'The UTC end must be after the start.'},
        {name: 'intervalHours', label: 'Minimum hours between replies to one sender', type: 'number', required: true, min: 1, max: 168, default: r.intervalHours || 24, validate: value => Number.isInteger(Number(value)) ? '' : 'Use whole hours.'}
      ])
    ];
  }

  function optionFields(options = defaults, mailboxes = []) {
    const boxes = active(mailboxes);
    return [
      {name: 'catchAll', label: 'Unknown recipients', type: 'select', required: true, options: ['reject', 'mailbox'], default: options.catchAll},
      {name: 'catchAllMailbox', label: 'Catch-all mailbox', type: 'select', options: [{value: '', label: 'None'}, ...boxes.map(box => ({value: box.id, label: box.address}))], default: options.catchAllMailbox,
        validate: (value, values) => values.catchAll !== 'mailbox' || boxes.some(box => box.id === value) ? '' : 'Select an active catch-all mailbox.'},
      {name: 'archiveAfterDays', label: 'Archive after (days, metadata preview)', type: 'number', required: true, min: 7, max: 3650, default: options.archiveAfterDays},
      {name: 'trashAfterDays', label: 'Trash retention (days, metadata preview)', type: 'number', required: true, min: 7, max: 365, default: options.trashAfterDays},
      {name: 'quarantineDays', label: 'Quarantine retention (days)', type: 'select', required: true, options: [7, 30, 60].map(value => ({value: String(value), label: value + ' days'})), default: String(options.quarantineDays)}
    ];
  }

  function normalizeRule(type, values, id) {
    const row = {id, type, ...values, name: text(values.name), enabled: !!values.enabled};
    if (type === 'Filter') Object.assign(row, {priority: Number(values.priority), value: text(values.value), target: text(values.target), stop: !!values.stop});
    else Object.assign(row, {subject: text(values.subject), message: text(values.message), intervalHours: Number(values.intervalHours)});
    return row;
  }
  const normalizeOptions = values => ({...values, catchAllMailbox: values.catchAll === 'mailbox' ? values.catchAllMailbox : '', archiveAfterDays: Number(values.archiveAfterDays), trashAfterDays: Number(values.trashAfterDays), quarantineDays: Number(values.quarantineDays)});

  function issue(config, mailboxes) {
    if (!config || !Array.isArray(config.rules) || !config.options || config.rules.length > 100) return 'Invalid mail policy structure (maximum 100 rules).';
    const opts = UI.validateValues(optionFields(config.options, mailboxes), config.options);
    if (!opts.valid) return Object.values(opts.errors)[0];
    if (![config.options.archiveAfterDays, config.options.trashAfterDays, config.options.quarantineDays].every(Number.isInteger)) return 'Retention values must be whole days.';
    const ids = new Set(), names = new Set(), enabled = [];
    for (const row of config.rules) {
      if (!row || typeof row.id !== 'string' || !row.id || ids.has(row.id) || !['Filter', 'Autoresponder'].includes(row.type) || typeof row.enabled !== 'boolean') return 'Invalid or duplicate rule identity.';
      ids.add(row.id);
      const name = UI.normalize(text(row.name));
      if (!name || names.has(name)) return 'Rule names must be unique.';
      names.add(name);
      // Incomplete imported responders are visible, disabled drafts only.
      if (!row.enabled && row.migrationNote) continue;
      const result = UI.validateValues(ruleFields({type: row.type, record: row, mailboxes}), row);
      if (!result.valid) return row.name + ': ' + Object.values(result.errors)[0];
      if (row.type === 'Filter') {
        if (!Number.isInteger(row.priority)) return 'Filter priority must be a whole number.';
        if (row.field === 'spamScore' ? row.operator !== 'atLeast' || !/^(?:\d+(?:\.\d+)?)$/.test(row.value) || Number(row.value) > 100 : row.operator === 'atLeast') return 'Match operator does not fit the message field.';
        if (['Move to folder', 'Tag'].includes(row.action) && (!/^[\p{L}\p{N} _.-]{1,80}$/u.test(row.target) || row.target.includes('..') || row.target === '.')) return 'Use a folder/tag label without paths or parent traversal.';
        if (row.enabled && enabled.some(other => other.type === 'Filter' && other.priority === row.priority && (other.mailboxId === '*' || row.mailboxId === '*' || other.mailboxId === row.mailboxId))) return 'Overlapping filters must have different priorities.';
      } else if (row.enabled && enabled.some(other => other.type === 'Autoresponder' && other.mailboxId === row.mailboxId)) return 'Only one enabled autoresponder is allowed per mailbox.';
      if (row.enabled) enabled.push(row);
    }
    return '';
  }

  function messageFields(mailboxes) {
    return [
      {name: 'mailboxId', label: 'Recipient mailbox', type: 'select', required: true, options: active(mailboxes).map(box => ({value: box.id, label: box.address})), default: active(mailboxes)[0]?.id || ''},
      {name: 'unknownRecipient', label: 'Simulate an unknown local recipient (unknown@example.com)', type: 'checkbox', default: false},
      {name: 'sender', label: 'Sender', type: 'email', required: true, default: 'visitor@example.net'},
      {name: 'subject', label: 'Subject', maxLength: 300, default: 'Invoice request'},
      {name: 'spamScore', label: 'Spam score (0–100)', type: 'number', required: true, min: 0, max: 100, default: 0},
      {name: 'at', label: 'Message time (UTC)', type: 'datetime-local', required: true, default: new Date().toISOString().slice(0, 16), validate: value => utc(value) ? '' : 'Enter a valid UTC date and time.'},
      {name: 'automated', label: 'Automated sender or mailing list', type: 'checkbox', default: false},
      {name: 'lastReply', label: 'Last reply to this sender (UTC, optional)', type: 'datetime-local', default: '', validate: (value, values) => !value || utc(value) && value <= values.at ? '' : 'Last reply must be a valid earlier UTC date.'}
    ];
  }

  function preview(config, mailboxes, message) {
    const problem = issue(config, mailboxes);
    if (problem) throw new Error(problem);
    const values = UI.validateValues(messageFields(mailboxes), message);
    if (!values.valid) throw new Error(Object.values(values.errors)[0]);
    const steps = [], tags = [];
    const mailboxId = message.unknownRecipient ? config.options.catchAll === 'mailbox' ? config.options.catchAllMailbox : null : message.mailboxId;
    if (message.unknownRecipient) steps.push({rule: 'Unknown recipient policy', action: mailboxId ? 'Catch-all delivery' : 'Reject unknown recipient'});
    if (!mailboxId) return {simulation: true, recipient: 'unknown@example.com', disposition: 'Reject unknown recipient', tags, response: 'Suppressed: unknown recipient rejected', steps, note: 'No mail sent, moved, archived or deleted. Local policy preview only.'};
    let disposition = 'Inbox';
    for (const row of config.rules.filter(row => row.enabled && row.type === 'Filter').sort((a, b) => a.priority - b.priority)) {
      if (row.mailboxId !== '*' && row.mailboxId !== mailboxId) continue;
      const value = row.field === 'sender' ? message.sender : row.field === 'subject' ? message.subject : message.spamScore;
      const match = row.operator === 'atLeast' ? Number(value) >= Number(row.value) : row.operator === 'equals' ? UI.normalize(String(value)) === UI.normalize(row.value) : UI.normalize(String(value)).includes(UI.normalize(row.value));
      if (!match) continue;
      steps.push({rule: row.name, action: row.action, target: row.target});
      if (row.action === 'Tag') tags.push(row.target);
      else disposition = row.action === 'Move to folder' ? 'Folder: ' + row.target : row.action;
      if (row.stop || ['Reject', 'Quarantine'].includes(row.action)) break;
    }
    const responder = config.rules.find(row => row.enabled && row.type === 'Autoresponder' && row.mailboxId === mailboxId);
    let response = 'No active responder';
    if (responder) {
      response = message.automated ? 'Suppressed: automated/list sender' : message.at < responder.starts || message.at >= responder.ends ? 'Outside schedule' : ['Reject', 'Quarantine'].includes(disposition) ? 'Suppressed: message not delivered' : message.lastReply && (Date.parse(message.at + 'Z') - Date.parse(message.lastReply + 'Z')) / 3600000 < responder.intervalHours ? 'Suppressed: reply interval' : 'Would reply: ' + responder.subject;
    }
    return {simulation: true, recipient: message.unknownRecipient ? 'unknown@example.com' : mailboxes.find(box => box.id === mailboxId)?.address, resolvedMailbox: mailboxes.find(box => box.id === mailboxId)?.address, disposition, tags, response, steps,
      retention: {archiveAfterDays: config.options.archiveAfterDays, trashAfterDays: config.options.trashAfterDays, quarantineDays: config.options.quarantineDays}, note: 'No mail sent, moved, archived or deleted. No transport or authentication checks are performed.'};
  }

  const context = boxes => JSON.stringify(boxes.map(({id, address, status}) => ({id, address, status})).sort((a, b) => a.id.localeCompare(b.id)));
  function clientFields(mailboxes, grants = []) {
    return [
      {name: 'name', label: 'Demo device or application', required: true, maxLength: 80, validate: (value, values) => grants.some(item => item.status === 'Active' && item.mailboxId === values.mailboxId && UI.normalize(item.name) === UI.normalize(text(value))) ? 'An active device with this name already exists for this mailbox.' : ''},
      {name: 'mailboxId', label: 'Mailbox', type: 'select', required: true, options: active(mailboxes).map(box => ({value: box.id, label: box.address})), default: active(mailboxes)[0]?.id || ''},
      {name: 'protocol', label: 'Protocol metadata', type: 'select', required: true, options: ['IMAP + SMTP', 'SMTP only', 'IMAP only'], default: 'IMAP + SMTP'}
    ];
  }
  global.WCPMailRuleSchemas = Object.freeze({defaults, seed, ruleFields, optionFields, normalizeRule, normalizeOptions, issue, messageFields, preview, context, utc, clientFields});
})(globalThis);
