(()=>{
  'use strict';
  const host=document.querySelector('#accountDNS');if(!host)return;
  const UI=window.WCPUI,P=window.WCPPrototypeOperations,S=window.WCPDNSTLSSchemas,key='wcp360-account-dns-v1',zone=S.zone;
  const seeds=[{id:'soa',name:zone,type:'SOA',value:'ns1.example.com. hostmaster.example.com.',ttl:3600,protected:true},{id:'ns1',name:zone,type:'NS',value:'ns1.example.com.',ttl:3600,protected:true},{id:'ns2',name:zone,type:'NS',value:'ns2.example.com.',ttl:3600,protected:true},{id:'apex',name:zone,type:'A',value:'192.0.2.10',ttl:300},{id:'www',name:'www.'+zone,type:'CNAME',value:zone+'.',ttl:300},{id:'mail',name:'mail.'+zone,type:'A',value:'192.0.2.20',ttl:600},{id:'mx',name:zone,type:'MX',value:'mail.'+zone+'.',priority:10,ttl:600},{id:'spf',name:zone,type:'TXT',value:'v=spf1 include:_spf.example.com ~all',ttl:600}];
  const state=P.load(key,{records:seeds,published:structuredClone(seeds),revision:1,publishedRevision:1,operations:[],observations:[],securityCheck:null},['records','published','operations','observations']);
  let rootState=null;const policy=()=>{try{rootState=JSON.parse(localStorage.getItem('wcp360-dns-center-v1')||'null');}catch{rootState=null;}return S.dnsPolicy(rootState);};
  const tabs={records:'Draft records',published:'Published records',dnssec:'DNSSEC',propagation:'Propagation',activity:'Activity'};
  let active=Object.hasOwn(tabs,location.hash.slice(1))?location.hash.slice(1):'records',grid=null;
  const save=()=>P.save(key,state),esc=UI.escape;
  const signature=record=>JSON.stringify([S.dataKey(record),record.ttl]);
  const changed=()=>state.records.length!==state.published.length||state.records.some(record=>!state.published.some(item=>item.id===record.id&&signature(item)===signature(record)));
  const editable=record=>!record.protected&&policy().types.includes(record.type);
  const button=(action,label,primary=false)=>`<button type="button" class="wcp-action-btn ${primary?'primary':''}" data-account-dns-action="${action}">${label}</button>`;
  function sync(){
    const jobs=P.jobs();let changed=false;
    for(const operation of state.operations){const job=jobs.find(item=>item.id===operation.jobId&&item.scope==='user');if(!job)continue;
      if(job.status!==operation.status||job.progress!==operation.progress){operation.status=job.status;operation.progress=job.progress;changed=true;}
      if(job.status!=='completed'||operation.applied)continue;
      if(operation.kind==='publish'){
        const issues=S.dnsIssues(operation.snapshot,policy(),{baseline:state.published});
        if(issues.length||policy().error){operation.result='Policy review required';operation.message=policy().error||issues[0].message;}
        else{state.published=structuredClone(operation.snapshot);state.publishedRevision=operation.revision;state.observations=[];state.securityCheck=null;operation.result='Demo publication applied';}
      }else if(operation.revision!==state.publishedRevision)operation.result='Stale: published revision changed';
      else if(operation.kind==='propagation'){
        state.observations=['Paris','Frankfurt','Singapore'].map((location,index)=>({id:operation.id+'-'+index,location,revision:operation.revision,answer:operation.answer,status:'Simulated match',checkedAt:new Date().toISOString()}));operation.result='Simulated observations ready';
      }else{state.securityCheck={revision:operation.revision,checkedAt:new Date().toISOString(),status:'Simulated validation complete'};operation.result='Demo validation complete';}
      operation.applied=true;changed=true;
    }
    if(changed)save();return changed;
  }
  function enqueue(kind,name,data={}){const job=P.job({name,kind:'account-dns-'+kind,scope:'user'});state.operations.unshift({id:P.uid(),jobId:job.id,kind,name,status:job.status,progress:job.progress,revision:state.publishedRevision,...data,createdAt:new Date().toISOString(),applied:false});save();render();}
  function edit(record){
    if(record&&!editable(record))throw new Error('This record is managed by ROOT or its type is disabled.');
    P.form({title:record?'Edit DNS record':'Add DNS record',description:'Changes remain in the local draft until you review and publish the zone.',fields:S.recordFields({records:state.records,policy:policy(),record}),onSubmit:values=>{
      const current=record?state.records.find(item=>item.id===record.id):null;if(record&&(!current||!editable(current)))throw new Error('This record is unavailable or protected by the current policy.');
      P.validate(S.recordFields({records:state.records,policy:policy(),record:current}),values);
      const next={...S.normalizeRecord(values,{id:current?.id||P.uid()}),note:String(values.note||'')};
      if(current)Object.assign(current,next);else state.records.push(next);state.revision++;save();render();
    }});
  }
  async function remove(rows){
    const ids=rows.map(item=>item.id),valid=()=>ids.length&&ids.every(id=>{const record=state.records.find(item=>item.id===id);return record&&editable(record);});
    if(!valid())throw new Error('Select existing account-editable records. Protected records cannot be removed.');
    if(!await UI.Dialog.confirm({title:'Remove draft records',message:'Remove '+ids.length+' records from the draft? Published DNS remains available until the next publication.'}))return;
    if(!valid())throw new Error('The selection changed while confirmation was open.');state.records=state.records.filter(item=>!ids.includes(item.id));state.revision++;save();render();
  }
  function importRecords(){P.form({title:'Import draft records',description:'Add a JSON array of record objects. The complete batch is validated before any record is added. SOA and NS remain managed by ROOT.',fields:[{name:'json',label:'JSON records',type:'textarea',required:true,maxLength:100000,default:'[{"name":"api","type":"A","value":"192.0.2.30","ttl":300}]',validate:value=>{try{S.validateBatch(JSON.parse(value),state.records,policy());return '';}catch(error){return error.message;}}}],submitLabel:'Add to draft',onSubmit:values=>{const records=S.validateBatch(JSON.parse(values.json),state.records,policy(),{uid:P.uid});state.records.push(...records);state.revision++;save();render();}});}
  async function publish(){
    sync();if(!changed())throw new Error('The draft is already published.');
    const check=()=>{const current=policy(),issues=S.dnsIssues(state.records,current,{baseline:state.published});if(current.error||issues.length)throw new Error(current.error||issues[0].message);if(state.operations.some(item=>item.kind==='publish'&&P.pending(item)))throw new Error('A publication is already pending.');};check();
    const content=document.createElement('div');content.className='protection-preview';
    const changes=[];for(const record of state.records){const previous=state.published.find(item=>item.id===record.id);if(!previous||signature(previous)!==signature(record))changes.push((previous?'Edit ':'Add ')+S.relative(record.name)+' '+record.type+' '+record.value);}
    for(const record of state.published)if(!state.records.some(item=>item.id===record.id))changes.push('Remove '+S.relative(record.name)+' '+record.type+' '+record.value);
    content.innerHTML='<p>Review '+changes.length+' local changes for '+esc(zone)+'.</p><ul>'+changes.map(value=>'<li>'+esc(value)+'</li>').join('')+'</ul><p>A simulated publication job will capture this draft revision.</p>';
    const dialog=UI.Dialog.open({title:'Review DNS publication',content}),confirm=document.createElement('button');confirm.type='button';confirm.className='wcp-ui-button primary';confirm.textContent='Publish demo revision';content.append(confirm);
    confirm.addEventListener('click',P.safe(()=>{check();enqueue('publish','Publish '+zone+' revision '+state.revision,{revision:state.revision,snapshot:structuredClone(state.records)});dialog.close();}));
  }
  async function discard(){const check=()=>{sync();if(state.operations.some(item=>item.kind==='publish'&&P.pending(item)))throw new Error('Complete or cancel the pending publication before discarding the draft.');};check();if(!await UI.Dialog.confirm({title:'Discard DNS draft',message:'Replace all draft records with the last published demo revision?',typed:zone,confirmLabel:'Discard draft'}))return;check();state.records=structuredClone(state.published);state.revision++;save();render();}
  function check(kind){sync();if(state.operations.some(item=>item.kind===kind&&P.pending(item)))throw new Error('This check is already pending.');const answer=state.published.filter(item=>item.type==='A').map(item=>S.relative(item.name)+' = '+item.value).join('; ')||'No published A record';enqueue(kind,kind==='dnssec'?'Validate DNSSEC metadata for '+zone:'Simulate propagation for '+zone,{answer});}
  function mountRecords(published=false){const records=published?state.published:state.records;grid=UI.DataGrid.mount(host.querySelector('[data-account-dns-view]'),{id:published?'dns-published':'dns-records',title:published?'Published account records':'Draft account records',rows:records.map(record=>({...record,owner:S.relative(record.name),access:record.protected?'ROOT-managed':policy().types.includes(record.type)?'Editable':'Type disabled'})),store:UI.createStore({scope:'user',owner:zone,module:published?'dns-published':'dns-draft'}),columns:[{key:'owner',label:'Name'},{key:'type',label:'Type'},{key:'value',label:'Value'},{key:'ttl',label:'TTL (s)',type:'number'},{key:'priority',label:'Priority',type:'number',hidden:true},{key:'weight',label:'Weight',type:'number',hidden:true},{key:'port',label:'Port',type:'number',hidden:true},{key:'access',label:'Access'},{key:'note',label:'Note',hidden:true}],...(!published?{rowActions:[{label:'Edit',when:editable,run:P.safe(edit)},{label:'Remove',when:editable,run:P.safe(record=>remove([record]))}],bulkActions:[{label:'Remove from draft',run:P.safe(remove)}]}:{})});}
  function render(){
    grid?.destroy();grid=null;sync();const current=policy();
    host.innerHTML=`<div class="protection-page"><div class="wcp-page-head"><div><div class="wcp-page-eyebrow">USER · ACCOUNT DNS</div><h2 class="wcp-page-heading">${zone}</h2><p class="wcp-page-sub">Edit a draft, review the changes and simulate publication to the account zone.</p></div><div class="wcp-page-actions">${button('import','Import JSON')}${button('add','Add record',true)}</div></div><div class="protection-metrics"><article><span>Draft records</span><strong>${state.records.length}</strong><small>${changed()?'Unpublished changes':'Draft matches publication'}</small></article><article><span>Published revision</span><strong>${state.publishedRevision}</strong><small>${state.published.length} local records</small></article><article><span>Minimum TTL</span><strong>${current.minimumTtl??'Review'}</strong><small>ROOT policy · seconds</small></article><article><span>Publication</span><strong>${state.operations.some(item=>item.kind==='publish'&&P.pending(item))?'Pending':'Ready'}</strong><small>Simulated Job Center</small></article></div><div class="wcp-page-actions">${button('publish','Review & publish',true)}${button('discard','Discard draft')}${button('refresh','Refresh policy')}</div><nav class="wcp-tabs protection-tabs" role="tablist" aria-label="Account DNS sections">${Object.entries(tabs).map(([id,label])=>`<button type="button" id="dns-tab-${id}" class="wcp-tab ${active===id?'active':''}" role="tab" aria-controls="dns-view" aria-selected="${active===id}" tabindex="${active===id?0:-1}" data-account-dns-tab="${id}">${label}</button>`).join('')}</nav><div id="dns-view" role="tabpanel" aria-labelledby="dns-tab-${active}" data-account-dns-view></div><p class="protection-note">Local prototype. No DNS records are published externally. Effective types: ${esc(current.types.join(', ')||'None')}. ${esc(current.error)}</p></div>`;
    const view=host.querySelector('[data-account-dns-view]');
    if(active==='records'||active==='published')mountRecords(active==='published');
    if(active==='dnssec')view.innerHTML=`<section class="wcp-panel"><div class="wcp-panel-head"><div class="wcp-panel-title">DNSSEC ownership and validation</div>${button('dnssec','Simulate validation')}</div><div class="protection-panel-body"><dl><div><dt>ROOT policy</dt><dd>${esc(current.dnssec)}</dd></div><div><dt>Zone signing metadata</dt><dd>${esc(rootState?.zones?.find(item=>item.name===zone)?.dnssec||'Managed by ROOT')}</dd></div><div><dt>Account check</dt><dd>${esc(state.securityCheck?.status||'No local check yet')}</dd></div></dl><p>Signing keys, DS publication and rollover remain ROOT-managed. This view performs no cryptographic or registrar validation.</p></div></section>`;
    if(active==='propagation'){view.innerHTML=`<div class="protection-section-head"><div><h3>Simulated resolver observations</h3><p>Results capture the published revision at request time.</p></div>${button('propagation','Run demo check')}</div><div data-dns-observations></div>`;grid=UI.DataGrid.mount(view.querySelector('[data-dns-observations]'),{id:'dns-observations',title:'Local propagation simulation',rows:state.observations,columns:[{key:'location',label:'Location'},{key:'answer',label:'Captured answer'},{key:'revision',label:'Revision',type:'number'},{key:'status',label:'Result'},{key:'checkedAt',label:'Checked',type:'date'}]});}
    if(active==='activity')grid=UI.DataGrid.mount(view,{id:'dns-activity',title:'Account DNS jobs',rows:state.operations,columns:[{key:'name',label:'Operation'},{key:'status',label:'Job status'},{key:'result',label:'Local result'},{key:'revision',label:'Revision',type:'number'},{key:'message',label:'Review detail',hidden:true},{key:'createdAt',label:'Requested',type:'date'}]});
  }
  function setTab(id){if(!Object.hasOwn(tabs,id))return;active=id;if(location.hash.slice(1)!==id)location.hash=id;render();}
  const actions={add:()=>edit(),import:importRecords,publish,discard,refresh:render,dnssec:()=>check('dnssec'),propagation:()=>check('propagation')};
  host.addEventListener('click',event=>{const tab=event.target.closest('[data-account-dns-tab]');if(tab){setTab(tab.dataset.accountDnsTab);return;}const control=event.target.closest('[data-account-dns-action]');if(control&&actions[control.dataset.accountDnsAction])P.safe(actions[control.dataset.accountDnsAction])();});
  host.addEventListener('keydown',event=>{const tab=event.target.closest('[data-account-dns-tab]');if(!tab||!['ArrowLeft','ArrowRight','Home','End'].includes(event.key))return;event.preventDefault();const ids=Object.keys(tabs),index=ids.indexOf(active),next=event.key==='Home'?0:event.key==='End'?ids.length-1:(index+(event.key==='ArrowRight'?1:-1)+ids.length)%ids.length;setTab(ids[next]);host.querySelector('#dns-tab-'+ids[next])?.focus();});
  window.addEventListener('hashchange',()=>{const id=location.hash.slice(1);if(Object.hasOwn(tabs,id)&&active!==id){active=id;render();}});
  document.addEventListener('wcp:runtime-change',()=>{if(sync())render();});
  render();const query=new URLSearchParams(location.search),requested=query.get('action');
  if(['add','edit'].includes(requested)){P.safe(()=>{if(requested==='add')edit();else{const record=state.records.find(item=>item.id===query.get('id'));if(!record)throw new Error('Choose an existing record from the draft grid to edit it.');edit(record);}})();const clean=new URL(location.href);clean.searchParams.delete('action');history.replaceState(null,'',clean.pathname+clean.search+clean.hash);}
})();
