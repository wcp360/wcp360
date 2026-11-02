import {readFileSync} from 'node:fs';
import vm from 'node:vm';
import assert from 'node:assert/strict';

const source=readFileSync(new URL('../assets/governance-centers.js',import.meta.url),'utf8');
let checks=0;
function test(name,run){try{run();checks++;}catch(error){throw new Error(name+': '+error.message,{cause:error});}}
const modePages={continuity:'continuity-control.html',privacy:'privacy-governance.html',delivery:'delivery-platform.html'};
function harness(mode,{saved,raw,blocked=false,prompts=[]}={}){
  const key='wcp360-governance-'+mode+'-v1',storage=new Map(),events=[],notices=[],listeners=new Map(),host={innerHTML:''};
  if(raw!==undefined)storage.set(key,raw);else if(saved!==undefined)storage.set(key,JSON.stringify(saved));
  let writes=0;
  const document={body:{dataset:{governanceCenter:mode}},querySelector:selector=>selector==='#governanceCenter'?host:selector==='#wcpToastStack'?{appendChild:node=>notices.push(node.innerHTML)}:null,createElement:()=>({innerHTML:'',remove(){}}),addEventListener:(type,callback)=>listeners.set(type,callback),dispatchEvent:event=>events.push(event)};
  class Clock extends Date{static now(){return 1800000000000;}}
  const context={document,Date:Clock,URL,structuredClone,location:{pathname:'/'+modePages[mode]},localStorage:{getItem:name=>storage.get(name)||null,setItem:(name,value)=>{if(blocked)throw new Error('storage unavailable');writes++;storage.set(name,value);}},prompt:()=>prompts.length?prompts.shift():null,setTimeout(){},CustomEvent:class{constructor(type,{detail}={}){this.type=type;this.detail=detail;}}};
  vm.runInNewContext(source,context);
  const click=(selector,dataset)=>listeners.get('click')?.({target:{closest:value=>value===selector?{dataset}:null}});
  return {host,storage,events,notices,key,get writes(){return writes;},state:()=>storage.has(key)?JSON.parse(storage.get(key)):null,action:id=>click('[data-gov-action]',{govAction:id}),tab:id=>click('[data-gov-tab]',{govTab:id})};
}

