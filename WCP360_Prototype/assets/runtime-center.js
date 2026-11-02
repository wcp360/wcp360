(function () {
  if (document.body.dataset.runtimeCenter !== 'root') return;

  const qs = (selector, context = document) => context.querySelector(selector);
  const qsa = (selector, context = document) => [...context.querySelectorAll(selector)];
  const tabs = [
    ['overview', 'space_dashboard', 'Overview'],
    ['web', 'web', 'Web servers'],
    ['php', 'php', 'PHP & FPM'],
    ['languages', 'deployed_code', 'Node · Python · Ruby'],
    ['modules', 'extension', 'Modules'],
    ['profiles', 'layers', 'Stack profiles'],
    ['assignments', 'account_tree', 'Assignments'],
    ['lifecycle', 'update', 'Lifecycle'],
    ['history', 'history', 'History']
  ];
  const storeKey = 'wcp360-runtime-center-v1';
  const defaults = {
    versions: [
      { id: 'php-84', family: 'PHP', version: '8.4.13', channel: 'Stable', nodes: 14, assignments: 86, status: 'Recommended' },
      { id: 'php-83', family: 'PHP', version: '8.3.26', channel: 'Stable', nodes: 14, assignments: 191, status: 'Supported' },
      { id: 'php-82', family: 'PHP', version: '8.2.29', channel: 'Maintenance', nodes: 8, assignments: 62, status: 'Review' },
      { id: 'php-74', family: 'PHP', version: '7.4.33', channel: 'Legacy vault', nodes: 2, assignments: 3, status: 'Restricted' },
      { id: 'node-22', family: 'Node.js', version: '22.20 LTS', channel: 'LTS', nodes: 12, assignments: 34, status: 'Recommended' },
      { id: 'node-20', family: 'Node.js', version: '20.19 LTS', channel: 'Maintenance', nodes: 12, assignments: 18, status: 'Supported' },
      { id: 'python-313', family: 'Python', version: '3.13.7', channel: 'Stable', nodes: 10, assignments: 21, status: 'Recommended' },
      { id: 'python-312', family: 'Python', version: '3.12.11', channel: 'Security', nodes: 10, assignments: 12, status: 'Supported' },
      { id: 'ruby-33', family: 'Ruby', version: '3.3.9', channel: 'Stable', nodes: 6, assignments: 11, status: 'Recommended' }
    ],
    modules: { imagick: true, ioncube: true, redis: true, bcmath: true, opcache: true, grpc: false, sharp: true, psycopg: true, pillow: true, nokogiri: true },
    profiles: [
      { id: 'standard-php', name: 'Standard PHP', stack: 'Nginx → Apache → PHP 8.4 FPM', accounts: 184, revision: 'v24', state: 'Published' },
      { id: 'wordpress', name: 'WordPress Optimized', stack: 'Nginx cache → PHP 8.3 → Redis', accounts: 96, revision: 'v18', state: 'Published' },
      { id: 'node-api', name: 'Node API', stack: 'Nginx → Node 22 → systemd', accounts: 34, revision: 'v12', state: 'Published' },
      { id: 'python-asgi', name: 'Python ASGI', stack: 'Nginx → Uvicorn → Python 3.13', accounts: 21, revision: 'v9', state: 'Published' },
      { id: 'rails', name: 'Ruby Rails', stack: 'Nginx → Puma → Ruby 3.3', accounts: 11, revision: 'v7', state: 'Canary' }
    ]
  };
  const read = () => { try { return JSON.parse(localStorage.getItem(storeKey)) || {}; } catch (error) { return {}; } };
  const stored = read();
  const state = {
    ...defaults,
    ...stored,
    versions: Array.isArray(stored.versions) ? stored.versions : defaults.versions,
    profiles: Array.isArray(stored.profiles) ? stored.profiles : defaults.profiles,
    modules: { ...defaults.modules, ...(stored.modules || {}) }
  };
  const save = () => { try { localStorage.setItem(storeKey, JSON.stringify(state)); } catch (error) {} };
  let active = tabs.some(tab => tab[0] === location.hash.slice(1)) ? location.hash.slice(1) : 'overview';
  let returnFocus = null;
  const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, character => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[character]));
  const chip = (label, tone = '') => `<span class="wcp-status ${tone}">${escapeHtml(label)}</span>`;
  const action = (kind, label, primary = false, icon = '') => `<button type="button" class="wcp-action-btn ${primary ? 'primary' : ''}" data-rtc-action="${kind}">${icon ? `<span class="material-symbols-rounded">${icon}</span>` : ''}${label}</button>`;
  const panelHead = (title, copy, controls = '') => `<div class="wcp-panel-head rtc-panel-head"><div><div class="wcp-panel-title">${title}</div><div class="wcp-panel-sub">${copy}</div></div><div class="rtc-actions">${controls}</div></div>`;
  const note = (icon, title, copy) => `<div class="rtc-note"><span class="material-symbols-rounded">${icon}</span><div><strong>${title}</strong><p>${copy}</p></div></div>`;
  const kv = (items) => `<div class="rtc-kv">${items.map(item => `<div><span>${item[0]}</span><strong>${item[1]}</strong></div>`).join('')}</div>`;
  const config = (label, value, copy) => `<article class="rtc-config"><span>${label}</span><strong>${value}</strong><small>${copy}</small></article>`;
  const health = (icon, title, copy, status, tone = '') => `<div class="rtc-health-row"><span class="material-symbols-rounded">${icon}</span><div><strong>${title}</strong><small>${copy}</small></div>${chip(status, tone)}</div>`;
  const profileCard = (profile) => `<article class="rtc-card" data-rtc-profile="${profile.id}"><div class="rtc-card-head"><span class="rtc-card-icon"><span class="material-symbols-rounded">layers</span></span>${chip(profile.state, profile.state === 'Published' ? 'success' : 'warning')}</div><h4>${escapeHtml(profile.name)}</h4><p>${escapeHtml(profile.stack)}</p>${kv([['Revision', profile.revision], ['Assignments', profile.accounts + ' accounts'], ['Drift', profile.state === 'Canary' ? '2 canary nodes' : 'None']])}<footer><span class="rtc-pill">Signed manifest</span><button class="wcp-action-btn" data-rtc-action="assign" data-profile="${profile.id}">Assign</button></footer></article>`;

  const views = {
    overview: () => `<div class="rtc-section" data-rtc-view="overview">
      <div class="rtc-section-head"><div><h3>Fleet runtime topology</h3><p>One ROOT control plane for ingress, web engines, language runtimes, modules, profiles and tenant assignments. USER can choose only from versions published through its effective plan.</p></div><div class="rtc-actions">${action('validate', 'Validate fleet', false, 'fact_check')}${action('install', 'Install runtime', true, 'download')}</div></div>
      <section class="wcp-panel">${panelHead('Production request path', 'Authoritative topology applied by signed stack profiles')}<div class="rtc-stack"><div class="rtc-stack-step"><span class="material-symbols-rounded">public</span><strong>Edge / CDN</strong><small>Anycast · WAF</small></div><div class="rtc-stack-step"><span class="material-symbols-rounded">swap_horiz</span><strong>HAProxy</strong><small>TLS · routing</small></div><div class="rtc-stack-step"><span class="material-symbols-rounded">web</span><strong>Nginx</strong><small>HTTP/3 · cache</small></div><div class="rtc-stack-step"><span class="material-symbols-rounded">dns</span><strong>Apache</strong><small>Compatibility</small></div><div class="rtc-stack-step"><span class="material-symbols-rounded">code</span><strong>Runtime</strong><small>PHP · Node · Py · Ruby</small></div><div class="rtc-stack-step"><span class="material-symbols-rounded">memory</span><strong>Workers</strong><small>cgroups · pools</small></div><div class="rtc-stack-step"><span class="material-symbols-rounded">database</span><strong>Data / cache</strong><small>SQL · Redis</small></div></div></section>
      <div class="rtc-grid two"><section class="wcp-panel">${panelHead('Health and configuration drift', 'Continuous reconciliation across 24 infrastructure nodes')}<div class="rtc-health">${health('check_circle','Nginx configuration','14 / 14 nodes match profile web-standard v24','Compliant','success')}${health('warning','Apache compatibility tier','One local include expires in 6 days','1 drift','warning')}${health('deployed_code','Language runtime inventory','9 published versions · signatures valid','Healthy','success')}${health('extension','Module compatibility','Two extensions block PHP 8.4 migration','Review','warning')}</div></section><section class="wcp-panel">${panelHead('Change readiness', 'Preflight, canary, drain, health gates and rollback')}<div class="rtc-health">${health('verified','Artifact trust','Repository signature, checksum, SBOM and CVE gate','Enforced','success')}${health('science','Canary cohort','web-01 and web-02 receive changes first','2 nodes')}${health('monitor_heart','Acceptance gates','HTTP probe, error rate, latency and worker health','8 gates','success')}${health('undo','Rollback slot','Previous profile retained for 14 days','Ready','success')}</div></section></div>
      ${note('shield_lock', 'ROOT publishes; tenants consume', 'Only ROOT installs binaries, curates repositories, licenses modules and publishes stack profiles. RESELLER may narrow availability; USER selects an allowed profile or version for owned applications.')}
    </div>`,

    web: () => `<div class="rtc-section" data-rtc-view="web">
      <div class="rtc-section-head"><div><h3>Web engines, proxy modes and listeners</h3><p>Configure the request path without editing unmanaged files. Every revision is validated with native syntax checks before a graceful reload.</p></div><div class="rtc-actions">${action('validate-web', 'Validate config', false, 'fact_check')}${action('configure-web', 'Edit web policy', true, 'tune')}</div></div>
      <div class="rtc-grid"><article class="rtc-card"><div class="rtc-card-head"><span class="rtc-card-icon"><span class="material-symbols-rounded">web</span></span>${chip('Primary', 'success')}</div><h4>Nginx 1.28</h4><p>Public ingress, TLS termination, static assets, cache and reverse proxy.</p>${kv([['Nodes','14'],['Workers','auto · 16 max'],['Protocols','HTTP/1.1 · 2 · 3'],['Reload','Graceful']])}<footer><span class="rtc-pill">Profile managed</span>${action('reload-nginx','Graceful reload')}</footer></article><article class="rtc-card"><div class="rtc-card-head"><span class="rtc-card-icon"><span class="material-symbols-rounded">dns</span></span>${chip('Compatibility')}</div><h4>Apache 2.4</h4><p>Optional origin tier for .htaccess compatibility and legacy handlers.</p>${kv([['Nodes','8'],['MPM','event'],['Direct exposure','Disabled'],['Overrides','Restricted']])}<footer><span class="rtc-pill">Internal only</span>${action('configure-apache','Configure')}</footer></article><article class="rtc-card"><div class="rtc-card-head"><span class="rtc-card-icon"><span class="material-symbols-rounded">swap_horiz</span></span>${chip('HA', 'success')}</div><h4>HAProxy 3.0</h4><p>Edge load balancing, health checks, connection draining and traffic shifting.</p>${kv([['Nodes','2 active + 1 standby'],['Algorithm','leastconn'],['Drain timeout','45 seconds'],['PROXY protocol','v2']])}<footer><span class="rtc-pill">Keepalived VIP</span>${action('configure-proxy','Configure')}</footer></article></div>
      <section class="wcp-panel">${panelHead('Global web policy', 'Inherited by stack profiles unless a validated profile override exists')}<div class="rtc-config-grid">${config('Default topology','Nginx → Apache / runtime','Origin tier selected by profile')}${config('TLS baseline','TLS 1.2 + 1.3','Modern ciphers · OCSP stapling')}${config('Compression','Brotli + Gzip','Static and dynamic content rules')}${config('Request ceiling','128 MiB','Plan can only reduce this value')}${config('Header timeout','30 seconds','Slow-client protection')}${config('Keepalive','75 s · 1,000 req','Per-listener connection policy')}${config('Real client IP','Trusted proxy CIDRs','Reject spoofed forwarded headers')}${config('Config source','Signed schema v24','Raw fragments require expiry')}</div></section>
      ${note('rule', 'Atomic publication', 'Syntax check, dependency graph, port collision detection and canary health gates must pass before traffic is shifted. Failed revisions never become active.')}
    </div>`,

    php: () => `<div class="rtc-section" data-rtc-view="php">
      <div class="rtc-section-head"><div><h3>PHP versions, FPM pools and limits</h3><p>Install versions side by side, isolate each account pool and govern extensions, php.ini layers, OpCache, slow logs and retirement windows.</p></div><div class="rtc-actions">${action('change-default', 'Change default')}${action('install-php', 'Install PHP', true, 'download')}</div></div>
      <section class="wcp-panel">${panelHead('Published PHP inventory', 'Defaults affect new assignments only; existing applications remain pinned')}<div class="rtc-table-wrap"><table class="rtc-table"><thead><tr><th>Version</th><th>Channel</th><th>Nodes</th><th>Assignments</th><th>Handler</th><th>Support</th><th>Status</th><th></th></tr></thead><tbody>${state.versions.filter(item => item.family === 'PHP').map(item => `<tr data-runtime-version="${item.id}"><td><strong>PHP ${escapeHtml(item.version)}</strong><small>Signed WCP360 runtime bundle</small></td><td>${item.channel}</td><td>${item.nodes}</td><td>${item.assignments}</td><td>PHP-FPM</td><td>${item.status === 'Restricted' ? 'Exception only' : item.status === 'Review' ? 'Security fixes' : 'Vendor supported'}</td><td>${chip(item.status, ['Recommended','Supported'].includes(item.status) ? 'success' : 'warning')}</td><td><button class="wcp-action-btn" data-rtc-action="inspect-version">Inspect</button></td></tr>`).join('')}</tbody></table></div></section>
      <div class="rtc-grid two"><section class="wcp-panel">${panelHead('FPM pool baseline', '356 pools · per-account UID, socket and cgroup')}<div class="rtc-config-grid">${config('Process manager','dynamic','Adaptive capacity for shared hosting')}${config('Children','12 max','Plan and cgroup remain authoritative')}${config('Idle timeout','20 seconds','Recycle unused workers')}${config('Request recycle','500 requests','Limits long-lived leaks')}${config('Memory limit','512 MiB','Plan may reduce per account')}${config('Execution time','120 seconds','CLI jobs use separate policy')}${config('Slow log','5 seconds','Redacted and tenant-scoped')}${config('Emergency restart','10 / 60 s','Self-healing safety threshold')}</div></section><section class="wcp-panel">${panelHead('PHP policy layers', 'Clear provenance for every effective setting')}<div class="rtc-health">${health('foundation','Platform baseline','Secure compiled defaults and forbidden directives','Immutable','success')}${health('dns','Server profile','Node capacity, extension ABI and pool template','Managed','success')}${health('inventory_2','Plan / feature list','Allowed versions, extensions and maximum limits','Inherited')}${health('person','Account override','Only allowlisted php.ini directives','Bounded','warning')}</div></section></div>
    </div>`,

    languages: () => `<div class="rtc-section" data-rtc-view="languages">
      <div class="rtc-section-head"><div><h3>Node.js, Python and Ruby platform</h3><p>Curated interpreters, tenant-scoped dependency stores, virtual environments, supervisors, application sockets, build limits and safe lifecycle policy.</p></div>${action('install', 'Add runtime version', true, 'add')}</div>
      <div class="rtc-grid"><article class="rtc-card"><div class="rtc-card-head"><span class="rtc-card-icon"><span class="material-symbols-rounded">javascript</span></span>${chip('22 LTS default','success')}</div><h4>Node.js</h4><p>Node 22 and 20 LTS with Corepack and signed package mirror.</p><div class="rtc-pill-list"><span class="rtc-pill">npm</span><span class="rtc-pill">pnpm</span><span class="rtc-pill">Yarn</span><span class="rtc-pill">PM2</span><span class="rtc-pill">systemd</span></div>${kv([['Published versions','2'],['Applications','52'],['Package store','Per-account'],['Supervisor','systemd · PM2 adapter']])}<footer>${action('language-policy','Manage Node.js')}</footer></article><article class="rtc-card"><div class="rtc-card-head"><span class="rtc-card-icon"><span class="material-symbols-rounded">code</span></span>${chip('3.13 default','success')}</div><h4>Python</h4><p>Python 3.13 and 3.12 with isolated venvs and wheel cache.</p><div class="rtc-pill-list"><span class="rtc-pill">pip</span><span class="rtc-pill">Poetry</span><span class="rtc-pill">Gunicorn</span><span class="rtc-pill">Uvicorn</span><span class="rtc-pill">Celery</span></div>${kv([['Published versions','2'],['Applications','33'],['Environment','venv per app'],['App server','Gunicorn · Uvicorn']])}<footer>${action('language-policy','Manage Python')}</footer></article><article class="rtc-card"><div class="rtc-card-head"><span class="rtc-card-icon"><span class="material-symbols-rounded">diamond</span></span>${chip('3.3 default','success')}</div><h4>Ruby</h4><p>Ruby 3.3 with Bundler, per-application gems and Puma sockets.</p><div class="rtc-pill-list"><span class="rtc-pill">Bundler</span><span class="rtc-pill">Puma</span><span class="rtc-pill">Sidekiq</span><span class="rtc-pill">Rails</span></div>${kv([['Published versions','1'],['Applications','11'],['Gem home','Per-account'],['App server','Puma · systemd']])}<footer>${action('language-policy','Manage Ruby')}</footer></article></div>
      <section class="wcp-panel">${panelHead('Runtime versions and assignments', 'Side-by-side versions are published only after supply-chain and compatibility checks')}<div class="rtc-table-wrap"><table class="rtc-table"><thead><tr><th>Runtime</th><th>Version</th><th>Channel</th><th>Nodes</th><th>Applications</th><th>Isolation</th><th>Status</th></tr></thead><tbody>${state.versions.filter(item => item.family !== 'PHP').map(item => `<tr data-runtime-version="${item.id}"><td><strong>${item.family}</strong><small>Managed artifact</small></td><td>${item.version}</td><td>${item.channel}</td><td>${item.nodes}</td><td>${item.assignments}</td><td>Account + app</td><td>${chip(item.status,'success')}</td></tr>`).join('')}</tbody></table></div></section>
      ${note('security', 'Build and execution are separated', 'Package installation runs in restricted build workers. Production application processes run without compiler access, under tenant UID, cgroup, PID and network policy.')}
    </div>`,

    modules: () => `<div class="rtc-section" data-rtc-view="modules">
      <div class="rtc-section-head"><div><h3>Extensions and native module registry</h3><p>Curate PHP extensions and native dependencies for Python, Node.js and Ruby with ABI compatibility, licensing, CVE and provenance controls.</p></div>${action('add-module', 'Add signed module', true, 'add')}</div>
      <div class="rtc-toolbar"><label class="rtc-search"><span class="material-symbols-rounded">search</span><input type="search" data-rtc-module-search placeholder="Search module, language, ABI or source" aria-label="Search runtime modules"></label><select data-rtc-module-family aria-label="Filter modules by language"><option value="all">All languages</option><option>PHP</option><option>Node.js</option><option>Python</option><option>Ruby</option></select></div>
      <section class="wcp-panel">${panelHead('Managed module sets', 'Activation builds an immutable set, runs compatibility tests and reloads only healthy pools')}<div class="rtc-table-wrap"><table class="rtc-table"><thead><tr><th>Module</th><th>Language / ABI</th><th>Version</th><th>Source</th><th>License</th><th>Compatibility</th><th>Default</th></tr></thead><tbody>
        ${[['imagick','Imagick','PHP','3.8.0','Signed repository','Open source','PHP 8.2–8.4'],['ioncube','ionCube Loader','PHP','14.4','Commercial vault','Fleet entitlement','PHP 8.1–8.4'],['redis','Redis','PHP','6.2.0','Signed repository','Open source','PHP 8.2–8.4'],['bcmath','BCMath','PHP','8.4','Runtime bundle','Open source','PHP 8.4'],['opcache','OPcache','PHP','8.4','Runtime bundle','Open source','PHP 8.4'],['grpc','gRPC native','Node.js','1.13','Curated npm mirror','Apache-2.0','Node 20–22'],['sharp','Sharp','Node.js','0.34','Curated npm mirror','Apache-2.0','Node 20–22'],['psycopg','psycopg','Python','3.2','WCP wheelhouse','LGPL-3.0','CPython 3.12–3.13'],['pillow','Pillow','Python','11.3','WCP wheelhouse','HPND','CPython 3.12–3.13'],['nokogiri','Nokogiri','Ruby','1.18','Curated gem mirror','MIT','Ruby 3.3']].map(item => `<tr data-rtc-module="${item[0]}" data-search="${item.join(' ').toLowerCase()}" data-family="${item[2]}"><td><strong>${item[1]}</strong><small>SBOM and checksums retained</small></td><td>${item[2]}</td><td>${item[3]}</td><td>${item[4]}</td><td>${item[5]}</td><td>${chip(item[6], item[0] === 'grpc' ? 'warning' : 'success')}</td><td><label class="rtc-toggle" aria-label="Enable ${item[1]} by default"><input type="checkbox" data-rtc-module-toggle="${item[0]}" ${state.modules[item[0]] ? 'checked' : ''}><i></i></label></td></tr>`).join('')}
      </tbody></table></div></section><div class="rtc-empty" data-rtc-module-empty>No module matches these filters.</div>
      ${note('license', 'Commercial entitlements fail closed', 'A licensed extension can be activated only on entitled nodes. Expiry warnings, allocation changes and reconciliation evidence are visible before a rollout.')}
    </div>`,

    profiles: () => `<div class="rtc-section" data-rtc-view="profiles">
      <div class="rtc-section-head"><div><h3>Versioned stack profiles</h3><p>Compose the web engine, runtime, module set, process model, limits and observability policy once, then publish a signed revision to eligible servers and plans.</p></div><div class="rtc-actions">${action('compare-profiles','Compare')}${action('create-profile','Build profile',true,'add_box')}</div></div>
      <div class="rtc-grid">${state.profiles.map(profileCard).join('')}</div>
      <section class="wcp-panel">${panelHead('Profile publication pipeline', 'Draft → validate → build → canary → staged rollout → published')}<div class="rtc-config-grid">${config('1 · Compose','Typed manifest','No unmanaged server directives')}${config('2 · Resolve','Packages + modules','Dependency and license graph')}${config('3 · Build','Immutable artifact','Signature, SBOM and scan')}${config('4 · Test','Ephemeral node','Syntax and application probes')}${config('5 · Canary','2 production nodes','Traffic-shift health gates')}${config('6 · Rollout','25% batches','Drain and automatic pause')}${config('7 · Reconcile','Continuous','Drift is detected and explained')}${config('8 · Retain','14 days','One-click signed rollback')}</div></section>
    </div>`,

    assignments: () => `<div class="rtc-section" data-rtc-view="assignments">
      <div class="rtc-section-head"><div><h3>Account, application and fleet assignments</h3><p>Trace every effective stack from ROOT profile through plan allowance to account and application pin, including placement and migration readiness.</p></div><div class="rtc-actions">${action('plan-rollout','Plan rollout')}${action('assign','Assign profile',true,'account_tree')}</div></div>
      <section class="wcp-panel">${panelHead('Effective runtime assignments', 'Changing the platform default never upgrades an existing application silently')}<div class="rtc-table-wrap"><table class="rtc-table"><thead><tr><th>Account / application</th><th>Profile</th><th>Runtime</th><th>Process / pool</th><th>Node</th><th>Source</th><th>State</th><th></th></tr></thead><tbody>
        <tr data-rtc-assignment><td><strong>example.com</strong><small>WordPress production</small></td><td>WordPress Optimized v18</td><td>PHP 8.3</td><td>Dedicated FPM pool</td><td>web-07</td><td>Plan + app pin</td><td>${chip('Healthy','success')}</td><td><button class="wcp-action-btn" data-rtc-action="assign">Change</button></td></tr>
        <tr data-rtc-assignment><td><strong>api.example.com</strong><small>Node API</small></td><td>Node API v12</td><td>Node.js 22 LTS</td><td>systemd socket</td><td>web-11</td><td>Application pin</td><td>${chip('Healthy','success')}</td><td><button class="wcp-action-btn" data-rtc-action="assign">Change</button></td></tr>
        <tr data-rtc-assignment><td><strong>portal.example.com</strong><small>Django ASGI</small></td><td>Python ASGI v9</td><td>Python 3.13</td><td>Uvicorn · 4 workers</td><td>web-09</td><td>Plan default</td><td>${chip('Healthy','success')}</td><td><button class="wcp-action-btn" data-rtc-action="assign">Change</button></td></tr>
        <tr data-rtc-assignment><td><strong>legacy.example.com</strong><small>Legacy PHP application</small></td><td>Compatibility v5</td><td>PHP 7.4 vault</td><td>Isolated FPM pool</td><td>legacy-02</td><td>ROOT exception</td><td>${chip('Migration due','warning')}</td><td><button class="wcp-action-btn" data-rtc-action="assign">Migrate</button></td></tr>
        <tr data-rtc-assignment><td><strong>shop.example.com</strong><small>PrestaShop</small></td><td>Standard PHP v24</td><td>PHP 8.4</td><td>Dedicated FPM pool</td><td>web-03</td><td>Plan default</td><td>${chip('Canary','warning')}</td><td><button class="wcp-action-btn" data-rtc-action="assign">Inspect</button></td></tr>
      </tbody></table></div></section>
      <div class="rtc-grid two"><section class="wcp-panel">${panelHead('Placement guardrails', 'Assignment is rejected if any hard requirement fails')}<div class="rtc-health">${health('dns','Server role','Node has compatible web and runtime roles','Pass','success')}${health('hard_drive','Capacity','CPU, RAM, I/O, PID and storage headroom','Pass','success')}${health('license','Entitlements','All commercial modules licensed on target','Pass','success')}${health('shield','Isolation','Tenant UID, sockets and cgroup available','Pass','success')}</div></section><section class="wcp-panel">${panelHead('Rollout strategy', 'Migrations remain reversible during the acceptance window')}<div class="rtc-health">${health('science','Canary','5 accounts · internal and low-risk','Required')}${health('waves','Batches','10% → 25% → 50% → remaining','Staged')}${health('monitor_heart','Health gates','Errors, p95 latency, worker saturation','Automatic','success')}${health('undo','Rollback','Previous runtime and socket mapping retained','14 days','success')}</div></section></div>
    </div>`,

    lifecycle: () => `<div class="rtc-section" data-rtc-view="lifecycle">
      <div class="rtc-section-head"><div><h3>Support lifecycle, CVEs and upgrade policy</h3><p>Track vendor support, WCP360 maintenance, compatibility blockers, vulnerability exposure and explicit retirement stages across all runtime families.</p></div>${action('schedule-upgrade','Schedule upgrade',true,'calendar_month')}</div>
      <div class="rtc-grid"><article class="rtc-card"><div class="rtc-card-head"><span class="rtc-card-icon"><span class="material-symbols-rounded">security_update_good</span></span>${chip('Current','success')}</div><h4>PHP 8.4</h4><p>Active vendor support with WCP360 stable channel.</p>${kv([['Security advisories','0 exposed'],['Upgrade readiness','14 / 14 nodes'],['Applications ready','342'],['Next review','30 days']])}</article><article class="rtc-card"><div class="rtc-card-head"><span class="rtc-card-icon"><span class="material-symbols-rounded">update</span></span>${chip('Maintenance','warning')}</div><h4>Node.js 20 LTS</h4><p>Maintenance channel; migration to Node.js 22 is available.</p>${kv([['Security advisories','1 mitigated'],['Upgrade readiness','46 / 52 apps'],['Compatibility blockers','2 native modules'],['Retirement','2027-04-30']])}</article><article class="rtc-card"><div class="rtc-card-head"><span class="rtc-card-icon"><span class="material-symbols-rounded">warning</span></span>${chip('Exception only','warning')}</div><h4>PHP 7.4 vault</h4><p>Isolated legacy artifact with compensating controls.</p>${kv([['New assignments','Blocked'],['Existing accounts','3'],['Outbound network','Restricted'],['Exception expires','2026-12-15']])}</article></div>
      <section class="wcp-panel">${panelHead('Retirement policy', 'Warn → block new assignments → assisted migration → quarantine → remove')}<div class="rtc-timeline"><div class="rtc-timeline-item"><time>2026-11-15</time><span class="rtc-dot"></span><div><strong>PHP 8.2 migration wave opens</strong><small>Compatibility report and canary selection published to account owners.</small></div>${chip('Planned')}</div><div class="rtc-timeline-item"><time>2026-12-15</time><span class="rtc-dot"></span><div><strong>PHP 7.4 exceptions expire</strong><small>Remaining workloads move to quarantine profile unless an approved extension exists.</small></div>${chip('Action','warning')}</div><div class="rtc-timeline-item"><time>2027-01-10</time><span class="rtc-dot"></span><div><strong>Python 3.12 default changes</strong><small>Python 3.13 becomes default for new applications; existing venvs remain pinned.</small></div>${chip('Safe','success')}</div><div class="rtc-timeline-item"><time>2027-04-30</time><span class="rtc-dot"></span><div><strong>Node.js 20 LTS retirement</strong><small>New assignments already blocked 60 days before the retirement date.</small></div>${chip('Tracked')}</div></div></section>
      ${note('crisis_alert', 'Vulnerability response is risk based', 'A critical remotely exploitable CVE may override a maintenance window, but still uses signed artifacts, canary deployment, health gates and automatic rollback.')}
    </div>`,

    history: () => `<div class="rtc-section" data-rtc-view="history">
      <div class="rtc-section-head"><div><h3>Configuration history and recovery</h3><p>Immutable evidence for runtime installation, profile publication, assignment, module changes, reloads and rollbacks with actor and correlation identifiers.</p></div>${action('export-history','Export evidence',false,'download')}</div>
      <section class="wcp-panel">${panelHead('Recent runtime changes', 'Every operation links configuration diff, target inventory, checks and recovery slot')}<div class="rtc-timeline"><div class="rtc-timeline-item"><time>12:42 UTC</time><span class="rtc-dot"></span><div><strong>Stack profile v24 published</strong><small>Yann · ROOT · 14 web nodes · canary and 8 health gates passed · CHG-49281</small></div>${chip('Completed','success')}</div><div class="rtc-timeline-item"><time>11:18 UTC</time><span class="rtc-dot"></span><div><strong>Imagick 3.8 activated</strong><small>Module set ms-128 · PHP 8.3 / 8.4 · 277 affected pools · CHG-49197</small></div>${chip('Completed','success')}</div><div class="rtc-timeline-item"><time>Yesterday</time><span class="rtc-dot"></span><div><strong>Apache local override detected</strong><small>web-06 · owned exception has 6 days remaining · DRIFT-188</small></div>${chip('Review','warning')}</div><div class="rtc-timeline-item"><time>Oct 27</time><span class="rtc-dot"></span><div><strong>Node.js 22.20 installed</strong><small>12 nodes · signed artifact · SBOM retained · JOB-88204</small></div>${chip('Completed','success')}</div><div class="rtc-timeline-item"><time>Oct 25</time><span class="rtc-dot"></span><div><strong>PHP 8.4 rollout automatically paused</strong><small>Error-rate gate failed on web-02; previous slot restored in 18 seconds · JOB-87913</small></div>${chip('Rolled back','warning')}</div></div></section>
      <div class="rtc-grid two"><section class="wcp-panel">${panelHead('Recovery inventory', 'Signed artifacts and mappings retained by policy')}<div class="rtc-health">${health('layers','Stack profiles','12 published · 18 previous revisions','Ready','success')}${health('deployed_code','Runtime slots','9 current · 7 rollback slots','Ready','success')}${health('extension','Module sets','16 retained sets','Ready','success')}${health('account_tree','Assignment maps','Daily snapshot + every change','Ready','success')}</div></section><section class="wcp-panel">${panelHead('Evidence integrity', 'Audit records are externalized to immutable storage')}<div class="rtc-health">${health('verified','Artifact signatures','100% verified','Valid','success')}${health('fingerprint','Audit chain','Latest hash anchored 3 min ago','Intact','success')}${health('inventory','SBOM retention','365 days online · 7 years archive','Policy')}${health('lock','WORM destination','EU compliance vault','Connected','success')}</div></section></div>
    </div>`
  };

  const host = qs('#runtimeCenter');
  host.innerHTML = `<div class="rtc-root">
    <section class="rtc-hero"><div><div class="rtc-eyebrow">WCP360 / ROOT / Web &amp; Data</div><h2>Runtime &amp; Web Stack Center</h2><p>Authoritative control of web servers, PHP-FPM, Node.js, Python, Ruby, native modules, versioned stack profiles and fleet-wide assignments.</p></div><div class="rtc-hero-actions">${action('create-profile','Build profile')}${action('install','Install runtime',true,'download')}</div></section>
    <section class="rtc-metrics"><article class="rtc-metric"><span>Web fleet</span><strong>23 / 24</strong><small>One approved configuration drift</small></article><article class="rtc-metric"><span>Published runtimes</span><strong>9 versions</strong><small>PHP · Node.js · Python · Ruby</small></article><article class="rtc-metric"><span>Application assignments</span><strong>342</strong><small>356 isolated process pools</small></article><article class="rtc-metric"><span>Security posture</span><strong>97.8%</strong><small>Two compatibility blockers</small></article></section>
    <section class="rtc-boundary"><div><span>ROOT authority</span><strong>Install, license, profile and publish</strong></div><span class="material-symbols-rounded">arrow_forward</span><div><span>RESELLER delegation</span><strong>Narrow versions and profile availability</strong></div><span class="material-symbols-rounded">arrow_forward</span><div><span>USER consumption</span><strong>Select allowed stack for owned applications</strong></div></section>
    <div class="rtc-tabs" role="tablist" aria-label="Runtime and web stack views">${tabs.map(tab => `<button type="button" class="rtc-tab" role="tab" data-rtc-tab="${tab[0]}" aria-selected="false"><span class="material-symbols-rounded">${tab[1]}</span>${tab[2]}</button>`).join('')}</div>
    <div class="rtc-view" tabindex="-1"></div>
  </div>
  <button type="button" class="rtc-drawer-backdrop" data-rtc-close aria-label="Close runtime form"></button>
  <aside class="rtc-drawer" id="rtcDrawer" role="dialog" aria-modal="true" aria-hidden="true" aria-labelledby="rtcDrawerTitle"><div class="rtc-drawer-head"><div><strong id="rtcDrawerTitle">Runtime operation</strong><small data-rtc-drawer-sub>Validated ROOT workflow</small></div><button type="button" class="wcp-icon-btn" data-rtc-close aria-label="Close"><span class="material-symbols-rounded">close</span></button></div><div class="rtc-drawer-body"></div></aside>`;
  const workflowOverlay=WCPUI.Overlay.attach({panel:qs('#rtcDrawer'),backdrop:qs('.rtc-drawer-backdrop'),source:'runtime-center'});

  const renderView = (focus = false) => {
    qsa('[data-rtc-tab]').forEach(button => {
      const selected = button.dataset.rtcTab === active;
      button.classList.toggle('active', selected);
      button.setAttribute('aria-selected', String(selected));
    });
    qs('.rtc-view').innerHTML = views[active]();
    if (focus) qs('.rtc-view').focus();
    bindView();
  };
  const setTab = (name, focus = true) => {
    if (!views[name]) return;
    active = name;
    history.replaceState(null, '', `#${name}`);
    renderView(focus);
  };
  const formShell = (kind, body, submit = 'Create job') => `<form class="rtc-form" data-rtc-form="${kind}">${body}<div class="rtc-feedback" role="status">Validated. The operation was added to Job Center.</div><div class="rtc-form-actions"><button type="button" class="wcp-action-btn" data-rtc-close>Cancel</button><button type="submit" class="wcp-action-btn primary">${submit}</button></div></form>`;
  const field = (label, control) => `<label class="rtc-field"><span>${label}</span>${control}</label>`;
  const openDrawer = (kind, trigger) => {
    returnFocus = trigger || document.activeElement;
    const drawer = qs('#rtcDrawer');
    const body = qs('.rtc-drawer-body', drawer);
    const title = qs('#rtcDrawerTitle', drawer);
    const subtitle = qs('[data-rtc-drawer-sub]', drawer);
    let content = '';
    if (['install','install-php'].includes(kind)) {
      title.textContent = 'Install runtime version';
      subtitle.textContent = 'Trusted artifact, fleet target and staged publication';
      content = formShell('install-runtime',
        field('Runtime family', '<select name="family"><option>PHP</option><option>Node.js</option><option>Python</option><option>Ruby</option></select>') +
        '<div class="rtc-form-grid">' + field('Version', '<input name="version" required value="8.4.14" autocomplete="off">') + field('Release channel', '<select name="channel"><option>Stable</option><option>LTS</option><option>Maintenance</option><option>Preview</option></select>') + '</div>' +
        field('Target cohort', '<select name="target"><option>Canary web nodes (2)</option><option>All compatible web nodes</option><option>Build repository only</option></select>') +
        field('Artifact source', '<select name="source"><option>WCP360 signed repository</option><option>Approved vendor repository</option><option>Uploaded signed artifact</option></select>') +
        '<label class="rtc-confirm"><input type="checkbox" required><span><strong>Require signature, checksum, SBOM and vulnerability gate</strong><br>The artifact remains unpublished if a supply-chain check fails.</span></label>', 'Validate & stage');
    } else if (kind === 'create-profile') {
      title.textContent = 'Build stack profile';
      subtitle.textContent = 'Versioned, signed and reusable configuration';
      content = formShell('create-profile',
        field('Profile name', '<input name="name" required value="Modern Web Stack" autocomplete="off">') +
        '<div class="rtc-form-grid">' + field('Web topology', '<select name="web"><option>Nginx only</option><option>Nginx → Apache</option><option>HAProxy → Nginx</option></select>') + field('Runtime', '<select name="runtime"><option>PHP 8.4</option><option>Node.js 22 LTS</option><option>Python 3.13</option><option>Ruby 3.3</option></select>') + '</div>' +
        field('Module set', '<select name="modules"><option>WCP360 standard</option><option>WordPress optimized</option><option>Minimal hardened</option><option>Custom signed set</option></select>') +
        field('Publication strategy', '<select name="strategy"><option>Draft only</option><option>Validate and canary</option><option>Canary then staged rollout</option></select>'), 'Build profile');
    } else if (kind === 'assign') {
      title.textContent = 'Assign stack profile';
      subtitle.textContent = 'Account or application assignment with compatibility preview';
      content = formShell('assign-profile',
        field('Target account or application', '<input name="target" required value="example.com" autocomplete="off">') +
        field('Published profile', `<select name="profile">${state.profiles.map(profile => `<option>${escapeHtml(profile.name)} · ${profile.revision}</option>`).join('')}</select>`) +
        '<div class="rtc-form-grid">' + field('Rollout', '<select name="rollout"><option>Canary then switch</option><option>Maintenance window</option><option>Next deployment</option></select>') + field('Recovery window', '<select name="recovery"><option>14 days</option><option>30 days</option><option>7 days</option></select>') + '</div>' +
        '<label class="rtc-confirm"><input type="checkbox" required><span><strong>Keep the current runtime slot and assignment map</strong><br>Automatic rollback remains possible during the acceptance window.</span></label>', 'Validate assignment');
    } else {
      title.textContent = 'Edit global web policy';
      subtitle.textContent = 'Versioned listeners, protocol and request policy';
      content = formShell('configure-web',
        '<div class="rtc-form-grid">' + field('Default topology', '<select name="topology"><option>Nginx → Apache / runtime</option><option>Nginx only</option><option>HAProxy → Nginx</option></select>') + field('Protocol baseline', '<select name="protocol"><option>HTTP/2 + HTTP/3</option><option>HTTP/2</option><option>HTTP/1.1 compatibility</option></select>') + '</div>' +
        '<div class="rtc-form-grid">' + field('Request body ceiling', '<input name="body" value="128 MiB">') + field('Header timeout', '<input name="timeout" value="30 seconds">') + '</div>' +
        field('Deployment', '<select name="deployment"><option>Validate and save draft</option><option>Canary then staged rollout</option><option>Next maintenance window</option></select>') +
        '<label class="rtc-confirm"><input type="checkbox" required><span><strong>Run native syntax checks and health probes</strong><br>Any failed gate prevents publication and restores the previous revision.</span></label>', 'Validate policy');
    }
    body.innerHTML = content;
    workflowOverlay.open(returnFocus);
  };
  const closeDrawer = () => {
    workflowOverlay.close();
  };
  const emit = (name, kind, risk = 'medium') => {
    document.dispatchEvent(new CustomEvent('wcp:operation', { detail: { name, kind, risk, scope: 'root', actor: 'Yann · ROOT' } }));
  };
  const filterModules = () => {
    const text = (qs('[data-rtc-module-search]')?.value || '').trim().toLowerCase();
    const family = qs('[data-rtc-module-family]')?.value || 'all';
    let visible = 0;
    qsa('[data-rtc-module]').forEach(row => {
      const show = (!text || row.dataset.search.includes(text)) && (family === 'all' || row.dataset.family === family);
      row.hidden = !show;
      if (show) visible += 1;
    });
    const empty = qs('[data-rtc-module-empty]');
    if (empty) empty.style.display = visible ? 'none' : 'block';
  };
  const bindView = () => {
    qs('[data-rtc-module-search]')?.addEventListener('input', filterModules);
    qs('[data-rtc-module-family]')?.addEventListener('change', filterModules);
  };

  qs('.rtc-tabs').addEventListener('click', event => {
    const tab = event.target.closest('[data-rtc-tab]');
    if (tab) setTab(tab.dataset.rtcTab);
  });
  host.addEventListener('click', event => {
    const trigger = event.target.closest('[data-rtc-action]');
    if (!trigger) return;
    const kind = trigger.dataset.rtcAction;
    if (['install','install-php','create-profile','assign','configure-web','configure-apache','configure-proxy','language-policy','change-default','add-module','schedule-upgrade'].includes(kind)) {
      openDrawer(['configure-apache','configure-proxy','language-policy','change-default','add-module','schedule-upgrade'].includes(kind) ? 'configure-web' : kind, trigger);
      return;
    }
    const operations = {
      validate: ['Validate runtime fleet','runtime-fleet-validation','low'],
      'validate-web': ['Validate web configuration','web-config-validation','low'],
      'reload-nginx': ['Graceful Nginx fleet reload','nginx-graceful-reload','medium'],
      'plan-rollout': ['Plan runtime assignment rollout','runtime-rollout-plan','low'],
      'compare-profiles': ['Compare stack profile revisions','stack-profile-compare','low'],
      'export-history': ['Export runtime audit evidence','runtime-evidence-export','low'],
      'inspect-version': ['Open runtime version inventory','runtime-inspection','low']
    };
    if (operations[kind]) emit(...operations[kind]);
  });
  host.addEventListener('change', event => {
    const toggle = event.target.closest('[data-rtc-module-toggle]');
    if (!toggle) return;
    state.modules[toggle.dataset.rtcModuleToggle] = toggle.checked;
    save();
    emit(`${toggle.checked ? 'Enable' : 'Disable'} runtime module ${toggle.dataset.rtcModuleToggle}`, 'runtime-module-policy', 'medium');
  });
  document.addEventListener('click', event => {
    if (event.target.closest('[data-rtc-close]')) closeDrawer();
  });
  document.addEventListener('submit', event => {
    const form = event.target.closest('[data-rtc-form]');
    if (!form) return;
    event.preventDefault();
    if (!form.reportValidity()) return;
    if (form.dataset.rtcForm === 'install-runtime') {
      const family = form.elements.family.value;
      const version = form.elements.version.value.trim();
      state.versions.push({ id: `${family.toLowerCase().replace(/\W+/g,'-')}-${Date.now()}`, family, version, channel: form.elements.channel.value, nodes: 2, assignments: 0, status: 'Staged' });
      save();
      emit(`Stage ${family} ${version} runtime`, 'runtime-install', 'high');
    } else if (form.dataset.rtcForm === 'create-profile') {
      const name = form.elements.name.value.trim();
      state.profiles.push({ id: `profile-${Date.now()}`, name, stack: `${form.elements.web.value} → ${form.elements.runtime.value}`, accounts: 0, revision: 'v1', state: 'Draft' });
      save();
      emit(`Build stack profile ${name}`, 'stack-profile-build', 'medium');
    } else if (form.dataset.rtcForm === 'assign-profile') {
      emit(`Assign runtime profile to ${form.elements.target.value}`, 'runtime-assignment', 'high');
    } else {
      emit('Publish web runtime policy revision', 'web-runtime-policy', 'high');
    }
    qs('.rtc-feedback', form)?.classList.add('show');
    setTimeout(() => {
      closeDrawer();
      renderView();
    }, 450);
  });
  window.addEventListener('hashchange', () => {
    const name = location.hash.slice(1);
    if (views[name]) { active = name; renderView(); }
  });
  renderView();
  save();
})();
