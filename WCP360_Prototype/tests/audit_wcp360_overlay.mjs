import {readFileSync} from 'node:fs';
import {fileURLToPath} from 'node:url';
import vm from 'node:vm';
import assert from 'node:assert/strict';

const source=readFileSync(fileURLToPath(new URL('../assets/foundation.js',import.meta.url)),'utf8');
let checks=0;
const test=(message,run)=>{run();checks++;};

function harness(){
  const listeners=new Map();let document,native=null;
  const events=[];
  const dispatch=(target,event)=>{event.target??=target;event.preventDefault??=()=>event.defaultPrevented=true;event.stopImmediatePropagation??=()=>event.stopped=true;for(const listener of [...(target.listeners?.get(event.type)||listeners.get(event.type)||[])]){listener(event);if(event.stopped)break;}return event;};
  class Element{
    constructor(tag='div'){this.tagName=tag.toUpperCase();this.children=[];this.parentElement=null;this.attrs=new Map();this.classes=new Set();this.listeners=new Map();this.inert=false;this.hidden=false;this.disabled=false;this.isConnected=true;this.style={overflow:''};this.visible=true;this.classList={add:value=>this.classes.add(value),remove:value=>this.classes.delete(value),contains:value=>this.classes.has(value)};}
    setAttribute(name,value){this.attrs.set(name,String(value));}
    getAttribute(name){return this.attrs.get(name)??null;}
    hasAttribute(name){return this.attrs.has(name);}
    removeAttribute(name){this.attrs.delete(name);}
    get tabIndex(){return this.hasAttribute('tabindex')?Number(this.getAttribute('tabindex')):['INPUT','SELECT','TEXTAREA','BUTTON','IFRAME'].includes(this.tagName)||this.tagName==='A'&&this.hasAttribute('href')?0:-1;}
    append(...children){for(const child of children){child.parentElement=this;this.children.push(child);}}
    contains(element){return element===this||this.children.some(child=>child.contains(element));}
    closest(){for(let element=this;element;element=element.parentElement)if(element.inert||element.hidden)return element;return null;}
    descendants(){return this.children.flatMap(child=>[child,...child.descendants()]);}
    querySelectorAll(){return this.descendants().filter(element=>['BUTTON','INPUT','SELECT','TEXTAREA','IFRAME'].includes(element.tagName)||element.tagName==='A'&&element.hasAttribute('href')||element.hasAttribute('tabindex'));}
    querySelector(){return this.descendants().find(element=>['H1','H2','H3','STRONG'].includes(element.tagName)||element.hasAttribute('data-drawer-title'))||null;}
    getClientRects(){return this.visible&&!this.hidden&&!this.parentElement?.hidden?[{}]:[];}
    focus(){if(this.closest())return;document.activeElement=this;dispatch(document,{type:'focusin',target:this});}
    addEventListener(type,listener){const list=this.listeners.get(type)||[];list.push(listener);this.listeners.set(type,list);}
    removeEventListener(type,listener){this.listeners.set(type,(this.listeners.get(type)||[]).filter(item=>item!==listener));}
    click(){dispatch(this,{type:'click'});}
  }
  const body=new Element('body');body.style.overflow='clip';
  document={body,activeElement:null,createElement:tag=>new Element(tag),querySelector:()=>native,addEventListener(type,listener){const list=listeners.get(type)||[];list.push(listener);listeners.set(type,list);},removeEventListener(type,listener){listeners.set(type,(listeners.get(type)||[]).filter(item=>item!==listener));},dispatchEvent(event){events.push(event);dispatch(document,event);}};
  const context={document,structuredClone,CustomEvent:class{constructor(type,options={}){this.type=type;this.detail=options.detail;}}};vm.runInNewContext(source,context);
  const app=new Element('main'),launcher=new Element('button'),preserved=new Element('section'),host=new Element('section'),content=new Element('div'),panel=new Element('aside'),backdrop=new Element('button'),title=new Element('h2'),close=new Element('button'),input=new Element('input'),link=new Element('a');link.setAttribute('href','#audit');preserved.inert=true;
  app.append(launcher,host);host.append(content,panel,backdrop);panel.append(title,close,input,link);body.append(app,preserved);document.activeElement=launcher;
  const attach=(options={})=>context.WCPUI.Overlay.attach({panel,backdrop,source:'test-workflow',...options});
  return {UI:context.WCPUI,document,Element,body,app,launcher,preserved,host,content,panel,backdrop,title,close,input,link,attach,events,key:(key,shiftKey=false)=>dispatch(document,{type:'keydown',key,shiftKey}),setNative:value=>native=value,dispatch:event=>document.dispatchEvent(event)};
}

