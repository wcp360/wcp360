(function(){
"use strict";
const icon=name=>{const i={
arrow:'<svg aria-hidden="true" viewBox="0 0 24 24" fill="none"><path d="M5 12h14m-5-5 5 5-5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
download:'<svg aria-hidden="true" viewBox="0 0 24 24" fill="none"><path d="M12 3v12m0 0 4-4m-4 4-4-4M5 20h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
menu:'<svg aria-hidden="true" viewBox="0 0 24 24" fill="none"><path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
close:'<svg aria-hidden="true" viewBox="0 0 24 24" fill="none"><path d="m6 6 12 12M18 6 6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
copy:'<svg aria-hidden="true" viewBox="0 0 24 24" fill="none"><rect x="8" y="8" width="11" height="11" rx="2" stroke="currentColor" stroke-width="1.6"/><path d="M16 8V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2" stroke="currentColor" stroke-width="1.6"/></svg>',
check:'<svg aria-hidden="true" viewBox="0 0 24 24" fill="none"><path d="m5 12 4 4L19 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
github:'<svg aria-hidden="true" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 0 0-3.16 19.49c.5.09.68-.22.68-.48v-1.88c-2.78.6-3.37-1.18-3.37-1.18-.45-1.16-1.11-1.47-1.11-1.47-.91-.62.07-.61.07-.61 1 .07 1.53 1.03 1.53 1.03.9 1.53 2.35 1.09 2.92.83.09-.65.35-1.09.64-1.34-2.22-.25-4.56-1.11-4.56-4.94 0-1.09.39-1.98 1.03-2.68-.1-.25-.45-1.27.1-2.64 0 0 .84-.27 2.75 1.02A9.55 9.55 0 0 1 12 6.82c.85 0 1.71.11 2.51.34 1.91-1.3 2.75-1.02 2.75-1.02.55 1.37.2 2.39.1 2.64.64.7 1.03 1.59 1.03 2.68 0 3.84-2.34 4.68-4.57 4.93.36.31.68.92.68 1.86v2.76c0 .27.18.58.69.48A10 10 0 0 0 12 2Z"/></svg>',
shield:'<svg aria-hidden="true" viewBox="0 0 24 24" fill="none"><path d="M12 3 5 6v5c0 4.5 2.8 8.2 7 10 4.2-1.8 7-5.5 7-10V6l-7-3Z" stroke="currentColor" stroke-width="1.7"/><path d="m9 12 2 2 4-4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>'
};return i[name]||""};
const mark='<span class="brand-mark" aria-hidden="true"><svg viewBox="0 0 28 28" fill="none"><path d="M5 7.5 8.6 20l5.4-7 5.4 7L23 7.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/><path d="M9.5 7.5h9" stroke="#50e3c2" stroke-width="2.2" stroke-linecap="round"/></svg></span>';
const navItems=[["home","Home","index.html"],["plugins","Plugins & Extensions","plugins.html"],["about","About","about.html"],["forum","Forum","forum.html"],["docs","Docs","docs.html"]];
const navGlyph=id=>{const paths={home:"M3 11.5 12 4l9 7.5V21h-6v-6H9v6H3v-9.5Z",features:"M4 4h6v6H4V4Zm10 0h6v6h-6V4ZM4 14h6v6H4v-6Zm10 0h6v6h-6v-6Z",plugins:"M9 3h6v4h4v6h-4v4a4 4 0 0 1-8 0v-4H3V7h6V3Z",about:"M12 3a9 9 0 1 0 0 18 9 9 0 0 0 0-18Zm-1 4h2v2h-2V7Zm0 4h2v6h-2v-6Z",forum:"M4 4h16v12H8l-4 4V4Zm4 5h8V7H8v2Zm0 4h6v-2H8v2Z",docs:"M5 3h11a3 3 0 0 1 3 3v15H7a2 2 0 0 1-2-2V3Zm3 3v2h7V6H8Zm0 5v2h7v-2H8Z"};return '<svg aria-hidden="true" viewBox="0 0 24 24" fill="currentColor"><path d="'+paths[id]+'"/></svg>'};
const megaIcon=name=>{const paths={overview:"M4 4h7v7H4V4Zm9 0h7v4h-7V4ZM4 13h7v7H4v-7Zm9-3h7v10h-7V10Z",root:"M12 3 4 7v5c0 4.8 3.2 7.8 8 9 4.8-1.2 8-4.2 8-9V7l-8-4Zm0 5v8m-4-4h8",user:"M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 9c0-4 3-7 7-7s7 3 7 7",infra:"M5 4h14v5H5V4Zm0 11h14v5H5v-5ZM8 9v6m8-6v6M8 6h.01M8 17h.01",security:"M12 3 5 6v5c0 4.5 2.8 8.2 7 10 4.2-1.8 7-5.5 7-10V6l-7-3Zm-3 9 2 2 4-5",backup:"M5 7a4 4 0 0 1 4-4h8a4 4 0 0 1 4 4v10a4 4 0 0 1-4 4H9a4 4 0 0 1-4-4V7Zm4 1h6m-6 4h6m-6 4h3",install:"M12 3v11m0 0 4-4m-4 4-4-4M5 20h14",api:"M8 8 4 12l4 4m8-8 4 4-4 4m-5 3 2-14",roadmap:"M5 4h14v16H5V4Zm4 0v16m4-12h3m-3 4h3m-3 4h3"};return '<svg aria-hidden="true" viewBox="0 0 24 24" fill="none"><path d="'+paths[name]+'" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>'};
const megaLink=(iconName,href,title,copy)=>'<a class="mega-link" href="'+href+'"><span class="mega-link-icon">'+megaIcon(iconName)+'</span><span><strong>'+title+'</strong><small>'+copy+'</small></span></a>';
const featureMega='<div class="nav-mega"><button class="nav-link nav-mega-trigger" type="button" aria-haspopup="true" aria-expanded="false" aria-controls="platform-mega-menu"'+(document.body.dataset.page==="features"?' data-current="true"':"")+'>'+navGlyph("features")+'<span>Features</span><svg class="mega-chevron" aria-hidden="true" viewBox="0 0 20 20" fill="none"><path d="m6 8 4 4 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></button><div class="mega-menu" id="platform-mega-menu" aria-hidden="true"><div class="mega-menu-grid"><section class="mega-group"><p>Control planes</p>'+megaLink("overview","features.html","Feature overview","Seven capability families") + megaLink("root","features.html#governance","WCP-ADMIN / ROOT","Governance and system control") + megaLink("user","features.html#hosting","WCP-USER","Websites, mail, data and files") + megaLink("user","docs.html#scopes","Reseller operations","Delegated accounts and capacity")+'</section><section class="mega-group"><p>Operate</p>'+megaLink("infra","features.html#infrastructure","Infrastructure","Fleet, network and storage") + megaLink("security","security.html","Security & trust","Zero-trust, audit and recovery") + megaLink("backup","features.html#advanced","Backups & disaster recovery","Retention, restore and failover") + megaLink("overview","plugins.html","Software & extensions","Signed, isolated capabilities")+'</section><section class="mega-group"><p>Build & learn</p>'+megaLink("install","install.html","Install WCP360","Requirements and verified methods") + megaLink("api","docs.html","Documentation","Architecture, API and CLI") + megaLink("api","docs.html#extensions","Extension SDK","Manifests and permissions") + megaLink("roadmap","roadmap.html","Roadmap","Delivery status and milestones")+'</section><aside class="mega-promo"><span class="mega-promo-label">START HERE</span><div class="mega-promo-icon">'+megaIcon("install")+'</div><h2>Install without guessing.</h2><p>Check the host, choose a delivery path and review every system change.</p><a href="install.html">Open installation center <span>→</span></a><small>Prototype commands are clearly marked.</small></aside></div><div class="mega-menu-foot"><span>Core-first architecture</span><span>ROOT · Reseller · User separation</span><a href="features.html">Explore all capabilities →</a></div></div></div>';
const page=document.body.dataset.page||"",headerMount=document.querySelector("[data-site-header]");
const renderNavLink=([id,label,href])=>'<a class="nav-link" href="'+href+'"'+(page===id?' aria-current="page"':"")+'>'+navGlyph(id)+'<span>'+label+'</span></a>';
if(headerMount)headerMount.innerHTML='<header class="site-header" id="site-header"><div class="container nav-shell"><a class="brand" href="index.html" aria-label="WCP360 — Home">'+mark+'<span class="brand-copy"><strong>WCP360</strong><small>Web Control Panel 360</small></span></a><nav class="primary-nav" id="primary-nav" aria-label="Main navigation">'+navItems.slice(0,1).map(renderNavLink).join("")+featureMega+navItems.slice(1).map(renderNavLink).join("")+'<a class="button button-primary mobile-cta" href="install.html">'+icon("download")+'Install for free</a></nav><div class="nav-actions"><a class="header-cta" href="install.html"'+(page==="install"?' aria-current="page"':"")+'>'+icon("download")+'<span>Install for free</span></a><button class="menu-toggle" type="button" aria-label="Open navigation" aria-controls="primary-nav" aria-expanded="false">'+icon("menu")+'</button></div></div></header>';
const footerMount=document.querySelector("[data-site-footer]");
if(footerMount)footerMount.innerHTML='<footer class="site-footer"><div class="container"><div class="footer-intro"><div><a class="brand" href="index.html">'+mark+'<span class="brand-copy"><strong>WCP360</strong><small>Web Control Panel 360</small></span></a><p>The sovereign, next-generation control system for web hosting. Built to keep ROOT governance, reseller operations and customer hosting clearly separated.</p></div><a class="footer-status" href="status.html">Prototype systems operational</a></div><div class="footer-grid"><div class="footer-column"><h2>Product</h2><a href="features.html">Features</a><a href="plugins.html">Plugins & Extensions</a><a href="roadmap.html">Changelog / Roadmap</a><a href="editions.html">Pricing / Editions</a></div><div class="footer-column"><h2>Resources</h2><a href="install.html">Installation Center</a><a href="docs.html">Documentation</a><a href="docs.html#api">API Reference</a><a href="status.html">System Status</a><a href="docs.html#cli">CLI Tools</a></div><div class="footer-column"><h2>Community</h2><a href="forum.html#repository">GitHub Repository</a><a href="forum.html#forum">Community Forum</a><a href="forum.html#channels">Discord / Matrix</a><a href="forum.html#contributors">Contributors</a></div><div class="footer-column"><h2>Legal & Trust</h2><a href="legal.html#privacy">Privacy Policy</a><a href="legal.html#terms">Terms of Service</a><a href="security.html">Security / GPG</a><a href="legal.html#gdpr">GDPR Compliance</a></div></div><div class="footer-bottom"><span>© 2026 WCP360 Project. Open Source (MIT License). All rights reserved.</span><div class="footer-bottom-links"><span>'+icon("github")+'Source planned</span><span>'+icon("shield")+'Signed releases</span></div></div></div></footer>';
const header=document.querySelector(".site-header"),menuButton=document.querySelector(".menu-toggle"),nav=document.querySelector(".primary-nav");
const megaRoot=document.querySelector(".nav-mega"),megaTrigger=document.querySelector(".nav-mega-trigger"),megaMenu=document.querySelector(".mega-menu");
const setMega=open=>{if(!megaRoot||!megaTrigger||!megaMenu)return;megaRoot.classList.toggle("is-open",open);megaTrigger.setAttribute("aria-expanded",String(open));megaMenu.setAttribute("aria-hidden",String(!open));header?.classList.toggle("mega-active",open)};
const setMenu=open=>{if(!menuButton||!nav)return;menuButton.setAttribute("aria-expanded",String(open));menuButton.setAttribute("aria-label",open?"Close navigation":"Open navigation");menuButton.innerHTML=icon(open?"close":"menu");nav.classList.toggle("is-open",open);document.body.classList.toggle("nav-open",open);if(!open)setMega(false)};
if(megaRoot&&megaTrigger&&megaMenu){
  let closeTimer;
  const hoverCapable=matchMedia("(hover:hover) and (pointer:fine)");
  megaTrigger.addEventListener("click",()=>setMega(megaTrigger.getAttribute("aria-expanded")!=="true"));
  megaRoot.addEventListener("mouseenter",()=>{if(hoverCapable.matches){clearTimeout(closeTimer);setMega(true)}});
  megaRoot.addEventListener("mouseleave",()=>{if(hoverCapable.matches)closeTimer=setTimeout(()=>setMega(false),120)});
  megaRoot.addEventListener("focusin",()=>setMega(true));
  megaRoot.addEventListener("focusout",()=>setTimeout(()=>{if(!megaRoot.contains(document.activeElement))setMega(false)},0));
  document.addEventListener("pointerdown",event=>{if(!megaRoot.contains(event.target))setMega(false)});
}
if(menuButton&&nav){
  menuButton.addEventListener("click",()=>setMenu(menuButton.getAttribute("aria-expanded")!=="true"));
  nav.addEventListener("click",event=>{if(event.target.closest("a"))setMenu(false)});
  window.addEventListener("keydown",event=>{if(event.key==="Escape"){const wasMegaOpen=megaTrigger?.getAttribute("aria-expanded")==="true";setMega(false);if(wasMegaOpen)megaTrigger?.focus();else setMenu(false)}});
  window.addEventListener("resize",()=>{if(innerWidth>900)setMenu(false)});
}
const setHeaderState=()=>{if(header)header.classList.toggle("is-scrolled",scrollY>8)};setHeaderState();addEventListener("scroll",setHeaderState,{passive:true});
document.querySelectorAll("[data-copy]").forEach(button=>button.addEventListener("click",async()=>{const source=document.querySelector(button.getAttribute("data-copy")),value=source?(source.dataset.command||source.textContent||"").trim():"";if(!value)return;try{await navigator.clipboard.writeText(value)}catch(_){const a=document.createElement("textarea");a.value=value;a.style.cssText="position:fixed;opacity:0";document.body.appendChild(a);a.select();document.execCommand("copy");a.remove()}const original=button.innerHTML;button.innerHTML=icon("check")+"<span>Copied</span>";button.setAttribute("aria-label","Command copied");setTimeout(()=>{button.innerHTML=original;button.setAttribute("aria-label","Copy command")},1800)}));
const filters=document.querySelectorAll("[data-filter]"),pluginSearch=document.querySelector("[data-plugin-search]");let activeFilter="all";const applyCatalogFilters=()=>{const query=(pluginSearch?.value||"").toLowerCase().trim();let visible=0;document.querySelectorAll("[data-kind]").forEach(card=>{const kinds=(card.dataset.kind||"").split(" "),search=(card.dataset.search||card.textContent||"").toLowerCase(),show=(activeFilter==="all"||kinds.includes(activeFilter))&&(!query||search.includes(query));card.hidden=!show;if(show)visible+=1});document.querySelector(".catalog-empty")?.classList.toggle("is-visible",visible===0)};filters.forEach(button=>button.addEventListener("click",()=>{activeFilter=button.dataset.filter||"all";filters.forEach(i=>i.classList.toggle("is-active",i===button));applyCatalogFilters()}));pluginSearch?.addEventListener("input",applyCatalogFilters);
const revealItems=document.querySelectorAll(".reveal");if("IntersectionObserver"in window&&!matchMedia("(prefers-reduced-motion: reduce)").matches){const observer=new IntersectionObserver(entries=>entries.forEach(entry=>{if(entry.isIntersecting){entry.target.classList.add("is-visible");observer.unobserve(entry.target)}}),{threshold:.12});revealItems.forEach(item=>observer.observe(item));setTimeout(()=>revealItems.forEach(item=>item.classList.add("is-visible")),800)}else revealItems.forEach(item=>item.classList.add("is-visible"));
const search=document.querySelector("[data-doc-search]");if(search)search.addEventListener("input",()=>{const q=search.value.toLowerCase().trim();document.querySelectorAll("[data-doc-link]").forEach(link=>{link.hidden=!!q&&!link.textContent.toLowerCase().includes(q)})});
const installTabs=[...document.querySelectorAll("[data-install-method]")],installPanels=[...document.querySelectorAll("[data-install-panel]")];
const selectInstallMethod=(name,focus=false)=>{
  installTabs.forEach(tab=>{const active=tab.dataset.installMethod===name;tab.setAttribute("aria-selected",String(active));tab.tabIndex=active?0:-1;if(active&&focus)tab.focus()});
  installPanels.forEach(panel=>{const active=panel.dataset.installPanel===name;panel.hidden=!active;panel.classList.toggle("is-active",active)});
};
installTabs.forEach((tab,index)=>{
  tab.addEventListener("click",()=>selectInstallMethod(tab.dataset.installMethod));
  tab.addEventListener("keydown",event=>{let target=index;if(event.key==="ArrowRight"||event.key==="ArrowDown")target=(index+1)%installTabs.length;else if(event.key==="ArrowLeft"||event.key==="ArrowUp")target=(index-1+installTabs.length)%installTabs.length;else if(event.key==="Home")target=0;else if(event.key==="End")target=installTabs.length-1;else return;event.preventDefault();selectInstallMethod(installTabs[target].dataset.installMethod,true)});
});
})();

