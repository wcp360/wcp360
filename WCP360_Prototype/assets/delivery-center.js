(()=>{
  'use strict';
  const host=document.querySelector('#deliveryCenter');if(!host)return;
  const UI=window.WCPUI,P=window.WCPPrototypeOperations,S=window.WCPDeliverySchemas,owner=S.owner,key='wcp360-account-delivery-v1',esc=UI.escape;
  const defaults={repositories:[{id:'site',owner,name:'example-site',remote:'https://github.com/example/site.git',branch:'main',profile:'Static website',protected:true,commit:'c3d4e5f',revision:1,archived:false}],releases:[{id:'r185',owner,label:'r185',repoId:'site',branch:'main',profile:'Static website',commit:'a1b2c3d',createdAt:'2026-10-28T10:00:00Z',status:'Ready'},{id:'r186',owner,label:'r186',repoId:'site',branch:'main',profile:'Static website',commit:'b2c3d4e',createdAt:'2026-10-29T10:00:00Z',status:'Ready'}],targets:{staging:{releaseId:'r186',generation:1,health:'Pass'},production:{releaseId:'r186',generation:2,health:'Pass'}},history:[{id:'seed185',target:'production',releaseId:'r185',health:'Pass',kind:'deploy',createdAt:'2026-10-28T12:00:00Z'},{id:'seed186',target:'production',releaseId:'r186',health:'Pass',kind:'deploy',createdAt:'2026-10-29T12:00:00Z'}],operations:[]};
  const state=P.load(key,defaults,['repositories','releases','history','operations']);
  const normalize=()=>{state.targets=Object.fromEntries(['staging','production'].map(target=>[target,{...defaults.targets[target],...state.targets?.[target]}]));};normalize();
  let lastSeen;try{lastSeen=localStorage.getItem(key);}catch{}
  const repoPage=location.pathname.endsWith('git-repositories.html'),tabs=repoPage?{repositories:'Repositories',activity:'Activity'}:{environments:'Environments',builds:'Builds',releases:'Releases',activity:'Activity'};
  let active=Object.hasOwn(tabs,location.hash.slice(1))?location.hash.slice(1):Object.keys(tabs)[0],grid=null;
  const enabled=()=>window.WCPNavigation?.available('deployments.html')!==false;
  const guard=()=>{if(!enabled())throw new Error('The Developer Pipelines extension is disabled by ROOT.');};
  const save=()=>{P.save(key,state);try{lastSeen=localStorage.getItem(key);}catch{}},repository=id=>state.repositories.find(item=>item.id===id&&item.owner===owner),release=id=>state.releases.find(item=>item.id===id&&item.owner===owner);
  function refresh(){let raw;try{raw=localStorage.getItem(key);}catch{return false;}if(raw===lastSeen)return false;lastSeen=raw;Object.assign(state,P.load(key,defaults,['repositories','releases','history','operations']));normalize();return true;}
  const busyRepo=id=>state.operations.some(item=>item.repoId===id&&P.pending(item));
  function sync(){
    let changed=refresh();const jobs=P.jobs();
    for(const operation of state.operations){
      const job=jobs.find(item=>item.id===operation.jobId&&item.scope==='user');if(!job||operation.applied)continue;
      if(operation.status!==job.status||operation.progress!==job.progress){operation.status=job.status;operation.progress=job.progress;changed=true;}
      if(!['completed','failed','cancelled'].includes(job.status))continue;
      operation.applied=true;changed=true;
      if(job.status!=='completed'){operation.result='No changes applied ('+job.status+')';continue;}
      if(!enabled()){operation.result='Blocked: extension disabled';continue;}
      const payload=operation.payload,current=repository(operation.repoId);
      if(['source','build'].includes(operation.kind)){
        if(!current||current.archived||current.revision!==operation.revision){operation.result='Blocked: repository configuration changed';continue;}
        if(operation.kind==='source'){current.commit=payload.commit;current.revision++;operation.result='Demo source revision updated';}
        else if(payload.outcome==='Fail')operation.result='Simulated build failed; no release created';
        else{const id='REL-'+operation.id;if(!release(id))state.releases.unshift({id,owner,label:payload.label,repoId:current.id,branch:payload.branch,commit:payload.commit,profile:payload.profile,status:'Ready',createdAt:new Date(job.completedAt||Date.now()).toISOString()});operation.result='Immutable demo release created';}
      }else if(['deploy','rollback'].includes(operation.kind)){
        const target=state.targets[operation.target],artifact=release(payload.releaseId);
        // Exclude this completed operation from pending locks; recheck the exact source and environment snapshot.
        const issue=S.deploymentIssue(state,artifact,operation.target,{rollback:operation.kind==='rollback'});
        if(issue||target.generation!==operation.generation||operation.target==='production'&&operation.kind!=='rollback'&&state.targets.staging.generation!==operation.stagingGeneration){operation.result='Blocked: '+(issue||'environment changed since review');continue;}
        if(payload.health!=='Pass'){operation.result='Health check failed; active version preserved';continue;}
        target.releaseId=artifact.id;target.generation++;target.health='Pass';
        state.history.unshift({id:operation.id,target:operation.target,releaseId:artifact.id,health:'Pass',kind:operation.kind,reason:payload.reason,createdAt:new Date(job.completedAt||Date.now()).toISOString()});operation.result='Demo '+operation.target+' version activated';
      }
    }
    if(changed)save();return changed;
  }
  function enqueue(kind,name,payload,{repo,target}={}){
    guard();const job=P.job({name,kind:'delivery-'+kind,scope:'user'});
    state.operations.unshift({id:P.uid(),jobId:job.id,kind,name,payload:structuredClone(payload),repoId:repo?.id,revision:repo?.revision,target,generation:state.targets[target]?.generation,stagingGeneration:state.targets.staging.generation,status:job.status,progress:job.progress,createdAt:new Date().toISOString(),applied:false});save();render();
  }
  function repoCheck(id){guard();sync();const current=repository(id);if(!current||current.archived)throw new Error('This repository is archived or unavailable.');if(busyRepo(id))throw new Error('A source or build job is already pending for this repository.');return current;}
  function editRepo(record){
    guard();sync();const current=record?repoCheck(record.id):null,revision=current?.revision;
    const fields=()=>S.repoFields({repositories:state.repositories,current});
    P.form({title:current?'Repository settings':'Add repository metadata',description:'Save a source URL, branch and build profile for this account. Remote access and credentials are not required for the demo.',fields:fields(),onSubmit:values=>{
      guard();sync();P.validate(fields(),values);
      if(current){const latest=repoCheck(current.id);if(latest.revision!==revision)throw new Error('Repository settings changed. Reopen the form.');Object.assign(latest,values,{revision:latest.revision+1,commit:latest.branch===values.branch?latest.commit:''});}
      else state.repositories.push({id:P.uid(),owner,...values,commit:'',revision:1,archived:false});save();render();
    }});
  }
  function source(record){
    const current=repoCheck(record.id),revision=current.revision;
    const fields=S.sourceFields(current);
    P.form({title:'Set demo source revision',description:'Enter a revision to simulate a source update. No fetch or clone is performed.',fields,submitLabel:'Queue source simulation',onSubmit:values=>{const latest=repoCheck(record.id);if(latest.revision!==revision)throw new Error('Repository settings changed. Reopen the form.');P.validate(S.sourceFields(latest),values);enqueue('source','Update source: '+latest.name,{commit:values.commit.toLowerCase()},{repo:latest});}});
  }
  function build(record){
    const current=repoCheck(record.id),revision=current.revision;if(!S.commit(current.commit))throw new Error('Set a demo source revision before building.');
    const fields=()=>S.buildFields(state);
    P.form({title:'Build '+current.name,description:'Snapshot: '+current.branch+' / '+current.commit+' / '+current.profile+'. The selected profile simulates an artifact without running commands.',fields:fields(),submitLabel:'Queue build simulation',onSubmit:values=>{const latest=repoCheck(record.id);if(latest.revision!==revision)throw new Error('Source or settings changed. Reopen the build form.');P.validate(fields(),values);enqueue('build','Build '+values.label,{...values,branch:latest.branch,commit:latest.commit,profile:latest.profile},{repo:latest});}});
  }
  async function archive(record){
    const check=()=>{const current=repoCheck(record.id);if(Object.values(state.targets).some(target=>release(target.releaseId)?.repoId===current.id))throw new Error('This repository supplies an active environment and cannot be archived.');return current;};
    const current=check();if(!await UI.Dialog.confirm({title:'Archive repository metadata',message:'Retain releases and activity for '+current.name+'.',typed:current.name}))return;check().archived=true;save();render();
  }
  async function restore(record){guard();sync();const current=repository(record.id);if(!current?.archived)throw new Error('This repository is not archived.');current.archived=false;current.revision++;save();render();}
  function deploy(record,target,rollback=false){
    const check=()=>{guard();sync();const artifact=release(record.id),issue=S.deploymentIssue(state,artifact,target,{rollback});if(issue)throw new Error(issue);return artifact;};
    const artifact=check(),generation=state.targets[target].generation,stagingGeneration=state.targets.staging.generation;
    const fields=S.deployFields({target,release:artifact,rollback});
    P.form({title:(rollback?'Roll back ':target==='production'?'Promote to ':'Deploy to ')+target,description:artifact.label+' / '+artifact.commit+' → '+target+' ('+(target==='production'?owner:'staging.'+owner)+'). Active: '+(release(state.targets[target].releaseId)?.label||'None')+'. Only a passing simulated health check changes the active version.',fields,submitLabel:'Queue '+(rollback?'rollback':'deployment')+' simulation',onSubmit:values=>{
      check();P.validate(fields,values);if(state.targets[target].generation!==generation||target==='production'&&!rollback&&state.targets.staging.generation!==stagingGeneration)throw new Error('The environment changed. Reopen the review.');
      enqueue(rollback?'rollback':'deploy',(rollback?'Roll back ':'Deploy ')+artifact.label+' to '+target,{releaseId:artifact.id,reason:values.reason.trim(),health:values.health},{target});
    }});
  }
  function inspect(record){
    const artifact=release(record.id);if(!artifact)throw new Error('The release is unavailable.');const content=document.createElement('div');content.className='protection-preview';
    content.innerHTML=`<dl><dt>Release</dt><dd>${esc(artifact.label)}</dd><dt>Source revision</dt><dd>${esc(artifact.commit)}</dd><dt>Branch snapshot</dt><dd>${esc(artifact.branch)}</dd><dt>Build profile</dt><dd>${esc(artifact.profile)}</dd><dt>Created</dt><dd>${esc(artifact.createdAt)}</dd><dt>Artifact ID</dt><dd>${esc(artifact.id)}</dd></dl><p>Immutable local metadata. No executable artifact or filesystem release directory is created.</p>`;UI.Dialog.open({title:'Release details',content});
  }
  const button=(id,label,primary=false)=>`<button type="button" class="wcp-action-btn ${primary?'primary':''}" data-delivery-action="${id}">${label}</button>`;
  function render(){
    sync();grid?.destroy();grid=null;
    if(!enabled()){host.innerHTML='<div class="wcp-callout warning"><div class="wcp-callout-title">Developer Pipelines disabled</div><p>ROOT has disabled this extension. Stored account metadata is retained.</p></div>';return;}
    host.classList.add('protection-page');host.innerHTML=`<div class="wcp-page-head"><div><div class="wcp-page-eyebrow">USER / ${owner} / Developer tools</div><h2 class="wcp-page-heading">${repoPage?'Git repositories':'Deployments'}</h2><p class="wcp-page-sub">Source metadata, builds and reviewed releases with persistent demo state.</p></div><div class="wcp-page-actions">${repoPage?button('create','Add repository',true):'<a class="wcp-action-btn primary" href="git-repositories.html">Manage sources</a>'}${button('jobs','Job Center')}</div></div><p class="protection-note">Local simulation: no repository is contacted, no build command runs and no website files are changed.</p><nav class="wcp-tabs protection-tabs" role="tablist" aria-label="Delivery sections">${Object.entries(tabs).map(([id,label])=>`<button type="button" class="wcp-tab ${id===active?'active':''}" role="tab" id="delivery-tab-${id}" aria-controls="delivery-panel" aria-selected="${id===active}" tabindex="${id===active?0:-1}" data-delivery-tab="${id}">${label}</button>`).join('')}</nav><section id="delivery-panel" role="tabpanel" aria-labelledby="delivery-tab-${active}" tabindex="0"><div data-delivery-view></div></section><p class="protection-note">${repoPage?'<a href="deployments.html#environments">Open environments and releases</a>':'<a href="git-repositories.html">Repositories & branch policy</a>'} · <a href="websites.html">Websites</a></p>`;
    const view=host.querySelector('[data-delivery-view]');
    if(active==='repositories')grid=UI.DataGrid.mount(view,{id:'delivery-repositories',title:'Account source repositories',rows:state.repositories.filter(item=>item.owner===owner).map(item=>({...item,status:item.archived?'Archived':busyRepo(item.id)?'Job pending':item.commit?'Ready':'Revision needed',policy:item.protected?'Production allowed':'Staging only'})),store:UI.createStore({scope:'user',owner,module:'delivery-repositories'}),columns:[{key:'name',label:'Repository'},{key:'remote',label:'Remote metadata'},{key:'branch',label:'Branch'},{key:'commit',label:'Demo revision'},{key:'profile',label:'Profile'},{key:'policy',label:'Branch policy'},{key:'status',label:'State'}],rowActions:[{label:'Settings',when:item=>!item.archived,run:P.safe(editRepo)},{label:'Set source revision',when:item=>!item.archived,run:P.safe(source)},{label:'Build release',when:item=>!item.archived,run:P.safe(build)},{label:'Archive',when:item=>!item.archived,run:P.safe(archive)},{label:'Restore repository',when:item=>item.archived,run:P.safe(restore)}]});
    if(active==='environments')grid=UI.DataGrid.mount(view,{id:'delivery-environments',title:'Active account environments',rows:Object.entries(state.targets).map(([id,target])=>({id,...target,hostname:id==='production'?owner:'staging.'+owner,label:release(target.releaseId)?.label||'None',commit:release(target.releaseId)?.commit||'',status:state.operations.some(item=>item.target===id&&P.pending(item))?'Job pending':'Active (demo)'})),columns:[{key:'id',label:'Environment'},{key:'hostname',label:'Hostname'},{key:'label',label:'Active release'},{key:'commit',label:'Revision'},{key:'generation',label:'Activation version',type:'number'},{key:'health',label:'Simulated health'},{key:'status',label:'State'}]});
    if(active==='builds')grid=UI.DataGrid.mount(view,{id:'delivery-builds',title:'Build simulations',rows:state.operations.filter(item=>item.kind==='build').map(item=>({...item,label:item.payload.label,commit:item.payload.commit,profile:item.payload.profile})),columns:[{key:'label',label:'Release label'},{key:'commit',label:'Revision'},{key:'profile',label:'Build profile'},{key:'status',label:'Job status'},{key:'result',label:'Result'},{key:'createdAt',label:'Requested',type:'date'}]});
    if(active==='releases')grid=UI.DataGrid.mount(view,{id:'delivery-releases',title:'Retained immutable releases',rows:state.releases.filter(item=>item.owner===owner).map(item=>({...item,activeIn:Object.entries(state.targets).filter(([,target])=>target.releaseId===item.id).map(([id])=>id).join(', ')||'Retained',rollback:S.retained(state,item.id)?'Known-good production':'Not yet in production'})),store:UI.createStore({scope:'user',owner,module:'delivery-releases'}),columns:[{key:'label',label:'Release'},{key:'branch',label:'Branch snapshot'},{key:'commit',label:'Revision'},{key:'profile',label:'Profile'},{key:'activeIn',label:'Active in'},{key:'rollback',label:'Rollback eligibility'},{key:'createdAt',label:'Built',type:'date'}],rowActions:[{label:'Details',run:P.safe(inspect)},{label:'Deploy to staging',run:P.safe(item=>deploy(item,'staging'))},{label:'Promote to production',run:P.safe(item=>deploy(item,'production'))},{label:'Roll back production',when:item=>S.retained(state,item.id)&&state.targets.production.releaseId!==item.id,run:P.safe(item=>deploy(item,'production',true))}]});
    if(active==='activity')grid=UI.DataGrid.mount(view,{id:'delivery-activity',title:'Delivery jobs and outcomes',rows:state.operations.map(item=>({...item,reason:item.payload.reason||'',commit:item.payload.commit||release(item.payload.releaseId)?.commit||''})),columns:[{key:'name',label:'Operation'},{key:'status',label:'Job status'},{key:'result',label:'Local result'},{key:'commit',label:'Revision'},{key:'reason',label:'Change note'},{key:'createdAt',label:'Requested',type:'date'},{key:'jobId',label:'Job ID',hidden:true}]});
  }
  function setTab(id){if(!Object.hasOwn(tabs,id))return;active=id;if(location.hash.slice(1)!==id)location.hash=id;render();}
  host.addEventListener('click',event=>{const tab=event.target.closest('[data-delivery-tab]');if(tab){setTab(tab.dataset.deliveryTab);return;}const control=event.target.closest('[data-delivery-action]'),actions={create:()=>editRepo(),jobs:()=>window.WCPRuntime?.openJobs()};if(control&&actions[control.dataset.deliveryAction])P.safe(actions[control.dataset.deliveryAction])();});
  host.addEventListener('keydown',event=>{const tab=event.target.closest('[data-delivery-tab]');if(!tab||!['ArrowLeft','ArrowRight','Home','End'].includes(event.key))return;event.preventDefault();const ids=Object.keys(tabs),index=ids.indexOf(active),next=event.key==='Home'?0:event.key==='End'?ids.length-1:(index+(event.key==='ArrowRight'?1:-1)+ids.length)%ids.length;setTab(ids[next]);host.querySelector('#delivery-tab-'+ids[next])?.focus();});
  window.addEventListener('hashchange',()=>{const id=location.hash.slice(1);if(Object.hasOwn(tabs,id)&&id!==active){active=id;render();}});
  window.addEventListener('storage',event=>{if([key,'wcp360-platform-software-v1'].includes(event.key))render();});
  document.addEventListener('wcp:runtime-change',()=>{if(sync())render();});
  document.addEventListener('wcp:runtime-ready',render);
  save();render();const query=new URLSearchParams(location.search),action=query.get('action');
  if(action==='rollback'){P.safe(()=>{guard();const current=release(query.get('id'));if(!current)throw new Error('Select a retained version in Releases to review a rollback.');deploy(current,'production',true);})();const clean=new URL(location.href);clean.searchParams.delete('action');history.replaceState(null,'',clean.pathname+clean.search+clean.hash);}
})();
