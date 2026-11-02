import {spawn} from 'node:child_process';
import {createServer} from 'node:http';
import {readFileSync,statSync,writeFileSync} from 'node:fs';
import {extname,join,normalize,resolve} from 'node:path';
import {tmpdir} from 'node:os';

const root=resolve(process.argv[2]||'WCP360 Official Website');
const host='127.0.0.1',sitePort=8773,debugPort=9239;
const chromePath=process.env.CHROME_PATH||'C:/Program Files/Google/Chrome/Application/chrome.exe';
const mime={'.html':'text/html; charset=utf-8','.css':'text/css; charset=utf-8','.js':'text/javascript; charset=utf-8','.svg':'image/svg+xml'};
const safe=file=>{try{return statSync(file).isFile()}catch{return false}};
const server=createServer((request,response)=>{
  let relative=decodeURIComponent(new URL(request.url,'http://'+host).pathname).split('/').filter(Boolean).join('/');
  if(!relative)relative='index.html';
  const file=normalize(join(root,relative));
  if(!file.startsWith(root)||!safe(file)){response.writeHead(404).end('Not found');return}
  response.writeHead(200,{'Content-Type':mime[extname(file)]||'application/octet-stream'});
  response.end(readFileSync(file));
});
const delay=ms=>new Promise(done=>setTimeout(done,ms));
async function waitChrome(){for(let i=0;i<80;i+=1){try{const response=await fetch('http://'+host+':'+debugPort+'/json/version');if(response.ok)return}catch{}await delay(100)}throw new Error('Chrome did not start')}
class Cdp{
  constructor(url){this.id=0;this.pending=new Map;this.listeners=new Map;this.ready=new Promise((ok,fail)=>{this.ws=new WebSocket(url);this.ws.onopen=ok;this.ws.onerror=fail;this.ws.onmessage=event=>this.receive(JSON.parse(event.data))})}
  receive(message){if(message.id){const item=this.pending.get(message.id);if(!item)return;this.pending.delete(message.id);message.error?item.fail(new Error(message.error.message)):item.ok(message.result);return}for(const listener of this.listeners.get(message.method)||[])listener(message.params)}
  async send(method,params={}){await this.ready;const id=++this.id;return new Promise((ok,fail)=>{this.pending.set(id,{ok,fail});this.ws.send(JSON.stringify({id,method,params}))})}
  on(method,listener){const list=this.listeners.get(method)||[];list.push(listener);this.listeners.set(method,list);return()=>this.listeners.set(method,list.filter(item=>item!==listener))}
  close(){this.ws.close()}
}
async function navigate(cdp,path){
  const loaded=new Promise(resolve=>{const stop=cdp.on('Page.loadEventFired',()=>{stop();resolve(true)})});
  await cdp.send('Page.navigate',{url:'http://'+host+':'+sitePort+'/'+path});
  await Promise.race([loaded,delay(5000)]);
  await delay(180);
}
async function evaluate(cdp,expression){const result=await cdp.send('Runtime.evaluate',{expression,awaitPromise:true,returnByValue:true});if(result.exceptionDetails)throw new Error(result.exceptionDetails.exception?.description||result.exceptionDetails.text);return result.result.value}
const findings=[],checks=[],screenshots=[];
const expect=(condition,rule,detail)=>{checks.push(rule);if(!condition)findings.push({rule,detail})};
const capture=async(cdp,name)=>{const data=await cdp.send('Page.captureScreenshot',{format:'png',captureBeyondViewport:false});const path=join(tmpdir(),name);writeFileSync(path,Buffer.from(data.data,'base64'));screenshots.push(path)};

