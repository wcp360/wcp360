(function () {
  const page = (location.pathname.split('/').pop() || '').toLowerCase();
  if (page === 'reseller-governance.html') {
    location.replace('reseller.html#governance');
    return;
  }
  if (page !== 'reseller.html') return;

  const qs = (selector, context = document) => context.querySelector(selector);
  const qsa = (selector, context = document) => [...context.querySelectorAll(selector)];
  const tabs = [
    ['overview', 'space_dashboard', 'Overview'],
    ['customers', 'business', 'Customers'],
    ['packages', 'inventory_2', 'Packages'],
    ['capacity', 'data_usage', 'Capacity'],
    ['services', 'dns', 'Services'],
    ['governance', 'policy', 'Governance'],
    ['brand', 'palette', 'Brand'],
    ['transfers', 'move_up', 'Transfers'],
    ['notifications', 'notifications', 'Notifications'],
    ['audit', 'manage_search', 'Audit & support']
  ];
  const key = 'wcp360-reseller-center-v1';
  const defaults = {
    customers: [
      { id: 'acme', name: 'Acme Studio', domain: 'acme.example.com', owner: 'owner@acme.example', package: 'Agency Pro', storage: 62, transfer: 48, status: 'Active' },
      { id: 'nova', name: 'Nova Retail', domain: 'nova.example.com', owner: 'ops@nova.example', package: 'Business', storage: 74, transfer: 81, status: 'Active' },
      { id: 'demo', name: 'Demo Partner', domain: 'demo.example.com', owner: 'demo@example.com', package: 'Trial', storage: 12, transfer: 9, status: 'Trial' },
      { id: 'legacy', name: 'Legacy Customer', domain: 'legacy.example.com', owner: 'admin@legacy.example', package: 'Starter', storage: 91, transfer: 67, status: 'Suspended' }
    ],
    services: { websites: true, email: true, databases: true, dns: true, ssl: true, backups: true, terminal: false, dedicatedIp: true },
    brand: { name: 'Example Partner', panel: 'panel.partner.example.com', support: 'support@partner.example.com', nameservers: 'ns1.partner.example.com · ns2.partner.example.com' }
  };
  const read = () => { try { return JSON.parse(localStorage.getItem(key)) || {}; } catch (error) { return {}; } };
  const stored = read();
  const state = { ...defaults, ...stored, customers: Array.isArray(stored.customers) ? stored.customers : defaults.customers, services: { ...defaults.services, ...(stored.services || {}) }, brand: { ...defaults.brand, ...(stored.brand || {}) } };
  const save = () => { try { localStorage.setItem(key, JSON.stringify(state)); } catch (error) {} };
  let active = tabs.some(item => item[0] === location.hash.slice(1)) ? location.hash.slice(1) : 'overview';
  let returnFocus = null;

  const chip = (label, tone = '') => `<span class="wcp-status ${tone}">${label}</span>`;
  const action = (kind, label, primary = false, icon = 'add') => `<button type="button" class="wcp-action-btn ${primary ? 'primary' : ''}" data-res-action="${kind}"><span class="material-symbols-rounded">${icon}</span>${label}</button>`;
  const panelHead = (title, copy, controls = '') => `<div class="wcp-panel-head res-panel-head"><div><div class="wcp-panel-title">${title}</div><div class="wcp-panel-sub">${copy}</div></div><div class="res-panel-actions">${controls}</div></div>`;
  const note = (icon, title, copy) => `<div class="res-note"><span class="material-symbols-rounded">${icon}</span><div><strong>${title}</strong><p>${copy}</p></div></div>`;
  const capacity = (label, value, percent, copy, warning = false) => `<article class="res-capacity"><span>${label}</span><strong>${value}</strong><div class="res-progress ${warning ? 'warning' : ''}"><i style="width:${Math.min(percent, 100)}%"></i></div><small>${copy}</small></article>`;
  const service = (id, icon, title, copy, rootPolicy) => `<div class="res-service"><span class="material-symbols-rounded">${icon}</span><div><strong>${title}</strong><small>${copy} · ROOT policy: ${rootPolicy}</small></div><label class="res-toggle" aria-label="Enable ${title} for reseller packages"><input type="checkbox" data-res-service="${id}" ${state.services[id] ? 'checked' : ''}><i></i></label></div>`;

  const views = {
    overview: () => `<div class="res-section" data-res-view="overview">
      <div class="res-section-head"><div><h3>Delegated hosting workspace</h3><p>Operate customer accounts without access to servers, global services or ROOT policy. Every action is intersected with the envelope delegated by ROOT.</p></div><div>${action('invite-operator', 'Invite operator')}${action('create-customer', 'Create customer', true, 'person_add')}</div></div>
      <div class="res-grid">
        <article class="res-card"><div class="res-card-head"><span class="material-symbols-rounded">business</span>${chip('18 / 25', 'success')}</div><h4>Customer accounts</h4><p>Create, modify, suspend and recover only accounts owned by this reseller.</p><div class="res-kv"><div><span>Active</span><strong>16</strong></div><div><span>Suspended</span><strong>2</strong></div><div><span>Pending jobs</span><strong>3</strong></div></div><footer><button class="wcp-action-btn" data-res-tab="customers">Open customers</button></footer></article>
        <article class="res-card"><div class="res-card-head"><span class="material-symbols-rounded">data_usage</span>${chip('76%', 'warning')}</div><h4>Delegated capacity</h4><p>Allocated and consumed resources remain distinct. Hard ROOT ceilings cannot be exceeded.</p><div class="res-kv"><div><span>Storage</span><strong>1.2 / 1.6 TiB</strong></div><div><span>Transfer</span><strong>8.4 / 14 TiB</strong></div><div><span>Dedicated IPs</span><strong>6 / 8</strong></div></div><footer><button class="wcp-action-btn" data-res-tab="capacity">Inspect capacity</button></footer></article>
        <article class="res-card"><div class="res-card-head"><span class="material-symbols-rounded">policy</span>${chip('Compliant', 'success')}</div><h4>Governance posture</h4><p>Operator ACLs, customer consent and immutable delegated audit evidence.</p><div class="res-kv"><div><span>Operators</span><strong>6</strong></div><div><span>ACL profiles</span><strong>4</strong></div><div><span>Denied actions · 7d</span><strong>14</strong></div></div><footer><button class="wcp-action-btn" data-res-tab="governance">Review access</button></footer></article>
      </div>
      <section class="wcp-panel">${panelHead('Recent delegated activity', 'Customer-visible operations and policy decisions')}<div class="res-event-list"><div class="res-event"><span class="material-symbols-rounded">person_add</span><div><strong>Customer account provisioned</strong><small>nova.example.com · Business · operator@example.com</small></div><small>12 min</small></div><div class="res-event"><span class="material-symbols-rounded">shield_lock</span><div><strong>Dedicated IP request denied</strong><small>Pool ceiling reached · ROOT policy RES-IP-04</small></div><small>31 min</small></div><div class="res-event"><span class="material-symbols-rounded">support_agent</span><div><strong>Support session completed</strong><small>Acme Studio · customer consent · 18 minutes</small></div><small>2 h</small></div></div></section>
    </div>`,

    customers: () => `<div class="res-section" data-res-view="customers">
      <div class="res-section-head"><div><h3>Customer accounts</h3><p>Customer ownership, package assignment and lifecycle operations inside the reseller boundary.</p></div><div>${action('bulk-customer', 'Bulk operation', false, 'checklist')}${action('create-customer', 'Create customer', true, 'person_add')}</div></div>
      <div class="res-toolbar"><label class="res-search"><span class="material-symbols-rounded">search</span><input type="search" data-res-customer-search placeholder="Search customer, domain, owner or package" aria-label="Search customer accounts"></label><select data-res-customer-status aria-label="Filter customer status"><option value="all">All statuses</option><option>Active</option><option>Trial</option><option>Suspended</option></select></div>
      <section class="wcp-panel">${panelHead('Owned accounts', 'The underlying server and global service topology remain hidden')}<div class="res-table-wrap"><table class="res-table"><thead><tr><th>Customer</th><th>Owner</th><th>Package</th><th>Storage</th><th>Transfer</th><th>Status</th><th></th></tr></thead><tbody data-res-customers>${state.customers.map(customer => `<tr data-res-customer="${customer.id}" data-res-search="${(customer.name + ' ' + customer.domain + ' ' + customer.owner + ' ' + customer.package).toLowerCase()}" data-res-status="${customer.status}"><td><strong>${customer.name}</strong><small>${customer.domain}</small></td><td>${customer.owner}</td><td>${customer.package}</td><td>${customer.storage}%</td><td>${customer.transfer}%</td><td>${chip(customer.status, customer.status === 'Active' ? 'success' : 'warning')}</td><td><button class="wcp-action-btn" data-res-action="manage-customer" data-res-id="${customer.id}">Manage</button></td></tr>`).join('')}</tbody></table></div></section><div class="res-empty" data-res-empty>No customer matches these filters.</div>
      ${note('visibility', 'Customer portal remains separate', 'A reseller changes the account envelope here. The customer operates websites, mail, databases and files from WCP-USER with its own identity and audit trail.')}
    </div>`,

    packages: () => `<div class="res-section" data-res-view="packages">
      <div class="res-section-head"><div><h3>Packages derived from ROOT plans</h3><p>Create commercial packages only from features and maximum values that ROOT delegated to this reseller.</p></div>${action('create-package', 'Create package', true, 'add_box')}</div>
      <div class="res-grid"><article class="res-card"><div class="res-card-head"><span class="material-symbols-rounded">rocket_launch</span>${chip('Published', 'success')}</div><h4>Starter</h4><p>5 sites · 25 GiB · 2 vCPU · 10 mailboxes.</p><div class="res-kv"><div><span>Customers</span><strong>7</strong></div><div><span>ROOT source</span><strong>Starter v12</strong></div><div><span>Margin to ceiling</span><strong>75%</strong></div></div><footer>${action('edit-package', 'Edit')}</footer></article><article class="res-card"><div class="res-card-head"><span class="material-symbols-rounded">workspace_premium</span>${chip('Published', 'success')}</div><h4>Business</h4><p>25 sites · 200 GiB · 8 vCPU · staging and Git.</p><div class="res-kv"><div><span>Customers</span><strong>9</strong></div><div><span>ROOT source</span><strong>Business Pro v18</strong></div><div><span>Margin to ceiling</span><strong>42%</strong></div></div><footer>${action('edit-package', 'Edit')}</footer></article><article class="res-card"><div class="res-card-head"><span class="material-symbols-rounded">storefront</span>${chip('Draft', 'warning')}</div><h4>Agency Pro</h4><p>100 sites · 500 GiB · bounded storage overselling.</p><div class="res-kv"><div><span>Customers</span><strong>2</strong></div><div><span>ROOT source</span><strong>Agency Cloud v7</strong></div><div><span>Approval</span><strong>Required</strong></div></div><footer>${action('edit-package', 'Continue')}</footer></article></div>
      ${note('rule', 'Inheritance is one-way', 'ROOT publishes plans and feature ceilings. RESELLER may narrow them into packages. USER receives one package and cannot widen it.')}
    </div>`,

    capacity: () => `<div class="res-section" data-res-view="capacity">
      <div class="res-section-head"><div><h3>Capacity, quotas and overselling</h3><p>Compare ROOT delegation, reseller allocation and observed customer consumption before provisioning.</p></div>${action('request-capacity', 'Request more capacity', true, 'add_chart')}</div>
      <div class="res-capacity-grid">${capacity('Storage', '1.2 / 1.6 TiB', 76, 'Used 890 GiB · storage overselling 120%', true)}${capacity('Guaranteed RAM', '92 / 128 GiB', 72, 'Cannot be oversold')}${capacity('CPU', '44 / 64 vCPU', 69, 'Burst allowed · guarantee enforced')}${capacity('Monthly transfer', '8.4 / 14 TiB', 60, 'Warning at 80%')}${capacity('Dedicated IPs', '6 / 8', 75, 'Assignment only · no overselling')}${capacity('Databases', '94 / 160', 59, 'Across all customer packages')}${capacity('Mailboxes', '284 / 500', 57, 'Outbound rate policy inherited')}${capacity('Customer accounts', '18 / 25', 72, 'ROOT hard account ceiling')}</div>
      <section class="wcp-panel">${panelHead('Resource policy', 'Overselling is explicit per resource, never global')}<div class="res-policy-list"><div class="res-policy"><span class="material-symbols-rounded">hard_drive</span><div><strong>Storage overselling · 120%</strong><small>Allowed while physical and forecast capacity remain healthy.</small></div>${chip('Allowed', 'success')}</div><div class="res-policy"><span class="material-symbols-rounded">memory</span><div><strong>RAM and CPU guarantees</strong><small>Reservations must fit the ROOT envelope before provisioning.</small></div>${chip('Hard limit', 'warning')}</div><div class="res-policy"><span class="material-symbols-rounded">lan</span><div><strong>Dedicated IP addresses</strong><small>Only explicitly delegated addresses can be assigned.</small></div>${chip('8 addresses')}</div></div></section>
    </div>`,

    services: () => `<div class="res-section" data-res-view="services">
      <div class="res-section-head"><div><h3>Delegated customer services</h3><p>Choose which ROOT-authorized services may appear in reseller packages. This never enables or configures server daemons.</p></div>${action('save-services', 'Publish service catalog', true, 'publish')}</div>
      <section class="wcp-panel">${panelHead('Package service catalog', 'Availability is ROOT allowance ∩ reseller choice ∩ customer package')}<div class="res-service-list">${service('websites','web','Websites & domains','Sites, redirects, runtimes and DNS records','Allowed')}${service('email','mail','Email & external webmail','Mailboxes, routing and Roundcube/SnappyMail/SoGo access','Allowed')}${service('databases','database','Databases','MariaDB, PostgreSQL and approved external SSO tools','Allowed')}${service('dns','language','DNS management','Customer-zone records only; no cluster access','Allowed')}${service('ssl','verified_user','SSL & AutoSSL','Certificates for owned customer domains','Allowed')}${service('backups','backup','Backups & restore','Customer restore points inside delegated retention','Allowed')}${service('terminal','terminal','SSH terminal','Account chroot only; package and operator restrictions','Conditional')}${service('dedicatedIp','lan','Dedicated IP assignment','Only from the delegated reseller pool','Allowed')}</div></section>
      ${note('dns', 'Infrastructure remains ROOT-owned', 'Service switches control customer visibility and package eligibility. They cannot start daemons, alter global mail/DNS/database policy or select physical servers.')}
    </div>`,

    governance: () => `<div class="res-section" data-res-view="governance">
      <div class="res-section-head"><div><h3>Operators, ACL profiles and effective access</h3><p>Separate owner, provisioning, support and billing duties without granting capabilities outside the ROOT envelope.</p></div><div>${action('compare-access', 'Compare access')}${action('invite-operator', 'Invite operator', true, 'person_add')}</div></div>
      <section class="wcp-panel">${panelHead('ACL profiles', 'Effective privilege = ROOT delegation ∩ reseller ACL ∩ target ownership')}<div class="res-table-wrap"><table class="res-table"><thead><tr><th>Profile</th><th>Operators</th><th>Account actions</th><th>Packages</th><th>Support access</th><th>Status</th><th></th></tr></thead><tbody><tr><td><strong>Reseller Owner</strong><small>Commercial and technical owner</small></td><td>2</td><td>Full owned lifecycle</td><td>Create within ceiling</td><td>Consent required</td><td>${chip('Active','success')}</td><td>${action('edit-acl','Edit')}</td></tr><tr><td><strong>Provisioning</strong><small>No billing or branding</small></td><td>1</td><td>Create · modify · suspend</td><td>Use approved</td><td>None</td><td>${chip('Active','success')}</td><td>${action('edit-acl','Edit')}</td></tr><tr><td><strong>Support</strong><small>Customer-visible activity</small></td><td>2</td><td>Read only</td><td>Read</td><td>Time-bound</td><td>${chip('Conditional','warning')}</td><td>${action('edit-acl','Edit')}</td></tr><tr><td><strong>Billing</strong><small>No service configuration</small></td><td>1</td><td>Read · billing hold</td><td>Read</td><td>None</td><td>${chip('Review','warning')}</td><td>${action('edit-acl','Edit')}</td></tr></tbody></table></div></section>
      ${note('shield_lock', 'No privilege amplification', 'A reseller operator cannot grant an ACL, package feature, resource ceiling or customer action that ROOT did not delegate to the reseller.')}
    </div>`,

    brand: () => `<div class="res-section" data-res-view="brand">
      <div class="res-section-head"><div><h3>Brand, nameservers and customer communication</h3><p>Customize the customer-facing identity inside ROOT-approved domains, themes and localization policy.</p></div>${action('edit-brand', 'Edit brand', true, 'edit')}</div>
      <div class="res-grid"><article class="res-card"><div class="res-card-head"><span class="material-symbols-rounded">palette</span>${chip('Active','success')}</div><h4>${state.brand.name}</h4><p>Logo, favicon, light/dark theme and customer panel identity.</p><div class="res-kv"><div><span>Panel domain</span><strong>${state.brand.panel}</strong></div><div><span>Support</span><strong>${state.brand.support}</strong></div></div></article><article class="res-card"><div class="res-card-head"><span class="material-symbols-rounded">dns</span>${chip('Verified','success')}</div><h4>Branded nameservers</h4><p>Delegated vanity nameservers mapped to ROOT-operated authoritative infrastructure.</p><div class="res-kv"><div><span>Nameservers</span><strong>${state.brand.nameservers}</strong></div><div><span>Glue / rDNS</span><strong>ROOT managed</strong></div></div></article><article class="res-card"><div class="res-card-head"><span class="material-symbols-rounded">translate</span>${chip('4 locales')}</div><h4>Customer localization</h4><p>English, French, German and Spanish with reseller-specific notices.</p><div class="res-kv"><div><span>Default</span><strong>English</strong></div><div><span>Fallback</span><strong>ROOT dictionary</strong></div></div></article></div>
      ${note('verified_user', 'ROOT keeps trust ownership', 'ROOT validates panel domains, TLS, DNS delegation and theme safety. The reseller controls content only within that approved boundary.')}
    </div>`,

    transfers: () => `<div class="res-section" data-res-view="transfers">
      <div class="res-section-head"><div><h3>Customer transfers and imports</h3><p>Request account imports or moves using ROOT-approved sources, destinations and maintenance policy.</p></div>${action('new-transfer', 'New transfer', true, 'move_up')}</div>
      <section class="wcp-panel">${panelHead('Transfer jobs', 'Credentials are encrypted and never exposed to reseller operators')}<div class="res-table-wrap"><table class="res-table"><thead><tr><th>Job</th><th>Customer</th><th>Source</th><th>Target</th><th>Progress</th><th>Status</th><th></th></tr></thead><tbody><tr><td><strong>TR-2048</strong><small>Account import</small></td><td>Nova Retail</td><td>cPanel remote</td><td>ROOT auto-placement</td><td>72%</td><td>${chip('Running','warning')}</td><td>${action('inspect-transfer','Inspect')}</td></tr><tr><td><strong>TR-2041</strong><small>Package migration</small></td><td>Acme Studio</td><td>WCP360 reseller</td><td>Agency Pro</td><td>100%</td><td>${chip('Completed','success')}</td><td>${action('inspect-transfer','Evidence')}</td></tr><tr><td><strong>TR-2039</strong><small>Direct SFTP/SQL</small></td><td>Legacy Customer</td><td>External Linux</td><td>Validation hold</td><td>18%</td><td>${chip('Review','warning')}</td><td>${action('inspect-transfer','Resolve')}</td></tr></tbody></table></div></section>
      ${note('account_tree', 'Placement is selected by ROOT policy', 'The reseller chooses the customer and package. The control plane chooses a compatible server or cluster and exposes only region and service guarantees.')}
    </div>`,

    notifications: () => `<div class="res-section" data-res-view="notifications">
      <div class="res-section-head"><div><h3>Notifications and customer notices</h3><p>Route reseller operational alerts and publish customer-facing notices without changing ROOT incident routing.</p></div>${action('create-notice', 'Create notice', true, 'campaign')}</div>
      <div class="res-grid"><article class="res-card"><div class="res-card-head"><span class="material-symbols-rounded">notifications_active</span>${chip('Healthy','success')}</div><h4>Operator alerts</h4><p>Capacity, customer jobs, delivery failures and delegated policy denials.</p><div class="res-kv"><div><span>Email</span><strong>3 recipients</strong></div><div><span>Webhook</span><strong>Signed · healthy</strong></div><div><span>Digest</span><strong>Daily 08:00</strong></div></div><footer>${action('edit-notifications','Configure')}</footer></article><article class="res-card"><div class="res-card-head"><span class="material-symbols-rounded">campaign</span>${chip('1 active')}</div><h4>Customer notices</h4><p>Maintenance, service degradation and commercial announcements scoped to owned customers.</p><div class="res-kv"><div><span>Active banner</span><strong>Mail maintenance</strong></div><div><span>Audience</span><strong>Business package</strong></div><div><span>Expires</span><strong>Oct 31 · 06:00</strong></div></div><footer>${action('create-notice','Manage')}</footer></article><article class="res-card"><div class="res-card-head"><span class="material-symbols-rounded">error</span>${chip('2 failed','warning')}</div><h4>Delivery health</h4><p>Retries, acknowledgements and dead letters for reseller-owned destinations.</p><div class="res-kv"><div><span>24 h delivered</span><strong>98.7%</strong></div><div><span>Retrying</span><strong>2</strong></div><div><span>Dead letters</span><strong>0</strong></div></div><footer>${action('inspect-delivery','Inspect')}</footer></article></div>
    </div>`,

    audit: () => `<div class="res-section" data-res-view="audit">
      <div class="res-section-head"><div><h3>Delegated audit, consent and support</h3><p>Search every reseller action, customer support session and ROOT policy decision visible to this reseller.</p></div><div>${action('export-audit','Export redacted',false,'download')}${action('open-support','Open support request',true,'support_agent')}</div></div>
      <div class="res-toolbar"><label class="res-search"><span class="material-symbols-rounded">search</span><input type="search" placeholder="Search actor, customer, operation or correlation ID" aria-label="Search reseller audit"></label><select aria-label="Audit time range"><option>Last 7 days</option><option>Last 30 days</option><option>Custom range</option></select></div>
      <section class="wcp-panel">${panelHead('Immutable delegated evidence', 'ROOT infrastructure details and other tenants are redacted')}<div class="res-event-list"><div class="res-event"><span class="material-symbols-rounded">manage_accounts</span><div><strong>Package changed · Acme Studio</strong><small>operator@example.com · Business → Agency Pro · COR-88421</small></div><small>12 min</small></div><div class="res-event"><span class="material-symbols-rounded">support_agent</span><div><strong>Customer support consent granted</strong><small>owner@nova.example · 30 minute session · COR-88408</small></div><small>48 min</small></div><div class="res-event"><span class="material-symbols-rounded">gpp_bad</span><div><strong>Dedicated IP assignment denied</strong><small>ROOT policy RES-IP-04 · delegated pool exhausted · COR-88394</small></div><small>2 h</small></div><div class="res-event"><span class="material-symbols-rounded">pause_circle</span><div><strong>Customer account suspended</strong><small>billing@example.com · billing hold · customer notified</small></div><small>1 d</small></div></div></section>
      ${note('support_agent', 'Support never bypasses boundaries', 'A reseller may request ROOT help or enter a customer support session only with consent, an expiry and a customer-visible audit record.')}
    </div>`
  };

  const formField = (name, label, value = '', type = 'text') => `<label class="res-field"><span>${label}</span><input name="${name}" type="${type}" value="${value}" required></label>`;
  const forms = {
    'create-customer': { title: 'Create customer account', copy: 'Provision within the ROOT-delegated envelope.', submit: 'Review provisioning', body: () => `${formField('name','Customer organization')}<div class="res-form-grid">${formField('owner','Owner email','owner@example.com','email')}${formField('domain','Primary domain','customer.example.com')}</div><div class="res-form-grid"><label class="res-field"><span>Package</span><select name="package"><option>Business</option><option>Starter</option><option>Agency Pro</option><option>Trial</option></select></label><label class="res-field"><span>Region</span><select><option>EU · ROOT automatic placement</option><option>France · ROOT automatic placement</option></select></label></div>${note('shield_lock','Provisioning boundary','ROOT selects physical placement and validates capacity. The reseller owns the customer relationship and package assignment.')}` },
    'create-package': { title: 'Create reseller package', copy: 'Narrow a ROOT plan into a customer offer.', submit: 'Create draft', body: () => `${formField('packageName','Package name','Partner Business')}<div class="res-form-grid"><label class="res-field"><span>ROOT plan source</span><select><option>Business Pro v18</option><option>Starter v12</option><option>Agency Cloud v7</option></select></label>${formField('storage','Storage limit (GiB)','200','number')}</div>${note('rule','Maximums enforced','Features and limits above the delegated ROOT plan are unavailable, not merely disabled after submission.')}` },
    'invite-operator': { title: 'Invite reseller operator', copy: 'Assign a bounded reseller ACL profile.', submit: 'Send invitation', body: () => `${formField('operatorEmail','Email address','operator@example.com','email')}<label class="res-field"><span>ACL profile</span><select><option>Support</option><option>Provisioning</option><option>Billing</option><option>Reseller Owner</option></select></label>${note('policy','Effective access','The selected ACL is intersected with the ROOT reseller delegation and customer ownership.')}` },
    'edit-brand': { title: 'Edit reseller brand', copy: 'Update customer-facing identity within ROOT policy.', submit: 'Validate brand', body: () => `${formField('brandName','Brand name',state.brand.name)}${formField('panel','Panel domain',state.brand.panel)}${formField('support','Support email',state.brand.support,'email')}${formField('nameservers','Branded nameservers',state.brand.nameservers)}` },
    'new-transfer': { title: 'New customer transfer', copy: 'Import or move an owned account through a durable ROOT job.', submit: 'Run preflight', body: () => `<label class="res-field"><span>Customer</span><select><option>Acme Studio</option><option>Nova Retail</option><option>Legacy Customer</option></select></label><div class="res-form-grid"><label class="res-field"><span>Source</span><select><option>cPanel remote</option><option>Plesk remote</option><option>DirectAdmin remote</option><option>Direct SFTP / SQL</option></select></label><label class="res-field"><span>Target package</span><select><option>Business</option><option>Agency Pro</option><option>Starter</option></select></label></div>${note('lock','Secret handling','Remote credentials are encrypted, single-use and never returned to reseller operators or audit exports.')}` },
    'open-support': { title: 'Open ROOT support request', copy: 'Share only selected customer evidence and diagnostics.', submit: 'Submit request', body: () => `<label class="res-field"><span>Category</span><select><option>Provisioning failure</option><option>Capacity request</option><option>Mail deliverability</option><option>Migration support</option><option>Security incident</option></select></label><label class="res-field"><span>Description</span><textarea name="description" required placeholder="Describe the problem and affected customer"></textarea></label>${note('privacy_tip','Consent and redaction','Customer data is excluded unless specifically selected and authorized. ROOT receives a time-limited support bundle.')}` }
  };

  function renderShell() {
    document.title = 'Reseller Center — WCP360 RESELLER';
    const title = qs('.wcp-page-title');
    if (title) title.textContent = 'Reseller Center';
    const host = qs('#parityCenter');
    if (!host) return;
    host.innerHTML = `<div class="res-page"><div class="wcp-page-head"><div><div class="wcp-page-eyebrow">WCP-RESELLER · DELEGATED HOSTING</div><h2 class="wcp-page-heading">Reseller Center</h2><p class="wcp-page-sub">Customer accounts, packages, capacity, services and delegated governance in a boundary controlled by ROOT.</p></div><div class="wcp-page-actions">${action('open-support','ROOT support',false,'support_agent')}${action('create-customer','Create customer',true,'person_add')}</div></div><section class="res-metrics"><article><span>Customer accounts</span><strong>${state.customers.length} / 25</strong><small>ROOT hard ceiling</small></article><article><span>Allocated storage</span><strong>1.2 / 1.6 TiB</strong><small>76% · warning</small></article><article><span>Active packages</span><strong>4</strong><small>3 published · 1 draft</small></article><article><span>Delegated posture</span><strong>Compliant</strong><small>14 denied actions enforced</small></article></section><section class="res-boundary" aria-label="Delegation chain"><div><span>ROOT</span><strong>Infrastructure · plans · maximum envelope</strong></div><span class="material-symbols-rounded">arrow_forward</span><div><span>RESELLER</span><strong>Customers · packages · delegated operations</strong></div><span class="material-symbols-rounded">arrow_forward</span><div><span>USER</span><strong>Websites · mail · databases · files</strong></div></section><div class="wcp-tabs res-tabs" role="tablist" aria-label="Reseller Center sections">${tabs.map(([id, icon, label]) => `<button type="button" class="wcp-tab" role="tab" data-res-tab="${id}" aria-controls="resView" aria-selected="false"><span class="material-symbols-rounded">${icon}</span>${label}</button>`).join('')}</div><div id="resView" class="res-view" role="tabpanel" tabindex="0"></div></div>`;
    ensureDrawer();
    renderView();
  }

  function renderView() {
    const view = qs('#resView');
    if (!view) return;
    view.innerHTML = views[active]();
    qsa('[data-res-tab]').forEach(control => {
      const selected = control.dataset.resTab === active;
      control.classList.toggle('active', selected);
      control.setAttribute('aria-selected', String(selected));
      control.tabIndex = selected ? 0 : -1;
    });
    filterCustomers();
  }

  function setTab(id, updateHash = true) {
    if (!views[id]) return;
    active = id;
    if (updateHash && location.hash.slice(1) !== id) location.hash = id;
    renderView();
    qs('#resView')?.focus({ preventScroll: true });
  }

  function ensureDrawer() {
    if (qs('#resDrawer')) return;
    document.body.insertAdjacentHTML('beforeend', '<button class="res-drawer-backdrop" type="button" data-res-close aria-label="Close dialog"></button><aside id="resDrawer" class="res-drawer" role="dialog" aria-modal="true" aria-hidden="true" aria-labelledby="resDrawerTitle"><div class="res-drawer-head"><div><strong id="resDrawerTitle"></strong><small data-res-drawer-copy></small></div><button class="wcp-close-btn" type="button" data-res-close aria-label="Close"><span class="material-symbols-rounded">close</span></button></div><div class="res-drawer-body"></div></aside>');
  }

  function openDrawer(kind, trigger) {
    const config = forms[kind] || forms['open-support'];
    const drawer = qs('#resDrawer');
    returnFocus = trigger || document.activeElement;
    qs('#resDrawerTitle', drawer).textContent = config.title;
    qs('[data-res-drawer-copy]', drawer).textContent = config.copy;
    qs('.res-drawer-body', drawer).innerHTML = '<div class="res-success" role="status">Saved. The delegated operation was added to audit.</div><form class="res-form" data-res-form="' + kind + '">' + config.body() + '<div class="res-form-actions"><button class="wcp-action-btn" type="button" data-res-close>Cancel</button><button class="wcp-action-btn primary" type="submit">' + config.submit + '</button></div></form>';
    drawer.classList.add('open');
    drawer.setAttribute('aria-hidden', 'false');
    qs('.res-drawer-backdrop')?.classList.add('open');
    setTimeout(() => qs('input,select,textarea,button', drawer)?.focus(), 20);
  }

  function closeDrawer() {
    qs('#resDrawer')?.classList.remove('open');
    qs('#resDrawer')?.setAttribute('aria-hidden', 'true');
    qs('.res-drawer-backdrop')?.classList.remove('open');
    returnFocus?.focus?.();
  }

  function filterCustomers() {
    const input = qs('[data-res-customer-search]');
    if (!input) return;
    const query = input.value.trim().toLowerCase();
    const status = qs('[data-res-customer-status]')?.value || 'all';
    let visible = 0;
    qsa('[data-res-customer]').forEach(row => {
      const match = (!query || row.dataset.resSearch.includes(query)) && (status === 'all' || row.dataset.resStatus === status);
      row.hidden = !match;
      if (match) visible += 1;
    });
    const empty = qs('[data-res-empty]');
    if (empty) empty.style.display = visible ? 'none' : 'block';
  }

  function notify(message) {
    document.dispatchEvent(new CustomEvent('wcp:notification', { detail: { title: 'Reseller Center', detail: message } }));
  }

  document.addEventListener('click', event => {
    const tab = event.target.closest('[data-res-tab]');
    if (tab) { event.preventDefault(); setTab(tab.dataset.resTab); return; }
    if (event.target.closest('[data-res-close]')) { event.preventDefault(); closeDrawer(); return; }
    const control = event.target.closest('[data-res-action]');
    if (!control) return;
    event.preventDefault();
    const kind = control.dataset.resAction;
    if (forms[kind]) { openDrawer(kind, control); return; }
    if (kind === 'manage-customer') {
      const customer = state.customers.find(item => item.id === control.dataset.resId);
      if (customer) {
        customer.status = customer.status === 'Suspended' ? 'Active' : 'Suspended';
        save();
        renderView();
        notify(customer.name + ' status changed to ' + customer.status + '.');
      }
      return;
    }
    notify(control.textContent.trim() + ' completed in prototype mode.');
  });
  document.addEventListener('input', event => { if (event.target.matches('[data-res-customer-search]')) filterCustomers(); });
  document.addEventListener('change', event => {
    if (event.target.matches('[data-res-customer-status]')) filterCustomers();
    const serviceControl = event.target.closest('[data-res-service]');
    if (serviceControl) {
      state.services[serviceControl.dataset.resService] = serviceControl.checked;
      save();
      notify('Delegated service catalog draft updated.');
    }
  });
  document.addEventListener('submit', event => {
    const form = event.target.closest('[data-res-form]');
    if (!form) return;
    event.preventDefault();
    if (!form.reportValidity()) return;
    const data = new FormData(form);
    if (form.dataset.resForm === 'create-customer') {
      state.customers.unshift({ id: 'customer-' + Date.now(), name: data.get('name'), domain: data.get('domain'), owner: data.get('owner'), package: data.get('package'), storage: 0, transfer: 0, status: 'Provisioning' });
      save();
      setTab('customers');
    }
    if (form.dataset.resForm === 'edit-brand') {
      state.brand = { name: data.get('brandName'), panel: data.get('panel'), support: data.get('support'), nameservers: data.get('nameservers') };
      save();
    }
    qs('.res-success', form.parentElement)?.classList.add('show');
    qsa('input,select,textarea,button', form).forEach(item => { item.disabled = true; });
    setTimeout(closeDrawer, 900);
  });
  document.addEventListener('keydown', event => { if (event.key === 'Escape' && qs('#resDrawer.open')) closeDrawer(); });
  window.addEventListener('hashchange', () => {
    const target = location.hash.slice(1);
    if (views[target]) setTab(target, false);
  });

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', renderShell);
  else renderShell();
})();
