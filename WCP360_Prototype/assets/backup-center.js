(()=>{
  'use strict';
  const host=document.querySelector('#backupCenter');if(!host)return;
  const UI=window.WCPUI,P=window.WCPPrototypeOperations,S=window.WCPProtectionSchemas,key='wcp360-backup-center-v1',owner='example.com';
  const now=Date.now(),days=86400000,defaults={policy:{cadence:'Every 6 hours',retentionDays:14,immutableDays:1,files:true,databases:true,mail:false,config:true},destinations:[{id:'primary',name:'Primary EU repository',repositoryId:'managed-eu',enabled:true,primary:true},{id:'offsite',name:'Off-site EU copy',repositoryId:'offsite-eu',enabled:true,primary:false}],points:[{id:'RP-AUTO-01',label:'Automatic application backup',owner,createdAt:new Date(now-32*60000).toISOString(),expiresAt:new Date(now+14*days).toISOString(),immutableUntil:new Date(now+days).toISOString(),resources:['files','databases','config'],sizeGiB:3.9,destinationId:'primary',verified:true},{id:'RP-DEPLOY-184',label:'Before deployment #184',owner,createdAt:new Date(now-days).toISOString(),expiresAt:new Date(now+13*days).toISOString(),immutableUntil:new Date(now-days).toISOString(),resources:['files','databases','config'],sizeGiB:3.8,destinationId:'primary',verified:true},{id:'RP-MANUAL-01',label:'Manual database snapshot',owner,createdAt:new Date(now-2*days).toISOString(),expiresAt:new Date(now+12*days).toISOString(),resources:['databases'],sizeGiB:2.4,destinationId:'offsite',verified:false}],operations:[]};
  const state=P.load(key,defaults,['points','destinations','operations']);state.policy={...defaults.policy,...state.policy};
  const tabs={overview:'Overview',points:'Restore points',policy:'Policy',storage:'Storage',tests:'Restore tests',activity:'Activity'};
  let active=Object.hasOwn(tabs,location.hash.slice(1))?location.hash.slice(1):'overview',grid=null;
  const save=()=>P.save(key,state),esc=UI.escape;
  const action=(name,label,primary=false)=>`<button type="button" class="wcp-action-btn ${primary?'primary':''}" data-backup-action="${name}">${label}</button>`;
  const currentPoint=id=>state.points.find(point=>point.id===id&&point.owner===owner);
  const destinationName=id=>state.destinations.find(item=>item.id===id)?.name||'Unavailable destination';
  function sync(){
    const jobs=P.jobs();let changed=false;
    for(const operation of state.operations){
      const job=jobs.find(item=>item.id===operation.jobId&&item.scope==='user');if(!job)continue;
      if(operation.status!==job.status||operation.progress!==job.progress){operation.status=job.status;operation.progress=job.progress;changed=true;}
      if(job.status!=='completed'||operation.applied)continue;
      if(operation.kind==='backup'){
        const payload=operation.payload,createdAt=job.completedAt||Date.now(),id='RP-'+operation.id;
        if(!state.points.some(point=>point.id===id))state.points.unshift({id,owner,label:payload.label,resources:payload.resources,destinationId:payload.destinationId,createdAt:new Date(createdAt).toISOString(),expiresAt:new Date(createdAt+payload.retentionDays*days).toISOString(),immutableUntil:new Date(createdAt+payload.immutableDays*days).toISOString(),sizeGiB:payload.sizeGiB,verified:payload.verify});
      }
      if(operation.kind==='verify'){const point=currentPoint(operation.pointId);if(point)point.verified=true;}
      operation.applied=true;operation.finishedAt=new Date(job.completedAt||Date.now()).toISOString();changed=true;
    }
    if(changed)save();return changed;
  }
  function pending(kind,pointId){return state.operations.some(item=>P.pending(item)&&(!kind||item.kind===kind)&&(!pointId||item.pointId===pointId));}
  function enqueue(kind,name,payload={},pointId){
    const job=P.job({name,kind:'account-backup-'+kind,scope:'user'});
    state.operations.unshift({id:P.uid(),jobId:job.id,kind,name,pointId,payload,status:job.status,progress:job.progress,createdAt:new Date().toISOString(),applied:false});save();render();
  }
  function createBackup(){
    sync();if(pending('backup'))throw new Error('A backup is already queued or running for this account.');
    P.form({title:'Create a demo backup',description:'Captures resource metadata only. The restore point appears after the job completes.',fields:S.backup({destinations:state.destinations,policy:state.policy}),submitLabel:'Queue backup',onSubmit:values=>{
      sync();P.validate(S.backup({destinations:state.destinations,policy:state.policy}),values);if(pending('backup'))throw new Error('A backup is already in progress.');
      const resources=S.selected(values);enqueue('backup','Back up '+owner,{...values,resources,retentionDays:Number(state.policy.retentionDays),immutableDays:Number(state.policy.immutableDays),sizeGiB:resources.reduce((sum,name)=>sum+({files:1.4,databases:2.4,mail:.6,config:.1}[name]),0)});
    }});
  }
  function restore(record){
    sync();const point=record?currentPoint(record.id):null;if(record&&!S.eligible(point))throw new Error('This point is unverified, expired, or no longer available.');
    P.form({title:'Restore account resources',description:'Staging is the default. Production requires the exact account domain. No server data is modified.',fields:S.restore({points:state.points,record:point}),submitLabel:'Queue restore simulation',onSubmit:values=>{
      sync();const selected=currentPoint(values.pointId);P.validate(S.restore({points:state.points,record:selected}),values);
      if(!S.eligible(selected))throw new Error('The selected point is no longer eligible for restore.');
      const resources=S.selected(values);if(!resources.length||resources.some(name=>!selected.resources.includes(name)))throw new Error('Select resources included in this restore point.');
      if(pending('restore'))throw new Error('An account restoration is already in progress.');
      enqueue('restore','Restore '+selected.label+' to '+values.target,{target:values.target,resources},selected.id);
    }});
  }
  async function verify(record){
    sync();const point=currentPoint(record.id);if(!point||Date.parse(point.expiresAt)<=Date.now())throw new Error('This point is missing or expired.');
    if(pending(null,point.id))throw new Error('An operation is already using this point.');
    enqueue('verify','Verify '+point.label,{},point.id);
  }
  async function testRestore(record){
    sync();const point=currentPoint(record.id);if(!S.eligible(point))throw new Error('Only verified, unexpired points can be tested.');
    if(pending(null,point.id))throw new Error('An operation is already using this point.');
    if(!await UI.Dialog.confirm({title:'Run isolated restore test',message:'Simulate restoration of '+point.label+' into an isolated test target?'}))return;
    sync();if(!S.eligible(currentPoint(record.id))||pending(null,record.id))throw new Error('This restore point changed while confirmation was open.');
    enqueue('test','Test restore '+point.label,{target:'Isolated test'},point.id);
  }
  async function deletePoint(record){
    sync();if(!S.removablePoint(currentPoint(record.id),state.operations))throw new Error('This point has an immutable retention, legal hold, or active operation.');
    if(!await UI.Dialog.confirm({title:'Remove demo restore point',message:'Permanently remove this local restore-point record?',confirmLabel:'Remove point',typed:'DELETE '+record.id}))return;
    sync();if(!S.removablePoint(currentPoint(record.id),state.operations))throw new Error('The point is now protected or in use.');
    state.points=state.points.filter(point=>point.id!==record.id);save();render();
  }
  function editPolicy(){P.form({title:'Account backup policy',description:'Changes apply to future demo points. Existing expiry and immutable dates are preserved. Automatic execution is not simulated.',fields:S.policy(state.policy),onSubmit:values=>{P.validate(S.policy(state.policy),values);state.policy={...values,retentionDays:Number(values.retentionDays),immutableDays:Number(values.immutableDays)};save();render();}});}
  function attachDestination(){P.form({title:'Attach an assigned destination',description:'Choose from repositories delegated by ROOT. Provider endpoints and credentials are managed separately.',fields:S.destination({destinations:state.destinations}),onSubmit:values=>{P.validate(S.destination({destinations:state.destinations}),values);state.destinations.push({...values,id:P.uid(),enabled:true,primary:false});save();render();}});}
  async function removeDestination(record){
    const used=()=>state.points.some(point=>point.destinationId===record.id)||state.operations.some(operation=>P.pending(operation)&&operation.payload?.destinationId===record.id);
    if(record.primary||used())throw new Error('The primary destination or a repository with retained points cannot be detached.');
    if(!await UI.Dialog.confirm({title:'Detach destination',message:'Remove the local mapping for '+record.name+'?'}))return;
    sync();if(used())throw new Error('This destination is now in use.');state.destinations=state.destinations.filter(item=>item.id!==record.id);save();render();
  }
  const pointRows=()=>state.points.filter(point=>point.owner===owner).map(point=>({...point,resourcesText:point.resources.join(', '),destination:destinationName(point.destinationId),status:Date.parse(point.expiresAt)<=Date.now()?'Expired':point.verified?'Verified':'Unverified'}));
  function mountPoints(){grid=UI.DataGrid.mount(host.querySelector('[data-protection-view]'),{id:'backup-points',title:'Account restore points',rows:pointRows(),store:UI.createStore({scope:'user',owner,module:'backup-points'}),columns:[{key:'label',label:'Restore point'},{key:'createdAt',label:'Created',type:'date'},{key:'resourcesText',label:'Resources'},{key:'sizeGiB',label:'Size (GiB)',type:'number'},{key:'destination',label:'Destination',hidden:true},{key:'status',label:'Integrity'},{key:'expiresAt',label:'Expires',type:'date',hidden:true}],rowActions:[{label:'Restore',when:point=>S.eligible(point),run:P.safe(restore)},{label:'Verify',when:point=>!point.verified&&Date.parse(point.expiresAt)>Date.now(),run:P.safe(verify)},{label:'Test restore',when:point=>S.eligible(point),run:P.safe(testRestore)},{label:'Remove',when:point=>S.removablePoint(point,state.operations),run:P.safe(deletePoint)}]});}
  function mountActivity(tests=false){grid=UI.DataGrid.mount(host.querySelector('[data-protection-view]'),{id:tests?'backup-tests':'backup-activity',title:tests?'Isolated restore tests':'Backup and restore activity',rows:state.operations.filter(item=>!tests||item.kind==='test'),store:UI.createStore({scope:'user',owner,module:tests?'backup-tests':'backup-activity'}),columns:[{key:'name',label:'Operation'},{key:'kind',label:'Kind'},{key:'status',label:'Status'},{key:'progress',label:'Progress (%)',type:'number'},{key:'createdAt',label:'Created',type:'date'},{key:'pointId',label:'Point',hidden:true}],rowActions:[{label:'Inspect',run:operation=>{const content=document.createElement('div');content.innerHTML=`<p>${esc(operation.name)}</p><p>Job: ${esc(operation.jobId)}</p><p>Status: ${esc(operation.status)}</p><p>Target: ${esc(operation.payload.target||owner)}</p><p>This job simulates metadata and never restores actual server content.</p>`;UI.Dialog.open({title:'Operation details',content});}}]});}
  function render(){
    grid?.destroy();grid=null;sync();
    const valid=state.points.filter(point=>S.eligible(point)),completedTests=state.operations.filter(item=>item.kind==='test'&&item.status==='completed').length;
    host.innerHTML=`<div class="protection-page"><div class="wcp-page-head"><div><div class="wcp-page-eyebrow">USER · APPLICATION PROTECTION</div><h2 class="wcp-page-heading">Backups & recovery</h2><p class="wcp-page-sub">Restore-point metadata, protection policy and recovery simulations for ${owner}.</p></div><div class="wcp-page-actions">${action('restore','Restore')}${action('backup','Backup now',true)}</div></div><div class="protection-metrics"><article><span>Restore points</span><strong>${state.points.filter(point=>point.owner===owner).length}</strong><small>${valid.length} verified and unexpired</small></article><article><span>Policy</span><strong>${esc(state.policy.cadence)}</strong><small>${state.policy.retentionDays} days retention · preview only</small></article><article><span>Active jobs</span><strong>${state.operations.filter(P.pending).length}</strong><small>Account-scoped Job Center</small></article><article><span>Restore tests</span><strong>${completedTests}</strong><small>Completed isolated simulations</small></article></div><nav class="wcp-tabs protection-tabs" role="tablist" aria-label="Backup sections">${Object.entries(tabs).map(([id,label])=>`<button type="button" class="wcp-tab ${active===id?'active':''}" id="backup-tab-${id}" role="tab" aria-controls="backup-view" aria-selected="${active===id}" tabindex="${active===id?0:-1}" data-backup-tab="${id}">${label}</button>`).join('')}</nav><div id="backup-view" role="tabpanel" aria-labelledby="backup-tab-${active}" data-protection-view></div><p class="protection-note">Browser-local simulation. Backups contain metadata only; the schedule does not run while this prototype is closed.</p></div>`;
    const view=host.querySelector('[data-protection-view]');
    if(active==='points')mountPoints();
    if(active==='tests'||active==='activity')mountActivity(active==='tests');
    if(active==='overview')view.innerHTML=`<div class="protection-panels"><section class="wcp-panel"><div class="wcp-panel-head"><div><div class="wcp-panel-title">Recovery readiness</div><div class="wcp-panel-sub">Verified and unexpired points can be restored.</div></div></div><div class="protection-panel-body"><p>${valid.length?esc(valid[0].label):'No eligible restore point. Create and verify a backup first.'}</p>${action('points','Browse restore points')}${action('activity','View activity')}</div></section><section class="wcp-panel"><div class="wcp-panel-head"><div class="wcp-panel-title">Protection scope</div></div><div class="protection-panel-body"><p>${esc(S.selected(state.policy).join(', '))}</p><p>Encryption and repository credentials remain ROOT-managed.</p>${action('edit-policy','Edit policy')}</div></section></div>`;
    if(active==='policy')view.innerHTML=`<section class="wcp-panel"><div class="wcp-panel-head"><div class="wcp-panel-title">Effective backup policy</div>${action('edit-policy','Edit policy')}</div><div class="protection-panel-body"><dl><div><dt>Schedule preview</dt><dd>${esc(state.policy.cadence)}</dd></div><div><dt>Retention</dt><dd>${state.policy.retentionDays} days</dd></div><div><dt>Immutable period</dt><dd>${state.policy.immutableDays} days</dd></div><div><dt>Resources</dt><dd>${esc(S.selected(state.policy).join(', '))}</dd></div></dl></div></section>`;
    if(active==='storage'){
      view.innerHTML=`<div class="protection-section-head"><div><h3>Assigned backup destinations</h3><p>Account mappings to ROOT-managed repositories.</p></div>${action('attach','Attach destination')}</div><div data-backup-storage></div>`;
      grid=UI.DataGrid.mount(view.querySelector('[data-backup-storage]'),{id:'backup-storage',title:'Assigned destinations',rows:state.destinations.map(item=>({...item,role:item.primary?'Primary':'Secondary',status:item.enabled?'Enabled':'Disabled'})),columns:[{key:'name',label:'Name'},{key:'repositoryId',label:'Repository'},{key:'role',label:'Role'},{key:'status',label:'State'}],rowActions:[{label:'Detach',when:item=>!item.primary,run:P.safe(removeDestination)}]});
    }
  }
  function setTab(id){if(!Object.hasOwn(tabs,id))return;active=id;if(location.hash.slice(1)!==id)location.hash=id;render();}
  const actions={backup:createBackup,restore:()=>restore(),points:()=>setTab('points'),activity:()=>setTab('activity'),'edit-policy':editPolicy,attach:attachDestination};
  host.addEventListener('click',event=>{const tab=event.target.closest('[data-backup-tab]');if(tab){setTab(tab.dataset.backupTab);return;}const control=event.target.closest('[data-backup-action]');if(control&&actions[control.dataset.backupAction])P.safe(actions[control.dataset.backupAction])();});
  host.addEventListener('keydown',event=>{const tab=event.target.closest('[data-backup-tab]');if(!tab||!['ArrowRight','ArrowLeft','Home','End'].includes(event.key))return;event.preventDefault();const ids=Object.keys(tabs),index=ids.indexOf(active),next=event.key==='Home'?0:event.key==='End'?ids.length-1:(index+(event.key==='ArrowRight'?1:-1)+ids.length)%ids.length;setTab(ids[next]);host.querySelector('#backup-tab-'+ids[next])?.focus();});
  window.addEventListener('hashchange',()=>{const id=location.hash.slice(1);if(Object.hasOwn(tabs,id)&&active!==id){active=id;render();}});
  document.addEventListener('wcp:runtime-change',()=>{if(sync())render();});
  render();
  const query=new URLSearchParams(location.search),requested=query.get('action');
  if(requested==='restore'){
    setTab('points');const requestedId=query.get('point')||query.get('id'),point=currentPoint(requestedId);P.safe(()=>{if(requestedId&&!point)throw new Error('The requested restore point does not belong to this account or no longer exists.');restore(point);})();
    const clean=new URL(location.href);clean.searchParams.delete('action');history.replaceState(null,'',clean.pathname+clean.search+clean.hash);
  }
})();
