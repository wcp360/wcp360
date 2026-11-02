import { spawn } from 'node:child_process';
import { createServer } from 'node:http';
import { readFileSync, statSync, writeFileSync } from 'node:fs';
import { extname, join, normalize, resolve } from 'node:path';
import { tmpdir } from 'node:os';

const root = resolve(process.argv[2] || 'WCP360_Prototype');
const host = '127.0.0.1';
const sitePort = 8767;
const debugPort = 9232;
const chromePath = process.env.CHROME_PATH || 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const screenshotDirectory = process.env.WCP_LAYOUT_SCREENSHOT_DIR ? resolve(process.env.WCP_LAYOUT_SCREENSHOT_DIR) : '';
const mime = { '.html': 'text/html; charset=utf-8', '.css': 'text/css; charset=utf-8', '.js': 'text/javascript; charset=utf-8', '.ico': 'image/x-icon' };
const viewports = [
  { name: 'wide', width: 1440, height: 1000 },
  { name: 'laptop', width: 1180, height: 900 },
  { name: 'tablet-landscape', width: 1024, height: 768 },
  { name: 'tablet', width: 768, height: 1024 },
  { name: 'mobile', width: 390, height: 844 },
  { name: 'mobile-narrow', width: 320, height: 760 }
];

const server = createServer((request, response) => {
  const urlPath = decodeURIComponent(new URL(request.url, `http://${host}`).pathname);
  const relative = urlPath === '/' ? 'system-settings.html' : urlPath.slice(1);
  const file = normalize(join(root, relative));
  try {
    if (!file.startsWith(root) || !statSync(file).isFile()) throw new Error('Not found');
    response.writeHead(200, { 'Content-Type': mime[extname(file)] || 'application/octet-stream' });
    response.end(readFileSync(file));
  } catch {
    response.writeHead(404).end('Not found');
  }
});

const delay = milliseconds => new Promise(resolveDelay => setTimeout(resolveDelay, milliseconds));

class Cdp {
  constructor(url) {
    this.id = 0;
    this.pending = new Map();
    this.ready = new Promise((resolveReady, reject) => {
      this.socket = new WebSocket(url);
      this.socket.onopen = resolveReady;
      this.socket.onerror = reject;
      this.socket.onmessage = event => {
        const message = JSON.parse(event.data);
        if (!message.id) return;
        const pending = this.pending.get(message.id);
        if (!pending) return;
        this.pending.delete(message.id);
        message.error ? pending.reject(new Error(message.error.message)) : pending.resolve(message.result);
      };
    });
  }
  async send(method, params = {}) {
    await this.ready;
    const id = ++this.id;
    return new Promise((resolveCall, reject) => {
      this.pending.set(id, { resolve: resolveCall, reject });
      this.socket.send(JSON.stringify({ id, method, params }));
    });
  }
  close() { this.socket.close(); }
}

async function waitForChrome() {
  for (let index = 0; index < 60; index += 1) {
    try {
      const response = await fetch(`http://${host}:${debugPort}/json/version`);
      if (response.ok) return;
    } catch {}
    await delay(100);
  }
  throw new Error('Chrome DevTools endpoint did not start');
}

const expression = `(() => {
  const visible = element => !element.closest('[aria-hidden="true"]') && Boolean(element.offsetWidth || element.offsetHeight || element.getClientRects().length);
  const allowedScroll = element => element.matches('.sys-category-panel,.sys-category-nav,.sys-drawer,textarea,input,select,.material-symbols-rounded,.wcp-status');
  const overflowing = [...document.querySelectorAll('#systemSettingsCenter *')].filter(element => {
    if (!visible(element) || allowedScroll(element)) return false;
    return element.scrollWidth > element.clientWidth + 2;
  }).slice(0, 20).map(element => ({
    tag: element.tagName,
    className: String(element.className).slice(0, 90),
    overflow: element.scrollWidth - element.clientWidth,
    text: element.textContent.trim().replace(/\\s+/g, ' ').slice(0, 90)
  }));
  const tinyText = [...document.querySelectorAll('#systemSettingsCenter *')].filter(element => {
    if (!visible(element) || element.children.length || !element.textContent.trim() || element.classList.contains('material-symbols-rounded')) return false;
    return parseFloat(getComputedStyle(element).fontSize) < 11;
  }).slice(0, 20).map(element => ({ className: String(element.className).slice(0, 80), size: getComputedStyle(element).fontSize, text: element.textContent.trim().slice(0, 70) }));
  const controls = [...document.querySelectorAll('#systemSettingsCenter input,#systemSettingsCenter select,#systemSettingsCenter textarea,#systemSettingsCenter button,#systemSettingsCenter a')].filter(visible);
  const undersizedControls = controls.filter(element => {
    if (element.matches('input[type="checkbox"],input[type="radio"],input[type="hidden"],.sys-toggle input,.sys-search input')) return false;
    const rectangle = element.getBoundingClientRect();
    return rectangle.width < 24 || rectangle.height < 32;
  }).slice(0, 20).map(element => ({ tag: element.tagName, className: String(element.className).slice(0, 80), width: Math.round(element.getBoundingClientRect().width), height: Math.round(element.getBoundingClientRect().height), text: element.textContent.trim().slice(0, 50) }));
  const workspace = document.querySelector('.sys-workspace');
  return {
    pageOverflow: Math.max(0, document.documentElement.scrollWidth - innerWidth),
    workspaceOverflow: workspace ? Math.max(0, workspace.scrollWidth - workspace.clientWidth) : -1,
    overflowing,
    tinyText,
    undersizedControls,
    workspaceWidth: workspace ? Math.round(workspace.getBoundingClientRect().width) : 0,
    cards: document.querySelectorAll('.sys-card').length,
    fields: document.querySelectorAll('[data-sys-path]').length,
    paths: [...new Set([...document.querySelectorAll('[data-sys-path]')].map(element => element.dataset.sysPath))]
  };
})()`;

