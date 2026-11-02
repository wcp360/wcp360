
(function(){
  const root=document.documentElement;
  const themeKey='wcp360-theme';
  const sideKey='wcp360-sidebar-collapsed';
  let commandIndex=0;
  let returnFocus=null;
  let sidebarReturnFocus=null;

  function qs(s,c=document){return c.querySelector(s)}
  function qsa(s,c=document){return [...c.querySelectorAll(s)]}
  function setOpen(el,on){if(el){el.classList.toggle('open',on);el.inert=!on;}}
  function setExpanded(selector,on){qsa(selector).forEach(x=>x.setAttribute('aria-expanded',String(on)))}
  function syncSidebarState(){
    for(const [selector,toggles,breakpoint] of [['.wcp-dashboard-sidebar','[data-wcp-sidebar-toggle]',1100],['.sidebar','[data-legacy-sidebar-toggle]',880]]){
      const sidebar=qs(selector);if(!sidebar)continue;
      const mobile=innerWidth<=breakpoint;
      if(mobile&&selector!=='.sidebar')sidebar.classList.remove('collapsed');
      if(mobile&&selector==='.sidebar')document.body.classList.remove('wcp-legacy-collapsed');
      if(!mobile)sidebar.classList.remove('mobile-open');
      const opened=sidebar.classList.contains('mobile-open');
      sidebar.inert=mobile&&!opened;
      sidebar.id=sidebar.id||'wcp-main-navigation';
      const collapsed=selector==='.sidebar'?document.body.classList.contains('wcp-legacy-collapsed'):sidebar.classList.contains('collapsed');
      qsa(toggles).forEach(toggle=>{toggle.setAttribute('aria-controls',sidebar.id);toggle.setAttribute('aria-expanded',String(mobile?opened:!collapsed));});
      if(selector!=='.sidebar')qs('.wcp-sidebar-overlay')?.classList.toggle('active',mobile&&opened);
    }
  }
  function closeMobileNavigation(restore=true){
    const opened=qsa('.wcp-dashboard-sidebar.mobile-open,.sidebar.mobile-open');
    opened.forEach(sidebar=>sidebar.classList.remove('mobile-open'));syncSidebarState();
    if(opened.length&&restore&&sidebarReturnFocus?.isConnected)sidebarReturnFocus.focus();
  }
  function toggleNavigation(sidebar,legacy,trigger){
    const mobile=innerWidth<=(legacy?880:1100);
    if(mobile){
      const opening=!sidebar.classList.contains('mobile-open');
      if(opening){sidebarReturnFocus=trigger;document.dispatchEvent(new CustomEvent('wcp:overlay-opening',{detail:{source:'navigation'}}));}
      sidebar.classList.toggle('mobile-open',opening);syncSidebarState();
      if(opening)qs('button,a[href],input',sidebar)?.focus();else if(sidebarReturnFocus?.isConnected)sidebarReturnFocus.focus();
    }else{
      if(legacy)document.body.classList.toggle('wcp-legacy-collapsed');
      else{sidebar.classList.toggle('collapsed');try{localStorage.setItem(sideKey,String(sidebar.classList.contains('collapsed')))}catch{}}
      syncSidebarState();
    }
  }
  function trapFocus(event,container){
    if(event.key!=='Tab'||!container)return;
    const focusable=qsa('a[href],button,input,select,textarea,[tabindex]',container).filter(x=>!x.disabled&&x.tabIndex>=0&&!x.closest('[inert],[hidden]')&&x.getClientRects().length);
    if(!focusable.length){event.preventDefault();container.setAttribute('tabindex','-1');container.focus();return;}
    const first=focusable[0],last=focusable[focusable.length-1];
    if(!focusable.includes(document.activeElement)){event.preventDefault();(event.shiftKey?last:first).focus()}
    else if(event.shiftKey&&document.activeElement===first){event.preventDefault();last.focus()}
    else if(!event.shiftKey&&document.activeElement===last){event.preventDefault();first.focus()}
  }

  function applyTheme(theme){return window.WCPUI.theme.apply(theme)}
  let savedTheme='system'; try{savedTheme=localStorage.getItem(themeKey)||'system'}catch(e){}
  applyTheme(savedTheme);

  function openCommand(){
    const b=qs('#wcpCommandBackdrop'); if(!b)return;
    if(b.classList.contains('open')){qs('#wcpCommandInput')?.focus();return;}
    const trigger=document.activeElement;
    document.dispatchEvent(new CustomEvent('wcp:overlay-opening',{detail:{source:'command'}}));
    returnFocus=trigger?.isConnected&&!trigger.closest('[inert]')?trigger:document.activeElement;
    b.inert=false;
    b.classList.add('open');
    b.setAttribute('aria-hidden','false');
    setExpanded('[data-open-command],.wcp-search',true);
    const i=qs('#wcpCommandInput'); if(i){i.value='';i.setAttribute('aria-expanded','true');setTimeout(()=>i.focus(),10)}
    filterCommands('');
  }
  function closeCommand(){
    const b=qs('#wcpCommandBackdrop');
    if(!b?.classList.contains('open'))return;
    setOpen(b,false);if(b)b.setAttribute('aria-hidden','true');
    setExpanded('[data-open-command],.wcp-search',false);
    qs('#wcpCommandInput')?.setAttribute('aria-expanded','false');
    if(returnFocus?.isConnected&&!returnFocus.closest('[inert]'))returnFocus.focus();
  }
  function filterCommands(q){
    const results=qsa('#wcpCommandBackdrop .wcp-command-result');
    const val=(q||'').trim().toLowerCase();
    results.forEach(r=>r.style.display=(!val||normalizeSearch(r.textContent).includes(normalizeSearch(val)))?'grid':'none');
    const visible=results.filter(r=>r.style.display!=='none');
    const list=qs('#wcpCommandBackdrop .wcp-command-scroll');
    if(list){let empty=qs('[data-command-empty]',list);if(!empty){empty=document.createElement('div');empty.dataset.commandEmpty='';empty.className='runtime-job-empty';empty.textContent='No matching tools. Try another search.';empty.setAttribute('role','status');list.appendChild(empty);}empty.hidden=visible.length>0;
      qsa('.wcp-command-group',list).forEach(group=>{let next=group.nextElementSibling,shown=false;while(next&&!next.classList.contains('wcp-command-group')){if(next.classList.contains('wcp-command-result')&&next.style.display!=='none')shown=true;next=next.nextElementSibling;}group.hidden=!shown;});}
    qs('#wcpCommandInput')?.removeAttribute('aria-activedescendant');
    results.forEach(r=>{r.classList.remove('active');r.setAttribute('aria-selected','false')});
    commandIndex=0;if(visible[0]){visible[0].classList.add('active');visible[0].setAttribute('aria-selected','true');qs('#wcpCommandInput')?.setAttribute('aria-activedescendant',visible[0].id)}
  }
  function moveCommand(delta){
    const visible=qsa('#wcpCommandBackdrop .wcp-command-result').filter(r=>r.style.display!=='none');
    if(!visible.length)return;
    visible.forEach(r=>{r.classList.remove('active');r.setAttribute('aria-selected','false')});
    commandIndex=(commandIndex+delta+visible.length)%visible.length;
    visible[commandIndex].classList.add('active');visible[commandIndex].setAttribute('aria-selected','true');qs('#wcpCommandInput')?.setAttribute('aria-activedescendant',visible[commandIndex].id);visible[commandIndex].scrollIntoView({block:'nearest'});
  }

  function closePopovers(except){
    qsa('.wcp-popover.open').forEach(p=>{if(p!==except){setOpen(p,false);p.setAttribute('aria-hidden','true')}});
    for(const [selector,id] of [['[data-toggle-notifications]','#wcpNotifications'],['[data-toggle-profile]','#wcpProfileMenu']])setExpanded(selector,!!qs(id)?.classList.contains('open'));
  }
  function markCurrentNavigation(){
    const page=(location.pathname.split('/').pop()||'index.html').toLowerCase();
    const route=page+(location.hash||(page==='reseller.html'?'#overview':''));
    const sidebar=qs('.wcp-dashboard-sidebar')||qs('.sidebar');
    const candidates=qsa('.wcp-nav-item,.nav',sidebar||document);
    candidates.forEach(a=>{a.classList.remove('active');a.removeAttribute('aria-current')});
    const owner=routeOwner(route);
    let current=candidates.find(a=>canonicalRoute(a.getAttribute('href')||'')===(owner?.href||canonicalRoute(route)));
    if(!current&&sidebar){
      const canonical=sidebar.classList.contains('wcp-dashboard-sidebar');
      const section=qs('.wcp-nav-items',sidebar);
      if(section){
        current=document.createElement('a');current.href=page;current.dataset.contextualRoute='';
        const title=(qs('h1')?.textContent||document.title.split('—')[0]||page).trim();
        current.className=canonical?'wcp-nav-item':'nav';
        current.innerHTML=canonical?`<span class="material-symbols-rounded wcp-nav-icon">subdirectory_arrow_right</span><span class="wcp-nav-label"></span>`:`<span class="ico">↳</span><span></span>`;
        current.querySelector('span:last-child').textContent=title;section.appendChild(current);
      }
    }
    if(current){current.classList.add('active');current.setAttribute('aria-current','page')}
    if(sidebar)syncNavGroups(sidebar);
  }

  function normalizeLegacySidebar(){
    const sidebar=qs('.sidebar');if(!sidebar||qs('.wcp-dashboard-sidebar'))return;
    document.body.classList.add('wcp-legacy-shell');sidebar.setAttribute('aria-label','Navigation principale');
    qsa('.nav',sidebar).forEach(a=>a.classList.toggle('active',location.pathname.endsWith(a.getAttribute('href')||'')));
    let overlay=qs('.wcp-legacy-sidebar-overlay');
    if(!overlay){overlay=document.createElement('button');overlay.className='wcp-legacy-sidebar-overlay';overlay.type='button';overlay.setAttribute('aria-label','Fermer la navigation');sidebar.after(overlay)}
    const header=qs('.header');
    if(header&&!qs('[data-legacy-sidebar-toggle]',header)){
      const toggle=document.createElement('button');toggle.type='button';toggle.className='wcp-menu-btn wcp-legacy-menu';toggle.dataset.legacySidebarToggle='';toggle.setAttribute('aria-label','Afficher ou réduire la navigation');toggle.innerHTML='<span class="material-symbols-rounded" aria-hidden="true">menu</span>';header.prepend(toggle);
    }
  }

  const navProducts={
    user:[
      ['Workspace',[['canonical-user-dashboard.html','dashboard','Overview'],['websites.html','web','Websites'],['domains.html','language','Domains & DNS']]],
      ['Services',[['email.html','mail','Email & Webmail','mail rules routing filters responders autoresponders retention catch-all message preview'],['databases.html','database','Databases'],['file-manager.html','folder_open','File Manager'],['file-access-center.html','folder_managed','File Access & Transfers'],['backups.html','backup','Backups'],['cron.html','schedule','Cron Jobs'],['ssl.html','verified_user','SSL / TLS']]],
      ['Operations',[['app-installer.html','apps','App Installer'],['deployments.html','rocket_launch','Deployments'],['git-repositories.html','account_tree','Git Repositories'],['site-analytics.html','query_stats','Site Analytics & Logs'],['web-compatibility.html','settings_ethernet','Web Compatibility','redirects MIME cache compression HSTS indexes referrer web rules'],['app-health.html','monitoring','Metrics & Health'],['api-webhooks.html','key','API & Webhooks']]],
      ['Account',[['canonical-security.html','shield','Security'],['notifications-center.html','notifications','Notifications'],['profile-account-settings.html','manage_accounts','Account settings'],['help-support.html','help','Help & Support']]]
    ],
    reseller:[
      ['Workspace',[['reseller.html#overview','space_dashboard','Overview'],['reseller.html#customers','business','Customer Accounts'],['reseller.html#packages','inventory_2','Packages'],['reseller.html#capacity','data_usage','Capacity']]],
      ['Delegation',[['reseller.html#services','dns','Customer Services'],['reseller.html#governance','policy','Governance & ACLs'],['reseller.html#brand','palette','Brand & Nameservers']]],
      ['Operations',[['reseller.html#transfers','move_up','Transfers'],['reseller.html#notifications','notifications','Notifications'],['reseller.html#audit','manage_search','Audit & Support']]]
    ],
    root:[
      ['Command',[['canonical-admin-dashboard.html','space_dashboard','Fleet Overview'],['incidents.html','warning','Incidents'],['jobs.html','pending_actions','Jobs'],['approvals.html','approval','Approvals']]],
      ['Hosting',[['hosting-accounts.html','business','Hosting Accounts'],['provisioning-blueprints.html','folder_copy','Provisioning Blueprints'],['lifecycle-hooks.html','webhook','Lifecycle Hooks'],['users-roles.html','group','Identity & Access'],['usage-metering.html','receipt_long','Usage & Metering']]],
      ['Infrastructure',[['servers.html','dns','Servers'],['server-profiles.html','view_quilt','Server Profiles'],['cluster.html','hub','Clusters'],['admin-monitoring.html','monitoring','Platform Monitoring'],['service-manager.html','settings_applications','Service Manager'],['service-config-schemas.html','schema','Service Configuration'],['processes-workers.html','memory','Processes & Workers'],['resource-diagnostics.html','troubleshoot','Resource Diagnostics'],['virtualization.html','computer','Virtualization'],['storage-volumes.html','hard_drive','Storage & Volumes'],['maintenance.html','build','Maintenance']]],
      ['Network & DNS',[['admin-dns.html','language','DNS Infrastructure'],['dns-toolbox.html','travel_explore','DNS & Network Toolbox'],['provider-adapters.html','cloud_sync','Provider Adapters'],['ip-management.html','lan','IP Management'],['network-interfaces.html','cable','Network Interfaces'],['network-routes.html','route','Routing & NAT'],['firewall.html','shield','Firewall']]],
      ['Web & Data',[['web-runtime.html','developer_mode','Runtime & Web Stack'],['web-routing.html','alt_route','Web Routing & Domains','web policy redirect status MIME constraints'],['file-transfer-services.html','folder_shared','File Transfer Services'],['web-default-pages.html','web_asset','Default Web Pages'],['application-catalog.html','apps','Application Catalog'],['database-services.html','database','Database Services'],['admin-mail.html','mail','Mail Infrastructure'],['certificates.html','verified_user','SSL & AutoSSL']]],
      ['Continuity',[['admin-backups.html','backup','Backup Infrastructure'],['continuity-control.html','emergency_share','Continuity & Disaster Recovery'],['transfers.html','move_up','Transfers & Migrations'],['transfer-importer.html','move_down','Migration Importer'],['bulk-restore.html','inventory_2','Bulk Restore'],['restore-tests.html','restore','Recovery Tests']]],
      ['Security',[['admin-security.html','security','Platform Security'],['credential-security.html','password','Credential Security'],['privacy-governance.html','privacy_tip','Privacy & Data Governance'],['privileged-session.html','key','Privileged Sessions'],['break-glass.html','emergency','Break-glass Access'],['impersonation.html','supervisor_account','Impersonation']]],
      ['System',[['license-management.html','license','License & Trial'],['updates.html','system_update','Updates'],['software-manager.html','deployed_code','Software & Plugins'],['extension-platform.html','extension','Extension Platform'],['api-automation.html','code','API & Automation'],['delivery-platform.html','account_tree','Delivery Platform'],['scheduled-tasks.html','schedule','Scheduled Tasks'],['system-settings.html','tune','System Settings'],['platform-readiness.html','fact_check','Platform Readiness'],['notification-routing.html','notifications_active','Notification Routing'],['logs-diagnostics.html','troubleshoot','Logs & Diagnostics'],['audit-explorer.html','manage_search','Audit Explorer'],['support-bundle.html','diagnosis','Support Bundle'],['terminal.html','terminal','ROOT Terminal']]]
    ],
    design:[
      ['Design System',[['index.html','home','Prototype Home'],['components.html','widgets','Shared UI Library'],['interaction-lab.html','interests','Interaction Lab'],['validation-checklist.html','fact_check','Validation'],['theme-system.html','palette','Themes'],['signature-index.html','draw','Signatures']]]
    ]
  };


  // One registry feeds sidebar ownership, the home directory and command search.
  const routeAliases={
  "server-logs.html": "logs-diagnostics.html#logs",
  "app-maintenance.html": "app-lifecycle.html#maintenance",
  "environment-variables.html": "app-lifecycle.html#variables",
  "app-runtime.html": "app-lifecycle.html#runtime",
  "environments.html": "app-lifecycle.html#environments",
  "builds.html": "deployments.html#builds",
  "releases.html": "deployments.html#releases",
  "release-rollback.html": "deployments.html?action=rollback#releases",
  "admin-dashboard.html": "canonical-admin-dashboard.html",
  "user-dashboard.html": "canonical-user-dashboard.html",
  "server-overview.html": "canonical-server-overview.html",
  "website-overview.html": "canonical-website-overview.html",
  "security.html": "canonical-security.html",
  "dns.html": "canonical-dns.html",
  "dns-zone.html": "canonical-dns.html",
  "roles.html": "users-roles.html#roles",
  "plans.html": "users-roles.html#plans",
  "feature-lists.html": "users-roles.html#features",
  "effective-permissions.html": "users-roles.html#permissions",
  "demo-accounts.html": "users-roles.html#demo",
  "resellers-access.html": "users-roles.html#resellers",
  "quotas.html": "users-roles.html#quotas",
  "account-operations.html": "hosting-accounts.html#accounts",
  "server-configuration.html": "system-settings.html#server",
  "language-runtimes.html": "web-runtime.html#languages",
  "runtime-modules.html": "web-runtime.html#modules",
  "web-stack-builder.html": "web-runtime.html#profiles",
  "mail-server-config.html": "admin-mail.html#smtp",
  "webmail-services.html": "admin-mail.html#webmail",
  "mail-queue.html": "admin-mail.html#queue",
  "dns-zone-templates.html": "admin-dns.html#templates",
  "dns-cluster.html": "admin-dns.html#cluster",
  "reseller-governance.html": "reseller.html#governance",
  "create-website.html": "websites.html?action=create",
  "database-create.html": "databases.html?action=new-database",
  "database-user-create.html": "databases.html?action=new-user",
  "mailbox-create.html": "email.html?action=new-mailbox",
  "mail-alias-create.html": "email.html?action=new-alias",
  "mail-rules.html": "email.html#rules",
  "mailbox-detail.html": "email.html#mailboxes",
  "mail-antispam.html": "email.html#security",
  "mail-authentication.html": "email.html#security",
  "mail-deliverability.html": "email.html#trace",
  "mail-logs.html": "email.html#trace",
  "mailboxes.html": "email.html#mailboxes",
  "mail-aliases.html": "email.html#routing",
  "file-trash.html": "file-manager.html#trash",
  "file-settings.html": "file-manager.html#settings",
  "file-transfers.html": "file-manager.html#transfers",
  "file-upload.html": "file-manager.html?action=upload",
  "file-search.html": "file-manager.html#files",
  "backup-policy.html": "backups.html#policy",
  "backup-storage.html": "backups.html#storage",
  "backup-activity.html": "backups.html#activity",
  "restore-points.html": "backups.html#points",
  "restore-wizard.html": "backups.html?action=restore",
  "restore-backup.html": "backups.html?action=restore",
  "restore-review.html": "backups.html?action=restore",
  "dns-record-add.html": "canonical-dns.html?action=add",
  "dns-record-edit.html": "canonical-dns.html?action=edit",
  "dnssec.html": "canonical-dns.html#dnssec",
  "dns-propagation.html": "canonical-dns.html#propagation",
  "ssl-tls.html": "ssl.html#certificates",
  "ssl-certificate-detail.html": "ssl.html?action=inspect"
};
  const routeFamilies={
  "canonical-user-dashboard.html": [
    "delete-resource.html"
  ],
  "websites.html": [
    "create-website.html",
    "canonical-website-overview.html",
    "app-runtime.html",
    "app-maintenance.html",
    "app-lifecycle.html",
    "environment-variables.html",
    "environments.html"
  ],
  "domains.html": [
    "domain-add.html",
    "domain-detail.html",
    "canonical-dns.html",
    "dns-record-add.html",
    "dns-record-edit.html",
    "dns-propagation.html",
    "dnssec.html"
  ],
  "email.html": [
    "mailboxes.html",
    "mailbox-create.html",
    "mailbox-detail.html",
    "mail-aliases.html",
    "mail-alias-create.html",
    "mail-domain-add.html",
    "mail-domain-detail.html",
    "mail-antispam.html",
    "mail-authentication.html",
    "mail-deliverability.html",
    "mail-logs.html",
    "webmail.html"
  ],
  "file-manager.html": [
    "file-editor.html",
    "file-upload.html",
    "file-permissions.html",
    "file-search.html",
    "file-settings.html",
    "file-trash.html"
  ],
  "file-access-center.html": [
    "file-access.html"
  ],
  "databases.html": [
    "database-access.html",
    "database-backups.html",
    "database-create.html",
    "database-detail.html",
    "database-diagnostics.html",
    "database-transfer.html",
    "database-user-create.html",
    "database-user-detail.html",
    "database-users.html"
  ],
  "backups.html": [
    "backup-activity.html",
    "backup-policy.html",
    "backup-storage.html",
    "restore-backup.html",
    "restore-points.html",
    "restore-review.html",
    "restore-wizard.html"
  ],
  "deployments.html": [
    "builds.html",
    "releases.html",
    "release-rollback.html"
  ],
  "api-webhooks.html": [
    "webhooks.html",
    "webhook-deliveries.html",
    "automations.html"
  ],
  "ssl.html": [
    "ssl-tls.html",
    "ssl-certificate-detail.html"
  ],
  "canonical-security.html": [
    "security-audit.html",
    "security-auth.html",
    "security-keys.html",
    "security-network.html",
    "security-sessions.html",
    "security-tokens.html",
    "security-waf.html"
  ],
  "app-health.html": [
    "monitoring.html",
    "monitoring-alerts.html",
    "monitoring-capacity.html",
    "monitoring-correlation.html",
    "monitoring-metrics.html",
    "monitoring-slo.html"
  ],
  "notifications-center.html": [
    "notification-preferences.html"
  ],
  "servers.html": [
    "canonical-server-overview.html",
    "server-actions.html",
    "server-maintenance.html",
    "server-resources.html",
    "server-runtime.html",
    "server-services.html"
  ],
  "cluster.html": [
    "cluster-failover.html",
    "cluster-maintenance.html",
    "cluster-nodes.html",
    "cluster-replication.html",
    "cluster-scenarios.html",
    "cluster-storage.html"
  ],
  "network-interfaces.html": [
    "network-diagnostics.html",
    "network-ports.html"
  ],
  "network-routes.html": [
    "network-nat.html"
  ],
  "firewall.html": [
    "firewall-rules.html"
  ],
  "incidents.html": [
    "incident-detail.html"
  ],
  "jobs.html": [
    "job-detail.html",
    "job-queues.html"
  ],
  "approvals.html": [
    "approval-detail.html"
  ],
  "audit-explorer.html": [
    "audit.html"
  ],
  "updates.html": [
    "updates-batch.html",
    "updates-exclusions.html",
    "updates-history.html",
    "updates-kernel.html",
    "updates-packages.html",
    "updates-schedule.html"
  ],
  "processes-workers.html": [
    "process-detail.html",
    "worker-pools.html"
  ],
  "storage-volumes.html": [
    "volume-detail.html"
  ],
  "notification-routing.html": [
    "notification-delivery.html"
  ],
  "logs-diagnostics.html": [
    "server-logs.html",
    "error-remediation.html"
  ],
  "service-manager.html": [
    "service-detail.html"
  ],
  "system-settings.html": [
    "setup-wizard.html"
  ],
  "validation-checklist.html": [
    "validation-accessibility.html",
    "validation-contrast.html",
    "validation-keyboard-focus.html",
    "validation-reduced-motion.html",
    "validation-responsive.html",
    "validation-rtl-i18n.html",
    "validation-screen-reader.html"
  ]
};
  const normalizeSearch=value=>String(value||'').normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase();
  function canonicalRoute(href){
    const [base,fragment]=String(href||'').split('#');
    const [page,query]=base.split('?');
    const target=routeAliases[page.toLowerCase()]||page;
    const [baseTarget,defaultHash]=target.split('#');
    const [canonical,defaultQuery]=baseTarget.split('?');
    const params=new URLSearchParams(defaultQuery||'');new URLSearchParams(query||'').forEach((value,key)=>params.set(key,value));
    const search=params.toString();
    return canonical+(search?'?'+search:'')+((fragment||defaultHash)?'#'+(fragment||defaultHash):'');
  }
  function navigationEntries(scope){
    return (navProducts[scope]||[]).flatMap(([category,items])=>items.filter(([href])=>extensionRouteAvailable(href)).map(([href,icon,label,keywords=''])=>({href:canonicalRoute(href),icon,label,category,scope,keywords:[...(routeFamilies[href]||[]),keywords].join(' ')})));
  }
  function routeOwner(href){
    const route=canonicalRoute(href).replace(/\?[^#]*/,''),page=route.split('#')[0];
    const entries=Object.keys(navProducts).flatMap(navigationEntries);
    return entries.find(item=>item.href===route)||entries.find(item=>item.href===page)||entries.find(item=>(routeFamilies[item.href]||[]).includes(page))||entries.find(item=>item.href.split('#')[0]===page)||null;
  }
  window.WCPNavigation={resolve:canonicalRoute,entries:navigationEntries,owner:routeOwner,matches:(item,term)=>normalizeSearch([item.label,item.category,item.keywords,item.href].join(' ')).includes(normalizeSearch(term)),available:extensionRouteAvailable};

  function inferProduct(page,headings){
    const declared=(document.body.dataset.productScope||'').toLowerCase();
    if(['user','reseller','root','design'].includes(declared))return declared;
    const owner=routeOwner(page);if(owner)return owner.scope;
    const design=/^(?:index|components|interaction-lab|signature-index|theme-system|validation-)/.test(page);
    if(design||headings.includes('design lab'))return'design';
    if(['reseller.html','reseller-governance.html'].includes(page))return'reseller';
    const rootExact=new Set(['admin-dashboard.html','admin-mail.html','canonical-admin-dashboard.html','canonical-server-overview.html','servers.html','server-overview.html','server-configuration.html','firewall.html','cluster.html','incidents.html','jobs.html','approvals.html','approval-detail.html','audit.html','audit-explorer.html','updates.html','maintenance.html','resellers-access.html','users-roles.html','roles.html','plans.html','feature-lists.html','effective-permissions.html','demo-accounts.html','quotas.html','usage-metering.html','system-settings.html','license-management.html','terminal.html','break-glass.html','impersonation.html','privileged-session.html','processes-workers.html','resource-diagnostics.html','storage-volumes.html','worker-pools.html','notification-delivery.html','notification-routing.html','service-detail.html','service-config-schemas.html','service-manager.html','support-bundle.html','error-remediation.html','hosting-accounts.html','account-operations.html','provisioning-blueprints.html','lifecycle-hooks.html','web-default-pages.html','admin-dns.html','dns-zone-templates.html','dns-cluster.html','ip-management.html','database-services.html','web-runtime.html','language-runtimes.html','runtime-modules.html','web-stack-builder.html','file-transfer-services.html','application-catalog.html','webmail-services.html','mail-server-config.html','mail-queue.html','software-manager.html','extension-platform.html','scheduled-tasks.html','platform-readiness.html','api-automation.html','logs-diagnostics.html','certificates.html','transfers.html','transfer-importer.html','bulk-restore.html','restore-tests.html','server-profiles.html']);
    const rootPrefix=/^(?:admin-|server-|cluster-|network-|firewall-|updates-|incident-|job-|approval-|process-|worker-|volume-|reseller)/;
    if(rootExact.has(page)||rootPrefix.test(page))return'root';
    return'user';
  }

  const navSectionIcons={Workspace:'space_dashboard',Services:'widgets',Operations:'rocket_launch',Account:'manage_accounts',Delegation:'account_tree',Command:'terminal',Hosting:'business',Infrastructure:'dns','Network & DNS':'lan','Web & Data':'developer_mode',Continuity:'backup',Security:'shield',System:'settings','Design System':'design_services'};
  const extensionRouteMap={'application-catalog.html':'app-installer','app-installer.html':'app-installer','cluster.html':'ha-dr','deployments.html':'developer-pipelines','git-repositories.html':'developer-pipelines'};
  const extensionDefaults={'app-installer':true,'ha-dr':true,'developer-pipelines':true};
  function extensionEnabled(id){try{const state=JSON.parse(localStorage.getItem('wcp360-platform-software-v1')||'null');const item=state?.plugins?.find(plugin=>plugin.id===id);return item?item.enabled!==false:extensionDefaults[id]!==false}catch(error){return extensionDefaults[id]!==false}}
  function extensionRouteAvailable(href){const page=canonicalRoute(href).split(/[?#]/)[0].toLowerCase(),extension=extensionRouteMap[page];return !extension||extensionEnabled(extension)}
  function setNavGroup(sidebar,group,open,exclusive=true){
    if(exclusive&&open)qsa('[data-nav-group]',sidebar).forEach(item=>{if(item!==group)setNavGroup(sidebar,item,false,false)});
    group.classList.toggle('open',open);
    qs('[data-nav-group-toggle]',group)?.setAttribute('aria-expanded',String(open));
    const items=qs('.wcp-nav-items',group);if(items)items.hidden=!open;
  }
  function syncNavGroups(sidebar){
    if(sidebar.dataset.navSearching==='true')return;
    const groups=qsa('[data-nav-group]',sidebar);if(!groups.length)return;
    const active=groups.find(group=>qs('.wcp-nav-item.active,.nav.active',group))||groups[0];
    groups.forEach(group=>setNavGroup(sidebar,group,group===active,false));
  }
  function renderProductNavigation(sidebar,scope){
    const canonical=sidebar.classList.contains('wcp-dashboard-sidebar');
    const sections=(navProducts[scope]||navProducts.user).map(([heading,items])=>[heading,items.filter(([href])=>extensionRouteAvailable(href)).map(([href,...rest])=>[canonicalRoute(href),...rest])]).filter(([,items])=>items.length);
    const search=['root','reseller','user'].includes(scope)?`<label class="wcp-root-nav-search"><span class="material-symbols-rounded" aria-hidden="true">search</span><input type="search" data-root-nav-search placeholder="Find a ${scope.toUpperCase()} tool" aria-label="Find a ${scope} tool"><kbd>/</kbd></label>`:'';
    if(canonical){
      const nav=qs('.wcp-nav',sidebar);if(!nav)return;
      nav.innerHTML=search+sections.map(([heading,items],index)=>`<div class="wcp-nav-section ${index===0?'open':''}" data-nav-group><button type="button" class="wcp-nav-group-toggle" data-nav-group-toggle aria-expanded="${index===0?'true':'false'}"><span class="material-symbols-rounded wcp-nav-group-icon">${navSectionIcons[heading]||items[0]?.[1]||'folder'}</span><span class="wcp-nav-heading">${heading}</span><span class="wcp-nav-count" aria-hidden="true">${items.length}</span><span class="material-symbols-rounded wcp-nav-chevron" aria-hidden="true">expand_more</span></button><div class="wcp-nav-items" ${index===0?'':'hidden'}>${items.map(([href,icon,label])=>`<a class="wcp-nav-item" href="${href}"><span class="material-symbols-rounded wcp-nav-icon">${icon}</span><span class="wcp-nav-label">${label}</span></a>`).join('')}</div></div>`).join('');
      const status=qs('.wcp-sidebar-status',sidebar);if(status)status.innerHTML=scope==='root'?'<div class="wcp-sidebar-status-row"><span>Privileged administration</span><span class="wcp-status-dot"></span></div><div class="wcp-sidebar-meta">ROOT · EU-PROD</div>':scope==='reseller'?'<div class="wcp-sidebar-status-row"><span>Delegated administration</span><span class="wcp-status-dot"></span></div><div class="wcp-sidebar-meta">RESELLER · EXAMPLE PARTNER</div>':scope==='user'?'<div class="wcp-sidebar-status-row"><span>Account operational</span><span class="wcp-status-dot"></span></div><div class="wcp-sidebar-meta">USER · example.com</div>':'<div class="wcp-sidebar-status-row"><span>Prototype workspace</span><span class="wcp-status-dot"></span></div><div class="wcp-sidebar-meta">Internal design tools</div>';
    }else{
      const scroll=qs('.nav-scroll',sidebar);if(!scroll)return;
      scroll.innerHTML=search+sections.map(([heading,items],index)=>`<div class="group ${index===0?'open':''}" data-nav-group><button type="button" class="wcp-nav-group-toggle" data-nav-group-toggle aria-expanded="${index===0?'true':'false'}"><span class="material-symbols-rounded wcp-nav-group-icon">${navSectionIcons[heading]||items[0]?.[1]||'folder'}</span><span class="group-title">${heading}</span><span class="wcp-nav-count" aria-hidden="true">${items.length}</span><span class="material-symbols-rounded wcp-nav-chevron" aria-hidden="true">expand_more</span></button><div class="wcp-nav-items" ${index===0?'':'hidden'}>${items.map(([href,icon,label])=>`<a data-nav class="nav" href="${href}"><span class="ico material-symbols-rounded">${icon}</span><span>${label}</span></a>`).join('')}</div></div>`).join('');
      const status=qs('.statusbox',sidebar);if(status)status.innerHTML=scope==='root'?'<div class="wcp-sidebar-status-row"><span>Privileged administration</span><span class="wcp-status-dot"></span></div><div class="wcp-sidebar-meta">ROOT · EU-PROD</div>':scope==='reseller'?'<div class="wcp-sidebar-status-row"><span>Delegated administration</span><span class="wcp-status-dot"></span></div><div class="wcp-sidebar-meta">RESELLER · EXAMPLE PARTNER</div>':scope==='user'?'<div class="wcp-sidebar-status-row"><span>Account operational</span><span class="wcp-status-dot"></span></div><div class="wcp-sidebar-meta">USER · example.com</div>':'<div class="wcp-sidebar-status-row"><span>Prototype workspace</span><span class="wcp-status-dot"></span></div><div class="wcp-sidebar-meta">Internal design tools</div>';
    }
    const input=qs('[data-root-nav-search]',sidebar);if(input){
      input.addEventListener('input',()=>{const term=normalizeSearch(input.value.trim());sidebar.dataset.navSearching=String(Boolean(term));qsa(canonical?'.wcp-nav-section':'.group',sidebar).forEach(group=>{let shown=0;qsa(canonical?'.wcp-nav-item':'.nav',group).forEach(link=>{const entry=navigationEntries(scope).find(item=>item.href===link.getAttribute('href'));const match=!term||window.WCPNavigation.matches(entry||{label:link.textContent},term);link.hidden=!match;if(match)shown++});group.hidden=!shown;if(term&&shown)setNavGroup(sidebar,group,true,false)});if(!term){qsa('[data-nav-group]',sidebar).forEach(group=>group.hidden=false);syncNavGroups(sidebar)}});
      document.addEventListener('keydown',event=>{
        if(event.key!=='/'||event.defaultPrevented||event.ctrlKey||event.metaKey||event.altKey||document.activeElement?.closest('input,textarea,select,[contenteditable]')||qs('[role="dialog"][aria-hidden="false"],dialog[open]'))return;
        event.preventDefault();
        if(innerWidth<=(canonical?1100:880)&&!sidebar.classList.contains('mobile-open'))toggleNavigation(sidebar,!canonical,document.activeElement);
        else if(canonical&&sidebar.classList.contains('collapsed')){sidebar.classList.remove('collapsed');syncSidebarState();}
        else if(!canonical&&document.body.classList.contains('wcp-legacy-collapsed')){document.body.classList.remove('wcp-legacy-collapsed');syncSidebarState();}
        input.focus();
      });
    }
  }

  function applyAccessScope(){
    const sidebar=qs('.wcp-dashboard-sidebar')||qs('.sidebar');if(!sidebar)return;
    const page=(location.pathname.split('/').pop()||'index.html').toLowerCase();
    const headings=qsa('.wcp-nav-heading,.group-title',sidebar).map(x=>x.textContent.trim().toLowerCase());
    const scope=inferProduct(page,headings),label=scope.toUpperCase();
    document.body.dataset.accessScope=scope;sidebar.dataset.accessScope=scope;
    sidebar.setAttribute('aria-label',`WCP360 ${label}`);
    renderProductNavigation(sidebar,scope);

    let badge=qs('.wcp-scope-badge',sidebar);
    if(!badge){badge=document.createElement('span');badge.className='wcp-scope-badge';const brand=qs('.wcp-dashboard-brand',sidebar);(brand||sidebar).appendChild(badge)}
    badge.textContent=label;badge.setAttribute('aria-label',scope==='design'?'Produit interne DESIGN':scope==='root'?'Produit administrateur WCP360 ROOT':scope==='reseller'?'Produit revendeur WCP360 RESELLER':'Produit client WCP360 USER');

    qs('.wcp-scope-switch',sidebar)?.remove();
    adaptProductChrome(scope);
    ensurePortalProfile(scope);
  }
  function adaptProductChrome(scope){
    if(scope!=='root')return;
    const create=qs('.wcp-dashboard-header [data-open-drawer="wcpCreateDrawer"]');
    if(create)create.innerHTML='<span class="material-symbols-rounded">person_add</span><span class="label">Create account</span>';
    const createDrawer=qs('#wcpCreateDrawer');if(createDrawer){
      const title=qs('.wcp-drawer-title',createDrawer),sub=qs('.wcp-drawer-sub',createDrawer),body=qs('.wcp-drawer-body',createDrawer);
      if(title)title.textContent='ROOT create';if(sub)sub.textContent='Provision privileged resources with review and audit';
      if(body)body.innerHTML='<a class="wcp-command-result" href="hosting-accounts.html"><div class="wcp-command-result-icon"><span class="material-symbols-rounded">business</span></div><div><div class="wcp-command-result-title">Hosting account</div><div class="wcp-command-result-sub">Account, package, quotas, DNS and mail</div></div></a><a class="wcp-command-result" href="admin-dns.html"><div class="wcp-command-result-icon"><span class="material-symbols-rounded">language</span></div><div><div class="wcp-command-result-title">DNS zone</div><div class="wcp-command-result-sub">Authoritative zone with DNSSEC policy</div></div></a><a class="wcp-command-result" href="servers.html"><div class="wcp-command-result-icon"><span class="material-symbols-rounded">dns</span></div><div><div class="wcp-command-result-title">Register server</div><div class="wcp-command-result-sub">Enroll a node into the managed fleet</div></div></a><a class="wcp-command-result" href="transfers.html"><div class="wcp-command-result-icon"><span class="material-symbols-rounded">move_up</span></div><div><div class="wcp-command-result-title">Transfer or migration</div><div class="wcp-command-result-sub">Import or move hosting accounts</div></div></a>';
    }
    qsa('#wcpCommandBackdrop .wcp-command-scroll').forEach(list=>{list.innerHTML='<div class="wcp-command-group">ROOT actions</div><a class="wcp-command-result active" href="hosting-accounts.html"><div class="wcp-command-result-icon"><span class="material-symbols-rounded">person_add</span></div><div><div class="wcp-command-result-title">Create hosting account</div><div class="wcp-command-result-sub">Package, placement, quotas and access policy</div></div><div class="wcp-command-result-hint">Open</div></a><a class="wcp-command-result" href="service-manager.html"><div class="wcp-command-result-icon"><span class="material-symbols-rounded">restart_alt</span></div><div><div class="wcp-command-result-title">Restart a managed service</div><div class="wcp-command-result-sub">Preflight, drain, restart and health check</div></div><div class="wcp-command-result-hint">Review</div></a><a class="wcp-command-result" href="admin-dns.html"><div class="wcp-command-result-icon"><span class="material-symbols-rounded">language</span></div><div><div class="wcp-command-result-title">Manage DNS zones</div><div class="wcp-command-result-sub">Authoritative cluster and DNSSEC</div></div><div class="wcp-command-result-hint">Open</div></a><div class="wcp-command-group">Infrastructure</div><a class="wcp-command-result" href="canonical-server-overview.html"><div class="wcp-command-result-icon"><span class="material-symbols-rounded">dns</span></div><div><div class="wcp-command-result-title">srv-par-01</div><div class="wcp-command-result-sub">Server · EU-PROD · Healthy</div></div><div class="wcp-command-result-hint">Open</div></a><a class="wcp-command-result" href="incidents.html"><div class="wcp-command-result-icon"><span class="material-symbols-rounded">warning</span></div><div><div class="wcp-command-result-title">Open infrastructure incidents</div><div class="wcp-command-result-sub">1 critical item requires attention</div></div><div class="wcp-command-result-hint">Open</div></a>'});
    const profile=qs('#wcpProfileMenu');if(profile){const title=qs('.wcp-popover-title',profile);if(title)title.textContent='Yann · ROOT Operator';const items=qsa('.wcp-popover-item-title',profile);if(items[0])items[0].textContent='Operator profile';if(items[1])items[1].textContent='Privileged workspace'}
  }
  function ensurePortalProfile(scope){
    if(!['user','reseller','root'].includes(scope))return;
    const header=qs('.wcp-dashboard-header')||qs('.header'),avatar=qs('.wcp-avatar,.avatar,[data-toggle-profile]',header||document);if(!header||!avatar)return;
    avatar.dataset.toggleProfile='';avatar.setAttribute('role','button');avatar.setAttribute('tabindex','0');avatar.setAttribute('aria-haspopup','dialog');avatar.setAttribute('aria-expanded','false');avatar.setAttribute('aria-label',scope==='root'?'Open ROOT operator profile':scope==='reseller'?'Open RESELLER profile':'Open USER profile');
    let profile=qs('#wcpProfileMenu');
    if(!profile){profile=document.createElement('div');profile.id='wcpProfileMenu';profile.className='wcp-popover';profile.innerHTML=`<div class="wcp-popover-title">${scope==='root'?'Yann · ROOT Operator':scope==='reseller'?'Yann · Example Partner':'Yann · Example Company'}</div><a class="wcp-popover-item" href="${scope==='root'?'users-roles.html':scope==='reseller'?'reseller-governance.html':'profile-account-settings.html'}"><div class="wcp-popover-icon"><span class="material-symbols-rounded">person</span></div><div><div class="wcp-popover-item-title">${scope==='root'?'Operator profile':scope==='reseller'?'Reseller profile':'Profile & account'}</div><div class="wcp-popover-item-sub">Identity, security and preferences</div></div></a>`;avatar.insertAdjacentElement('afterend',profile)}
    if(!qs('.wcp-portal-switch',profile)){profile.insertAdjacentHTML('beforeend',`<div class="wcp-popover-divider"></div><a class="wcp-popover-item" href="index.html">Prototype home &amp; tool directory</a><section class="wcp-portal-switch" aria-label="Prototype portal switch"><div class="wcp-portal-switch-head"><div><strong>Portal preview</strong><small>Prototype only · permissions remain isolated</small></div><span class="wcp-prototype-chip">DEMO</span></div><div class="wcp-portal-switch-options" role="group" aria-label="Choose WCP360 portal"><a href="canonical-user-dashboard.html" data-portal-switch="user" class="${scope==='user'?'active':''}" ${scope==='user'?'aria-current="page"':''}><span class="material-symbols-rounded">person</span><strong>USER</strong><small>Hosting account</small></a><a href="reseller.html" data-portal-switch="reseller" class="${scope==='reseller'?'active':''}" ${scope==='reseller'?'aria-current="page"':''}><span class="material-symbols-rounded">storefront</span><strong>RESELLER</strong><small>Delegated WHM</small></a><a href="canonical-admin-dashboard.html" data-portal-switch="root" class="${scope==='root'?'active':''}" ${scope==='root'?'aria-current="page"':''}><span class="material-symbols-rounded">admin_panel_settings</span><strong>ROOT</strong><small>Control plane</small></a></div><p>Production requires an authorized ROOT session. This shortcut exists only to review the full prototype.</p></section>`)}
  }
  function enhanceRootDashboard(){
    const page=(location.pathname.split('/').pop()||'').toLowerCase();
    if(!['canonical-admin-dashboard.html','admin-dashboard.html'].includes(page)||qs('.wcp-root-capabilities'))return;
    const host=qs('.wcp-content-inner')||qs('.content');if(!host)return;
    const tools=[
      ['hosting-accounts.html','business','Hosting Accounts','Lifecycle, suspension and account placement'],
      ['admin-dns.html','language','DNS Infrastructure','Zones, templates, cluster, DNSSEC and providers'],
      ['service-manager.html','settings_applications','Service Manager','Enable, monitor, reload and restart daemons'],
      ['web-runtime.html','developer_mode','Runtime & Web Stack','Web engines, PHP, Node.js, Python, Ruby, modules and stack profiles'],
      ['database-services.html','database','Database Services','MariaDB, PostgreSQL, replication and upgrades'],
      ['ip-management.html','lan','IP Management','Pools, assignments, NAT and reverse DNS'],
      ['certificates.html','verified_user','SSL & AutoSSL','Platform certificates and account coverage'],
      ['transfers.html','move_up','Transfers','Account migration, validation and cutover']
    ];
    host.insertAdjacentHTML('beforeend',`<section class="wcp-root-capabilities"><div class="wcp-root-capabilities-head"><div><div class="wcp-panel-title">Hosting control plane</div><div class="wcp-panel-sub">Core ROOT operations across accounts and infrastructure</div></div><a class="wcp-action-btn" href="system-settings.html">System settings</a></div><div class="wcp-root-capability-grid">${tools.map(([href,icon,title,copy])=>`<a class="wcp-root-capability" href="${href}"><span class="material-symbols-rounded">${icon}</span><div><div class="wcp-root-capability-title">${title}</div><div class="wcp-root-capability-copy">${copy}</div></div><span class="material-symbols-rounded wcp-root-capability-arrow">chevron_right</span></a>`).join('')}</div></section>`);
  }
  function openDrawer(id){
    const d=qs('#'+id), b=qs('#wcpDrawerBackdrop');
    if(d){
      const trigger=document.activeElement;
      document.dispatchEvent(new CustomEvent('wcp:overlay-opening',{detail:{source:'drawer'}}));
      closeDrawers();returnFocus=trigger?.isConnected&&!trigger.closest('[inert]')?trigger:document.activeElement;
      d.inert=false;d.classList.add('open');d.setAttribute('aria-hidden','false');setOpen(b,true);
      setTimeout(()=>qs('button,a,input,select,textarea,[tabindex]:not([tabindex="-1"])',d)?.focus(),10);
    }
  }
  function closeDrawers(){
    const opened=qsa('.wcp-side-drawer.open');
    opened.forEach(x=>{x.classList.remove('open');x.setAttribute('aria-hidden','true');x.inert=true;});
    setOpen(qs('#wcpDrawerBackdrop'),false);if(opened.length&&returnFocus?.isConnected&&!returnFocus.closest('[inert]'))returnFocus.focus();
  }
  function toast(title,sub='Action completed successfully.'){return window.WCPUI.toast({title,message:sub})}

  function enhanceAccessibility(){
    const main=qs('main');if(main&&!main.id)main.id='main-content';
    if(main)main.setAttribute('tabindex','-1');
    if(main&&!qs('.wcp-skip-link')){const skip=document.createElement('a');skip.className='wcp-skip-link';skip.href='#'+main.id;skip.textContent='Aller au contenu principal';document.body.prepend(skip)}

    // Remove superseded prototype overlays when the canonical controls exist.
    if(qs('#wcpCommandBackdrop')){qs('#commandPalette')?.remove();qs('#notifDrawer')?.remove()}

    qsa('button').forEach(button=>{
      if(!button.hasAttribute('type')&&!button.closest('form'))button.type='button';
      const icons=qsa('.material-symbols-rounded',button);
      const text=button.cloneNode(true);qsa('.material-symbols-rounded',text).forEach(icon=>icon.remove());
      if(!text.textContent.trim()&&!button.getAttribute('aria-label')){
        const icon=icons[0]?.textContent.trim();
        const labels={close:'Fermer',search:'Rechercher',more_horiz:'Plus d’options',more_vert:'Plus d’options',notifications:'Notifications',menu:'Navigation',refresh:'Actualiser',download:'Télécharger',upload:'Importer',edit:'Modifier',delete:'Supprimer',visibility:'Afficher',content_copy:'Copier'};
        button.setAttribute('aria-label',button.title||labels[icon]||icon?.replaceAll('_',' ')||'Action');
      }
    });
    qsa('.wcp-nav-item,.nav').forEach(link=>{const label=qs('.wcp-nav-label,span:last-child',link)?.textContent.trim();if(label){link.setAttribute('aria-label',label);link.title=label;}});
    qsa('[data-nav-group-toggle]').forEach(toggle=>{const heading=qs('.wcp-nav-heading,.group-title',toggle)?.textContent.trim();if(heading)toggle.setAttribute('aria-label',heading);});
    qsa('.material-symbols-rounded').forEach(icon=>icon.setAttribute('aria-hidden','true'));
    qsa('input,select,textarea').forEach((field,index)=>{
      if(field.getAttribute('aria-label')||field.getAttribute('aria-labelledby'))return;
      if(field.id&&qs(`label[for="${CSS.escape(field.id)}"]`))return;
      const wrapped=field.closest('label');if(wrapped)return;
      const nearby=field.closest('.field,.wcp-field,.wcp-form-row')?.querySelector('label,.wcp-field-label');
      const option=field.tagName==='SELECT'&&field.options?.[field.selectedIndex]?.text;
      field.setAttribute('aria-label',nearby?.textContent.trim()||field.placeholder||option||`Champ ${index+1}`);
    });
    qsa('.wcp-side-drawer').forEach(drawer=>{drawer.setAttribute('role','dialog');drawer.setAttribute('aria-modal','true')});
    qsa('.wcp-popover').forEach(popover=>{popover.setAttribute('role','dialog');popover.setAttribute('aria-hidden','true');popover.inert=true;});
    qsa('[data-toggle-notifications],[data-toggle-profile],[data-open-command],.wcp-search,[data-wcp-sidebar-toggle],[data-legacy-sidebar-toggle]').forEach(x=>x.setAttribute('aria-expanded','false'));

    const command=qs('#wcpCommandBackdrop'),commandInput=qs('#wcpCommandInput');
    const commandList=command&&qs('.wcp-command-scroll',command);
    if(commandList){commandList.id=commandList.id||'wcpCommandResults';commandList.setAttribute('role','listbox');qsa('.wcp-command-result',commandList).forEach((x,i)=>{x.setAttribute('role','option');x.tabIndex=-1;x.id=x.id||`wcp-command-option-${i}`})}
    if(commandInput){commandInput.setAttribute('role','combobox');commandInput.setAttribute('aria-autocomplete','list');commandInput.setAttribute('aria-controls',commandList?.id||'wcpCommandResults');commandInput.setAttribute('aria-expanded','false')}

    qsa('.wcp-popover-item').forEach(item=>{if(!item.matches('a,button')){item.setAttribute('role','button');item.tabIndex=0}});
    qsa('.wcp-tab[href="#"]').forEach(tab=>{tab.setAttribute('role','tab');tab.setAttribute('aria-selected',String(tab.classList.contains('active')))});
    qsa('.wcp-filter-chip').forEach(chip=>{chip.setAttribute('aria-pressed',String(chip.classList.contains('active')))});

    qsa('.wcp-action-btn,.btn').forEach(button=>{
      if(!button.matches('button')||button.matches('[data-open-drawer],[data-demo-action],[data-open-command],[data-toggle-ops],[data-density],[data-tab],[data-reveal-secret],[data-copy-secret],[data-wcp-sidebar-toggle],[data-legacy-sidebar-toggle]'))return;
      button.dataset.prototypeAction=button.textContent.trim()||button.getAttribute('aria-label')||'Action';
    });
  }
  function loadRuntimeAssets(){
    const page=(location.pathname.split('/').pop()||'').toLowerCase(),scope=document.body.dataset.accessScope;
    if(['login.html','mfa.html'].includes(page))return;
    if(!['user','reseller','root'].includes(scope)&&!['login.html','mfa.html'].includes(page))return;
    if(!qs('link[data-product-runtime],link[href="assets/product-runtime.css"]')){const link=document.createElement('link');link.rel='stylesheet';link.href='assets/product-runtime.css';link.dataset.productRuntime='';document.head.appendChild(link)}
    if(['user','reseller','root'].includes(scope)&&!qs('link[data-experience-runtime]')){const link=document.createElement('link');link.rel='stylesheet';link.href='assets/experience-runtime.css';link.dataset.experienceRuntime='';document.head.appendChild(link)}
    if(['user','reseller','root'].includes(scope)&&!qs('link[data-readable-type]')){const link=document.createElement('link');link.rel='stylesheet';link.href='assets/readable-type.css';link.dataset.readableType='';document.head.appendChild(link)}
    // Load in order: the experience layer consumes the shared runtime.
    const src='assets/product-runtime.js';
    if(!qs(`script[src="${src}"]`)){const script=document.createElement('script');script.src=src;script.dataset.productRuntime='';script.async=false;document.body.appendChild(script)}
    if(['user','reseller','root'].includes(scope)&&!qs('script[src="assets/experience-runtime.js"]')){const script=document.createElement('script');script.src='assets/experience-runtime.js';script.dataset.experienceRuntime='';script.async=false;document.body.appendChild(script)}
  }

  function enhanceCoverageTools(){
    const page=(location.pathname.split('/').pop()||'').toLowerCase(),actions=qs('.wcp-dashboard-header .wcp-header-actions');
    if(page==='hosting-accounts.html'&&!qs('script[data-account-recovery]')){const script=document.createElement('script');script.src='assets/account-recovery.js';script.dataset.accountRecovery='';document.body.appendChild(script)}
    if(page==='web-runtime.html'&&!qs('script[data-runtime-security]')){const script=document.createElement('script');script.src='assets/runtime-security.js';script.dataset.runtimeSecurity='';document.body.appendChild(script)}
    if(page==='databases.html'&&!qs('script[data-database-completion]')){const script=document.createElement('script');script.src='assets/database-completion.js';script.dataset.databaseCompletion='';document.body.appendChild(script)}
    const links={
      'admin-dns.html':[['dns-toolbox.html','travel_explore','DNS Toolbox'],['provider-adapters.html','extension','Provider Adapters']],
      'admin-security.html':[['credential-security.html','password','Credentials']],
      'software-manager.html':[['provider-adapters.html','hub','Provider Adapters'],['virtualization.html','developer_board','Virtualization']],
      'cluster.html':[['continuity-control.html','emergency_share','HA / DR Control']],
      'cluster-failover.html':[['continuity-control.html#drills','emergency_share','HA / DR Control']],
      'system-settings.html':[['privacy-governance.html','privacy_tip','Privacy Governance']],
      'database-services.html':[['databases.html#tools','open_in_new','Preview USER tools']]
    }[page]||[];
    if(actions&&links.length&&!actions.querySelector('[data-coverage-tool]'))links.reverse().forEach(item=>actions.insertAdjacentHTML('afterbegin',`<a class='wcp-action-btn' data-coverage-tool href='${item[0]}'><span class='material-symbols-rounded'>${item[1]}</span>${item[2]}</a>`));
    const grid=qs('.wcp-root-capability-grid');
    if(grid&&!grid.querySelector('[data-capability-tools]'))grid.insertAdjacentHTML('beforeend',`<a class='wcp-root-capability' data-capability-tools href='dns-toolbox.html'><span class='material-symbols-rounded'>travel_explore</span><div><div class='wcp-root-capability-title'>DNS &amp; Network Toolbox</div><div class='wcp-root-capability-copy'>WHOIS, record lookup, propagation, ping and traceroute</div></div><span class='material-symbols-rounded wcp-root-capability-arrow'>chevron_right</span></a><a class='wcp-root-capability' href='credential-security.html'><span class='material-symbols-rounded'>password</span><div><div class='wcp-root-capability-title'>Credential &amp; Account Access</div><div class='wcp-root-capability-copy'>Admin password, root rotation and account identity</div></div><span class='material-symbols-rounded wcp-root-capability-arrow'>chevron_right</span></a><a class='wcp-root-capability' href='continuity-control.html'><span class='material-symbols-rounded'>emergency_share</span><div><div class='wcp-root-capability-title'>Continuity &amp; Disaster Recovery</div><div class='wcp-root-capability-copy'>Floating IP, fencing, replication and recovery drills</div></div><span class='material-symbols-rounded wcp-root-capability-arrow'>chevron_right</span></a><a class='wcp-root-capability' href='privacy-governance.html'><span class='material-symbols-rounded'>privacy_tip</span><div><div class='wcp-root-capability-title'>Privacy &amp; Data Governance</div><div class='wcp-root-capability-copy'>Subject requests, legal holds, residency and evidence</div></div><span class='material-symbols-rounded wcp-root-capability-arrow'>chevron_right</span></a><a class='wcp-root-capability' href='delivery-platform.html'><span class='material-symbols-rounded'>account_tree</span><div><div class='wcp-root-capability-title'>Developer Delivery Platform</div><div class='wcp-root-capability-copy'>Git providers, webhooks, runners and deployment policy</div></div><span class='material-symbols-rounded wcp-root-capability-arrow'>chevron_right</span></a>`);
  }

  document.addEventListener('DOMContentLoaded',()=>{
    normalizeLegacySidebar();
    applyAccessScope();
    enhanceRootDashboard();
    enhanceCoverageTools();
    enhanceAccessibility();
    const command=qs('#wcpCommandBackdrop');if(command){command.setAttribute('aria-hidden','true');command.inert=true;}
    qsa('.wcp-side-drawer').forEach(x=>{x.inert=true;x.setAttribute('aria-hidden','true');x.setAttribute('aria-label',qs('.wcp-drawer-title',x)?.textContent||'Panel')});
    try{if(localStorage.getItem(sideKey)==='true'&&innerWidth>1100)qs('.wcp-dashboard-sidebar')?.classList.add('collapsed')}catch(e){}
    markCurrentNavigation();
    syncSidebarState();
    // density persistence
    let density='default';try{density=localStorage.getItem('wcp360-density')||'default'}catch(e){}
    qsa('[data-density]').forEach(b=>b.classList.toggle('active',b.dataset.density===density));
    qsa('.wcp-table').forEach(t=>{t.classList.toggle('compact',density==='compact');t.classList.toggle('comfortable',density==='comfortable')});
    loadRuntimeAssets();
  });

  document.addEventListener('click',e=>{
    const navGroupToggle=e.target.closest('[data-nav-group-toggle]');if(navGroupToggle){
      const sidebar=navGroupToggle.closest('.wcp-dashboard-sidebar,.sidebar'),group=navGroupToggle.closest('[data-nav-group]');if(!sidebar||!group)return;
      setNavGroup(sidebar,group,!group.classList.contains('open'));return;
    }
    const legacyToggle=e.target.closest('[data-legacy-sidebar-toggle]');if(legacyToggle){
      const sidebar=qs('.sidebar');if(!sidebar)return;
      toggleNavigation(sidebar,true,legacyToggle);
      return;
    }
    if(e.target.closest('.wcp-legacy-sidebar-overlay')){closeMobileNavigation();return}
    const theme=e.target.closest('[data-wcp-theme]');if(theme){e.preventDefault();applyTheme(theme.dataset.wcpTheme);return}
    const toggle=e.target.closest('[data-wcp-sidebar-toggle]');if(toggle){
      const s=qs('.wcp-dashboard-sidebar');if(!s)return;
      toggleNavigation(s,false,toggle);
      return;
    }
    if(e.target.closest('.wcp-sidebar-overlay')){closeMobileNavigation();return}
    if(e.target.closest('[data-open-command],.wcp-search')){openCommand();return}
    if(e.target===qs('#wcpCommandBackdrop')){closeCommand();return}
    const notif=e.target.closest('[data-toggle-notifications]');if(notif){const p=qs('#wcpNotifications');const will=!p?.classList.contains('open');closePopovers(p);setOpen(p,will);p?.setAttribute('aria-hidden',String(!will));notif.setAttribute('aria-expanded',String(will));return}
    const profile=e.target.closest('[data-toggle-profile]');if(profile){const p=qs('#wcpProfileMenu');const will=!p?.classList.contains('open');closePopovers(p);setOpen(p,will);p?.setAttribute('aria-hidden',String(!will));profile.setAttribute('aria-expanded',String(will));return}
    const drawer=e.target.closest('[data-open-drawer]');if(drawer){openDrawer(drawer.dataset.openDrawer);return}
    const commandResult=e.target.closest('#wcpCommandBackdrop .wcp-command-result');if(commandResult){closeCommand();return}
    if(e.target.closest('[data-close-drawer]')||e.target===qs('#wcpDrawerBackdrop')){closeDrawers();return}
    const density=e.target.closest('[data-density]');if(density){
      const d=density.dataset.density;qsa('[data-density]').forEach(b=>b.classList.toggle('active',b===density));
      qsa('.wcp-table').forEach(t=>{t.classList.toggle('compact',d==='compact');t.classList.toggle('comfortable',d==='comfortable')});
      try{localStorage.setItem('wcp360-density',d)}catch(err){};return;
    }
    const tab=e.target.closest('[data-tab]');if(tab){
      const set=tab.closest('[data-tabs]');if(!set)return;
      qsa('[data-tab]',set).forEach(x=>x.classList.toggle('active',x===tab));
      const root=set.parentElement;qsa('.wcp-tab-panel',root).forEach(p=>p.classList.toggle('active',p.dataset.panel===tab.dataset.tab));return;
    }
    const staticTab=e.target.closest('.wcp-tab[href="#"]');if(staticTab){e.preventDefault();qsa('.wcp-tab',staticTab.parentElement).forEach(x=>{const on=x===staticTab;x.classList.toggle('active',on);x.setAttribute('aria-selected',String(on))});toast(staticTab.textContent.trim(),'View selected. Context data refreshed.');return}
    const chip=e.target.closest('.wcp-filter-chip');if(chip){const group=chip.parentElement;qsa('.wcp-filter-chip',group).forEach(x=>{const on=x===chip;x.classList.toggle('active',on);x.setAttribute('aria-pressed',String(on))});return}
    const reveal=e.target.closest('[data-reveal-secret]');if(reveal){
      const field=reveal.closest('.wcp-secret');const val=qs('.wcp-secret-value',field);if(!val)return;
      field.dataset.secret=field.dataset.secret||'wcp_live_8TQ4N7K2M9P3';const on=val.classList.toggle('revealed');val.textContent=on?field.dataset.secret:'wcp_live_••••••••••P3';reveal.textContent=on?'Hide':'Reveal';return;
    }
    const copy=e.target.closest('[data-copy-secret]');if(copy){
      const field=copy.closest('.wcp-secret');const value=field?.dataset.secret||'wcp_live_8TQ4N7K2M9P3';
      if(!navigator.clipboard?.writeText){toast('Copy unavailable','Select and copy the value manually.');return}
      navigator.clipboard.writeText(value).then(()=>toast('Copied to clipboard','Secret copied.')).catch(()=>toast('Copy unavailable','Select and copy the value manually.'));return
    }
    const ops=e.target.closest('[data-toggle-ops]');if(ops){qs('.wcp-ops-bar')?.classList.toggle('open');return}
    const opTab=e.target.closest('[data-ops-tab]');if(opTab){qsa('[data-ops-tab]').forEach(x=>x.classList.toggle('active',x===opTab));return}
    const action=e.target.closest('[data-demo-action]');if(action){document.dispatchEvent(new CustomEvent('wcp:review-action',{detail:{name:action.dataset.demoAction||action.textContent.trim()||'Operation'}}));if(!window.WCPExperience)toast(action.dataset.demoAction||'Prototype action','Interactive demonstration.');return}
    const prototypeAction=e.target.closest('[data-prototype-action]');if(prototypeAction){const custom=[...prototypeAction.attributes].some(attribute=>attribute.name.startsWith('data-')&&!['data-prototype-action'].includes(attribute.name));if(custom||prototypeAction.type==='submit')return;document.dispatchEvent(new CustomEvent('wcp:review-action',{detail:{name:prototypeAction.dataset.prototypeAction,source:prototypeAction}}));if(!window.WCPExperience)toast(prototypeAction.dataset.prototypeAction,'Request validated. Review impact before execution.');return}
    const popoverAction=e.target.closest('.wcp-popover-item[role="button"]');if(popoverAction){toast(popoverAction.textContent.trim().replace(/\s+/g,' '),'Context opened. Permissions and scope preserved.');closePopovers();return}
    if(!e.target.closest('.wcp-popover'))closePopovers();
  });

  document.addEventListener('input',e=>{
    if(e.target.id==='wcpCommandInput')filterCommands(e.target.value);
    if(e.target.matches('[data-table-filter]')){
      const q=e.target.value.trim().toLowerCase();
      const table=e.target.closest('.wcp-panel')?.querySelector('.wcp-table');
      if(table)qsa('tbody tr',table).forEach(tr=>tr.style.display=(!q||tr.textContent.toLowerCase().includes(q))?'':'none');
    }
  });

  window.addEventListener('hashchange',markCurrentNavigation);
  window.addEventListener('resize',syncSidebarState);
  document.addEventListener('wcp:overlay-opening',event=>{
    if(event.detail?.source!=='command')closeCommand();
    if(event.detail?.source!=='drawer')closeDrawers();
    if(event.detail?.source!=='navigation')closeMobileNavigation();
    closePopovers();
  });

  document.addEventListener('keydown',e=>{
    if(qs('dialog[open]'))return;
    if((e.metaKey||e.ctrlKey)&&e.key.toLowerCase()==='k'){e.preventDefault();openCommand();return}
    if(qs('#wcpCommandBackdrop')?.classList.contains('open')){
      if(e.key==='Escape'){closeCommand();return}
      if(e.key==='ArrowDown'){e.preventDefault();moveCommand(1);return}
      if(e.key==='ArrowUp'){e.preventDefault();moveCommand(-1);return}
      if(e.key==='Enter'){
        const active=qs('#wcpCommandBackdrop .wcp-command-result.active');if(active){e.preventDefault();active.click()}return;
      }
      trapFocus(e,qs('#wcpCommandBackdrop'));
    }
    const activeDrawer=qs('.wcp-side-drawer.open');if(activeDrawer)trapFocus(e,activeDrawer);
    const mobileSidebar=qs('.wcp-dashboard-sidebar.mobile-open,.sidebar.mobile-open');if(mobileSidebar)trapFocus(e,mobileSidebar);
    if((e.key==='Enter'||e.key===' ')&&document.activeElement?.getAttribute('role')==='button'){e.preventDefault();document.activeElement.click();return}
    if(e.key==='Escape'){
      const popover=qs('.wcp-popover.open'),trigger=popover&&qs(popover.id==='wcpProfileMenu'?'[data-toggle-profile]':'[data-toggle-notifications]');
      closeDrawers();closePopovers();closeMobileNavigation();if(trigger?.isConnected)trigger.focus();
    }
  });
})();
