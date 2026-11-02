import {fileURLToPath} from 'node:url';
import { spawn } from 'node:child_process';
import { createServer } from 'node:http';
import { readFileSync, readdirSync, statSync } from 'node:fs';
import { extname, join, normalize, resolve } from 'node:path';
import { tmpdir } from 'node:os';

const root = resolve(process.argv[2] || fileURLToPath(new URL('../',import.meta.url)));
const pages = readdirSync(root).filter((name) => name.endsWith('.html')).sort();
const integrationOnly = process.env.WCP_AUDIT_INTEGRATION_ONLY === '1';
const host = '127.0.0.1';
const sitePort = 8766;
const debugPort = 9231;
const chromePath = process.env.CHROME_PATH || 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const mime = { '.html': 'text/html; charset=utf-8', '.css': 'text/css; charset=utf-8', '.js': 'text/javascript; charset=utf-8', '.ico': 'image/x-icon' };

const server = createServer((request, response) => {
  const urlPath = decodeURIComponent(new URL(request.url, `http://${host}`).pathname);
  const relative = urlPath === '/' ? 'index.html' : urlPath.slice(1);
  const file = normalize(join(root, relative));
  if (!file.startsWith(root) || !statSafe(file)) { response.writeHead(404).end('Not found'); return; }
  response.writeHead(200, { 'Content-Type': mime[extname(file)] || 'application/octet-stream' });
  response.end(readFileSync(file));
});

function statSafe(file) { try { return statSync(file).isFile(); } catch { return false; } }
const delay = (ms) => new Promise((done) => setTimeout(done, ms));

async function waitForChrome() {
  for (let i = 0; i < 60; i += 1) {
    if (chrome.exitCode !== null) throw new Error('Isolated Chrome exited before the browser audit started: '+chrome.exitCode);
    try { const response = await fetch(`http://${host}:${debugPort}/json/version`, {signal:AbortSignal.timeout(1000)}); if (response.ok) return; } catch {}
    await delay(100);
  }
  throw new Error('Chrome DevTools endpoint did not start');
}

class Cdp {
  constructor(url) { this.id = 0; this.pending = new Map(); this.listeners = new Map(); this.ready = new Promise((resolveReady, reject) => { this.ws = new WebSocket(url); this.ws.onopen = resolveReady; this.ws.onerror = reject; this.ws.onmessage = (event) => this.receive(JSON.parse(event.data)); }); }
  receive(message) {
    if (message.id) { const pending = this.pending.get(message.id); if (!pending) return; this.pending.delete(message.id); message.error ? pending.reject(new Error(message.error.message)) : pending.resolve(message.result); return; }
    for (const listener of this.listeners.get(message.method) || []) listener(message.params);
  }
  async send(method, params = {}) { await this.ready; const id = ++this.id; return new Promise((resolveCall, reject) => { this.pending.set(id, { resolve: resolveCall, reject }); this.ws.send(JSON.stringify({ id, method, params })); }); }
  on(method, listener) { const list = this.listeners.get(method) || []; list.push(listener); this.listeners.set(method, list); return () => this.listeners.set(method, list.filter((item) => item !== listener)); }
  close() { this.ws.close(); }
}

async function navigate(cdp, url) {
  let off, timer;
  const loaded = new Promise(resolveLoaded => { off = cdp.on('Page.loadEventFired', resolveLoaded); });
  try {
    await cdp.send('Page.navigate', { url });
    await Promise.race([loaded,new Promise((_,reject) => { timer=setTimeout(() => reject(new Error('Page load timed out: '+url)),20000); })]);
    await delay(80);
  } finally { clearTimeout(timer);off?.(); }
}

async function inspect(cdp) {
  const expression = `(() => {
    const visible = (el) => !!(el.offsetWidth || el.offsetHeight || el.getClientRects().length);
    const unnamed = [...document.querySelectorAll('input,select,textarea,button')].filter(el => visible(el) && !el.getAttribute('aria-label') && !el.getAttribute('aria-labelledby') && !(el.id && document.querySelector('label[for="'+CSS.escape(el.id)+'"]')) && !el.closest('label') && !el.textContent.trim()).length;
    const sidebar = document.querySelector('.wcp-dashboard-sidebar,.sidebar');
    const rootControl = document.querySelector('.root-control');
    const createdJob = window.WCPRuntime?.getState().jobs[0];
    const jobDetail = Boolean(createdJob?.correlation && createdJob?.reversible);
    if (createdJob?.id) window.WCPRuntime.cancelJob(createdJob.id);
    const cancelled = window.WCPRuntime?.getState().jobs.find(job => job.id === createdJob?.id)?.status === 'cancelled';
    return {
      overflow: Math.max(0, document.documentElement.scrollWidth - innerWidth),
      rootOverflow: rootControl ? Math.max(0, rootControl.scrollWidth - rootControl.clientWidth) : 0,
      unnamed,
      hashLinks: document.querySelectorAll('a[href="#"]').length,
      duplicateLegacyCommand: !!(document.querySelector('#commandPalette') && document.querySelector('#wcpCommandBackdrop')),
      activeNav: sidebar ? sidebar.querySelectorAll('.wcp-nav-item.active[aria-current="page"],.nav.active').length : 0,
      scope: document.body.dataset.accessScope || null,
      runtimeReady: !document.body.dataset.accessScope || !['user','reseller','root'].includes(document.body.dataset.accessScope) || !!window.WCPRuntime,
      experienceReady: !document.body.dataset.accessScope || !['user','reseller','root'].includes(document.body.dataset.accessScope) || !!window.WCPExperience,
      contextBars: document.querySelectorAll('.wcp-experience-bar').length,
      commandResults: document.querySelectorAll('#wcpCommandBackdrop .wcp-command-result').length,
      commandLinks: [...document.querySelectorAll('#wcpCommandBackdrop a[href]')].map(a => (a.getAttribute('href') || '').toLowerCase()),
      sidebarLinks: sidebar ? [...sidebar.querySelectorAll('a[href]')].map(a => (a.getAttribute('href') || '').toLowerCase()) : [],
      sidebarHeadings: sidebar ? [...sidebar.querySelectorAll('.wcp-nav-heading,.group-title')].map(el => el.textContent.trim()) : [],
      sidebarGroups: sidebar ? sidebar.querySelectorAll('[data-nav-group]').length : 0,
      expandedSidebarGroups: sidebar ? sidebar.querySelectorAll('[data-nav-group].open [data-nav-group-toggle][aria-expanded="true"]').length : 0,
      sidebarSearch: sidebar ? Boolean(sidebar.querySelector('[data-root-nav-search]')) : false,
      scopeBadges: sidebar ? [...sidebar.querySelectorAll('.wcp-scope-badge')].map(el => el.textContent.trim()) : [],
      scopeSwitches: sidebar ? sidebar.querySelectorAll('[data-scope-link],.wcp-scope-switch').length : 0,
      profilePortalSwitches: document.querySelectorAll('#wcpProfileMenu [data-portal-switch]').length,
      profilePortalCurrent: document.querySelector('#wcpProfileMenu [data-portal-switch].active')?.dataset.portalSwitch || null,
      sidebarPortalSwitches: sidebar ? sidebar.querySelectorAll('[data-portal-switch]').length : 0,
      prototypePortalLabel: document.querySelector('#wcpProfileMenu .wcp-portal-switch')?.textContent.includes('Prototype only') || false,
      rootChromeLeaks: document.body.dataset.accessScope === 'root' ? ['Create application','Create database','Current application context','My Workspace'].filter(term => [...document.querySelectorAll('#wcpCreateDrawer,#wcpCommandBackdrop,#wcpProfileMenu')].some(el => el.textContent.includes(term))) : [],
      tinyText: [...document.querySelectorAll('.wcp-dashboard-content *')].filter(el => visible(el) && !el.children.length && el.textContent.trim() && !el.classList.contains('material-symbols-rounded') && !['SCRIPT','STYLE'].includes(el.tagName) && parseFloat(getComputedStyle(el).fontSize) < 9.5).slice(0,20).map(el => ({ tag: el.tagName, className: String(el.className).slice(0,80), size: getComputedStyle(el).fontSize, text: el.textContent.trim().slice(0,50) }))
    };
  })()`;
  const result = await cdp.send('Runtime.evaluate', { expression, returnByValue: true });
  return result.result.value;
}

async function inspectIdentity(cdp, page) {
  const expression = `(() => {
    if (!document.body.classList.contains('iam-page')) return null;
    const result = {
      metrics: document.querySelectorAll('.iam-metric').length,
      tabs: document.querySelectorAll('.iam-family-tabs .wcp-tab').length,
      drawer: !!document.querySelector('#iamDrawer'),
      workflow: true,
      tabsInteractive: false,
      sections: new Set([...document.querySelectorAll('[data-iam-tab]')].map(item => item.dataset.iamTab)).size
    };
    document.querySelector('[data-iam-tab="roles"]')?.click();
    result.tabsInteractive = document.querySelector('[data-iam-view="roles"]') !== null && document.querySelector('[data-iam-tab="roles"][aria-selected="true"]') !== null;
    document.querySelector('[data-iam-tab="users"]')?.click();
    const trigger = document.querySelector('[data-iam-action]');
    if (trigger) {
      trigger.click();
      result.workflow = document.querySelector('#iamDrawer.open[aria-hidden="false"]') !== null;
      document.querySelector('[data-iam-close]')?.click();
    }
    if (location.pathname.endsWith('users-roles.html')) {
      const before = document.querySelectorAll('.wcp-user-card').length;
      document.querySelector('[data-iam-action="invite"]')?.click();
      const email = document.querySelector('#iam-email'); if (email) email.value = 'audit@example.com';
      document.querySelector('[data-iam-form="invite"]')?.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
      result.userCreated = document.querySelectorAll('.wcp-user-card').length === before + 1;
    }
    return result;
  })()`;
  const result = await cdp.send('Runtime.evaluate', { expression, returnByValue: true });
  return result.result.value;
}

async function inspectResellerCenter(cdp) {
  const expression = `(() => {
    if (document.body.dataset.resellerService !== 'center') return null;
    const result = {
      metrics: document.querySelectorAll('.res-metrics article').length,
      tabs: document.querySelectorAll('.res-tabs [data-res-tab]').length,
      boundary: document.querySelectorAll('.res-boundary > div').length,
      drawer: !!document.querySelector('#resDrawer'),
      tabWorkflow: false,
      created: false,
      servicePolicy: false
    };
    document.querySelector('.res-tabs [data-res-tab="capacity"]')?.click();
    result.tabWorkflow = document.querySelector('[data-res-view="capacity"]') !== null && document.querySelector('.res-tabs [data-res-tab="capacity"][aria-selected="true"]') !== null;
    document.querySelector('.res-tabs [data-res-tab="customers"]')?.click();
    const before = document.querySelectorAll('[data-res-customer]').length;
    document.querySelector('[data-res-action="create-customer"]')?.click();
    const name = document.querySelector('[data-res-form="create-customer"] [name="name"]'); if (name) name.value = 'Audit Customer';
    const owner = document.querySelector('[data-res-form="create-customer"] [name="owner"]'); if (owner) owner.value = 'owner@audit-reseller.example';
    const domain = document.querySelector('[data-res-form="create-customer"] [name="domain"]'); if (domain) domain.value = 'audit-reseller.example';
    document.querySelector('[data-res-form="create-customer"]')?.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    result.created = document.querySelectorAll('[data-res-customer]').length === before + 1;
    document.querySelector('.res-tabs [data-res-tab="services"]')?.click();
    const service = document.querySelector('[data-res-service]');
    const initial = service?.checked;
    service?.click();
    result.servicePolicy = Boolean(service && service.checked !== initial);
    return result;
  })()`;
  const result = await cdp.send('Runtime.evaluate', { expression, returnByValue: true });
  return result.result.value;
}

async function inspectRuntimeCenter(cdp, page) {
  if (page !== 'web-runtime.html') return null;
  const expression = `(() => {
    if (document.body.dataset.runtimeCenter !== 'root') return null;
    const storedBefore = JSON.parse(localStorage.getItem('wcp360-runtime-center-v1') || '{}');
    const versionsBefore = storedBefore.versions?.length || 0;
    const profilesBefore = storedBefore.profiles?.length || 0;
    const jobsBefore = window.WCPRuntime?.getState().jobs.length || 0;
    const result = {
      metrics: document.querySelectorAll('.rtc-metric').length,
      tabs: document.querySelectorAll('[data-rtc-tab]').length,
      boundary: document.querySelectorAll('.rtc-boundary > div').length,
      drawer: !!document.querySelector('#rtcDrawer'),
      tabWorkflow: false,
      versions: 0,
      modules: 0,
      profiles: 0,
      assignments: 0,
      installed: false,
      profileCreated: false,
      modulePolicy: false,
      runtimeJobs: false
    };
    document.querySelector('[data-rtc-tab="languages"]')?.click();
    result.tabWorkflow = document.querySelector('[data-rtc-view="languages"]') !== null && document.querySelector('[data-rtc-tab="languages"].active[aria-selected="true"]') !== null;
    document.querySelector('[data-rtc-action="install"]')?.click();
    const install = document.querySelector('[data-rtc-form="install-runtime"]');
    if (install) {
      install.elements.family.value = 'Node.js';
      install.elements.version.value = '22.audit';
      install.querySelector('input[type="checkbox"]').checked = true;
      install.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    }
    let stored = JSON.parse(localStorage.getItem('wcp360-runtime-center-v1') || '{}');
    result.installed = (stored.versions?.length || 0) === versionsBefore + 1;
    document.querySelector('[data-rtc-tab="profiles"]')?.click();
    result.profiles = document.querySelectorAll('[data-rtc-profile]').length;
    document.querySelector('[data-rtc-action="create-profile"]')?.click();
    const profile = document.querySelector('[data-rtc-form="create-profile"]');
    if (profile) {
      profile.elements.name.value = 'Audit Stack';
      profile.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    }
    stored = JSON.parse(localStorage.getItem('wcp360-runtime-center-v1') || '{}');
    result.profileCreated = (stored.profiles?.length || 0) === profilesBefore + 1;
    document.querySelector('[data-rtc-tab="modules"]')?.click();
    result.modules = document.querySelectorAll('[data-rtc-module]').length;
    const moduleToggle = document.querySelector('[data-rtc-module-toggle]');
    const initial = moduleToggle?.checked;
    moduleToggle?.click();
    result.modulePolicy = Boolean(moduleToggle && moduleToggle.checked !== initial);
    document.querySelector('[data-rtc-tab="php"]')?.click();
    result.versions = document.querySelectorAll('[data-runtime-version]').length;
    document.querySelector('[data-rtc-tab="assignments"]')?.click();
    result.assignments = document.querySelectorAll('[data-rtc-assignment]').length;
    result.runtimeJobs = (window.WCPRuntime?.getState().jobs.length || 0) >= jobsBefore + 3;
    return result;
  })()`;
  const result = await cdp.send('Runtime.evaluate', { expression, returnByValue: true });
  return result.result.value;
}

