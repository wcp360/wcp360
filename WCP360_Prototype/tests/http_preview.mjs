import {request} from 'node:http';
import {readdirSync} from 'node:fs';
import assert from 'node:assert/strict';
const port=Number(process.argv[2] || 8790);
const get=(path,method='GET') => new Promise((resolve,reject) => {
  const req=request({host:'127.0.0.1',port,path,method,timeout:5000},response => {
    const chunks=[];response.on('data',chunk => chunks.push(chunk));response.on('end',() => resolve({status:response.statusCode,headers:response.headers,body:Buffer.concat(chunks).toString('utf8')}));
  });req.on('error',reject);req.on('timeout',() => req.destroy(new Error('Preview request timeout')));req.end();
});
let checks=0;
const check=(condition,message) => {assert.ok(condition,message);checks++;};
const home=await get('/');
check(home.status===200&&/<!doctype html>/i.test(home.body),'Home must load');
check(home.headers['x-content-type-options']==='nosniff','Explicit MIME handling');
check(home.headers['cache-control']==='no-store','Preview must refresh edited source');
const head=await get('/index.html','HEAD');check(head.status===200&&!head.body&&Number(head.headers['content-length'])>0,'HEAD returns headers only');
const post=await get('/','POST');check(post.status===405&&post.headers.allow==='GET, HEAD','Only read methods are supported');
for(const path of ['/missing.html','/../README_GITHUB.md','/%2e%2e/README_GITHUB.md','/%2e%2e%5cREADME_GITHUB.md','/assets/../../README_GITHUB.md','/serve-prototype.ps1','/serve-prototype.mjs','/.env','/assets/','/%00index.html']) check((await get(path)).status===404,'Block unknown/outside/source path '+path);
check((await get('/%ZZ')).status===400,'Malformed escape is rejected');
const pages=readdirSync(new URL('../',import.meta.url)).filter(name => name.endsWith('.html'));
for(const page of pages){const response=await get('/'+page);check(response.status===200&&response.headers['content-type']?.includes('text/html')&&/<!doctype html>/i.test(response.body),'HTML route '+page);}
for(const name of ['foundation.js','reference-ui.js','prototype-search.js','prototype-home.js','setup-model.js','setup-wizard.js'])check((await get('/assets/'+name)).status===200,'Shared asset '+name);
console.log(JSON.stringify({suite:'Local preview HTTP contract',port,pages:pages.length,checks,status:'passed',coverage:'Read-only HTTP requests; no browser rendering, host operations or production server validation.'},null,2));