await new Promise(done=>server.listen(sitePort,host,done));
const chrome=spawn(chromePath,['--headless=new','--disable-gpu','--no-first-run','--no-default-browser-check','--remote-debugging-port='+debugPort,'--user-data-dir='+join(tmpdir(),'wcp360-additions-audit-'+process.pid),'about:blank'],{stdio:'ignore'});
try{
  await waitChrome();
  const target=await (await fetch('http://'+host+':'+debugPort+'/json/new?about:blank',{method:'PUT'})).json();
  const cdp=new Cdp(target.webSocketDebuggerUrl);
  await Promise.all([cdp.send('Page.enable'),cdp.send('Runtime.enable'),cdp.send('Log.enable')]);
  cdp.on('Runtime.exceptionThrown',event=>findings.push({rule:'runtime-exception',detail:event.exceptionDetails.exception?.description||event.exceptionDetails.text}));
  cdp.on('Log.entryAdded',event=>{if(event.entry.level==='error')findings.push({rule:'console-error',detail:event.entry.text})});

  await cdp.send('Emulation.setDeviceMetricsOverride',{width:1440,height:1050,deviceScaleFactor:1,mobile:false});
  await navigate(cdp,'app-installer.html');
  const appDepth=await evaluate(cdp,`({apps:document.querySelectorAll('[data-app]').length,filters:document.querySelectorAll('[data-app-filter]').length,roles:document.querySelectorAll('.app-role-card').length,lifecycle:document.querySelectorAll('.app-lifecycle-flow>div').length,overflow:Math.max(0,document.documentElement.scrollWidth-innerWidth),footerLink:Boolean(document.querySelector('.site-footer [href*=app-installer]')),megaLink:Boolean(document.querySelector('.mega-menu [href*=app-installer]'))})`);
  expect(appDepth.apps===8&&appDepth.filters===6&&appDepth.roles===3&&appDepth.lifecycle===6&&appDepth.overflow<=1&&appDepth.footerLink&&appDepth.megaLink,'app-installer-depth',appDepth);
  const appInteraction=await evaluate(cdp,`(()=>{const filter=document.querySelector('[data-app-filter=developer]');filter.click();const visible=[...document.querySelectorAll('[data-app]')].filter(card=>!card.hidden);visible[0].querySelector('[data-app-select]').click();const selected=document.querySelector('[data-selected-app]').textContent.trim();const form=document.querySelector('[data-app-plan]');form.requestSubmit();return {visible:visible.length,active:filter.classList.contains('is-active'),selected,result:!document.querySelector('[data-plan-result]').hidden}})()`);
  expect(appInteraction.visible===3&&appInteraction.active&&appInteraction.selected&&appInteraction.result,'app-installer-interactions',appInteraction);
  await navigate(cdp,'app-installer.html');await delay(450);await capture(cdp,'wcp360-official-app-installer-desktop.png');
  await evaluate(cdp,`(()=>{document.documentElement.style.scrollBehavior='auto';const target=document.querySelector('#catalog');scrollTo(0,target.getBoundingClientRect().top+scrollY-130)})()`);await delay(250);await capture(cdp,'wcp360-official-app-catalog-desktop.png');

  await navigate(cdp,'contact.html');
  const contactDepth=await evaluate(cdp,`({routes:document.querySelectorAll('.contact-route-card').length,router:document.querySelectorAll('.contact-router>a').length,fields:document.querySelectorAll('[data-contact-form] input,[data-contact-form] select,[data-contact-form] textarea').length,supportRows:document.querySelectorAll('.contact-support-row').length,overflow:Math.max(0,document.documentElement.scrollWidth-innerWidth),footerLink:Boolean(document.querySelector('.site-footer [href*=contact]'))})`);
  expect(contactDepth.routes===4&&contactDepth.router===4&&contactDepth.fields>=8&&contactDepth.supportRows===4&&contactDepth.overflow<=1&&contactDepth.footerLink,'contact-page-depth',contactDepth);
  const contactForm=await evaluate(cdp,`(()=>{const form=document.querySelector('[data-contact-form]');form.querySelector('[name=topic]').value='Product & partnerships';form.querySelector('[name=name]').value='Prototype User';form.querySelector('[name=email]').value='user@example.com';form.querySelector('[name=subject]').value='Evaluate WCP360';form.querySelector('[name=message]').value='We want to evaluate the control panel architecture on a dedicated test server.';form.querySelector('[type=checkbox]').checked=true;form.requestSubmit();return {valid:form.checkValidity(),result:!document.querySelector('[data-contact-result]').hidden}})()`);
  expect(contactForm.valid&&contactForm.result,'contact-form-validation',contactForm);
  await navigate(cdp,'contact.html');await delay(450);await capture(cdp,'wcp360-official-contact-desktop.png');

  await navigate(cdp,'about.html');
  const aboutAccent=await evaluate(cdp,`getComputedStyle(document.querySelector('.about-brief-rule strong')).color`);
  expect(aboutAccent!=='rgb(8, 119, 186)','no-saturated-blue-about-rule',aboutAccent);

  await cdp.send('Emulation.setDeviceMetricsOverride',{width:390,height:844,deviceScaleFactor:1,mobile:false});
  for(const page of ['app-installer.html','contact.html']){
    await navigate(cdp,page);
    const state=await evaluate(cdp,`({overflow:Math.max(0,document.documentElement.scrollWidth-innerWidth),header:Boolean(document.querySelector('.site-header')),footer:Boolean(document.querySelector('.site-footer'))})`);
    expect(state.header&&state.footer&&state.overflow<=1,'mobile-shell-and-overflow:'+page,state);
  }
  await navigate(cdp,'app-installer.html');await delay(450);await capture(cdp,'wcp360-official-app-installer-mobile.png');
  await evaluate(cdp,`(()=>{document.documentElement.style.scrollBehavior='auto';const target=document.querySelector('#catalog');scrollTo(0,target.getBoundingClientRect().top+scrollY-115)})()`);await delay(250);await capture(cdp,'wcp360-official-app-catalog-mobile.png');
  await navigate(cdp,'contact.html');await delay(450);await capture(cdp,'wcp360-official-contact-mobile.png');
  cdp.close();
}finally{chrome.kill();server.close()}
console.log(JSON.stringify({checks:checks.length,findings,screenshots},null,2));
process.exitCode=findings.length?1:0;