async function inspectRootControl(cdp) {
  const expression = `(() => {
    if (!document.body.dataset.rootModule) return null;
    const result = {
      metrics: document.querySelectorAll('.root-metric').length,
      rows: document.querySelectorAll('[data-root-row]').length,
      checks: document.querySelectorAll('.root-check').length,
      tabs: document.querySelectorAll('[data-root-view]').length,
      activity: document.querySelectorAll('.root-activity-item').length,
      tabWorkflow: false,
      drawer: false,
      workflow: false
    };
    const jobsBefore = window.WCPRuntime?.getState().jobs.length || 0;
    document.querySelector('[data-root-view="configuration"]')?.click();
    result.tabWorkflow = document.querySelector('[data-root-view="configuration"].active[aria-selected="true"]') !== null && document.querySelector('[data-root-view-copy]')?.textContent.includes('Versioned settings');
    document.querySelector('[data-root-primary]')?.click();
    result.drawer = document.querySelector('.root-drawer.open[aria-hidden="false"]') !== null;
    document.querySelector('.root-form')?.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    result.workflow = document.querySelector('.root-feedback.show') !== null;
    result.runtimeJob = (window.WCPRuntime?.getState().jobs.length || 0) === jobsBefore + 1;
    return result;
  })()`;
  const result = await cdp.send('Runtime.evaluate', { expression, returnByValue: true });
  return result.result.value;
}

async function inspectWebRouting(cdp, page) {
  if (page !== 'web-routing.html') return null;
  const expression = `(() => {
    if (document.body.dataset.parityModule !== 'web-routing-governance') return null;
    const jobsBefore = window.WCPRuntime?.getState().jobs.length || 0;
    const result = {
      metrics: document.querySelectorAll('.parity-kpi').length,
      capabilities: document.querySelectorAll('.parity-capability').length,
      tabs: document.querySelectorAll('[data-parity-tab]').length,
      rows: document.querySelectorAll('[data-parity-row]').length,
      policies: document.querySelectorAll('.parity-policy').length,
      drawer: false,
      workflow: false,
      runtimeJob: false
    };
    document.querySelector('[data-parity-tab="resources"]')?.click();
    result.tabWorkflow = document.querySelector('[data-parity-panel="resources"]:not([hidden])') !== null;
    document.querySelector('[data-parity-primary]')?.click();
    result.drawer = document.querySelector('.parity-drawer.open[aria-hidden="false"]') !== null;
    document.querySelector('.parity-form')?.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    result.workflow = document.querySelector('.parity-feedback.show') !== null;
    result.runtimeJob = (window.WCPRuntime?.getState().jobs.length || 0) === jobsBefore + 1;
    return result;
  })()`;
  const result = await cdp.send('Runtime.evaluate', { expression, returnByValue: true });
  return result.result.value;
}

async function inspectExperience(cdp, page) {
  if (!['canonical-user-dashboard.html', 'canonical-admin-dashboard.html'].includes(page)) return null;
  const expression = `(() => {
    const jobsBefore = window.WCPRuntime?.getState().jobs.length || 0;
    const changesBefore = window.WCPExperience?.getState().changes.length || 0;
    const pin = document.querySelector('[data-x-pin]');
    pin?.click();
    const pinned = pin?.getAttribute('aria-pressed') === 'true';
    document.querySelector('[data-x-control]')?.click();
    const control = document.querySelector('#wcpExperienceControl.open[aria-hidden="false"]') !== null;
    document.querySelector('#wcpExperienceControl [data-x-close]')?.click();
    document.dispatchEvent(new CustomEvent('wcp:review-action', { detail: { name: 'Audit safe operation', kind: 'audit-safe', risk: 'low' } }));
    const review = document.querySelector('#wcpExperienceReview.open[aria-hidden="false"]') !== null;
    document.querySelector('[data-x-review-form]')?.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    return {
      context: document.querySelectorAll('.wcp-experience-bar').length,
      commands: document.querySelectorAll('#wcpCommandBackdrop .wcp-command-result').length,
      pinned,
      control,
      review,
      change: (window.WCPExperience?.getState().changes.length || 0) === changesBefore + 1,
      job: (window.WCPRuntime?.getState().jobs.length || 0) === jobsBefore + 1,
      jobDetail,
      cancelled
    };
  })()`;
  const result = await cdp.send('Runtime.evaluate', { expression, returnByValue: true });
  return result.result.value;
}

async function inspectUserService(cdp) {
  const expression = `(() => {
    const service = document.body.dataset.userService;
    if (!service) return null;
    const result = {
      service,
      metrics: document.querySelectorAll('.usr-metric').length,
      items: service === 'webmail' ? document.querySelectorAll('[data-client-card]').length : document.querySelectorAll('.app-card').length,
      drawer: false,
      workflow: false
    };
    const runtimeBefore = window.WCPRuntime?.getState();
    const trigger = service === 'webmail' ? document.querySelector('[data-webmail-preferences]') : document.querySelector('[data-install-featured]');
    trigger?.click();
    result.drawer = document.querySelector('.usr-drawer.open[aria-hidden="false"]') !== null;
    document.querySelector('.usr-drawer-form')?.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    result.workflow = document.querySelector('.usr-feedback.show') !== null;
    const runtimeAfter = window.WCPRuntime?.getState();
    result.runtimeEvent = service === 'installer' ? runtimeAfter?.jobs.length === runtimeBefore?.jobs.length + 1 : runtimeAfter?.notifications?.[0]?.title === 'Webmail preference updated';
    return result;
  })()`;
  const result = await cdp.send('Runtime.evaluate', { expression, returnByValue: true });
  return result.result.value;
}

async function inspectUserWorkspace(cdp) {
  const expression = `(() => {
    const workspace = document.body.dataset.userWorkspace;
    if (!workspace) return null;
    const jobsBefore = window.WCPRuntime?.getState().jobs.length || 0;
    if (workspace === 'websites') {
      const before = document.querySelectorAll('[data-site-row]').length;
      const result = {
        workspace,
        metrics: document.querySelectorAll('.web-metrics article').length,
        tabs: document.querySelectorAll('[data-web-tab]').length,
        items: before,
        drawer: false,
        workflow: false,
        created: false,
        runtimeJob: false,
        rootSeparation: false,
        runtimeCards: 0,
        domains: 0,
        access: 0,
        policies: 0,
        deleteDrawer: false,
        deleteGate: false,
        deleted: false
      };
      document.querySelector('[data-web-action="create"]')?.click();
      result.drawer = document.querySelector('.web-drawer.open[aria-hidden="false"]') !== null;
      document.querySelector('.web-form')?.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
      result.created = document.querySelectorAll('[data-site-row]').length === before + 1;
      result.workflow = document.querySelector('.web-feedback.show') !== null;
      result.runtimeJob = (window.WCPRuntime?.getState().jobs.length || 0) === jobsBefore + 1;
      document.querySelector('[data-web-manage]')?.click();
      result.rootSeparation = document.querySelector('.web-form-note')?.textContent.includes('configured by ROOT') && !document.querySelector('.web-form [name="server"],.web-form [name="cpu"],.web-form [name="ram"],.web-form [name="iops"],.web-form [name="bandwidth"]');
      document.querySelector('[data-web-close]')?.click();
      const createdRow = [...document.querySelectorAll('[data-site-row]')].find(row => row.textContent.includes('newsite.example.com'));
      createdRow?.querySelector('[data-web-manage]')?.click();
      document.querySelector('[data-manage-action="delete"]')?.click();
      result.deleteDrawer = document.querySelector('.web-drawer.open [data-web-confirm]') !== null;
      const confirm = document.querySelector('[data-web-confirm]'), deleteSubmit = document.querySelector('[data-web-danger-submit]');
      if (confirm) { confirm.value = 'WRONG'; confirm.dispatchEvent(new Event('input', { bubbles: true })); const blocked = deleteSubmit?.disabled === true; confirm.value = 'newsite.example.com'; confirm.dispatchEvent(new Event('input', { bubbles: true })); result.deleteGate = blocked && deleteSubmit?.disabled === false; }
      document.querySelector('.web-form')?.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
      result.deleted = document.querySelectorAll('[data-site-row]').length === before;
      document.querySelector('[data-web-tab="runtime"]')?.click();
      result.runtimeCards = document.querySelectorAll('[data-runtime-site]').length;
      document.querySelector('[data-web-tab="domains"]')?.click();
      result.domains = document.querySelectorAll('[data-web-domain]').length;
      document.querySelector('[data-web-tab="access"]')?.click();
      result.access = document.querySelectorAll('[data-web-access]').length;
      document.querySelector('[data-web-tab="policies"]')?.click();
      result.policies = document.querySelectorAll('[data-web-setting]').length;
      return result;
    }
    if (workspace === 'files') {
      const before = document.querySelectorAll('[data-file-row]').length;
      const result = {
        workspace,
        metrics: document.querySelectorAll('.fm-metrics article').length,
        tabs: document.querySelectorAll('[data-fm-tab]').length,
        items: before,
        drawer: false,
        workflow: false,
        created: false,
        runtimeJob: false,
        accessView: false,
        deleteDrawer: false,
        deleteGate: false,
        deleted: false
      };
      document.querySelector('[data-fm-action="new-file"]')?.click();
      result.drawer = document.querySelector('.fm-drawer.open[aria-hidden="false"]') !== null;
      const name = document.querySelector('.fm-form [name="name"]'); if (name) name.value = 'audit-file.txt';
      document.querySelector('.fm-form')?.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
      result.created = document.querySelectorAll('[data-file-row]').length === before + 1;
      result.workflow = document.querySelector('.fm-feedback.show') !== null;
      const createdRow = [...document.querySelectorAll('[data-file-row]')].find(row => row.textContent.includes('audit-file.txt'));
      const createdBox = createdRow?.querySelector('[data-fm-select]'); if (createdBox) createdBox.click();
      document.querySelector('[data-fm-action="delete"]')?.click();
      result.deleteDrawer = document.querySelector('.fm-drawer.open [data-fm-confirm-word]') !== null;
      const confirm = document.querySelector('[data-fm-confirm-word]'), deleteSubmit = document.querySelector('[data-fm-delete-submit]');
      if (confirm) { confirm.value = 'WRONG'; confirm.dispatchEvent(new Event('input', { bubbles: true })); const blocked = deleteSubmit?.disabled === true; confirm.value = 'DELETE'; confirm.dispatchEvent(new Event('input', { bubbles: true })); result.deleteGate = blocked && deleteSubmit?.disabled === false; }
      document.querySelector('.fm-form')?.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
      result.deleted = ![...document.querySelectorAll('[data-file-row]')].some(row => row.textContent.includes('audit-file.txt'));
      document.querySelector('[data-fm-action="compress"]')?.click();
      result.runtimeJob = (window.WCPRuntime?.getState().jobs.length || 0) === jobsBefore + 1;
      document.querySelector('[data-fm-tab="access"]')?.click();
      result.accessView = document.querySelector('.fm-access-grid') !== null;
      return result;
    }
    if (workspace === 'databases') {
      const before = document.querySelectorAll('[data-database-row]').length;
      const result = {
        workspace,
        metrics: document.querySelectorAll('.db-metrics article').length,
        tabs: document.querySelectorAll('[data-db-tab]').length,
        items: before,
        drawer: false,
        workflow: false,
        created: false,
        runtimeJob: false,
        users: 0,
        remoteHosts: 0,
        tools: 0,
        toolShortcut: false,
        rootGovernance: false,
        connectionsVisible: false,
        capacityControls: false
      };
      document.querySelector('[data-db-action="new-database"]')?.click();
      result.drawer = document.querySelector('.db-drawer.open[aria-hidden="false"]') !== null;
      const name = document.querySelector('.db-form [name="name"]'); if (name) name.value = 'audit';
      document.querySelector('.db-form')?.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
      result.created = document.querySelectorAll('[data-database-row]').length === before + 1;
      result.workflow = document.querySelector('.db-feedback.show') !== null;
      result.runtimeJob = (window.WCPRuntime?.getState().jobs.length || 0) === jobsBefore + 1;
      result.toolShortcut = document.querySelector('[data-db-action="open-default-tool"]') !== null && document.querySelector('.db-admin-access') !== null;
      document.querySelector('[data-db-manage]')?.click();
      result.connectionsVisible = [...document.querySelectorAll('.db-drawer *')].some(element => element.children.length === 0 && element.textContent.trim() === 'Connections');
      result.capacityControls = document.querySelector('.db-drawer [name="quota"],.db-drawer [name="maxConnections"]') !== null;
      document.querySelector('[data-db-close]')?.click();
      document.querySelector('[data-db-tab="users"]')?.click();
      result.users = document.querySelectorAll('[data-db-user-row]').length;
      document.querySelector('[data-db-tab="remote"]')?.click();
      result.remoteHosts = document.querySelectorAll('[data-remote-row]').length;
      document.querySelector('[data-db-tab="tools"]')?.click();
      result.tools = document.querySelectorAll('[data-user-db-tool]').length;
      result.rootGovernance = document.querySelector('.db-tools-governance')?.textContent.includes('ROOT') || false;
      return result;
    }
    const before = document.querySelectorAll('[data-mailbox-row]').length;
    const result = {
      workspace,
      metrics: document.querySelectorAll('.mail-metrics article').length,
      tabs: document.querySelectorAll('[data-mail-tab]').length,
      items: before,
      drawer: false,
      workflow: false,
      created: false,
      runtimeJob: false,
      webmailClients: 0,
      securityCards: 0
    };
    document.querySelector('[data-mail-action="new-mailbox"]')?.click();
    result.drawer = document.querySelector('.mail-drawer.open[aria-hidden="false"]') !== null;
    const local = document.querySelector('.mail-form [name="local"]'); if (local) local.value = 'audit';
    const display = document.querySelector('.mail-form [name="displayName"]'); if (display) display.value = 'Audit';
    const password = document.querySelector('.mail-form [name="password"]'); if (password) password.value = 'Audit-Only-2026!';
    document.querySelector('.mail-form')?.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    result.created = document.querySelectorAll('[data-mailbox-row]').length === before + 1;
    result.workflow = document.querySelector('.mail-feedback.show') !== null;
    result.runtimeJob = (window.WCPRuntime?.getState().jobs.length || 0) === jobsBefore + 1;
    document.querySelector('[data-mail-tab="webmail"]')?.click();
    result.webmailClients = document.querySelectorAll('[data-mail-webmail]').length;
    document.querySelector('[data-mail-tab="security"]')?.click();
    result.securityCards = document.querySelectorAll('.auth-card').length;
    return result;
  })()`;
  const result = await cdp.send('Runtime.evaluate', { expression, returnByValue: true });
  return result.result.value;
}

async function inspectRootWebmail(cdp) {
  const expression = `(() => {
    if (document.body.dataset.mailCenter !== 'root') return null;
    document.querySelector('[data-mail-tab="webmail"]')?.click();
    const jobsBefore = window.WCPRuntime?.getState().jobs.length || 0;
    const result = {
      metrics: document.querySelectorAll('.mic-metric').length,
      providers: document.querySelectorAll('[data-provider]').length,
      providerSwitches: document.querySelectorAll('[data-provider-enabled]').length,
      policies: document.querySelectorAll('[data-global-enabled],[data-user-choice],[data-enforce-tls],[data-auto-updates],[data-session-minutes],[data-plan-access]').length,
      workflow: false,
      runtimeJob: false,
      persisted: false
    };
    document.querySelector('[data-save-webmail]')?.click();
    result.workflow = document.querySelector('.root-feedback.show:not(.error)') !== null;
    result.runtimeJob = (window.WCPRuntime?.getState().jobs.length || 0) === jobsBefore + 1;
    try {
      const saved = JSON.parse(localStorage.getItem('wcp360-webmail-clients-v1'));
      result.persisted = saved?.clients?.length === 3 && saved.clients.some(client => client.enabled) && Boolean(saved.defaultClient);
    } catch (error) {}
    return result;
  })()`;
  const result = await cdp.send('Runtime.evaluate', { expression, returnByValue: true });
  return result.result.value;
}

