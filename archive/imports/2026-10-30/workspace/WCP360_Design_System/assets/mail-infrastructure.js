(function () {
  if (document.body.dataset.mailCenter !== 'root') return;
  const qs = (selector, context = document) => context.querySelector(selector);
  const qsa = (selector, context = document) => [...context.querySelectorAll(selector)];
  const tabs = [
    ['overview', 'space_dashboard', 'Overview'],
    ['nodes', 'dns', 'Nodes & profiles'],
    ['smtp', 'outgoing_mail', 'SMTP & routing'],
    ['imap', 'inbox', 'IMAP & storage'],
    ['security', 'shield_lock', 'Security'],
    ['reputation', 'monitoring', 'Reputation'],
    ['webmail', 'alternate_email', 'Webmail & SSO'],
    ['queue', 'outbox', 'Queue'],
    ['history', 'history', 'History']
  ];
  const infraKey = 'wcp360-mail-infrastructure-v1';
  const webmailKey = 'wcp360-webmail-clients-v1';
  const defaults = {
    profiles: [
      { id: 'production', name: 'Production Mail', smtp: 'Postfix', imap: 'Dovecot', security: 'Rspamd + ClamAV', nodes: 3, revision: 'v18', status: 'Published' },
      { id: 'transactional', name: 'Transactional Relay', smtp: 'Postfix relay', imap: 'Disabled', security: 'Rspamd', nodes: 1, revision: 'v9', status: 'Published' },
      { id: 'groupware', name: 'Groupware', smtp: 'Postfix', imap: 'Dovecot', security: 'Rspamd + SOGo', nodes: 2, revision: 'v7', status: 'Canary' }
    ],
    queue: [
      { id: '1tZ8A4-0007Xq-2M', sender: 'billing@example.com', recipient: 'gmail.com', age: '4m', node: 'mail-par-01', reason: 'Normal delivery', status: 'Queued' },
      { id: '1tZ89s-0007V2-0K', sender: 'hello@example.com', recipient: 'outlook.com', age: '18m', node: 'mail-par-02', reason: 'Remote rate limit', status: 'Deferred' },
      { id: '1tZ7pL-0006za-3D', sender: 'noreply@example.com', recipient: 'example.net', age: '2h', node: 'mail-par-01', reason: 'Repeated 550 response', status: 'Frozen' },
      { id: '1tZ8D1-0007aA-1P', sender: 'support@example.com', recipient: 'proton.me', age: '1m', node: 'mail-ams-01', reason: 'Normal delivery', status: 'Queued' },
      { id: '1tZ72B-0005tz-4A', sender: 'alerts@example.com', recipient: 'yahoo.com', age: '36m', node: 'mail-par-03', reason: 'Greylisting retry', status: 'Deferred' }
    ]
  };
  const webmailDefaults = {
    globalEnabled: true, userChoice: true, defaultClient: 'roundcube', sessionMinutes: 480, enforceTls: true, autoUpdates: true,
    plans: ['Business', 'Agency', 'Reseller'],
    clients: [
      { id: 'roundcube', name: 'Roundcube', icon: 'mail', enabled: true, endpoint: 'https://mail.example.com/roundcube/', version: '1.6.11', auth: 'Signed SSO token', channel: 'Stable', status: 'healthy', description: 'Classic IMAP webmail with contacts, identities, filters and plugins.' },
      { id: 'snappymail', name: 'SnappyMail', icon: 'bolt', enabled: true, endpoint: 'https://mail.example.com/snappymail/', version: '2.38.2', auth: 'Signed SSO token', channel: 'Stable', status: 'healthy', description: 'Lightweight modern webmail with multi-account and OpenPGP support.' },
      { id: 'sogo', name: 'SOGo', icon: 'event_available', enabled: false, endpoint: 'https://mail.example.com/SOGo/', version: '5.12.4', auth: 'OpenID Connect', channel: 'Stable', status: 'maintenance', description: 'Groupware interface for mail, calendars, contacts and shared resources.' }
    ]
  };
  const read = (key) => { try { return JSON.parse(localStorage.getItem(key)) || {}; } catch (error) { return {}; } };
  const stored = read(infraKey);
  const state = {
    profiles: Array.isArray(stored.profiles) ? stored.profiles : defaults.profiles,
    queue: Array.isArray(stored.queue) ? stored.queue : defaults.queue
  };
  const storedWebmail = read(webmailKey);
  let webmail = {
    ...webmailDefaults,
    ...storedWebmail,
    clients: webmailDefaults.clients.map(client => ({ ...client, ...(Array.isArray(storedWebmail.clients) ? storedWebmail.clients.find(item => item.id === client.id) : {}) }))
  };
  const saveInfra = () => { try { localStorage.setItem(infraKey, JSON.stringify(state)); } catch (error) {} };
  const saveWebmail = () => { try { localStorage.setItem(webmailKey, JSON.stringify(webmail)); } catch (error) {} };
  const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, character => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[character]));
  let active = tabs.some(tab => tab[0] === location.hash.slice(1)) ? location.hash.slice(1) : 'overview';
  let returnFocus = null;

  const chip = (label, tone = '') => `<span class="wcp-status ${tone}">${escapeHtml(label)}</span>`;
  const action = (kind, label, primary = false, icon = '') => `<button type="button" class="wcp-action-btn ${primary ? 'primary' : ''}" data-mail-action="${kind}">${icon ? `<span class="material-symbols-rounded">${icon}</span>` : ''}${label}</button>`;
  const panelHead = (title, copy, controls = '') => `<div class="wcp-panel-head mic-panel-head"><div><div class="wcp-panel-title">${title}</div><div class="wcp-panel-sub">${copy}</div></div><div class="mic-actions">${controls}</div></div>`;
  const kv = (items) => `<div class="mic-kv">${items.map(item => `<div><span>${item[0]}</span><strong>${item[1]}</strong></div>`).join('')}</div>`;
  const config = (label, value, copy) => `<article class="mic-config"><span>${label}</span><strong>${value}</strong><small>${copy}</small></article>`;
  const listRow = (icon, title, copy, status, tone = '') => `<div class="mic-list-row"><span class="material-symbols-rounded">${icon}</span><div><strong>${title}</strong><small>${copy}</small></div>${chip(status, tone)}</div>`;
  const note = (icon, title, copy) => `<div class="mic-note"><span class="material-symbols-rounded">${icon}</span><div><strong>${title}</strong><p>${copy}</p></div></div>`;
  const profileCard = profile => `<article class="mic-card" data-mail-profile="${profile.id}"><div class="mic-card-head"><span class="mic-card-icon"><span class="material-symbols-rounded">dns</span></span>${chip(profile.status, profile.status === 'Published' ? 'success' : 'warning')}</div><h4>${escapeHtml(profile.name)}</h4><p>${escapeHtml(profile.smtp)} · ${escapeHtml(profile.imap)} · ${escapeHtml(profile.security)}</p>${kv([['Nodes', profile.nodes], ['Revision', profile.revision], ['Config drift', profile.status === 'Canary' ? '1 canary override' : 'None']])}<footer><span>Signed profile</span><button class="wcp-action-btn" data-mail-action="edit-profile">Edit</button></footer></article>`;

  const views = {
    overview: () => `<div class="mic-section" data-mail-view="overview"><div class="mic-section-head"><div><h3>Mail delivery control plane</h3><p>Operate SMTP, IMAP, mailbox storage, antispam, authentication, reputation, external webmail and durable queues from one authoritative ROOT surface.</p></div><div class="mic-actions">${action('health-check','Run health checks',false,'health_and_safety')}${action('create-profile','Create mail profile',true,'add_box')}</div></div>
      <section class="wcp-panel">${panelHead('End-to-end message path','Every handoff is encrypted, measured and tied to one policy revision')}<div class="mic-flow"><div class="mic-flow-step"><span class="material-symbols-rounded">public</span><strong>Internet</strong><small>MX · MTA-STS</small></div><div class="mic-flow-step"><span class="material-symbols-rounded">filter_alt</span><strong>Edge SMTP</strong><small>RBL · greylist</small></div><div class="mic-flow-step"><span class="material-symbols-rounded">security</span><strong>Security</strong><small>Rspamd · ClamAV</small></div><div class="mic-flow-step"><span class="material-symbols-rounded">outbox</span><strong>Transport</strong><small>Postfix · queue</small></div><div class="mic-flow-step"><span class="material-symbols-rounded">inbox</span><strong>Mailbox</strong><small>Dovecot · quota</small></div><div class="mic-flow-step"><span class="material-symbols-rounded">alternate_email</span><strong>Webmail</strong><small>External SSO</small></div><div class="mic-flow-step"><span class="material-symbols-rounded">monitoring</span><strong>Evidence</strong><small>Logs · DMARC</small></div></div></section>
      <div class="mic-grid two"><section class="wcp-panel">${panelHead('Service health','Synthetic delivery, authentication and storage checks')}<div class="mic-list">${listRow('outgoing_mail','SMTP transport','4 / 4 nodes · inbound and outbound probes','Healthy','success')}${listRow('inbox','IMAP service','3 / 3 nodes · login and mailbox probes','Healthy','success')}${listRow('shield','Filtering stack','One ClamAV signature update pending','Review','warning')}${listRow('alternate_email','Webmail SSO','2 enabled providers · 184 sessions / 24h','Healthy','success')}</div></section><section class="wcp-panel">${panelHead('Operational risks','Actionable signals across delivery and reputation')}<div class="mic-list">${listRow('schedule','Deferred queue','126 messages · 18 provider throttles','Attention','warning')}${listRow('verified_user','Authentication coverage','98.7% DKIM · 96% DMARC enforcement','Strong','success')}${listRow('speed','Outbound reputation','Shared pool score 98 / 100','Healthy','success')}${listRow('hard_drive','Mailbox storage','68% used · forecast 93 days','Normal','success')}</div></section></div>
      ${note('account_tree','Clear ownership boundary','ROOT operates mail nodes and global policy. RESELLER may narrow mailbox features and limits. USER manages only owned mailboxes, routes and approved webmail clients.')}
    </div>`,

    nodes: () => `<div class="mic-section" data-mail-view="nodes"><div class="mic-section-head"><div><h3>Mail nodes, roles and signed profiles</h3><p>Assign explicit SMTP, IMAP, filtering, webmail-proxy and archive roles without hiding service ownership or dependencies.</p></div><div class="mic-actions">${action('add-node','Enroll node')}${action('create-profile','Create profile',true,'add_box')}</div></div>
      <section class="wcp-panel">${panelHead('Fleet inventory','Roles, profile revision, capacity and observed state')}<div class="mic-table-wrap"><table class="mic-table"><thead><tr><th>Node</th><th>Region</th><th>Roles</th><th>Profile</th><th>Queue</th><th>Storage</th><th>State</th><th></th></tr></thead><tbody>
      <tr data-mail-node><td><strong>mail-par-01</strong><small>203.0.113.24</small></td><td>Paris</td><td>SMTP · IMAP · filter</td><td>Production v18</td><td>642</td><td>61%</td><td>${chip('Healthy','success')}</td><td>${action('inspect-node','Inspect')}</td></tr>
      <tr data-mail-node><td><strong>mail-par-02</strong><small>203.0.113.25</small></td><td>Paris</td><td>SMTP · IMAP · filter</td><td>Production v18</td><td>711</td><td>72%</td><td>${chip('Healthy','success')}</td><td>${action('inspect-node','Inspect')}</td></tr>
      <tr data-mail-node><td><strong>mail-ams-01</strong><small>203.0.113.52</small></td><td>Amsterdam</td><td>SMTP · IMAP · filter</td><td>Production v18</td><td>363</td><td>58%</td><td>${chip('Healthy','success')}</td><td>${action('inspect-node','Inspect')}</td></tr>
      <tr data-mail-node><td><strong>relay-par-01</strong><small>203.0.113.80</small></td><td>Paris</td><td>Outbound relay</td><td>Transactional v9</td><td>126</td><td>—</td><td>${chip('Throttled','warning')}</td><td>${action('inspect-node','Inspect')}</td></tr>
      <tr data-mail-node><td><strong>mail-fra-01</strong><small>203.0.113.91</small></td><td>Frankfurt</td><td>Warm standby</td><td>Production v18</td><td>0</td><td>12%</td><td>${chip('Standby')}</td><td>${action('inspect-node','Inspect')}</td></tr>
      </tbody></table></div></section><div class="mic-grid">${state.profiles.map(profileCard).join('')}</div>
      ${note('rule','Roles are capability contracts','A node cannot receive a mail profile until ports, DNS, certificates, storage, identity sources and package compatibility pass preflight.')}
    </div>`,

    smtp: () => `<div class="mic-section" data-mail-view="smtp"><div class="mic-section-head"><div><h3>SMTP transport, routing and retry policy</h3><p>Control listeners, relay authorization, TLS, provider-specific throttles, route selection, retries and account isolation.</p></div><div class="mic-actions">${action('test-delivery','Test delivery')}${action('edit-smtp','Edit SMTP policy',true,'tune')}</div></div>
      <div class="mic-grid"><article class="mic-card"><div class="mic-card-head"><span class="mic-card-icon"><span class="material-symbols-rounded">move_to_inbox</span></span>${chip('Active','success')}</div><h4>Inbound SMTP</h4><p>Public MX listeners with recipient verification before queue acceptance.</p>${kv([['Ports','25 · 465 · 587'],['TLS','Required on submission'],['Max message','50 MiB'],['Recipient checks','Enabled']])}</article><article class="mic-card"><div class="mic-card-head"><span class="mic-card-icon"><span class="material-symbols-rounded">outgoing_mail</span></span>${chip('Protected','success')}</div><h4>Outbound transport</h4><p>Account-attributed delivery using shared and dedicated reputation pools.</p>${kv([['Smart host','Direct MX'],['Rate baseline','250 / hour / account'],['Bounce limit','8%'],['Compromise action','Quarantine SMTP']])}</article><article class="mic-card"><div class="mic-card-head"><span class="mic-card-icon"><span class="material-symbols-rounded">route</span></span>${chip('4 routes')}</div><h4>Routing</h4><p>Local, remote MX, backup MX and transactional relay behavior.</p>${kv([['Local domains','1,284'],['Remote domains','86'],['Backup MX','12'],['Route validation','Continuous']])}</article></div>
      <section class="wcp-panel">${panelHead('Transport baseline','Typed service schema applied by profile')}<div class="mic-config-grid">${config('SMTP service','Postfix 3.9','Schema mail-smtp v18')}${config('TLS baseline','TLS 1.2 + 1.3','MTA-STS and DANE aware')}${config('Connection limit','40 / IP','Adaptive abuse penalty')}${config('Message ceiling','50 MiB','Plans may reduce')}${config('Retry schedule','15m → 1h → 4h','Expire after 5 days')}${config('Provider shaping','Per destination','Gmail · Microsoft · Yahoo')}${config('Open relay','Always denied','Auth or trusted service CIDR')}${config('Config activation','Canary + drain','Syntax and delivery probes')}</div></section>
      <section class="wcp-panel">${panelHead('Destination policies','Adaptive limits preserve shared reputation')}<div class="mic-table-wrap"><table class="mic-table"><thead><tr><th>Destination</th><th>Connections</th><th>Rate</th><th>TLS</th><th>Deferral behavior</th><th>State</th></tr></thead><tbody><tr><td><strong>Gmail / Google Workspace</strong></td><td>20</td><td>600 / min</td><td>Required</td><td>Adaptive backoff</td><td>${chip('Healthy','success')}</td></tr><tr><td><strong>Microsoft 365 / Outlook</strong></td><td>12</td><td>360 / min</td><td>Required</td><td>Enhanced status parser</td><td>${chip('Throttled','warning')}</td></tr><tr><td><strong>Yahoo / AOL</strong></td><td>8</td><td>180 / min</td><td>Preferred</td><td>Complaint-aware pause</td><td>${chip('Healthy','success')}</td></tr></tbody></table></div></section>
    </div>`,

    imap: () => `<div class="mic-section" data-mail-view="imap"><div class="mic-section-head"><div><h3>IMAP access, mailbox storage and quotas</h3><p>Govern Dovecot listeners, Maildir layout, indexes, quota enforcement, replication, retention and client limits.</p></div>${action('edit-imap','Edit IMAP policy',true,'tune')}</div>
      <div class="mic-grid"><article class="mic-card"><div class="mic-card-head"><span class="mic-card-icon"><span class="material-symbols-rounded">inbox</span></span>${chip('Healthy','success')}</div><h4>Dovecot IMAP</h4><p>Encrypted mailbox access with per-account connection and process limits.</p>${kv([['Listeners','993 · internal LMTP'],['Auth','WCP identity provider'],['Connections','20 / mailbox'],['Idle timeout','30 minutes']])}</article><article class="mic-card"><div class="mic-card-head"><span class="mic-card-icon"><span class="material-symbols-rounded">hard_drive</span></span>${chip('68% used')}</div><h4>Mailbox storage</h4><p>Maildir on replicated storage with separate indexes and snapshots.</p>${kv([['Allocated','18.4 TiB'],['Used','12.5 TiB'],['Replication','3 copies'],['Snapshot','Every 4 hours']])}</article><article class="mic-card"><div class="mic-card-head"><span class="mic-card-icon"><span class="material-symbols-rounded">data_usage</span></span>${chip('Enforced','success')}</div><h4>Quota policy</h4><p>Soft warnings, hard rejection and deterministic over-quota behavior.</p>${kv([['Warning','80% and 90%'],['Hard limit','100%'],['Grace','24 hours'],['Delivery at limit','Temporary reject']])}</article></div>
      <section class="wcp-panel">${panelHead('Storage and retention controls','Mailbox data is never silently deleted by quota enforcement')}<div class="mic-config-grid">${config('Format','Maildir++','Tenant UID and namespace')}${config('Index location','Dedicated NVMe','Rebuildable indexes')}${config('Compression','zstd','Messages older than 30 days')}${config('Trash retention','30 days','USER may empty earlier')}${config('Spam retention','14 days','Policy adjustable by plan')}${config('Legal hold','Supported','WORM archive target')}${config('Replication','Dovecot dsync','Cross-zone quorum')}${config('Recovery','Point-in-time','Mailbox, folder or message')}</div></section>
      ${note('backup','Mailbox restore is granular','ROOT can restore a complete mailbox, one folder or selected messages into a recovery namespace before merging into production.')}
    </div>`,

    security: () => `<div class="mic-section" data-mail-view="security"><div class="mic-section-head"><div><h3>Authentication, antispam and malware defense</h3><p>Layer connection reputation, content analysis, antivirus, sender authentication, quarantine and compromised-account response.</p></div><div class="mic-actions">${action('scan-security','Run security audit')}${action('edit-security','Edit security policy',true,'shield')}</div></div>
      <div class="mic-grid"><article class="mic-card"><div class="mic-card-head"><span class="mic-card-icon"><span class="material-symbols-rounded">filter_alt</span></span>${chip('Rspamd 3.13','success')}</div><h4>Spam analysis</h4><p>Bayesian scoring, RBLs, reputation, fuzzy hashes and policy groups.</p>${kv([['Reject score','15'],['Quarantine score','8'],['Greylisting','Adaptive'],['False positive rate','0.04%']])}</article><article class="mic-card"><div class="mic-card-head"><span class="mic-card-icon"><span class="material-symbols-rounded">coronavirus</span></span>${chip('Current','success')}</div><h4>Malware scanning</h4><p>ClamAV streams, archive limits and suspicious attachment sandboxing.</p>${kv([['Signatures','Current · 4 min'],['Max archive depth','8'],['Encrypted archive','Quarantine'],['Detection action','Reject + evidence']])}</article><article class="mic-card"><div class="mic-card-head"><span class="mic-card-icon"><span class="material-symbols-rounded">verified_user</span></span>${chip('Strong','success')}</div><h4>Sender authentication</h4><p>SPF, DKIM, DMARC, ARC, MTA-STS and TLS reporting from one policy chain.</p>${kv([['DKIM signed domains','1,268 / 1,284'],['DMARC enforcement','96%'],['MTA-STS','1,244 domains'],['ARC','Enabled']])}</article></div>
      <section class="wcp-panel">${panelHead('Defense layers','Each decision retains evidence without storing message bodies unnecessarily')}<div class="mic-list">${listRow('block','Connection defense','DNSBL, ASN risk, rate limits, tarpitting and invalid HELO','Enabled','success')}${listRow('fingerprint','Identity checks','SPF alignment, DKIM verification, DMARC and ARC chain','Enforced','success')}${listRow('policy','Content policy','Spam score, phishing indicators, attachment and DLP rules','Versioned')}${listRow('emergency','Compromised account','Block outbound SMTP, preserve inbound and quarantine evidence','Automatic','warning')}${listRow('inventory_2','Quarantine','Tenant isolation, release approval, expiry and audit','14 days')}</div></section>
    </div>`,

    reputation: () => `<div class="mic-section" data-mail-view="reputation"><div class="mic-section-head"><div><h3>Deliverability and reputation governance</h3><p>Protect shared and dedicated sending identities with pool allocation, warm-up, complaint suppression, feedback loops and reverse DNS validation.</p></div>${action('reputation-audit','Run deliverability audit',true,'fact_check')}</div>
      <div class="mic-grid"><article class="mic-card"><div class="mic-card-head"><span class="mic-card-icon"><span class="material-symbols-rounded">speed</span></span>${chip('98 / 100','success')}</div><h4>Shared transactional pool</h4><p>Low-latency operational traffic with strict account-level rate policy.</p>${kv([['IPs','4'],['rDNS / FCrDNS','Valid'],['Blocklists','0 listed'],['Complaint rate','0.018%']])}</article><article class="mic-card"><div class="mic-card-head"><span class="mic-card-icon"><span class="material-symbols-rounded">campaign</span></span>${chip('92 / 100','warning')}</div><h4>Marketing pool</h4><p>Permission-based bulk traffic with warm-up and complaint controls.</p>${kv([['IPs','6'],['Warm-up','Day 12 / 21'],['Blocklists','0 listed'],['Complaint rate','0.061%']])}</article><article class="mic-card"><div class="mic-card-head"><span class="mic-card-icon"><span class="material-symbols-rounded">lan</span></span>${chip('42 assignments')}</div><h4>Dedicated IP pool</h4><p>Customer-specific sending identities with minimum-volume requirements.</p>${kv([['Available','10 / 52'],['PTR compliance','100%'],['Warm-up required','Yes'],['Auto quarantine','Enabled']])}</article></div>
      <section class="wcp-panel">${panelHead('Authentication and reputation signals','Coverage, alignment and external feedback')}<div class="mic-table-wrap"><table class="mic-table"><thead><tr><th>Signal</th><th>Coverage</th><th>Target</th><th>Trend</th><th>Owner</th><th>State</th></tr></thead><tbody><tr><td><strong>DKIM signing</strong></td><td>98.7%</td><td>100%</td><td>+0.4%</td><td>ROOT DNS automation</td><td>${chip('Review 16','warning')}</td></tr><tr><td><strong>DMARC enforcement</strong></td><td>96.0%</td><td>98%</td><td>+1.2%</td><td>Domain policy</td><td>${chip('Improving','success')}</td></tr><tr><td><strong>Forward-confirmed rDNS</strong></td><td>100%</td><td>100%</td><td>Stable</td><td>IPAM</td><td>${chip('Compliant','success')}</td></tr><tr><td><strong>Complaint feedback loops</strong></td><td>8 providers</td><td>8</td><td>Stable</td><td>Mail platform</td><td>${chip('Connected','success')}</td></tr></tbody></table></div></section>
    </div>`,

    webmail: () => `<div class="mic-section" data-mail-view="webmail"><div class="mic-section-head"><div><h3>External webmail clients and scoped SSO</h3><p>WCP360 does not embed a webmail application. ROOT activates approved external clients; USER chooses among allowed providers and receives a short-lived mailbox-scoped SSO session.</p></div><div class="mic-actions"><button class="wcp-action-btn" data-mail-action="webmail-health"><span class="material-symbols-rounded">health_and_safety</span>Run health checks</button><button class="wcp-action-btn primary" data-save-webmail><span class="material-symbols-rounded">deployed_code_update</span>Save &amp; deploy</button></div></div>
      <div class="mic-feedback root-feedback" role="status"></div>
      <section class="wcp-panel">${panelHead('Access policy','Effective availability = ROOT activation ∩ plan allowance ∩ account state')}<div class="mic-policy-grid">
        <div class="mic-policy"><span><strong>Browser webmail access</strong><small>Expose external webmail launchers to USER.</small></span><label class="mic-toggle" aria-label="Enable browser webmail access"><input type="checkbox" data-global-enabled ${webmail.globalEnabled ? 'checked' : ''}><i></i></label></div>
        <div class="mic-policy"><span><strong>Allow users to choose</strong><small>Otherwise ROOT forces the default client.</small></span><label class="mic-toggle" aria-label="Allow users to choose webmail"><input type="checkbox" data-user-choice ${webmail.userChoice ? 'checked' : ''}><i></i></label></div>
        <div class="mic-policy"><span><strong>Enforce HTTPS endpoints</strong><small>Reject insecure callbacks and redirects.</small></span><label class="mic-toggle" aria-label="Enforce HTTPS webmail endpoints"><input type="checkbox" data-enforce-tls ${webmail.enforceTls ? 'checked' : ''}><i></i></label></div>
        <div class="mic-policy"><span><strong>Automatic security updates</strong><small>Canary supported security releases.</small></span><label class="mic-toggle" aria-label="Enable automatic webmail security updates"><input type="checkbox" data-auto-updates ${webmail.autoUpdates ? 'checked' : ''}><i></i></label></div>
        <label class="mic-policy"><span><strong>SSO session lifetime</strong><small>Single-use token for one mailbox.</small></span><select data-session-minutes><option value="60" ${webmail.sessionMinutes === 60 ? 'selected' : ''}>1 hour</option><option value="240" ${webmail.sessionMinutes === 240 ? 'selected' : ''}>4 hours</option><option value="480" ${webmail.sessionMinutes === 480 ? 'selected' : ''}>8 hours</option><option value="720" ${webmail.sessionMinutes === 720 ? 'selected' : ''}>12 hours</option></select></label>
        <div class="mic-policy"><span><strong>Plans with access</strong><small>At least one provider must remain enabled.</small></span><div class="mic-plan-options">${['Starter','Business','Agency','Reseller'].map(plan => `<label><input type="checkbox" data-plan-access value="${plan}" ${webmail.plans.includes(plan) ? 'checked' : ''}><span>${plan}</span></label>`).join('')}</div></div>
      </div></section>
      <div class="mic-provider-grid">${webmail.clients.map(client => `<article class="mic-provider" data-provider="${client.id}"><div class="mic-provider-head"><span class="mic-provider-icon"><span class="material-symbols-rounded">${client.icon}</span></span><div><div class="mic-provider-name">${client.name}</div><div class="mic-provider-version">${client.version} · ${client.channel}</div></div><label class="mic-toggle" aria-label="Enable ${client.name}"><input type="checkbox" data-provider-enabled ${client.enabled ? 'checked' : ''}><i></i></label></div><p>${client.description}</p><div class="mic-health"><i></i><strong>${client.status === 'healthy' ? 'Healthy' : 'Maintenance'}</strong><span>· last check 2 min</span></div><div class="mic-provider-fields"><label><span>Public endpoint</span><input type="url" data-provider-endpoint value="${escapeHtml(client.endpoint)}" required></label><label><span>Authentication</span><select data-provider-auth><option ${client.auth === 'Signed SSO token' ? 'selected' : ''}>Signed SSO token</option><option ${client.auth === 'OpenID Connect' ? 'selected' : ''}>OpenID Connect</option><option ${client.auth === 'SAML 2.0' ? 'selected' : ''}>SAML 2.0</option></select></label><label><span>Update channel</span><select data-provider-channel><option ${client.channel === 'Stable' ? 'selected' : ''}>Stable</option><option ${client.channel === 'Extended' ? 'selected' : ''}>Extended</option><option ${client.channel === 'Preview' ? 'selected' : ''}>Preview</option></select></label><label class="mic-default"><input type="radio" name="default-client" value="${client.id}" ${webmail.defaultClient === client.id ? 'checked' : ''}><span>Default for new accounts</span></label></div></article>`).join('')}</div>
      ${note('key','Passwords never enter launch URLs','WCP360 issues a single-use signed token scoped to one mailbox, one provider and one short expiry. Provider callbacks, TLS and proxy headers are validated before activation.')}
    </div>`,

    queue: () => `<div class="mic-section" data-mail-view="queue"><div class="mic-section-head"><div><h3>Durable mail queue and delivery diagnostics</h3><p>Search, retry, freeze, thaw or remove messages with recipient-domain context, account ownership, SMTP evidence and guarded destructive operations.</p></div><div class="mic-actions">${action('run-queue','Run queue')}${action('queue-search','Advanced search',true,'manage_search')}</div></div>
      <div class="mic-toolbar"><label class="mic-search"><span class="material-symbols-rounded">search</span><input type="search" data-queue-search placeholder="Message ID, sender, recipient, node or reason" aria-label="Search mail queue"></label><select data-queue-status aria-label="Filter queue status"><option value="all">All states</option><option>Queued</option><option>Deferred</option><option>Frozen</option></select></div>
      <section class="wcp-panel">${panelHead('Queued messages',`${state.queue.length} messages shown · message bodies are never exposed`)}<div class="mic-table-wrap"><table class="mic-table"><thead><tr><th>Message</th><th>Sender</th><th>Recipient domain</th><th>Age / node</th><th>Last result</th><th>State</th><th></th></tr></thead><tbody>${state.queue.map(item => `<tr data-mail-queue="${item.id}" data-search="${escapeHtml(Object.values(item).join(' ').toLowerCase())}" data-status="${item.status}"><td><strong>${item.id}</strong><small>Account-attributed envelope</small></td><td>${item.sender}</td><td>${item.recipient}</td><td>${item.age}<small>${item.node}</small></td><td>${item.reason}</td><td>${chip(item.status,item.status === 'Queued' ? 'processing' : 'warning')}</td><td><div class="mic-actions"><button class="wcp-action-btn" data-mail-action="retry-message" data-message="${item.id}">Retry</button><button class="wcp-action-btn" data-mail-action="remove-message" data-message="${item.id}">Remove</button></div></td></tr>`).join('')}</tbody></table></div></section><div class="mic-feedback" data-queue-feedback role="status"></div>
      ${note('policy','Destructive queue operations are scoped','Removal requires typing the exact message ID. Bulk removal additionally requires impact preview, approval policy and an immutable evidence record.')}
    </div>`,

    history: () => `<div class="mic-section" data-mail-view="history"><div class="mic-section-head"><div><h3>Mail configuration history and forensic evidence</h3><p>Trace profile changes, queue operations, webmail policy, authentication rotations, quarantine releases and reputation incidents through one correlation chain.</p></div>${action('export-evidence','Export evidence',false,'download')}</div>
      <section class="wcp-panel">${panelHead('Recent privileged operations','Configuration diff, targets, probes, actor and rollback remain linked')}<div class="mic-timeline"><div class="mic-event"><time>12:31 UTC</time><i></i><div><strong>Production Mail v18 published</strong><small>3 nodes · syntax and 12 delivery probes passed · CHG-50312</small></div>${chip('Completed','success')}</div><div class="mic-event"><time>11:48 UTC</time><i></i><div><strong>Microsoft destination throttle adjusted</strong><small>Automatic response to 451 rate signals · previous limit retained</small></div>${chip('Active','warning')}</div><div class="mic-event"><time>10:22 UTC</time><i></i><div><strong>DKIM key rotation completed</strong><small>84 domains · DNS propagation and signature verification passed</small></div>${chip('Verified','success')}</div><div class="mic-event"><time>Yesterday</time><i></i><div><strong>SnappyMail security release deployed</strong><small>Canary SSO, IMAP and callback health gates passed</small></div>${chip('Completed','success')}</div><div class="mic-event"><time>Oct 27</time><i></i><div><strong>Outbound account quarantined</strong><small>Credential compromise signal · inbound delivery preserved · INC-1204</small></div>${chip('Incident','warning')}</div></div></section>
      <div class="mic-grid two"><section class="wcp-panel">${panelHead('Recovery inventory','Configuration and data recovery remain separate')}<div class="mic-list">${listRow('dns','Service profiles','18 previous signed revisions','Ready','success')}${listRow('key','DKIM keys','Encrypted recovery escrow','Ready','success')}${listRow('inbox','Mailbox snapshots','4-hour points · 30 days','Ready','success')}${listRow('undo','Queue evidence','Envelope and result metadata · 30 days','Available')}</div></section><section class="wcp-panel">${panelHead('Audit integrity','Security evidence is externalized from mail nodes')}<div class="mic-list">${listRow('fingerprint','Audit hash chain','Anchored 2 minutes ago','Intact','success')}${listRow('lock','WORM destination','EU compliance archive','Connected','success')}${listRow('privacy_tip','Content minimization','No message bodies in operational logs','Enforced','success')}${listRow('schedule','Retention','Operational 30d · security 365d','Policy')}</div></section></div>
    </div>`
  };

  const host = qs('#mailInfrastructure');
  host.innerHTML = `<div class="mic-root"><section class="mic-hero"><div><div class="mic-eyebrow">WCP360 / ROOT / Web &amp; Data</div><h2>Mail Infrastructure Center</h2><p>Authoritative governance of SMTP, IMAP, filtering, authentication, reputation, external webmail, delivery queues and evidence.</p></div><div class="mic-hero-actions">${action('health-check','Health checks')}${action('create-profile','Create profile',true,'add_box')}</div></section>
    <section class="mic-metrics"><article class="mic-metric"><span>Mail nodes</span><strong>5 / 5</strong><small>Three active · one relay · one standby</small></article><article class="mic-metric"><span>Delivery success</span><strong>99.42%</strong><small>Last 24 hours · 842k messages</small></article><article class="mic-metric"><span>Queue depth</span><strong>1,842</strong><small>126 deferred · 7 frozen</small></article><article class="mic-metric"><span>Reputation</span><strong>98 / 100</strong><small>No active blocklist listing</small></article></section>
    <section class="mic-boundary"><div><span>ROOT authority</span><strong>Nodes, transports, security and providers</strong></div><span class="material-symbols-rounded">arrow_forward</span><div><span>RESELLER delegation</span><strong>Narrow features, quotas and mailbox packages</strong></div><span class="material-symbols-rounded">arrow_forward</span><div><span>USER workspace</span><strong>Owned mailboxes, routes and webmail launch</strong></div></section>
    <div class="mic-tabs" role="tablist" aria-label="Mail infrastructure views">${tabs.map(tab => `<button type="button" class="mic-tab" role="tab" data-mail-tab="${tab[0]}" aria-selected="false"><span class="material-symbols-rounded">${tab[1]}</span>${tab[2]}</button>`).join('')}</div><div class="mic-view" tabindex="-1"></div></div>
    <button type="button" class="mic-drawer-backdrop" data-mail-close aria-label="Close mail operation"></button><aside class="mic-drawer" id="mailDrawer" role="dialog" aria-modal="true" aria-hidden="true" aria-labelledby="mailDrawerTitle"><div class="mic-drawer-head"><div><strong id="mailDrawerTitle">Mail operation</strong><small data-mail-drawer-sub>Validated ROOT workflow</small></div><button type="button" class="wcp-icon-btn" data-mail-close aria-label="Close"><span class="material-symbols-rounded">close</span></button></div><div class="mic-drawer-body"></div></aside>`;

  const renderView = (focus = false) => {
    qsa('[data-mail-tab]').forEach(button => { const selected = button.dataset.mailTab === active; button.classList.toggle('active', selected); button.setAttribute('aria-selected', String(selected)); });
    qs('.mic-view').innerHTML = views[active]();
    if (focus) qs('.mic-view').focus();
    bindView();
  };
  const setTab = (name, focus = true) => {
    if (!views[name]) return;
    active = name;
    history.replaceState(null, '', `#${name}`);
    renderView(focus);
  };
  const field = (label, control) => `<label class="mic-field"><span>${label}</span>${control}</label>`;
  const formShell = (kind, body, submit = 'Create job', dangerous = false) => `<form class="mic-form ${dangerous ? 'mic-danger' : ''}" data-mail-form="${kind}">${body}<div class="mic-feedback" role="status">Validated. The operation was added to Job Center.</div><div class="mic-form-actions"><button type="button" class="wcp-action-btn" data-mail-close>Cancel</button><button type="submit" class="wcp-action-btn ${dangerous ? '' : 'primary'}" data-mail-submit>${submit}</button></div></form>`;
  const openDrawer = (kind, trigger, messageId = '') => {
    returnFocus = trigger || document.activeElement;
    const drawer = qs('#mailDrawer'), body = qs('.mic-drawer-body', drawer), title = qs('#mailDrawerTitle', drawer), subtitle = qs('[data-mail-drawer-sub]', drawer);
    let content = '';
    if (kind === 'create-profile') {
      title.textContent = 'Create mail service profile'; subtitle.textContent = 'Versioned SMTP, IMAP and security configuration';
      content = formShell('create-profile', field('Profile name','<input name="name" required value="Regional Mail" autocomplete="off">') + '<div class="mic-form-grid">' + field('SMTP transport','<select name="smtp"><option>Postfix</option><option>Postfix relay</option><option>Exim compatibility</option></select>') + field('IMAP service','<select name="imap"><option>Dovecot</option><option>Disabled</option></select>') + '</div>' + field('Security stack','<select name="security"><option>Rspamd + ClamAV</option><option>Rspamd</option><option>Inherited filtering</option></select>') + field('Rollout','<select name="rollout"><option>Draft only</option><option>Canary then staged</option><option>Maintenance window</option></select>'), 'Create profile');
    } else if (kind === 'remove-message') {
      title.textContent = 'Remove queued message'; subtitle.textContent = 'Destructive envelope operation with immutable evidence';
      content = formShell('remove-message', `<input type="hidden" name="message" value="${escapeHtml(messageId)}">` + note('warning','Delivery cannot resume after removal','Message content is discarded from the active queue. Envelope metadata and the administrative decision remain in audit evidence.') + field(`Type ${escapeHtml(messageId)} to confirm`, `<input name="confirmation" required autocomplete="off" data-mail-confirm data-expected="${escapeHtml(messageId)}">`), 'Remove message', true);
    } else if (kind === 'queue-search') {
      title.textContent = 'Advanced queue search'; subtitle.textContent = 'Envelope, response and correlation filters';
      content = formShell('queue-search', field('Sender, recipient or message ID','<input name="query" autocomplete="off">') + '<div class="mic-form-grid">' + field('State','<select name="status"><option>All states</option><option>Queued</option><option>Deferred</option><option>Frozen</option></select>') + field('Time range','<select name="range"><option>Last 24 hours</option><option>Last 7 days</option><option>Last 30 days</option></select>') + '</div>' + field('SMTP response contains','<input name="response" autocomplete="off">'), 'Run search');
    } else {
      title.textContent = 'Edit mail platform policy'; subtitle.textContent = 'Versioned service schema and safe rollout';
      content = formShell('edit-policy', field('Policy family', `<select name="family"><option>${active.toUpperCase()} baseline</option><option>Production Mail profile</option><option>Transactional Relay profile</option></select>`) + '<div class="mic-form-grid">' + field('Target','<select name="target"><option>Canary node</option><option>All compatible nodes</option><option>Selected profile only</option></select>') + field('Activation','<select name="activation"><option>Validate and save draft</option><option>Canary then staged rollout</option><option>Maintenance window</option></select>') + '</div>' + field('Change reason','<textarea name="reason" required>Reviewed mail platform policy update.</textarea>') + '<label class="mic-confirm"><input type="checkbox" required><span><strong>Run syntax, delivery and recovery checks</strong><br>Failed health gates stop publication and retain the previous revision.</span></label>', 'Validate policy');
    }
    body.innerHTML = content;
    const submit = qs('[data-mail-submit]', body);
    const confirmation = qs('[data-mail-confirm]', body);
    if (confirmation && submit) {
      submit.disabled = true;
      confirmation.addEventListener('input', () => { submit.disabled = confirmation.value === confirmation.dataset.expected ? false : true; });
    }
    drawer.classList.add('open'); drawer.setAttribute('aria-hidden','false'); qs('.mic-drawer-backdrop').classList.add('open'); document.body.style.overflow = 'hidden'; qs('input,select,textarea', drawer)?.focus();
  };
  const closeDrawer = () => { const drawer = qs('#mailDrawer'); drawer.classList.remove('open'); drawer.setAttribute('aria-hidden','true'); qs('.mic-drawer-backdrop').classList.remove('open'); document.body.style.overflow = ''; returnFocus?.focus?.(); };
  const emit = (name, kind, risk = 'medium') => document.dispatchEvent(new CustomEvent('wcp:operation', { detail: { name, kind, risk, scope:'root', actor:'Yann · ROOT' } }));
  const collectWebmail = () => {
    const clients = webmail.clients.map(client => {
      const card = qs(`[data-provider="${client.id}"]`);
      return { ...client, enabled: qs('[data-provider-enabled]',card).checked, endpoint: qs('[data-provider-endpoint]',card).value.trim(), auth: qs('[data-provider-auth]',card).value, channel: qs('[data-provider-channel]',card).value };
    });
    return { ...webmail, globalEnabled: qs('[data-global-enabled]').checked, userChoice: qs('[data-user-choice]').checked, enforceTls: qs('[data-enforce-tls]').checked, autoUpdates: qs('[data-auto-updates]').checked, sessionMinutes: Number(qs('[data-session-minutes]').value), defaultClient: qs('input[name="default-client"]:checked')?.value || '', plans: qsa('[data-plan-access]:checked').map(input => input.value), clients };
  };
  const saveWebmailPolicy = () => {
    const next = collectWebmail();
    const feedback = qs('.root-feedback');
    const selected = next.clients.find(client => client.id === next.defaultClient);
    let error = '';
    if (next.globalEnabled && !next.clients.some(client => client.enabled)) error = 'Enable at least one provider before publishing browser webmail access.';
    else if (next.globalEnabled && (!selected || !selected.enabled)) error = 'The default webmail provider must be enabled.';
    else if (next.enforceTls && next.clients.some(client => client.enabled && !client.endpoint.startsWith('https://'))) error = 'Every enabled provider must use an HTTPS endpoint.';
    else if (next.globalEnabled && !next.plans.length) error = 'Select at least one hosting plan with webmail access.';
    if (error) { feedback.textContent = error; feedback.classList.add('show','error'); return; }
    webmail = next; saveWebmail(); feedback.textContent = 'Webmail service policy validated. Deployment added to Job Center.'; feedback.classList.remove('error'); feedback.classList.add('show'); emit('Deploy webmail service policy','webmail-services','medium');
  };
  const filterQueue = () => {
    const text = (qs('[data-queue-search]')?.value || '').trim().toLowerCase(), status = qs('[data-queue-status]')?.value || 'all';
    qsa('[data-mail-queue]').forEach(row => { row.hidden = Boolean((text && !row.dataset.search.includes(text)) || (status !== 'all' && row.dataset.status !== status)); });
  };
  const bindView = () => { qs('[data-queue-search]')?.addEventListener('input',filterQueue); qs('[data-queue-status]')?.addEventListener('change',filterQueue); };

  qs('.mic-tabs').addEventListener('click', event => { const tab = event.target.closest('[data-mail-tab]'); if (tab) setTab(tab.dataset.mailTab); });
  host.addEventListener('click', event => {
    if (event.target.closest('[data-save-webmail]')) { saveWebmailPolicy(); return; }
    const trigger = event.target.closest('[data-mail-action]');
    if (!trigger) return;
    const kind = trigger.dataset.mailAction;
    if (['create-profile','edit-profile','add-node','edit-smtp','edit-imap','edit-security'].includes(kind)) { openDrawer(kind === 'create-profile' ? kind : 'edit-policy', trigger); return; }
    if (kind === 'remove-message') { openDrawer(kind,trigger,trigger.dataset.message); return; }
    if (kind === 'queue-search') { openDrawer(kind,trigger); return; }
    const operations = {
      'health-check':['Run mail infrastructure health checks','mail-health','low'],
      'test-delivery':['Run end-to-end mail delivery probe','mail-delivery-probe','low'],
      'scan-security':['Audit mail security controls','mail-security-audit','low'],
      'reputation-audit':['Run mail deliverability audit','mail-reputation-audit','low'],
      'webmail-health':['Validate webmail endpoints and SSO','webmail-health','low'],
      'run-queue':['Run mail queue delivery cycle','mail-queue-run','medium'],
      'retry-message':[`Retry queued message ${trigger.dataset.message || ''}`,'mail-queue-retry','medium'],
      'inspect-node':['Inspect mail node','mail-node-inspect','low'],
      'export-evidence':['Export mail audit evidence','mail-evidence-export','low']
    };
    if (operations[kind]) emit(...operations[kind]);
  });
  document.addEventListener('click', event => { if (event.target.closest('[data-mail-close]')) closeDrawer(); });
  document.addEventListener('submit', event => {
    const form = event.target.closest('[data-mail-form]');
    if (!form) return;
    event.preventDefault();
    if (!form.reportValidity()) return;
    if (form.dataset.mailForm === 'create-profile') {
      const name = form.elements.name.value.trim();
      state.profiles.push({ id: `mail-profile-${Date.now()}`, name, smtp:form.elements.smtp.value, imap:form.elements.imap.value, security:form.elements.security.value, nodes:0, revision:'v1', status:'Draft' });
      saveInfra(); emit(`Create mail service profile ${name}`,'mail-profile-create','medium');
    } else if (form.dataset.mailForm === 'remove-message') {
      const message = form.elements.message.value;
      if (form.elements.confirmation.value !== message) return;
      state.queue = state.queue.filter(item => item.id !== message);
      saveInfra(); emit(`Remove queued message ${message}`,'mail-queue-remove','high');
    } else if (form.dataset.mailForm === 'queue-search') emit('Run advanced mail queue search','mail-queue-search','low');
    else emit('Publish mail platform policy revision','mail-policy-publish','high');
    qs('.mic-feedback',form)?.classList.add('show');
    setTimeout(() => { closeDrawer(); renderView(); }, 350);
  });
  document.addEventListener('keydown', event => { if (event.key === 'Escape' && qs('#mailDrawer.open')) closeDrawer(); });
  window.addEventListener('hashchange', () => { const name = location.hash.slice(1); if (views[name]) { active = name; renderView(); } });
  renderView(); saveInfra(); saveWebmail();
})();
