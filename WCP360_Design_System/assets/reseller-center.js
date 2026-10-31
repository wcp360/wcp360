(function () {
  const page = (location.pathname.split('/').pop() || '').toLowerCase();
  if (page === 'reseller-governance.html') {
    location.replace('reseller.html#governance');
    return;
  }
  if (page !== 'reseller.html') return;

  const qs = (selector, context = document) => context.querySelector(selector);
  const qsa = (selector, context = document) => [...context.querySelectorAll(selector)];
  const tabs = [
    ['overview', 'space_dashboard', 'Overview'],
    ['customers', 'business', 'Customers'],
    ['packages', 'inventory_2', 'Packages'],
    ['capacity', 'data_usage', 'Capacity'],
    ['services', 'dns', 'Services'],
    ['governance', 'policy', 'Governance'],
    ['brand', 'palette', 'Brand'],
    ['transfers', 'move_up', 'Transfers'],
    ['notifications', 'notifications', 'Notifications'],
    ['audit', 'manage_search', 'Audit & support']
  ];
  const UI=window.WCPUI,S=window.WCPWorkspaceSchemas.reseller,esc=UI.escape;
  const key = 'wcp360-reseller-center-v1';
  const defaults = {
    customers: [
      { id: 'acme', name: 'Acme Studio', domain: 'acme.example.com', owner: 'owner@acme.example', package: 'Agency Pro', storage: 62, transfer: 48, status: 'Active' },
      { id: 'nova', name: 'Nova Retail', domain: 'nova.example.com', owner: 'ops@nova.example', package: 'Business', storage: 74, transfer: 81, status: 'Active' },
      { id: 'demo', name: 'Demo Partner', domain: 'demo.example.com', owner: 'demo@example.com', package: 'Trial', storage: 12, transfer: 9, status: 'Trial' },
      { id: 'legacy', name: 'Legacy Customer', domain: 'legacy.example.com', owner: 'admin@legacy.example', package: 'Starter', storage: 91, transfer: 67, status: 'Suspended' }
    ],
    services: { websites: true, email: true, databases: true, dns: true, ssl: true, backups: true, terminal: false, dedicatedIp: true },
    brand: { name: 'Example Partner', panel: 'panel.partner.example.com', support: 'support@partner.example.com', nameservers: 'ns1.partner.example.com · ns2.partner.example.com' }
  };
  const read = () => { try { return JSON.parse(localStorage.getItem(key)) || {}; } catch (error) { return {}; } };
  const stored = read();
  const state = { ...defaults, ...stored, customers: Array.isArray(stored.customers) ? stored.customers : defaults.customers, services: { ...defaults.services, ...(stored.services || {}) }, brand: { ...defaults.brand, ...(stored.brand || {}) } };
  const save = () => { try { localStorage.setItem(key, JSON.stringify(state)); } catch { UI.notify({title:'Reseller Center',message:'Browser storage is unavailable. Changes last only for this page.',tone:'error'}); } };
  for(const name of ['packages','audit','operators','transfers','notices','requests'])if(!Array.isArray(state[name]))state[name]=name==='packages'?structuredClone(S.packageDefaults):[];
  let active = tabs.some(item => item[0] === location.hash.slice(1)) ? location.hash.slice(1) : 'overview';


  const chip = (label, tone = '') => `<span class="wcp-status ${tone}">${label}</span>`;
  const action = (kind, label, primary = false, icon = 'add') => `<button type="button" class="wcp-action-btn ${primary ? 'primary' : ''}" data-res-action="${kind}"><span class="material-symbols-rounded">${icon}</span>${label}</button>`;
  const panelHead = (title, copy, controls = '') => `<div class="wcp-panel-head res-panel-head"><div><div class="wcp-panel-title">${title}</div><div class="wcp-panel-sub">${copy}</div></div><div class="res-panel-actions">${controls}</div></div>`;
  const note = (icon, title, copy) => `<div class="res-note"><span class="material-symbols-rounded">${icon}</span><div><strong>${title}</strong><p>${copy}</p></div></div>`;
  const capacity = (label, value, percent, copy, warning = false) => `<article class="res-capacity"><span>${label}</span><strong>${value}</strong><div class="res-progress ${warning ? 'warning' : ''}"><i style="width:${Math.min(percent, 100)}%"></i></div><small>${copy}</small></article>`;
  const service = (id, icon, title, copy, rootPolicy) => `<div class="res-service"><span class="material-symbols-rounded">${icon}</span><div><strong>${title}</strong><small>${copy} · ROOT policy: ${rootPolicy}</small></div><label class="res-toggle" aria-label="Enable ${title} for reseller packages"><input type="checkbox" data-res-service="${id}" ${state.services[id] ? 'checked' : ''}><i></i></label></div>`;

  const views = {
    overview: () => `<div class="res-section" data-res-view="overview">
      <div class="res-section-head"><div><h3>Delegated hosting workspace</h3><p>Operate customer accounts without access to servers, global services or ROOT policy. Every action is intersected with the envelope delegated by ROOT.</p></div><div>${action('invite-operator', 'Invite operator')}${action('create-customer', 'Create customer', true, 'person_add')}</div></div>
      <div class="res-grid">
        <article class="res-card"><div class="res-card-head"><span class="material-symbols-rounded">business</span>${chip(state.customers.length+' / 25', 'success')}</div><h4>Customer accounts</h4><p>Create, modify, suspend and recover only accounts owned by this reseller.</p><div class="res-kv"><div><span>Active</span><strong>${state.customers.filter(item=>item.status==='Active').length}</strong></div><div><span>Suspended</span><strong>${state.customers.filter(item=>item.status==='Suspended').length}</strong></div><div><span>Pending jobs</span><strong>${state.customers.filter(item=>item.status==='Provisioning').length}</strong></div></div><footer><button class="wcp-action-btn" data-res-tab="customers">Open customers</button></footer></article>
        <article class="res-card"><div class="res-card-head"><span class="material-symbols-rounded">data_usage</span>${chip('76%', 'warning')}</div><h4>Delegated capacity</h4><p>Allocated and consumed resources remain distinct. Hard ROOT ceilings cannot be exceeded.</p><div class="res-kv"><div><span>Storage</span><strong>1.2 / 1.6 TiB</strong></div><div><span>Transfer</span><strong>8.4 / 14 TiB</strong></div><div><span>Dedicated IPs</span><strong>6 / 8</strong></div></div><footer><button class="wcp-action-btn" data-res-tab="capacity">Inspect capacity</button></footer></article>
        <article class="res-card"><div class="res-card-head"><span class="material-symbols-rounded">policy</span>${chip('Compliant', 'success')}</div><h4>Governance posture</h4><p>Operator ACLs, customer consent and immutable delegated audit evidence.</p><div class="res-kv"><div><span>Operators</span><strong>6</strong></div><div><span>ACL profiles</span><strong>4</strong></div><div><span>Denied actions · 7d</span><strong>14</strong></div></div><footer><button class="wcp-action-btn" data-res-tab="governance">Review access</button></footer></article>
      </div>
      <section class="wcp-panel">${panelHead('Recent delegated activity', 'Customer-visible operations and policy decisions')}<div class="res-event-list"><div class="res-event"><span class="material-symbols-rounded">person_add</span><div><strong>Customer account provisioned</strong><small>nova.example.com · Business · operator@example.com</small></div><small>12 min</small></div><div class="res-event"><span class="material-symbols-rounded">shield_lock</span><div><strong>Dedicated IP request denied</strong><small>Pool ceiling reached · ROOT policy RES-IP-04</small></div><small>31 min</small></div><div class="res-event"><span class="material-symbols-rounded">support_agent</span><div><strong>Support session completed</strong><small>Acme Studio · customer consent · 18 minutes</small></div><small>2 h</small></div></div></section>
    </div>`,

    capacity: () => `<div class="res-section" data-res-view="capacity">
      <div class="res-section-head"><div><h3>Capacity, quotas and overselling</h3><p>Compare ROOT delegation, reseller allocation and observed customer consumption before provisioning.</p></div>${action('request-capacity', 'Request more capacity', true, 'add_chart')}</div>
      <div class="res-capacity-grid">${capacity('Storage', '1.2 / 1.6 TiB', 76, 'Used 890 GiB · storage overselling 120%', true)}${capacity('Guaranteed RAM', '92 / 128 GiB', 72, 'Cannot be oversold')}${capacity('CPU', '44 / 64 vCPU', 69, 'Burst allowed · guarantee enforced')}${capacity('Monthly transfer', '8.4 / 14 TiB', 60, 'Warning at 80%')}${capacity('Dedicated IPs', '6 / 8', 75, 'Assignment only · no overselling')}${capacity('Databases', '94 / 160', 59, 'Across all customer packages')}${capacity('Mailboxes', '284 / 500', 57, 'Outbound rate policy inherited')}${capacity('Customer accounts', state.customers.length+' / 25', state.customers.length*4, 'ROOT hard account ceiling')}</div>
      <section class="wcp-panel">${panelHead('Resource policy', 'Overselling is explicit per resource, never global')}<div class="res-policy-list"><div class="res-policy"><span class="material-symbols-rounded">hard_drive</span><div><strong>Storage overselling · 120%</strong><small>Allowed while physical and forecast capacity remain healthy.</small></div>${chip('Allowed', 'success')}</div><div class="res-policy"><span class="material-symbols-rounded">memory</span><div><strong>RAM and CPU guarantees</strong><small>Reservations must fit the ROOT envelope before provisioning.</small></div>${chip('Hard limit', 'warning')}</div><div class="res-policy"><span class="material-symbols-rounded">lan</span><div><strong>Dedicated IP addresses</strong><small>Only explicitly delegated addresses can be assigned.</small></div>${chip('8 addresses')}</div></div></section>
    </div>`,

    services: () => `<div class="res-section" data-res-view="services">
      <div class="res-section-head"><div><h3>Delegated customer services</h3><p>Choose which ROOT-authorized services may appear in reseller packages. This never enables or configures server daemons.</p></div>${action('save-services', 'Publish service catalog', true, 'publish')}</div>
      <section class="wcp-panel">${panelHead('Package service catalog', 'Availability is ROOT allowance ∩ reseller choice ∩ customer package')}<div class="res-service-list">${service('websites','web','Websites & domains','Sites, redirects, runtimes and DNS records','Allowed')}${service('email','mail','Email & external webmail','Mailboxes, routing and Roundcube/SnappyMail/SoGo access','Allowed')}${service('databases','database','Databases','MariaDB, PostgreSQL and approved external SSO tools','Allowed')}${service('dns','language','DNS management','Customer-zone records only; no cluster access','Allowed')}${service('ssl','verified_user','SSL & AutoSSL','Certificates for owned customer domains','Allowed')}${service('backups','backup','Backups & restore','Customer restore points inside delegated retention','Allowed')}${service('terminal','terminal','SSH terminal','Account chroot only; package and operator restrictions','Conditional')}${service('dedicatedIp','lan','Dedicated IP assignment','Only from the delegated reseller pool','Allowed')}</div></section>
      ${note('dns', 'Infrastructure remains ROOT-owned', 'Service switches control customer visibility and package eligibility. They cannot start daemons, alter global mail/DNS/database policy or select physical servers.')}
    </div>`,

    governance: () => `<div class="res-section" data-res-view="governance">
      <div class="res-section-head"><div><h3>Operators, ACL profiles and effective access</h3><p>Separate owner, provisioning, support and billing duties without granting capabilities outside the ROOT envelope.</p></div><div>${action('compare-access', 'Compare access')}${action('invite-operator', 'Invite operator', true, 'person_add')}</div></div>
      <section class="wcp-panel">${panelHead('ACL profiles', 'Effective privilege = ROOT delegation ∩ reseller ACL ∩ target ownership')}<div class="res-table-wrap"><table class="res-table"><thead><tr><th>Profile</th><th>Operators</th><th>Account actions</th><th>Packages</th><th>Support access</th><th>Status</th><th></th></tr></thead><tbody><tr><td><strong>Reseller Owner</strong><small>Commercial and technical owner</small></td><td>2</td><td>Full owned lifecycle</td><td>Create within ceiling</td><td>Consent required</td><td>${chip('Active','success')}</td><td>${action('edit-acl','Inspect')}</td></tr><tr><td><strong>Provisioning</strong><small>No billing or branding</small></td><td>1</td><td>Create · modify · suspend</td><td>Use approved</td><td>None</td><td>${chip('Active','success')}</td><td>${action('edit-acl','Inspect')}</td></tr><tr><td><strong>Support</strong><small>Customer-visible activity</small></td><td>2</td><td>Read only</td><td>Read</td><td>Time-bound</td><td>${chip('Conditional','warning')}</td><td>${action('edit-acl','Inspect')}</td></tr><tr><td><strong>Billing</strong><small>No service configuration</small></td><td>1</td><td>Read · billing hold</td><td>Read</td><td>None</td><td>${chip('Review','warning')}</td><td>${action('edit-acl','Inspect')}</td></tr></tbody></table></div></section>
      ${note('shield_lock', 'No privilege amplification', 'A reseller operator cannot grant an ACL, package feature, resource ceiling or customer action that ROOT did not delegate to the reseller.')}
    </div>`,

    brand: () => `<div class="res-section" data-res-view="brand">
      <div class="res-section-head"><div><h3>Brand, nameservers and customer communication</h3><p>Customize the customer-facing identity inside ROOT-approved domains, themes and localization policy.</p></div>${action('edit-brand', 'Edit brand', true, 'edit')}</div>
      <div class="res-grid"><article class="res-card"><div class="res-card-head"><span class="material-symbols-rounded">palette</span>${chip('Active','success')}</div><h4>${esc(state.brand.name)}</h4><p>Logo, favicon, light/dark theme and customer panel identity.</p><div class="res-kv"><div><span>Panel domain</span><strong>${esc(state.brand.panel)}</strong></div><div><span>Support</span><strong>${esc(state.brand.support)}</strong></div></div></article><article class="res-card"><div class="res-card-head"><span class="material-symbols-rounded">dns</span>${chip('Verified','success')}</div><h4>Branded nameservers</h4><p>Delegated vanity nameservers mapped to ROOT-operated authoritative infrastructure.</p><div class="res-kv"><div><span>Nameservers</span><strong>${esc(state.brand.nameservers)}</strong></div><div><span>Glue / rDNS</span><strong>ROOT managed</strong></div></div></article><article class="res-card"><div class="res-card-head"><span class="material-symbols-rounded">translate</span>${chip('4 locales')}</div><h4>Customer localization</h4><p>English, French, German and Spanish with reseller-specific notices.</p><div class="res-kv"><div><span>Default</span><strong>English</strong></div><div><span>Fallback</span><strong>ROOT dictionary</strong></div></div></article></div>
      ${note('verified_user', 'ROOT keeps trust ownership', 'ROOT validates panel domains, TLS, DNS delegation and theme safety. The reseller controls content only within that approved boundary.')}
    </div>`,

  };

  // Customer, package and operations views share one grid and one form lifecycle.
  let grid=null,dialog=null,sequence=0;
  const uid=()=>window.crypto?.randomUUID?.()||'reseller-'+Date.now()+'-'+(++sequence);
  const notify=message=>UI.notify({title:'Reseller Center',message});
  const safe=run=>async(...args)=>{try{return await run(...args);}catch(error){UI.notify({title:'Reseller operation blocked',message:error.message,tone:'error'});}};
  const job=(name,kind)=>window.WCPRuntime?.addJob({name,kind,scope:'reseller',actor:'Demo reseller'})?.id||null;
  function evidence(operation,target){state.audit.unshift({id:uid(),operation,target,actor:'Demo reseller',date:new Date().toISOString()});save();}
  function openForm(title,description,fields,onSubmit,submitLabel='Save'){
    const form=document.createElement('form');dialog=UI.Dialog.open({title,description,content:form,onClose:()=>UI.Form.detach(form)});const current=dialog;
    UI.Form.populate(form,{fields,submitLabel,onCancel:()=>current.close(),onSubmit:async values=>{await onSubmit(values);current.close();save();renderView();updateMetrics();}});return form;
  }
  function customerForm(record){
    const fields=S.customer({customers:state.customers,packages:state.packages,record});
    openForm(record?'Manage customer':'Create customer','Only accounts owned by this reseller appear here. Placement is automatic.',fields,values=>{
      window.WCPWorkspaceSchemas.assertValid(S.customer({customers:state.customers,packages:state.packages,record}),values);
      if(record){const current=state.customers.find(item=>item.id===record.id);if(!current)throw new Error('This customer no longer exists.');Object.assign(current,values,{domain:values.domain.toLowerCase()});evidence('Customer updated',values.domain);}
      else {const id=uid(),jobId=job('Provision '+values.domain,'reseller-provision');state.customers.unshift({...values,domain:values.domain.toLowerCase(),id,storage:0,transfer:0,status:jobId?'Provisioning':'Active',provisionJob:jobId});evidence('Customer created',values.domain);active='customers';location.hash='#customers';}
    },record?'Save customer':'Create demo customer');
  }
  function packageForm(record){
    openForm(record?'Edit package':'Create package','Limits must remain within the selected ROOT source plan.',S.package({packages:state.packages,customers:state.customers,record}),values=>{
      window.WCPWorkspaceSchemas.assertValid(S.package({packages:state.packages,customers:state.customers,record}),values);
      const next={...values,storage:Number(values.storage),sites:Number(values.sites),cpu:Number(values.cpu),mailboxes:Number(values.mailboxes)};
      if(record){const current=state.packages.find(item=>item.id===record.id);if(!current)throw new Error('This package no longer exists.');state.customers.filter(item=>item.package===record.name).forEach(item=>item.package=next.name);Object.assign(current,next);}else state.packages.push({...next,id:uid()});
      evidence(record?'Package updated':'Package created',next.name);active='packages';location.hash='#packages';
    });
  }
  async function lifecycle(rows,status){
    const ids=rows.map(item=>item.id),eligible=item=>status==='Suspended'?['Active','Trial'].includes(item.status):item.status==='Suspended';
    if(!rows.length||rows.some(item=>!state.customers.includes(item)||!eligible(item)))throw new Error('Every selected customer must support this status transition.');
    if(!await UI.Dialog.confirm({title:status==='Suspended'?'Suspend customers':'Resume customers',message:`Apply ${status} to ${rows.length} owned customers?`,confirmLabel:status==='Suspended'?'Suspend':'Resume'}))return;
    const current=state.customers.filter(item=>ids.includes(item.id));if(current.length!==ids.length||current.some(item=>!eligible(item)))throw new Error('A customer changed while confirmation was open.');
    current.forEach(item=>{if(status==='Suspended')item.previousStatus=item.status;item.status=status==='Active'&&item.previousStatus==='Trial'?'Trial':status;delete item.provisionJob;});evidence('Customer status: '+status,current.map(item=>item.domain).join(', '));renderView();updateMetrics();notify('Customer statuses updated.');
  }
  function syncJobs(){
    const jobs=window.WCPRuntime?.getState().jobs||[];let changed=false;
    for(const customer of state.customers){const current=jobs.find(job=>job.id===customer.provisionJob);if(!current)continue;const status=current.status==='completed'?'Active':['failed','cancelled'].includes(current.status)?'Provisioning failed':'Provisioning';if(customer.status!==status){customer.status=status;changed=true;}}
    for(const transfer of state.transfers){const current=jobs.find(job=>job.id===transfer.jobId);if(current&&(transfer.progress!==current.progress||transfer.status!==current.status)){transfer.progress=current.progress;transfer.status=current.status;changed=true;}}
    if(changed)save();return changed;
  }
  views.customers=()=>`<div class="res-section"><div class="res-section-head"><div><h3>Customer accounts</h3><p>Owned accounts, packages and explicit lifecycle actions.</p></div>${action('create-customer','Create customer',true,'person_add')}</div><div data-res-grid></div>${note('visibility','Separate customer portal','Customer operations remain in WCP-USER. This center manages the delegated account envelope.')}</div>`;
  views.packages=()=>`<div class="res-section"><div class="res-section-head"><div><h3>Packages</h3><p>Reusable offers derived from ROOT plans. Draft offers cannot be assigned to new customers.</p></div>${action('create-package','Create package',true,'add_box')}</div><div data-res-grid></div></div>`;
  views.transfers=()=>`<div class="res-section"><div class="res-section-head"><div><h3>Customer transfer preflights</h3><p>Local simulation; no credentials or remote data are collected.</p></div>${action('new-transfer','New preflight',true,'move_up')}</div><div data-res-grid></div></div>`;
  views.audit=()=>`<div class="res-section"><div class="res-section-head"><div><h3>Delegated activity</h3><p>Browser-local prototype history. Export and filtering use the shared grid.</p></div>${action('open-support','Create support request',true,'support_agent')}</div><div data-res-grid></div></div>`;
  views.notifications=()=>`<div class="res-section"><div class="res-section-head"><div><h3>Customer notices</h3><p>Saved locally for review; no message is sent to customers.</p></div>${action('create-notice','Create notice',true,'campaign')}</div><div data-res-grid></div></div>`;
  function mountGrid(){
    const target=qs('[data-res-grid]');if(!target)return;
    const config={
      customers:{title:'Owned customers',rows:state.customers,columns:[{key:'name',label:'Customer'},{key:'domain',label:'Domain'},{key:'owner',label:'Owner'},{key:'package',label:'Package'},{key:'storage',label:'Storage (%)',type:'number'},{key:'transfer',label:'Transfer (%)',type:'number'},{key:'status',label:'Status'},{key:'region',label:'Region',hidden:true}],rowActions:[{label:'Manage',run:safe(customerForm)},{label:'Suspend',when:item=>['Active','Trial'].includes(item.status),run:safe(item=>lifecycle([item],'Suspended'))},{label:'Resume',when:item=>item.status==='Suspended',run:safe(item=>lifecycle([item],'Active'))}],bulkActions:[{label:'Suspend',run:safe(rows=>lifecycle(rows,'Suspended'))},{label:'Resume',run:safe(rows=>lifecycle(rows,'Active'))}]},
      packages:{title:'Delegated packages',rows:state.packages,columns:[{key:'name',label:'Package'},{key:'source',label:'ROOT source'},{key:'storage',label:'Storage (GiB)',type:'number'},{key:'sites',label:'Sites',type:'number'},{key:'cpu',label:'vCPU',type:'number'},{key:'mailboxes',label:'Mailboxes',type:'number'},{key:'status',label:'Publication'}],rowActions:[{label:'Edit',run:safe(packageForm)}]},
      transfers:{title:'Transfer jobs',rows:state.transfers,columns:[{key:'customer',label:'Customer'},{key:'source',label:'Source'},{key:'package',label:'Target package'},{key:'progress',label:'Progress (%)',type:'number'},{key:'status',label:'Status'}]},
      audit:{title:'Local activity',rows:state.audit,columns:[{key:'operation',label:'Operation'},{key:'target',label:'Target'},{key:'actor',label:'Actor'},{key:'date',label:'Date',type:'date'}]},
      notifications:{title:'Draft notices',rows:state.notices,columns:[{key:'title',label:'Title'},{key:'audience',label:'Audience'},{key:'message',label:'Message'},{key:'status',label:'Status'}],rowActions:[{label:'Delete draft',run:safe(async item=>{if(await UI.Dialog.confirm({title:'Delete local draft',message:'Delete '+item.title+'?'})){state.notices=state.notices.filter(row=>row.id!==item.id);evidence('Notice draft deleted',item.title);renderView();}})}]}
    }[active];
    grid=UI.DataGrid.mount(target,{id:'reseller-'+active,store:UI.createStore({scope:'reseller',owner:'example-partner',module:active}),...config});
  }
  function updateMetrics(){const metrics=qsa('[data-res-metric]');const values={customers:state.customers.length+' / 25',packages:String(state.packages.length),published:state.packages.filter(item=>item.status==='Published').length+' published'};metrics.forEach(element=>element.textContent=values[element.dataset.resMetric]||'');}
  function renderShell(){
    document.title='Reseller Center — WCP360 RESELLER';const title=qs('.wcp-page-title');if(title)title.textContent='Reseller Center';const host=qs('#parityCenter');if(!host)return;
    host.innerHTML=`<div class="res-page"><div class="wcp-page-head"><div><div class="wcp-page-eyebrow">WCP-RESELLER · DELEGATED HOSTING</div><h2 class="wcp-page-heading">Reseller Center</h2><p class="wcp-page-sub">Customer accounts, offers and operations inside the ROOT-delegated envelope.</p></div><div class="wcp-page-actions">${action('open-support','ROOT support',false,'support_agent')}${action('create-customer','Create customer',true,'person_add')}</div></div><section class="res-metrics"><article><span>Customer accounts</span><strong data-res-metric="customers"></strong><small>ROOT hard ceiling</small></article><article><span>Capacity model</span><strong>Demo envelope</strong><small>ROOT plan limits enforced per offer</small></article><article><span>Packages</span><strong data-res-metric="packages"></strong><small data-res-metric="published"></small></article><article><span>Workspace</span><strong>Local prototype</strong><small>No customer messages sent</small></article></section><div class="wcp-tabs res-tabs" role="tablist" aria-label="Reseller Center sections">${tabs.map(([id,icon,label])=>`<button type="button" id="res-tab-${id}" class="wcp-tab" role="tab" data-res-tab="${id}" aria-controls="resView" aria-selected="false"><span class="material-symbols-rounded">${icon}</span>${label}</button>`).join('')}</div><div id="resView" class="res-view" role="tabpanel" tabindex="0"></div></div>`;
    renderView();updateMetrics();
  }
  function renderView(){
    const view=qs('#resView');if(!view)return;grid?.destroy();grid=null;syncJobs();view.innerHTML=views[active]();view.setAttribute('aria-labelledby','res-tab-'+active);
    qsa('.res-tabs [data-res-tab]').forEach(control=>{const selected=control.dataset.resTab===active;control.classList.toggle('active',selected);control.setAttribute('aria-selected',String(selected));control.tabIndex=selected?0:-1;});mountGrid();
  }
  function setTab(id,updateHash=true){if(!views[id])return;active=id;if(updateHash&&location.hash.slice(1)!==id)location.hash=id;renderView();qs('#resView')?.focus({preventScroll:true});}
  function runAction(kind){
    if(kind==='create-customer'){customerForm();return;}
    if(kind==='create-package'){packageForm();return;}
    if(kind==='save-services'){save();evidence('Service catalog saved','Owned packages');notify('Local service choices saved.');return;}
    if(kind==='edit-brand'){
      openForm('Edit reseller brand','Local identity preview. DNS and TLS verification remain simulated.',[{name:'name',label:'Brand name',required:true,maxLength:100,default:state.brand.name},{name:'panel',label:'Panel domain',required:true,rule:'domain',default:state.brand.panel},{name:'support',label:'Support email',type:'email',required:true,default:state.brand.support},{name:'nameservers',label:'Nameservers',required:true,default:state.brand.nameservers,validate:value=>String(value).split(/[\s,·]+/).filter(Boolean).every(UI.rules.domain)?'':'Enter valid nameserver domains separated by spaces.'}],values=>{state.brand=values;evidence('Brand updated',values.name);});return;
    }
    if(kind==='invite-operator'){
      openForm('Prepare operator invitation','Saves a local invitation draft. No email is sent.',[{name:'email',label:'Email',type:'email',required:true,validate:value=>state.operators.some(item=>UI.normalize(item.email)===UI.normalize(value))?'This operator is already listed.':''},{name:'profile',label:'Bounded profile',type:'select',required:true,options:['Support','Provisioning','Billing']}],values=>{state.operators.push({...values,id:uid(),status:'Draft invitation'});evidence('Operator invitation drafted',values.email);},'Save invitation draft');return;
    }
    if(kind==='new-transfer'){
      if(!state.customers.length)throw new Error('Create a customer before requesting a transfer.');
      openForm('Transfer preflight','Creates a local simulated job for an owned customer.',[{name:'customerId',label:'Customer',type:'select',required:true,options:state.customers.map(item=>({value:item.id,label:item.name}))},{name:'source',label:'Source',type:'select',required:true,options:['cPanel remote','Plesk remote','DirectAdmin remote','Direct SFTP / SQL']},{name:'package',label:'Published target package',type:'select',required:true,options:state.packages.filter(item=>item.status==='Published').map(item=>item.name)}],values=>{const customer=state.customers.find(item=>item.id===values.customerId);if(!customer||!state.packages.some(item=>item.status==='Published'&&item.name===values.package))throw new Error('The customer or target package is unavailable.');const jobId=job('Transfer preflight '+customer.domain,'reseller-transfer');state.transfers.unshift({...values,id:uid(),customer:customer.name,jobId,progress:jobId?4:100,status:jobId?'running':'completed'});evidence('Transfer preflight requested',customer.domain);});return;
    }
    if(kind==='open-support'||kind==='request-capacity'){
      openForm(kind==='open-support'?'ROOT support request':'Capacity request','Saved as a local request with a simulated review job.',[{name:'category',label:'Category',type:'select',required:true,options:['Provisioning','Capacity','Mail delivery','Migration','Security']},{name:'description',label:'Description',type:'textarea',required:true,minLength:10,maxLength:2000}],values=>{state.requests.push({...values,id:uid(),jobId:job('Review reseller '+values.category,'reseller-request'),status:'Local review'});evidence('Request drafted',values.category);});return;
    }
    if(kind==='create-notice'){
      openForm('Create customer notice draft','Local review only. No message is published or sent.',[{name:'title',label:'Title',required:true,maxLength:120},{name:'audience',label:'Audience',type:'select',required:true,options:['All owned customers',...state.packages.map(item=>item.name)]},{name:'message',label:'Message',type:'textarea',required:true,minLength:5,maxLength:2000}],values=>{state.notices.unshift({...values,id:uid(),status:'Draft'});evidence('Notice drafted',values.title);});return;
    }
    if(['compare-access','edit-acl'].includes(kind)){
      const content=document.createElement('div');content.innerHTML='<p>ROOT delegation ∩ reseller profile ∩ customer ownership.</p><p>Support: read and consent-based sessions. Provisioning: create, update and suspend owned accounts. Billing: read and billing holds.</p><p>Profile ceilings are read-only in this prototype.</p><h4>Local operator invitations</h4>'+state.operators.map(item=>'<p>'+esc(item.email)+' · '+esc(item.profile)+' · '+esc(item.status)+'</p>').join('');UI.Dialog.open({title:'Effective delegated access',content});return;
    }
    UI.Dialog.open({title:'Prototype detail',description:'This view is informational. No operation has been applied.'});
  }
  document.addEventListener('click',event=>{const tab=event.target.closest('[data-res-tab]');if(tab){event.preventDefault();setTab(tab.dataset.resTab);return;}const control=event.target.closest('[data-res-action]');if(control){event.preventDefault();safe(()=>runAction(control.dataset.resAction))();}});
  document.addEventListener('change',event=>{const control=event.target.closest('[data-res-service]');if(control&&Object.hasOwn(state.services,control.dataset.resService)){state.services[control.dataset.resService]=control.checked;save();}});
  document.addEventListener('keydown',event=>{const tab=event.target.closest('.res-tabs [data-res-tab]');if(!tab||!['ArrowLeft','ArrowRight','Home','End'].includes(event.key))return;event.preventDefault();const index=tabs.findIndex(item=>item[0]===tab.dataset.resTab),next=event.key==='Home'?0:event.key==='End'?tabs.length-1:(index+(event.key==='ArrowRight'?1:-1)+tabs.length)%tabs.length;setTab(tabs[next][0]);qs('#res-tab-'+tabs[next][0])?.focus();});
  window.addEventListener('hashchange',()=>{const target=location.hash.slice(1);if(views[target]&&target!==active)setTab(target,false);});
  document.addEventListener('wcp:runtime-change',()=>{if(syncJobs()&&grid&&['customers','transfers'].includes(active))grid.setRows(state[active]);});
  if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',renderShell);else renderShell();
})();