async function inspectMailInfrastructure(cdp, page) {
  if (page !== 'admin-mail.html') return null;
  const expression = `(() => {
    if (document.body.dataset.mailCenter !== 'root') return null;
    const before = JSON.parse(localStorage.getItem('wcp360-mail-infrastructure-v1') || '{}');
    const profilesBefore = before.profiles?.length || 0;
    const queueBefore = before.queue?.length || 0;
    const jobsBefore = window.WCPRuntime?.getState().jobs.length || 0;
    const result = {
      metrics: document.querySelectorAll('.mic-metric').length,
      tabs: document.querySelectorAll('[data-mail-tab]').length,
      boundary: document.querySelectorAll('.mic-boundary > div').length,
      drawer: !!document.querySelector('#mailDrawer'),
      nodes: 0,
      profiles: 0,
      queue: 0,
      tabWorkflow: false,
      profileCreated: false,
      removeDrawer: false,
      removeGate: false,
      removed: false,
      runtimeJobs: false
    };
    document.querySelector('[data-mail-tab="nodes"]')?.click();
    result.nodes = document.querySelectorAll('[data-mail-node]').length;
    result.profiles = document.querySelectorAll('[data-mail-profile]').length;
    result.tabWorkflow = document.querySelector('[data-mail-view="nodes"]') !== null && document.querySelector('[data-mail-tab="nodes"].active[aria-selected="true"]') !== null;
    document.querySelector('[data-mail-action="create-profile"]')?.click();
    const profile = document.querySelector('[data-mail-form="create-profile"]');
    if (profile) {
      profile.elements.name.value = 'Audit Mail Profile';
      profile.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    }
    let stored = JSON.parse(localStorage.getItem('wcp360-mail-infrastructure-v1') || '{}');
    result.profileCreated = (stored.profiles?.length || 0) === profilesBefore + 1;
    document.querySelector('[data-mail-tab="queue"]')?.click();
    result.queue = document.querySelectorAll('[data-mail-queue]').length;
    const remove = document.querySelector('[data-mail-action="remove-message"]');
    const message = remove?.dataset.message;
    remove?.click();
    result.removeDrawer = document.querySelector('#mailDrawer.open [data-mail-form="remove-message"]') !== null;
    const confirmation = document.querySelector('[data-mail-confirm]');
    const submit = document.querySelector('[data-mail-submit]');
    if (confirmation && submit) {
      const initiallyBlocked = submit.disabled;
      confirmation.value = message || '';
      confirmation.dispatchEvent(new Event('input', { bubbles: true }));
      result.removeGate = initiallyBlocked && !submit.disabled;
      document.querySelector('[data-mail-form="remove-message"]')?.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    }
    stored = JSON.parse(localStorage.getItem('wcp360-mail-infrastructure-v1') || '{}');
    result.removed = (stored.queue?.length || 0) === queueBefore - 1;
    result.runtimeJobs = (window.WCPRuntime?.getState().jobs.length || 0) >= jobsBefore + 2;
    return result;
  })()`;
  const result = await cdp.send('Runtime.evaluate', { expression, returnByValue: true });
  return result.result.value;
}

async function inspectCertificateCenter(cdp, page) {
  if (page !== 'certificates.html') return null;
  const expression = `(() => {
    if (document.body.dataset.certificateCenter !== 'root') return null;
    const before = JSON.parse(localStorage.getItem('wcp360-certificate-center-v1') || '{}');
    const countBefore = before.certificates?.length || 0;
    const jobsBefore = window.WCPRuntime?.getState().jobs.length || 0;
    const result = {
      metrics: document.querySelectorAll('.crt-metric').length,
      tabs: document.querySelectorAll('[data-cert-tab]').length,
      boundary: document.querySelectorAll('.crt-boundary > div').length,
      drawer: !!document.querySelector('#certificateDrawer'),
      coverage: 0,
      certificates: 0,
      services: 0,
      authorities: 0,
      validation: 0,
      renewals: 0,
      tabWorkflow: false,
      issued: false,
      revokeDrawer: false,
      revokeGate: false,
      revoked: false,
      runtimeJobs: false
    };
    document.querySelector('[data-cert-tab="coverage"]')?.click();
    result.coverage = document.querySelectorAll('[data-cert-coverage]').length;
    result.tabWorkflow = document.querySelector('[data-cert-view="coverage"]') !== null && document.querySelector('[data-cert-tab="coverage"].active[aria-selected="true"]') !== null;
    document.querySelector('[data-cert-tab="inventory"]')?.click();
    result.certificates = document.querySelectorAll('[data-cert-row]').length;
    document.querySelector('[data-cert-action="issue"]')?.click();
    const issue = document.querySelector('[data-cert-form="issue"]');
    if (issue) {
      issue.elements.name.value = 'audit-cert.example.com';
      issue.querySelector('input[type="checkbox"]').checked = true;
      issue.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    }
    let stored = JSON.parse(localStorage.getItem('wcp360-certificate-center-v1') || '{}');
    result.issued = (stored.certificates?.length || 0) === countBefore + 1;
    document.querySelector('[data-cert-close]')?.click();
    const revoke = document.querySelector('[data-cert-action="revoke"]');
    const expected = revoke?.dataset.name;
    revoke?.click();
    result.revokeDrawer = document.querySelector('#certificateDrawer.open [data-cert-form="revoke"]') !== null;
    const confirmation = document.querySelector('[data-cert-confirm]');
    const submit = document.querySelector('[data-cert-submit]');
    if (confirmation && submit) {
      const blocked = submit.disabled;
      confirmation.value = expected || '';
      confirmation.dispatchEvent(new Event('input', { bubbles: true }));
      result.revokeGate = blocked && !submit.disabled;
      document.querySelector('[data-cert-form="revoke"]')?.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    }
    stored = JSON.parse(localStorage.getItem('wcp360-certificate-center-v1') || '{}');
    result.revoked = (stored.certificates?.length || 0) === countBefore;
    document.querySelector('[data-cert-tab="services"]')?.click();
    result.services = document.querySelectorAll('[data-cert-service]').length;
    document.querySelector('[data-cert-tab="authorities"]')?.click();
    result.authorities = document.querySelectorAll('[data-cert-authority]').length;
    document.querySelector('[data-cert-tab="validation"]')?.click();
    result.validation = document.querySelectorAll('[data-cert-dcv]').length;
    document.querySelector('[data-cert-tab="renewals"]')?.click();
    result.renewals = document.querySelectorAll('[data-cert-renewal]').length;
    result.runtimeJobs = (window.WCPRuntime?.getState().jobs.length || 0) >= jobsBefore + 2;
    return result;
  })()`;
  const result = await cdp.send('Runtime.evaluate', { expression, returnByValue: true });
  return result.result.value;
}

async function inspectApplicationCatalog(cdp, page) {
  if (page !== 'application-catalog.html') return null;
  const expression = `(() => {
    if (document.body.dataset.applicationCatalog !== 'root') return null;
    const before = JSON.parse(localStorage.getItem('wcp360-application-catalog-v1') || '{}');
    const appsBefore = before.apps?.length || 0;
    const jobsBefore = window.WCPRuntime?.getState().jobs.length || 0;
    const result = {
      metrics: document.querySelectorAll('.cat-metric').length,
      tabs: document.querySelectorAll('[data-cat-tab]').length,
      boundary: document.querySelectorAll('.cat-boundary > div').length,
      drawer: !!document.querySelector('#catalogDrawer'),
      tabWorkflow: false,
      apps: 0,
      sources: 0,
      templates: 0,
      updates: 0,
      policies: 0,
      installations: 0,
      licenses: 0,
      history: 0,
      appCreated: false,
      togglePersisted: false,
      runtimeJobs: false
    };
    document.querySelector('[data-cat-tab="catalog"]')?.click();
    result.tabWorkflow = document.querySelector('[data-cat-tab="catalog"].active[aria-selected="true"]') !== null;
    result.apps = document.querySelectorAll('[data-cat-app]').length;
    document.querySelector('[data-cat-action="add-app"]')?.click();
    const form = document.querySelector('[data-cat-form="add-app"]');
    if (form) {
      form.elements.name.value = 'Audit Catalog Application';
      form.elements.version.value = '1.0.audit';
      form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    }
    let stored = JSON.parse(localStorage.getItem('wcp360-application-catalog-v1') || '{}');
    result.appCreated = (stored.apps?.length || 0) === appsBefore + 1;
    const toggle = document.querySelector('[data-cat-app-toggle="ghost"]');
    const initial = toggle?.checked;
    toggle?.click();
    stored = JSON.parse(localStorage.getItem('wcp360-application-catalog-v1') || '{}');
    result.togglePersisted = Boolean(toggle && stored.apps?.find(app => app.id === 'ghost')?.enabled === !initial);
    document.querySelector('[data-cat-tab="sources"]')?.click();
    result.sources = document.querySelectorAll('[data-cat-source]').length;
    document.querySelector('[data-cat-tab="templates"]')?.click();
    result.templates = document.querySelectorAll('[data-cat-template]').length;
    document.querySelector('[data-cat-tab="updates"]')?.click();
    result.updates = document.querySelectorAll('[data-cat-update]').length;
    document.querySelector('[data-cat-tab="policies"]')?.click();
    result.policies = document.querySelectorAll('[data-cat-policy]').length;
    document.querySelector('[data-cat-tab="installations"]')?.click();
    result.installations = document.querySelectorAll('[data-cat-installation]').length;
    document.querySelector('[data-cat-tab="licenses"]')?.click();
    result.licenses = document.querySelectorAll('[data-cat-license]').length;
    document.querySelector('[data-cat-tab="history"]')?.click();
    result.history = document.querySelectorAll('[data-cat-event]').length;
    result.runtimeJobs = (window.WCPRuntime?.getState().jobs.length || 0) >= jobsBefore + 2;
    return result;
  })()`;
  const result = await cdp.send('Runtime.evaluate', { expression, returnByValue: true });
  return result.result.value;
}

async function inspectFileTransferCenter(cdp, page) {
  if (page !== 'file-transfer-services.html') return null;
  const expression = `(() => {
    if (document.body.dataset.fileTransferCenter !== 'root') return null;
    const before = JSON.parse(localStorage.getItem('wcp360-file-transfer-center-v1') || '{}');
    const endpointsBefore = before.endpoints?.length || 0;
    const sessionsBefore = before.sessions?.length || 0;
    const sessionBackup = before.sessions?.[0] ? JSON.parse(JSON.stringify(before.sessions[0])) : null;
    const jobsBefore = window.WCPRuntime?.getState().jobs.length || 0;
    const result = {
      metrics: document.querySelectorAll('.ftc-metric').length,
      tabs: document.querySelectorAll('[data-ftc-tab]').length,
      boundary: document.querySelectorAll('.ftc-boundary > div').length,
      drawer: !!document.querySelector('#fileTransferDrawer'),
      tabWorkflow: false,
      endpoints: 0,
      protocols: 0,
      plainFtpLocked: false,
      profiles: 0,
      policies: 0,
      sessions: 0,
      network: 0,
      security: 0,
      bans: 0,
      history: 0,
      endpointCreated: false,
      togglePersisted: false,
      disconnectDrawer: false,
      disconnectGate: false,
      disconnected: false,
      runtimeJobs: false
    };
    document.querySelector('[data-ftc-tab="endpoints"]')?.click();
    result.tabWorkflow = document.querySelector('[data-ftc-tab="endpoints"].active[aria-selected="true"]') !== null;
    result.endpoints = document.querySelectorAll('[data-ftc-endpoint]').length;
    document.querySelector('[data-ftc-action="add-endpoint"]')?.click();
    const endpointForm = document.querySelector('[data-ftc-form="add-endpoint"]');
    if (endpointForm) {
      endpointForm.elements.name.value = 'sftp-audit-edge';
      endpointForm.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    }
    let stored = JSON.parse(localStorage.getItem('wcp360-file-transfer-center-v1') || '{}');
    result.endpointCreated = (stored.endpoints?.length || 0) === endpointsBefore + 1;
    document.querySelector('[data-ftc-tab="protocols"]')?.click();
    result.protocols = document.querySelectorAll('[data-ftc-protocol]').length;
    result.plainFtpLocked = document.querySelector('[data-ftc-protocol="ftp"] [data-ftc-protocol-toggle]') === null && document.querySelector('[data-ftc-protocol="ftp"]')?.textContent.includes('Permanently denied');
    const toggle = document.querySelector('[data-ftc-protocol-toggle="webdav"]');
    const initial = toggle?.checked;
    toggle?.click();
    stored = JSON.parse(localStorage.getItem('wcp360-file-transfer-center-v1') || '{}');
    result.togglePersisted = Boolean(toggle && stored.protocols?.find(protocol => protocol.id === 'webdav')?.enabled === !initial);
    document.querySelector('[data-ftc-tab="profiles"]')?.click();
    result.profiles = document.querySelectorAll('[data-ftc-profile]').length;
    document.querySelector('[data-ftc-tab="policies"]')?.click();
    result.policies = document.querySelectorAll('[data-ftc-policy]').length;
    document.querySelector('[data-ftc-tab="sessions"]')?.click();
    result.sessions = document.querySelectorAll('[data-ftc-session]').length;
    const disconnect = document.querySelector('[data-ftc-disconnect]');
    const expected = disconnect?.dataset.ftcDisconnect;
    disconnect?.click();
    result.disconnectDrawer = document.querySelector('#fileTransferDrawer.open [data-ftc-form="disconnect"]') !== null;
    const confirmation = document.querySelector('[data-ftc-confirm]');
    const submit = document.querySelector('[data-ftc-submit]');
    if (confirmation && submit) {
      const blocked = submit.disabled;
      confirmation.value = expected || '';
      confirmation.dispatchEvent(new Event('input', { bubbles: true }));
      result.disconnectGate = blocked && !submit.disabled;
      document.querySelector('[data-ftc-form="disconnect"]')?.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    }
    stored = JSON.parse(localStorage.getItem('wcp360-file-transfer-center-v1') || '{}');
    result.disconnected = (stored.sessions?.length || 0) === sessionsBefore - 1;
    if (sessionBackup && !stored.sessions?.some(session => session.id === sessionBackup.id)) {
      stored.sessions.unshift(sessionBackup);
      localStorage.setItem('wcp360-file-transfer-center-v1', JSON.stringify(stored));
    }
    document.querySelector('[data-ftc-tab="network"]')?.click();
    result.network = document.querySelectorAll('[data-ftc-network]').length;
    document.querySelector('[data-ftc-tab="security"]')?.click();
    result.security = document.querySelectorAll('[data-ftc-security]').length;
    result.bans = document.querySelectorAll('[data-ftc-ban]').length;
    document.querySelector('[data-ftc-tab="history"]')?.click();
    result.history = document.querySelectorAll('[data-ftc-event]').length;
    result.runtimeJobs = (window.WCPRuntime?.getState().jobs.length || 0) >= jobsBefore + 3;
    return result;
  })()`;
  const result = await cdp.send('Runtime.evaluate', { expression, returnByValue: true });
  return result.result.value;
}

