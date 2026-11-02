import {readFileSync,readdirSync,existsSync} from 'node:fs';
import {join,resolve} from 'node:path';
import {fileURLToPath} from 'node:url';
import vm from 'node:vm';
import assert from 'node:assert/strict';

const root=fileURLToPath(new URL('../',import.meta.url));
const read=name=>readFileSync(join(root,name),'utf8');
const pages=readdirSync(root).filter(name=>name.endsWith('.html'));
let assertions=0,fragments=0,foundationConsumers=0;
const check=(condition,message)=>{assert.ok(condition,message);assertions++;};
const escape=value=>value.replace(/[.*+?^${}()|[\]\\]/g,'\\$&');
for(const page of pages){
  const html=read(page);
  check([...html.matchAll(/<main\b/gi)].length===1,page+': exactly one main landmark');
  check([...html.matchAll(/<h1\b/gi)].length===1,page+': exactly one document heading');
  const scripts=[...html.matchAll(/<script\b[^>]*\bsrc=["']([^"']+)["']/gi)].map(match=>match[1]);
  for(const [index,script] of scripts.entries()){
    if(script!=='assets/reference-ui.js'&&!(script.startsWith('assets/')&&/\bWCPUI\.Overlay\b/.test(read(script))))continue;
    foundationConsumers++;
    check(scripts.indexOf('assets/foundation.js')>=0&&scripts.indexOf('assets/foundation.js')<index,page+': foundation.js must load before '+script);
  }
  for(const match of html.matchAll(/<(?:a|link)\b[^>]*\bhref=["']([^"']+)["']/gi)){
    const href=match[1];if(/^(?:https?:|mailto:|tel:|javascript:|data:)/i.test(href)||href==='#')continue;
    const url=new URL(href,'http://localhost/'+page),target=decodeURIComponent(url.pathname.slice(1));
    check(existsSync(resolve(root,target)),page+': local target '+href);
    if(!url.hash||!target.endsWith('.html'))continue;
    const destination=read(target),id=decodeURIComponent(url.hash.slice(1));fragments++;
    if(new RegExp('\\bid=["\']'+escape(id)+'["\']').test(destination))continue;
    // Centers deliberately use URL fragments as controller-owned tab state.
    // Require a hash-aware local controller and a declared target literal.
    const controllers=[...destination.matchAll(/<script\b[^>]*\bsrc=["'](assets\/[^"']+)["']/gi)]
      .map(match=>match[1]).filter(name=>name!=='assets/reference-ui.js').map(read).join('\n');
    check(controllers.includes('location.hash')&&new RegExp('["\']'+escape(id)+'["\']|\\b'+escape(id)+'\\s*:').test(controllers),page+': unresolved fragment '+href);
  }
}

for(const page of ['login.html','mfa.html']){
  const html=read(page),inputs=[...html.matchAll(/<input\b[^>]*>/gi)];
  check(inputs.length>0,page+': demonstration inputs');
  check(inputs.every(match=>! /\bname\s*=/.test(match[0])),page+': input values cannot be serialized into URL parameters');
  check(/<button\b[^>]*type="submit"[^>]*\bdisabled/.test(html),page+': submit stays disabled without controller');
  check(!html.includes('wcpCommandBackdrop')&&!html.includes('wcpJobsDrawer'),page+': no duplicate authenticated workspace overlays');
}

