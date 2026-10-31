(()=>{
  'use strict';
  const host=document.querySelector('#scheduleCenter');if(!host)return;
  const UI=window.WCPUI,P=window.WCPPrototypeOperations,S=window.WCPProtectionSchemas;
  const scope=location.pathname.endsWith('/scheduled-tasks.html')?'root':'user',rootScope=scope==='root',owner=rootScope?'platform':'example.com';
  const key=rootScope?'wcp360-platform-schedules-v1':'wcp360-account-cron-v1',policyKey='wcp360-platform-schedules-v1';
  const defaults={policy:structuredClone(S.tenantPolicy),rows:rootScope?[
    {id:'quota',name:'Account quota reconciliation',operation:'Reconcile account quotas',expression:'4 * * * *',timezone:'UTC',timeoutSeconds:300,enabled:true},
    {id:'renewal',name:'Managed certificate renewal',operation:'Renew managed certificates',expression:'20 2 * * *',timezone:'UTC',timeoutSeconds:600,enabled:true},
    {id:'verification',name:'Weekly backup verification',operation:'Verify backup manifests',expression:'0 5 * * 0',timezone:'UTC',timeoutSeconds:900,enabled:true},
    {id:'metering',name:'Monthly usage review',operation:'Close usage statements',expression:'50 23 1 * *',timezone:'UTC',timeoutSeconds:300,enabled:true}
  ]:[{id:'artisan',name:'Application scheduler',command:'php artisan schedule:run',expression:'*/5 * * * *',timezone:'Europe/Paris',timeoutSeconds:300,enabled:true},{id:'exports',name:'Expired export cleanup',command:'cleanup:exports',expression:'0 2 * * *',timezone:'Europe/Paris',timeoutSeconds:300,enabled:true}],runs:[]};
  const state=P.load(key,defaults,['rows','runs']);state.policy={...S.tenantPolicy,...state.policy};
  const tabs={schedules:'Schedules',history:'Execution history',...(rootScope?{policy:'Tenant policy'}:{})};
  let active=Object.hasOwn(tabs,location.hash.slice(1))?location.hash.slice(1):'schedules',grid=null;
  const save=()=>P.save(key,state),esc=UI.escape;
  const tenantPolicy=()=>rootScope?state.policy:{...S.tenantPolicy,...P.load(policyKey,{policy:S.tenantPolicy}).policy};
  const fields=record=>S.schedule({rows:state.rows,record,scope,policy:tenantPolicy()});
  const action=(name,label,primary=false)=>`<button type="button" class="wcp-action-btn ${primary?'primary':''}" data-schedule-action="${name}">${label}</button>`;
  function sync(){const jobs=P.jobs();let changed=false;for(const run of state.runs){const job=jobs.find(item=>item.id===run.jobId&&item.scope===scope);if(job&&(job.status!==run.status||job.progress!==run.progress)){run.status=job.status;run.progress=job.progress;run.finishedAt=job.completedAt?new Date(job.completedAt).toISOString():'';changed=true;}}if(changed)save();return changed;}
  const busy=id=>state.runs.some(run=>run.scheduleId===id&&P.pending(run));
  function edit(record){
    P.form({title:record?'Edit schedule':rootScope?'Create platform schedule':'Create application cron job',description:rootScope?'Platform operations are selected from a fixed catalog and use UTC.':'Commands are stored as prototype text and are never executed. The delegated tenant policy applies.',fields:fields(record),onSubmit:values=>{
      const current=record?state.rows.find(item=>item.id===record.id):null;if(record&&!current)throw new Error('This schedule no longer exists.');
      P.validate(fields(current),values);const next={...values,timeoutSeconds:Number(values.timeoutSeconds)};
      if(current)Object.assign(current,next);else state.rows.push({...next,id:P.uid()});save();render();
    }});
  }
  function preview(record){
    const current=state.rows.find(item=>item.id===record.id);if(!current)throw new Error('This schedule no longer exists.');
    const dates=S.nextRuns(current.expression,current.timezone),content=document.createElement('div');content.className='protection-preview';
    content.innerHTML=`<p><strong>${esc(current.name)}</strong> · <code>${esc(current.expression)}</code></p><p>Timezone: ${esc(current.timezone)}</p>${dates.length?'<ol>'+dates.map(date=>'<li>'+esc(new Date(date).toLocaleString('en-GB',{timeZone:current.timezone}))+' · '+esc(date)+'</li>').join('')+'</ol>':'<p>No occurrence found within the next 31 days. The expression may still be valid outside this preview window.</p>'}<p>Preview uses timezone rules, skips missing local times and shows both occurrences of repeated local times. No scheduler is running in the background.</p>`;
    UI.Dialog.open({title:'Next-run preview',content});
  }
  function run(record,scheduled=false){
    sync();const current=state.rows.find(item=>item.id===record.id);if(!current||!current.enabled)throw new Error('This schedule is disabled or no longer exists.');
    P.validate(fields(current),current);
    if(busy(current.id))throw new Error('A run is already pending for this schedule.');
    const limit=rootScope?50:tenantPolicy().concurrent;if(state.runs.filter(P.pending).length>=limit)throw new Error('The concurrent-run allowance is exhausted.');
    const due=scheduled?S.nextRuns(current.expression,current.timezone,{count:1})[0]:null;if(scheduled&&!due)throw new Error('No occurrence exists within the 31-day preparation window.');
    const job=P.job({name:(scheduled?'Prepared occurrence: ':'Run once: ')+current.name,kind:rootScope?'platform-schedule':'account-cron',scope,status:scheduled?'scheduled':'running',reason:scheduled?'Preview due '+due+'; manual start required.':'Manual prototype execution; no command is executed.'});
    state.runs.unshift({id:P.uid(),scheduleId:current.id,name:current.name,expression:current.expression,timezone:current.timezone,operation:current.operation||current.command,timeoutSeconds:current.timeoutSeconds,due:due||'',jobId:job.id,status:job.status,progress:job.progress,createdAt:new Date().toISOString()});save();render();
  }
  async function toggle(rows,enabled){
    const ids=rows.map(item=>item.id),current=state.rows.filter(item=>ids.includes(item.id));if(current.length!==ids.length||!ids.length)throw new Error('Select existing schedules.');
    if(enabled)current.forEach(item=>P.validate(fields(item),item));
    if(!await UI.Dialog.confirm({title:enabled?'Enable schedules':'Pause schedules',message:enabled?'Enable the selected schedule previews?':'Pause future previews? Already queued jobs remain in the Job Center.'}))return;
    const fresh=state.rows.filter(item=>ids.includes(item.id));if(fresh.length!==ids.length)throw new Error('A schedule changed while confirmation was open.');
    if(enabled)fresh.forEach(item=>P.validate(fields(item),item));fresh.forEach(item=>item.enabled=enabled);save();render();
  }
  async function remove(record){
    sync();if(busy(record.id))throw new Error('Cancel or complete the pending run before removing this schedule.');
    if(!await UI.Dialog.confirm({title:'Remove local schedule',message:'Remove '+record.name+'? Existing execution history is retained.',typed:'DELETE '+record.name,confirmLabel:'Remove schedule'}))return;
    sync();if(busy(record.id))throw new Error('This schedule is now in use.');state.rows=state.rows.filter(item=>item.id!==record.id);save();render();
  }
  function editPolicy(){P.form({title:'Delegated tenant cron policy',description:'Applies to USER cron configurations. Existing schedules are revalidated when edited, enabled or run.',fields:S.tenantPolicyFields(state.policy),onSubmit:values=>{P.validate(S.tenantPolicyFields(state.policy),values);state.policy=Object.fromEntries(Object.entries(values).map(([name,value])=>[name,Number(value)]));save();render();}});}
  function rows(){return state.rows.map(item=>{const result=UI.validateValues(fields(item),item);return {...item,status:!result.valid?'Policy review required':item.enabled?'Enabled':'Paused',lastStatus:state.runs.find(run=>run.scheduleId===item.id)?.status||'No local run'};});}
  function mountSchedules(){grid=UI.DataGrid.mount(host.querySelector('[data-protection-view]'),{id:'schedule-'+scope,title:rootScope?'Platform operation schedules':'Application cron jobs',rows:rows(),store:UI.createStore({scope,owner,module:'schedules'}),columns:[{key:'name',label:'Schedule'},{key:'expression',label:'Cron expression'},{key:'timezone',label:'Timezone'},{key:rootScope?'operation':'command',label:rootScope?'Operation':'Command',hidden:true},{key:'status',label:'State'},{key:'lastStatus',label:'Latest run'},{key:'timeoutSeconds',label:'Runtime ceiling (s)',type:'number',hidden:true}],rowActions:[{label:'Edit',run:P.safe(edit)},{label:'Preview',run:P.safe(preview)},{label:'Run once',when:item=>item.enabled,run:P.safe(item=>run(item))},{label:'Prepare next',when:item=>item.enabled,run:P.safe(item=>run(item,true))},{label:'Pause',when:item=>item.enabled,run:P.safe(item=>toggle([item],false))},{label:'Enable',when:item=>!item.enabled,run:P.safe(item=>toggle([item],true))},{label:'Remove',when:item=>!busy(item.id),run:P.safe(remove)}],bulkActions:[{label:'Pause',run:P.safe(items=>toggle(items,false))},{label:'Enable',run:P.safe(items=>toggle(items,true))}]});}
  function mountHistory(){grid=UI.DataGrid.mount(host.querySelector('[data-protection-view]'),{id:'schedule-history-'+scope,title:'Execution history',rows:state.runs,store:UI.createStore({scope,owner,module:'schedule-history'}),columns:[{key:'name',label:'Schedule'},{key:'status',label:'Status'},{key:'progress',label:'Progress (%)',type:'number'},{key:'createdAt',label:'Requested',type:'date'},{key:'due',label:'Prepared occurrence (UTC)',type:'date',hidden:true},{key:'operation',label:'Captured operation',hidden:true}],rowActions:[{label:'Inspect',run:record=>{const content=document.createElement('div');content.className='protection-preview';content.innerHTML=`<p>Job: ${esc(record.jobId)}</p><p>Schedule: ${esc(record.expression)} · ${esc(record.timezone)}</p><p>Captured operation: <code>${esc(record.operation)}</code></p><p>Status: ${esc(record.status)}</p><p>${record.due?'Prepared for '+esc(record.due)+'; manual Job Center start required.':'Manually requested simulation.'}</p>`;UI.Dialog.open({title:'Execution details',content});}}]});}
  function render(){
    grid?.destroy();grid=null;sync();const policy=tenantPolicy();
    host.innerHTML=`<div class="protection-page"><div class="wcp-page-head"><div><div class="wcp-page-eyebrow">${scope.toUpperCase()} · SCHEDULED EXECUTION</div><h2 class="wcp-page-heading">${rootScope?'Platform schedules':'Application cron'}</h2><p class="wcp-page-sub">${rootScope?'Approved platform operations, tenant execution policy and scoped history.':'Account-scoped commands, timezone previews and manual execution simulations.'}</p></div><div class="wcp-page-actions">${action('refresh','Refresh')}${action('create',rootScope?'Create platform schedule':'Create cron job',true)}</div></div><div class="protection-metrics"><article><span>Schedules</span><strong>${state.rows.length}</strong><small>${state.rows.filter(item=>item.enabled).length} enabled previews</small></article><article><span>Pending runs</span><strong>${state.runs.filter(P.pending).length}</strong><small>${rootScope?'50 platform':'Up to '+policy.concurrent+' account'} concurrent runs</small></article><article><span>Minimum cadence</span><strong>${rootScope?5:policy.minimumMinutes} min</strong><small>Numeric cron expressions</small></article><article><span>Scope</span><strong>${rootScope?'ROOT':'USER'}</strong><small>${rootScope?'Platform catalog · UTC':'Owned application · '+owner}</small></article></div><nav class="wcp-tabs protection-tabs" role="tablist" aria-label="Schedule sections">${Object.entries(tabs).map(([id,label])=>`<button type="button" class="wcp-tab ${active===id?'active':''}" role="tab" id="schedule-tab-${id}" aria-controls="schedule-view" aria-selected="${active===id}" tabindex="${active===id?0:-1}" data-schedule-tab="${id}">${label}</button>`).join('')}</nav><div id="schedule-view" role="tabpanel" aria-labelledby="schedule-tab-${active}" data-protection-view></div><p class="protection-note">This prototype stores schedules and simulates jobs. Prepared occurrences wait for a manual start in the Job Center. Commands are never executed and schedules do not trigger automatically.</p></div>`;
    if(active==='schedules')mountSchedules();if(active==='history')mountHistory();
    if(active==='policy')host.querySelector('[data-protection-view]').innerHTML=`<section class="wcp-panel"><div class="wcp-panel-head"><div class="wcp-panel-title">Tenant cron ceilings</div>${action('edit-policy','Edit policy')}</div><div class="protection-panel-body"><dl><div><dt>Minimum interval</dt><dd>${policy.minimumMinutes} minutes</dd></div><div><dt>Runtime ceiling</dt><dd>${policy.timeoutSeconds} seconds</dd></div><div><dt>Concurrency</dt><dd>${policy.concurrent} runs per account</dd></div></dl><p>USER schedules stay in their account workspace. This ROOT center does not edit application commands.</p></div></section>`;
  }
  function setTab(id){if(!Object.hasOwn(tabs,id))return;active=id;if(location.hash.slice(1)!==id)location.hash=id;render();}
  host.addEventListener('click',event=>{const tab=event.target.closest('[data-schedule-tab]');if(tab){setTab(tab.dataset.scheduleTab);return;}const control=event.target.closest('[data-schedule-action]');if(!control)return;const actions={create:()=>edit(),refresh:render,...(rootScope?{'edit-policy':editPolicy}:{})};if(actions[control.dataset.scheduleAction])P.safe(actions[control.dataset.scheduleAction])();});
  host.addEventListener('keydown',event=>{const tab=event.target.closest('[data-schedule-tab]');if(!tab||!['ArrowRight','ArrowLeft','Home','End'].includes(event.key))return;event.preventDefault();const ids=Object.keys(tabs),index=ids.indexOf(active),next=event.key==='Home'?0:event.key==='End'?ids.length-1:(index+(event.key==='ArrowRight'?1:-1)+ids.length)%ids.length;setTab(ids[next]);host.querySelector('#schedule-tab-'+ids[next])?.focus();});
  window.addEventListener('hashchange',()=>{const id=location.hash.slice(1);if(Object.hasOwn(tabs,id)&&id!==active){active=id;render();}});
  document.addEventListener('wcp:runtime-change',()=>{if(sync())render();});
  render();
})();