async function inspectDnsCenter(cdp, page) {
  if (page !== 'admin-dns.html') return null;
  const expression = `(() => {
    if (document.body.dataset.dnsCenter !== 'root') return null;
    const before = JSON.parse(localStorage.getItem('wcp360-dns-center-v1') || '{}');
    const zonesBefore = before.zones?.length || 0;
    const zoneBackup = before.zones?.[0] ? JSON.parse(JSON.stringify(before.zones[0])) : null;
    const jobsBefore = window.WCPRuntime?.getState().jobs.length || 0;
    const result = {
      metrics: document.querySelectorAll('.dnc-metric').length,
      tabs: document.querySelectorAll('[data-dnc-tab]').length,
      boundary: document.querySelectorAll('.dnc-boundary > div').length,
      drawer: !!document.querySelector('#dnsCenterDrawer'),
      tabWorkflow: false,
      zones: 0, templates: 0, nodes: 0, nameservers: 0, dnssec: 0,
      providers: 0, policies: 0, recordTypes: 0, history: 0,
      zoneCreated: false, providerPersisted: false, nameserverConfigured: false,
      deleteDrawer: false, deleteGate: false, deleted: false, runtimeJobs: false
    };
    document.querySelector('[data-dnc-tab="zones"]')?.click();
    result.tabWorkflow = document.querySelector('[data-dnc-tab="zones"].active[aria-selected="true"]') !== null;
    result.zones = document.querySelectorAll('[data-dnc-zone]').length;
    document.querySelector('[data-dnc-action="add-zone"]')?.click();
    const addForm = document.querySelector('[data-dnc-form="add-zone"]');
    if (addForm) {
      addForm.elements.name.value = 'audit-' + Date.now() + '.example.com';
      addForm.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    }
    let stored = JSON.parse(localStorage.getItem('wcp360-dns-center-v1') || '{}');
    result.zoneCreated = (stored.zones?.length || 0) === zonesBefore + 1;
    document.querySelector('[data-dnc-tab="providers"]')?.click();
    result.providers = document.querySelectorAll('[data-dnc-provider]').length;
    const providerToggle = document.querySelector('[data-dnc-provider-toggle="digitalocean"]');
    const providerInitial = providerToggle?.checked;
    providerToggle?.click();
    stored = JSON.parse(localStorage.getItem('wcp360-dns-center-v1') || '{}');
    result.providerPersisted = Boolean(providerToggle && stored.providers?.find(provider => provider.id === 'digitalocean')?.enabled === !providerInitial);
    document.querySelector('[data-dnc-tab="templates"]')?.click();
    result.templates = document.querySelectorAll('[data-dnc-template]').length;
    document.querySelector('[data-dnc-tab="cluster"]')?.click();
    result.nodes = document.querySelectorAll('[data-dnc-node]').length;
    document.querySelector('[data-dnc-tab="nameservers"]')?.click();
    result.nameservers = document.querySelectorAll('[data-dnc-nameserver]').length;
    document.querySelector('[data-dnc-action="add-nameserver"]')?.click();
    const nameserverForm = document.querySelector('[data-dnc-form="save-nameserver"]');
    if (nameserverForm) {
      nameserverForm.elements.name.value = 'Audit nameservers ' + Date.now();
      nameserverForm.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    }
    stored = JSON.parse(localStorage.getItem('wcp360-dns-center-v1') || '{}');
    result.nameserverConfigured = (stored.nameservers?.length || 0) === result.nameservers + 1 && stored.nameservers.at(-1)?.glue === 'Verification queued';
    document.querySelector('[data-dnc-tab="dnssec"]')?.click();
    result.dnssec = document.querySelectorAll('[data-dnc-dnssec]').length;
    document.querySelector('[data-dnc-tab="policies"]')?.click();
    result.policies = document.querySelectorAll('[data-dnc-policy]').length;
    result.recordTypes = document.querySelectorAll('[data-dnc-record-type]').length;
    document.querySelector('[data-dnc-tab="zones"]')?.click();
    const remove = document.querySelector('[data-dnc-delete-zone]');
    const zoneId = remove?.dataset.dncDeleteZone;
    const expectedName = stored.zones?.find(zone => zone.id === zoneId)?.name;
    remove?.click();
    result.deleteDrawer = document.querySelector('#dnsCenterDrawer.open [data-dnc-form="delete-zone"]') !== null;
    const confirmation = document.querySelector('[data-dnc-confirm]');
    const submit = document.querySelector('[data-dnc-submit]');
    if (confirmation && submit) {
      const blocked = submit.disabled;
      confirmation.value = expectedName || '';
      confirmation.dispatchEvent(new Event('input', { bubbles: true }));
      result.deleteGate = blocked && !submit.disabled;
      document.querySelector('[data-dnc-form="delete-zone"]')?.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    }
    stored = JSON.parse(localStorage.getItem('wcp360-dns-center-v1') || '{}');
    result.deleted = expectedName ? !stored.zones?.some(zone => zone.name === expectedName) : false;
    if (zoneBackup && !stored.zones?.some(zone => zone.id === zoneBackup.id)) {
      stored.zones.unshift(zoneBackup);
      localStorage.setItem('wcp360-dns-center-v1', JSON.stringify(stored));
    }
    document.querySelector('[data-dnc-tab="history"]')?.click();
    result.history = document.querySelectorAll('[data-dnc-event]').length;
    result.runtimeJobs = (window.WCPRuntime?.getState().jobs.length || 0) >= jobsBefore + 3;
    return result;
  })()`;
  const result = await cdp.send('Runtime.evaluate', { expression, returnByValue: true });
  return result.result.value;
}

async function inspectRootDatabase(cdp) {
  const expression = `(() => {
    if (document.body.dataset.rootService !== 'databases') return null;
    const jobsBefore = window.WCPRuntime?.getState().jobs.length || 0;
    const result = {
      metrics: document.querySelectorAll('.root-metric').length,
      providers: document.querySelectorAll('[data-db-tool]').length,
      providerSwitches: document.querySelectorAll('[data-tool-enabled]').length,
      policies: document.querySelectorAll('[data-db-global-enabled],[data-db-user-choice],[data-db-enforce-tls],[data-db-auto-updates],[data-db-session-minutes],[data-db-plan-access]').length,
      engines: document.querySelectorAll('.root-db-engine-grid article').length,
      workflow: false,
      runtimeJob: false,
      persisted: false
    };
    document.querySelector('[data-save-db-tools]')?.click();
    result.workflow = document.querySelector('.root-feedback.show:not(.error)') !== null;
    result.runtimeJob = (window.WCPRuntime?.getState().jobs.length || 0) === jobsBefore + 1;
    try {
      const saved = JSON.parse(localStorage.getItem('wcp360-database-tools-v1'));
      result.persisted = saved?.tools?.length === 3 && saved.tools.some(tool => tool.enabled) && Boolean(saved.defaultTool);
    } catch (error) {}
    return result;
  })()`;
  const result = await cdp.send('Runtime.evaluate', { expression, returnByValue: true });
  return result.result.value;
}

async function inspectRootCapacity(cdp) {
  const expression = `(() => {
    if (document.body.dataset.rootService !== 'capacity') return null;
    const jobsBefore = window.WCPRuntime?.getState().jobs.length || 0;
    const before = document.querySelectorAll('[data-cap-account]').length;
    const result = {
      metrics: document.querySelectorAll('.cap-metrics article').length,
      tabs: document.querySelectorAll('[data-cap-tab]').length,
      accounts: before,
      emails: document.querySelectorAll('.cap-account-email').length,
      resourcePills: document.querySelectorAll('.cap-account-list .cap-resource-pills > span').length,
      suspendActions: document.querySelectorAll('[data-cap-suspend]').length,
      terminateActions: document.querySelectorAll('[data-cap-terminate]').length,
      templatesLink: document.querySelector('[data-cap-templates]')?.getAttribute('href') === 'web-default-pages.html',
      drawer: false,
      workflow: false,
      created: false,
      createdEmail: false,
      suspended: false,
      suspendTemplate: false,
      terminateGate: false,
      terminated: false,
      terminateTemplate: false,
      runtimeJob: false,
      placementCards: 0,
      addons: 0,
      servers: 0
    };
    document.querySelector('[data-cap-action="create-account"]')?.click();
    result.drawer = document.querySelector('.cap-drawer.open[aria-hidden="false"]') !== null;
    const domain = document.querySelector('.cap-form [name="domain"]'); if (domain) domain.value = 'audit-capacity.example';
    const owner = document.querySelector('.cap-form [name="owner"]'); if (owner) owner.value = 'Audit Capacity';
    const email = document.querySelector('.cap-form [name="email"]'); if (email) email.value = 'owner@audit-capacity.example';
    document.querySelector('.cap-form')?.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    result.created = document.querySelectorAll('[data-cap-account]').length === before + 1;
    result.createdEmail = document.querySelector('[data-cap-account] .cap-account-email')?.textContent.includes('owner@audit-capacity.example') || false;
    result.workflow = document.querySelector('.cap-feedback.show') !== null;
    document.querySelector('[data-cap-account] [data-cap-suspend]')?.click();
    document.querySelector('.cap-form')?.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    result.suspended = document.querySelector('[data-cap-account] .wcp-status')?.textContent.trim() === 'Suspended';
    try { result.suspendTemplate = JSON.parse(localStorage.getItem('wcp360-hosting-capacity-v1'))?.accounts?.find(item => item.domain === 'audit-capacity.example')?.defaultPage === 'suspended'; } catch (error) {}
    document.querySelector('[data-cap-account] [data-cap-terminate]')?.click();
    const confirmation = document.querySelector('[data-cap-confirm]');
    const terminateButton = document.querySelector('[data-cap-terminate-submit]');
    const disabledBefore = Boolean(terminateButton?.disabled);
    if (confirmation) { confirmation.value = 'audit-capacity.example'; confirmation.dispatchEvent(new Event('input', { bubbles: true })); }
    result.terminateGate = disabledBefore && terminateButton?.disabled === false;
    document.querySelector('.cap-form')?.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    result.terminated = document.querySelector('[data-cap-account] .wcp-status')?.textContent.trim() === 'Terminating';
    try { result.terminateTemplate = JSON.parse(localStorage.getItem('wcp360-hosting-capacity-v1'))?.accounts?.find(item => item.domain === 'audit-capacity.example')?.defaultPage === 'unavailable'; } catch (error) {}
    result.runtimeJob = (window.WCPRuntime?.getState().jobs.length || 0) === jobsBefore + 3;
    document.querySelector('[data-cap-tab="placement"]')?.click();
    result.placementCards = document.querySelectorAll('.cap-placement-card').length;
    document.querySelector('[data-cap-tab="addons"]')?.click();
    result.addons = document.querySelectorAll('.cap-addon-grid article').length;
    document.querySelector('[data-cap-tab="pools"]')?.click();
    result.servers = document.querySelectorAll('[data-cap-server]').length;
    return result;
  })()`;
  const result = await cdp.send('Runtime.evaluate', { expression, returnByValue: true });
  return result.result.value;
}

async function inspectRootDefaultPages(cdp) {
  const expression = `(() => {
    if (document.body.dataset.rootService !== 'default-pages') return null;
    const jobsBefore = window.WCPRuntime?.getState().jobs.length || 0;
    const result = {
      metrics: document.querySelectorAll('.dpt-metrics article').length,
      tabs: document.querySelectorAll('[data-dpt-tab]').length,
      templates: document.querySelectorAll('[data-dpt-template]').length,
      categories: document.querySelectorAll('[data-dpt-category-section]').length,
      preview: false,
      mobilePreview: false,
      drawer: false,
      edited: false,
      published: false,
      runtimeJob: false,
      brandingFields: 0,
      rules: 0,
      locales: 0,
      versions: 0
    };
    document.querySelector('[data-dpt-preview]')?.click();
    result.preview = document.querySelector('.dpt-preview.open[aria-hidden="false"]') !== null;
    document.querySelector('[data-dpt-device="mobile"]')?.click();
    result.mobilePreview = document.querySelector('[data-dpt-browser].mobile') !== null;
    document.querySelector('[data-dpt-preview-close]')?.click();
    const firstId = document.querySelector('[data-dpt-template]')?.dataset.dptTemplate;
    document.querySelector('[data-dpt-edit]')?.click();
    result.drawer = document.querySelector('.dpt-drawer.open[aria-hidden="false"]') !== null;
    const headline = document.querySelector('.dpt-editor [name="headline"]'); if (headline) headline.value = 'Audited default page headline';
    document.querySelector('.dpt-editor')?.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    result.edited = document.querySelector('[data-dpt-template="' + firstId + '"]')?.textContent.includes('Audited default page headline') || false;
    document.querySelector('[data-dpt-template="' + firstId + '"] [data-dpt-publish]')?.click();
    result.published = document.querySelector('[data-dpt-template="' + firstId + '"] .wcp-status')?.textContent.trim() === 'Published';
    result.runtimeJob = (window.WCPRuntime?.getState().jobs.length || 0) === jobsBefore + 1;
    document.querySelector('[data-dpt-tab="branding"]')?.click();
    result.brandingFields = document.querySelectorAll('[data-dpt-brand-form] input,[data-dpt-brand-form] select').length;
    document.querySelector('[data-dpt-tab="assignments"]')?.click();
    result.rules = document.querySelectorAll('.dpt-rule').length;
    document.querySelector('[data-dpt-tab="locales"]')?.click();
    result.locales = document.querySelectorAll('.dpt-locale').length;
    document.querySelector('[data-dpt-tab="versions"]')?.click();
    result.versions = document.querySelectorAll('.dpt-version').length;
    return result;
  })()`;
  const result = await cdp.send('Runtime.evaluate', { expression, returnByValue: true });
  return result.result.value;
}

