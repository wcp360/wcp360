import { spawn } from "node:child_process";
import { createServer } from "node:http";
import { readFileSync, statSync, writeFileSync } from "node:fs";
import { extname, join, normalize, resolve } from "node:path";
import { tmpdir } from "node:os";

const root=resolve(process.argv[2]||"WCP360 Official Website");
const host="127.0.0.1",sitePort=8772,debugPort=9238;
const chromePath=process.env.CHROME_PATH||"C:/Program Files/Google/Chrome/Application/chrome.exe";
const mime={".html":"text/html; charset=utf-8",".css":"text/css; charset=utf-8",".js":"text/javascript; charset=utf-8",".json":"application/json; charset=utf-8",".svg":"image/svg+xml"};
const safe=file=>{try{return statSync(file).isFile();}catch{return false;}};
const server=createServer((request,response)=>{
  let relative=decodeURIComponent(new URL(request.url,"http://"+host).pathname).split("/").filter(Boolean).join("/");
  if(!relative)relative="index.html";
  const file=normalize(join(root,relative));
  if(!file.startsWith(root)||!safe(file)){response.writeHead(404).end("Not found");return;}
  response.writeHead(200,{"Content-Type":mime[extname(file)]||"application/octet-stream"});
  response.end(readFileSync(file));
});
const delay=ms=>new Promise(done=>setTimeout(done,ms));
async function waitChrome(){for(let i=0;i<80;i+=1){try{const response=await fetch("http://"+host+":"+debugPort+"/json/version");if(response.ok)return;}catch{}await delay(100);}throw new Error("Chrome did not start");}
class Cdp{
  constructor(url){this.id=0;this.pending=new Map();this.listeners=new Map();this.ready=new Promise((ok,fail)=>{this.ws=new WebSocket(url);this.ws.onopen=ok;this.ws.onerror=fail;this.ws.onmessage=e=>this.receive(JSON.parse(e.data));});}
  receive(message){if(message.id){const item=this.pending.get(message.id);if(!item)return;this.pending.delete(message.id);message.error?item.fail(new Error(message.error.message)):item.ok(message.result);return;}for(const listener of this.listeners.get(message.method)||[])listener(message.params);}
  async send(method,params={}){await this.ready;const id=++this.id;return new Promise((ok,fail)=>{this.pending.set(id,{ok,fail});this.ws.send(JSON.stringify({id,method,params}));});}
  on(method,listener){const list=this.listeners.get(method)||[];list.push(listener);this.listeners.set(method,list);return()=>this.listeners.set(method,list.filter(item=>item!==listener));}
  close(){this.ws.close();}
}
async function navigate(cdp,path){
  const loaded=new Promise(resolve=>{const stop=cdp.on("Page.loadEventFired",()=>{stop();resolve(true)})});
  await cdp.send("Page.navigate",{url:"http://"+host+":"+sitePort+"/"+path});
  await Promise.race([loaded,delay(5000)]);
  for(let i=0;i<60;i+=1){const ready=await cdp.send("Runtime.evaluate",{expression:"document.readyState",returnByValue:true});if(["interactive","complete"].includes(ready.result?.value))break;await delay(50);}
  await delay(150);
}
async function evaluate(cdp,expression){const result=await cdp.send("Runtime.evaluate",{expression,awaitPromise:true,returnByValue:true});if(result.exceptionDetails)throw new Error(result.exceptionDetails.exception?.description||result.exceptionDetails.text);return result.result.value;}
const findings=[],checks=[];
const expect=(condition,rule,detail)=>{checks.push(rule);if(!condition)findings.push({rule,detail});};
const pages=["index.html","install.html","features.html","plugins.html","about.html","forum.html","docs.html","roadmap.html","editions.html","status.html","security.html","legal.html","404.html"];
const screenshots=[];
async function capturePage(cdp,name){await delay(350);const data=await cdp.send("Page.captureScreenshot",{format:"png",captureBeyondViewport:false});const path=join(tmpdir(),"wcp360-official-"+name+".png");writeFileSync(path,Buffer.from(data.data,"base64"));screenshots.push(path);}
pages.splice(4,0,'app-installer.html');
pages.splice(7,0,'contact.html');

