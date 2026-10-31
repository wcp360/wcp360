import {fileURLToPath} from 'node:url';
import { spawn } from "node:child_process";
import { createServer } from "node:http";
import { readFileSync, statSync } from "node:fs";
import { extname, join, normalize, resolve } from "node:path";
import { tmpdir } from "node:os";

const root=resolve(process.argv[2]||fileURLToPath(new URL('../',import.meta.url)));
const host="127.0.0.1",sitePort=8768,debugPort=9233;
const chromePath=process.env.CHROME_PATH||"C:/Program Files/Google/Chrome/Application/chrome.exe";
const mime={".html":"text/html; charset=utf-8",".css":"text/css; charset=utf-8",".js":"text/javascript; charset=utf-8",".json":"application/json; charset=utf-8"};
const safe=file=>{try{return statSync(file).isFile();}catch{return false;}};
const server=createServer((request,response)=>{
  const relative=decodeURIComponent(new URL(request.url,"http://"+host).pathname).split("/").filter(Boolean).join("/");
  const file=normalize(join(root,relative));
  if(!file.startsWith(root)||!safe(file)){response.writeHead(404).end("Not found");return;}
  response.writeHead(200,{"Content-Type":mime[extname(file)]||"application/octet-stream"});
  response.end(readFileSync(file));
});
const delay=ms=>new Promise(done=>setTimeout(done,ms));
async function waitChrome(){for(let index=0;index<60;index+=1){try{const response=await fetch("http://"+host+":"+debugPort+"/json/version");if(response.ok)return;}catch{}await delay(100);}throw new Error("Chrome did not start");}
class Cdp{
  constructor(url){this.id=0;this.pending=new Map();this.listeners=new Map();this.ready=new Promise((ok,fail)=>{this.ws=new WebSocket(url);this.ws.onopen=ok;this.ws.onerror=fail;this.ws.onmessage=event=>this.receive(JSON.parse(event.data));});}
  receive(message){if(message.id){const item=this.pending.get(message.id);if(!item)return;this.pending.delete(message.id);message.error?item.fail(new Error(message.error.message)):item.ok(message.result);return;}for(const listener of this.listeners.get(message.method)||[])listener(message.params);}
  async send(method,params={}){await this.ready;const id=++this.id;return new Promise((ok,fail)=>{this.pending.set(id,{ok,fail});this.ws.send(JSON.stringify({id,method,params}));});}
  on(method,listener){const list=this.listeners.get(method)||[];list.push(listener);this.listeners.set(method,list);return()=>this.listeners.set(method,list.filter(item=>item!==listener));}
  close(){this.ws.close();}
}
async function navigate(cdp,path){
  const parts=path.split("#"),pathname=parts[0],hash=parts[1]?"#"+parts[1]:"";
  await cdp.send("Page.navigate",{url:"http://"+host+":"+sitePort+"/"+pathname.split("/").map(encodeURIComponent).join("/")+hash});
  for(let index=0;index<50;index+=1){
    const ready=await cdp.send("Runtime.evaluate",{expression:"document.readyState",returnByValue:true});
    if(ready.result?.value==="interactive"||ready.result?.value==="complete")break;
    await delay(50);
  }
  await delay(180);
}
async function evaluate(cdp,expression){const result=await cdp.send("Runtime.evaluate",{expression,awaitPromise:true,returnByValue:true});if(result.exceptionDetails)throw new Error(result.exceptionDetails.exception?.description||result.exceptionDetails.text);return result.result.value;}
const findings=[];let checks=0;
const expect=(condition,rule,detail)=>{checks+=1;if(!condition)findings.push({rule,detail});};

