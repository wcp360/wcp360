import {readFileSync} from 'node:fs';
import {fileURLToPath} from 'node:url';
import vm from 'node:vm';
import assert from 'node:assert/strict';

const source=name=>readFileSync(fileURLToPath(new URL('../assets/'+name,import.meta.url)),'utf8');
let checks=0;
async function test(message,run){await run();checks++;}

function formHarness(){
  const nodes=new Map(),handlers=new Map();
  const node=()=>({children:[],attrs:new Map(),disabled:false,isConnected:true,value:'',
    getAttribute(name){return this.attrs.get(name)??null;},setAttribute(name,value){this.attrs.set(name,String(value));},removeAttribute(name){this.attrs.delete(name);},
    append(...items){this.children.push(...items);},prepend(...items){this.children.unshift(...items);},replaceChildren(...items){this.children=items;},
    insertAdjacentElement(_,element){nodes.set(element.id,element);},remove(){this.isConnected=false;},focus(){this.focused=true;},
    addEventListener(type,listener){handlers.set(type,listener);},removeEventListener(type){handlers.delete(type);}
  });
  const field=node();field.id='name';field.value='Demo';
  const save=node(),other=node(),locked=node();locked.disabled=true;
  const form=node();form.noValidate=false;form.elements={namedItem:()=>field};form.querySelectorAll=()=>[save,other,locked];
  const document={createElement:node,getElementById:id=>nodes.get(id),querySelector:()=>null};
  const context={document,structuredClone};vm.runInNewContext(source('foundation.js'),context);
  return {UI:context.WCPUI,form,field,save,other,locked,submit:submitter=>handlers.get('submit')({submitter,preventDefault(){},stopImmediatePropagation(){}})};
}

await test('Concurrent valid submissions run one operation and restore every submit control',async()=>{
  const h=formHarness();let calls=0,release;
  h.UI.Form.attach(h.form,{fields:[{name:'name',required:true}],onSubmit:()=>{calls++;return new Promise(resolve=>release=resolve);}});
  const pending=h.submit(h.save);await h.submit(h.other);await h.submit();
  assert.equal(calls,1);assert.equal(h.form.getAttribute('aria-busy'),'true');assert.ok(h.save.disabled&&h.other.disabled&&h.locked.disabled);
  release();await pending;assert.equal(h.form.getAttribute('aria-busy'),null);assert.equal(h.save.disabled,false);assert.equal(h.other.disabled,false);assert.equal(h.locked.disabled,true);
  const next=h.submit(h.other);assert.equal(calls,2);release();await next;
});
await test('Invalid forms focus the field and do not enter a busy state',async()=>{
  const h=formHarness();let calls=0;h.field.value=' ';
  h.UI.Form.attach(h.form,{fields:[{name:'name',required:true}],onSubmit:()=>calls++});await h.submit(h.save);
  assert.equal(calls,0);assert.equal(h.field.focused,true);assert.equal(h.save.disabled,false);assert.equal(h.form.getAttribute('aria-busy'),null);
});
await test('Rejected async operations release the form and preserve pre-existing ARIA state',async()=>{
  const h=formHarness();h.form.setAttribute('aria-busy','false');let calls=0;
  h.UI.Form.attach(h.form,{fields:[{name:'name',required:true}],onSubmit:async()=>{calls++;throw null;}});
  await h.submit(h.save);await h.submit(h.other);assert.equal(calls,2);assert.equal(h.form.getAttribute('aria-busy'),'false');assert.equal(h.save.disabled,false);
});
await test('Detaching a pending form restores native validation without changing its replacement',async()=>{
  const h=formHarness();let release;
  const old=h.UI.Form.attach(h.form,{fields:[{name:'name'}],onSubmit:()=>new Promise(resolve=>release=resolve)});const pending=h.submit(h.save);old.destroy();
  assert.equal(h.form.noValidate,false);assert.equal(h.form.getAttribute('aria-busy'),null);assert.equal(h.save.disabled,false);
  h.UI.Form.attach(h.form,{fields:[{name:'name'}]});h.form.setAttribute('aria-busy','replacement');release();await pending;
  assert.equal(h.form.getAttribute('aria-busy'),'replacement');assert.equal(h.form.noValidate,true);
});

