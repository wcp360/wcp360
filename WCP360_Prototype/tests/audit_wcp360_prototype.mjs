import {fileURLToPath} from 'node:url';
import fs from 'node:fs';
import path from 'node:path';
import process from 'node:process';

const root = path.resolve(process.argv[2] || fileURLToPath(new URL('../',import.meta.url)));
const files = fs.readdirSync(root).filter((name) => name.endsWith('.html')).sort();
const findings = [];
const add = (severity, file, rule, detail) => findings.push({ severity, file, rule, detail });

for (const file of files) {
  const source = fs.readFileSync(path.join(root, file), 'utf8');
  if (!/<!doctype html>/i.test(source)) add('error', file, 'doctype', 'DOCTYPE missing');
  if (!/<html\b[^>]*\blang=["'][^"']+["']/i.test(source)) add('error', file, 'language', 'HTML language missing');
  if (!/<meta\b[^>]*\bname=["']viewport["']/i.test(source)) add('error', file, 'viewport', 'Viewport meta missing');
  if (!/<title>\s*\S[\s\S]*?<\/title>/i.test(source)) add('error', file, 'title', 'Document title missing');
  if (!/<h1\b/i.test(source)) add('warning', file, 'heading', 'No h1 heading');

  const ids = [...source.matchAll(/\bid=["']([^"']+)["']/gi)].map((match) => match[1]);
  const duplicateIds = [...new Set(ids.filter((id, index) => ids.indexOf(id) !== index))];
  if (duplicateIds.length) add('error', file, 'duplicate-id', duplicateIds.join(', '));

  for (const match of source.matchAll(/<(?:a|link|script|img)\b[^>]*?\b(?:href|src)=["']([^"'#?]+)["']/gi)) {
    const ref = match[1];
    if (/^(?:https?:|data:|mailto:|tel:|javascript:|\/)/i.test(ref)) continue;
    if (!fs.existsSync(path.resolve(root, ref))) add('error', file, 'broken-reference', ref);
  }

  for (const match of source.matchAll(/<img\b[^>]*>/gi)) {
    if (!/\balt=["'][^"']*["']/i.test(match[0])) add('error', file, 'image-alt', match[0].slice(0, 100));
  }
  for (const match of source.matchAll(/<button\b([^>]*)>([\s\S]*?)<\/button>/gi)) {
    const attrs = match[1];
    const content = match[2].replace(/<[^>]+>/g, '').trim();
    if (!content && !/\baria-label=["'][^"']+["']/i.test(attrs)) add('warning', file, 'button-name', match[0].slice(0, 100));
  }
  if (/[ÃÂâ][\u0080-\u00bf]|ðŸ|ï¿½|ï¼|\uFFFD/.test(source)) add('error', file, 'mojibake', 'Probable broken UTF-8 text');
}

const unique = [...new Map(findings.map((item) => [`${item.severity}|${item.file}|${item.rule}|${item.detail}`, item])).values()];
const totals = unique.reduce((acc, item) => ({ ...acc, [item.severity]: (acc[item.severity] || 0) + 1 }), {});
console.log(JSON.stringify({ root, htmlFiles: files.length, totals, findings: unique }, null, 2));
process.exitCode = totals.error ? 1 : 0;
