(()=>{
  'use strict';
  const host=document.querySelector('#accountCertificates');if(!host)return;
  const UI=window.WCPUI,P=window.WCPPrototypeOperations,S=window.WCPDNSTLSSchemas,key='wcp360-account-certificates-v1',owner=S.zone,day=86400000;
  const defaults={certificates:[{id:'web',label:'Website certificate',owner,sans:[owner,'www.'+owner],authority:'Demo ACME',algorithm:'RSA 2048',challenge:'HTTP-01',status:'Valid',chainValid:true,autoRenew:true,generation:1,expiresAt:new Date(Date.now()+67*day).toISOString()},{id:'mail',label:'Mail hostname certificate',owner,sans:['mail.'+owner],authority:'Demo ACME',algorithm:'ECDSA P-256',challenge:'DNS-01',status:'Valid',chainValid:true,autoRenew:true,generation:1,expiresAt:new Date(Date.now()+14*day).toISOString()}],bindings:{[owner]:'web',['www.'+owner]:'web'},operations:[]};
  const state=P.load(key,defaults,['certificates','operations']);state.bindings=Object.fromEntries(Object.entries(state.bindings||{}).map(([hostname,binding])=>[hostname,typeof binding==='string'?{certificateId:binding,generation:1}:binding]));
  const tabs={certificates:'Certificates',deployments:'Website bindings',activity:'Activity'};let active=Object.hasOwn(tabs,location.hash.slice(1))?location.hash.slice(1):'certificates',grid=null;
  const save=()=>P.save(key,state),esc=UI.escape;
  const certificate=id=>state.certificates.find(item=>item.id===id&&item.owner===owner);
  const dnsAllowed=()=>{let policy;try{policy=JSON.parse(localStorage.getItem('wcp360-dns-center-v1')||'null');}catch{}return S.dnsPolicy(policy).types.includes('TXT')&&!S.dnsPolicy(policy).error;};
  const busy=id=>state.operations.some(item=>item.certificateId===id&&P.pending(item));
  const button=(name,label,primary=false)=>`<button type="button" class="wcp-action-btn ${primary?'primary':''}" data-account-cert-action="${name}">${label}</button>`;
  function sync(){
    const jobs=P.jobs();let changed=false;
    for(const operation of state.operations){const job=jobs.find(item=>item.id===operation.jobId&&item.scope==='user');if(!job)continue;
      if(operation.status!==job.status||operation.progress!==job.progress){operation.status=job.status;operation.progress=job.progress;changed=true;}
      if(job.status!=='completed'||operation.applied)continue;
      const current=certificate(operation.certificateId);
      if(operation.kind==='issue'){
        const values=operation.payload,id='CRT-'+operation.id;
        if(values.challenge==='DNS-01'&&!dnsAllowed())operation.result='Blocked by current DNS policy';
        else{if(!certificate(id))state.certificates.unshift({id,owner,...values,authority:'Demo ACME',status:'Valid',chainValid:true,generation:1,expiresAt:new Date((job.completedAt||Date.now())+30*day).toISOString()});operation.result='Demo certificate issued';}
      }else if(!current||current.status==='Revoked'||current.generation!==operation.generation)operation.result='Blocked: certificate changed or is unavailable';
      else if(operation.kind==='renew'){
        if(current.challenge==='DNS-01'&&!dnsAllowed())operation.result='Blocked by current DNS policy';
        else{current.expiresAt=new Date(Math.max(Date.now(),Date.parse(current.expiresAt)||0)+30*day).toISOString();current.generation++;current.chainValid=true;operation.result='Demo validity extended by 30 days';}
      }else if(operation.kind==='deploy'){
        if(!S.usable(current)||!S.covers(current,operation.payload.hostname))operation.result='Blocked: certificate is not deployable';
        else{state.bindings[operation.payload.hostname]={certificateId:current.id,generation:current.generation};operation.result='Local website binding updated';}
      }else if(operation.kind==='revoke'){
        current.status='Revoked';current.autoRenew=false;for(const hostname of Object.keys(state.bindings))if(state.bindings[hostname].certificateId===current.id)delete state.bindings[hostname];operation.result='Demo certificate revoked; local bindings removed';
      }
      operation.applied=true;changed=true;
    }
    if(changed)save();return changed;
  }
  function enqueue(kind,name,payload={},current){
    const job=P.job({name,kind:'account-tls-'+kind,scope:'user'});state.operations.unshift({id:P.uid(),jobId:job.id,kind,name,payload,certificateId:current?.id,generation:current?.generation,status:job.status,progress:job.progress,createdAt:new Date().toISOString(),applied:false});save();render();
  }
  function issue(){
    const fields=()=>S.issueFields({certificates:state.certificates.filter(item=>item.owner===owner),operations:state.operations,dnsAllowed:dnsAllowed()});
    P.form({title:'Issue a demo certificate',description:'Local metadata only. No CA is contacted and no private key is generated or stored.',fields:fields(),submitLabel:'Queue issuance simulation',onSubmit:values=>{sync();P.validate(fields(),values);enqueue('issue','Issue '+values.label,{...values,label:values.label.trim(),sans:S.names(values.sans)});}});
  }
  function inspect(record){
    const current=certificate(record.id);if(!current)throw new Error('This certificate no longer belongs to the account.');
    const content=document.createElement('div');content.className='protection-preview';content.innerHTML=`<p><strong>${esc(current.label)}</strong></p><dl><dt>SANs</dt><dd>${current.sans.map(esc).join(', ')}</dd><dt>Status</dt><dd>${esc(current.status)}</dd><dt>Expiry</dt><dd>${esc(current.expiresAt)}</dd><dt>Key profile</dt><dd>${esc(current.algorithm)}</dd><dt>Validation method</dt><dd>${esc(current.challenge)}</dd><dt>Chain</dt><dd>${current.chainValid?'Simulated valid':'Needs review'}</dd><dt>Metadata ID</dt><dd>${esc(current.id)}</dd><dt>Version</dt><dd>${current.generation}</dd></dl><p>Website bindings: ${esc(Object.keys(state.bindings).filter(hostname=>state.bindings[hostname].certificateId===current.id).join(', ')||'None')}</p><p>This is certificate metadata for a prototype, not a usable X.509 certificate.</p>`;UI.Dialog.open({title:'Certificate details',content});
  }
  async function renew(record){
    const check=()=>{sync();const current=certificate(record.id);if(!current||current.status==='Revoked')throw new Error('Revoked or unavailable certificates cannot be renewed.');if(busy(record.id))throw new Error('A lifecycle job is already pending for this certificate.');if(current.challenge==='DNS-01'&&!dnsAllowed())throw new Error('DNS-01 is blocked by the effective TXT-record policy.');return current;};
    const current=check();if(!await UI.Dialog.confirm({title:'Renew demo certificate',message:'Extend the simulated validity of '+current.label+' after a renewal job completes?'}))return;
    enqueue('renew','Renew '+current.label,{},check());
  }
  function deploy(record){
    sync();const current=certificate(record.id);if(!S.usable(current))throw new Error('The certificate must be valid, unexpired and chain-validated.');if(busy(current.id))throw new Error('A lifecycle job is already pending.');
    P.form({title:'Bind certificate to a website',description:'Only account website HTTPS bindings are available. Platform and mail-service deployment stay ROOT-owned.',fields:S.deployFields({certificate:current}),submitLabel:'Queue deployment simulation',onSubmit:values=>{sync();const latest=certificate(record.id);if(!latest)throw new Error('This certificate no longer exists.');P.validate(S.deployFields({certificate:latest}),values);if(busy(latest.id))throw new Error('A lifecycle job is already pending.');enqueue('deploy','Bind '+latest.label+' to '+values.hostname,values,latest);}});
  }
  function revoke(record){
    sync();const current=certificate(record.id);if(!current||current.status==='Revoked'||busy(current.id))throw new Error('This certificate is unavailable, revoked or already in use.');
    const fields=[{name:'reason',label:'Revocation reason',type:'select',required:true,options:['Superseded','Key compromise (demo)','Service retired'],default:'Superseded'},{name:'confirmation',label:'Type '+current.label+' to confirm',required:true,validate:value=>value===current.label?'':'Type the exact certificate label.'}];
    const hosts=Object.keys(state.bindings).filter(hostname=>state.bindings[hostname].certificateId===current.id);
    P.form({title:'Revoke demo certificate',description:'Completion removes its local website bindings: '+(hosts.join(', ')||'none')+'. No external certificate is revoked.',fields,submitLabel:'Queue revocation simulation',onSubmit:values=>{sync();P.validate(fields,values);const latest=certificate(record.id);if(!latest||latest.status==='Revoked'||busy(latest.id))throw new Error('The certificate changed while the form was open.');enqueue('revoke','Revoke '+latest.label,{reason:values.reason},latest);}});
  }
  function preferences(record){
    const current=certificate(record.id);if(!current||current.status==='Revoked')throw new Error('This certificate is unavailable or revoked.');
    P.form({title:'Renewal preference',description:'Stores the account preference. No automatic renewal scheduler runs in this prototype.',fields:[{name:'autoRenew',label:'Enable renewal preview',type:'checkbox',default:current.autoRenew}],onSubmit:values=>{const latest=certificate(record.id);if(!latest||latest.status==='Revoked')throw new Error('This certificate is unavailable or revoked.');latest.autoRenew=values.autoRenew;save();render();}});
  }
  function mountCertificates(){grid=UI.DataGrid.mount(host.querySelector('[data-account-cert-view]'),{id:'account-certificates',title:'Account certificates',rows:state.certificates.filter(item=>item.owner===owner).map(item=>({...item,sansText:item.sans.join(', '),displayStatus:item.status==='Revoked'?'Revoked':Date.parse(item.expiresAt)<=Date.now()?'Expired':item.chainValid?'Valid':'Chain review',renewal:item.autoRenew?'Preview enabled':'Manual',pending:busy(item.id)?'Pending job':'Ready'})),store:UI.createStore({scope:'user',owner,module:'certificates'}),columns:[{key:'label',label:'Certificate'},{key:'sansText',label:'Hostnames'},{key:'displayStatus',label:'Status'},{key:'expiresAt',label:'Expiry',type:'date'},{key:'pending',label:'Lifecycle'},{key:'algorithm',label:'Key profile',hidden:true},{key:'renewal',label:'Renewal',hidden:true}],rowActions:[{label:'Inspect',run:P.safe(inspect)},{label:'Renew',when:item=>item.status!=='Revoked'&&!busy(item.id),run:P.safe(renew)},{label:'Bind website',when:item=>S.usable(item)&&!busy(item.id),run:P.safe(deploy)},{label:'Preferences',when:item=>item.status!=='Revoked',run:P.safe(preferences)},{label:'Revoke',when:item=>item.status!=='Revoked'&&!busy(item.id),run:P.safe(revoke)}]});}
  function render(){
    grid?.destroy();grid=null;sync();const owned=state.certificates.filter(item=>item.owner===owner),usable=owned.filter(item=>S.usable(item));
    host.innerHTML=`<div class="protection-page"><div class="wcp-page-head"><div><div class="wcp-page-eyebrow">USER · CERTIFICATE LIFECYCLE</div><h2 class="wcp-page-heading">SSL / TLS</h2><p class="wcp-page-sub">Account hostnames, certificate metadata and website bindings for ${owner}.</p></div><div class="wcp-page-actions">${button('refresh','Refresh')}${button('issue','Issue certificate',true)}</div></div><div class="protection-metrics"><article><span>Certificates</span><strong>${owned.length}</strong><small>${usable.length} valid and unexpired demo records</small></article><article><span>Website bindings</span><strong>${Object.keys(state.bindings).length}</strong><small>Owned HTTPS hostnames</small></article><article><span>Pending jobs</span><strong>${state.operations.filter(P.pending).length}</strong><small>Account-scoped lifecycle</small></article><article><span>DNS-01 policy</span><strong>${dnsAllowed()?'Available':'Blocked'}</strong><small>Effective ROOT TXT allowance</small></article></div><nav class="wcp-tabs protection-tabs" role="tablist" aria-label="Account certificate sections">${Object.entries(tabs).map(([id,label])=>`<button type="button" id="cert-tab-${id}" class="wcp-tab ${active===id?'active':''}" role="tab" aria-controls="cert-view" aria-selected="${active===id}" tabindex="${active===id?0:-1}" data-account-cert-tab="${id}">${label}</button>`).join('')}</nav><div id="cert-view" role="tabpanel" aria-labelledby="cert-tab-${active}" data-account-cert-view></div><p class="protection-note">Browser-local simulation. No key material is handled, no certificate authority is contacted and no service is reloaded.</p></div>`;
    const view=host.querySelector('[data-account-cert-view]');if(active==='certificates')mountCertificates();
    if(active==='deployments')grid=UI.DataGrid.mount(view,{id:'account-certificate-bindings',title:'Local website HTTPS bindings',rows:Object.entries(state.bindings).map(([hostname,binding])=>{const current=certificate(binding.certificateId);return {id:hostname,hostname,label:current?.label||'Unavailable certificate',generation:binding.generation,status:!S.usable(current)?'Needs review':current.generation!==binding.generation?'New version needs deployment':'Simulated active'};}),columns:[{key:'hostname',label:'Website hostname'},{key:'label',label:'Certificate'},{key:'generation',label:'Bound version',type:'number'},{key:'status',label:'State'}]});
    if(active==='activity')grid=UI.DataGrid.mount(view,{id:'account-certificate-activity',title:'Account certificate jobs',rows:state.operations,columns:[{key:'name',label:'Operation'},{key:'status',label:'Job status'},{key:'result',label:'Local result'},{key:'createdAt',label:'Requested',type:'date'},{key:'certificateId',label:'Certificate ID',hidden:true}]});
  }
  function setTab(id){if(!Object.hasOwn(tabs,id))return;active=id;if(location.hash.slice(1)!==id)location.hash=id;render();}
  host.addEventListener('click',event=>{const tab=event.target.closest('[data-account-cert-tab]');if(tab){setTab(tab.dataset.accountCertTab);return;}const control=event.target.closest('[data-account-cert-action]');const actions={issue,refresh:render};if(control&&actions[control.dataset.accountCertAction])P.safe(actions[control.dataset.accountCertAction])();});
  host.addEventListener('keydown',event=>{const tab=event.target.closest('[data-account-cert-tab]');if(!tab||!['ArrowLeft','ArrowRight','Home','End'].includes(event.key))return;event.preventDefault();const ids=Object.keys(tabs),index=ids.indexOf(active),next=event.key==='Home'?0:event.key==='End'?ids.length-1:(index+(event.key==='ArrowRight'?1:-1)+ids.length)%ids.length;setTab(ids[next]);host.querySelector('#cert-tab-'+ids[next])?.focus();});
  window.addEventListener('hashchange',()=>{const id=location.hash.slice(1);if(Object.hasOwn(tabs,id)&&active!==id){active=id;render();}});
  document.addEventListener('wcp:runtime-change',()=>{if(sync())render();});
  save();render();const query=new URLSearchParams(location.search),requested=query.get('action');
  if(['issue','inspect'].includes(requested)){P.safe(()=>{if(requested==='issue')issue();else{const id=query.get('id'),current=certificate(id||'web');if(!current)throw new Error('The requested certificate does not belong to this account or no longer exists.');inspect(current);}})();const clean=new URL(location.href);clean.searchParams.delete('action');history.replaceState(null,'',clean.pathname+clean.search+clean.hash);}
})();