(()=>{
  'use strict';
  const groups=[...document.querySelectorAll('.mega-group')];
  const buildGroup=groups.find(group=>group.querySelector('p')?.textContent.trim()==='Build & learn');
  if(buildGroup&&!buildGroup.querySelector('[href*=app-installer]')){
    const link=document.createElement('a');
    link.className='mega-link';
    link.href='app-installer.html';
    link.innerHTML='<span class=mega-link-icon><b>APP</b></span><span><strong>One-Click App Installer</strong><small>Curated apps, plans and rollback</small></span>';
    const roadmap=buildGroup.querySelector('[href*=roadmap]');
    buildGroup.insertBefore(link,roadmap||null);
  }
  const columns=[...document.querySelectorAll('.footer-column')];
  const product=columns.find(column=>column.querySelector('h2')?.textContent.trim()==='Product');
  if(product&&!product.querySelector('[href*=app-installer]')){
    const link=document.createElement('a');
    link.href='app-installer.html';
    link.textContent='One-Click App Installer';
    product.insertBefore(link,product.children[2]||null);
  }
  const legal=columns.find(column=>column.querySelector('h2')?.textContent.trim()==='Legal & Trust');
  if(legal&&!legal.querySelector('[href*=contact]')){
    const link=document.createElement('a');
    link.href='contact.html';
    link.textContent='Contact';
    legal.insertBefore(link,legal.children[1]||null);
  }
  if(document.body.classList.contains('plugin-page')){
    const card=[...document.querySelectorAll('.service-card')].find(item=>item.querySelector('h3')?.textContent.trim()==='Application Installer');
    const link=card?.querySelector('.service-card-links a');
    if(link){
      link.href='app-installer.html';
      link.innerHTML='Explore One-Click App Installer <span>&rarr;</span>';
    }
  }

  const page=document.body.dataset.page||'';
  const sectionAliases={
    'app-installer':'plugins',
    security:'features',
    roadmap:'features',
    editions:'features',
    status:'features'
  };
  const activeSection=sectionAliases[page]||page;
  if(activeSection==='features'){
    document.querySelector('.nav-mega-trigger')?.setAttribute('data-current','true');
  }else{
    const activeLink=document.querySelector('.primary-nav .nav-link[href="'+activeSection+'.html"]');
    if(activeLink) activeLink.setAttribute('aria-current','page');
  }

  const ensureMeta=(attribute,name,content)=>{
    let meta=document.head.querySelector('meta['+attribute+'="'+name+'"]');
    if(!meta){
      meta=document.createElement('meta');
      meta.setAttribute(attribute,name);
      document.head.appendChild(meta);
    }
    meta.setAttribute('content',content);
  };
  const descriptions={
    'not-found':'The requested WCP360 page could not be found. Return to the official project home or open the documentation.',
    'app-installer':'Explore the WCP360 one-click application installer with curated apps, policy controls, preflight plans, backups and rollback.',
    contact:'Contact the WCP360 project through the right route for product questions, partnerships, community help or security disclosure.'
  };
  const description=document.head.querySelector('meta[name="description"]')?.content||descriptions[page]||'Explore WCP360, the open-source control plane for sovereign web hosting operations.';
  ensureMeta('name','description',description);
  ensureMeta('property','og:type','website');
  ensureMeta('property','og:site_name','WCP360');
  ensureMeta('property','og:title',document.title);
  ensureMeta('property','og:description',description);
  ensureMeta('name','twitter:card','summary');
  ensureMeta('name','twitter:title',document.title);
  ensureMeta('name','twitter:description',description);
  if(!document.head.querySelector('link[rel="manifest"]')){
    const manifest=document.createElement('link');
    manifest.rel='manifest';
    manifest.href='site.webmanifest';
    document.head.appendChild(manifest);
  }
  if(!document.head.querySelector('link[rel="apple-touch-icon"]')){
    const touchIcon=document.createElement('link');
    touchIcon.rel='apple-touch-icon';
    touchIcon.href='assets/apple-touch-icon.png';
    document.head.appendChild(touchIcon);
  }

  const menuButton=document.querySelector('.menu-toggle');
  const primaryNav=document.querySelector('.primary-nav');
  const shellContent=[document.querySelector('main'),document.querySelector('[data-site-footer]')].filter(Boolean);
  const mobileNavigation=matchMedia('(max-width: 900px)');
  let focusBeforeMenu=null;
  const visibleMenuItems=()=>[menuButton,...primaryNav.querySelectorAll('a[href],button:not([disabled])')]
    .filter(item=>item&&item.getClientRects().length&&getComputedStyle(item).visibility!=='hidden');
  const syncMobileAccessibility=()=>{
    if(!menuButton||!primaryNav) return;
    const open=document.body.classList.contains('nav-open')&&mobileNavigation.matches;
    if(mobileNavigation.matches) primaryNav.setAttribute('aria-hidden',String(!open));
    else primaryNav.removeAttribute('aria-hidden');
    shellContent.forEach(element=>{
      if(open) element.setAttribute('inert','');
      else element.removeAttribute('inert');
    });
    if(open&&!focusBeforeMenu) focusBeforeMenu=document.activeElement;
    if(!open) focusBeforeMenu=null;
  };
  if(menuButton&&primaryNav){
    new MutationObserver(syncMobileAccessibility).observe(document.body,{attributes:true,attributeFilter:['class']});
    mobileNavigation.addEventListener?.('change',syncMobileAccessibility);
    syncMobileAccessibility();
    document.addEventListener('keydown',event=>{
      const open=document.body.classList.contains('nav-open')&&mobileNavigation.matches;
      if(event.key==='Escape'&&open){
        setTimeout(()=>menuButton.focus({preventScroll:true}),0);
        return;
      }
      if(event.key!=='Tab'||!open) return;
      const items=visibleMenuItems();
      if(!items.length) return;
      const first=items[0],last=items[items.length-1];
      if(event.shiftKey&&document.activeElement===first){
        event.preventDefault();
        last.focus();
      }else if(!event.shiftKey&&document.activeElement===last){
        event.preventDefault();
        first.focus();
      }
    });
  }

  if(document.querySelector('main')){
    const backToTop=document.createElement('button');
    backToTop.type='button';
    backToTop.className='back-to-top';
    backToTop.setAttribute('aria-label','Back to top');
    backToTop.innerHTML='<svg aria-hidden="true" viewBox="0 0 24 24" fill="none"><path d="m6 14 6-6 6 6M12 8v11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg><span>Top</span>';
    document.body.appendChild(backToTop);
    const syncBackToTop=()=>backToTop.classList.toggle('is-visible',scrollY>700);
    backToTop.addEventListener('click',()=>scrollTo({top:0,behavior:matchMedia('(prefers-reduced-motion: reduce)').matches?'auto':'smooth'}));
    addEventListener('scroll',syncBackToTop,{passive:true});
    syncBackToTop();
  }
})();
