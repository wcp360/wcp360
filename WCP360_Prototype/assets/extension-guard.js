(()=>{
  'use strict';
  const id=document.body.dataset.extensionRequired;if(!id)return;
  const defaults={'app-installer':true,'ha-dr':true,'developer-pipelines':true};
  let extension=null;
  try{const state=JSON.parse(localStorage.getItem('wcp360-platform-software-v1')||'null');extension=state?.plugins?.find(item=>item.id===id)||null}catch(error){}
  const enabled=extension?extension.enabled!==false:defaults[id]!==false;
  document.body.dataset.extensionState=enabled?'enabled':'disabled';
  if(enabled)return;
  const extensionRoutes={
    'app-installer':['app-installer.html','application-catalog.html'],
    'ha-dr':['cluster.html'],
    'developer-pipelines':['deployments.html','git-repositories.html']
  };
  const pruneNavigation=()=>{
    const routes=new Set(extensionRoutes[id]||[]);
    document.querySelectorAll('a[href]').forEach(link=>{
      const route=(link.getAttribute('href')||'').split('#')[0].toLowerCase();
      if(routes.has(route)&&(link.closest('.wcp-dashboard-sidebar,.sidebar,#wcpCommandBackdrop')))link.remove();
    });
    document.querySelectorAll('[data-nav-group]').forEach(group=>{
      const count=group.querySelector('.wcp-nav-count');
      const links=group.querySelectorAll('.wcp-nav-item').length;
      if(count)count.textContent=String(links);
    });
  };
  pruneNavigation();
  document.addEventListener('DOMContentLoaded',pruneNavigation,{once:true});
  const host=document.querySelector('#applicationCatalog,#appInstaller,[data-extension-host]');if(!host)return;
  const scope=document.body.dataset.applicationCatalog==='root'?'ROOT':'USER';
  host.className='';
  host.innerHTML=`<div class="wcp-page-head"><div><div class="wcp-page-eyebrow">${scope} · Optional extension</div><div class="wcp-page-heading">${extension?.name||'Extension'} is disabled</div><div class="wcp-page-sub">The WCP360 core remains operational. This optional surface has been withdrawn cleanly and no core hosting service depends on it.</div></div></div><section class="wcp-panel"><div class="wcp-panel-head"><div><div class="wcp-panel-title">Deterministic degraded state</div><div class="wcp-panel-sub">Extension ID: ${id}</div></div><span class="wcp-status warning">Disabled</span></div><div style="padding:16px;display:grid;gap:12px"><div class="wcp-callout info"><div class="wcp-callout-title">What still works</div><div class="wcp-callout-text">Authentication, accounts, websites, domains, databases, mail, files, local backups, service control, audit and signed platform updates remain available.</div></div><div class="wcp-callout warning"><div class="wcp-callout-title">Extension behavior</div><div class="wcp-callout-text">New extension jobs are rejected, running jobs drain or checkpoint, owned data is preserved and navigation contributions are removed.</div></div>${scope==='ROOT'?'<div class="wcp-page-actions" style="justify-content:flex-start"><a class="wcp-action-btn primary" href="software-manager.html">Open Software &amp; Extensions</a><a class="wcp-action-btn" href="canonical-admin-dashboard.html">Return to Fleet Overview</a></div>':'<div class="wcp-page-actions" style="justify-content:flex-start"><a class="wcp-action-btn primary" href="canonical-user-dashboard.html">Return to Overview</a></div>'}</div></section>`;
  document.querySelectorAll('.cat-drawer,.cat-drawer-backdrop,.app-drawer,.app-drawer-backdrop').forEach(node=>node.remove());
})();