test('A new overlay is inaccessible while closed and receives a linked title',()=>{
  const h=harness(),overlay=h.attach();assert.equal(overlay.opened,false);assert.equal(h.panel.inert,true);assert.equal(h.panel.getAttribute('aria-hidden'),'true');assert.equal(h.backdrop.hidden,true);assert.equal(h.backdrop.inert,true);assert.equal(h.panel.getAttribute('aria-labelledby'),h.title.id);assert.ok(h.title.id);
});
test('A nested overlay isolates only surrounding siblings and restores previous states',()=>{
  const h=harness(),overlay=h.attach();overlay.open();assert.equal(overlay.opened,true);assert.equal(h.app.inert,false);assert.equal(h.host.inert,false);assert.equal(h.launcher.inert,true);assert.equal(h.content.inert,true);assert.equal(h.preserved.inert,true);assert.equal(h.panel.inert,false);assert.equal(h.backdrop.inert,false);assert.equal(h.backdrop.hidden,false);assert.equal(h.document.activeElement,h.input);assert.equal(h.body.style.overflow,'hidden');
  overlay.close();assert.equal(h.content.inert,false);assert.equal(h.launcher.inert,false);assert.equal(h.preserved.inert,true);assert.equal(h.body.style.overflow,'clip');assert.equal(h.document.activeElement,h.launcher);
});
test('Two instances sharing a source still close each other and retain the base launcher',()=>{
  const h=harness(),first=h.attach(),panel=new h.Element('aside'),backdrop=new h.Element('button'),input=new h.Element('input');panel.append(input);h.body.append(panel,backdrop);
  const second=h.UI.Overlay.attach({panel,backdrop,source:'test-workflow'});first.open();second.open(h.input);assert.equal(first.opened,false);assert.equal(second.opened,true);assert.equal(h.document.activeElement,input);second.close();assert.equal(h.document.activeElement,h.launcher);assert.equal(h.events.filter(event=>event.type==='wcp:overlay-opening').length,2);
});
test('Reopening an active form after replacing its contents retains the original return target',()=>{
  const h=harness(),overlay=h.attach();overlay.open();h.input.isConnected=false;h.panel.children=h.panel.children.filter(element=>element!==h.input);const next=new h.Element('input');h.panel.append(next);overlay.open(h.input);assert.equal(h.document.activeElement,next);overlay.close();assert.equal(h.document.activeElement,h.launcher);
});
test('Tab wraps in both directions and redirects an external focus attempt',()=>{
  const h=harness(),overlay=h.attach();overlay.open();h.close.focus();assert.equal(h.key('Tab',true).defaultPrevented,true);assert.equal(h.document.activeElement,h.link);assert.equal(h.key('Tab').defaultPrevented,true);assert.equal(h.document.activeElement,h.close);
  const external=new h.Element('button');h.body.append(external);external.focus();assert.equal(h.document.activeElement,h.input);
});
test('Escape and backdrop clicks close once while idle close leaves focus untouched',()=>{
  const h=harness(),overlay=h.attach();overlay.open();assert.equal(h.key('Escape').defaultPrevented,true);assert.equal(overlay.opened,false);const other=new h.Element('button');h.body.append(other);other.focus();overlay.close();assert.equal(h.document.activeElement,other);overlay.open();h.backdrop.click();assert.equal(overlay.opened,false);assert.equal(h.document.activeElement,other);
});
test('A native confirmation keeps its own keyboard and focus behavior above a custom form',()=>{
  const h=harness(),overlay=h.attach();overlay.open();const native=new h.Element('dialog'),nativeButton=new h.Element('button');native.append(nativeButton);h.body.append(native);h.setNative(native);nativeButton.focus();assert.equal(h.document.activeElement,nativeButton);assert.equal(h.key('Tab').defaultPrevented,undefined);assert.equal(h.key('Escape').defaultPrevented,undefined);assert.equal(overlay.opened,true);overlay.close();assert.equal(h.document.activeElement,nativeButton);
  overlay.open();assert.equal(overlay.opened,false);h.setNative(null);overlay.open();assert.equal(overlay.opened,true);
});
test('An empty overlay has a focus target and an accessible fallback name',()=>{
  const h=harness();h.panel.children=[];const overlay=h.attach();overlay.open();assert.equal(h.document.activeElement,h.panel);assert.equal(h.panel.getAttribute('tabindex'),'-1');assert.equal(h.panel.getAttribute('aria-label'),'WCP360 workflow');assert.equal(h.key('Tab').defaultPrevented,true);
});
test('Destroy releases its background and removes its event handlers',()=>{
  const h=harness(),overlay=h.attach();overlay.open();overlay.destroy();assert.equal(overlay.opened,false);assert.equal(h.content.inert,false);const other=new h.Element('button');h.body.append(other);other.focus();overlay.open();h.key('Escape');h.dispatch({type:'wcp:overlay-opening',detail:{source:'another'}});assert.equal(h.document.activeElement,other);assert.equal(h.panel.inert,true);assert.equal(h.body.style.overflow,'clip');
});
test('Attaching the same panel again destroys the previous instance',()=>{
  const h=harness(),first=h.attach();first.open();const replacement=h.attach();assert.equal(first.opened,false);assert.equal(h.body.style.overflow,'clip');first.open();assert.equal(first.opened,false);replacement.open();assert.equal(replacement.opened,true);
});
console.log(JSON.stringify({suite:'WCP360 shared modal lifecycle',checks,status:'passed',coverage:'DOM adapters exercise modal state, nested background isolation, keyboard boundaries and native-dialog coexistence; browser geometry is not certified.'},null,2));
