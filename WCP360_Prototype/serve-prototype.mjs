#!/usr/bin/env node
// Development preview only. No installation, elevated privileges or external dependencies.
import {createServer} from 'node:http';
import {readFile, realpath, stat} from 'node:fs/promises';
import {dirname, extname, resolve, sep} from 'node:path';
import {fileURLToPath} from 'node:url';

const root = await realpath(dirname(fileURLToPath(import.meta.url)));
const port = Number(process.argv[2] || 8790);
if (!Number.isInteger(port) || port < 1024 || port > 65535) throw new Error('Choose a port between 1024 and 65535.');
const mime = {'.html':'text/html; charset=utf-8','.css':'text/css; charset=utf-8','.js':'text/javascript; charset=utf-8','.json':'application/json; charset=utf-8','.md':'text/plain; charset=utf-8','.ico':'image/x-icon','.png':'image/png','.svg':'image/svg+xml','.webp':'image/webp','.woff2':'font/woff2'};
const server = createServer(async (request, response) => {
  const send = (status, content, type = 'text/plain; charset=utf-8') => {
    const bytes = Buffer.isBuffer(content) ? content : Buffer.from(content);
    response.writeHead(status, {'Content-Type':type,'Content-Length':bytes.length,'Cache-Control':'no-store','X-Content-Type-Options':'nosniff',...(status === 405 ? {'Allow':'GET, HEAD'} : {})});
    response.end(request.method === 'HEAD' ? undefined : bytes);
  };
  if (!['GET','HEAD'].includes(request.method)) { send(405,'Only GET and HEAD are supported');return; }
  let route;
  try { route = decodeURIComponent(request.url.split(/[?#]/)[0]); } catch { send(400,'Malformed URL');return; }
  if (!route.startsWith('/') || route.includes('\\') || route.includes('\0') || route.split('/').some(part => part.startsWith('.'))) { send(404,'Not found');return; }
  if (route === '/') route = '/index.html';
  const candidate = resolve(root,'.' + route);
  if (!candidate.startsWith(root + sep) || !mime[extname(candidate)]) { send(404,'Not found');return; }
  try {
    const target = await realpath(candidate);
    if (!target.startsWith(root + sep) || !(await stat(target)).isFile()) { send(404,'Not found');return; }
    send(200,await readFile(target),mime[extname(candidate)]);
  } catch (error) { send(['ENOENT','ENOTDIR','EACCES'].includes(error.code) ? 404 : 500,'Preview file unavailable'); }
});
server.requestTimeout = 5000;
server.headersTimeout = 5000;
server.on('error', error => { console.error(`WCP360 preview: ${error.message}`);process.exitCode = 1; });
server.listen(port,'127.0.0.1',() => console.log(`WCP360 prototype: http://127.0.0.1:${port}/ (Ctrl+C to stop)`));