function harness(page,{portal='user',search='',storageBlocked=false}={}){
  const storage=new Map([['wcp360-auth-portal',portal]]),assigned=[],replaced=[];
  function element(){const handlers=new Map(),attrs=new Map(),classes=new Set();return {handlers,attrs,dataset:{},value:'',hidden:false,disabled:true,textContent:'',
    classList:{toggle(name,on){on?classes.add(name):classes.delete(name);},contains:name=>classes.has(name)},
    setAttribute(name,value){attrs.set(name,String(value));},removeAttribute(name){attrs.delete(name);},focus(){this.focused=true;},
    addEventListener(name,callback){handlers.set(name,callback);},fire(name,event={}){handlers.get(name)?.(event);}};}
  const form=element(),feedback=element(),submit=element(),identity=element(),password=element(),code=element(),toggle=element(),label=element(),help=element();
  identity.value='admin@example.com';password.value='demo-wcp360';
  const buttons=[];form.action=page==='login.html'?'mfa.html':'canonical-user-dashboard.html';form.reportValidity=()=>true;
  form.insertAdjacentHTML=(_position,html)=>{form.inserted=html;if(html.includes('data-portal'))for(const match of html.matchAll(/data-portal="([^"]+)"/g)){const button=element();button.dataset.portal=match[1];buttons.push(button);}};
  form.querySelector=selector=>({'[data-auth-feedback]':feedback,'[type="submit"]':submit}[selector]||null);
  form.querySelectorAll=selector=>selector==='[aria-invalid]'?[identity,password,code].filter(field=>field.attrs.has('aria-invalid')):[];
  const document={querySelector:selector=>({'[data-auth-form]':form,'#login-identity':identity,'#login-password':password,'#mfa-code':code,'[data-auth-recovery]':toggle,'[data-auth-code-label]':label,'[data-auth-code-help]':help}[selector]||null),querySelectorAll:()=>buttons};
  const location={pathname:'/'+page,search,hash:'',assign:value=>assigned.push(value)};
  vm.runInNewContext(read('assets/auth-runtime.js'),{document,location,URLSearchParams,history:{replaceState:(_state,_title,value)=>replaced.push(value)},localStorage:{getItem:key=>{if(storageBlocked)throw Error('blocked');return storage.get(key)||null;},setItem:(key,value)=>{if(storageBlocked)throw Error('blocked');storage.set(key,value);}}});
  return {form,feedback,submit,identity,password,code,toggle,label,help,buttons,storage,assigned,replaced,submitForm(){let prevented=false;form.fire('submit',{preventDefault(){prevented=true;}});check(prevented,page+': browser submission intercepted');}};
}

const login=harness('login.html');
check(login.buttons.length===3,'All three portals are available');
check(login.buttons.filter(button=>button.attrs.get('aria-pressed')==='true').length===1,'One portal has accessible selection state');
login.buttons.find(button=>button.dataset.portal==='reseller').fire('click');
login.submitForm();check(login.assigned[0]==='mfa.html?portal=reseller','Reseller journey reaches its MFA context');
check(login.password.value==='','Password is cleared before navigation');
check([...login.storage.keys()].every(key=>key==='wcp360-auth-portal'),'Only portal preference is persisted');
const badIdentity=harness('login.html');badIdentity.identity.value='real@example.net';badIdentity.submitForm();
check(!badIdentity.assigned.length&&badIdentity.identity.attrs.get('aria-invalid')==='true','Real identity input is rejected locally');
const badPassword=harness('login.html');badPassword.password.value='another-value';badPassword.submitForm();
check(!badPassword.assigned.length&&!badPassword.feedback.hidden,'Incorrect demo password shows feedback');
badPassword.form.fire('input');check(badPassword.feedback.hidden&&!badPassword.password.attrs.has('aria-invalid'),'Input clears stale errors');
const blockedLogin=harness('login.html',{storageBlocked:true});blockedLogin.buttons.find(button=>button.dataset.portal==='root').fire('click');blockedLogin.submitForm();
check(blockedLogin.assigned[0]==='mfa.html?portal=root','Blocked storage still preserves selected portal during navigation');

for(const [portal,target] of Object.entries({user:'canonical-user-dashboard.html',reseller:'reseller.html',root:'canonical-admin-dashboard.html'})){
  const mfa=harness('mfa.html',{search:'?portal='+portal,storageBlocked:true});mfa.code.value='123456';mfa.submitForm();
  check(mfa.assigned[0]===target,portal+': MFA opens correct portal without storage');
  check(mfa.code.value==='','MFA code is cleared before navigation');
}
const invalidCode=harness('mfa.html');invalidCode.code.value='000000';invalidCode.submitForm();
check(!invalidCode.assigned.length&&invalidCode.code.attrs.get('aria-invalid')==='true','Wrong six-digit fixture is rejected');
const recovery=harness('mfa.html',{portal:'root'});recovery.toggle.fire('click');
check(recovery.code.pattern==='DEMO-RECOVERY'&&recovery.code.maxLength===13&&recovery.toggle.attrs.get('aria-pressed')==='true','Recovery switches field constraints and announces mode');
recovery.code.value='DEMO-RECOVERY';recovery.submitForm();check(recovery.assigned[0]==='canonical-admin-dashboard.html','Recovery fixture reaches selected portal');
recovery.toggle.fire('click');check(recovery.code.pattern==='[0-9]{6}'&&recovery.code.value==='','Returning to numeric mode clears prior input');
const legacy=harness('mfa.html',{portal:'root',search:'?password=old-demo&code=123456&portal=reseller'});
check(legacy.replaced[0]==='/mfa.html?portal=reseller','Historical credential parameters are discarded');
check(legacy.form.action==='reseller.html','Explicit safe portal takes precedence over previous selection');
const invalidPortal=harness('mfa.html',{search:'?portal=%3Cimg%3E'});
check(invalidPortal.form.action==='canonical-user-dashboard.html'&&!invalidPortal.form.inserted.includes('<img>'),'Invalid portal cannot become markup or a destination');

console.log(JSON.stringify({suite:'WCP360 authentication demo and HTML routes',htmlPages:pages.length,fragments,foundationConsumers,assertions,status:'passed',boundary:'Controller adapters and static HTML; native browser validation, focus and rendering require browser QA.'},null,2));