await new Promise(done=>server.listen(sitePort,host,done));
const chrome=spawn(chromePath,["--headless=new","--disable-gpu","--no-first-run","--no-default-browser-check","--remote-debugging-port="+debugPort,"--user-data-dir="+join(tmpdir(),"wcp360-extension-audit-"+process.pid),"about:blank"],{stdio:"ignore"});
try{
  await waitChrome();
  const target=await (await fetch("http://"+host+":"+debugPort+"/json/new?about:blank",{method:"PUT"})).json();
  const cdp=new Cdp(target.webSocketDebuggerUrl);
  await Promise.all([cdp.send("Page.enable"),cdp.send("Runtime.enable")]);
  cdp.on("Runtime.exceptionThrown",event=>findings.push({rule:"runtime-exception",detail:event.exceptionDetails.exception?.description||event.exceptionDetails.text}));
  await navigate(cdp,"software-manager.html#plugins");
  await evaluate(cdp,`localStorage.removeItem("wcp360-platform-software-v1")`);
  await navigate(cdp,"software-manager.html#plugins");

  const initial=await evaluate(cdp,`(()=>{const card=document.querySelector('[data-sw-plugin="insights-connector"]');return {cards:document.querySelectorAll("[data-sw-plugin]").length,summary:document.querySelectorAll(".sw-extension-summary article").length,installed:card?.dataset.installed,enabled:card?.dataset.enabled,installButton:Boolean(card?.querySelector("[data-sw-action='extension-install']")),directToggles:document.querySelectorAll("[data-sw-toggle-plugin]").length,coreMetric:document.querySelector("[data-sw-metrics] article strong")?.textContent.trim()};})()`);
  expect(initial.cards===16&&initial.summary===5&&initial.installed==="false"&&initial.enabled==="false"&&initial.installButton&&initial.directToggles===0&&initial.coreMetric==="12 / 12","initial-extension-state",initial);

  const installed=await evaluate(cdp,`(()=>{const card=document.querySelector('[data-sw-plugin="insights-connector"]');card.querySelector("[data-sw-action='extension-install']").click();const form=document.querySelector("[data-sw-form]");form.elements.activate.checked=true;form.dispatchEvent(new Event("submit",{bubbles:true,cancelable:true}));const state=JSON.parse(localStorage.getItem("wcp360-platform-software-v1")||"{}"),item=state.plugins.find(entry=>entry.id==="insights-connector");return {installed:item?.installed,enabled:item?.enabled,health:item?.health,rollout:item?.rollout,permissions:item?.permissions?.length,core:document.querySelector("[data-sw-metrics] article strong")?.textContent.trim()};})()`);
  expect(installed.installed&&installed.enabled&&installed.health==="Healthy"&&installed.rollout==="100%"&&installed.permissions>=4&&installed.core==="12 / 12","install-and-activate",installed);

  const disabled=await evaluate(cdp,`(()=>{document.querySelector('[data-sw-plugin="insights-connector"] [data-sw-action="extension-disable"]').click();document.querySelector("[data-sw-form]").dispatchEvent(new Event("submit",{bubbles:true,cancelable:true}));const item=JSON.parse(localStorage.getItem("wcp360-platform-software-v1")).plugins.find(entry=>entry.id==="insights-connector");return {installed:item.installed,enabled:item.enabled,health:item.health,dataPolicy:item.dataPolicy};})()`);
  expect(disabled.installed&&!disabled.enabled&&disabled.health==="Stopped"&&Boolean(disabled.dataPolicy),"graceful-disable",disabled);

  const reactivated=await evaluate(cdp,`(()=>{document.querySelector('[data-sw-plugin="insights-connector"] [data-sw-action="extension-activate"]').click();document.querySelector("[data-sw-form]").dispatchEvent(new Event("submit",{bubbles:true,cancelable:true}));const item=JSON.parse(localStorage.getItem("wcp360-platform-software-v1")).plugins.find(entry=>entry.id==="insights-connector");return {enabled:item.enabled,quarantined:item.quarantined,health:item.health};})()`);
  expect(reactivated.enabled&&!reactivated.quarantined&&reactivated.health==="Healthy","canary-reactivation",reactivated);

  const quarantined=await evaluate(cdp,`(()=>{document.querySelector('[data-sw-plugin="insights-connector"] [data-sw-action="extension-safety"]').click();const input=document.querySelector("[data-sw-extension-confirm]"),submit=document.querySelector("[data-sw-danger-submit]"),before=submit.disabled;input.value="insights-connector";input.dispatchEvent(new Event("input",{bubbles:true}));const gate=!submit.disabled;document.querySelector("[data-sw-form]").dispatchEvent(new Event("submit",{bubbles:true,cancelable:true}));const item=JSON.parse(localStorage.getItem("wcp360-platform-software-v1")).plugins.find(entry=>entry.id==="insights-connector");return {before,gate,enabled:item.enabled,quarantined:item.quarantined,health:item.health};})()`);
  expect(quarantined.before&&quarantined.gate&&!quarantined.enabled&&quarantined.quarantined&&quarantined.health==="Isolated","quarantine-gate",quarantined);

  const released=await evaluate(cdp,`(()=>{document.querySelector('[data-sw-plugin="insights-connector"] [data-sw-action="extension-activate"]').click();document.querySelector("[data-sw-form]").dispatchEvent(new Event("submit",{bubbles:true,cancelable:true}));const item=JSON.parse(localStorage.getItem("wcp360-platform-software-v1")).plugins.find(entry=>entry.id==="insights-connector");return {enabled:item.enabled,quarantined:item.quarantined,health:item.health};})()`);
  expect(released.enabled&&!released.quarantined&&released.health==="Healthy","quarantine-release-through-canary",released);

  const uninstalled=await evaluate(cdp,`(()=>{document.querySelector('[data-sw-plugin="insights-connector"] [data-sw-action="extension-disable"]').click();document.querySelector("[data-sw-form]").dispatchEvent(new Event("submit",{bubbles:true,cancelable:true}));document.querySelector('[data-sw-plugin="insights-connector"] [data-sw-action="extension-safety"]').click();const form=document.querySelector("[data-sw-form]");form.elements.safetyAction.value="Uninstall extension";form.elements.confirm.value="insights-connector";form.elements.confirm.dispatchEvent(new Event("input",{bubbles:true}));form.dispatchEvent(new Event("submit",{bubbles:true,cancelable:true}));const item=JSON.parse(localStorage.getItem("wcp360-platform-software-v1")).plugins.find(entry=>entry.id==="insights-connector");return {installed:item.installed,enabled:item.enabled,quarantined:item.quarantined,health:item.health,dataPolicy:item.dataPolicy,core:document.querySelector("[data-sw-metrics] article strong")?.textContent.trim()};})()`);
  expect(!uninstalled.installed&&!uninstalled.enabled&&!uninstalled.quarantined&&uninstalled.health==="Not installed"&&Boolean(uninstalled.dataPolicy)&&uninstalled.core==="12 / 12","controlled-uninstall",uninstalled);

  const added=await evaluate(cdp,`(()=>{document.querySelector("[data-sw-kind='Plugin']").click();const form=document.querySelector("[data-sw-form]");form.elements.component.value="example/demo-extension";form.dispatchEvent(new Event("submit",{bubbles:true,cancelable:true}));const item=JSON.parse(localStorage.getItem("wcp360-platform-software-v1")).plugins.find(entry=>entry.id==="example-demo-extension");return {installed:item?.installed,enabled:item?.enabled,health:item?.health,permissions:item?.permissions?.length,card:Boolean(document.querySelector('[data-sw-plugin="example-demo-extension"]'))};})()`);
  expect(added.installed&&!added.enabled&&added.health==="Stopped"&&added.permissions===1&&added.card,"catalog-package-install",added);

  const manifestGate=await evaluate(cdp,`(async()=>{document.querySelector('[data-sw-plugin="example-demo-extension"] [data-sw-action="plugin"]').click();await new Promise(done=>setTimeout(done,30));const toggle=document.querySelector('[data-sw-form] [name="enabled"]');return {disabled:toggle?.disabled,label:toggle?.closest(".sw-check")?.textContent.includes("lifecycle-controlled")};})()`);
  expect(manifestGate.disabled&&manifestGate.label,"no-direct-runtime-toggle",manifestGate);

  await cdp.send("Emulation.setDeviceMetricsOverride",{width:390,height:844,deviceScaleFactor:1,mobile:false});
  await navigate(cdp,"software-manager.html#plugins");
  const mobile=await evaluate(cdp,`({overflow:Math.max(0,document.documentElement.scrollWidth-innerWidth),cards:document.querySelectorAll("[data-sw-plugin]").length,actions:document.querySelectorAll(".sw-plugin-actions .wcp-action-btn").length})`);
  expect(mobile.overflow<=1&&mobile.cards>=16&&mobile.actions>0,"responsive-extension-lifecycle",mobile);
  cdp.close();
}finally{chrome.kill();server.close();}
console.log(JSON.stringify({checks,findings},null,2));
process.exitCode=findings.length?1:0;