test('Malformed saved objects and null collections recover in all three centers',()=>{
  for(const mode of Object.keys(modePages))for(const saved of [null,[],{tab:'missing',vips:null,replicas:null,requests:null,residency:{},providers:null,runners:null}]){
    const h=harness(mode,{saved});assert.ok(h.host.innerHTML.includes('<h2>'));assert.equal(h.writes,0);assert.equal(h.events.length,0);
  }
  assert.ok(harness('continuity',{raw:'{broken'}).host.innerHTML.includes('Floating addresses'));
});
test('Invalid persisted rows are discarded without breaking valid rows or empty inventories',()=>{
  const h=harness('privacy',{saved:{requests:[null,{},['short'],['DSR-1','owner@example.com','account','Export','Tomorrow','Review']],residency:[]}});
  assert.ok(h.host.innerHTML.includes('DSR-1'));assert.equal(h.host.innerHTML.includes('DSR-1184'),false);h.tab('residency');assert.equal(h.state().residency.length,0);
});
test('Valid IPv4 and IPv6 input persists once and emits one local ROOT operation',()=>{
  for(const input of [' 203.0.113.22 ','2001:DB8:360:0:0:0:0:99','::ffff:192.0.2.1']){
    const h=harness('continuity',{prompts:[input]});h.action('add-vip');assert.equal(h.state().vips.length,4);assert.equal(h.writes,1);assert.equal(h.events.length,1);assert.equal(h.events[0].detail.scope,'root');assert.equal(h.events[0].type,'wcp:operation');
  }
});
test('Invalid address formats leave storage and job events untouched',()=>{
  for(const input of ['', 'not-an-ip','256.2.3.4','01.2.3.4','127.1','1.2.3.4/24','2001:::1','2001:db8::1%eth0','<img src=x>']){
    const h=harness('continuity',{prompts:[input]});h.action('add-vip');assert.equal(h.writes,0,input);assert.equal(h.events.length,0,input);assert.ok(h.notices.at(-1).includes('Action blocked'),input);
  }
});
test('Duplicate addresses compare canonical IPv6 forms and IPv4 strings',()=>{
  for(const input of ['203.0.113.20','2001:0db8:0360:0000:0000:0000:0000:0020']){
    const h=harness('continuity',{prompts:[input]});h.action('add-vip');assert.equal(h.writes,0);assert.equal(h.events.length,0);assert.ok(h.notices.at(-1).includes('already exists'));
  }
});
test('Display names remain literal text through provider creation and reload',()=>{
  const value="<img src=x onerror='demo'> & Lab",h=harness('delivery',{prompts:[value]});h.action('provider');assert.equal(h.state().providers.at(-1)[0],value);assert.equal(h.host.innerHTML.includes(value),false);assert.ok(h.host.innerHTML.includes('&lt;img src=x onerror=&#39;demo&#39;&gt; &amp; Lab'));
  const reloaded=harness('delivery',{saved:h.state()});assert.ok(reloaded.host.innerHTML.includes('&lt;img'));assert.equal(reloaded.host.innerHTML.includes('<img'),false);
});
test('Persisted subject, runner and row text is escaped and runner style stays numeric',()=>{
  const text="<img src=x> & 'quoted'",privacy=harness('privacy',{saved:{requests:[["DSR-' data-injected='yes",text,text,'Export',text,'Review']]}});
  assert.equal(privacy.host.innerHTML.includes('<img'),false);assert.equal(privacy.host.innerHTML.includes("data-gov-action='request:DSR-' data-injected"),false);assert.ok(privacy.host.innerHTML.includes('DSR-&#39;'));
  const delivery=harness('delivery',{saved:{tab:'runners',runners:[[text,text,text,"1%;background:url('x')",text]]}});assert.equal(delivery.host.innerHTML.includes('<img'),false);assert.ok(delivery.host.innerHTML.includes("style='width:0%'"));
});
test('Provider, replica and runner duplication or invalid names produce no writes or jobs',()=>{
  for(const [mode,action,value] of [['delivery','provider','github'],['delivery','provider','   '],['delivery','runner','RUNNER-PAR-01'],['delivery','runner','bad runner'],['delivery','runner','<img>'],['continuity','add-replica','MariaDB primary']]){
    const h=harness(mode,{prompts:[value]});h.action(action);assert.equal(h.writes,0);assert.equal(h.events.length,0);
  }
});
test('Subject email validation and duplicate requests do not mutate state',()=>{
  for(const subject of ['not-an-email','<img>@example.com','OWNER@example.com','']){const h=harness('privacy',{prompts:[subject]});h.action('new-request');assert.equal(h.writes,0);assert.equal(h.events.length,0);}
});
test('Requests created in the same millisecond retain distinct IDs and local preview jobs',()=>{
  const h=harness('privacy',{prompts:['new-a@example.com','new-b@example.com']});h.action('new-request');h.action('new-request');assert.equal(h.state().requests.length,5);assert.equal(new Set(h.state().requests.map(row=>row[0])).size,5);assert.equal(h.events.length,2);
});
test('Missing, blocked and already pending privacy requests never queue twice',()=>{
  const h=harness('privacy',{prompts:['ERASE','ERASE']});h.action('request:missing');h.action('request:DSR-1173');assert.equal(h.events.length,0);h.action('request:DSR-1184');assert.equal(h.events.length,1);h.action('request:DSR-1184');assert.equal(h.events.length,1);assert.equal(h.state().requests.find(row=>row[0]==='DSR-1184')[5],'Approval');
});
test('Failed persistence rolls back the visible mutation and does not enqueue an operation',()=>{
  const h=harness('delivery',{blocked:true,prompts:['New provider']});h.action('provider');assert.equal(h.events.length,0);assert.equal(h.state(),null);h.tab('providers');assert.equal(h.host.innerHTML.includes('<strong>New provider</strong>'),false);assert.ok(h.notices.at(-1).includes('No change was saved'));
});
test('Cancelled input and actions from another center are harmless',()=>{
  const h=harness('continuity',{prompts:[null]});h.action('add-vip');h.action('provider');h.tab('runners');assert.equal(h.writes,0);assert.equal(h.events.length,0);
});
console.log(JSON.stringify({suite:'WCP360 governance centers',pages:Object.values(modePages),behaviorChecks:checks,status:'passed',boundary:'Real controller with DOM, prompt, storage and event adapters. No browser layout, network, privacy execution or infrastructure changes.'},null,2));
