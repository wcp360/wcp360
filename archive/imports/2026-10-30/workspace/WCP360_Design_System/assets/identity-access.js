(function () {
  const canonical = 'users-roles.html';
  const legacy = {
    'roles.html': 'roles',
    'plans.html': 'plans',
    'feature-lists.html': 'features',
    'effective-permissions.html': 'permissions',
    'demo-accounts.html': 'demo',
    'resellers-access.html': 'resellers',
    'quotas.html': 'quotas'
  };
  const page = (location.pathname.split('/').pop() || '').toLowerCase();
  if (legacy[page]) {
    location.replace(canonical + '#' + legacy[page]);
    return;
  }
  if (page !== canonical) return;

  const qs = (selector, context = document) => context.querySelector(selector);
  const qsa = (selector, context = document) => [...context.querySelectorAll(selector)];
  const sections = [
    ['users', 'group', 'Users'],
    ['roles', 'admin_panel_settings', 'Roles'],
    ['plans', 'inventory_2', 'Plans'],
    ['features', 'checklist', 'Feature lists'],
    ['permissions', 'rule', 'Effective permissions'],
    ['demo', 'science', 'Demo accounts'],
    ['resellers', 'storefront', 'Resellers'],
    ['quotas', 'data_usage', 'Quotas']
  ];
  let active = sections.some(item => item[0] === location.hash.slice(1)) ? location.hash.slice(1) : 'users';
  let returnFocus = null;

  const chip = (label, tone = '') => `<span class="iam-chip ${tone}">${label}</span>`;
  const button = (kind, label, primary = false, icon = 'add') => `<button type="button" class="wcp-action-btn ${primary ? 'primary' : ''}" data-iam-action="${kind}"><span class="material-symbols-rounded">${icon}</span>${label}</button>`;
  const panelHead = (title, copy, controls = '') => `<div class="wcp-panel-head iam-panel-head"><div><div class="wcp-panel-title">${title}</div><div class="wcp-panel-sub">${copy}</div></div><div class="iam-panel-actions">${controls}</div></div>`;
  const callout = (icon, title, copy) => `<div class="iam-callout"><span class="material-symbols-rounded">${icon}</span><div><strong>${title}</strong><p>${copy}</p></div></div>`;
  const metricStrip = () => `<section class="iam-overview-strip" aria-label="Identity and access summary">
    <article class="iam-metric"><span class="iam-metric-label">Identities</span><strong class="iam-metric-value">128</strong><small class="iam-metric-meta">121 active · 7 pending</small></article>
    <article class="iam-metric"><span class="iam-metric-label">Privileged</span><strong class="iam-metric-value">14</strong><small class="iam-metric-meta">3 ROOT · 11 delegated</small></article>
    <article class="iam-metric"><span class="iam-metric-label">MFA coverage</span><strong class="iam-metric-value">97%</strong><small class="iam-metric-meta">4 accounts to remediate</small></article>
    <article class="iam-metric"><span class="iam-metric-label">Access reviews</span><strong class="iam-metric-value">3</strong><small class="iam-metric-meta">Due within 7 days</small></article>
  </section>`;
  const accessRow = (capability, source, decision, tone = 'allow') => `<div class="iam-access-row"><div><strong>${capability}</strong><small>${source}</small></div><span class="wcp-perm-pill ${tone}">${decision}</span><button type="button" class="wcp-action-btn" data-iam-action="trace">Explain</button></div>`;

  const views = {
    users: () => `<div class="iam-section" data-iam-view="users">
      <div class="iam-section-head"><div><h3>People and service identities</h3><p>Manage staff, customer owners, API identities, authentication posture and current sessions from one inventory.</p></div><div>${button('invite', 'Invite user')}${button('account', 'Create hosting account', true, 'person_add')}</div></div>
      <div class="iam-toolbar"><label class="iam-search"><span class="material-symbols-rounded">search</span><input id="iamUserSearch" type="search" placeholder="Search email, organization, role or scope" aria-label="Search identities"></label><div class="iam-toolbar-group"><select id="iamStatusFilter" aria-label="Filter status"><option value="all">All statuses</option><option value="active">Active</option><option value="pending">Pending</option><option value="suspended">Suspended</option></select><select id="iamRoleFilter" aria-label="Filter role"><option value="all">All roles</option><option value="owner">Owners</option><option value="operator">Operators</option><option value="service">Service identities</option></select></div></div>
      <div class="wcp-user-grid" data-iam-user-grid>
        <article class="wcp-user-card" data-user-search="yann@example.com example company owner root" data-user-status="active" data-user-role="owner"><div class="wcp-user-head"><div><div class="wcp-user-title">yann@example.com</div><div class="wcp-user-sub">Example Company · Account owner</div></div><span class="wcp-status success">Active</span></div><div class="iam-user-meta">${chip('Owner')}${chip('MFA enforced', 'success')}${chip('2 active sessions')}</div><div class="iam-detail-grid"><span><small>Scope</small><strong>Organization</strong></span><span><small>Last sign-in</small><strong>18 min ago</strong></span></div><div class="iam-card-actions"><button class="wcp-action-btn" data-iam-action="trace">Inspect access</button><button class="wcp-action-btn" data-iam-action="manage-user">Manage</button></div></article>
        <article class="wcp-user-card" data-user-search="ops@example.com platform operations operator" data-user-status="active" data-user-role="operator"><div class="wcp-user-head"><div><div class="wcp-user-title">ops@example.com</div><div class="wcp-user-sub">Platform operations · Delegated staff</div></div><span class="wcp-status success">Active</span></div><div class="iam-user-meta">${chip('Operator')}${chip('JIT required', 'warning')}${chip('EU-PROD')}</div><div class="iam-detail-grid"><span><small>Privilege</small><strong>Time-bound</strong></span><span><small>Review</small><strong>Due in 5 days</strong></span></div><div class="iam-card-actions"><button class="wcp-action-btn" data-iam-action="trace">Inspect access</button><button class="wcp-action-btn" data-iam-action="manage-user">Manage</button></div></article>
        <article class="wcp-user-card" data-user-search="deploy-bot service identity ci deployment" data-user-status="active" data-user-role="service"><div class="wcp-user-head"><div><div class="wcp-user-title">deploy-bot</div><div class="wcp-user-sub">Service identity · CI deployments</div></div><span class="wcp-status success">Active</span></div><div class="iam-user-meta">${chip('Service identity')}${chip('Token rotates in 12d')}${chip('No interactive login')}</div><div class="iam-detail-grid"><span><small>Scope</small><strong>3 applications</strong></span><span><small>Last used</small><strong>7 min ago</strong></span></div><div class="iam-card-actions"><button class="wcp-action-btn" data-iam-action="trace">Inspect access</button><button class="wcp-action-btn" data-iam-action="manage-user">Manage</button></div></article>
        <article class="wcp-user-card" data-user-search="billing@example.com billing observer viewer" data-user-status="pending" data-user-role="operator"><div class="wcp-user-head"><div><div class="wcp-user-title">billing@example.com</div><div class="wcp-user-sub">Billing observer · Read only</div></div><span class="wcp-status warning">Pending</span></div><div class="iam-user-meta">${chip('Observer')}${chip('Expires in 2d', 'warning')}</div><div class="iam-detail-grid"><span><small>Scope</small><strong>Billing only</strong></span><span><small>Invited by</small><strong>yann@example.com</strong></span></div><div class="iam-card-actions"><button class="wcp-action-btn" data-iam-action="resend">Resend</button><button class="wcp-action-btn" data-iam-action="revoke">Revoke</button></div></article>
      </div><div class="iam-empty" data-iam-empty>No identity matches the current filters.</div>
    </div>`,

    roles: () => `<div class="iam-section" data-iam-view="roles">
      <div class="iam-section-head"><div><h3>Roles and access boundaries</h3><p>Reusable least-privilege roles. ROOT actions, customer capabilities and reseller delegation remain separate.</p></div>${button('role', 'Create role', true, 'add_moderator')}</div>
      ${callout('shield_lock', 'Deny wins', 'An explicit deny, expired JIT grant or scope boundary always overrides role and plan allowances.')}
      <section class="wcp-panel">${panelHead('Role inventory', 'Assignments, scopes and review state', '<button class="wcp-action-btn" data-iam-action="review">Start access review</button>')}<div class="iam-table-wrap"><table class="iam-table"><thead><tr><th>Role</th><th>Audience</th><th>Members</th><th>Boundary</th><th>Review</th><th></th></tr></thead><tbody>
        <tr><td><strong>ROOT administrator</strong><small>Full platform governance</small></td><td>Internal staff</td><td>3</td><td>${chip('ROOT', 'danger')}</td><td><span class="wcp-status success">Current</span></td><td><button class="wcp-action-btn" data-iam-action="trace">Inspect</button></td></tr>
        <tr><td><strong>Platform operator</strong><small>Operations without IAM grants</small></td><td>Internal staff</td><td>11</td><td>${chip('JIT required', 'warning')}</td><td><span class="wcp-status warning">Due in 5d</span></td><td><button class="wcp-action-btn" data-iam-action="role">Edit</button></td></tr>
        <tr><td><strong>Account owner</strong><small>One hosting account</small></td><td>Customers</td><td>87</td><td>${chip('USER')}</td><td><span class="wcp-status success">Current</span></td><td><button class="wcp-action-btn" data-iam-action="role">Edit</button></td></tr>
        <tr><td><strong>Reseller administrator</strong><small>Bounded child accounts</small></td><td>Partners</td><td>9</td><td>${chip('RESELLER')}</td><td><span class="wcp-status success">Current</span></td><td><button class="wcp-action-btn" data-iam-action="role">Edit</button></td></tr>
      </tbody></table></div></section>
    </div>`,

    plans: () => `<div class="iam-section" data-iam-view="plans">
      <div class="iam-section-head"><div><h3>Hosting plans and packages</h3><p>Combine feature availability, default quotas, runtime policy and commercial metadata without granting administrative privilege.</p></div>${button('plan', 'Create plan', true, 'add_box')}</div>
      <div class="iam-plan-grid">
        <article class="iam-plan-card"><div><span class="material-symbols-rounded">rocket_launch</span><span class="wcp-status success">Published</span></div><h4>Starter</h4><p>Small sites and managed applications.</p><dl><div><dt>Storage</dt><dd>25 GiB</dd></div><div><dt>Sites</dt><dd>5</dd></div><div><dt>CPU / RAM</dt><dd>2 vCPU / 2 GiB</dd></div><div><dt>Accounts</dt><dd>41</dd></div></dl><footer><button class="wcp-action-btn" data-iam-action="plan">Edit</button><button class="wcp-action-btn" data-iam-tab="features">Features</button></footer></article>
        <article class="iam-plan-card featured"><div><span class="material-symbols-rounded">workspace_premium</span><span class="wcp-status success">Published</span></div><h4>Business Pro</h4><p>Production workloads with staging and priority backups.</p><dl><div><dt>Storage</dt><dd>200 GiB</dd></div><div><dt>Sites</dt><dd>25</dd></div><div><dt>CPU / RAM</dt><dd>8 vCPU / 16 GiB</dd></div><div><dt>Accounts</dt><dd>72</dd></div></dl><footer><button class="wcp-action-btn" data-iam-action="plan">Edit</button><button class="wcp-action-btn" data-iam-tab="quotas">Quotas</button></footer></article>
        <article class="iam-plan-card"><div><span class="material-symbols-rounded">storefront</span><span class="wcp-status warning">Draft</span></div><h4>Agency Cloud</h4><p>Reseller-ready package with bounded overselling.</p><dl><div><dt>Storage</dt><dd>1 TiB pool</dd></div><div><dt>Sites</dt><dd>250</dd></div><div><dt>CPU / RAM</dt><dd>Shared pool</dd></div><div><dt>Accounts</dt><dd>Draft</dd></div></dl><footer><button class="wcp-action-btn" data-iam-action="plan">Continue</button><button class="wcp-action-btn" data-iam-action="clone">Clone</button></footer></article>
      </div>${callout('policy', 'Plan ≠ permission', 'A plan exposes product features and capacity. A role and valid scope are still required to operate them.')}
    </div>`,

    features: () => `<div class="iam-section" data-iam-view="features">
      <div class="iam-section-head"><div><h3>Feature lists</h3><p>Central availability matrix consumed by plans. Changes are versioned and affect new assignments only until published.</p></div>${button('feature', 'Create feature list', true, 'playlist_add')}</div>
      <section class="wcp-panel">${panelHead('Capability matrix', 'Plan-level availability; authorization remains in Roles', '<button class="wcp-action-btn" data-iam-action="publish-features">Publish draft</button>')}<div class="iam-table-wrap"><table class="iam-table iam-feature-table"><thead><tr><th>Capability</th><th>Starter</th><th>Business</th><th>Agency</th></tr></thead><tbody>
        ${[['Website management',1,1,1],['Email & external webmail',1,1,1],['Database tools & SSO',1,1,1],['App installer',1,1,1],['Git deployments',0,1,1],['Staging environments',0,1,1],['Dedicated IP',0,1,1],['SSH terminal',0,0,1]].map(row => '<tr><td><strong>' + row[0] + '</strong></td>' + row.slice(1).map(value => '<td><button class="iam-matrix-toggle" aria-pressed="' + Boolean(value) + '">' + (value ? 'Allowed' : 'Blocked') + '</button></td>').join('') + '</tr>').join('')}
      </tbody></table></div></section>
    </div>`,

    permissions: () => `<div class="iam-section" data-iam-view="permissions">
      <div class="iam-section-head"><div><h3>Effective permission explorer</h3><p>Resolve what a subject can actually do after plan, role, scope, policy, JIT and explicit overrides are evaluated.</p></div>${button('export', 'Export evidence', false, 'download')}</div>
      <div class="iam-evaluator"><label><span>Subject</span><select><option>ops@example.com</option><option>yann@example.com</option><option>deploy-bot</option></select></label><label><span>Target scope</span><select><option>EU-PROD / example.com</option><option>Organization</option><option>srv-par-01</option></select></label><label><span>Evaluation time</span><select><option>Now</option><option>At next sign-in</option></select></label><button class="wcp-action-btn primary" data-iam-action="evaluate"><span class="material-symbols-rounded">rule</span>Evaluate</button></div>
      <div class="iam-formula"><span>Feature list</span><b>∩</b><span>Assigned role</span><b>∩</b><span>Scope policy</span><b>∩</b><span>JIT / MFA</span><b>−</b><span>Explicit deny</span><strong>= Effective access</strong></div>
      <section class="wcp-panel">${panelHead('Decision results', 'Evaluated now · correlation IAM-48219')}<div class="iam-access-list">${accessRow('View applications', 'Business modules + Platform operator', 'Allow')}${accessRow('Deploy release', 'Role allowed; MFA and scope valid', 'Allow')}${accessRow('Restart PHP-FPM', 'JIT elevation required', 'Conditional', 'inherit')}${accessRow('Manage ROOT identities', 'Explicit ROOT boundary', 'Deny', 'deny')}${accessRow('Terminate hosting account', 'Two-person approval required', 'Deny', 'deny')}</div></section>
    </div>`,

    demo: () => `<div class="iam-section" data-iam-view="demo">
      <div class="iam-section-head"><div><h3>Demo and training accounts</h3><p>Ephemeral environments with synthetic data, secret redaction and destructive operations blocked by policy.</p></div>${button('demo', 'Create demo profile', true, 'science')}</div>
      <div class="iam-demo-grid"><article class="iam-demo-card"><header><div><strong>Sales · Hosting</strong><small>Full USER product tour</small></div><span class="wcp-status success">Ready</span></header><div class="iam-user-meta">${chip('30 min session')}${chip('Reset after logout')}${chip('Synthetic data')}</div><p>Websites, email, databases, backups and analytics with controlled sample operations.</p><footer><button class="wcp-action-btn" data-iam-action="launch-demo">Launch</button><button class="wcp-action-btn" data-iam-action="demo">Configure</button></footer></article><article class="iam-demo-card"><header><div><strong>Partner · Reseller</strong><small>Delegation and customer lifecycle</small></div><span class="wcp-status warning">Review</span></header><div class="iam-user-meta">${chip('60 min session')}${chip('Max 5 child accounts')}${chip('No exports')}</div><p>Reseller governance, package assignment and safe customer account simulations.</p><footer><button class="wcp-action-btn" disabled>Launch</button><button class="wcp-action-btn" data-iam-action="demo">Review policy</button></footer></article></div>
      ${callout('encrypted', 'Hard safety boundary', 'Secrets, live data, terminal access, API tokens, external email and destructive jobs are always disabled in demo mode.')}
    </div>`,

    resellers: () => `<div class="iam-section" data-iam-view="resellers">
      <div class="iam-section-head"><div><h3>Reseller delegation</h3><p>Delegate account management inside a parent-defined capacity, feature and authorization envelope.</p></div>${button('reseller', 'Create reseller', true, 'add_business')}</div>
      <div class="iam-boundary"><div><span>ROOT capacity</span><strong>4 TiB · 64 vCPU · 256 GiB RAM</strong></div><span class="material-symbols-rounded">arrow_forward</span><div><span>Reseller envelope</span><strong>1 TiB · 16 vCPU · 64 GiB RAM</strong></div><span class="material-symbols-rounded">arrow_forward</span><div><span>Child accounts</span><strong>32 active · max 50</strong></div></div>
      <section class="wcp-panel">${panelHead('Reseller inventory', 'Capacity, overselling and delegated capabilities')}<div class="iam-table-wrap"><table class="iam-table"><thead><tr><th>Reseller</th><th>Owner</th><th>Accounts</th><th>Storage</th><th>Overselling</th><th>Status</th><th></th></tr></thead><tbody><tr><td><strong>Example Partner EU</strong><small>Paris region</small></td><td>partner@example.com</td><td>32 / 50</td><td>684 GiB / 1 TiB</td><td>${chip('Bounded', 'warning')}</td><td><span class="wcp-status success">Active</span></td><td><button class="wcp-action-btn" data-iam-action="reseller">Manage</button></td></tr><tr><td><strong>Example Studio</strong><small>Frankfurt region</small></td><td>admin@example.org</td><td>8 / 20</td><td>142 / 500 GiB</td><td>${chip('Disabled')}</td><td><span class="wcp-status success">Active</span></td><td><button class="wcp-action-btn" data-iam-action="reseller">Manage</button></td></tr></tbody></table></div></section>
      ${callout('shield_lock', 'No privilege amplification', 'A reseller cannot expose features, capacity or actions outside the ROOT envelope, even when overselling is enabled.')}
    </div>`,

    quotas: () => `<div class="iam-section" data-iam-view="quotas">
      <div class="iam-section-head"><div><h3>Quota policy and effective capacity</h3><p>Manage plan defaults, reseller pools and account overrides with soft warnings, hard enforcement and inheritance visibility.</p></div>${button('quota', 'Edit quota policy', true, 'tune')}</div>
      <div class="iam-quota-grid"><article><span>Storage</span><strong>142 / 200 GiB</strong><div><i style="width:71%"></i></div><small>Warning 80% · Hard 100%</small></article><article><span>Inodes</span><strong>1.8M / 5M</strong><div><i style="width:36%"></i></div><small>Warning 75% · Hard 100%</small></article><article><span>Bandwidth</span><strong>3.2 / 10 TiB</strong><div><i style="width:32%"></i></div><small>Monthly reset · Burst allowed</small></article><article><span>Databases</span><strong>12 / 20</strong><div><i style="width:60%"></i></div><small>MariaDB + PostgreSQL</small></article></div>
      <section class="wcp-panel">${panelHead('Inheritance and enforcement', 'Plan defaults may be narrowed by parent policy and explicitly overridden per account')}<div class="iam-table-wrap"><table class="iam-table"><thead><tr><th>Resource</th><th>Plan default</th><th>Parent cap</th><th>Account override</th><th>Effective</th><th>Enforcement</th></tr></thead><tbody><tr><td><strong>Storage</strong></td><td>200 GiB</td><td>250 GiB</td><td>—</td><td><strong>200 GiB</strong></td><td>${chip('Hard')}</td></tr><tr><td><strong>CPU</strong></td><td>8 vCPU</td><td>12 vCPU</td><td>10 vCPU</td><td><strong>10 vCPU</strong></td><td>${chip('Throttle')}</td></tr><tr><td><strong>RAM</strong></td><td>16 GiB</td><td>24 GiB</td><td>—</td><td><strong>16 GiB</strong></td><td>${chip('OOM policy')}</td></tr><tr><td><strong>IOPS</strong></td><td>5,000</td><td>8,000</td><td>6,000</td><td><strong>6,000</strong></td><td>${chip('Throttle')}</td></tr><tr><td><strong>Email / hour</strong></td><td>500</td><td>1,000</td><td>250</td><td><strong>250</strong></td><td>${chip('Defer')}</td></tr></tbody></table></div></section>
      ${callout('info', 'Capacity stays separate from authorization', 'A remaining quota never grants access to a disabled module. A valid permission never bypasses a hard limit.')}
    </div>`
  };

  const field = (id, label, type = 'text', extra = '') => `<label class="iam-field" for="${id}"><span>${label}</span><input id="${id}" name="${id}" type="${type}" ${extra} required></label>`;
  const forms = {
    invite: { title: 'Invite user', sub: 'Grant scoped access through a time-bound invitation.', submit: 'Send invitation', body: () => `${field('iam-email', 'Email address', 'email', 'autocomplete="email" placeholder="name@example.com"')}<div class="iam-grid-2"><label class="iam-field"><span>Role</span><select name="role"><option>Operator</option><option>Viewer</option><option>Billing observer</option></select></label><label class="iam-field"><span>Scope</span><select><option>Organization</option><option>Application · example.com</option><option>Infrastructure · EU-PROD</option></select></label></div><label class="iam-check"><input type="checkbox" checked><span><strong>Require MFA at first sign-in</strong><br>Mandatory for privileged scopes.</span></label>` },
    account: { title: 'Create hosting account', sub: 'Provision an isolated account and its first owner.', submit: 'Review account', body: () => `${field('iam-org', 'Organization name', 'text', 'placeholder="Example Company"')}<div class="iam-grid-2">${field('iam-owner', 'Owner email', 'email', 'placeholder="owner@example.com"')}<label class="iam-field"><span>Hosting plan</span><select><option>Business Pro</option><option>Starter</option><option>Agency Cloud</option></select></label></div><div class="iam-security-note"><span class="material-symbols-rounded">verified_user</span><div>Account creation creates an auditable job. ROOT capabilities are never granted by a hosting plan.</div></div>` },
    role: { title: 'Role policy', sub: 'Create or update a least-privilege permission set.', submit: 'Save role', body: () => `${field('iam-role-name', 'Role name', 'text', 'placeholder="Deployment operator"')}<label class="iam-field"><span>Access boundary</span><select><option>USER account</option><option>RESELLER envelope</option><option>ROOT · JIT only</option></select></label><label class="iam-field"><span>Description</span><textarea placeholder="Responsibilities and exclusions"></textarea></label><label class="iam-check"><input type="checkbox" checked><span>Deny shell, tokens, IAM grants and destructive restore by default.</span></label>` },
    plan: { title: 'Hosting plan', sub: 'Combine product availability and a default resource envelope.', submit: 'Save draft', body: () => `${field('iam-plan-name', 'Plan name', 'text', 'placeholder="Agency Plus"')}<div class="iam-grid-2"><label class="iam-field"><span>Feature list</span><select><option>Business modules</option><option>Starter modules</option><option>Agency modules</option></select></label><label class="iam-field"><span>Quota template</span><select><option>Business · 200 GiB</option><option>Starter · 25 GiB</option></select></label></div>` },
    feature: { title: 'Create feature list', sub: 'Define a versioned product capability bundle.', submit: 'Create draft', body: () => `${field('iam-feature-name', 'Feature list name', 'text', 'placeholder="Managed commerce modules"')}<label class="iam-field"><span>Start from</span><select><option>Business modules</option><option>Starter modules</option><option>Empty list</option></select></label>` },
    demo: { title: 'Demo profile', sub: 'Expose a safe, resettable product tour.', submit: 'Save profile', body: () => `${field('iam-demo-name', 'Profile name', 'text', 'placeholder="Sales · Hosting"')}<label class="iam-check"><input type="checkbox" checked disabled><span>Always block secrets, terminal, exports, tokens and destructive actions.</span></label>` },
    reseller: { title: 'Reseller delegation', sub: 'Create or edit a bounded partner envelope.', submit: 'Review delegation', body: () => `${field('iam-reseller-name', 'Reseller name', 'text', 'placeholder="Example Partner EU"')}<div class="iam-grid-2">${field('iam-reseller-owner', 'Owner email', 'email', 'placeholder="owner@example.com"')}<label class="iam-field"><span>Maximum plan</span><select><option>Agency Cloud</option><option>Business Pro</option></select></label></div><label class="iam-check"><input type="checkbox"><span>Permit bounded overselling with alerts and hard parent limits.</span></label>` },
    quota: { title: 'Quota policy', sub: 'Change resource defaults without altering authorization.', submit: 'Save quota draft', body: () => `<div class="iam-grid-2">${field('iam-storage', 'Storage (GiB)', 'number', 'min="1" value="200"')}${field('iam-inodes', 'Inodes', 'number', 'min="1000" value="5000000"')}</div><div class="iam-grid-2">${field('iam-cpu', 'CPU cores', 'number', 'min="1" value="8"')}${field('iam-ram', 'RAM (GiB)', 'number', 'min="1" value="16"')}</div>` },
    trace: { title: 'Permission trace', sub: 'Explain the final authorization decision.', submit: null, body: () => '<div class="iam-trace"><div class="iam-trace-step"><span class="iam-trace-num">1</span><div><strong>Feature availability</strong><small>Business modules exposes the capability</small></div><span class="wcp-perm-pill allow">Allow</span></div><div class="iam-trace-step"><span class="iam-trace-num">2</span><div><strong>Assigned role</strong><small>Platform operator grants the action</small></div><span class="wcp-perm-pill allow">Allow</span></div><div class="iam-trace-step"><span class="iam-trace-num">3</span><div><strong>Scope and policy</strong><small>MFA valid; JIT depends on risk</small></div><span class="wcp-perm-pill inherit">Conditional</span></div><div class="iam-trace-step"><span class="iam-trace-num">4</span><div><strong>Explicit boundaries</strong><small>ROOT IAM and destructive operations denied</small></div><span class="wcp-perm-pill deny">Deny wins</span></div></div>' },
    'manage-user': { title: 'Manage identity', sub: 'Review authentication, sessions, assignments and lifecycle.', submit: 'Save changes', body: () => '<div class="iam-detail-grid drawer"><span><small>Authentication</small><strong>Passkey + TOTP</strong></span><span><small>Active sessions</small><strong>2</strong></span><span><small>Role assignments</small><strong>1 direct · 2 inherited</strong></span><span><small>Last review</small><strong>12 days ago</strong></span></div><div class="iam-security-note"><span class="material-symbols-rounded">warning</span><div>Suspension revokes sessions and tokens. Deletion requires approval and typed confirmation.</div></div>' }
  };

  function renderShell() {
    document.body.classList.add('iam-page');
    document.body.dataset.productScope = 'root';
    document.title = 'Identity & Access — WCP360 ROOT';
    const title = qs('.wcp-page-title');
    if (title) title.textContent = 'Identity & Access';
    const host = qs('.wcp-content-inner');
    if (!host) return;
    host.innerHTML = `<div class="wcp-page-head"><div><div class="wcp-page-eyebrow">WCP-ADMIN · IDENTITY GOVERNANCE</div><h2 class="wcp-page-heading">Identity & Access</h2><p class="wcp-page-sub">Accounts, roles, hosting plans, delegated resellers, effective capabilities and resource limits across WCP360.</p></div><div class="wcp-page-actions"><a class="wcp-action-btn" href="audit-explorer.html"><span class="material-symbols-rounded">manage_search</span>Audit evidence</a>${button('invite', 'Invite user')}${button('account', 'Create account', true, 'person_add')}</div></div>${metricStrip()}<div class="wcp-tabs iam-family-tabs" role="tablist" aria-label="Identity and access sections">${sections.map(([id, icon, label]) => `<button type="button" class="wcp-tab" role="tab" data-iam-tab="${id}" aria-controls="iamView" aria-selected="false"><span class="material-symbols-rounded">${icon}</span>${label}</button>`).join('')}</div><div id="iamView" class="iam-view" role="tabpanel" tabindex="0"></div>`;
    ensureDrawer();
    renderView();
  }

  function renderView() {
    const view = qs('#iamView');
    if (!view) return;
    view.innerHTML = views[active]();
    qsa('[data-iam-tab]').forEach(tab => {
      const selected = tab.dataset.iamTab === active;
      tab.classList.toggle('active', selected);
      tab.setAttribute('aria-selected', String(selected));
      tab.tabIndex = selected ? 0 : -1;
    });
    filterUsers();
  }

  function setTab(tab, updateUrl = true) {
    if (!views[tab]) return;
    active = tab;
    if (updateUrl) history.replaceState(null, '', location.pathname + '#' + tab);
    renderView();
    qs('#iamView')?.focus({ preventScroll: true });
  }

  function ensureDrawer() {
    if (qs('#iamDrawer')) return;
    document.body.insertAdjacentHTML('beforeend', '<button class="iam-drawer-backdrop" type="button" data-iam-close aria-label="Close dialog"></button><aside id="iamDrawer" class="iam-drawer" role="dialog" aria-modal="true" aria-hidden="true" aria-labelledby="iamDrawerTitle"><div class="iam-drawer-head"><div><div id="iamDrawerTitle" class="iam-drawer-title"></div><div class="iam-drawer-sub"></div></div><button type="button" class="wcp-close-btn" data-iam-close aria-label="Close"><span class="material-symbols-rounded">close</span></button></div><div class="iam-drawer-body"></div></aside>');
  }

  function openDrawer(kind, trigger) {
    const config = forms[kind] || forms.trace;
    const drawer = qs('#iamDrawer');
    returnFocus = trigger || document.activeElement;
    qs('.iam-drawer-title', drawer).textContent = config.title;
    qs('.iam-drawer-sub', drawer).textContent = config.sub;
    qs('.iam-drawer-body', drawer).innerHTML = '<div class="iam-success" role="status">Saved. The change was added to the audit trail.</div><form class="iam-form" data-iam-form="' + kind + '">' + config.body() + '<div class="iam-form-actions"><button type="button" class="wcp-action-btn" data-iam-close>' + (config.submit ? 'Cancel' : 'Done') + '</button>' + (config.submit ? '<button type="submit" class="wcp-action-btn primary">' + config.submit + '</button>' : '') + '</div></form>';
    drawer.classList.add('open');
    drawer.setAttribute('aria-hidden', 'false');
    qs('.iam-drawer-backdrop')?.classList.add('open');
    setTimeout(() => qs('input,select,textarea,button', drawer)?.focus(), 20);
  }

  function closeDrawer() {
    const drawer = qs('#iamDrawer');
    drawer?.classList.remove('open');
    drawer?.setAttribute('aria-hidden', 'true');
    qs('.iam-drawer-backdrop')?.classList.remove('open');
    returnFocus?.focus?.();
  }

  function filterUsers() {
    const search = qs('#iamUserSearch');
    if (!search) return;
    const query = search.value.trim().toLowerCase();
    const status = qs('#iamStatusFilter')?.value || 'all';
    const role = qs('#iamRoleFilter')?.value || 'all';
    let visible = 0;
    qsa('[data-user-search]').forEach(card => {
      const match = (!query || card.dataset.userSearch.includes(query)) && (status === 'all' || card.dataset.userStatus === status) && (role === 'all' || card.dataset.userRole === role);
      card.hidden = !match;
      if (match) visible += 1;
    });
    const empty = qs('[data-iam-empty]');
    if (empty) empty.style.display = visible ? 'none' : 'block';
  }

  document.addEventListener('click', event => {
    const tab = event.target.closest('[data-iam-tab]');
    if (tab) {
      event.preventDefault();
      setTab(tab.dataset.iamTab);
      return;
    }
    if (event.target.closest('[data-iam-close]')) {
      event.preventDefault();
      closeDrawer();
      return;
    }
    const toggle = event.target.closest('.iam-matrix-toggle');
    if (toggle) {
      const next = toggle.getAttribute('aria-pressed') !== 'true';
      toggle.setAttribute('aria-pressed', String(next));
      toggle.textContent = next ? 'Allowed' : 'Blocked';
      return;
    }
    const control = event.target.closest('[data-iam-action]');
    if (!control) return;
    event.preventDefault();
    const kind = control.dataset.iamAction;
    if (['resend', 'revoke', 'clone', 'publish-features', 'review', 'export', 'evaluate', 'launch-demo'].includes(kind)) {
      document.dispatchEvent(new CustomEvent('wcp:notification', { detail: { title: 'Identity & Access', detail: control.textContent.trim() + ' completed in prototype mode.' } }));
      if (kind === 'revoke') control.closest('[data-user-search]')?.remove();
      return;
    }
    openDrawer(kind, control);
  });
  document.addEventListener('input', event => { if (event.target.matches('#iamUserSearch')) filterUsers(); });
  document.addEventListener('change', event => { if (event.target.matches('#iamStatusFilter,#iamRoleFilter')) filterUsers(); });
  document.addEventListener('submit', event => {
    const form = event.target.closest('[data-iam-form]');
    if (!form) return;
    event.preventDefault();
    if (!form.reportValidity()) return;
    if (['invite', 'account'].includes(form.dataset.iamForm)) {
      const data = new FormData(form);
      const email = data.get('iam-email') || data.get('iam-owner') || 'new.user@example.com';
      setTab('users');
      qs('[data-iam-user-grid]')?.insertAdjacentHTML('beforeend', '<article class="wcp-user-card" data-user-search="' + String(email).toLowerCase() + ' pending" data-user-status="pending" data-user-role="operator"><div class="wcp-user-head"><div><div class="wcp-user-title"></div><div class="wcp-user-sub">New identity · Invitation issued</div></div><span class="wcp-status warning">Pending</span></div><div class="iam-user-meta">' + chip('MFA required') + chip('Expires in 24h', 'warning') + '</div></article>');
      const newCard = qs('[data-iam-user-grid]')?.lastElementChild;
      if (newCard) qs('.wcp-user-title', newCard).textContent = email;
    }
    qs('.iam-success', form.parentElement)?.classList.add('show');
    qsa('input,select,textarea,button', form).forEach(control => { control.disabled = true; });
    setTimeout(closeDrawer, 900);
  });
  document.addEventListener('keydown', event => { if (event.key === 'Escape' && qs('#iamDrawer.open')) closeDrawer(); });
  window.addEventListener('hashchange', () => {
    const target = location.hash.slice(1);
    if (views[target]) setTab(target, false);
  });

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', renderShell);
  else renderShell();
})();
