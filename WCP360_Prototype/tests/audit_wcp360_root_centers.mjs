import {readFileSync} from 'node:fs';
import vm from 'node:vm';
import assert from 'node:assert/strict';
const definitions={catalog:{file:'application-catalog.js',host:'applicationCatalog',key:'wcp360-application-catalog-v1',page:'application-catalog.html'},software:{file:'platform-software.js',host:'softwareCenter',key:'wcp360-platform-software-v1',page:'software-manager.html'},mail:{file:'mail-infrastructure.js',host:'mailInfrastructure',key:'wcp360-mail-infrastructure-v1',page:'admin-mail.html'}};
let checks=0;
function test(name,run){try{run();checks++;}catch(error){throw new Error(name+': '+error.message,{cause:error});}}
// Small HTML-tree, event, storage and overlay adapter. It does not render CSS,
// implement native validity or certify keyboard/screen-reader browser behavior.
function harness(kind,{saved,extra={},blocked=false,hash=''}={}){
 const d=definitions[kind],storage=new Map(Object.entries(extra)),events=[],overlays=[];if(saved!==undefined)storage.set(d.key,JSON.stringify(saved));
 const decode=text=>text.replace(/&(?:amp|lt|gt|quot|#39);/g,value=>({'&amp;':'&','&lt;':'<','&gt;':'>','&quot;':'"','&#39;':"'"}[value]));
 function matchOne(node,selector){
  selector=selector.trim();if(!selector)return false;
  const split=selector.match(/^(.*)\s+([^\s]+)$/);if(split){if(!matchOne(node,split[2]))return false;for(let p=node.parentElement;p;p=p.parentElement)if(matchOne(p,split[1]))return true;return false;}
  if(selector.includes(':checked')&&!node.checked)return false;selector=selector.replace(':checked','');
  let ok=true;selector=selector.replace(/\[([\w-]+)(?:([\^]?=)["']([^"']*)["'])?\]/g,(_all,name,op,value)=>{const actual=node.attrs[name];if(actual===undefined||op==='='&&actual!==value||op==='^='&&!actual.startsWith(value))ok=false;return '';});if(!ok)return false;
  const tag=selector.match(/^[a-z][\w-]*/i)?.[0];if(tag&&node.tagName!==tag.toLowerCase())return false;
  const id=selector.match(/#([\w-]+)/)?.[1];if(id&&node.attrs.id!==id)return false;
  return [...selector.matchAll(/\.([\w-]+)/g)].every(m=>(node.attrs.class||'').split(/\s+/).includes(m[1]));
 }
 const matches=(node,selector)=>selector.split(',').some(item=>matchOne(node,item));
 function element(tag='div',attrs={}){
  const node={tagName:tag,attrs,children:[],parentElement:null,style:{},dataset:{},handlers:[],hidden:'hidden'in attrs,checked:'checked'in attrs,disabled:'disabled'in attrs,ownText:'',reportValidity:()=>true};
  for(const [name,value]of Object.entries(attrs))if(name.startsWith('data-'))node.dataset[name.slice(5).replace(/-([a-z])/g,(_all,c)=>c.toUpperCase())]=value;
  for(const name of ['id','name','className','type'])Object.defineProperty(node,name,{get:()=>node.attrs[name==='className'?'class':name]||(name==='type'?'text':''),set:value=>node.attrs[name==='className'?'class':name]=String(value)});
  let explicitValue=attrs.value;
  Object.defineProperty(node,'value',{get:()=>explicitValue!==undefined?explicitValue:tag==='select'?(node.querySelector('option[selected]')||node.querySelector('option'))?.value||'':tag==='option'?attrs.value??node.textContent:tag==='textarea'?node.textContent:node.type==='checkbox'?'on':'',set:value=>explicitValue=String(value)});
  node.classList={contains:name=>(node.attrs.class||'').split(/\s+/).includes(name),add(...names){const set=new Set((node.attrs.class||'').split(/\s+/));names.forEach(n=>set.add(n));node.attrs.class=[...set].join(' ');},remove(...names){node.attrs.class=(node.attrs.class||'').split(/\s+/).filter(n=>!names.includes(n)).join(' ');},toggle(name,on){on??=!this.contains(name);on?this.add(name):this.remove(name);return on;}};
  node.setAttribute=(name,value)=>node.attrs[name]=String(value);node.getAttribute=name=>node.attrs[name]??null;node.hasAttribute=name=>name in node.attrs;
  node.append=(...items)=>{for(const item of items){if(item.parentElement)item.parentElement.children=item.parentElement.children.filter(c=>c!==item);item.parentElement=node;node.children.push(item);}};node.appendChild=item=>node.append(item);
  node.querySelectorAll=selector=>{const result=[];const walk=parent=>{for(const child of parent.children){if(matches(child,selector))result.push(child);walk(child);}};walk(node);return result;};node.querySelector=selector=>node.querySelectorAll(selector)[0]||null;
  node.closest=selector=>{for(let current=node;current;current=current.parentElement)if(matches(current,selector))return current;return null;};
  node.addEventListener=(type,callback,capture=false)=>node.handlers.push({type,callback,capture:!!capture});
  node.fire=(type,extraEvent={})=>{const path=[];for(let n=node;n;n=n.parentElement)path.push(n);let stopped=false;const event={type,target:node,currentTarget:null,preventDefault(){},stopImmediatePropagation(){stopped=true;},...extraEvent};for(const n of [...path].reverse()){event.currentTarget=n;for(const h of n.handlers.filter(h=>h.type===type&&h.capture)){h.callback(event);if(stopped)return;}}for(const n of path){event.currentTarget=n;for(const h of n.handlers.filter(h=>h.type===type&&!h.capture)){h.callback(event);if(stopped)return;}}};
  node.focus=()=>node.focused=true;node.insertAdjacentHTML=(_where,html)=>{const wrapper=element();wrapper.innerHTML=html;node.append(...wrapper.children);};
  Object.defineProperty(node,'elements',{get:()=>new Proxy({},{get:(_target,key)=>node.querySelectorAll('[name]').find(input=>input.name===key)})});
  Object.defineProperty(node,'textContent',{get:()=>node.ownText+node.children.map(c=>c.textContent).join(''),set:value=>{node.children=[];node.ownText=String(value);}});
  Object.defineProperty(node,'innerHTML',{get:()=>node.raw||'',set:value=>{node.raw=String(value);node.children=[];node.ownText='';const stack=[node];for(const m of node.raw.matchAll(/<\/?([a-z][\w-]*)\b[^>]*>|([^<]+)/gi)){if(m[2]){stack.at(-1).ownText+=decode(m[2]);continue;}const token=m[0],tag=m[1].toLowerCase();if(token.startsWith('</')){const index=stack.findLastIndex(item=>item.tagName===tag);if(index>0)stack.length=index;continue;}const attrs={};for(const attr of token.slice(1+tag.length,-1).matchAll(/([^\s=/>]+)(?:\s*=\s*(?:"([^"]*)"|'([^']*)'|([^\s>]+)))?/g))attrs[attr[1]]=decode(attr[2]??attr[3]??attr[4]??'');const child=element(tag,attrs);stack.at(-1).append(child);if(!['input','br','hr','img','meta','link'].includes(tag))stack.push(child);}}});return node;
 }
 const document=element('document'),body=element('body'),host=element('div',{id:d.host});document.body=body;document.append(body);body.append(host);body.dataset.mailCenter='root';body.dataset.rootService='software';document.dispatchEvent=event=>{events.push(event);};
 document.createElement=element;
 const location={hash,pathname:'/'+d.page},history={replaceState(_a,_b,url){location.hash=url.includes('#')?'#'+url.split('#')[1]:'';}};
 const WCPUI={Overlay:{attach(){const overlay={opened:false,open(){overlay.opened=true;},close(){overlay.opened=false;}};overlays.push(overlay);return overlay;}}};const window={WCPUI,addEventListener(){}};
 class FormData{constructor(form){this.values=form.querySelectorAll('[name]').filter(input=>!input.disabled&&(!['checkbox','radio'].includes(input.type)||input.checked)).map(input=>[input.name,input.value]);}get(name){return this.values.find(entry=>entry[0]===name)?.[1]??null;}entries(){return this.values[Symbol.iterator]();}}
 vm.runInNewContext(readFileSync(new URL('../assets/'+d.file,import.meta.url),'utf8'),{document,window,WCPUI,location,history,URL,FormData,setTimeout(){},clearTimeout(){},Date,structuredClone,localStorage:{getItem:key=>storage.get(key)||null,setItem:(key,value)=>{if(blocked)throw Error('blocked');storage.set(key,value);}},CustomEvent:class{constructor(type,{detail}={}){this.type=type;this.detail=detail;}}});
 const find=selector=>body.querySelector(selector);return{host,storage,events,overlays,find,all:selector=>body.querySelectorAll(selector),state:()=>JSON.parse(storage.get(d.key)||'null'),get blocked(){return blocked;},set blocked(value){blocked=value;},click(selector){const target=find(selector);assert.ok(target,'Missing '+selector);target.fire('click');},submit(values={}){const form=body.querySelector('form');assert.ok(form,'Missing form');for(const [name,value]of Object.entries(values)){const input=form.querySelector('[name="'+name+'"]');assert.ok(input,'Missing input '+name);if(input.type==='checkbox')input.checked=Boolean(value);else input.value=value;}form.fire('submit');},ops:()=>events.filter(e=>e.type==='wcp:operation')};
}

test('All three ROOT centers mount without writing defaults or queueing work',()=>{
 for(const kind of Object.keys(definitions)){const h=harness(kind);assert.ok(h.host.innerHTML.length>100);assert.equal(h.storage.size,0);assert.equal(h.ops().length,0);}
});
test('New applications stay hidden until explicit local review',()=>{
 const h=harness('catalog');h.click('[data-cat-action="add-app"]');h.submit({name:'Demo Catalog Application'});let app=h.state().apps.find(a=>a.name==='Demo Catalog Application');assert.equal(app.enabled,false);assert.equal(app.reviewState,'Pending');assert.equal(h.ops().length,1);
 h.click('[data-cat-tab="catalog"]');const toggle=h.find('[data-cat-app-toggle="'+app.id+'"]');toggle.checked=true;toggle.fire('change');assert.equal(h.state().apps.find(a=>a.id===app.id).enabled,false);assert.equal(h.ops().length,1);
 h.click('[data-cat-manage="'+app.id+'"]');h.click('[data-cat-edit-app]');h.submit({reason:'Reviewed local demo catalog',ack:true});assert.equal(h.state().apps.find(a=>a.id===app.id).enabled,true);assert.ok(h.state().apps.find(a=>a.id===app.id).reviewReason.includes('local'));assert.equal(h.ops().length,2);
});
test('New sources are quarantined, require HTTPS and preserve local approval',()=>{
 const h=harness('catalog');h.click('[data-cat-tab="sources"]');h.click('[data-cat-action="add-source"]');h.submit({name:'Demo Registry',endpoint:'http://unsafe.example/catalog'});assert.equal(h.state(),null);assert.equal(h.ops().length,0);
 h.submit({name:'Demo Registry',endpoint:'https://registry.example/catalog'});const source=h.state().sources.find(s=>s.name==='Demo Registry');assert.equal(source.enabled,false);assert.equal(source.reviewState,'Pending');assert.equal(source.sync,'Not verified');
 const card=h.find('[data-cat-source="'+source.id+'"]');assert.ok(card.textContent.includes('Quarantined'));assert.ok(card.textContent.includes('Not checked'));assert.equal(card.querySelector('.cat-badge').classList.contains('success'),false);const toggle=h.find('[data-cat-source-toggle="'+source.id+'"]');toggle.checked=true;toggle.fire('change');assert.equal(h.state().sources.find(s=>s.id===source.id).enabled,false);
 h.click('[data-cat-review-source="'+source.id+'"]');h.submit({reason:'Local registry review',ack:true});assert.equal(h.state().sources.find(s=>s.id===source.id).enabled,true);
});
test('Catalog rejects duplicate names and disabled source selections',()=>{
 const h=harness('catalog');h.click('[data-cat-action="add-app"]');h.submit({name:'WordPress'});assert.equal(h.state(),null);h.submit({name:'Other app',source:'No such source'});assert.equal(h.state(),null);assert.equal(h.ops().length,0);
});
test('Catalog storage failure keeps the editor and queues no simulated operation',()=>{
 const h=harness('catalog',{blocked:true});h.click('[data-cat-action="add-app"]');h.submit({name:'Storage Test'});assert.equal(h.state(),null);assert.equal(h.ops().length,0);assert.equal(h.overlays[0].opened,true);assert.ok(h.find('.cat-feedback').textContent.includes('No change was saved'));
 h.blocked=false;h.submit({name:'Storage Test'});assert.equal(h.state().apps.filter(a=>a.name==='Storage Test').length,1);assert.equal(h.ops().length,1);
});
test('Catalog source sync preserves unverified status instead of fabricating results',()=>{
 const h=harness('catalog');h.click('[data-cat-action="sync"]');assert.equal(h.state().sources[0].sync,'4 min ago');assert.ok(h.find('.cat-feedback').textContent.includes('No source was contacted'));
});
test('Catalog rollout request retains every selected input',()=>{
 const h=harness('catalog');h.click('[data-cat-tab="updates"]');h.click('[data-cat-action="publish-update"]');h.submit({maintenance:'Tomorrow 03:00 UTC',window:'48 hours'});assert.equal(h.state().rolloutDrafts[0].maintenance,'Tomorrow 03:00 UTC');assert.equal(h.state().rolloutDrafts[0].window,'48 hours');assert.equal(h.state().rolloutDrafts[0].demo,true);
});
test('Software maintenance policy values persist and reopen accurately',()=>{
 const h=harness('software');h.click('[data-sw-action="policy"]');h.submit({soak:'31',retention:'28',window:'Thursday · 23:00–00:30 CET',freeze:false});assert.equal(h.state().maintenancePolicy.soak,31);assert.equal(h.state().maintenancePolicy.retention,28);assert.equal(h.state().maintenancePolicy.freeze,false);assert.equal(h.ops().length,1);
 h.click('[data-sw-action="policy"]');assert.equal(h.find('[name="soak"]').value,'31');assert.equal(h.find('[name="freeze"]').checked,false);const reload=harness('software',{saved:h.state()});reload.click('[data-sw-action="policy"]');assert.equal(reload.find('[name="retention"]').value,'28');
});
test('Software package target version and selected scope survive reload',()=>{
 const h=harness('software');h.click('[data-sw-tab="packages"]');const id=h.find('[data-sw-action="package"]').dataset.swId;h.click('[data-sw-action="package"]');h.submit({targetVersion:'99.1.0',target:'Canary only'});const p=h.state().packages.find(p=>p.id===id);assert.equal(p.targetVersion,'99.1.0');assert.equal(p.targetScope,'Canary only');h.click('[data-sw-action="package"]');assert.equal(h.find('[name="targetVersion"]').value,'99.1.0');
});
test('Software rejects invalid quantities and maintenance ranges',()=>{
 const h=harness('software');h.click('[data-sw-action="policy"]');h.submit({soak:'0',retention:'-1'});assert.equal(h.ops().length,0);assert.equal(h.state(),null);assert.equal(h.overlays[0].opened,true);
});
test('Software storage failure rolls back and leaves the active form open',()=>{
 const h=harness('software',{blocked:true});h.click('[data-sw-action="policy"]');h.submit({soak:'31',retention:'28'});assert.equal(h.state(),null);assert.equal(h.ops().length,0);assert.equal(h.overlays[0].opened,true);assert.ok(h.find('.sw-feedback').textContent.includes('No change was saved'));
});
test('Mail profile editing targets the chosen profile and retains field values',()=>{
 const h=harness('mail');h.click('[data-mail-tab="nodes"]');h.click('[data-profile-id="transactional"]');assert.equal(h.find('[name="name"]').value,'Transactional Relay');h.submit({name:'Transactional Demo',smtp:'Postfix relay',security:'Rspamd'});assert.equal(h.state().profiles.find(p=>p.id==='transactional').name,'Transactional Demo');assert.equal(h.state().profiles.find(p=>p.id==='production').name,'Production Mail');assert.equal(h.state().requests[0].inputs.name,'Transactional Demo');assert.ok(h.find('.mic-view').textContent.includes('Local demonstration requests'));
});
test('Mail node enrollment has a distinct form and persistent local request',()=>{
 const h=harness('mail');h.click('[data-mail-tab="nodes"]');h.click('[data-mail-action="add-node"]');h.submit({name:'mail-lyon-01',region:'Lyon',address:'192.0.2.44',profile:'production'});assert.equal(h.state().nodeDrafts[0].name,'mail-lyon-01');assert.equal(h.state().nodeDrafts[0].address,'192.0.2.44');assert.equal(h.state().requests[0].kind,'add-node');assert.equal(h.ops().length,1);h.click('[data-mail-action="add-node"]');h.submit({name:'mail-lyon-01',region:'Lyon',address:'192.0.2.44',profile:'production'});assert.equal(h.state().nodeDrafts.length,1);assert.equal(h.ops().length,1);
});
test('Mail policy requests retain all inputs and repopulate the same family',()=>{
 const h=harness('mail');h.click('[data-mail-tab="smtp"]');h.click('[data-mail-action="edit-smtp"]');h.submit({target:'All compatible nodes',activation:'Maintenance window',reason:'Deliverability policy review'});assert.equal(h.state().policyDrafts.smtp.target,'All compatible nodes');assert.equal(h.state().requests[0].inputs.reason,'Deliverability policy review');h.click('[data-mail-action="edit-smtp"]');assert.equal(h.find('[name="reason"]').value,'Deliverability policy review');
});
test('Advanced mail search filters actual queue rows and emits no job',()=>{
 const h=harness('mail');h.click('[data-mail-tab="queue"]');h.click('[data-mail-action="queue-search"]');h.submit({query:'hello',status:'Deferred',response:'rate',range:'Last 24 hours'});const rows=h.all('[data-mail-queue]');assert.equal(rows.filter(row=>!row.hidden).length,1);assert.ok(rows.find(row=>!row.hidden).textContent.includes('hello@example.com'));assert.equal(h.ops().length,0);assert.equal(h.state(),null);assert.ok(h.find('[data-queue-feedback]').textContent.includes('1 local'));
});
test('Mail storage failure keeps form values and avoids fake success or job',()=>{
 const h=harness('mail',{blocked:true});h.click('[data-mail-action="create-profile"]');h.submit({name:'Mail Storage Test'});assert.equal(h.state(),null);assert.equal(h.ops().length,0);assert.equal(h.overlays[0].opened,true);assert.equal(h.find('[name="name"]').value,'Mail Storage Test');assert.ok(h.find('.mic-drawer-body .mic-feedback').textContent.includes('No change was saved'));h.blocked=false;h.submit();assert.equal(h.state().profiles.filter(p=>p.name==='Mail Storage Test').length,1);
});
test('Mail endpoint validation rejects malformed HTTPS and embedded credentials',()=>{
 const h=harness('mail');h.click('[data-mail-tab="webmail"]');const endpoint=h.find('[data-provider-endpoint]');endpoint.value='https://';h.click('[data-save-webmail]');assert.equal(h.storage.has('wcp360-webmail-clients-v1'),false);endpoint.value='https://user:password@example.com/';h.click('[data-save-webmail]');assert.equal(h.ops().length,0);endpoint.value='https://mail.example.com/roundcube/';h.click('[data-save-webmail]');assert.ok(h.storage.has('wcp360-webmail-clients-v1'));assert.equal(h.ops().length,1);
});
test('Malformed local collections do not break ROOT center rendering',()=>{
 for(const [kind,saved]of [['catalog',{schema:1,sources:null,events:null,apps:[null]}],['software',{packages:[null],plugins:[null],history:null}],['mail',{profiles:[null],queue:[null],requests:[null]}]]){const h=harness(kind,{saved});assert.ok(h.host.innerHTML.length>100);assert.equal(h.ops().length,0);}
});
test('Failed catalog review never publishes a draft and can be retried safely',()=>{
 const h=harness('catalog');h.click('[data-cat-action="add-app"]');h.submit({name:'Review Storage Test'});const id=h.state().apps.find(a=>a.name==='Review Storage Test').id;h.click('[data-cat-tab="catalog"]');h.click('[data-cat-manage="'+id+'"]');h.click('[data-cat-edit-app]');h.blocked=true;h.submit({reason:'Local review reason',ack:true});assert.equal(h.state().apps.find(a=>a.id===id).enabled,false);assert.equal(h.ops().length,1);assert.equal(h.overlays[0].opened,true);h.blocked=false;h.submit();assert.equal(h.state().apps.find(a=>a.id===id).enabled,true);assert.equal(h.ops().length,2);
});
test('Failed source creation remains absent and cannot enter publication choices',()=>{
 const h=harness('catalog',{blocked:true});h.click('[data-cat-tab="sources"]');h.click('[data-cat-action="add-source"]');h.submit({name:'Blocked source',endpoint:'https://registry.example/catalog'});assert.equal(h.state(),null);assert.equal(h.ops().length,0);assert.equal(h.overlays[0].opened,true);h.click('[data-cat-close]');h.click('[data-cat-action="add-app"]');assert.equal(h.all('[name="source"] option').some(o=>o.textContent==='Blocked source'),false);
});
test('Software navigation works while storage is unavailable',()=>{
 const h=harness('software',{blocked:true});h.click('[data-sw-tab="packages"]');assert.ok(h.find('[data-sw-action="package"]'));assert.equal(h.state(),null);assert.equal(h.ops().length,0);
});
test('Webmail storage failure reports in the visible policy after closing an editor',()=>{
 const h=harness('mail');h.click('[data-mail-action="create-profile"]');h.click('[data-mail-close]');h.click('[data-mail-tab="webmail"]');h.blocked=true;h.click('[data-save-webmail]');assert.equal(h.storage.has('wcp360-webmail-clients-v1'),false);assert.equal(h.ops().length,0);assert.ok(h.find('.root-feedback').textContent.includes('No change was saved'));h.blocked=false;h.click('[data-save-webmail]');assert.ok(h.storage.has('wcp360-webmail-clients-v1'));assert.equal(h.ops().length,1);
});
test('Repeating an unchanged software policy cannot queue before a failed write',()=>{
 const h=harness('software');h.click('[data-sw-action="policy"]');h.submit({soak:'31',retention:'28'});assert.equal(h.ops().length,1);h.click('[data-sw-action="policy"]');h.blocked=true;h.submit();assert.equal(h.ops().length,1);assert.equal(h.state().maintenancePolicy.soak,31);assert.equal(h.overlays[0].opened,true);
});
console.log(JSON.stringify({suite:'WCP360 specialized ROOT centers',pages:Object.values(definitions).map(d=>d.page),behaviorChecks:checks,status:'passed',boundary:'Actual controllers through HTML-tree, event, form-data, overlay and storage adapters. Native form validity is stubbed; no browser rendering, external verification or infrastructure action is certified.'},null,2));