await new Promise(resolveListen => server.listen(sitePort, host, resolveListen));
const chrome = spawn(chromePath, ['--headless=new', '--disable-gpu', '--no-first-run', '--no-default-browser-check', `--remote-debugging-port=${debugPort}`, `--user-data-dir=${join(tmpdir(), `wcp360-layout-audit-${process.pid}`)}`, 'about:blank'], { stdio: 'ignore' });
const findings = [];
const screenshots = [];
const pathOwners = new Map();

try {
  await waitForChrome();
  const targetResponse = await fetch(`http://${host}:${debugPort}/json/new?about:blank`, { method: 'PUT' });
  const target = await targetResponse.json();
  const cdp = new Cdp(target.webSocketDebuggerUrl);
  await Promise.all([cdp.send('Page.enable'), cdp.send('Runtime.enable')]);

  for (const viewport of viewports) {
    await cdp.send('Emulation.setDeviceMetricsOverride', { width: viewport.width, height: viewport.height, deviceScaleFactor: 1, mobile: viewport.width < 600 });
    await cdp.send('Page.navigate', { url: `http://${host}:${sitePort}/system-settings.html` });
    await delay(250);
    const categoryResult = await cdp.send('Runtime.evaluate', { expression: `(() => [...document.querySelectorAll('[data-sys-tab]')].map(button => button.dataset.sysTab))()`, returnByValue: true });
    for (const category of categoryResult.result.value || []) {
      await cdp.send('Runtime.evaluate', { expression: `document.querySelector('[data-sys-tab=${JSON.stringify(category)}]')?.click()` });
      await delay(25);
      const result = await cdp.send('Runtime.evaluate', { expression, returnByValue: true });
      const value = result.result.value;
      if (viewport.name === 'wide') value.paths.forEach(path => pathOwners.set(path, [...(pathOwners.get(path) || []), category]));
      delete value.paths;
      if (value.pageOverflow > 1 || value.workspaceOverflow > 1 || value.overflowing.length || value.tinyText.length || value.undersizedControls.length) findings.push({ viewport: viewport.name, category, ...value });
    }
  }
  const duplicatePaths = [...pathOwners.entries()].filter(([, owners]) => new Set(owners).size > 1).map(([path, owners]) => ({ path, categories: [...new Set(owners)] }));
  if (duplicatePaths.length) findings.push({ viewport: 'content-model', category: 'duplicate-setting-path', duplicatePaths });
  if (screenshotDirectory) {
    for (const shot of [
      { name: 'system-settings-desktop.png', width: 1440, height: 1000, category: 'security' },
      { name: 'system-settings-mobile.png', width: 390, height: 844, category: 'operations' }
    ]) {
      await cdp.send('Emulation.setDeviceMetricsOverride', { width: shot.width, height: shot.height, deviceScaleFactor: 1, mobile: shot.width < 600 });
      await cdp.send('Page.navigate', { url: `http://${host}:${sitePort}/system-settings.html?section=${shot.category}` });
      await delay(400);
      const capture = await cdp.send('Page.captureScreenshot', { format: 'png', captureBeyondViewport: false });
      const target = join(screenshotDirectory, shot.name);
      writeFileSync(target, Buffer.from(capture.data, 'base64'));
      screenshots.push(target);
    }
  }
  cdp.close();
} finally {
  chrome.kill();
  server.close();
}

console.log(JSON.stringify({ viewports: viewports.length, findings, screenshots }, null, 2));
process.exitCode = findings.length ? 1 : 0;
