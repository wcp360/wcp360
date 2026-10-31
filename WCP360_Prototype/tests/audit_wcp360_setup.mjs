import {readFileSync} from 'node:fs';
import vm from 'node:vm';
import assert from 'node:assert/strict';

const source = name => readFileSync(new URL('../assets/' + name,import.meta.url),'utf8');
let checks = 0;
const test = (name, run) => { try { run();checks++; } catch (error) { error.message = name + ': ' + error.message;throw error; } };

// Real wizard and model; only the DOM, native form validation and storage boundary are adapted.
function harness(initial = {}) {
  const storage = new Map(Object.entries(initial));
  const events = [];
  let controls = [], buttons = [], fixture, denied = '';
  const element = (attributes = {}) => ({...attributes, listeners:{},addEventListener(name,fn){this.listeners[name]=fn;},focus(){},setAttribute(){},click(){if(!this.disabled)this.listeners.click?.();}});
  const parse = markup => {
    controls = [...markup.matchAll(/<(?:input|select)\b[^>]*data-setup-path="([^"]+)"[^>]*>/g)].map(match => {
      const tag = match[0];
      return element({dataset:{setupPath:match[1]},type:tag.match(/type="([^"]+)"/)?.[1] || 'text',value:tag.match(/value="([^"]*)"/)?.[1] || '',checked:/\schecked[\s>]/.test(tag)});
    });
    buttons = [...markup.matchAll(/<button\b[^>]*>/g)].map(match => {
      const tag = match[0], key = tag.match(/data-setup-(mode|back|next|preflight|again)(?:="([^"]+)")?/);
      return key ? element({key:key[1],dataset:key[1] === 'mode' ? {setupMode:key[2]} : {},disabled:/\sdisabled[\s>]/.test(tag)}) : null;
    }).filter(Boolean);
    fixture = markup.includes('data-setup-fixture') ? element() : null;
  };
  const host = {
    markup:'',className:'setup-shell',
    set innerHTML(value){this.markup=value;parse(value);},get innerHTML(){return this.markup;},
    querySelectorAll(selector){
      if(selector === '[data-setup-mode]')return buttons.filter(button => button.key === 'mode');
      if(selector === '[data-setup-path]')return controls;
      if(selector === '[data-setup-form]')return [...this.markup.matchAll(/<form data-setup-form/g)].map(() => ({reportValidity:() => true}));
      return [];
    },
    querySelector(selector){
      if(selector === '[data-setup-fixture]')return fixture;
      if(selector === '.setup-feedback' || selector === 'h1')return element();
      const key = selector.match(/^\[data-setup-(\w+)(?:="([^"]+)")?\]$/);
      return key ? buttons.find(button => button.key === key[1] && (!key[2] || button.dataset.setupMode === key[2])) || null : null;
    }
  };
  const context = {window:{},URL,document:{querySelector:selector => selector === '#setupWizard' ? host : null,dispatchEvent:event => events.push(event)},CustomEvent:class{constructor(type,{detail}){this.type=type;this.detail=detail;}},localStorage:{getItem:key => storage.get(key) || null,removeItem:key => storage.delete(key),setItem:(key,value) => {if(denied === '*' || denied === key)throw new Error('Storage denied');storage.set(key,value);}}};
  vm.createContext(context);
  vm.runInContext(source('setup-model.js'),context);
  vm.runInContext(source('setup-wizard.js'),context);
  return {
    host,events,storage,model:context.window.WCPSetup,
    state:() => JSON.parse(storage.get('wcp360-setup-wizard-v1')),
    settings:() => JSON.parse(storage.get('wcp360-system-settings-v1') || '{}'),
    click:(key,mode) => host.querySelector(`[data-setup-${key}${mode ? '="'+mode+'"' : ''}]`)?.click(),
    change:(path,value) => {const control=controls.find(item => item.dataset.setupPath === path);assert.ok(control,'Visible field '+path);if(control.type==='checkbox')control.checked=value;else control.value=value;control.listeners.change();},
    fixture:id => fixture.listeners.change({target:{value:id}}),
    deny:key => {denied=key;}
  };
}
const quickReview = h => {h.click('mode','quick');h.click('next');h.click('next');h.click('preflight');h.click('next');};
const seed = harness(), model = seed.model, defaults = seed.state().config;
const modified = (path,value) => {const config=structuredClone(defaults);const parts=path.split('.');config[parts[0]][parts[1]]=value;return config;};
test('default profile validates',() => assert.equal(model.errors(defaults).length,0));
test('ready fixture passes',() => assert.equal(model.preflight(defaults).ok,true));
for (const fixture of ['disk','dns','ports','clock']) test(fixture+' fixture blocks',() => {const result=model.preflight(defaults,fixture);assert.equal(result.ok,false);assert.ok(result.checks.some(check => !check.ok));assert.equal(result.simulated,true);});
for (const [path,value] of [
  ['identity.hostname','localhost'],['identity.hostname','-bad.example.com'],['identity.adminEmail','bad'],['identity.environment','<img src=x>'],
  ['database.engine','MariaDB 11.4 LTS'],['database.host','999.0.0.1'],['database.user','postgres'],['database.name','root'],['database.password','literal-password'],['database.port',65536],['database.port',1.5],['database.pool',0],
  ['storage.dataRoot','/etc'],['storage.dataRoot','/var/lib/../root'],['storage.endpoint','http://backup.example.com'],['storage.endpoint','https://user:password@backup.example.com'],['storage.secret','private-key'],
  ['network.publicIp','999.1.1.1'],['network.nameservers','ns1.example.com'],['network.dnsEndpoint','http://dns.example.com'],['network.smtpHost','smtp'],
  ['security.mfaRoot',false],['security.firewall',false],['security.rootSsh',true],['security.passwordMin',5],['resource.ram',NaN],['resource.inodes',-1],['runtime.php','unapproved']
]) test('reject '+path+' '+String(value),() => assert.ok(model.errors(modified(path,value)).some(issue => issue.path === path)));
test('service dependencies block preflight',() => {const config=modified('services.web',false);assert.equal(model.preflight(config).ok,false);});
test('legacy secrets and unknown fields are discarded on load',() => {const h=harness({'wcp360-setup-wizard-v1':JSON.stringify({mode:'expert',config:{database:{password:'OLD-SECRET'},storage:{secret:'OLD-KEY'},injected:{value:'bad'}}})});const saved=JSON.stringify(h.state());assert.ok(!saved.includes('OLD-SECRET')&&!saved.includes('OLD-KEY')&&!saved.includes('injected'));});
test('invalid stored types return to defaults',() => {const config=model.restore(defaults,{resource:{ram:'broken'},security:{mfaRoot:'false'}});assert.equal(config.resource.ram,defaults.resource.ram);assert.equal(config.security.mfaRoot,true);});
test('corrupt JSON recovers the mode chooser',() => assert.ok(harness({'wcp360-setup-wizard-v1':'{broken'}).host.innerHTML.includes('data-setup-mode="quick"')));
test('stale preflight is not trusted after reload',() => {const h=harness({'wcp360-setup-wizard-v1':JSON.stringify({mode:'quick',step:3,preflight:true,config:defaults})});assert.equal(h.state().preflight,false);h.click('next');assert.equal(h.events.length,0);});
test('quick setup persists settings and one operation',() => {const h=harness();quickReview(h);h.click('next');assert.equal(h.state().completed,true);assert.equal(h.settings().access.mfaRoot,true);assert.equal(h.events.filter(event => event.type === 'wcp:operation').length,1);assert.ok(h.host.innerHTML.includes('Simulated initialization'));});
test('expert runtime selection reaches settings without an event side effect',() => {const h=harness();h.click('mode','expert');for(let index=0;index<7;index++)h.click('next');h.change('runtime.php','8.3');h.click('next');h.click('next');h.click('preflight');h.click('next');assert.equal(h.state().completed,true);assert.equal(h.settings().runtime.php,'8.3');});
test('failed fixture blocks and ready fixture recovers',() => {const h=harness();h.click('mode','quick');h.click('next');h.click('next');h.fixture('disk');h.click('preflight');assert.equal(h.state().preflight,false);assert.ok(h.host.innerHTML.includes('Failed · demo'));h.click('next');assert.equal(h.state().step,2);h.fixture('ready');h.click('preflight');h.click('next');h.click('next');assert.equal(h.state().completed,true);});
test('invalid hostname prevents leaving the form',() => {const h=harness();h.click('mode','quick');h.click('next');h.change('identity.hostname','localhost');h.click('next');assert.equal(h.state().step,1);assert.ok(h.host.innerHTML.includes('fully qualified'));});
test('literal secret input is cleared and never stored',() => {const h=harness();h.click('mode','expert');h.click('next');h.click('next');h.change('database.password','DO-NOT-PERSIST-THIS');assert.ok(!JSON.stringify([...h.storage]).includes('DO-NOT-PERSIST-THIS'));h.click('next');assert.equal(h.state().step,2);});
test('settings storage failure never reports success or queues a job',() => {const h=harness();quickReview(h);h.deny('wcp360-system-settings-v1');h.click('next');assert.equal(h.state().completed,false);assert.equal(h.events.length,0);assert.ok(h.host.innerHTML.includes('could not be saved'));});
test('malformed previous settings are reported without queuing a job',() => {const h=harness({'wcp360-system-settings-v1':'{broken'});quickReview(h);h.click('next');assert.equal(h.events.length,0);assert.equal(h.state().completed,false);assert.ok(h.host.innerHTML.includes('could not be read'));});
test('malformed revision metadata is normalized',() => {const h=harness({'wcp360-system-settings-v1':JSON.stringify({version:'broken',versions:'broken'})});quickReview(h);h.click('next');assert.equal(h.settings().version,1);assert.equal(h.settings().versions.length,1);});
test('draft storage failure never reports completion',() => {const h=harness();quickReview(h);h.deny('wcp360-setup-wizard-v1');h.click('next');assert.equal(h.events.length,0);assert.equal(h.storage.has('wcp360-system-settings-v1'),false);assert.ok(!h.host.innerHTML.includes('setup-complete-card'));});

const searchContext={window:{}};vm.runInNewContext(source('prototype-search.js'),searchContext);
const search=searchContext.window.WCPPrototypeSearch.matches;
test('French accent insensitive multiword search',() => assert.equal(search({label:'Security audit',category:'Security'},'sécurité audit'),true));
test('French plural backup search',() => assert.equal(search({label:'Backups',category:'Data protection'},'sauvegardes'),true));
test('all search terms must match',() => assert.equal(search({label:'Backups',category:'Data protection'},'sauvegardes dns'),false));
test('prototype property names are safe query tokens',() => assert.equal(search({label:'Domains'},'constructor'),false));
test('empty query preserves the catalogue',() => assert.equal(search({label:'Domains'},'  '),true));
console.log(JSON.stringify({suite:'Prototype setup and search',behaviorChecks:checks,status:'passed',coverage:'Real model and wizard via DOM/storage adapters; no native forms, Linux inspection, browser rendering or production installation.'},null,2));