function runtimeHarness(jobs){
  let time=1800000000000,sequence=0;const handlers=new Map(),intervals=[],shared=new Map();let document;
  const node=()=>{const classes=new Set();return {innerHTML:'',children:[],inert:false,isConnected:true,scrollTop:0,style:{overflow:''},attrs:new Map(),
    classList:{add:value=>classes.add(value),remove:value=>classes.delete(value),contains:value=>classes.has(value)},
    setAttribute(name,value){this.attrs.set(name,String(value));},getAttribute(name){return this.attrs.get(name)??null;},hasAttribute(name){return this.attrs.has(name);},
    contains(target){return this===target||this.children.includes(target);},querySelectorAll(){return [];},focus(){document.activeElement=this;},closest(){return null;}
  };};
  const list=node(),close=node(),panel=node(),backdrop=node(),inline=node(),feed=node(),app=node(),alreadyInert=node(),launcher=node();alreadyInert.inert=true;
  panel.children=[list,close];panel.querySelector=selector=>selector==='.runtime-job-list'?list:close;
  const elements=new Map([['.runtime-job-center',panel],['.runtime-backdrop',backdrop],['[data-runtime-page]',inline],['[data-runtime-notifications]',feed]]);
  document={body:{dataset:{accessScope:'user'},children:[app,alreadyInert,panel,backdrop],style:{overflow:'auto'},append(){}},activeElement:launcher,createElement:node,querySelector:selector=>elements.get(selector)||null,querySelectorAll:()=>[],
    addEventListener(type,listener){const list=handlers.get(type)||[];list.push(listener);handlers.set(type,list);},dispatchEvent(event){for(const listener of handlers.get(event.type)||[])listener(event);}
  };
  class Clock extends Date{constructor(...args){super(...(args.length?args:[time]));}static now(){return time;}}
  shared.set('wcp360-runtime-v2',JSON.stringify({jobs,notifications:[]}));
  const context={document,window:{WCPUI:{toast(){}},addEventListener(){}},location:{search:'',hash:''},localStorage:{getItem:key=>shared.get(key),setItem:(key,value)=>shared.set(key,value)},Date:Clock,crypto:{randomUUID:()=>String(++sequence)},structuredClone,URLSearchParams,CustomEvent:class{constructor(type,options={}){this.type=type;this.detail=options.detail;}},setInterval:callback=>intervals.push(callback)};
  vm.runInNewContext(source('product-runtime.js'),context);
  return {api:context.window.WCPRuntime,document,panel,app,alreadyInert,launcher,inline,dispatch:(type,detail)=>document.dispatchEvent({type,detail}),input:value=>document.dispatchEvent({type:'input',target:{matches:()=>true,value}}),advance:ms=>{time+=ms;intervals.forEach(callback=>callback());}};
}
const job=(id,extra={})=>({id,name:id,scope:'user',status:'scheduled',createdAt:1800000000000,...extra});
await test('A running job already at 100 percent completes exactly once',()=>{
  const h=runtimeHarness([job('done',{status:'running',progress:100})]);assert.equal(h.api.getState().jobs[0].status,'completed');
  const count=h.api.getState().notifications.length;h.advance(40000);assert.equal(h.api.getState().notifications.length,count);
});
await test('Corrupt stored IDs and states recover without making a duplicate job actionable',()=>{
  const h=runtimeHarness([job('same'),job('same'),job('invalid',{status:'unknown',duration:'Infinity'}),job('missing',{id:null})]);
  assert.equal(h.api.getState().jobs.length,2);const invalid=h.api.getState().jobs.find(item=>item.id==='invalid');assert.equal(invalid.status,'failed');assert.equal(invalid.duration,36000);
});
await test('Completed and scheduled progress remain consistent with their status',()=>{
  const h=runtimeHarness([job('complete',{status:'completed',progress:12}),job('waiting',{progress:80})]);
  assert.equal(h.api.getState().jobs[0].progress,100);assert.equal(h.api.getState().jobs[1].progress,0);
});
await test('Job search handles accents and remains inside the current portal',()=>{
  const h=runtimeHarness([job('one',{name:'École backup'}),job('two',{name:'Other backup'}),job('root',{scope:'root',name:'École ROOT'})]);h.input('ECOLE');
  assert.ok(h.inline.innerHTML.includes('École backup'));assert.ok(!h.inline.innerHTML.includes('Other backup'));assert.ok(!h.inline.innerHTML.includes('École ROOT'));
});
await test('Another overlay releases the Job Center and restores existing inert and scroll states',()=>{
  const h=runtimeHarness([job('one')]);h.api.openJobs();assert.equal(h.app.inert,true);assert.equal(h.document.activeElement,h.panel.querySelector('[data-runtime-close]'));assert.equal(h.document.body.style.overflow,'hidden');
  h.dispatch('wcp:overlay-opening',{source:'command'});assert.equal(h.panel.inert,true);assert.equal(h.app.inert,false);assert.equal(h.alreadyInert.inert,true);assert.equal(h.document.body.style.overflow,'auto');assert.equal(h.document.activeElement,h.launcher);
});
await test('Switching from another overlay restores an available launcher',()=>{
  const h=runtimeHarness([job('one')]);let hidden=false;
  h.document.activeElement={isConnected:true,closest:()=>hidden?{}:null,focus(){throw new Error('An inert overlay trigger must not receive focus.');}};
  h.document.addEventListener('wcp:overlay-opening',event=>{if(event.detail.source==='runtime'){hidden=true;h.document.activeElement=h.launcher;}});
  h.api.openJobs();h.dispatch('wcp:overlay-opening',{source:'experience'});assert.equal(h.document.activeElement,h.launcher);
});
console.log(JSON.stringify({suite:'WCP360 interaction state regressions',checks,status:'passed',coverage:'Async form controllers and Job Center DOM adapters. Native focus, layout and browser rendering require a separate browser review.'},null,2));
