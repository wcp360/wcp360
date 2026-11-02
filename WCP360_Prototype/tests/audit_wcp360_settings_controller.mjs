import {readFileSync} from 'node:fs';
import vm from 'node:vm';
import assert from 'node:assert/strict';

const source=name=>readFileSync(new URL('../assets/'+name,import.meta.url),'utf8');
let checks=0;
async function test(name,run){try{await run();checks++;}catch(error){throw new Error(name+': '+error.message,{cause:error});}}

// Actual foundation, settings model, persistence and controller. The deliberately
// small tree adapter covers attributes, parsing, event bubbling and programmatic
// focus. Dialog modality and native form validity are stubbed; this is not a
// browser, layout test, native keyboard test or accessibility certification.
function harness({url='https://prototype.test/system-settings.html#server',initial={},runtime=true}={}){
  const storage=new Map(Object.entries(initial)),writes=[],operations=[],windowListeners=new Map();
  let document,denied='';
  const decode=value=>String(value).replace(/&(?:amp|lt|gt|quot|#39);/g,entity=>({'&amp;':'&','&lt;':'<','&gt;':'>','&quot;':'"','&#39;':"'"}[entity]));
  const dataName=name=>'data-'+String(name).replace(/[A-Z]/g,char=>'-'+char.toLowerCase());
  function matchesSimple(node,selector){
    if(!node||!selector)return false;
    for(const match of selector.matchAll(/\[([^\]=\s]+)(?:=["']?([^\]"']*)["']?)?\]/g)){
      if(!node.hasAttribute(match[1]))return false;
      if(match[2]!==undefined&&node.getAttribute(match[1])!==match[2])return false;
    }
    selector=selector.replace(/\[[^\]]+\]/g,'');
    const tag=selector.match(/^[a-z][\w-]*/i)?.[0];if(tag&&node.tagName!==tag.toUpperCase())return false;
    const id=selector.match(/#([\w-]+)/)?.[1];if(id&&node.id!==id)return false;
    return [...selector.matchAll(/\.([\w-]+)/g)].every(match=>node.classList.contains(match[1]));
  }
  function matches(node,selector){return selector.split(',').some(part=>{
    const pieces=part.trim().split(/\s+(?![^\[]*\])/);if(!matchesSimple(node,pieces.pop()))return false;
    let ancestor=node.parentElement;
    while(pieces.length){const expected=pieces.pop();while(ancestor&&!matchesSimple(ancestor,expected))ancestor=ancestor.parentElement;if(!ancestor)return false;ancestor=ancestor.parentElement;}
    return true;
  });}
  function element(tag='div'){
    const attrs=new Map(),handlers=new Map();let ownText='',markup='',assignedValue;
    const node={tagName:tag.toUpperCase(),children:[],parentElement:null,style:{},disabled:false,hidden:false,inert:false,checked:false,open:false,
      setAttribute(name,value){attrs.set(name,String(value));if(['disabled','hidden','inert','checked','open'].includes(name))this[name]=true;},
      getAttribute(name){if(['disabled','hidden','inert','checked','open'].includes(name))return this[name]?'':null;return attrs.get(name)??null;},
      hasAttribute(name){return this.getAttribute(name)!==null;},
      removeAttribute(name){attrs.delete(name);if(['disabled','hidden','inert','checked','open'].includes(name))this[name]=false;},
      append(...items){for(const item of items){item.remove();item.parentElement=this;this.children.push(item);}},
      appendChild(item){this.append(item);return item;},
      remove(){if(this.parentElement)this.parentElement.children=this.parentElement.children.filter(child=>child!==this);this.parentElement=null;},
      matches(selector){return matches(this,selector);},
      closest(selector){let current=this;while(current){if(matches(current,selector))return current;current=current.parentElement;}return null;},
      querySelectorAll(selector){const result=[];const walk=current=>{for(const child of current.children){if(matches(child,selector))result.push(child);walk(child);}};walk(this);return result;},
      querySelector(selector){return this.querySelectorAll(selector)[0]||null;},
      contains(item){while(item){if(item===this)return true;item=item.parentElement;}return false;},
      appendText(text){ownText+=decode(text);},focus(){document.activeElement=this;},
      addEventListener(name,callback){if(!handlers.has(name))handlers.set(name,[]);handlers.get(name).push(callback);},
      dispatchEvent(event){event.target??=this;for(const callback of handlers.get(event.type)||[])callback(event);this['on'+event.type]?.(event);if(event.bubbles!==false)this.parentElement?.dispatchEvent(event);return !event.defaultPrevented;},
      showModal(){this.open=true;},close(){this.open=false;this.dispatchEvent({type:'close',bubbles:false});},
      reportValidity(){return true;},
    };
    node.classList={contains:name=>(attrs.get('class')||'').split(/\s+/).includes(name),add(...names){const classes=new Set((attrs.get('class')||'').split(/\s+/).filter(Boolean));names.forEach(name=>classes.add(name));attrs.set('class',[...classes].join(' '));},remove(...names){attrs.set('class',(attrs.get('class')||'').split(/\s+/).filter(name=>!names.includes(name)).join(' '));}};
    node.dataset=new Proxy({}, {get:(_target,name)=>attrs.get(dataName(name)),set:(_target,name,value)=>{attrs.set(dataName(name),String(value));return true;}});
    for(const name of ['id','className','type','name','href'])Object.defineProperty(node,name,{get:()=>attrs.get(name==='className'?'class':name)||'',set:value=>attrs.set(name==='className'?'class':name,String(value))});
    Object.defineProperties(node,{
      isConnected:{get(){let current=this;while(current.parentElement)current=current.parentElement;return current===document?.documentElement;}},
      value:{get(){if(assignedValue!==undefined)return assignedValue;if(this.tagName==='TEXTAREA')return this.textContent;if(this.tagName==='SELECT'){const options=this.querySelectorAll('option');const option=options.find(item=>item.hasAttribute('selected'))||options[0];return option?.getAttribute('value')??option?.textContent??'';}return decode(attrs.get('value')||'');},set:value=>{assignedValue=String(value);}},
      elements:{get(){const named=name=>this.querySelectorAll('input,select,textarea,button').find(item=>item.name===name)||null;return new Proxy({}, {get:(_target,name)=>name==='namedItem'?named:named(name)});}},
      textContent:{get:()=>ownText+node.children.map(child=>child.textContent).join(''),set:value=>{ownText=String(value);for(const child of [...node.children])child.remove();}},
      innerHTML:{get:()=>markup,set:html=>{markup=String(html);ownText='';for(const child of [...node.children])child.remove();const stack=[node],voids=new Set(['INPUT','IMG','LINK','META','BR','HR']);for(const token of markup.matchAll(/<\/?([a-z][\w-]*)\b([^>]*)>|([^<]+)/gi)){
        if(token[3]){stack.at(-1).appendText(token[3]);continue;}
        const name=token[1].toUpperCase();if(token[0].startsWith('</')){if(stack.at(-1).tagName===name)stack.pop();continue;}
        const child=element(name);for(const attr of token[2].matchAll(/([\w:-]+)(?:\s*=\s*(?:"([^"]*)"|'([^']*)'|([^\s>]+)))?/g))child.setAttribute(attr[1],decode(attr[2]??attr[3]??attr[4]??''));stack.at(-1).append(child);if(!voids.has(name)&&!token[0].endsWith('/>'))stack.push(child);
      }}},
    });
    return node;
  }
  const html=element('html'),body=element('body'),head=element('head');html.append(head,body);
  document={documentElement:html,body,head,activeElement:body,createElement:element,
    querySelector:selector=>html.querySelector(selector),querySelectorAll:selector=>html.querySelectorAll(selector),
    dispatchEvent:event=>{if(event.type==='wcp:operation')operations.push(event);return true;}};
  body.innerHTML='<main><div id="systemSettingsCenter"></div></main>';
  const context={document,URL,URLSearchParams,structuredClone,console,location:new URL(url),CustomEvent:class{constructor(type,{detail}={}){this.type=type;this.detail=detail;}},
    localStorage:{getItem:key=>{if(denied==='read')throw Error('Read denied');return storage.get(key)??null;},setItem:(key,value)=>{if(denied==='*'||denied===key)throw Error('Write denied');writes.push([key,value]);storage.set(key,value);},removeItem:key=>storage.delete(key)},
    addEventListener(name,callback){if(!windowListeners.has(name))windowListeners.set(name,[]);windowListeners.get(name).push(callback);},
  };
  context.window=context;context.history={replaceState(_state,_unused,next){context.location=new URL(next,context.location);}};if(runtime)context.WCPRuntime={};
  vm.createContext(context);
  for(const name of ['foundation.js','settings-model.js','settings-store.js','system-settings.js'])vm.runInContext(source(name),context,{filename:name});
  const find=selector=>document.querySelector(selector),all=selector=>document.querySelectorAll(selector);
  const flush=async()=>{for(let index=0;index<8;index++)await Promise.resolve();};
  const event=(type,target)=>({type,target,defaultPrevented:false,preventDefault(){this.defaultPrevented=true;}});
  return {context,document,storage,writes,operations,find,all,api:context.WCPSystemSettings,model:context.WCPSettingsModel,
    state:()=>context.WCPSystemSettings.getState(),activeWrites:()=>writes.filter(([key])=>key===context.WCPSettingsStore.key),
    async click(selector){const target=find(selector);assert.ok(target,'Missing click target '+selector);if(target.disabled)return;target.focus();target.dispatchEvent(event('click',target));await flush();},
    async change(selector,value,type='input'){const target=find(selector);assert.ok(target,'Missing change target '+selector);if(target.type==='checkbox')target.checked=value;else target.value=value;target.dispatchEvent(event(type,target));await flush();},
    async submit(){const form=find('dialog[open] form');assert.ok(form,'Missing review form');form.dispatchEvent(event('submit',form));await flush();},
    async hash(value){context.location.hash=value;for(const callback of windowListeners.get('hashchange')||[])callback({type:'hashchange'});await flush();},
    deny(value){denied=value;},
  };
}
const field=path=>'[data-ss-path="'+path+'"]';

await test('Full configuration sections and history have working navigation',async()=>{
  const h=harness();assert.equal(h.model.sections.length,24);assert.equal(h.all('[data-ss-nav] [data-ss-section]').length,26);
  for(const section of h.model.sections){await h.click('[data-ss-nav] [data-ss-section="'+section.id+'"]');assert.equal(h.find('#ss-section-title').textContent,section.title);assert.equal(h.all('[data-ss-path]').length,section.groups.reduce((count,group)=>count+group.fields.length,0));}
  await h.click('[data-ss-nav] [data-ss-section="history"]');assert.match(h.find('#ss-section-title').textContent,/Historique/);
});
await test('All 248 settings are reachable exactly once and reflected in the overview',()=>{
  const h=harness(),paths=[];
  for(const section of h.model.sections){h.api.navigate(section.id,false);paths.push(...h.all('[data-ss-path]').map(input=>input.dataset.ssPath));}
  assert.equal(paths.length,248);assert.equal(new Set(paths).size,248);assert.equal(String(h.find('[data-ss-metric="fields"]').textContent),'248');assert.equal(h.state().issues.length,0);
});
await test('Each rendered field has a unique associated label, help and error',()=>{
  const h=harness();for(const section of h.model.sections){h.api.navigate(section.id,false);const ids=h.all('[id]').map(node=>node.id);assert.equal(new Set(ids).size,ids.length);for(const input of h.all('[data-ss-path]')){assert.ok(h.find('label[for="'+input.id+'"]'));for(const id of input.getAttribute('aria-describedby').split(' '))assert.ok(h.find('#'+id),'Missing description '+id);}}
});
await test('Search finds editable fields beyond the selected section',async()=>{
  const h=harness();await h.change('[data-ss-search]','session');assert.ok(h.find(field('access.sessionMinutes')));assert.match(h.find('#ss-section-title').textContent,/résultat/);await h.change(field('access.sessionMinutes'),'45');assert.equal(h.state().draft.access.sessionMinutes,45);assert.equal(h.activeWrites().length,0);
});
await test('Search is accent and case insensitive and requires every term',async()=>{
  const h=harness();await h.change('[data-ss-search]','MEMOIRE');assert.ok(h.find(field('resource.ram')));await h.change('[data-ss-search]','contact securite');assert.ok(h.find(field('identity.securityEmail')));assert.equal(h.find(field('resource.ram')),null);await h.change('[data-ss-search]','contact inexistant');assert.equal(h.all('[data-ss-path]').length,0);assert.match(h.find('.ss-empty').textContent,/Aucun/);
});
await test('Search text is escaped and clearing restores the active section',async()=>{
  const h=harness();await h.change('[data-ss-search]','<script>');assert.equal(h.find('script'),null);await h.click('[data-ss-action="clear-search"]');assert.equal(h.find('[data-ss-search]').value,'');assert.ok(h.find(field('identity.hostname')));assert.equal(h.context.location.search,'');
});
await test('Editing only persists a draft and updates visible dirty state',async()=>{
  const h=harness();await h.change(field('identity.serverName'),'control-rebuilt');assert.equal(h.state().dirty,true);assert.equal(h.state().baseline.identity.serverName,'wcp-eu-01');assert.equal(h.state().draft.identity.serverName,'control-rebuilt');assert.equal(h.activeWrites().length,0);assert.ok(h.storage.has(h.context.WCPSettingsStore.draftKey));assert.equal(h.find('[data-ss-savebar]').dataset.dirty,'true');assert.equal(h.find('[data-ss-action="review"]').disabled,false);
});
await test('Resetting a field restores the baseline without changing the active revision',async()=>{
  const h=harness();await h.change(field('identity.serverName'),'temporary-name');await h.click('[data-ss-reset-field="identity.serverName"]');assert.equal(h.state().dirty,false);assert.equal(h.find(field('identity.serverName')).value,'wcp-eu-01');assert.equal(h.activeWrites().length,0);assert.equal(h.find('[data-ss-action="review"]').disabled,true);
});
await test('Discard is confirmed and removes the draft without a revision',async()=>{
  const h=harness();await h.change(field('identity.serverName'),'temporary-name');await h.click('[data-ss-action="discard"]');assert.ok(h.state().dirty);await h.click('dialog[open] .wcp-ui-form-actions .primary');assert.equal(h.state().dirty,false);assert.equal(h.storage.has(h.context.WCPSettingsStore.draftKey),false);assert.equal(h.activeWrites().length,0);
});
await test('Invalid review shows error summary and focuses the first invalid field',async()=>{
  const h=harness();await h.change(field('identity.hostname'),'localhost');await h.click('[data-ss-action="review"]');assert.equal(h.find('dialog[open]'),null);assert.equal(h.find('[data-ss-error-summary]').hidden,false);assert.equal(h.document.activeElement,h.find(field('identity.hostname')));assert.equal(h.find(field('identity.hostname')).getAttribute('aria-invalid'),'true');assert.equal(h.activeWrites().length,0);
});
await test('Valid review shows the actual before and after values',async()=>{
  const h=harness();await h.change(field('identity.hostname'),'panel.review.example');await h.click('[data-ss-action="review"]');const dialog=h.find('dialog[open]');assert.ok(dialog);assert.ok(dialog.classList.contains('ss-review-dialog'));assert.deepEqual(h.all('dialog[open] .ss-diff-row code').map(node=>node.textContent),['wcp-admin.example.com','panel.review.example']);assert.ok(h.find('dialog[open] [name="acknowledged"]'));assert.equal(h.activeWrites().length,0);
});
await test('Publishing requires a reason and acknowledgement and creates one revision and event',async()=>{
  const h=harness();await h.change(field('identity.hostname'),'panel.review.example');await h.click('[data-ss-action="review"]');await h.submit();assert.equal(h.activeWrites().length,0);assert.equal(h.find('[data-ss-review-feedback]').hidden,false);await h.change('dialog[open] [name="reason"]','Valider le nouveau nom du panneau');await h.submit();assert.equal(h.activeWrites().length,0);await h.change('dialog[open] [name="acknowledged"]',true);await h.submit();assert.equal(h.activeWrites().length,1);assert.equal(h.operations.length,1);assert.equal(h.state().version,1);assert.equal(h.state().dirty,false);assert.equal(h.state().baseline.identity.hostname,'panel.review.example');assert.equal(h.find('dialog[open]'),null);assert.equal(h.context.location.hash,'#history');assert.equal(h.state().history.filter(row=>row.current).length,1);
});
await test('Active storage failure keeps the dialog and draft without reporting success',async()=>{
  const h=harness();await h.change(field('identity.serverName'),'retained-draft');await h.click('[data-ss-action="review"]');await h.change('dialog[open] [name="reason"]','Ajuster le nom du serveur');h.deny(h.context.WCPSettingsStore.key);await h.submit();assert.equal(h.activeWrites().length,0);assert.equal(h.operations.length,0);assert.equal(h.state().version,0);assert.equal(h.state().dirty,true);assert.ok(h.find('dialog[open]'));assert.equal(h.find('[data-ss-review-feedback]').hidden,false);assert.match(h.find('[data-ss-review-feedback]').textContent,/pas été enregistrée/);assert.equal(h.find('[data-ss-notices] [data-tone="success"]'),null);
});
await test('Blocked draft storage is visible and never changes the active revision',async()=>{
  const h=harness();h.deny(h.context.WCPSettingsStore.draftKey);await h.change(field('identity.serverName'),'memory-only');assert.equal(h.state().draftStatus,'unsaved');assert.ok(h.find('[data-ss-notices] [role="alert"]'));assert.equal(h.activeWrites().length,0);assert.equal(h.state().baseline.identity.serverName,'wcp-eu-01');
});
await test('Legacy URL aliases resolve to the canonical sections',async()=>{
  const h=harness({url:'https://prototype.test/system-settings.html?section=providers'});assert.match(h.find('#ss-section-title').textContent,/Fournisseurs/);await h.hash('zerotrust');assert.match(h.find('#ss-section-title').textContent,/Zero Trust/);await h.hash('changes');assert.match(h.find('#ss-section-title').textContent,/Historique/);await h.hash('does-not-exist');assert.match(h.find('#ss-section-title').textContent,/Vue d’ensemble/);
});
await test('Canonical navigation updates URLs and URL search initializes editable results',()=>{
  const h=harness({url:'https://prototype.test/system-settings.html?section=network&q=session'});assert.ok(h.find(field('access.sessionMinutes')));h.api.navigate('security',false);assert.equal(h.context.location.hash,'#security');assert.equal(h.context.location.search,'');assert.equal(h.find('[data-ss-search]').value,'');
});
await test('History restoration prepares a draft while preserving the current revision',async()=>{
  const h=harness();await h.change(field('identity.serverName'),'history-target');await h.click('[data-ss-action="review"]');await h.change('dialog[open] [name="reason"]','Enregistrer le serveur de démonstration');await h.submit();const active=h.storage.get(h.context.WCPSettingsStore.key),baseline=h.state().history.find(row=>!row.current);assert.ok(baseline);await h.click('[data-ss-restore="'+baseline.id+'"]');await h.click('dialog[open] .wcp-ui-form-actions .primary');assert.equal(h.state().draft.identity.serverName,'wcp-eu-01');assert.equal(h.state().baseline.identity.serverName,'history-target');assert.equal(h.state().dirty,true);assert.equal(h.storage.get(h.context.WCPSettingsStore.key),active);assert.equal(h.activeWrites().length,1);assert.equal(h.operations.length,1);
});
await test('Saving without the runtime reports a local revision without a fabricated job',async()=>{
  const h=harness({runtime:false});await h.change(field('identity.serverName'),'local-only');await h.click('[data-ss-action="review"]');await h.change('dialog[open] [name="reason"]','Conserver cette configuration locale');await h.submit();assert.equal(h.state().version,1);assert.equal(h.operations.length,0);assert.match(h.find('[data-ss-notices]').textContent,/Aucun service système/);
});

await test('Restored incomplete numbers remain visibly invalid and cannot be reviewed as defaults',async()=>{
  const first=harness();first.api.navigate('resources',false);await first.change(field('resource.ram'),'');
  const h=harness({initial:Object.fromEntries(first.storage)});h.api.navigate('resources',false);
  assert.equal(h.find(field('resource.ram')).value,'');assert.equal(h.state().draft.resource.ram,'');
  await h.click('[data-ss-action="review"]');assert.equal(h.find('dialog[open]'),null);assert.equal(h.find(field('resource.ram')).getAttribute('aria-invalid'),'true');assert.equal(h.activeWrites().length,0);
});
await test('Corrupt saved drafts have a visible confirmed removal action',async()=>{
  const h=harness({initial:{'wcp360-system-settings-draft-v2':'{broken'}});
  assert.match(h.find('[data-ss-notices]').textContent,/brouillon enregistré est illisible/);assert.equal(h.state().dirty,false);
  await h.click('[data-ss-action="clear-draft"]');assert.equal(h.storage.has(h.context.WCPSettingsStore.draftKey),true);
  await h.click('dialog[open] .wcp-ui-form-actions .primary');assert.equal(h.storage.has(h.context.WCPSettingsStore.draftKey),false);assert.equal(h.find('[data-ss-action="clear-draft"]'),null);assert.equal(h.activeWrites().length,0);
});

await test('The default overview exposes all restored domains and grouped navigation',()=>{
  const h=harness({url:'https://prototype.test/system-settings.html'});
  assert.match(h.find('#ss-section-title').textContent,/Vue d’ensemble/);assert.equal(h.all('.ss-category').length,24);assert.equal(h.all('.ss-nav-group').length,h.model.navigationGroups.length+1);assert.equal(h.all('.ss-config-summary .ss-summary-item').length,4);
});
await test('Advanced search reaches editable runtime, mail and retention preferences',async()=>{
  const h=harness();await h.change('[data-ss-search]','mémoire php');assert.ok(h.find(field('runtime.phpMemory')));await h.change(field('runtime.phpMemory'),'512');
  h.api.navigate('mail',false);await h.change(field('mail.domainHourlyLimit'),'250');h.api.navigate('backups',false);await h.change(field('backups.retentionDays'),'21');
  assert.equal(h.state().draft.runtime.phpMemory,512);assert.equal(h.state().draft.mail.domainHourlyLimit,250);assert.equal(h.state().changes.length,3);assert.equal(h.activeWrites().length,0);
});
await test('Provider creation adds an editable draft record and rejects duplicates',async()=>{
  const h=harness();h.api.navigate('providers',false);await h.click('[data-ss-add="providers"]');
  await h.change('dialog[open] [name="name"]','DNS démo');await h.change('dialog[open] [name="endpoint"]','https://dns.example.com');await h.change('dialog[open] [name="secretRef"]','secret://demo/dns');await h.submit();
  assert.equal(h.state().draft.providers.length,1);assert.equal(h.state().baseline.providers.length,0);assert.equal(h.activeWrites().length,0);assert.match(h.find('.ss-record-copy').textContent,/DNS démo/);
  await h.click('[data-ss-add="providers"]');await h.change('dialog[open] [name="name"]','DNS démo');await h.change('dialog[open] [name="endpoint"]','https://dns.example.com');await h.submit();assert.ok(h.find('dialog[open]'));assert.match(h.find('[data-ss-record-feedback]').textContent,/existe déjà|déjà déclaré/);assert.equal(h.state().draft.providers.length,1);
});
await test('Provider editing and confirmed removal preserve the applied configuration',async()=>{
  const first=harness();first.api.navigate('providers',false);await first.click('[data-ss-add="providers"]');await first.change('dialog[open] [name="name"]','Original DNS');await first.change('dialog[open] [name="endpoint"]','https://dns.example.com');await first.submit();
  const h=harness({initial:Object.fromEntries(first.storage)});h.api.navigate('providers',false);await h.click('[data-ss-edit="providers"]');assert.equal(h.find('dialog[open] [name="name"]').value,'Original DNS');await h.change('dialog[open] [name="name"]','Updated DNS');await h.submit();assert.equal(h.state().draft.providers[0].name,'Updated DNS');
  await h.click('[data-ss-remove="providers"]');assert.equal(h.state().draft.providers.length,1);await h.click('dialog[open] .wcp-ui-form-actions .primary');assert.equal(h.state().draft.providers.length,0);assert.equal(h.state().dirty,false);assert.equal(h.activeWrites().length,0);
});
await test('Collection review includes real before and after records and requires acknowledgement',async()=>{
  const h=harness();h.api.navigate('storage',false);await h.click('[data-ss-add="storage.targets"]');
  for(const [name,value]of Object.entries({id:'primary',name:'Destination démo',endpoint:'https://storage.example.com',bucket:'backups',secretRef:'secret://demo/storage'}))await h.change('dialog[open] [name="'+name+'"]',value);
  await h.submit();assert.equal(h.state().draft.storage.targets[0].id,'primary');await h.click('[data-ss-action="review"]');assert.ok(h.find('dialog[open] [name="acknowledged"]'));assert.ok(h.all('dialog[open] .ss-diff-row code').some(node=>node.textContent.includes('Destination démo')));assert.equal(h.activeWrites().length,0);
  await h.change('dialog[open] [name="reason"]','Configurer la destination de démonstration');await h.change('dialog[open] [name="acknowledged"]',true);await h.submit();assert.equal(h.activeWrites().length,1);assert.equal(h.state().baseline.storage.targets[0].id,'primary');
});
await test('IPAM editor rejects overlapping pools without staging the invalid record',async()=>{
  const h=harness();h.api.navigate('ipam',false);await h.click('[data-ss-add="ipam.pools"]');for(const [name,value]of Object.entries({name:'Pool A',cidr:'192.0.2.0/24',gateway:'192.0.2.1'}))await h.change('dialog[open] [name="'+name+'"]',value);await h.submit();
  await h.click('[data-ss-add="ipam.pools"]');for(const [name,value]of Object.entries({name:'Pool B',cidr:'192.0.2.128/25'}))await h.change('dialog[open] [name="'+name+'"]',value);await h.submit();assert.match(h.find('[data-ss-record-feedback]').textContent,/chevauche/);assert.equal(h.state().draft.ipam.pools.length,1);assert.equal(h.activeWrites().length,0);
});
await test('Collection draft persistence failure remains visible with values in memory',async()=>{
  const h=harness();h.api.navigate('providers',false);h.deny(h.context.WCPSettingsStore.draftKey);await h.click('[data-ss-add="providers"]');await h.change('dialog[open] [name="name"]','Memory DNS');await h.change('dialog[open] [name="endpoint"]','https://dns.example.com');await h.submit();assert.equal(h.state().draft.providers[0].name,'Memory DNS');assert.equal(h.state().draftStatus,'unsaved');assert.match(h.find('[data-ss-notices]').textContent,/uniquement en mémoire/);assert.equal(h.operations.length,0);
});

console.log(JSON.stringify({suite:'WCP360 System Settings controller',behaviorChecks:checks,status:'passed',coverage:'Real foundation, settings model, store and controller through a minimal DOM/storage adapter. Native dialog modality, form validity, CSS layout and browser accessibility are not verified.'},null,2));
