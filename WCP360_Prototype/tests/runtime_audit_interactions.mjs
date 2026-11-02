import assert from 'node:assert/strict';
import {spawn} from 'node:child_process';
import {createServer} from 'node:http';
import {readFileSync, mkdtempSync, rmSync, existsSync} from 'node:fs';
import {fileURLToPath} from 'node:url';
import {join, resolve, relative, isAbsolute} from 'node:path';
import {tmpdir} from 'node:os';

// Isolated browser regression tests for native forms, grid confirmations and
// focus in the shared Job Center. Run separately from the static suite.
const root=fileURLToPath(new URL('../',import.meta.url));
const chromePath=process.env.CHROME_PATH||'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const profile=mkdtempSync(join(tmpdir(),'wcp-interactions-'));
const delay=ms=>new Promise(resolveDelay=>setTimeout(resolveDelay,ms));
const assets=new Map(['foundation.js','foundation.css','product-runtime.js','product-runtime.css'].map(name=>['/assets/'+name,name]));
const fixture=`<!doctype html><html lang="en"><head><meta charset="utf-8"><link rel="stylesheet" href="/assets/product-runtime.css"></head><body data-access-scope="user">
<main id="app"><button type="button" id="launcher" data-runtime-jobs>Job Center</button><form id="example"><label>Name <input name="name" value="Demo"></label><button type="submit" id="save">Save</button><button type="submit" id="other">Save again</button></form><div id="grid"></div><div id="limited"></div><label>Search jobs <input type="search" data-runtime-search></label><div class="runtime-page-list" data-runtime-page></div><div id="wcpNotifications"></div></main><div id="alreadyInert" inert>Unavailable area</div>
<script>localStorage.setItem('wcp360-runtime-v2',JSON.stringify({jobs:[{id:'live',name:'École backup',scope:'user',status:'running',progress:20,duration:3600000,startedAt:Date.now(),createdAt:Date.now()},{id:'finished',name:'Finished operation',scope:'user',status:'running',progress:100,createdAt:Date.now()},{id:'broken',name:'Failed operation',scope:'user',status:'failed',createdAt:Date.now()},{id:'root-only',name:'ROOT only',scope:'root',status:'scheduled',createdAt:Date.now()},{id:'live',name:'Duplicate live ID',scope:'user',status:'running'}],notifications:[]}));</script>
<script src="/assets/foundation.js"></script><script src="/assets/product-runtime.js"></script></body></html>`;
const server=createServer((request,response)=>{
  const path=new URL(request.url,'http://127.0.0.1').pathname;
  if(path==='/'){response.writeHead(200,{'Content-Type':'text/html; charset=utf-8'});response.end(fixture);return;}
  const asset=assets.get(path);if(!asset){response.writeHead(404).end();return;}
  response.writeHead(200,{'Content-Type':asset.endsWith('.css')?'text/css':'text/javascript'});response.end(readFileSync(join(root,'assets',asset)));
});
await new Promise(resolveListen=>server.listen(0,'127.0.0.1',resolveListen));
const chrome=spawn(chromePath,['--headless=new','--disable-gpu','--no-first-run','--no-default-browser-check','--remote-debugging-port=0','--user-data-dir='+profile,'about:blank'],{windowsHide:true,stdio:'ignore'});
let launchError;chrome.on('error',error=>launchError=error);
let socket,sequence=0,checks=0;const pending=new Map();
function send(method,params={}){const id=++sequence;return new Promise((resolveCall,reject)=>{const timeout=setTimeout(()=>{pending.delete(id);reject(new Error('CDP timed out: '+method+' '+(params.expression||'').slice(0,160)));},10000);pending.set(id,{resolve:result=>{clearTimeout(timeout);resolveCall(result);},reject:error=>{clearTimeout(timeout);reject(error);}});socket.send(JSON.stringify({id,method,params}));});}
async function evaluate(expression){const result=await send('Runtime.evaluate',{expression,awaitPromise:true,returnByValue:true});if(result.exceptionDetails)throw new Error(result.exceptionDetails.text+': '+result.exceptionDetails.exception?.description);return result.result.value;}
function check(value,message){assert.ok(value,message);checks++;}
try{
  const activePort=join(profile,'DevToolsActivePort');
  for(let attempt=0;attempt<100&&!existsSync(activePort);attempt++){if(launchError)throw launchError;if(chrome.exitCode!==null)throw new Error('Chrome exited before startup: '+chrome.exitCode);await delay(100);}
  if(!existsSync(activePort))throw new Error('Chrome DevTools endpoint did not start.');
  const port=readFileSync(activePort,'utf8').split(/\r?\n/)[0];
  const tabs=await (await fetch('http://127.0.0.1:'+port+'/json/list')).json();
  socket=new WebSocket(tabs.find(tab=>tab.type==='page').webSocketDebuggerUrl);
  socket.onmessage=event=>{const message=JSON.parse(event.data);if(!message.id)return;const callback=pending.get(message.id);if(!callback)return;pending.delete(message.id);message.error?callback.reject(new Error(message.error.message)):callback.resolve(message.result);};
  await new Promise((resolveSocket,reject)=>{socket.onopen=resolveSocket;socket.onerror=reject;});
  await send('Page.navigate',{url:'http://127.0.0.1:'+server.address().port+'/'});
  for(let attempt=0;attempt<100;attempt++){if(await evaluate('Boolean(window.WCPRuntime&&window.WCPUI)'))break;await delay(50);}
  check(await evaluate(`(()=>{const jobs=WCPRuntime.getState().jobs;return jobs.filter(job=>job.id==='live').length===1&&jobs.find(job=>job.id==='finished').status==='completed'&&!document.querySelector('[data-runtime-page]').textContent.includes('ROOT only');})()`),'Persisted jobs recover unique IDs, completed state and portal boundaries.');
  await evaluate(`window.submitCount=0;window.formController=WCPUI.Form.attach(document.querySelector('#example'),{fields:[{name:'name',required:true}],onSubmit:()=>{submitCount++;return new Promise(resolve=>window.releaseSubmission=resolve);}});document.querySelector('#example').requestSubmit(document.querySelector('#save'));document.querySelector('#example').requestSubmit(document.querySelector('#other'));`);
  check(await evaluate(`submitCount===1&&document.querySelector('#save').disabled&&document.querySelector('#other').disabled&&document.querySelector('#example').getAttribute('aria-busy')==='true'`),'A second submit cannot duplicate an asynchronous operation.');
  await evaluate('releaseSubmission();Promise.resolve()');
  check(await evaluate(`!document.querySelector('#save').disabled&&!document.querySelector('#other').disabled&&!document.querySelector('#example').hasAttribute('aria-busy')`),'Submit controls and busy state recover after completion.');
  await evaluate(`document.querySelector('#example').requestSubmit(document.querySelector('#save'));formController.destroy();`);
  check(await evaluate(`!document.querySelector('#example').hasAttribute('aria-busy')&&!document.querySelector('#example').noValidate`),'Detaching a pending form restores native form state.');
  await evaluate('releaseSubmission();Promise.resolve()');

  await evaluate(`window.bulkCount=0;window.grid=WCPUI.DataGrid.mount(document.querySelector('#grid'),{id:'interaction-grid',rows:[{id:'one',name:'Resource'}],columns:[{key:'name',label:'Name',hidden:true}],bulkActions:[{label:'Apply',confirm:true,run:async()=>{bulkCount++;}}]});grid.model.select('one',true);grid.refresh();document.querySelector('#grid [data-ui-bulk-action]').click();window.firstConfirmation=document.querySelector('dialog');document.querySelector('#grid [data-ui-bulk-action]').click();`);
  check(await evaluate(`document.querySelector('dialog')===firstConfirmation&&document.querySelectorAll('#grid tbody td').length===2`),'Repeated bulk clicks keep one confirmation and a fully hidden configuration retains a data column.');
  await evaluate(`document.querySelector('dialog .primary').click();Promise.resolve()`);
  check(await evaluate('bulkCount===1&&!document.querySelector("dialog")&&grid.model.selectedRows().length===0'),'A confirmed bulk operation runs once and clears selection.');
  await evaluate(`WCPUI.DataGrid.mount(document.querySelector('#limited'),{id:'limited-grid',rows:[{id:'one',name:'Resource'}],columns:[{key:'name',sortable:false,filterable:false}]});`);
  check(await evaluate(`document.querySelector('#limited [data-ui-add-sort]').disabled&&document.querySelector('#limited [data-ui-filter] button[type="submit"]').disabled`),'A read-only grid does not expose unsupported filtering or sorting actions.');

  await evaluate(`document.querySelector('[data-runtime-page] [data-runtime-cancel="live"]').focus();WCPRuntime.notify('Refresh without focus loss');`);
  check(await evaluate(`document.activeElement.matches('[data-runtime-page] [data-runtime-cancel="live"]')`),'Refreshing jobs retains keyboard focus in the visible page.');
  await evaluate(`document.querySelector('[data-runtime-page] [data-runtime-cancel="live"]').click()`);
  check(await evaluate(`document.activeElement===document.querySelector('[data-runtime-page]')&&WCPRuntime.getState().jobs.find(job=>job.id==='live').status==='cancelled'`),'Removing a job action keeps focus in its page and commits cancellation.');
  await evaluate(`const search=document.querySelector('[data-runtime-search]');search.value='ecole';search.dispatchEvent(new Event('input',{bubbles:true}));`);
  check(await evaluate(`document.querySelector('[data-runtime-page]').textContent.includes('École backup')&&!document.querySelector('[data-runtime-page]').textContent.includes('Finished operation')`),'Job search ignores accents and applies the query.');
  await evaluate(`document.querySelector('#launcher').focus();WCPRuntime.openJobs();`);
  check(await evaluate(`document.querySelector('#app').inert&&document.querySelector('.runtime-job-center').contains(document.activeElement)&&document.querySelector('[data-runtime-filter="failed"]')`),'The Job Center makes background controls inert and offers failed jobs.');
  await send('Input.dispatchKeyEvent',{type:'keyDown',key:'Tab',code:'Tab',windowsVirtualKeyCode:9,modifiers:8});
  await send('Input.dispatchKeyEvent',{type:'keyUp',key:'Tab',code:'Tab',windowsVirtualKeyCode:9,modifiers:8});
  check(await evaluate(`document.querySelector('.runtime-job-center').contains(document.activeElement)`),'Native Shift+Tab remains inside the open Job Center.');
  await evaluate(`document.querySelector('[data-runtime-close]').click()`);
  check(await evaluate(`!document.querySelector('#app').inert&&document.querySelector('#alreadyInert').inert&&document.activeElement===document.querySelector('#launcher')`),'Closing the Job Center restores background state and launcher focus.');
  await evaluate(`WCPRuntime.openJobs();document.dispatchEvent(new CustomEvent('wcp:overlay-opening',{detail:{source:'command'}}));`);
  check(await evaluate(`document.querySelector('.runtime-job-center').inert&&!document.querySelector('#app').inert`),'Opening another overlay closes the Job Center and releases its background.');
  console.log(JSON.stringify({suite:'WCP360 native interaction regressions',checks,status:'passed'},null,2));
}finally{
  if(socket?.readyState===WebSocket.OPEN){try{await send('Browser.close');}catch{}socket.close();}
  if(chrome.exitCode===null){await Promise.race([new Promise(resolveExit=>chrome.once('exit',resolveExit)),delay(1500)]);if(chrome.exitCode===null)chrome.kill();}
  await new Promise(resolveClose=>server.close(resolveClose));
  const withinTemp=relative(resolve(tmpdir()),resolve(profile));
  if(withinTemp&&!withinTemp.startsWith('..')&&!isAbsolute(withinTemp)){try{rmSync(profile,{recursive:true,force:true,maxRetries:5,retryDelay:100});}catch{}}
}