async function inspectPlatformSoftware(cdp) {
  const expression = `(() => {
    if (document.body.dataset.rootService !== 'software') return null;
    const jobsBefore = window.WCPRuntime?.getState().jobs.length || 0;
    const result = {
      metrics: document.querySelectorAll('.sw-metrics article').length,
      tabs: document.querySelectorAll('[data-sw-tab]').length,
      stages: document.querySelectorAll('.sw-stage').length,
      nodes: document.querySelectorAll('[data-sw-node]').length,
      drawer: false,
      installed: false,
      packages: 0,
      core: 0,
      ownership: 0,
      ownershipLinks: 0,
      ownershipDuplicates: -1,
      ownershipFilter: false,
      plugins: 0,
      contracts: 0,
      pluginToggled: false,
      licenses: 0,
      releases: 0,
      rollout: false,
      rollbackGate: false,
      rollback: false,
      repositories: 0,
      guardrails: 0,
      history: 0,
      persisted: false,
      runtimeJobs: false
    };
    document.querySelector('[data-sw-action="install"]')?.click();
    result.drawer = document.querySelector('.sw-drawer.open[aria-hidden="false"]') !== null;
    const component = document.querySelector('.sw-form [name="component"]'); if (component) component.value = 'audit-agent';
    document.querySelector('.sw-form')?.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    result.installed = document.querySelector('.sw-feedback.show')?.textContent.includes('audit-agent') || false;
    document.querySelector('[data-sw-tab="packages"]')?.click();
    result.packages = document.querySelectorAll('[data-sw-package]').length;
    document.querySelector('[data-sw-tab="core"]')?.click();
    result.core = document.querySelectorAll('[data-sw-core]').length;
    document.querySelector('[data-sw-tab="ownership"]')?.click();
    result.ownership = document.querySelectorAll('[data-sw-ownership]').length;
    result.ownershipLinks = document.querySelectorAll('[data-sw-ownership] .sw-write-owner[href]').length;
    result.ownershipDuplicates = Number(document.querySelector('[data-sw-ownership-duplicates]')?.textContent || -1);
    const ownerSearch = document.querySelector('[data-sw-owner-search]');
    if (ownerSearch) {
      ownerSearch.value = 'nameserver';
      ownerSearch.dispatchEvent(new Event('input', { bubbles: true }));
      const visibleOwners = [...document.querySelectorAll('[data-sw-ownership]')].filter(item => !item.hidden);
      result.ownershipFilter = visibleOwners.length === 1 && visibleOwners[0].dataset.swOwnership === 'dns';
      ownerSearch.value = '';
      ownerSearch.dispatchEvent(new Event('input', { bubbles: true }));
    }
    document.querySelector('[data-sw-tab="plugins"]')?.click();
    result.plugins = document.querySelectorAll('[data-sw-plugin]').length;
    const activate = document.querySelector('[data-sw-plugin="billing"] [data-sw-action="extension-activate"]');
    activate?.click();
    document.querySelector('.sw-form')?.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    try {
      const saved = JSON.parse(localStorage.getItem('wcp360-platform-software-v1'));
      result.pluginToggled = saved?.plugins?.find(item => item.id === 'billing')?.enabled === true;
    } catch (error) {}
    document.querySelector('[data-sw-tab="contracts"]')?.click();
    result.contracts = document.querySelectorAll('[data-sw-contract]').length;
    document.querySelector('[data-sw-tab="licenses"]')?.click();
    result.licenses = document.querySelectorAll('[data-sw-license]').length;
    document.querySelector('[data-sw-tab="releases"]')?.click();
    result.releases = document.querySelectorAll('[data-sw-release]').length;
    document.querySelector('[data-sw-release] [data-sw-action="rollout"]')?.click();
    document.querySelector('.sw-form')?.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    result.rollout = document.querySelector('[data-sw-release] .wcp-status')?.textContent.includes('Canary scheduled') || false;
    document.querySelector('[data-sw-release] [data-sw-action="rollback"]')?.click();
    const confirm = document.querySelector('[data-sw-confirm]');
    const submit = document.querySelector('[data-sw-danger-submit]');
    const disabledBefore = Boolean(submit?.disabled);
    if (confirm) { confirm.value = confirm.dataset.swConfirm; confirm.dispatchEvent(new Event('input', { bubbles: true })); }
    result.rollbackGate = disabledBefore && submit?.disabled === false;
    document.querySelector('.sw-form')?.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    result.rollback = document.querySelector('[data-sw-release] .wcp-status')?.textContent.includes('Rollback queued') || false;
    document.querySelector('[data-sw-tab="policies"]')?.click();
    result.repositories = document.querySelectorAll('.sw-repo').length;
    result.guardrails = document.querySelectorAll('[data-sw-policy-form] .sw-switch').length;
    result.history = document.querySelectorAll('[data-sw-history]').length;
    try {
      const saved = JSON.parse(localStorage.getItem('wcp360-platform-software-v1'));
      result.persisted = saved?.packages?.some(item => item.name === 'audit-agent') && saved?.releases?.some(item => item.status === 'Rollback queued');
    } catch (error) {}
    result.runtimeJobs = (window.WCPRuntime?.getState().jobs.length || 0) === jobsBefore + 4;
    document.querySelector('[data-sw-tab="overview"]')?.click();
    return result;
  })()`;
  const result = await cdp.send('Runtime.evaluate', { expression, returnByValue: true });
  return result.result.value;
}

async function inspectSystemSettings(cdp) {
  const scenario = () => {
    if (document.body.dataset.rootService !== 'settings' || !window.WCPSystemSettings) return null;
    const state = () => window.WCPSystemSettings.getState();
    const key = 'wcp360-system-settings-v1', original = localStorage.getItem(key), before = state();
    const result = {metrics:document.querySelectorAll('.ss-overview-card').length, categories:document.querySelectorAll('[data-ss-nav] [data-ss-section]').length, fields:0, search:false, dirty:false, draftOnly:false, review:false, requiredReason:false, published:false, persisted:false, versions:false, ownerLinks:false, wizardLink:!!document.querySelector('a[href="setup-wizard.html"]')};
    for (const section of window.WCPSettingsModel.sections) {
      window.WCPSystemSettings.navigate(section.id);
      result.fields += document.querySelectorAll('[data-ss-path]').length;
    }
    result.ownerLinks = document.querySelectorAll('.ss-owner-link').length > 0;
    const search = document.querySelector('[data-ss-search]');
    search.value = 'memoire'; search.dispatchEvent(new Event('input', {bubbles:true}));
    result.search = !!document.querySelector('[data-ss-path="resource.ram"]');
    window.WCPSystemSettings.navigate('resources');
    const cpu = document.querySelector('[data-ss-path="resource.cpu"]');
    const next = before.draft.resource.cpu === 64 ? 63 : before.draft.resource.cpu + 1;
    cpu.value = String(next); cpu.dispatchEvent(new Event('input', {bubbles:true}));
    result.dirty = state().dirty && document.querySelector('[data-ss-savebar]').dataset.dirty === 'true';
    result.draftOnly = localStorage.getItem(key) === original;
    document.querySelector('[data-ss-action="review"]').click();
    const dialog = document.querySelector('dialog.ss-review-dialog[open]');
    result.review = !!dialog && dialog.querySelectorAll('.ss-diff').length === state().changes.length;
    if (dialog) {
      const form = dialog.querySelector('form');
      result.requiredReason = !form.checkValidity() && localStorage.getItem(key) === original;
      form.elements.reason.value = 'Audit du nouveau centre des paramètres';
      if (form.elements.acknowledged) form.elements.acknowledged.checked = true;
      form.dispatchEvent(new Event('submit', {bubbles:true,cancelable:true}));
      result.published = state().version === before.version + 1 && !state().dirty;
      try { const saved = JSON.parse(localStorage.getItem(key)); result.persisted = saved.resource.cpu === next && saved.schemaVersion === window.WCPSettingsModel.schemaVersion; result.versions = saved.settingsHistory.length >= 2; } catch {}
    }
    window.WCPSystemSettings.navigate('server');
    return result;
  };
  const result = await cdp.send('Runtime.evaluate', {expression:'(' + scenario.toString() + ')()', returnByValue:true});
  return result.result.value;
}

async function inspectSetupWizard(cdp) {
  const expression = `(() => {
    if (document.body.dataset.rootService !== 'setup') return null;
    const jobsBefore = window.WCPRuntime?.getState().jobs.length || 0;
    const result = { expertSteps: 0, quickSteps: 0, preflight: false, expertComplete: false, quickComplete: false, persisted: false, runtimeJobs: false, reset: false, tinyText: [] };
    document.querySelector('[data-setup-mode="expert"]')?.click();
    result.expertSteps = document.querySelectorAll('.setup-step').length;
    for (let index = 0; index < 9; index += 1) document.querySelector('[data-setup-next]')?.click();
    document.querySelector('[data-setup-preflight]')?.click();
    result.preflight = document.querySelector('[data-setup-next]')?.disabled === false;
    document.querySelector('[data-setup-next]')?.click();
    result.expertComplete = document.querySelector('.setup-complete-card') !== null;
    document.querySelector('[data-setup-again]')?.click();
    result.reset = document.querySelector('[data-setup-mode="quick"]') !== null;
    document.querySelector('[data-setup-mode="quick"]')?.click();
    result.quickSteps = document.querySelectorAll('.setup-step').length;
    document.querySelector('[data-setup-next]')?.click();
    document.querySelector('[data-setup-next]')?.click();
    document.querySelector('[data-setup-preflight]')?.click();
    document.querySelector('[data-setup-next]')?.click();
    document.querySelector('[data-setup-next]')?.click();
    result.quickComplete = document.querySelector('.setup-complete-card') !== null;
    try {
      const wizard = JSON.parse(localStorage.getItem('wcp360-setup-wizard-v1'));
      const settings = JSON.parse(localStorage.getItem('wcp360-system-settings-v1'));
      result.persisted = wizard?.completed === true && wizard?.mode === 'quick' && settings?.behavior?.overselling === false && settings?.access?.mfaRoot === true && settings?.access?.sshKeys === true && settings?.providers?.length >= 4;
    } catch (error) {}
    result.runtimeJobs = (window.WCPRuntime?.getState().jobs.length || 0) === jobsBefore + 2;
    const visible = el => !!(el.offsetWidth || el.offsetHeight || el.getClientRects().length);
    result.tinyText = [...document.querySelectorAll('#setupWizard *')].filter(el => visible(el) && !el.children.length && el.textContent.trim() && !el.classList.contains('material-symbols-rounded') && parseFloat(getComputedStyle(el).fontSize) < 9.5).slice(0,10).map(el => ({ text: el.textContent.trim().slice(0,40), size: getComputedStyle(el).fontSize }));
    document.querySelector('[data-setup-again]')?.click();
    document.querySelector('[data-setup-mode="quick"]')?.click();
    document.querySelector('[data-setup-next]')?.click();
    return result;
  })()`;
  const result = await cdp.send('Runtime.evaluate', { expression, returnByValue: true });
  return result.result.value;
}

async function inspectRootLicense(cdp) {
  const expression = `(() => {
    if (document.body.dataset.rootService !== 'license') return null;
    const jobsBefore = window.WCPRuntime?.getState().jobs.length || 0;
    const result = {
      metrics: document.querySelectorAll('.lic-metrics article').length,
      tabs: document.querySelectorAll('[data-lic-tab]').length,
      lifecycleStates: document.querySelectorAll('[data-lic-simulate]').length,
      drawer: false,
      workflow: false,
      activated: false,
      runtimeJob: false,
      entitlements: 0,
      activationActions: 0,
      auditEvents: 0,
      simulated: false,
      persisted: false
    };
    document.querySelector('[data-lic-action="activate"]')?.click();
    result.drawer = document.querySelector('.lic-drawer.open[aria-hidden="false"]') !== null;
    document.querySelector('.lic-form')?.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    result.workflow = document.querySelector('.lic-feedback.show') !== null;
    result.activated = document.querySelector('.lic-metrics')?.textContent.includes('Licensed') || false;
    result.runtimeJob = (window.WCPRuntime?.getState().jobs.length || 0) === jobsBefore + 1;
    document.querySelector('[data-lic-tab="entitlements"]')?.click();
    result.entitlements = document.querySelectorAll('[data-lic-entitlement]').length;
    document.querySelector('[data-lic-tab="activation"]')?.click();
    result.activationActions = document.querySelectorAll('.lic-action-list [data-lic-action]').length;
    document.querySelector('[data-lic-tab="audit"]')?.click();
    result.auditEvents = document.querySelectorAll('.lic-audit-list article').length;
    document.querySelector('[data-lic-tab="overview"]')?.click();
    document.querySelector('[data-lic-simulate="grace"]')?.click();
    result.simulated = document.querySelector('.lic-metrics')?.textContent.includes('Grace period') || false;
    try { result.persisted = JSON.parse(localStorage.getItem('wcp360-license-center-v1'))?.license?.status === 'grace'; } catch (error) {}
    return result;
  })()`;
  const result = await cdp.send('Runtime.evaluate', { expression, returnByValue: true });
  return result.result.value;
}