await new Promise(done=>server.listen(sitePort,host,done));
const chrome=spawn(chromePath,["--headless=new","--disable-gpu","--no-first-run","--no-default-browser-check","--remote-debugging-port="+debugPort,"--user-data-dir="+join(tmpdir(),"wcp360-official-audit-"+process.pid),"about:blank"],{stdio:"ignore"});
try{
  await waitChrome();
  const target=await (await fetch("http://"+host+":"+debugPort+"/json/new?about:blank",{method:"PUT"})).json();
  const cdp=new Cdp(target.webSocketDebuggerUrl);
  await Promise.all([cdp.send("Page.enable"),cdp.send("Runtime.enable"),cdp.send("Log.enable")]);
  cdp.on("Runtime.exceptionThrown",event=>findings.push({rule:"runtime-exception",detail:event.exceptionDetails.exception?.description||event.exceptionDetails.text}));
  cdp.on("Log.entryAdded",event=>{if(event.entry.level==="error")findings.push({rule:"console-error",detail:event.entry.text});});

  await cdp.send("Emulation.setDeviceMetricsOverride",{width:1440,height:1050,deviceScaleFactor:1,mobile:false});
  for(const page of pages){
    await navigate(cdp,page);
    const state=await evaluate(cdp,`({title:document.title,h1:document.querySelector("h1")?.textContent.trim(),header:Boolean(document.querySelector(".site-header")),footer:Boolean(document.querySelector(".site-footer")),overflow:Math.max(0,document.documentElement.scrollWidth-innerWidth),mobileCtaVisible:document.querySelector(".mobile-cta")?.getClientRects().length>0,font:getComputedStyle(document.body).fontFamily,description:Boolean(document.querySelector('meta[name="description"]')?.content),openGraph:Boolean(document.querySelector('meta[property="og:title"]')?.content&&document.querySelector('meta[property="og:description"]')?.content),twitter:Boolean(document.querySelector('meta[name="twitter:title"]')?.content),manifest:Boolean(document.querySelector('link[rel="manifest"]')),touchIcon:Boolean(document.querySelector('link[rel="apple-touch-icon"]')),backToTop:Boolean(document.querySelector(".back-to-top"))})`);
    expect(state.title.includes("WCP360"),"desktop-title:"+page,state);
    expect(Boolean(state.h1),"desktop-heading:"+page,state);
    expect(state.header&&state.footer,"desktop-shell:"+page,state);
    expect(state.overflow<=1,"desktop-overflow:"+page,state);
    expect(!state.mobileCtaVisible,"desktop-single-cta:"+page,state);
    expect(state.description&&state.openGraph&&state.twitter&&state.manifest&&state.touchIcon,"metadata:"+page,state);
    expect(state.backToTop,"back-to-top-control:"+page,state);
  }

  await navigate(cdp,"security.html");
  const featureSectionActive=await evaluate(cdp,`document.querySelector(".nav-mega-trigger")?.dataset.current==="true"`);
  expect(featureSectionActive,"feature-subpage-navigation",featureSectionActive);
  await navigate(cdp,"app-installer.html");
  const pluginSectionActive=await evaluate(cdp,`document.querySelector('.nav-link[href="plugins.html"]')?.getAttribute("aria-current")`);
  expect(pluginSectionActive==="page","plugin-subpage-navigation",pluginSectionActive);

  await navigate(cdp,"index.html");
  const mega=await evaluate(cdp,`(()=>{const trigger=document.querySelector(".nav-mega-trigger"),menu=document.querySelector(".mega-menu");trigger.click();return {expanded:trigger.getAttribute("aria-expanded"),open:document.querySelector(".nav-mega").classList.contains("is-open"),hidden:menu.getAttribute("aria-hidden"),links:menu.querySelectorAll("a").length,rect:menu.getBoundingClientRect().toJSON()};})()`);
  expect(mega.expanded==="true"&&mega.open&&mega.hidden==="false"&&mega.links>=12&&mega.rect.width>700,"desktop-mega-menu",mega);
  await delay(250);
  const megaDesktopData=await cdp.send("Page.captureScreenshot",{format:"png",captureBeyondViewport:false});
  const megaDesktopPath=join(tmpdir(),"wcp360-official-mega-menu-desktop.png");writeFileSync(megaDesktopPath,Buffer.from(megaDesktopData.data,"base64"));screenshots.push(megaDesktopPath);
  const megaClosed=await evaluate(cdp,`(()=>{window.dispatchEvent(new KeyboardEvent("keydown",{key:"Escape"}));return document.querySelector(".nav-mega-trigger").getAttribute("aria-expanded");})()`);
  expect(megaClosed==="false","desktop-mega-menu-close",megaClosed);

  await navigate(cdp,"features.html");
  const features=await evaluate(cdp,`({categories:document.querySelectorAll(".category-section").length,cards:document.querySelectorAll(".capability-card").length,labels:[...document.querySelectorAll(".status-pill")].map(x=>x.textContent.trim().toLowerCase())})`);
  expect(features.categories===7&&features.cards>=45,"feature-coverage",features);
  expect(["core","extension","labs","roadmap"].every(word=>features.labels.some(label=>label.includes(word))),"feature-status-taxonomy",features);
  await capturePage(cdp,"features-desktop");

  await navigate(cdp,"install.html");
  const installDepth=await evaluate(cdp,`({localNav:document.querySelectorAll(".install-local-nav a").length,requirements:document.querySelectorAll(".requirement-card").length,compatibility:document.querySelectorAll(".compatibility-row").length,preflight:document.querySelectorAll(".preflight-checklist article").length,options:document.querySelectorAll(".option-table tbody tr").length,footprint:document.querySelectorAll(".footprint-card").length,network:document.querySelectorAll(".network-table tbody tr").length,channels:document.querySelectorAll(".update-channel").length,updateSteps:document.querySelectorAll(".update-flow>div").length,postInstall:document.querySelectorAll(".post-install-grid article").length})`);
  expect(installDepth.localNav===7&&installDepth.requirements===6&&installDepth.compatibility===6&&installDepth.preflight===5&&installDepth.options===9&&installDepth.footprint===3&&installDepth.network===5&&installDepth.channels===3&&installDepth.updateSteps===6&&installDepth.postInstall===4,"installation-center-depth",installDepth);
  const installTabs=await evaluate(cdp,`(()=>{const archive=document.querySelector('[data-install-method="archive"]');archive.click();const active=document.querySelector('[data-install-panel="archive"]');return {tabs:document.querySelectorAll("[data-install-method]").length,selected:archive.getAttribute("aria-selected"),active:active.classList.contains("is-active"),visible:!active.hidden,otherHidden:[...document.querySelectorAll("[data-install-panel]")].filter(x=>x!==active).every(x=>x.hidden)};})()`);
  expect(installTabs.tabs===3&&installTabs.selected==="true"&&installTabs.active&&installTabs.visible&&installTabs.otherHidden,"install-method-tabs",installTabs);
  await navigate(cdp,"install.html");
  await delay(700);
  const installDesktopData=await cdp.send("Page.captureScreenshot",{format:"png",captureBeyondViewport:false});
  const installDesktopPath=join(tmpdir(),"wcp360-official-install-desktop.png");writeFileSync(installDesktopPath,Buffer.from(installDesktopData.data,"base64"));screenshots.push(installDesktopPath);
  for(const [section,name] of [["#requirements","install-requirements-desktop"],["#preflight","install-preflight-desktop"],["#options","install-options-desktop"],["#updates","install-updates-desktop"]]){
    await evaluate(cdp,`(()=>{document.documentElement.style.scrollBehavior="auto";const target=document.querySelector("${section}");scrollTo(0,target.getBoundingClientRect().top+scrollY-138);return scrollY;})()`);
    await capturePage(cdp,name);
  }

  await navigate(cdp,"plugins.html");
  const filters=await evaluate(cdp,`(()=>{const all=document.querySelectorAll("[data-kind]").length;document.querySelector('[data-filter="official"]').click();const visible=[...document.querySelectorAll("[data-kind]")].filter(card=>!card.hidden).length;return {all,visible,active:document.querySelector('[data-filter="official"]').classList.contains("is-active")};})()`);
  expect(filters.all===9&&filters.visible>0&&filters.visible<filters.all&&filters.active,"marketplace-filter",filters);
  await navigate(cdp,"plugins.html");
  await delay(350);
  const pluginSearch=await evaluate(cdp,`(()=>{const input=document.querySelector("[data-plugin-search]");input.value="forensics";input.dispatchEvent(new Event("input",{bubbles:true}));const visible=[...document.querySelectorAll("[data-kind]")].filter(card=>!card.hidden);return {count:visible.length,title:visible[0]?.querySelector("h3")?.textContent.trim()};})()`);
  expect(pluginSearch.count===1&&pluginSearch.title==="Forensics Lab","marketplace-search",pluginSearch);
  await navigate(cdp,"plugins.html");
  await delay(700);
  const pluginDesktopData=await cdp.send("Page.captureScreenshot",{format:"png",captureBeyondViewport:false});
  const pluginDesktopPath=join(tmpdir(),"wcp360-official-plugins-desktop.png");writeFileSync(pluginDesktopPath,Buffer.from(pluginDesktopData.data,"base64"));screenshots.push(pluginDesktopPath);
  const catalogueDesktopScroll=await evaluate(cdp,`(()=>{document.documentElement.style.scrollBehavior="auto";const target=document.querySelector("#marketplace .service-tools");scrollTo(0,target.getBoundingClientRect().top+scrollY-135);return scrollY;})()`);
  expect(catalogueDesktopScroll>500,"marketplace-desktop-scroll",catalogueDesktopScroll);
  await delay(350);
  const pluginCatalogueDesktopData=await cdp.send("Page.captureScreenshot",{format:"png",captureBeyondViewport:false});
  const pluginCatalogueDesktopPath=join(tmpdir(),"wcp360-official-plugin-catalogue-desktop.png");writeFileSync(pluginCatalogueDesktopPath,Buffer.from(pluginCatalogueDesktopData.data,"base64"));screenshots.push(pluginCatalogueDesktopPath);

  await navigate(cdp,"about.html");
  const aboutDepth=await evaluate(cdp,`({localLinks:document.querySelectorAll(".about-local-nav a").length,tensions:document.querySelectorAll(".about-tension").length,principles:document.querySelectorAll(".about-principle").length,audiences:document.querySelectorAll(".about-audience-card").length,boundaries:document.querySelectorAll(".about-boundary-card").length,stages:document.querySelectorAll(".about-stage").length})`);
  expect(aboutDepth.localLinks===5&&aboutDepth.tensions===3&&aboutDepth.principles===4&&aboutDepth.audiences===4&&aboutDepth.boundaries===3&&aboutDepth.stages===4,"about-presentation-depth",aboutDepth);
  await delay(500);
  const aboutDesktopData=await cdp.send("Page.captureScreenshot",{format:"png",captureBeyondViewport:false});
  const aboutDesktopPath=join(tmpdir(),"wcp360-official-about-desktop.png");writeFileSync(aboutDesktopPath,Buffer.from(aboutDesktopData.data,"base64"));screenshots.push(aboutDesktopPath);
  await evaluate(cdp,`(()=>{document.documentElement.style.scrollBehavior="auto";const target=document.querySelector("#architecture");scrollTo(0,target.getBoundingClientRect().top+scrollY-130);return scrollY;})()`);
  await delay(300);
  const aboutNavSticky=await evaluate(cdp,`(()=>{const nav=document.querySelector(".about-local-nav"),header=document.querySelector(".site-header"),rect=nav.getBoundingClientRect();return {position:getComputedStyle(nav).position,top:rect.top,headerHeight:header.getBoundingClientRect().height,visibleLinks:[...nav.querySelectorAll("a")].filter(link=>link.getClientRects().length).length};})()`);
  expect(aboutNavSticky.position==="sticky"&&Math.abs(aboutNavSticky.top-aboutNavSticky.headerHeight)<=2&&aboutNavSticky.visibleLinks===5,"about-sticky-local-nav",aboutNavSticky);
  const aboutBoundaryDesktopData=await cdp.send("Page.captureScreenshot",{format:"png",captureBeyondViewport:false});
  const aboutBoundaryDesktopPath=join(tmpdir(),"wcp360-official-about-boundary-desktop.png");writeFileSync(aboutBoundaryDesktopPath,Buffer.from(aboutBoundaryDesktopData.data,"base64"));screenshots.push(aboutBoundaryDesktopPath);

  await navigate(cdp,"forum.html");
  const communityDepth=await evaluate(cdp,`({switchLinks:document.querySelectorAll(".community-switchboard>a").length,pathCards:document.querySelectorAll(".community-path-card").length,forumCategories:document.querySelectorAll(".community-forum-list article").length,channels:document.querySelectorAll(".community-channel-card").length,roles:document.querySelectorAll(".community-role").length,unverifiedExternal:[...document.querySelectorAll("a[href]")].filter(a=>/github\\\\.com|discord\\\\.(gg|com)|matrix\\\\.to/.test(a.href)).length,footerTargets:["#repository","#forum","#channels","#contributors"].filter(hash=>document.querySelector('.site-footer a[href="forum.html'+hash+'"]')).length})`);
  expect(communityDepth.switchLinks===4&&communityDepth.pathCards===4&&communityDepth.forumCategories===6&&communityDepth.channels===2&&communityDepth.roles===6&&communityDepth.unverifiedExternal===0&&communityDepth.footerTargets===4,"community-presentation-depth",communityDepth);
  await delay(500);
  const communityDesktopData=await cdp.send("Page.captureScreenshot",{format:"png",captureBeyondViewport:false});
  const communityDesktopPath=join(tmpdir(),"wcp360-official-community-desktop.png");writeFileSync(communityDesktopPath,Buffer.from(communityDesktopData.data,"base64"));screenshots.push(communityDesktopPath);
  await evaluate(cdp,`(()=>{document.documentElement.style.scrollBehavior="auto";const target=document.querySelector("#repository");scrollTo(0,target.getBoundingClientRect().top+scrollY-130);return scrollY;})()`);
  await delay(300);
  const communityNavSticky=await evaluate(cdp,`(()=>{const nav=document.querySelector(".community-local-nav"),header=document.querySelector(".site-header"),rect=nav.getBoundingClientRect();return {position:getComputedStyle(nav).position,top:rect.top,headerHeight:header.getBoundingClientRect().height,visibleLinks:[...nav.querySelectorAll("a")].filter(link=>link.getClientRects().length).length};})()`);
  expect(communityNavSticky.position==="sticky"&&Math.abs(communityNavSticky.top-communityNavSticky.headerHeight)<=2&&communityNavSticky.visibleLinks===5,"community-sticky-local-nav",communityNavSticky);
  const communityRepositoryDesktopData=await cdp.send("Page.captureScreenshot",{format:"png",captureBeyondViewport:false});
  const communityRepositoryDesktopPath=join(tmpdir(),"wcp360-official-community-repository-desktop.png");writeFileSync(communityRepositoryDesktopPath,Buffer.from(communityRepositoryDesktopData.data,"base64"));screenshots.push(communityRepositoryDesktopPath);

  await navigate(cdp,"docs.html");
  const docs=await evaluate(cdp,`({paths:document.querySelectorAll(".docs-path-card").length,officialLinks:document.querySelectorAll('a[href^="https://docs.wcp360.example/"]').length,facts:document.querySelectorAll(".docs-official-facts>div").length,preview:Boolean(document.querySelector(".docs-portal-preview")),legacyAnchors:["wizard","scopes","state","cli","api","extensions","security","contribute"].every(id=>Boolean(document.getElementById(id)))})`);
  expect(docs.paths===3&&docs.officialLinks>=6&&docs.facts===4&&docs.preview&&docs.legacyAnchors,"documentation-gateway",docs);
  await capturePage(cdp,"docs-desktop");
  await evaluate(cdp,`(()=>{document.documentElement.style.scrollBehavior="auto";const target=document.querySelector(".docs-official");scrollTo(0,target.getBoundingClientRect().top+scrollY-115);return scrollY;})()`);
  await capturePage(cdp,"docs-official-desktop");
  for(const page of ["security","roadmap","editions","status","legal","404"]){await navigate(cdp,page+".html");await capturePage(cdp,page+"-desktop");}

  await navigate(cdp,"index.html");
  const copy=await evaluate(cdp,`(async()=>{const button=document.querySelector("[data-copy]");button.click();await new Promise(done=>setTimeout(done,80));return button.textContent.trim();})()`);
  expect(copy==="Copied","copy-command",copy);
  const stickyHeader=await evaluate(cdp,`(async()=>{document.documentElement.style.scrollBehavior="auto";scrollTo(0,Math.min(900,document.documentElement.scrollHeight-innerHeight));await new Promise(done=>setTimeout(done,100));const mount=document.querySelector("[data-site-header]"),header=document.querySelector(".site-header"),rect=mount.getBoundingClientRect(),state={position:getComputedStyle(mount).position,top:rect.top,scrollY,scrolled:header.classList.contains("is-scrolled")};scrollTo(0,0);return state;})()`);
  expect(stickyHeader.position==="sticky"&&Math.abs(stickyHeader.top)<=1&&stickyHeader.scrollY>100&&stickyHeader.scrolled,"sticky-header",stickyHeader);
  const backToTop=await evaluate(cdp,`(async()=>{document.documentElement.style.scrollBehavior="auto";scrollTo(0,Math.min(1200,document.documentElement.scrollHeight-innerHeight));await new Promise(done=>setTimeout(done,120));const button=document.querySelector(".back-to-top"),visible=button.classList.contains("is-visible");button.click();await new Promise(done=>setTimeout(done,800));return {visible,scrollY};})()`);
  expect(backToTop.visible&&backToTop.scrollY<5,"back-to-top-interaction",backToTop);
  const homeDepth=await evaluate(cdp,`({audiences:document.querySelectorAll(".audience-card").length,operationSteps:document.querySelectorAll(".operation-flow>div").length,projectStates:document.querySelectorAll(".project-state-card").length,faq:document.querySelectorAll(".home-faq-list details").length,extensionCards:document.querySelectorAll(".extension-contract-card").length,extensionIcons:document.querySelectorAll(".extension-contract-card .extension-icon svg").length,extensionTerms:document.querySelectorAll(".extension-contract-grid>div").length,externalExtensionIcons:document.querySelectorAll(".extension-contract-card use").length})`);
  expect(homeDepth.audiences===4&&homeDepth.operationSteps===4&&homeDepth.projectStates===4&&homeDepth.faq===6&&homeDepth.extensionCards===3&&homeDepth.extensionIcons===3&&homeDepth.extensionTerms===9&&homeDepth.externalExtensionIcons===0,"home-presentation-depth",homeDepth);
  await navigate(cdp,"index.html");
  await delay(700);
  const desktopData=await cdp.send("Page.captureScreenshot",{format:"png",captureBeyondViewport:false});
  const desktopPath=join(tmpdir(),"wcp360-official-home-desktop.png");writeFileSync(desktopPath,Buffer.from(desktopData.data,"base64"));screenshots.push(desktopPath);
  await evaluate(cdp,`(()=>{document.documentElement.style.scrollBehavior="auto";const target=document.querySelector(".audience-section");scrollTo(0,target.getBoundingClientRect().top+scrollY-80);return scrollY;})()`);
  await delay(250);
  const homeAudienceDesktopData=await cdp.send("Page.captureScreenshot",{format:"png",captureBeyondViewport:false});
  const homeAudienceDesktopPath=join(tmpdir(),"wcp360-official-home-audiences-desktop.png");writeFileSync(homeAudienceDesktopPath,Buffer.from(homeAudienceDesktopData.data,"base64"));screenshots.push(homeAudienceDesktopPath);
  await evaluate(cdp,`(()=>{const target=document.querySelector(".extension-section");scrollTo(0,target.getBoundingClientRect().top+scrollY-80);return scrollY;})()`);
  await delay(250);
  const homeExtensionsDesktopData=await cdp.send("Page.captureScreenshot",{format:"png",captureBeyondViewport:false});
  const homeExtensionsDesktopPath=join(tmpdir(),"wcp360-official-home-extensions-desktop.png");writeFileSync(homeExtensionsDesktopPath,Buffer.from(homeExtensionsDesktopData.data,"base64"));screenshots.push(homeExtensionsDesktopPath);
  await evaluate(cdp,`(()=>{const target=document.querySelector(".home-trust-section");scrollTo(0,target.getBoundingClientRect().top+scrollY-80);return scrollY;})()`);
  await delay(250);
  const homeTrustDesktopData=await cdp.send("Page.captureScreenshot",{format:"png",captureBeyondViewport:false});
  const homeTrustDesktopPath=join(tmpdir(),"wcp360-official-home-trust-desktop.png");writeFileSync(homeTrustDesktopPath,Buffer.from(homeTrustDesktopData.data,"base64"));screenshots.push(homeTrustDesktopPath);

  await cdp.send("Emulation.setDeviceMetricsOverride",{width:390,height:844,deviceScaleFactor:1,mobile:false});
  for(const page of pages){
    await navigate(cdp,page);
    const state=await evaluate(cdp,`({overflow:Math.max(0,document.documentElement.scrollWidth-innerWidth),header:Boolean(document.querySelector(".site-header")),footer:Boolean(document.querySelector(".site-footer"))})`);
    expect(state.header&&state.footer,"mobile-shell:"+page,state);
    expect(state.overflow<=1,"mobile-overflow:"+page,state);
  }
  await navigate(cdp,"index.html");
  await delay(700);
  const mobileType=await evaluate(cdp,`(()=>{const heading=document.querySelector(".hero h1"),rects=[...document.querySelector(".hero h1 .gradient-text").getClientRects()];return {opacity:Number(getComputedStyle(document.querySelector(".hero-copy")).opacity),headingWidth:heading.getBoundingClientRect().width,viewport:innerWidth,maxRight:Math.max(...rects.map(rect=>rect.right)),minLeft:Math.min(...rects.map(rect=>rect.left)),lines:rects.length};})()`);
  expect(mobileType.opacity>.99&&mobileType.maxRight<=mobileType.viewport+1&&mobileType.minLeft>=-1,"mobile-hero-typography",mobileType);
  const menu=await evaluate(cdp,`(async()=>{const button=document.querySelector(".menu-toggle"),mega=document.querySelector(".nav-mega-trigger"),nav=document.querySelector(".primary-nav"),main=document.querySelector("main"),footer=document.querySelector("[data-site-footer]");button.click();await new Promise(done=>setTimeout(done,0));const opened=button.getAttribute("aria-expanded")==="true"&&nav.classList.contains("is-open")&&document.body.classList.contains("nav-open");const isolated=main.hasAttribute("inert")&&footer.hasAttribute("inert")&&nav.getAttribute("aria-hidden")==="false";mega.click();const megaOpened=mega.getAttribute("aria-expanded")==="true"&&document.querySelector(".nav-mega").classList.contains("is-open");window.dispatchEvent(new KeyboardEvent("keydown",{key:"Escape"}));const megaClosed=mega.getAttribute("aria-expanded")==="false"&&button.getAttribute("aria-expanded")==="true";window.dispatchEvent(new KeyboardEvent("keydown",{key:"Escape"}));await new Promise(done=>setTimeout(done,0));return {opened,isolated,megaOpened,megaClosed,closed:button.getAttribute("aria-expanded")==="false"&&!document.body.classList.contains("nav-open"),restored:!main.hasAttribute("inert")&&!footer.hasAttribute("inert")&&nav.getAttribute("aria-hidden")==="true"};})()`);
  expect(menu.opened&&menu.isolated&&menu.megaOpened&&menu.megaClosed&&menu.closed&&menu.restored,"mobile-menu-and-mega",menu);
  const mobileData=await cdp.send("Page.captureScreenshot",{format:"png",captureBeyondViewport:false});
  const mobilePath=join(tmpdir(),"wcp360-official-home-mobile.png");writeFileSync(mobilePath,Buffer.from(mobileData.data,"base64"));screenshots.push(mobilePath);
  await evaluate(cdp,`(()=>{document.documentElement.style.scrollBehavior="auto";const target=document.querySelector(".audience-section");scrollTo(0,target.getBoundingClientRect().top+scrollY-68);return scrollY;})()`);
  await delay(250);
  const homeAudienceMobileData=await cdp.send("Page.captureScreenshot",{format:"png",captureBeyondViewport:false});
  const homeAudienceMobilePath=join(tmpdir(),"wcp360-official-home-audiences-mobile.png");writeFileSync(homeAudienceMobilePath,Buffer.from(homeAudienceMobileData.data,"base64"));screenshots.push(homeAudienceMobilePath);
  await evaluate(cdp,`(()=>{const target=document.querySelector(".extension-section");scrollTo(0,target.getBoundingClientRect().top+scrollY-68);return scrollY;})()`);
  await delay(250);
  const homeExtensionsMobileData=await cdp.send("Page.captureScreenshot",{format:"png",captureBeyondViewport:false});
  const homeExtensionsMobilePath=join(tmpdir(),"wcp360-official-home-extensions-mobile.png");writeFileSync(homeExtensionsMobilePath,Buffer.from(homeExtensionsMobileData.data,"base64"));screenshots.push(homeExtensionsMobilePath);
  await navigate(cdp,"about.html");
  await delay(500);
  const aboutMobileData=await cdp.send("Page.captureScreenshot",{format:"png",captureBeyondViewport:false});
  const aboutMobilePath=join(tmpdir(),"wcp360-official-about-mobile.png");writeFileSync(aboutMobilePath,Buffer.from(aboutMobileData.data,"base64"));screenshots.push(aboutMobilePath);
  await evaluate(cdp,`(()=>{document.documentElement.style.scrollBehavior="auto";const target=document.querySelector("#architecture");scrollTo(0,target.getBoundingClientRect().top+scrollY-122);return scrollY;})()`);
  await delay(300);
  const aboutBoundaryMobileData=await cdp.send("Page.captureScreenshot",{format:"png",captureBeyondViewport:false});
  const aboutBoundaryMobilePath=join(tmpdir(),"wcp360-official-about-boundary-mobile.png");writeFileSync(aboutBoundaryMobilePath,Buffer.from(aboutBoundaryMobileData.data,"base64"));screenshots.push(aboutBoundaryMobilePath);
  await navigate(cdp,"forum.html");
  await delay(500);
  const communityMobileData=await cdp.send("Page.captureScreenshot",{format:"png",captureBeyondViewport:false});
  const communityMobilePath=join(tmpdir(),"wcp360-official-community-mobile.png");writeFileSync(communityMobilePath,Buffer.from(communityMobileData.data,"base64"));screenshots.push(communityMobilePath);
  await evaluate(cdp,`(()=>{document.documentElement.style.scrollBehavior="auto";const target=document.querySelector("#channels");scrollTo(0,target.getBoundingClientRect().top+scrollY-122);return scrollY;})()`);
  await delay(300);
  const communityChannelsMobileData=await cdp.send("Page.captureScreenshot",{format:"png",captureBeyondViewport:false});
  const communityChannelsMobilePath=join(tmpdir(),"wcp360-official-community-channels-mobile.png");writeFileSync(communityChannelsMobilePath,Buffer.from(communityChannelsMobileData.data,"base64"));screenshots.push(communityChannelsMobilePath);
  await navigate(cdp,"docs.html");
  await capturePage(cdp,"docs-mobile");
  await evaluate(cdp,`(()=>{document.documentElement.style.scrollBehavior="auto";const target=document.querySelector(".docs-official");scrollTo(0,target.getBoundingClientRect().top+scrollY-105);return scrollY;})()`);
  await capturePage(cdp,"docs-official-mobile");
  await navigate(cdp,"install.html");
  await delay(700);
  const installMobileData=await cdp.send("Page.captureScreenshot",{format:"png",captureBeyondViewport:false});
  const installMobilePath=join(tmpdir(),"wcp360-official-install-mobile.png");writeFileSync(installMobilePath,Buffer.from(installMobileData.data,"base64"));screenshots.push(installMobilePath);
  for(const [section,name] of [["#preflight","install-preflight-mobile"],["#options","install-options-mobile"],["#updates","install-updates-mobile"]]){
    await evaluate(cdp,`(()=>{document.documentElement.style.scrollBehavior="auto";const target=document.querySelector("${section}");scrollTo(0,target.getBoundingClientRect().top+scrollY-120);return scrollY;})()`);
    await capturePage(cdp,name);
  }
  await navigate(cdp,"plugins.html");
  await delay(700);
  const pluginMobileData=await cdp.send("Page.captureScreenshot",{format:"png",captureBeyondViewport:false});
  const pluginMobilePath=join(tmpdir(),"wcp360-official-plugins-mobile.png");writeFileSync(pluginMobilePath,Buffer.from(pluginMobileData.data,"base64"));screenshots.push(pluginMobilePath);
  const catalogueMobileScroll=await evaluate(cdp,`(()=>{document.documentElement.style.scrollBehavior="auto";const target=document.querySelector("#marketplace .service-tools");scrollTo(0,target.getBoundingClientRect().top+scrollY-82);return scrollY;})()`);
  expect(catalogueMobileScroll>500,"marketplace-mobile-scroll",catalogueMobileScroll);
  await delay(350);
  const pluginCatalogueMobileData=await cdp.send("Page.captureScreenshot",{format:"png",captureBeyondViewport:false});
  const pluginCatalogueMobilePath=join(tmpdir(),"wcp360-official-plugin-catalogue-mobile.png");writeFileSync(pluginCatalogueMobilePath,Buffer.from(pluginCatalogueMobileData.data,"base64"));screenshots.push(pluginCatalogueMobilePath);
  cdp.close();
}finally{chrome.kill();server.close();}
console.log(JSON.stringify({checks:checks.length,findings,screenshots},null,2));
process.exitCode=findings.length?1:0;
