import {readFileSync, readdirSync, existsSync} from 'node:fs';
import {resolve, join} from 'node:path';
import {fileURLToPath} from 'node:url';
import vm from 'node:vm';
import assert from 'node:assert/strict';

const root=fileURLToPath(new URL('../',import.meta.url));
const source=name=>readFileSync(join(root,name),'utf8');
const pages=readdirSync(root).filter(name=>name.endsWith('.html'));
const scripts=readdirSync(join(root,'assets')).filter(name=>name.endsWith('.js'));
let references=0, assertions=0;
const check=(condition,message)=>{assert.ok(condition,message);assertions++;};
for(const script of scripts)new vm.Script(source('assets/'+script),{filename:script});
for(const page of pages){
  const html=source(page);
  check(/<!doctype html>/i.test(html),page+': doctype');
  check(/<html[^>]+lang=/i.test(html),page+': language');
  const ids=[...html.matchAll(/\bid=["']([^"']+)["']/g)].map(match=>match[1]);
  check(new Set(ids).size===ids.length,page+': duplicate IDs');
  for(const match of html.matchAll(/<(?:a|link|script|img)\b[^>]*?\b(?:href|src)=["']([^"'#]+)["']/gi)){
    const ref=match[1].split(/[?#]/)[0];if(/^(https?:|data:|mailto:|tel:|javascript:|\/)/i.test(ref))continue;
    references++;check(existsSync(resolve(root,ref)),page+': missing '+ref);
  }
}

const storage=new Map();
const localStorage={getItem:key=>storage.get(key)||null,setItem:(key,value)=>storage.set(key,value)};
const navContext={document:{documentElement:{setAttribute(){}},querySelectorAll:()=>[],addEventListener(){},body:{dataset:{}}},window:{addEventListener(){}},localStorage,location:{pathname:'/index.html',search:'',hash:''},URLSearchParams};
vm.runInNewContext(source('assets/foundation.js'),navContext);navContext.window.WCPUI=navContext.WCPUI;
vm.runInNewContext(source('assets/reference-ui.js'),navContext);
const navigation=navContext.window.WCPNavigation;
for(const scope of ['user','reseller','root','design']){
  const entries=navigation.entries(scope);
  check(entries.length>0,scope+': empty catalog');
  check(new Set(entries.map(item=>item.href)).size===entries.length,scope+': duplicate tool');
  for(const item of entries){check(existsSync(join(root,item.href.split('#')[0])),item.href+': missing tool');check(navigation.owner(item.href)?.scope===scope,item.href+': wrong owner');}
}
for(const page of pages.filter(page=>!['login.html','mfa.html'].includes(page)))check(!!navigation.owner(page),page+': uncategorized route');
check(!navigation.entries('user').some(item=>item.href==='mail-rules.html'),'Mail Rules is consolidated into Email');
check(navigation.resolve('mail-rules.html')==='email.html#rules','Mail Rules resolves to the shared rule editor');
check(navigation.matches(navigation.entries('user').find(item=>item.href==='email.html'),'autoresponders'),'Email search includes rule functions');
for(const route of ['mail-rules.html','mailbox-detail.html','mail-antispam.html','mail-authentication.html','mail-deliverability.html','mail-logs.html'])check(navigation.owner(route)?.href==='email.html'&&navigation.owner(route)?.scope==='user',route+': shared mail ownership');
check(navigation.owner('dns-record-add.html').href==='domains.html','DNS details belong to Domains');
check(navigation.owner('file-access.html').href==='file-access-center.html','File access belongs to its own center');
check(navigation.owner('network-nat.html').href==='network-routes.html','NAT belongs to routing');
check(navigation.owner('websites.html?site=demo#runtime').href==='websites.html','Query string must preserve route ownership');
check(navigation.matches({label:'Sécurité',category:'Account'},'securite'),'Accent-insensitive search');
check(navigation.matches(navigation.entries('user').find(item=>item.href==='domains.html'),'dns-record'),'Search includes child functions');
storage.set('wcp360-platform-software-v1',JSON.stringify({plugins:[{id:'app-installer',enabled:false}]}));
check(!navigation.entries('root').some(item=>item.href==='application-catalog.html'),'Disabled extension absent from ROOT catalog');
check(!navigation.entries('user').some(item=>item.href==='app-installer.html'),'Disabled extension absent from USER catalog');
storage.clear();
let redirects=0;
for(const page of pages){
  const html=source(page),inline=html.match(/<script>(location\.replace[\s\S]*?)<\/script>/);
  if(!inline)continue;
  let destination;vm.runInNewContext(inline[1],{location:{search:'?test=1',hash:'#review',replace:value=>destination=value}});
  check(new URLSearchParams(destination.split('?')[1].split('#')[0]).get('test')==='1'&&destination.endsWith('#review'),page+': query/hash lost');
  check(existsSync(join(root,destination.split(/[?#]/)[0])),page+': missing redirect target');redirects++;
}

// A minimal DOM adapter exercises the shared state engine without a browser.
// The assertions cover persisted behavior; layout and real DOM wiring require browser QA.
function runtimeHarness(scope,shared=new Map()){
  let time=1800000000000;const handlers=new Map(),intervals=[];
  const node=()=>({innerHTML:'',children:[],inert:true,scrollTop:0,classList:{add(){},remove(){},contains:()=>false},setAttribute(){},focus(){}});
  const list=node(),close=node(),panel=node(),backdrop=node(),inline=node(),feed=node(),detail=node();
  panel.querySelector=selector=>selector==='.runtime-job-list'?list:close;
  const elements=new Map([['.runtime-job-center',panel],['.runtime-backdrop',backdrop],['[data-runtime-page]',inline],['[data-runtime-notifications]',feed],['[data-runtime-detail]',detail]]);
  const document={body:{dataset:{accessScope:scope},append(){}},activeElement:null,createElement:()=>node(),querySelector:selector=>elements.get(selector)||null,querySelectorAll:()=>[],addEventListener:(name,callback)=>{if(!handlers.has(name))handlers.set(name,[]);handlers.get(name).push(callback);},dispatchEvent:event=>{for(const callback of handlers.get(event.type)||[])callback(event);}};
  class Clock extends Date{constructor(...args){super(...(args.length?args:[time]));}static now(){return time;}}
  let sequence=0;
  const context={document,window:{WCPUI:{toast(){}},addEventListener(){}},location:{pathname:'/jobs.html',search:'',hash:''},localStorage:{getItem:key=>shared.get(key)||null,setItem:(key,value)=>shared.set(key,value)},crypto:{randomUUID:()=>scope+'-'+(++sequence)+'-'+time},structuredClone,Date:Clock,URLSearchParams,CustomEvent:class{constructor(type,options={}){this.type=type;this.detail=options.detail;}},setInterval:callback=>intervals.push(callback),setTimeout(){}};
  vm.runInNewContext(source('assets/product-runtime.js'),context);
  return {api:context.window.WCPRuntime,document,inline,feed,shared,advance:ms=>{time+=ms;intervals.forEach(callback=>callback());}};
}
const rootSession=runtimeHarness('root');
const scheduled=rootSession.api.addJob({name:'Deferred backup',status:'scheduled',kind:'backup'});
rootSession.advance(60000);
check(rootSession.api.getState().jobs.find(job=>job.id===scheduled.id).status==='scheduled','Scheduled jobs must not run early');
rootSession.api.startJob(scheduled.id);rootSession.advance(40000);
check(rootSession.api.getState().jobs.find(job=>job.id===scheduled.id).status==='completed','Started job completes');
check(rootSession.inline.innerHTML.includes('Deferred backup'),'Job page reads shared runtime');
const cancelled=rootSession.api.addJob({name:'Cancel test'});rootSession.api.cancelJob(cancelled.id);rootSession.advance(60000);
check(rootSession.api.getState().jobs.find(job=>job.id===cancelled.id).status==='cancelled','Cancellation persists');
const escaped=rootSession.api.addJob({name:'<img src=x onerror=alert(1)>'});
check(!rootSession.inline.innerHTML.includes('<img src=x'),'Job labels are escaped');
check(rootSession.api.getState().notifications.some(note=>note.detail===escaped.id),'Operations create notifications');
const reloaded=runtimeHarness('root',rootSession.shared);
check(reloaded.api.getState().jobs.some(job=>job.id===cancelled.id&&job.status==='cancelled'),'Navigation preserves jobs');
const user=runtimeHarness('user',rootSession.shared);
check(!user.inline.innerHTML.includes('Deferred backup'),'USER cannot see ROOT jobs');
user.api.cancelJob(escaped.id);
check(user.api.getState().jobs.find(job=>job.id===escaped.id).status!=='cancelled','Cross-scope job cancellation blocked');
user.api.addJob({name:'Account backup',scope:'root'});
check(user.api.getState().jobs[0].scope==='user','Operations retain active scope');
check(!user.inline.innerHTML.includes('href="audit-explorer.html"'),'USER audit stays in account portal');
check(user.feed.innerHTML.includes('Account backup'),'Notification page reads runtime');
const malformed=new Map([['wcp360-runtime-v2','{"jobs":null,"notifications":{}}']]);
check(runtimeHarness('root',malformed).api.getState().jobs.length>0,'Malformed storage recovers to demo defaults');
console.log(JSON.stringify({htmlPages:pages.length,javascriptFiles:scripts.length,localReferences:references,compatibilityRedirects:redirects,assertions,status:'passed',visualQA:'Not covered by this script (static integration and state engine only).'},null,2));