async function inspectInternalDistribution(cdp, page) {
  if (page !== 'internal-distribution.html') return null;
  const expression = `(async () => {
    const wait = () => new Promise(resolve => setTimeout(resolve, 0));
    const go = async tab => {
      location.hash = tab;
      await wait();
    };
    const result = {
      metrics: document.querySelectorAll('.internal-metric').length,
      nav: document.querySelectorAll('#internalNav .wcp-nav-item').length,
      activeNav: document.querySelectorAll('#internalNav .wcp-nav-item.active[aria-current="page"]').length,
      boundary: 0,
      privateBlocked: false,
      builds: 0,
      buildCreated: false,
      buildPersisted: false,
      releases: 0,
      releasePromoted: false,
      artifacts: 0,
      revokeDrawer: false,
      revokeGate: false,
      revoked: false,
      licenses: 0,
      apps: 0,
      extensions: 0,
      extensionCreated: false,
      extensionReviewed: false,
      extensionPersisted: false,
      customers: 0,
      customerCreated: false,
      customerSuspendDrawer: false,
      customerSuspendGate: false,
      customerSuspended: false,
      customerPersisted: false,
      fleet: 0,
      security: 0,
      operators: 0,
      approvals: 0,
      sessions: 0,
      jitCreated: false,
      jitDrawer: false,
      jitGate: false,
      jitApproved: false,
      jitSessionCreated: false,
      jitPersisted: false
    };
    await go('boundary');
    result.boundary = document.querySelectorAll('[data-boundary-component]').length;
    const publicBefore = document.querySelectorAll('.internal-boundary-side.public [data-boundary-component]').length;
    document.querySelector('[data-action="toggle-component"][data-index="12"]')?.click();
    result.privateBlocked = document.querySelectorAll('.internal-boundary-side.public [data-boundary-component]').length === publicBefore
      && document.querySelector('.internal-toast')?.textContent.includes('INTERNAL');
    await go('builds');
    const buildsBefore = document.querySelectorAll('[data-build-row]').length;
    document.querySelector('[data-action="create-build"]')?.click();
    document.querySelector('[data-form-kind="create-build"]')?.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    result.builds = document.querySelectorAll('[data-build-row]').length;
    result.buildCreated = result.builds === buildsBefore + 1;
    try { result.buildPersisted = JSON.parse(localStorage.getItem('wcp360.internal.control-plane.v1'))?.builds?.length === result.builds; } catch {}
    await go('releases');
    const releasesBefore = document.querySelectorAll('[data-release-row]').length;
    document.querySelector('[data-action="promote-release"]')?.click();
    const channel = document.getElementById('releaseChannel');
    if (channel) channel.value = 'Stable';
    document.querySelector('[data-form-kind="promote-release"]')?.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    result.releases = document.querySelectorAll('[data-release-row]').length;
    result.releasePromoted = result.releases === releasesBefore + 1;
    await go('distribution');
    result.artifacts = document.querySelectorAll('[data-artifact-row]').length;
    document.querySelector('[data-action="revoke-artifact"][data-index="0"]')?.click();
    result.revokeDrawer = document.querySelector('[data-form-kind="revoke-artifact"]') !== null;
    const submit = document.getElementById('internalDrawerSubmit');
    const initiallyDisabled = submit?.disabled === true;
    const token = document.getElementById('confirmToken');
    if (token) {
      token.value = 'REVOKE';
      token.dispatchEvent(new Event('input', { bubbles: true }));
    }
    result.revokeGate = initiallyDisabled && submit?.disabled === false;
    document.querySelector('[data-form-kind="revoke-artifact"]')?.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    result.revoked = [...document.querySelectorAll('[data-artifact-row]')][0]?.textContent.includes('Revoked') || false;
    await go('licensing');
    result.licenses = document.querySelectorAll('[data-license-row]').length;
    await go('marketplace');
    result.apps = document.querySelectorAll('[data-app-row]').length;
    await go('extensions');
    const extensionsBefore = document.querySelectorAll('[data-extension-row]').length;
    document.querySelector('[data-action="submit-extension"]')?.click();
    document.querySelector('[data-form-kind="submit-extension"]')?.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    result.extensions = document.querySelectorAll('[data-extension-row]').length;
    result.extensionCreated = result.extensions === extensionsBefore + 1;
    document.querySelector('[data-action="review-extension"][data-index="0"]')?.click();
    const extensionStage = document.getElementById('extensionStage');
    if (extensionStage) extensionStage.value = 'Approved for signing';
    document.querySelector('[data-form-kind="review-extension"]')?.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    result.extensionReviewed = [...document.querySelectorAll('[data-extension-row]')][0]?.textContent.includes('Approved for signing') || false;
    try { result.extensionPersisted = JSON.parse(localStorage.getItem('wcp360.internal.control-plane.v1'))?.extensions?.length === result.extensions; } catch {}
    await go('customers');
    const customersBefore = document.querySelectorAll('[data-customer-row]').length;
    document.querySelector('[data-action="create-customer"]')?.click();
    const customerName = document.getElementById('customerName');
    if (customerName) customerName.value = 'Audit Hosting';
    document.querySelector('[data-form-kind="create-customer"]')?.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    result.customers = document.querySelectorAll('[data-customer-row]').length;
    result.customerCreated = result.customers === customersBefore + 1;
    document.querySelector('[data-action="suspend-customer"][data-index="0"]')?.click();
    result.customerSuspendDrawer = document.querySelector('[data-form-kind="suspend-customer"]') !== null;
    const customerSubmit = document.getElementById('internalDrawerSubmit');
    const customerInitiallyDisabled = customerSubmit?.disabled === true;
    const customerToken = document.getElementById('confirmToken');
    if (customerToken) {
      customerToken.value = 'SUSPEND';
      customerToken.dispatchEvent(new Event('input', { bubbles: true }));
    }
    result.customerSuspendGate = customerInitiallyDisabled && customerSubmit?.disabled === false;
    document.querySelector('[data-form-kind="suspend-customer"]')?.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    result.customerSuspended = [...document.querySelectorAll('[data-customer-row]')][0]?.textContent.includes('Suspended') || false;
    try { result.customerPersisted = JSON.parse(localStorage.getItem('wcp360.internal.control-plane.v1'))?.customers?.[0]?.subscription === 'Suspended'; } catch {}
    await go('fleet');
    result.fleet = document.querySelectorAll('[data-fleet-row]').length;
    await go('security');
    result.security = document.querySelectorAll('[data-security-control]').length;
    result.operators = document.querySelectorAll('[data-internal-operator]').length;
    const approvalsBefore = document.querySelectorAll('[data-internal-approval]').length;
    const sessionsBefore = document.querySelectorAll('[data-internal-session]').length;
    document.querySelector('[data-action="request-access"]')?.click();
    document.querySelector('[data-form-kind="request-access"]')?.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    result.approvals = document.querySelectorAll('[data-internal-approval]').length;
    result.jitCreated = result.approvals === approvalsBefore + 1;
    document.querySelector('[data-action="approve-access"][data-index="0"]')?.click();
    result.jitDrawer = document.querySelector('[data-form-kind="approve-access"]') !== null;
    const jitSubmit = document.getElementById('internalDrawerSubmit');
    const jitInitiallyDisabled = jitSubmit?.disabled === true;
    const jitToken = document.getElementById('confirmToken');
    const expectedJitToken = document.querySelector('.internal-confirm-token')?.textContent || '';
    if (jitToken) {
      jitToken.value = expectedJitToken;
      jitToken.dispatchEvent(new Event('input', { bubbles: true }));
    }
    result.jitGate = jitInitiallyDisabled && jitSubmit?.disabled === false;
    document.querySelector('[data-form-kind="approve-access"]')?.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    result.jitApproved = [...document.querySelectorAll('[data-internal-approval]')][0]?.textContent.includes('Approved') || false;
    result.sessions = document.querySelectorAll('[data-internal-session]').length;
    result.jitSessionCreated = result.sessions === sessionsBefore + 1;
    try {
      const saved = JSON.parse(localStorage.getItem('wcp360.internal.control-plane.v1'));
      result.jitPersisted = saved?.approvals?.[0]?.status === 'Approved' && saved?.sessions?.length === result.sessions;
    } catch {}
    return result;
  })()`;
  const result = await cdp.send('Runtime.evaluate', { expression, awaitPromise: true, returnByValue: true });
  return result.result.value;
}

async function inspectAuth(cdp, page) {
  if (!['login.html', 'mfa.html'].includes(page)) return null;
  const expression = `(() => {
    if (${JSON.stringify(page)} === 'login.html') {
      const portals = document.querySelectorAll('[data-portal]');
      document.querySelector('[data-portal="root"]')?.click();
      return { page: 'login', portals: portals.length, selected: document.querySelectorAll('[data-portal].active').length, root: document.querySelector('[data-portal="root"].active') !== null };
    }
    return { page: 'mfa', context: document.querySelector('.auth-context')?.textContent || '', action: document.querySelector('form')?.getAttribute('action') || '' };
  })()`;
  const result = await cdp.send('Runtime.evaluate', { expression, returnByValue: true });
  return result.result.value;
}

await new Promise((resolveListen) => server.listen(sitePort, host, resolveListen));
const chrome = spawn(chromePath, ['--headless=new', '--disable-gpu', '--no-first-run', '--no-default-browser-check', `--remote-debugging-port=${debugPort}`, `--user-data-dir=${join(tmpdir(), `wcp360-runtime-audit-${process.pid}`)}`, 'about:blank'], { stdio: 'ignore' });

