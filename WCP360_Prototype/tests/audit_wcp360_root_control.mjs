import {readFileSync} from 'node:fs';
import vm from 'node:vm';
import assert from 'node:assert/strict';

const source=readFileSync(new URL('../assets/root-control.js',import.meta.url),'utf8');
const pages={services:'service-manager.html',ip:'ip-management.html',automation:'api-automation.html',transfers:'transfers.html'};
let checks=0;
const test=(name,run)=>{try{run();checks++;}catch(error){throw new Error(name+': '+error.message,{cause:error});}};

// Minimal HTML tree/event adapter. This does not implement rendering, native
// validation or a browser accessibility tree.
function harness(module,{saved,raw,blocked=false}={}){
  const storageKey='wcp360-root-control-drafts-v1:'+module,storage=new Map(),events=[];
  if(raw!==undefined)storage.set(storageKey,raw);else if(saved!==undefined)storage.set(storageKey,JSON.stringify(saved));
  const decode=text=>text.replace(/&(?:amp|lt|gt|quot|#39);/g,value=>({'&amp;':'&','&lt;':'<','&gt;':'>','&quot;':'"','&#39;':"'"}[value]));
  function matches(node,selector){
    let result=true;
    selector=selector.replace(/\[([\w-]+)(?:(\^?=)["']([^"']*)["'])?\]/g,(_all,name,operator,value)=>{const actual=node.attrs[name];if(actual===undefined||operator==='='&&actual!==value||operator==='^='&&!actual.startsWith(value))result=false;return '';});
    if(!result)return false;
    const tag=selector.match(/^[a-z][\w-]*/i)?.[0];if(tag&&node.tagName!==tag)return false;
    const id=selector.match(/#([\w-]+)/)?.[1];if(id&&node.attrs.id!==id)return false;
    return [...selector.matchAll(/\.([\w-]+)/g)].every(match=>(node.attrs.class||'').split(/\s+/).includes(match[1]));
  }
  function element(tag='div',attrs={}){
    const handlers=new Map(),node={tagName:tag,attrs,children:[],parentElement:null,style:{},dataset:{},hidden:'hidden' in attrs,value:attrs.value||'',tabIndex:0,ownText:'',handlers};
    for(const [name,value] of Object.entries(attrs))if(name.startsWith('data-'))node.dataset[name.slice(5).replace(/-([a-z])/g,(_all,char)=>char.toUpperCase())]=value;
    node.classList={add(name){const values=new Set((node.attrs.class||'').split(/\s+/));values.add(name);node.attrs.class=[...values].join(' ');},toggle(name,on){const values=new Set((node.attrs.class||'').split(/\s+/));on?values.add(name):values.delete(name);node.attrs.class=[...values].join(' ');},contains:name=>(node.attrs.class||'').split(/\s+/).includes(name)};
    node.setAttribute=(name,value)=>node.attrs[name]=String(value);
    node.append=(...items)=>{for(const item of items){if(item.parentElement)item.parentElement.children=item.parentElement.children.filter(child=>child!==item);item.parentElement=node;node.children.push(item);}};
    node.querySelectorAll=selector=>{const result=[];const visit=parent=>{for(const child of parent.children){if(matches(child,selector))result.push(child);visit(child);}};visit(node);return result;};
    node.querySelector=selector=>node.querySelectorAll(selector)[0]||null;
    node.closest=selector=>{for(let current=node;current;current=current.parentElement)if(matches(current,selector))return current;return null;};
    node.addEventListener=(name,callback)=>{const list=handlers.get(name)||[];list.push(callback);handlers.set(name,list);};
    node.fire=(name,event={})=>{for(const callback of handlers.get(name)||[])callback({target:node,preventDefault(){},...event});};
    node.focus=()=>node.focused=true;
    Object.defineProperty(node,'cells',{get:()=>node.children.filter(child=>child.tagName==='td')});
    Object.defineProperty(node,'textContent',{get:()=>node.ownText+node.children.map(child=>child.textContent).join(''),set:value=>{node.children=[];node.ownText=String(value);}});
    Object.defineProperty(node,'innerHTML',{get:()=>node.raw||'',set:value=>{
      node.raw=String(value);node.children=[];node.ownText='';const stack=[node];
      for(const match of node.raw.matchAll(/<\/?([a-z][\w-]*)\b[^>]*>|([^<]+)/gi)){
        if(match[2]){stack.at(-1).ownText+=decode(match[2]);continue;}
        const token=match[0],tag=match[1].toLowerCase();if(token.startsWith('</')){const index=stack.findLastIndex(item=>item.tagName===tag);if(index>0)stack.length=index;continue;}
        const attrs={};for(const attr of token.slice(1+tag.length,-1).matchAll(/([^\s=/>]+)(?:\s*=\s*(?:"([^"]*)"|'([^']*)'|([^\s>]+)))?/g))attrs[attr[1]]=decode(attr[2]??attr[3]??attr[4]??'');
        const child=element(tag,attrs);stack.at(-1).append(child);if(!['input','br','hr','img','meta','link'].includes(tag))stack.push(child);
      }
    }});
    return node;
  }
  const body=element('body'),host=element('div',{id:'rootControl'});body.dataset.rootModule=module;body.append(host);
  const document={body,querySelector:selector=>body.querySelector(selector),querySelectorAll:selector=>body.querySelectorAll(selector),createElement:element,dispatchEvent:event=>events.push(event)};
  const overlays=[];
  const WCPUI={Overlay:{attach(options){const api={options,opened:false,open(){api.opened=true;},close(){api.opened=false;}};overlays.push(api);return api;}}};
  vm.runInNewContext(source,{document,WCPUI,structuredClone,Date,localStorage:{getItem:key=>storage.get(key)||null,setItem:(key,value)=>{if(blocked)throw Error('blocked');storage.set(key,value);}},CustomEvent:class{constructor(type,{detail}={}){this.type=type;this.detail=detail;}}});
  return {host,body,events,storage,storageKey,overlays,state:()=>JSON.parse(storage.get(storageKey)||'null'),find:selector=>body.querySelector(selector),all:selector=>body.querySelectorAll(selector),click:selector=>body.querySelector(selector).fire('click'),manage:target=>host.querySelectorAll('[data-root-manage]').find(button=>button.closest('tr').cells[0].textContent.trim()===target).fire('click'),values:()=>body.querySelector('form').querySelectorAll('input').map(input=>input.value),submit(values){values?.forEach((value,index)=>body.querySelector('form').querySelectorAll('input')[index].value=value);body.querySelector('form').fire('submit');}};
}

test('All four modules mount separate functional tab panels',()=>{
  for(const module of Object.keys(pages)){
    const h=harness(module);assert.equal(h.all('[data-root-panel]').filter(panel=>!panel.hidden).length,1);
    for(const view of ['configuration','policies','activity','overview']){h.click('[data-root-view="'+view+'"]');assert.equal(h.all('[data-root-panel]').filter(panel=>!panel.hidden)[0].dataset.rootPanel,view);assert.equal(h.find('[data-root-view="'+view+'"]').attrs['aria-selected'],'true');}
    assert.ok(h.find('[data-root-panel="configuration"]').textContent.includes('No local drafts'));assert.ok(h.find('[data-root-panel="policies"]').textContent.includes('safeguards'));
  }
});
test('Manage initializes each selected row target rather than the first service',()=>{
  const h=harness('services');h.manage('mariadb');assert.equal(h.values()[0],'mariadb');h.manage('php-fpm');assert.equal(h.values()[0],'php-fpm');assert.equal(h.find('#rootDrawerTitle').textContent,'Manage php-fpm');
});
test('Healthy filter excludes paused resources and supports the inactive view',()=>{
  const h=harness('automation'),select=h.find('[data-root-status]');select.value='healthy';select.fire('change');
  const rows=h.all('[data-root-row]');assert.equal(rows.find(row=>row.dataset.rootState==='paused').hidden,true);assert.equal(rows.filter(row=>!row.hidden).length,3);
  select.value='warning';select.fire('change');assert.deepEqual(rows.filter(row=>!row.hidden).map(row=>row.dataset.rootState),['paused']);
});
test('Submitting captures real values and target in persistent draft and simulation event',()=>{
  const h=harness('services');h.manage('mariadb');h.submit(['mariadb','Selected database nodes','Validate configuration','Tonight']);
  const state=h.state();assert.equal(state.drafts.mariadb.values[1],'Selected database nodes');assert.equal(h.events.length,1);assert.equal(h.events[0].detail.target,'mariadb');assert.equal(h.events[0].detail.inputs['Target scope'],'Selected database nodes');assert.equal(h.events[0].detail.demo,true);assert.equal(h.overlays[0].opened,false);assert.ok(h.find('.root-feedback').textContent.includes('no service or infrastructure was changed'));
  h.click('[data-root-view="activity"]');assert.ok(h.find('.root-activity-list').textContent.includes('Tonight'));
});
test('Draft values remain isolated by target and survive reload',()=>{
  const h=harness('services');h.manage('mariadb');h.submit(['mariadb','Database pool','Reload preview','Tomorrow']);h.manage('nginx');h.submit(['nginx','Web pool','Health preview','Now']);
  const reload=harness('services',{saved:h.state()});reload.manage('mariadb');assert.equal(reload.values()[1],'Database pool');assert.equal(reload.values()[3],'Tomorrow');reload.manage('nginx');assert.equal(reload.values()[1],'Web pool');
  reload.click('[data-root-view="configuration"]');const reopen=reload.all('[data-root-reopen]').find(button=>button.dataset.rootReopen==='mariadb');reopen.fire('click');assert.equal(reload.values()[1],'Database pool');
});
test('Storage failure keeps the editor open and never shows a success or queues a job',()=>{
  const h=harness('ip',{blocked:true});h.manage('203.0.113.24');h.submit(['203.0.113.24','Dedicated IP','example.com','mail.example.com']);assert.equal(h.state(),null);assert.equal(h.events.length,0);assert.equal(h.overlays[0].opened,true);assert.equal(h.find('.root-form-feedback').hidden,false);assert.ok(h.find('.root-form-feedback').textContent.includes('no operation was queued'));assert.equal(h.find('.root-feedback').classList.contains('show'),false);
});
test('Corrupted storage recovers without creating fake history or throwing',()=>{
  for(const raw of ['null','{broken','[]','{"drafts":null,"activity":null}','{"drafts":{"x":{"values":null}},"activity":[null,{}]}']){const h=harness('services',{raw});h.manage('mariadb');assert.equal(h.values()[0],'mariadb');assert.equal(h.events.length,0);assert.ok(h.find('.root-activity-list').textContent.includes('No local workflow'));}
});
test('Empty fields are rejected without persistence or events',()=>{
  const h=harness('transfers');h.click('[data-root-primary]');h.submit(['Remote','   ','Destination','Copy']);assert.equal(h.state(),null);assert.equal(h.events.length,0);assert.equal(h.find('.root-form-feedback').hidden,false);
});
test('Draft text and targets are rendered as text rather than markup after reload',()=>{
  const h=harness('automation');h.click('[data-root-primary]');h.submit(['<img src=x>','Read & manage','30 days','Owner']);const reload=harness('automation',{saved:h.state()});assert.equal(reload.find('[data-root-config-list]').innerHTML.includes('<img src=x>'),false);assert.ok(reload.find('[data-root-config-list]').innerHTML.includes('&lt;img src=x&gt;'));assert.equal(reload.all('img').length,0);
});
test('Tabs support arrow navigation with one selected tab and matching panel',()=>{
  const h=harness('services');h.find('[data-root-view="overview"]').fire('keydown',{key:'ArrowRight'});assert.equal(h.find('[data-root-view="configuration"]').focused,true);assert.equal(h.find('[data-root-panel="configuration"]').hidden,false);assert.equal(h.all('[data-root-view]').filter(tab=>tab.attrs['aria-selected']==='true').length,1);
});
console.log(JSON.stringify({suite:'WCP360 ROOT module controls',pages:Object.values(pages),behaviorChecks:checks,status:'passed',storage:'wcp360-root-control-drafts-v1:<module>',boundary:'Real controller through HTML-tree, event, overlay and storage adapters. No native browser validation, rendering or infrastructure actions.'},null,2));
