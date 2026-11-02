import { spawn } from "node:child_process";
import { createServer } from "node:http";
import { readFileSync, statSync } from "node:fs";
import { extname, join, normalize, resolve } from "node:path";
import { tmpdir } from "node:os";

const root=resolve(process.argv[2]||".");
const host="127.0.0.1",sitePort=8767,debugPort=9232;
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
async function navigate(cdp,path){const loaded=new Promise(done=>{const off=cdp.on("Page.loadEventFired",()=>{off();done();});});await cdp.send("Page.navigate",{url:"http://"+host+":"+sitePort+"/"+path.split("/").map(encodeURIComponent).join("/")});await loaded;await delay(180);}
async function evaluate(cdp,expression){const result=await cdp.send("Runtime.evaluate",{expression,awaitPromise:true,returnByValue:true});if(result.exceptionDetails)throw new Error(result.exceptionDetails.text);return result.result.value;}
const findings=[];
let checks=0;
const expect=(condition,rule,detail)=>{checks+=1;if(!condition)findings.push({rule,detail});};

await new Promise(done=>server.listen(sitePort,host,done));
const chrome=spawn(chromePath,["--headless=new","--disable-gpu","--no-first-run","--no-default-browser-check","--remote-debugging-port="+debugPort,"--user-data-dir="+join(tmpdir(),"wcp360-private-audit-"+process.pid),"about:blank"],{stdio:"ignore"});
try{
  await waitChrome();
  const target=await (await fetch("http://"+host+":"+debugPort+"/json/new?about:blank",{method:"PUT"})).json();
  const cdp=new Cdp(target.webSocketDebuggerUrl);
  await Promise.all([cdp.send("Page.enable"),cdp.send("Runtime.enable")]);
  cdp.on("Runtime.exceptionThrown",event=>findings.push({rule:"runtime-exception",detail:event.exceptionDetails.exception?.description||event.exceptionDetails.text}));
  await navigate(cdp,"WCP360 Internal/index.html");
  await evaluate(cdp,`sessionStorage.clear();localStorage.removeItem("wcp360.insights.exchange.v1")`);
  await navigate(cdp,"WCP360 Internal/internal-distribution.html");
  const blocked=await evaluate(cdp,`({path:location.pathname,reason:new URLSearchParams(location.search).get("reason")})`);
  expect(blocked.path.includes("WCP360%20Internal/index.html")||blocked.path.includes("WCP360 Internal/index.html"),"private-auth-guard",blocked);
  expect(blocked.reason==="session-required","private-auth-reason",blocked);
  const rejected=await evaluate(cdp,`(()=>{const input=document.getElementById("internalEmail");input.value="root@example.com";document.getElementById("internalLoginForm").dispatchEvent(new Event("submit",{bubbles:true,cancelable:true}));return {error:!document.getElementById("internalLoginError").hidden,session:sessionStorage.getItem("wcp360.internal.session.v1")};})()`);
  expect(rejected.error&&!rejected.session,"customer-identity-rejected",rejected);
  await evaluate(cdp,`(()=>{document.getElementById("internalEmail").value="operator@internal.example.com";document.getElementById("internalLoginForm").dispatchEvent(new Event("submit",{bubbles:true,cancelable:true}));return true;})()`);
  await delay(280);
  const authenticated=await evaluate(cdp,`(()=>{const session=JSON.parse(sessionStorage.getItem("wcp360.internal.session.v1")||"null");return {page:location.pathname,valid:session?.email==="operator@internal.example.com"&&session?.authStrength==="phishing-resistant"&&session?.expiresAt>Date.now(),nav:document.querySelectorAll("#internalNav .wcp-nav-item").length,logout:Boolean(document.querySelector("[data-internal-logout]"))};})()`);
  expect(authenticated.page.includes("internal-distribution.html")&&authenticated.valid&&authenticated.nav===11&&authenticated.logout,"private-login-workflow",authenticated);
  const boundary=await evaluate(cdp,`(async()=>{location.hash="boundary";await new Promise(done=>setTimeout(done,30));const rows=[...document.querySelectorAll("[data-component-row]")];return {components:rows.length,detached:rows.some(row=>row.textContent.includes("WCP360 Insights Connector")),privateServices:rows.filter(row=>row.textContent.includes("Never distributed")).length};})()`);
  expect(boundary.components===19&&boundary.detached&&boundary.privateServices===6,"distribution-boundary",boundary);
  const distributionSource=readFileSync(join(root,"WCP360 Internal","assets","internal-distribution.js"),"utf8");
  expect(distributionSource.includes('component.selected&&!component.detached&&component.family!=="Internal service"'),"allowlist-hard-exclusions",{detachedExcluded:distributionSource.includes("!component.detached"),internalExcluded:distributionSource.includes('component.family!=="Internal service"')});
  const distributionLanes=await evaluate(cdp,`(async()=>{location.hash="distribution";await new Promise(done=>setTimeout(done,40));const state=JSON.parse(localStorage.getItem("wcp360.internal.control-plane.v1")||"{}");return {coreRows:document.querySelectorAll("[data-artifact-row]").length,detachedRows:document.querySelectorAll("[data-detached-artifact-row]").length,coreIncluded:state.detachedArtifacts?.some(item=>item.coreIncluded!==false),extensionIndex:state.detachedManifestPublished};})()`);
  expect(distributionLanes.coreRows===5&&distributionLanes.detachedRows===1&&!distributionLanes.coreIncluded&&!distributionLanes.extensionIndex,"independent-distribution-lanes",distributionLanes);
  const detachedBuild=await evaluate(cdp,`(async()=>{location.hash="builds";await new Promise(done=>setTimeout(done,80));const trigger=document.querySelector("[data-action='create-build']");if(!trigger)return {missing:"create-build",hash:location.hash,title:document.querySelector("h2")?.textContent};trigger.click();const form=document.querySelector("[data-form-kind='create-build']");if(!form)return {missing:"create-build-form"};form.elements.buildTarget.value="WCP360 Insights Connector (detached)";form.elements.buildVersion.value="1.1.0";form.elements.buildCompatibility.value=">=2.6.0 <3.0.0";form.dispatchEvent(new Event("submit",{bubbles:true,cancelable:true}));const state=JSON.parse(localStorage.getItem("wcp360.internal.control-plane.v1")||"{}"),build=state.builds[0],artifact=state.detachedArtifacts[0];return {lane:build?.lane,target:build?.target,version:artifact?.version,compatibility:artifact?.compatibility,coreIncluded:artifact?.coreIncluded,coreArtifacts:state.artifacts?.length};})()`);
  expect(detachedBuild.lane==="Detached extension"&&detachedBuild.target==="WCP360 Insights Connector"&&detachedBuild.version==="1.1.0"&&detachedBuild.compatibility===">=2.6.0 <3.0.0"&&detachedBuild.coreIncluded===false&&detachedBuild.coreArtifacts===5,"detached-build-isolation",detachedBuild);
  const extensionPublication=await evaluate(cdp,`(async()=>{location.hash="distribution";await new Promise(done=>setTimeout(done,80));const trigger=document.querySelector("[data-action='publish-extension-index']");if(!trigger)return {missing:"publish-extension-index",hash:location.hash,title:document.querySelector("h2")?.textContent};trigger.click();const state=JSON.parse(localStorage.getItem("wcp360.internal.control-plane.v1")||"{}");return {extensionIndex:state.detachedManifestPublished,coreManifest:state.manifestPublished,detachedRows:document.querySelectorAll("[data-detached-artifact-row]").length};})()`);
  expect(extensionPublication.extensionIndex&&!extensionPublication.coreManifest&&extensionPublication.detachedRows===2,"independent-extension-publication",extensionPublication);
  const detachedRevocation=await evaluate(cdp,`(()=>{const trigger=document.querySelector("[data-action='revoke-detached-artifact']");if(!trigger)return {missing:"revoke-detached-artifact"};trigger.click();const submit=document.getElementById("internalDrawerSubmit"),input=document.getElementById("confirmToken");if(!submit||!input)return {missing:"revoke-drawer"};const before=submit.disabled;input.value="REVOKE";input.dispatchEvent(new Event("input",{bubbles:true}));const gate=!submit.disabled;document.querySelector("[data-form-kind='revoke-artifact']").dispatchEvent(new Event("submit",{bubbles:true,cancelable:true}));const state=JSON.parse(localStorage.getItem("wcp360.internal.control-plane.v1")||"{}");return {before,gate,revokedDetached:state.revoked?.includes("wcp360-insights-connector-1.1.0.zip"),revokedCore:state.artifacts?.some(item=>state.revoked?.includes(item.name))};})()`);
  expect(detachedRevocation.before&&detachedRevocation.gate&&detachedRevocation.revokedDetached&&!detachedRevocation.revokedCore,"lane-scoped-revocation",detachedRevocation);

  await navigate(cdp,"WCP360 Extensions/wcp360-insights-connector/index.html");
  await evaluate(cdp,`localStorage.removeItem("wcp360-insights-connector-v1");localStorage.removeItem("wcp360.insights.exchange.v1")`);
  await navigate(cdp,"WCP360 Extensions/wcp360-insights-connector/index.html");
  const connector=await evaluate(cdp,`(()=>({tabs:document.querySelectorAll("[data-ic-tab]").length,metrics:document.querySelectorAll(".ic-metric").length,detached:Boolean(document.querySelector("[data-ic-action='open-connect']")),overflow:Math.max(0,document.documentElement.scrollWidth-innerWidth)}))()`);
  expect(connector.tabs===5&&connector.metrics===4&&connector.detached&&connector.overflow<=1,"connector-initial-state",connector);
  const paired=await evaluate(cdp,`(()=>{document.querySelector("[data-ic-action='open-connect']").click();const form=document.querySelector("[data-ic-form='connect']");form.elements.pairingCode.value="PAIR-AUDIT-01";form.dispatchEvent(new Event("submit",{bubbles:true,cancelable:true}));const state=JSON.parse(localStorage.getItem("wcp360-insights-connector-v1")||"{}"),exchange=JSON.parse(localStorage.getItem("wcp360.insights.exchange.v1")||"{}");return {connected:state.connected,installations:exchange.installations?.length||0,secretStored:JSON.stringify(state).includes("PAIR-AUDIT")};})()`);
  expect(paired.connected&&paired.installations===1&&!paired.secretStored,"connector-pairing",paired);
  const audited=await evaluate(cdp,`(()=>{document.querySelector("[data-ic-action='run-audit']").click();const state=JSON.parse(localStorage.getItem("wcp360-insights-connector-v1")||"{}"),exchange=JSON.parse(localStorage.getItem("wcp360.insights.exchange.v1")||"{}");return {findings:state.findings?.length||0,reports:state.reports?.length||0,sharedFindings:exchange.findings?.length||0,sharedReports:exchange.reports?.length||0};})()`);
  expect(audited.findings>=4&&audited.reports>=1&&audited.sharedFindings>=4&&audited.sharedReports>=1,"connector-audit",audited);
  const bugged=await evaluate(cdp,`(()=>{document.querySelector("[data-ic-action='open-bug']").click();const form=document.querySelector("[data-ic-form='bug']");form.elements.title.value="Audit reproduction";form.elements.steps.value="Open ROOT, run check, observe failure";form.dispatchEvent(new Event("submit",{bubbles:true,cancelable:true}));const state=JSON.parse(localStorage.getItem("wcp360-insights-connector-v1")||"{}"),exchange=JSON.parse(localStorage.getItem("wcp360.insights.exchange.v1")||"{}");return {bugs:state.bugs?.length||0,shared:exchange.bugs?.length||0};})()`);
  expect(bugged.bugs===1&&bugged.shared===1,"connector-bug-report",bugged);
  await navigate(cdp,"WCP360 Internal/internal-distribution.html");
  const intake=await evaluate(cdp,`(async()=>{location.hash="fleet";await new Promise(done=>setTimeout(done,40));return {reports:document.querySelectorAll("[data-connector-report]").length,findings:document.querySelectorAll("[data-connector-finding]").length,bugs:document.querySelectorAll("[data-connector-bug]").length};})()`);
  expect(intake.reports>=1&&intake.findings>=4&&intake.bugs>=1,"internal-consented-intake",intake);
  await navigate(cdp,"WCP360 Extensions/wcp360-insights-connector/index.html");
  const detached=await evaluate(cdp,`(()=>{document.querySelector("[data-ic-action='open-detach']").click();const submit=document.querySelector("[data-ic-danger-submit]"),before=submit.disabled,input=document.querySelector("[data-ic-confirm]");input.value="DETACH";input.dispatchEvent(new Event("input",{bubbles:true}));const gate=!submit.disabled;document.querySelector("[data-ic-form='detach']").dispatchEvent(new Event("submit",{bubbles:true,cancelable:true}));const state=JSON.parse(localStorage.getItem("wcp360-insights-connector-v1")||"{}"),exchange=JSON.parse(localStorage.getItem("wcp360.insights.exchange.v1")||"{}");return {before,gate,connected:state.connected,installations:exchange.installations?.length||0};})()`);
  expect(detached.before&&detached.gate&&!detached.connected&&detached.installations===0,"connector-detach",detached);

  for(const width of [390,1440]){
    await cdp.send("Emulation.setDeviceMetricsOverride",{width,height:844,deviceScaleFactor:1,mobile:false});
    for(const path of ["WCP360 Internal/index.html","WCP360 Internal/internal-distribution.html","WCP360 Extensions/wcp360-insights-connector/index.html"]){
      await navigate(cdp,path);
      const layout=await evaluate(cdp,`({overflow:Math.max(0,document.documentElement.scrollWidth-innerWidth),controls:document.querySelectorAll("button,input,select,textarea").length})`);
      expect(layout.overflow<=1&&layout.controls>0,"responsive-layout",{path,width,...layout});
    }
  }
  cdp.close();
}finally{chrome.kill();server.close();}
console.log(JSON.stringify({checks,findings},null,2));
process.exitCode=findings.length?1:0;