const findings = [];
try {
  await waitForChrome();
  const targetResponse = await fetch(`http://${host}:${debugPort}/json/new?about:blank`, { method: 'PUT' });
  const target = await targetResponse.json();
  const cdp = new Cdp(target.webSocketDebuggerUrl);
  await Promise.all([cdp.send('Page.enable'), cdp.send('Runtime.enable')]);
  let current = '';
  cdp.on('Runtime.exceptionThrown', ({ exceptionDetails }) => findings.push({ severity: 'error', page: current, mode: 'runtime', detail: exceptionDetails.exception?.description || exceptionDetails.text }));

  if (!integrationOnly) for (const mode of [{ name: 'desktop', width: 1440, height: 1000 }, { name: 'mobile', width: 390, height: 844 }]) {
    await cdp.send('Emulation.setDeviceMetricsOverride', { width: mode.width, height: mode.height, deviceScaleFactor: 1, mobile: false });
    for (const page of pages) {
      current = page;
      await navigate(cdp, `http://${host}:${sitePort}/${page}`);
      const result = await inspect(cdp);
      const standaloneSetup = page === 'setup-wizard.html';
      if (result.overflow > 1) findings.push({ severity: 'warning', page, mode: mode.name, rule: 'horizontal-overflow', detail: `${result.overflow}px` });
      if (result.rootOverflow > 1) findings.push({ severity: 'error', page, mode: mode.name, rule: 'root-content-overflow', detail: `${result.rootOverflow}px` });
      if (result.unnamed) findings.push({ severity: 'error', page, mode: mode.name, rule: 'unnamed-control', detail: result.unnamed });
      if (result.duplicateLegacyCommand) findings.push({ severity: 'error', page, mode: mode.name, rule: 'duplicate-command-center' });
      if (result.hashLinks) findings.push({ severity: 'warning', page, mode: mode.name, rule: 'hash-link', detail: result.hashLinks });
      if (!result.runtimeReady) findings.push({ severity: 'error', page, mode: mode.name, rule: 'product-runtime' });
      if (!result.experienceReady) findings.push({ severity: 'error', page, mode: mode.name, rule: 'experience-runtime' });
      if (!standaloneSetup && result.scope && ['user','reseller','root'].includes(result.scope) && result.contextBars !== 1) findings.push({ severity: 'error', page, mode: mode.name, rule: 'workspace-context', detail: result.contextBars });
      if (!standaloneSetup && result.scope && ['user','reseller','root'].includes(result.scope) && result.commandResults < 10) findings.push({ severity: 'error', page, mode: mode.name, rule: 'global-command-index', detail: result.commandResults });
      if (!standaloneSetup && result.scope && result.activeNav !== 1) findings.push({ severity: 'warning', page, mode: mode.name, rule: 'active-navigation', detail: result.activeNav });
      const rootOnly = ['admin-mail.html','canonical-admin-dashboard.html','admin-dashboard.html','servers.html','server-overview.html','cluster.html','firewall.html','incidents.html','jobs.html','approvals.html','approval-detail.html','admin-backups.html','users-roles.html','roles.html','plans.html','feature-lists.html','effective-permissions.html','demo-accounts.html','resellers-access.html','quotas.html','usage-metering.html','admin-security.html','updates.html','audit.html','terminal.html','hosting-accounts.html','account-operations.html','provisioning-blueprints.html','lifecycle-hooks.html','web-default-pages.html','admin-dns.html','dns-zone-templates.html','dns-cluster.html','ip-management.html','database-services.html','web-runtime.html','language-runtimes.html','runtime-modules.html','web-stack-builder.html','file-transfer-services.html','service-manager.html','service-config-schemas.html','resource-diagnostics.html','server-profiles.html','certificates.html','transfers.html','transfer-importer.html','bulk-restore.html','server-configuration.html','webmail-services.html','mail-server-config.html','mail-queue.html','software-manager.html','extension-platform.html','scheduled-tasks.html','platform-readiness.html','setup-wizard.html','api-automation.html','logs-diagnostics.html','application-catalog.html','license-management.html'];
      const userOnly = ['canonical-user-dashboard.html','user-dashboard.html','websites.html','website-overview.html','canonical-website-overview.html','domains.html','email.html','mail-rules.html','webmail.html','app-installer.html','databases.html','file-manager.html','file-access-center.html','backups.html','cron.html','ssl.html','deployments.html','git-repositories.html','site-analytics.html','web-compatibility.html','app-health.html','monitoring.html','canonical-security.html','security.html','security-auth.html','security-keys.html','security-network.html','security-sessions.html','security-tokens.html','security-waf.html','api-webhooks.html','profile-account-settings.html','help-support.html'];
      const resellerOnly = ['reseller.html','reseller-governance.html'];
      const forbidden = result.scope === 'user' ? [...rootOnly,...resellerOnly] : result.scope === 'root' ? [...userOnly,...resellerOnly] : result.scope === 'reseller' ? [...rootOnly,...userOnly] : [];
      const mixed = [...result.sidebarLinks,...result.commandLinks].filter(link => forbidden.includes(link.split('#')[0]));
      if (mixed.length) findings.push({ severity: 'error', page, mode: mode.name, rule: 'mixed-access-scope', detail: [...new Set(mixed)] });
      const expectedHeadings = {
        user: ['Workspace','Services','Operations','Account'],
        reseller: ['Workspace','Delegation','Operations'],
        root: ['Command','Hosting','Infrastructure','Network & DNS','Web & Data','Continuity','Security','System'],
        design: ['Design System']
      }[result.scope];
      if (!standaloneSetup && expectedHeadings && JSON.stringify(result.sidebarHeadings) !== JSON.stringify(expectedHeadings)) findings.push({ severity: 'error', page, mode: mode.name, rule: 'product-navigation', detail: { expected: expectedHeadings, actual: result.sidebarHeadings } });
      const expectedGroups = { user: 4, reseller: 3, root: 8 }[result.scope];
      if (!standaloneSetup && expectedGroups && (result.sidebarGroups !== expectedGroups || result.expandedSidebarGroups !== 1 || !result.sidebarSearch)) findings.push({ severity: 'error', page, mode: mode.name, rule: 'lightweight-navigation', detail: { groups: result.sidebarGroups, expectedGroups, expanded: result.expandedSidebarGroups, search: result.sidebarSearch } });
      if (!standaloneSetup && result.scope && (result.scopeBadges.length !== 1 || result.scopeBadges[0] !== result.scope.toUpperCase())) findings.push({ severity: 'error', page, mode: mode.name, rule: 'product-identity', detail: result.scopeBadges });
      if (result.scopeSwitches) findings.push({ severity: 'error', page, mode: mode.name, rule: 'cross-product-switch', detail: result.scopeSwitches });
      if (!standaloneSetup && result.scope && ['user','reseller','root'].includes(result.scope) && (result.profilePortalSwitches !== 3 || result.profilePortalCurrent !== result.scope || result.sidebarPortalSwitches !== 0 || !result.prototypePortalLabel)) findings.push({ severity: 'error', page, mode: mode.name, rule: 'prototype-profile-portal-switch', detail: { switches: result.profilePortalSwitches, current: result.profilePortalCurrent, scope: result.scope, sidebar: result.sidebarPortalSwitches, prototypeLabel: result.prototypePortalLabel } });
      if (result.rootChromeLeaks.length) findings.push({ severity: 'error', page, mode: mode.name, rule: 'mixed-root-command-ui', detail: result.rootChromeLeaks });
      if (['hosting-accounts.html','web-default-pages.html','websites.html','software-manager.html','system-settings.html','users-roles.html','web-runtime.html','admin-mail.html','certificates.html','admin-dns.html','internal-distribution.html'].includes(page) && result.tinyText.length) findings.push({ severity: 'error', page, mode: mode.name, rule: 'tiny-text', detail: result.tinyText });
      if (mode.name === 'desktop') {
        const identity = await inspectIdentity(cdp, page);
        if (identity && (identity.metrics !== 4 || identity.tabs !== 8 || identity.sections !== 8 || !identity.tabsInteractive || !identity.drawer || !identity.workflow || identity.userCreated === false)) findings.push({ severity: 'error', page, mode: mode.name, rule: 'identity-workflow', detail: identity });
        const resellerCenter = await inspectResellerCenter(cdp);
        if (resellerCenter && (resellerCenter.metrics !== 4 || resellerCenter.tabs !== 10 || resellerCenter.boundary !== 3 || !resellerCenter.drawer || !resellerCenter.tabWorkflow || !resellerCenter.created || !resellerCenter.servicePolicy)) findings.push({ severity: 'error', page, mode: mode.name, rule: 'reseller-center-workflow', detail: resellerCenter });
        const runtimeCenter = await inspectRuntimeCenter(cdp, page);
        if (runtimeCenter && (runtimeCenter.metrics !== 4 || runtimeCenter.tabs !== 9 || runtimeCenter.boundary !== 3 || !runtimeCenter.drawer || !runtimeCenter.tabWorkflow || runtimeCenter.versions < 4 || runtimeCenter.modules !== 10 || runtimeCenter.profiles < 5 || runtimeCenter.assignments !== 5 || !runtimeCenter.installed || !runtimeCenter.profileCreated || !runtimeCenter.modulePolicy || !runtimeCenter.runtimeJobs)) findings.push({ severity: 'error', page, mode: mode.name, rule: 'runtime-center-workflow', detail: runtimeCenter });
        const mailInfrastructure = await inspectMailInfrastructure(cdp, page);
        if (mailInfrastructure && (mailInfrastructure.metrics !== 4 || mailInfrastructure.tabs !== 9 || mailInfrastructure.boundary !== 3 || !mailInfrastructure.drawer || mailInfrastructure.nodes !== 5 || mailInfrastructure.profiles < 3 || mailInfrastructure.queue < 5 || !mailInfrastructure.tabWorkflow || !mailInfrastructure.profileCreated || !mailInfrastructure.removeDrawer || !mailInfrastructure.removeGate || !mailInfrastructure.removed || !mailInfrastructure.runtimeJobs)) findings.push({ severity: 'error', page, mode: mode.name, rule: 'mail-infrastructure-workflow', detail: mailInfrastructure });
        const certificateCenter = await inspectCertificateCenter(cdp, page);
        if (certificateCenter && (certificateCenter.metrics !== 4 || certificateCenter.tabs !== 9 || certificateCenter.boundary !== 3 || !certificateCenter.drawer || certificateCenter.coverage !== 4 || certificateCenter.certificates < 6 || certificateCenter.services !== 7 || certificateCenter.authorities !== 4 || certificateCenter.validation !== 4 || certificateCenter.renewals !== 5 || !certificateCenter.tabWorkflow || !certificateCenter.issued || !certificateCenter.revokeDrawer || !certificateCenter.revokeGate || !certificateCenter.revoked || !certificateCenter.runtimeJobs)) findings.push({ severity: 'error', page, mode: mode.name, rule: 'certificate-center-workflow', detail: certificateCenter });
        const applicationCatalog = await inspectApplicationCatalog(cdp, page);
        if (applicationCatalog && (applicationCatalog.metrics !== 4 || applicationCatalog.tabs !== 9 || applicationCatalog.boundary !== 3 || !applicationCatalog.drawer || !applicationCatalog.tabWorkflow || applicationCatalog.apps < 10 || applicationCatalog.sources < 4 || applicationCatalog.templates < 6 || applicationCatalog.updates < 5 || applicationCatalog.policies !== 8 || applicationCatalog.installations < 6 || applicationCatalog.licenses < 4 || applicationCatalog.history < 8 || !applicationCatalog.appCreated || !applicationCatalog.togglePersisted || !applicationCatalog.runtimeJobs)) findings.push({ severity: 'error', page, mode: mode.name, rule: 'application-catalog-workflow', detail: applicationCatalog });
        const fileTransferCenter = await inspectFileTransferCenter(cdp, page);
        if (fileTransferCenter && (fileTransferCenter.metrics !== 4 || fileTransferCenter.tabs !== 9 || fileTransferCenter.boundary !== 3 || !fileTransferCenter.drawer || !fileTransferCenter.tabWorkflow || fileTransferCenter.endpoints < 5 || fileTransferCenter.protocols !== 4 || !fileTransferCenter.plainFtpLocked || fileTransferCenter.profiles < 6 || fileTransferCenter.policies !== 12 || fileTransferCenter.sessions < 6 || fileTransferCenter.network < 5 || fileTransferCenter.security < 6 || fileTransferCenter.bans < 5 || fileTransferCenter.history < 8 || !fileTransferCenter.endpointCreated || !fileTransferCenter.togglePersisted || !fileTransferCenter.disconnectDrawer || !fileTransferCenter.disconnectGate || !fileTransferCenter.disconnected || !fileTransferCenter.runtimeJobs)) findings.push({ severity: 'error', page, mode: mode.name, rule: 'file-transfer-center-workflow', detail: fileTransferCenter });
        const dnsCenter = await inspectDnsCenter(cdp, page);
        if (dnsCenter && (dnsCenter.metrics !== 4 || dnsCenter.tabs !== 9 || dnsCenter.boundary !== 3 || !dnsCenter.drawer || !dnsCenter.tabWorkflow || dnsCenter.zones < 6 || dnsCenter.templates < 6 || dnsCenter.nodes < 6 || dnsCenter.nameservers < 4 || dnsCenter.dnssec < 4 || dnsCenter.providers < 4 || dnsCenter.policies !== 10 || dnsCenter.recordTypes !== 9 || dnsCenter.history < 8 || !dnsCenter.zoneCreated || !dnsCenter.providerPersisted || !dnsCenter.nameserverConfigured || !dnsCenter.deleteDrawer || !dnsCenter.deleteGate || !dnsCenter.deleted || !dnsCenter.runtimeJobs)) findings.push({ severity: 'error', page, mode: mode.name, rule: 'dns-center-workflow', detail: dnsCenter });
        const rootControl = await inspectRootControl(cdp);
        if (rootControl && (rootControl.metrics !== 4 || rootControl.rows < 4 || rootControl.checks !== 3 || rootControl.tabs !== 4 || rootControl.activity !== 3 || !rootControl.tabWorkflow || !rootControl.drawer || !rootControl.workflow || !rootControl.runtimeJob)) findings.push({ severity: 'error', page, mode: mode.name, rule: 'root-control-workflow', detail: rootControl });
        const webRouting = await inspectWebRouting(cdp, page);
        if (webRouting && (webRouting.metrics !== 4 || webRouting.capabilities !== 8 || webRouting.tabs !== 5 || webRouting.rows !== 4 || webRouting.policies !== 10 || !webRouting.tabWorkflow || !webRouting.drawer || !webRouting.workflow || !webRouting.runtimeJob)) findings.push({ severity: 'error', page, mode: mode.name, rule: 'web-routing-workflow', detail: webRouting });
        const experience = await inspectExperience(cdp, page);
        if (experience && (experience.context !== 1 || experience.commands < 10 || !experience.pinned || !experience.control || !experience.review || !experience.change || !experience.job || !experience.jobDetail || !experience.cancelled)) findings.push({ severity: 'error', page, mode: mode.name, rule: 'consolidated-experience-workflow', detail: experience });
        const rootWebmail = await inspectRootWebmail(cdp);
        if (rootWebmail && (rootWebmail.metrics !== 4 || rootWebmail.providers !== 3 || rootWebmail.providerSwitches !== 3 || rootWebmail.policies < 9 || !rootWebmail.workflow || !rootWebmail.runtimeJob || !rootWebmail.persisted)) findings.push({ severity: 'error', page, mode: mode.name, rule: 'root-webmail-workflow', detail: rootWebmail });
        const rootDatabase = await inspectRootDatabase(cdp);
        if (rootDatabase && (rootDatabase.metrics !== 4 || rootDatabase.providers !== 3 || rootDatabase.providerSwitches !== 3 || rootDatabase.policies < 9 || rootDatabase.engines !== 2 || !rootDatabase.workflow || !rootDatabase.runtimeJob || !rootDatabase.persisted)) findings.push({ severity: 'error', page, mode: mode.name, rule: 'root-database-workflow', detail: rootDatabase });
        const rootCapacity = await inspectRootCapacity(cdp);
        if (rootCapacity && (rootCapacity.metrics !== 4 || rootCapacity.tabs !== 5 || rootCapacity.accounts < 4 || rootCapacity.emails < 4 || rootCapacity.resourcePills < 20 || rootCapacity.suspendActions < 4 || rootCapacity.terminateActions < 4 || !rootCapacity.templatesLink || !rootCapacity.drawer || !rootCapacity.workflow || !rootCapacity.created || !rootCapacity.createdEmail || !rootCapacity.suspended || !rootCapacity.suspendTemplate || !rootCapacity.terminateGate || !rootCapacity.terminated || !rootCapacity.terminateTemplate || !rootCapacity.runtimeJob || rootCapacity.placementCards < 4 || rootCapacity.addons !== 8 || rootCapacity.servers < 4)) findings.push({ severity: 'error', page, mode: mode.name, rule: 'root-capacity-workflow', detail: rootCapacity });
        const rootDefaultPages = await inspectRootDefaultPages(cdp);
        if (rootDefaultPages && (rootDefaultPages.metrics !== 4 || rootDefaultPages.tabs !== 5 || rootDefaultPages.templates < 24 || rootDefaultPages.categories !== 5 || !rootDefaultPages.preview || !rootDefaultPages.mobilePreview || !rootDefaultPages.drawer || !rootDefaultPages.edited || !rootDefaultPages.published || !rootDefaultPages.runtimeJob || rootDefaultPages.brandingFields < 8 || rootDefaultPages.rules < 5 || rootDefaultPages.locales !== 4 || rootDefaultPages.versions < 8)) findings.push({ severity: 'error', page, mode: mode.name, rule: 'root-default-pages-workflow', detail: rootDefaultPages });
        const platformSoftware = await inspectPlatformSoftware(cdp);
        if (platformSoftware && (platformSoftware.metrics !== 4 || platformSoftware.tabs !== 9 || platformSoftware.stages !== 5 || platformSoftware.nodes < 6 || !platformSoftware.drawer || !platformSoftware.installed || platformSoftware.packages < 9 || platformSoftware.core !== 12 || platformSoftware.ownership !== 27 || platformSoftware.ownershipLinks !== 27 || platformSoftware.ownershipDuplicates !== 0 || !platformSoftware.ownershipFilter || platformSoftware.plugins < 10 || platformSoftware.contracts !== 6 || !platformSoftware.pluginToggled || platformSoftware.licenses !== 4 || platformSoftware.releases !== 4 || !platformSoftware.rollout || !platformSoftware.rollbackGate || !platformSoftware.rollback || platformSoftware.repositories !== 3 || platformSoftware.guardrails !== 5 || platformSoftware.history < 9 || !platformSoftware.persisted || !platformSoftware.runtimeJobs)) findings.push({ severity: 'error', page, mode: mode.name, rule: 'platform-software-workflow', detail: platformSoftware });
        const internalDistribution = await inspectInternalDistribution(cdp, page);
        if (internalDistribution && (internalDistribution.metrics !== 4 || internalDistribution.nav !== 11 || internalDistribution.activeNav !== 1 || internalDistribution.boundary !== 18 || !internalDistribution.privateBlocked || internalDistribution.builds < 6 || !internalDistribution.buildCreated || !internalDistribution.buildPersisted || internalDistribution.releases < 5 || !internalDistribution.releasePromoted || internalDistribution.artifacts !== 5 || !internalDistribution.revokeDrawer || !internalDistribution.revokeGate || !internalDistribution.revoked || internalDistribution.licenses !== 4 || internalDistribution.apps !== 6 || internalDistribution.extensions < 7 || !internalDistribution.extensionCreated || !internalDistribution.extensionReviewed || !internalDistribution.extensionPersisted || internalDistribution.customers < 7 || !internalDistribution.customerCreated || !internalDistribution.customerSuspendDrawer || !internalDistribution.customerSuspendGate || !internalDistribution.customerSuspended || !internalDistribution.customerPersisted || internalDistribution.fleet !== 5 || internalDistribution.security !== 8 || internalDistribution.operators !== 5 || internalDistribution.approvals < 5 || internalDistribution.sessions < 5 || !internalDistribution.jitCreated || !internalDistribution.jitDrawer || !internalDistribution.jitGate || !internalDistribution.jitApproved || !internalDistribution.jitSessionCreated || !internalDistribution.jitPersisted)) findings.push({ severity: 'error', page, mode: mode.name, rule: 'internal-distribution-workflow', detail: internalDistribution });
        const setupWizard = await inspectSetupWizard(cdp);
        if (setupWizard && (setupWizard.expertSteps !== 10 || setupWizard.quickSteps !== 4 || !setupWizard.preflight || !setupWizard.expertComplete || !setupWizard.quickComplete || !setupWizard.persisted || !setupWizard.runtimeJobs || !setupWizard.reset || setupWizard.tinyText.length)) findings.push({ severity: 'error', page, mode: mode.name, rule: 'initial-setup-workflow', detail: setupWizard });
        const systemSettings = await inspectSystemSettings(cdp);
        if (page === 'system-settings.html' && (!systemSettings || systemSettings.metrics !== 4 || systemSettings.categories !== 26 || systemSettings.fields !== 248 || !systemSettings.search || !systemSettings.dirty || !systemSettings.draftOnly || !systemSettings.review || !systemSettings.requiredReason || !systemSettings.published || !systemSettings.persisted || !systemSettings.versions || !systemSettings.ownerLinks || !systemSettings.wizardLink)) findings.push({severity:'error',page,mode:mode.name,rule:'system-settings-workflow',detail:systemSettings});
        const rootLicense = await inspectRootLicense(cdp);
        if (rootLicense && (rootLicense.metrics !== 4 || rootLicense.tabs !== 5 || rootLicense.lifecycleStates !== 4 || !rootLicense.drawer || !rootLicense.workflow || !rootLicense.activated || !rootLicense.runtimeJob || rootLicense.entitlements !== 8 || rootLicense.activationActions < 4 || rootLicense.auditEvents < 5 || !rootLicense.simulated || !rootLicense.persisted)) findings.push({ severity: 'error', page, mode: mode.name, rule: 'root-license-workflow', detail: rootLicense });
        const userService = await inspectUserService(cdp);
        if (userService && (userService.metrics !== 4 || userService.items < (userService.service === 'webmail' ? 2 : 5) || !userService.drawer || !userService.workflow || !userService.runtimeEvent)) findings.push({ severity: 'error', page, mode: mode.name, rule: 'user-service-workflow', detail: userService });
        const userWorkspace = await inspectUserWorkspace(cdp);
        if (userWorkspace && (userWorkspace.metrics !== 4 || userWorkspace.tabs !== 5 || userWorkspace.items < (userWorkspace.workspace === 'databases' ? 3 : 4) || !userWorkspace.drawer || !userWorkspace.workflow || !userWorkspace.created || !userWorkspace.runtimeJob || (userWorkspace.workspace === 'websites' && (!userWorkspace.rootSeparation || userWorkspace.runtimeCards < 4 || userWorkspace.domains < 4 || userWorkspace.access < 3 || userWorkspace.policies < 6 || !userWorkspace.deleteDrawer || !userWorkspace.deleteGate || !userWorkspace.deleted)) || (userWorkspace.workspace === 'files' && (!userWorkspace.accessView || !userWorkspace.deleteDrawer || !userWorkspace.deleteGate || !userWorkspace.deleted)) || (userWorkspace.workspace === 'mail' && (userWorkspace.webmailClients < 2 || userWorkspace.securityCards !== 3)) || (userWorkspace.workspace === 'databases' && (userWorkspace.users < 3 || userWorkspace.remoteHosts < 2 || userWorkspace.tools < 2 || !userWorkspace.toolShortcut || !userWorkspace.rootGovernance || userWorkspace.connectionsVisible || userWorkspace.capacityControls)))) findings.push({ severity: 'error', page, mode: mode.name, rule: 'user-workspace-workflow', detail: userWorkspace });
        const auth = await inspectAuth(cdp, page);
        if (auth && ((auth.page === 'login' && (auth.portals !== 3 || auth.selected !== 1 || !auth.root)) || (auth.page === 'mfa' && (!auth.context.includes('WCP360 ROOT') || !auth.action.includes('canonical-admin-dashboard.html'))))) findings.push({ severity: 'error', page, mode: mode.name, rule: 'auth-product-routing', detail: auth });
      }
    }
  }
  await cdp.send('Emulation.setDeviceMetricsOverride', { width: 1440, height: 1000, deviceScaleFactor: 1, mobile: false });
  await navigate(cdp, `http://${host}:${sitePort}/webmail-services.html`);
  const enabledFromRoot = await cdp.send('Runtime.evaluate', { expression: `(() => { const card=document.querySelector('[data-provider="sogo"]'); const toggle=card?.querySelector('[data-provider-enabled]'); if(!toggle)return false; toggle.checked=true; toggle.dispatchEvent(new Event('change',{bubbles:true})); document.querySelector('[data-save-webmail]')?.click(); return !document.querySelector('.root-feedback.error'); })()`, returnByValue: true });
  await navigate(cdp, `http://${host}:${sitePort}/email.html`);
  const visibleForUser = await cdp.send('Runtime.evaluate', { expression: `(() => { document.querySelector('[data-mail-tab="webmail"]')?.click(); return document.querySelector('[data-mail-webmail="sogo"]') !== null && document.querySelectorAll('[data-mail-webmail]').length === 3; })()`, returnByValue: true });
  await navigate(cdp, `http://${host}:${sitePort}/webmail-services.html`);
  const disabledFromRoot = await cdp.send('Runtime.evaluate', { expression: `(() => { const card=document.querySelector('[data-provider="sogo"]'); const toggle=card?.querySelector('[data-provider-enabled]'); if(!toggle)return false; toggle.checked=false; toggle.dispatchEvent(new Event('change',{bubbles:true})); document.querySelector('[data-save-webmail]')?.click(); return !document.querySelector('.root-feedback.error'); })()`, returnByValue: true });
  await navigate(cdp, `http://${host}:${sitePort}/email.html`);
  const hiddenForUser = await cdp.send('Runtime.evaluate', { expression: `(() => { document.querySelector('[data-mail-tab="webmail"]')?.click(); return document.querySelector('[data-mail-webmail="sogo"]') === null && document.querySelectorAll('[data-mail-webmail]').length === 2; })()`, returnByValue: true });
  if (!enabledFromRoot.result.value || !visibleForUser.result.value || !disabledFromRoot.result.value || !hiddenForUser.result.value) findings.push({ severity: 'error', page: 'webmail-services.html → webmail.html', mode: 'integration', rule: 'root-user-webmail-policy', detail: { enabledFromRoot: enabledFromRoot.result.value, visibleForUser: visibleForUser.result.value, disabledFromRoot: disabledFromRoot.result.value, hiddenForUser: hiddenForUser.result.value } });
  await navigate(cdp, `http://${host}:${sitePort}/database-services.html`);
  const dbToolEnabledFromRoot = await cdp.send('Runtime.evaluate', { expression: `(() => { const card=document.querySelector('[data-db-tool="adminer"]'); const toggle=card?.querySelector('[data-tool-enabled]'); if(!toggle)return false; toggle.checked=true; toggle.dispatchEvent(new Event('change',{bubbles:true})); document.querySelector('[data-save-db-tools]')?.click(); return !document.querySelector('.root-feedback.error'); })()`, returnByValue: true });
  await navigate(cdp, `http://${host}:${sitePort}/databases.html`);
  const dbToolVisibleForUser = await cdp.send('Runtime.evaluate', { expression: `(() => { document.querySelector('[data-db-tab="tools"]')?.click(); return document.querySelector('[data-user-db-tool="adminer"]') !== null && document.querySelectorAll('[data-user-db-tool]').length === 3; })()`, returnByValue: true });
  await navigate(cdp, `http://${host}:${sitePort}/database-services.html`);
  const dbToolDisabledFromRoot = await cdp.send('Runtime.evaluate', { expression: `(() => { const card=document.querySelector('[data-db-tool="adminer"]'); const toggle=card?.querySelector('[data-tool-enabled]'); if(!toggle)return false; toggle.checked=false; toggle.dispatchEvent(new Event('change',{bubbles:true})); document.querySelector('[data-save-db-tools]')?.click(); return !document.querySelector('.root-feedback.error'); })()`, returnByValue: true });
  await navigate(cdp, `http://${host}:${sitePort}/databases.html`);
  const dbToolHiddenForUser = await cdp.send('Runtime.evaluate', { expression: `(() => { document.querySelector('[data-db-tab="tools"]')?.click(); return document.querySelector('[data-user-db-tool="adminer"]') === null && document.querySelectorAll('[data-user-db-tool]').length === 2; })()`, returnByValue: true });
  if (!dbToolEnabledFromRoot.result.value || !dbToolVisibleForUser.result.value || !dbToolDisabledFromRoot.result.value || !dbToolHiddenForUser.result.value) findings.push({ severity: 'error', page: 'database-services.html → databases.html', mode: 'integration', rule: 'root-user-database-tool-policy', detail: { enabledFromRoot: dbToolEnabledFromRoot.result.value, visibleForUser: dbToolVisibleForUser.result.value, disabledFromRoot: dbToolDisabledFromRoot.result.value, hiddenForUser: dbToolHiddenForUser.result.value } });
  await navigate(cdp, `http://${host}:${sitePort}/application-catalog.html`);
  const appEnabledFromRoot = await cdp.send('Runtime.evaluate', { expression: `(() => { document.querySelector('[data-cat-tab="catalog"]')?.click(); const toggle=document.querySelector('[data-cat-app-toggle="ghost"]'); if(!toggle)return false; if(!toggle.checked)toggle.click(); const saved=JSON.parse(localStorage.getItem('wcp360-application-catalog-v1')||'{}'); return saved.apps?.find(app=>app.id==='ghost')?.enabled===true; })()`, returnByValue: true });
  await navigate(cdp, `http://${host}:${sitePort}/app-installer.html`);
  const appVisibleForUser = await cdp.send('Runtime.evaluate', { expression: `document.querySelector('[data-user-app="ghost"]') !== null`, returnByValue: true });
  await navigate(cdp, `http://${host}:${sitePort}/application-catalog.html`);
  const appDisabledFromRoot = await cdp.send('Runtime.evaluate', { expression: `(() => { document.querySelector('[data-cat-tab="catalog"]')?.click(); const toggle=document.querySelector('[data-cat-app-toggle="ghost"]'); if(!toggle)return false; if(toggle.checked)toggle.click(); const saved=JSON.parse(localStorage.getItem('wcp360-application-catalog-v1')||'{}'); return saved.apps?.find(app=>app.id==='ghost')?.enabled===false; })()`, returnByValue: true });
  await navigate(cdp, `http://${host}:${sitePort}/app-installer.html`);
  const appHiddenForUser = await cdp.send('Runtime.evaluate', { expression: `document.querySelector('[data-user-app="ghost"]') === null && document.querySelectorAll('[data-user-app]').length >= 5`, returnByValue: true });
  if (!appEnabledFromRoot.result.value || !appVisibleForUser.result.value || !appDisabledFromRoot.result.value || !appHiddenForUser.result.value) findings.push({ severity: 'error', page: 'application-catalog.html → app-installer.html', mode: 'integration', rule: 'root-user-application-catalog-policy', detail: { appEnabledFromRoot: appEnabledFromRoot.result.value, appVisibleForUser: appVisibleForUser.result.value, appDisabledFromRoot: appDisabledFromRoot.result.value, appHiddenForUser: appHiddenForUser.result.value } });
  await navigate(cdp, `http://${host}:${sitePort}/file-transfer-services.html`);
  const webdavEnabledFromRoot = await cdp.send('Runtime.evaluate', { expression: `(() => { document.querySelector('[data-ftc-tab="protocols"]')?.click(); const toggle=document.querySelector('[data-ftc-protocol-toggle="webdav"]'); if(!toggle)return false; if(!toggle.checked)toggle.click(); const saved=JSON.parse(localStorage.getItem('wcp360-file-transfer-center-v1')||'{}'); return saved.protocols?.find(protocol=>protocol.id==='webdav')?.enabled===true; })()`, returnByValue: true });
  await navigate(cdp, `http://${host}:${sitePort}/file-access-center.html`);
  const webdavVisibleForUser = await cdp.send('Runtime.evaluate', { expression: `document.querySelector('[data-user-transfer-protocol="webdav"]') !== null && [...document.querySelectorAll('.parity-form select option')].some(option=>option.textContent==='WebDAV')`, returnByValue: true });
  await navigate(cdp, `http://${host}:${sitePort}/file-transfer-services.html`);
  const webdavDisabledFromRoot = await cdp.send('Runtime.evaluate', { expression: `(() => { document.querySelector('[data-ftc-tab="protocols"]')?.click(); const toggle=document.querySelector('[data-ftc-protocol-toggle="webdav"]'); if(!toggle)return false; if(toggle.checked)toggle.click(); const saved=JSON.parse(localStorage.getItem('wcp360-file-transfer-center-v1')||'{}'); return saved.protocols?.find(protocol=>protocol.id==='webdav')?.enabled===false; })()`, returnByValue: true });
  await navigate(cdp, `http://${host}:${sitePort}/file-access-center.html`);
  const webdavHiddenForUser = await cdp.send('Runtime.evaluate', { expression: `document.querySelector('[data-user-transfer-protocol="webdav"]') === null && ![...document.querySelectorAll('.parity-form select option')].some(option=>option.textContent==='WebDAV') && document.querySelector('[data-user-transfer-protocol="sftp"]') !== null`, returnByValue: true });
  if (!webdavEnabledFromRoot.result.value || !webdavVisibleForUser.result.value || !webdavDisabledFromRoot.result.value || !webdavHiddenForUser.result.value) findings.push({ severity: 'error', page: 'file-transfer-services.html → file-access-center.html', mode: 'integration', rule: 'root-user-file-transfer-policy', detail: { webdavEnabledFromRoot: webdavEnabledFromRoot.result.value, webdavVisibleForUser: webdavVisibleForUser.result.value, webdavDisabledFromRoot: webdavDisabledFromRoot.result.value, webdavHiddenForUser: webdavHiddenForUser.result.value } });
  await navigate(cdp, `http://${host}:${sitePort}/admin-dns.html`);
  const dnsTypeEnabledFromRoot = await cdp.send('Runtime.evaluate', { expression: `(() => { document.querySelector('[data-dnc-tab="policies"]')?.click(); const toggle=document.querySelector('[data-dnc-record-toggle="TLSA"]'); if(!toggle)return false; if(!toggle.checked)toggle.click(); const saved=JSON.parse(localStorage.getItem('wcp360-dns-center-v1')||'{}'); return saved.recordTypes?.find(type=>type.id==='TLSA')?.enabled===true; })()`, returnByValue: true });
  await navigate(cdp, `http://${host}:${sitePort}/dns-record-add.html`);
  const dnsTypeVisibleForUser = await cdp.send('Runtime.evaluate', { expression: `document.querySelector('select[name="type"] option[value="TLSA"]') !== null`, returnByValue: true });
  await navigate(cdp, `http://${host}:${sitePort}/admin-dns.html`);
  const dnsTypeDisabledFromRoot = await cdp.send('Runtime.evaluate', { expression: `(() => { document.querySelector('[data-dnc-tab="policies"]')?.click(); const toggle=document.querySelector('[data-dnc-record-toggle="TLSA"]'); if(!toggle)return false; if(toggle.checked)toggle.click(); const saved=JSON.parse(localStorage.getItem('wcp360-dns-center-v1')||'{}'); return saved.recordTypes?.find(type=>type.id==='TLSA')?.enabled===false; })()`, returnByValue: true });
  await navigate(cdp, `http://${host}:${sitePort}/dns-record-add.html`);
  const dnsTypeHiddenForUser = await cdp.send('Runtime.evaluate', { expression: `document.querySelector('select[name="type"] option[value="TLSA"]') === null && document.querySelector('select[name="type"] option[value="A"]') !== null`, returnByValue: true });
  if (!dnsTypeEnabledFromRoot.result.value || !dnsTypeVisibleForUser.result.value || !dnsTypeDisabledFromRoot.result.value || !dnsTypeHiddenForUser.result.value) findings.push({ severity: 'error', page: 'admin-dns.html → dns-record-add.html', mode: 'integration', rule: 'root-user-dns-record-policy', detail: { dnsTypeEnabledFromRoot: dnsTypeEnabledFromRoot.result.value, dnsTypeVisibleForUser: dnsTypeVisibleForUser.result.value, dnsTypeDisabledFromRoot: dnsTypeDisabledFromRoot.result.value, dnsTypeHiddenForUser: dnsTypeHiddenForUser.result.value } });
  await navigate(cdp, `http://${host}:${sitePort}/software-manager.html`);
  const appExtensionDisabled = await cdp.send('Runtime.evaluate', { expression: `(() => { document.querySelector('[data-sw-tab="plugins"]')?.click(); const action=document.querySelector('[data-sw-plugin="app-installer"] [data-sw-action="extension-disable"]'); if(!action)return false; action.click(); document.querySelector('.sw-form')?.dispatchEvent(new Event('submit',{bubbles:true,cancelable:true})); const saved=JSON.parse(localStorage.getItem('wcp360-platform-software-v1')||'{}'); const item=saved.plugins?.find(entry=>entry.id==='app-installer'); return item?.installed===true&&item?.enabled===false&&item?.health==='Stopped'; })()`, returnByValue: true });
  await navigate(cdp, `http://${host}:${sitePort}/app-installer.html`);
  const appExtensionIsolationDetail = await cdp.send('Runtime.evaluate', { expression: `(() => ({
    disabledState: document.body.dataset.extensionState === 'disabled',
    degradedHeading: Boolean(document.querySelector('#appInstaller .wcp-page-heading')?.textContent.includes('disabled')),
    extensionRouteRemoved: ![...document.querySelectorAll('.wcp-nav-item')].some(link => link.getAttribute('href') === 'app-installer.html'),
    coreRoutePreserved: [...document.querySelectorAll('.wcp-nav-item')].some(link => link.getAttribute('href') === 'websites.html')
  }))()`, returnByValue: true });
  const isolation = appExtensionIsolationDetail.result.value || {};
  const appExtensionIsolated = Object.values(isolation).every(Boolean);
  await navigate(cdp, `http://${host}:${sitePort}/software-manager.html`);
  const appExtensionEnabled = await cdp.send('Runtime.evaluate', { expression: `(() => { document.querySelector('[data-sw-tab="plugins"]')?.click(); const action=document.querySelector('[data-sw-plugin="app-installer"] [data-sw-action="extension-activate"]'); if(!action)return false; action.click(); document.querySelector('.sw-form')?.dispatchEvent(new Event('submit',{bubbles:true,cancelable:true})); const saved=JSON.parse(localStorage.getItem('wcp360-platform-software-v1')||'{}'); const item=saved.plugins?.find(entry=>entry.id==='app-installer'); return item?.installed===true&&item?.enabled===true&&item?.health==='Healthy'; })()`, returnByValue: true });
  await navigate(cdp, `http://${host}:${sitePort}/app-installer.html`);
  const appExtensionRestored = await cdp.send('Runtime.evaluate', { expression: `document.body.dataset.extensionState==='enabled' && document.querySelectorAll('[data-user-app]').length >= 5 && [...document.querySelectorAll('.wcp-nav-item')].some(link=>link.getAttribute('href')==='app-installer.html')`, returnByValue: true });
  if (!appExtensionDisabled.result.value || !appExtensionIsolated || !appExtensionEnabled.result.value || !appExtensionRestored.result.value) findings.push({ severity: 'error', page: 'software-manager.html → app-installer.html', mode: 'integration', rule: 'extension-failure-isolation', detail: { appExtensionDisabled: appExtensionDisabled.result.value, isolation, appExtensionEnabled: appExtensionEnabled.result.value, appExtensionRestored: appExtensionRestored.result.value } });
  cdp.close();
} finally {
  chrome.kill();
  server.close();
}

const totals = findings.reduce((acc, item) => ({ ...acc, [item.severity]: (acc[item.severity] || 0) + 1 }), {});
console.log(JSON.stringify({ pages: pages.length, totals, findings }, null, 2));
process.exitCode = totals.error ? 1 : 0;
