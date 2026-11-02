import {readFileSync} from 'node:fs';
import {fileURLToPath} from 'node:url';
import vm from 'node:vm';
import assert from 'node:assert/strict';

const root=new URL('../',import.meta.url);
const source=name=>readFileSync(fileURLToPath(new URL('assets/'+name,root)),'utf8');
let checks=0;
function test(name,run){try{run();checks++;}catch(error){throw new Error(name+': '+error.message,{cause:error});}}

// Deliberately small DOM adapter: tree membership, attributes, listeners and focus.
// getClientRects only marks adapter nodes as available; no CSS, rendering, native
// focus navigation, accessibility tree or browser layout is simulated or certified.
function harness({width=1400,legacy=false,experience=false,saved,storageBlocked=false}={}){
  const listeners=new Map(),windowListeners=new Map(),timers=[];
  let document;
  const dataName=name=>'data-'+name.replace(/[A-Z]/g,char=>'-'+char.toLowerCase());
  function splitSelector(selector){return selector.split(',').map(value=>value.trim()).filter(Boolean);}
  function matchesSimple(node,selector){
    if(!node||!selector)return false;
    let valid=true;
    selector=selector.replace(/:not\(([^)]+)\)/g,(_all,excluded)=>{if(matchesSimple(node,excluded))valid=false;return '';});
    if(!valid)return false;
    if(selector.includes(':last-child')){if(node.parentElement?.children.at(-1)!==node)return false;selector=selector.replace(':last-child','');}
    for(const match of selector.matchAll(/\[([^\]=\s]+)(?:=["']?([^\]"']*)["']?)?\]/g)){
      if(!node.hasAttribute(match[1]))return false;
      if(match[2]!==undefined&&node.getAttribute(match[1])!==match[2])return false;
    }
    selector=selector.replace(/\[[^\]]+\]/g,'');
    const tag=selector.match(/^[a-z][\w-]*/i)?.[0];if(tag&&node.tagName!==tag.toUpperCase())return false;
    const id=selector.match(/#([\w-]+)/)?.[1];if(id&&node.id!==id)return false;
    return [...selector.matchAll(/\.([\w-]+)/g)].every(match=>node.classList.contains(match[1]));
  }
  function matches(node,selector){return splitSelector(selector).some(part=>{
    const pieces=part.split(/\s+(?![^\[]*\])/);if(!matchesSimple(node,pieces.pop()))return false;
    let ancestor=node.parentElement;
    while(pieces.length){const expected=pieces.pop();while(ancestor&&!matchesSimple(ancestor,expected))ancestor=ancestor.parentElement;if(!ancestor)return false;ancestor=ancestor.parentElement;}
    return true;
  });}
  function element(tag='div'){
    const attrs=new Map(),handlers=new Map();let ownText='',markup='';
    const node={tagName:tag.toUpperCase(),attrs,handlers,children:[],parentElement:null,style:{},disabled:false,inert:false,hidden:false,value:'',
      setAttribute(name,value){attrs.set(name,String(value));if(name==='inert'||name==='hidden'||name==='disabled')this[name]=true;},
      getAttribute(name){if(['inert','hidden','disabled'].includes(name))return this[name]?'':null;return attrs.get(name)??null;},
      hasAttribute(name){return this.getAttribute(name)!==null;},removeAttribute(name){attrs.delete(name);if(['inert','hidden','disabled'].includes(name))this[name]=false;},
      append(...items){for(const item of items){item.remove();item.parentElement=this;this.children.push(item);}},appendChild(item){this.append(item);return item;},prepend(item){item.remove();item.parentElement=this;this.children.unshift(item);},
      remove(){if(this.parentElement)this.parentElement.children=this.parentElement.children.filter(child=>child!==this);this.parentElement=null;},
      after(item){const parent=this.parentElement;if(!parent)return;item.remove();item.parentElement=parent;parent.children.splice(parent.children.indexOf(this)+1,0,item);},
      insertAdjacentElement(where,item){if(where==='afterend')this.after(item);else if(where==='beforeend')this.append(item);else throw Error('Unsupported adapter insertion '+where);},
      insertAdjacentHTML(where,html){const holder=element();holder.innerHTML=html;const items=[...holder.children];if(where==='beforeend')this.append(...items);else if(where==='afterbegin')items.reverse().forEach(item=>this.prepend(item));else throw Error('Unsupported adapter HTML insertion '+where);},
      matches(selector){return matches(this,selector);},closest(selector){let item=this;while(item){if(matches(item,selector))return item;item=item.parentElement;}return null;},
      querySelectorAll(selector){const result=[];const walk=item=>{for(const child of item.children){if(matches(child,selector))result.push(child);walk(child);}};walk(this);return result;},querySelector(selector){return this.querySelectorAll(selector)[0]||null;},
      contains(item){while(item){if(item===this)return true;item=item.parentElement;}return false;},appendText(value){ownText+=value;},
      focus(){if(!this.closest('[inert]'))document.activeElement=this;},getClientRects(){return this.hidden?[]:[{}];},scrollIntoView(){},
      addEventListener(name,callback){if(!handlers.has(name))handlers.set(name,[]);handlers.get(name).push(callback);},
      cloneNode(deep){const copy=element(tag);for(const [name,value] of attrs)copy.setAttribute(name,value);copy.textContent=ownText;if(deep)copy.append(...this.children.map(child=>child.cloneNode(true)));return copy;},
      click(){document.dispatchEvent({type:'click',target:this,preventDefault(){}});},
    };
    node.classList={contains:name=>(attrs.get('class')||'').split(/\s+/).includes(name),add(...names){const classes=new Set((attrs.get('class')||'').split(/\s+/).filter(Boolean));names.forEach(name=>classes.add(name));attrs.set('class',[...classes].join(' '));},remove(...names){attrs.set('class',(attrs.get('class')||'').split(/\s+/).filter(name=>!names.includes(name)).join(' '));},toggle(name,on){on=on??!this.contains(name);on?this.add(name):this.remove(name);return on;}};
    node.dataset=new Proxy({}, {get:(_target,name)=>attrs.get(dataName(name)),set:(_target,name,value)=>{attrs.set(dataName(name),String(value));return true;}});
    for(const name of ['id','className','title','type','href'])Object.defineProperty(node,name,{get:()=>attrs.get(name==='className'?'class':name)||'',set:value=>attrs.set(name==='className'?'class':name,String(value))});
    Object.defineProperties(node,{
      attributes:{get:()=>[...attrs].map(([name,value])=>({name,value}))},
      isConnected:{get(){let item=this;while(item.parentElement)item=item.parentElement;return item===document?.documentElement;}},
      nextElementSibling:{get(){const list=this.parentElement?.children||[];return list[list.indexOf(this)+1]||null;}},
      tabIndex:{get:()=>attrs.has('tabindex')?Number(attrs.get('tabindex')):/^(BUTTON|INPUT|SELECT|TEXTAREA)$/.test(node.tagName)||node.tagName==='A'&&attrs.has('href')?0:-1,set:value=>attrs.set('tabindex',String(value))},
      textContent:{get:()=>ownText+node.children.map(child=>child.textContent).join(''),set:value=>{ownText=String(value);for(const child of [...node.children])child.remove();}},
      innerHTML:{get:()=>markup,set:html=>{markup=String(html);ownText='';for(const child of [...node.children])child.remove();const stack=[node],voids=new Set(['INPUT','IMG','LINK','META','BR','HR']);for(const token of markup.matchAll(/<\/?([a-z][\w-]*)\b([^>]*)>|([^<]+)/gi)){
        if(token[3]){stack.at(-1).appendText(token[3]);continue;}
        const name=token[1].toUpperCase();if(token[0].startsWith('</')){if(stack.at(-1).tagName===name)stack.pop();continue;}
        const child=element(name);for(const attr of token[2].matchAll(/([\w:-]+)(?:\s*=\s*(?:"([^"]*)"|'([^']*)'|([^\s>]+)))?/g))child.setAttribute(attr[1],attr[2]??attr[3]??attr[4]??'');stack.at(-1).append(child);if(!voids.has(name)&&!token[0].endsWith('/>'))stack.push(child);
      }}},
    });
    return node;
  }
  const html=element('html'),head=element('head'),body=element('body');html.append(head,body);
  document={documentElement:html,head,body,title:'Websites — WCP360',activeElement:body,createElement:element,
    querySelector:selector=>html.querySelector(selector),querySelectorAll:selector=>html.querySelectorAll(selector),
    addEventListener(name,callback){if(!listeners.has(name))listeners.set(name,[]);listeners.get(name).push(callback);},
    dispatchEvent(event){for(const callback of listeners.get(event.type)||[])callback(event);return true;}};
  body.innerHTML=`<div class="wcp-dashboard-container"><aside class="${legacy?'sidebar':'wcp-dashboard-sidebar collapsed'}"><button type="button" id="sidebar-first">Navigation item</button><a href="websites.html" id="sidebar-last">Websites</a></aside><main class="wcp-dashboard-main"><header class="wcp-dashboard-header"><button type="button" data-wcp-sidebar-toggle id="nav-one">Menu</button><button type="button" data-wcp-sidebar-toggle id="nav-two">Menu</button>${legacy?'<button type="button" data-legacy-sidebar-toggle id="legacy-toggle">Legacy menu</button>':''}<button type="button" data-open-command id="command-trigger">Search</button><button type="button" data-open-drawer="fixtureDrawer" id="drawer-trigger">Open drawer</button><button type="button" data-toggle-notifications id="notification-trigger">Notifications</button><button type="button" data-toggle-profile id="profile-trigger">Profile</button></header><h1 class="wcp-page-title">Websites</h1><button type="button" id="unrelated">Unrelated action</button></main></div><button class="wcp-sidebar-overlay" type="button">Close navigation</button><div id="wcpCommandBackdrop" class="wcp-command-backdrop"><input id="wcpCommandInput"><div class="wcp-command-scroll"><a class="wcp-command-result" href="websites.html">Websites</a></div><button type="button" id="command-last">Close</button></div><div id="wcpDrawerBackdrop"></div><aside id="fixtureDrawer" class="wcp-side-drawer"><button type="button" id="drawer-first">Close drawer</button><a href="websites.html" id="drawer-last">Details</a></aside><div class="wcp-popover" id="wcpNotifications"><button type="button">Notification</button></div><div class="wcp-popover" id="wcpProfileMenu"><button type="button">Profile</button></div><div id="previously-inert" inert>Preserved inert background</div>`;
  body.dataset.productScope='user';body.style.overflow='auto';
  const storage=new Map();if(saved!==undefined)storage.set('wcp360-experience-v1',typeof saved==='string'?saved:JSON.stringify(saved));
  const context={document,innerWidth:width,location:{pathname:'/websites.html',search:'',hash:''},URLSearchParams,structuredClone,Date,console,CSS:{escape:value=>value},navigator:{},
    CustomEvent:class{constructor(type,options={}){this.type=type;this.detail=options.detail;}},
    localStorage:{getItem:key=>{if(storageBlocked)throw Error('blocked');return storage.get(key)||null;},setItem:(key,value)=>{if(storageBlocked)throw Error('blocked');storage.set(key,value);}},
    setTimeout:callback=>timers.push(callback),addEventListener(name,callback){if(!windowListeners.has(name))windowListeners.set(name,[]);windowListeners.get(name).push(callback);},WCPUI:{theme:{apply(){}},toast(){}}};context.window=context;
  const flush=()=>{while(timers.length)timers.shift()();};
  vm.runInNewContext(source('reference-ui.js'),context);document.dispatchEvent({type:'DOMContentLoaded'});flush();
  if(experience){vm.runInNewContext(source('experience-runtime.js'),context);flush();}
  const find=selector=>document.querySelector(selector);
  return {context,document,storage,find,flush,api:context.WCPExperience,
    focus(selector){find(selector).focus();},
    click(selector){const target=find(selector);assert.ok(target,'Missing click target '+selector);target.focus();target.click();flush();},
    key(key,options={}){const event={type:'keydown',key,target:document.activeElement,defaultPrevented:false,preventDefault(){this.defaultPrevented=true;},...options};document.dispatchEvent(event);flush();return event;},
    resize(value){context.innerWidth=value;for(const callback of windowListeners.get('resize')||[])callback();},
    dispatch(type,detail){document.dispatchEvent({type,detail});flush();},
  };
}

test('Closed commands, drawers and popovers are inert at initialization',()=>{const h=harness();for(const id of ['wcpCommandBackdrop','fixtureDrawer','wcpNotifications','wcpProfileMenu'])assert.equal(h.find('#'+id).inert,true,id);});
test('Escape without an active overlay leaves focus in place',()=>{const h=harness();h.focus('#unrelated');h.key('Escape');assert.equal(h.document.activeElement,h.find('#unrelated'));});
test('Command opening, closing and repeated Escape restore focus only once',()=>{const h=harness();h.focus('#command-trigger');h.key('k',{ctrlKey:true});assert.equal(h.find('#wcpCommandBackdrop').inert,false);assert.equal(h.find('#command-trigger').getAttribute('aria-expanded'),'true');assert.equal(h.document.activeElement,h.find('#wcpCommandInput'));h.key('k',{ctrlKey:true});h.key('Escape');assert.equal(h.find('#wcpCommandBackdrop').inert,true);assert.equal(h.document.activeElement,h.find('#command-trigger'));h.focus('#unrelated');h.key('Escape');assert.equal(h.document.activeElement,h.find('#unrelated'));});
test('Command keyboard trap wraps at either boundary',()=>{const h=harness();h.click('#command-trigger');h.focus('#command-last');assert.equal(h.key('Tab').defaultPrevented,true);assert.equal(h.document.activeElement,h.find('#wcpCommandInput'));h.key('Tab',{shiftKey:true});assert.equal(h.document.activeElement,h.find('#command-last'));});
test('A command panel with no available controls retains keyboard focus',()=>{const h=harness();h.click('#command-trigger');const panel=h.find('#wcpCommandBackdrop');for(const item of panel.querySelectorAll('button,a[href],input,select,textarea,[tabindex]'))item.disabled=true;h.document.activeElement=h.document.body;assert.equal(h.key('Tab').defaultPrevented,true);assert.equal(h.document.activeElement,panel);assert.equal(panel.getAttribute('tabindex'),'-1');});
test('Drawer backdrop becomes interactive again when reopened',()=>{const h=harness();h.click('#drawer-trigger');assert.equal(h.find('#wcpDrawerBackdrop').inert,false);assert.equal(h.find('#fixtureDrawer').inert,false);h.click('#wcpDrawerBackdrop');assert.equal(h.find('#wcpDrawerBackdrop').inert,true);h.click('#drawer-trigger');assert.equal(h.find('#wcpDrawerBackdrop').inert,false);h.key('Escape');assert.equal(h.document.activeElement,h.find('#drawer-trigger'));});
test('Opening a drawer dismisses command search and its selection state',()=>{const h=harness();h.click('#command-trigger');h.click('#drawer-trigger');assert.equal(h.find('#wcpCommandBackdrop').inert,true);assert.equal(h.find('#command-trigger').getAttribute('aria-expanded'),'false');assert.equal(h.find('#fixtureDrawer').inert,false);});
test('Popover switching synchronizes both trigger and inert states',()=>{const h=harness();h.click('#notification-trigger');h.click('#profile-trigger');assert.equal(h.find('#wcpNotifications').inert,true);assert.equal(h.find('#notification-trigger').getAttribute('aria-expanded'),'false');assert.equal(h.find('#wcpProfileMenu').inert,false);h.key('Escape');assert.equal(h.find('#wcpProfileMenu').inert,true);assert.equal(h.document.activeElement,h.find('#profile-trigger'));});
test('Mobile navigation clears desktop collapse and synchronizes every toggle',()=>{const h=harness({width:500});const sidebar=h.find('.wcp-dashboard-sidebar');assert.equal(sidebar.classList.contains('collapsed'),false);assert.equal(sidebar.inert,true);for(const toggle of h.document.querySelectorAll('[data-wcp-sidebar-toggle]')){assert.equal(toggle.getAttribute('aria-expanded'),'false');assert.equal(toggle.getAttribute('aria-controls'),sidebar.id);}h.click('#nav-two');for(const toggle of h.document.querySelectorAll('[data-wcp-sidebar-toggle]'))assert.equal(toggle.getAttribute('aria-expanded'),'true');assert.equal(sidebar.inert,false);h.key('Escape');assert.equal(sidebar.inert,true);assert.equal(h.document.activeElement,h.find('#nav-two'));});
test('Mobile navigation traps keyboard focus and overlay dismissal restores it',()=>{const h=harness({width:500});h.click('#nav-one');h.focus('#sidebar-last');assert.equal(h.key('Tab').defaultPrevented,true);assert.equal(h.document.activeElement,h.find('#sidebar-first'));h.click('.wcp-sidebar-overlay');assert.equal(h.find('#nav-one').getAttribute('aria-expanded'),'false');assert.equal(h.document.activeElement,h.find('#nav-one'));});
test('Resizing synchronizes mobile, desktop and collapsed states',()=>{const h=harness();h.resize(500);assert.equal(h.find('.wcp-dashboard-sidebar').classList.contains('collapsed'),false);h.click('#nav-one');h.resize(1400);assert.equal(h.find('.wcp-dashboard-sidebar').classList.contains('mobile-open'),false);assert.equal(h.find('.wcp-dashboard-sidebar').inert,false);assert.equal(h.find('#nav-two').getAttribute('aria-expanded'),'true');});
test('Legacy mobile navigation uses the same inert and focus contracts',()=>{const h=harness({width:500,legacy:true});assert.equal(h.find('.sidebar').inert,true);h.click('#legacy-toggle');assert.equal(h.find('#legacy-toggle').getAttribute('aria-expanded'),'true');h.key('Escape');assert.equal(h.find('.sidebar').inert,true);assert.equal(h.document.activeElement,h.find('#legacy-toggle'));});
test('Malformed experience collections recover without a crash',()=>{for(const saved of ['{broken',[],{pinned:null,recent:{},changes:'invalid'}]){const h=harness({experience:true,saved});const state=h.api.getState();assert.equal(state.pinned.length,0);assert.equal(state.recent.length,1);assert.equal(state.changes.length,0);assert.equal(h.find('#wcpExperienceControl').inert,true);assert.equal(h.find('.x-backdrop').hidden,true);}});
test('Experience panels restore each background inert value and overflow',()=>{const h=harness({experience:true});h.focus('#unrelated');h.api.openControl();h.flush();assert.equal(h.find('#wcpExperienceControl').inert,false);assert.equal(h.find('.wcp-dashboard-container').inert,true);assert.equal(h.document.body.style.overflow,'hidden');h.key('Escape');assert.equal(h.find('#wcpExperienceControl').inert,true);assert.equal(h.find('.wcp-dashboard-container').inert,false);assert.equal(h.find('#previously-inert').inert,true);assert.equal(h.document.body.style.overflow,'auto');assert.equal(h.document.activeElement,h.find('#unrelated'));});
test('Experience traps forward, reverse and displaced keyboard focus',()=>{const h=harness({experience:true});h.api.openControl();h.flush();const panel=h.find('#wcpExperienceControl'),items=panel.querySelectorAll('button,a[href],input,select,textarea,[tabindex]').filter(item=>!item.disabled&&item.tabIndex>=0&&!item.closest('[hidden],[inert]'));items.at(-1).focus();assert.equal(h.key('Tab').defaultPrevented,true);assert.equal(h.document.activeElement,items[0]);h.key('Tab',{shiftKey:true});assert.equal(h.document.activeElement,items.at(-1));h.document.activeElement=h.document.body;h.key('Tab');assert.equal(h.document.activeElement,items[0]);});
test('Changing experience panels keeps the original return focus',()=>{const h=harness({experience:true});h.focus('#unrelated');h.api.openControl();h.flush();h.api.openReview({name:'Restart demo service',risk:'high'});h.flush();assert.equal(h.find('#wcpExperienceControl').inert,true);assert.equal(h.find('#wcpExperienceReview').inert,false);h.key('Escape');assert.equal(h.document.activeElement,h.find('#unrelated'));assert.equal(h.document.body.style.overflow,'auto');h.focus('#command-trigger');h.key('Escape');assert.equal(h.document.activeElement,h.find('#command-trigger'));});
test('Opening command search dismisses experience and restores background access',()=>{const h=harness({experience:true});h.focus('#unrelated');h.api.openControl();h.flush();h.key('k',{ctrlKey:true});assert.equal(h.find('#wcpExperienceControl').inert,true);assert.equal(h.find('.wcp-dashboard-container').inert,false);assert.equal(h.find('#wcpCommandBackdrop').inert,false);assert.equal(h.document.activeElement,h.find('#wcpCommandInput'));});
test('Stored navigation aliases and duplicates are normalized and scoped',()=>{const entry=(page,scope='user')=>({page,scope,title:'Demo',section:'Services'});const h=harness({experience:true,saved:{pinned:[entry('security.html'),entry('canonical-security.html'),entry('websites.html','root'),null],recent:[entry('user-dashboard.html'),entry('canonical-user-dashboard.html'),entry('missing.html')],changes:[null,{}, {id:'good',name:'Demo operation',scope:'user',createdAt:42},{id:'bad',name:'Bad',scope:'invalid',createdAt:42}]}});const state=h.api.getState();assert.deepEqual(state.pinned.map(item=>item.page),['canonical-security.html']);assert.deepEqual(state.recent.map(item=>item.page),['websites.html','canonical-user-dashboard.html']);assert.equal(state.changes.length,1);});
test('Change history deduplicates within each scope and normalizes unknown statuses',()=>{const change=(id,scope,status)=>({id,name:'Demo operation',scope,status,createdAt:42});const h=harness({experience:true,saved:{changes:[change('same','user','unknown'),change('same','user','running'),change('same','root','completed'),change('','user','completed')]}});const changes=h.api.getState().changes;assert.equal(changes.length,2);assert.equal(changes.find(item=>item.scope==='user').status,'failed');assert.equal(changes.find(item=>item.scope==='root').status,'completed');});
test('Blocked storage and an active native dialog do not break shell shortcuts',()=>{const h=harness({experience:true,storageBlocked:true});assert.equal(h.api.getState().recent.length,1);const dialog=h.document.createElement('dialog');dialog.setAttribute('open','');h.document.body.append(dialog);h.focus('#unrelated');const key=h.key('k',{ctrlKey:true});assert.equal(key.defaultPrevented,false);assert.equal(h.find('#wcpCommandBackdrop').inert,true);assert.equal(h.document.activeElement,h.find('#unrelated'));});

console.log(JSON.stringify({suite:'WCP360 shared shell',behaviorChecks:checks,status:'passed',coverage:'Real reference-ui and experience controllers through a minimal DOM adapter. No browser parsing, CSS layout, native focus or accessibility certification.'},null,2));
