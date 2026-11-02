(() => {
  'use strict';
  if(window.WCPRuntime)return;
  const scope=document.body.dataset.accessScope;
  if(!['user','reseller','root'].includes(scope))return;
  const key='wcp360-runtime-v2',now=Date.now();
  const defaults={jobs:[
    {id:'JOB-94821',name:'Deploy example.com',scope:'user',kind:'deployment',createdAt:now-250000,progress:100,status:'completed',actor:'Yann'},
    {id:'JOB-94837',name:'Backup api.example.com',scope:'user',kind:'backup',createdAt:now-72000,progress:68,status:'running',actor:'System'},
    {id:'JOB-ROOT-81',name:'Health check EU-PROD services',scope:'root',kind:'health',createdAt:now-96000,progress:82,status:'running',actor:'Yann · ROOT'}
  ],notifications:[]};
  const uid=prefix=>`${prefix}-${crypto.randomUUID?.()||Date.now()+'-'+Math.random().toString(16).slice(2)}`;
  const escape=value=>String(value??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  const normalize=value=>window.WCPUI?.normalize?window.WCPUI.normalize(value):String(value??'').normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase();
  const statuses=['running','scheduled','completed','failed','cancelled'];
  let state=structuredClone(defaults),filter=statuses.includes(location.hash.slice(1))?location.hash.slice(1):'all',search='',returnFocus;
  function read(){
    try{const saved=JSON.parse(localStorage.getItem(key));if(saved&&typeof saved==='object'&&Array.isArray(saved.jobs)&&Array.isArray(saved.notifications))state=saved;}catch{}
    const ids=new Set();
    state.jobs=state.jobs.filter(job=>{if(!job||typeof job!=='object'||!['root','user','reseller'].includes(job.scope)||!job.id||ids.has(String(job.id)))return false;ids.add(String(job.id));return true;});
    state.notifications=state.notifications.filter(note=>note&&typeof note==='object');
    state.jobs.forEach(job=>{job.id=String(job.id);job.correlation||=uid('COR');job.reversible=job.reversible!==false;job.status=statuses.includes(job.status)?job.status:'failed';job.duration=Number.isFinite(Number(job.duration))&&Number(job.duration)>0?Number(job.duration):36000;job.progress=job.status==='completed'?100:job.status==='scheduled'?0:Math.max(0,Math.min(100,Number(job.progress)||0));job.createdAt=Number.isFinite(Number(job.createdAt))?Number(job.createdAt):Date.now();job.startedAt=Number.isFinite(Number(job.startedAt))?Number(job.startedAt):Date.now()-job.progress/100*job.duration;});
  }
  function save(){state.notifications=state.notifications.slice(0,100);try{localStorage.setItem(key,JSON.stringify(state));}catch{}}
  const scoped=()=>state.jobs.filter(job=>job.scope===scope);
  const audit=scope==='root'?'audit-explorer.html':scope==='reseller'?'reseller.html#audit':'security-audit.html';
  function announce(title,detail=''){return window.WCPUI.toast({title,message:detail})}
  function note(title,detail=''){state.notifications.unshift({id:uid('NTF'),title,detail,scope,createdAt:Date.now(),read:false});}
  function addJob(detail={}){
    read();const scheduled=detail.status==='scheduled';
    const job={id:uid('JOB'),name:detail.name||'WCP360 operation',scope,kind:detail.kind||'operation',createdAt:Date.now(),startedAt:Date.now(),duration:36000,progress:scheduled?0:4,status:scheduled?'scheduled':'running',actor:detail.actor||'Demo operator',correlation:uid('COR'),reversible:detail.reversible!==false,reason:detail.reason||''};
    state.jobs.unshift(job);note(`${job.name} ${scheduled?'scheduled':'started'}`,job.id);save();render();announce(scheduled?'Operation scheduled':'Operation started',`${job.name} · Job Center`);return structuredClone(job);
  }
  function notify(title,detail=''){read();note(title,detail);save();render();announce(title,detail);}
  const labels={running:'Running',scheduled:'Scheduled',completed:'Completed',failed:'Failed',cancelled:'Cancelled'};
  const stage=job=>job.status==='scheduled'?'Waiting for manual start':job.status==='completed'?'Verified':job.status==='failed'?'Needs review':job.status==='cancelled'?'Cancelled safely':job.progress<15?'Validating permissions':job.progress<35?'Creating recovery point':job.progress<82?'Executing change':'Running health checks';
  function jobMarkup(job){
    return `<article class="runtime-job" data-runtime-job="${escape(job.id)}"><div class="runtime-job-top"><div><div class="runtime-job-name">${escape(job.name)}</div><div class="runtime-job-meta">${escape(job.id)} · ${escape(job.actor)}</div></div><span class="wcp-status ${job.status==='completed'?'success':job.status==='running'?'processing':''}">${escape(labels[job.status]||job.status)}</span></div><div class="runtime-stage"><span class="material-symbols-rounded">${job.status==='completed'?'task_alt':'pending_actions'}</span><strong>${stage(job)}</strong><span>${escape(job.correlation)}</span></div><div class="runtime-progress" role="progressbar" aria-label="${escape(job.name)}" aria-valuenow="${job.progress}" aria-valuemin="0" aria-valuemax="100"><i style="width:${job.progress}%"></i></div><div class="runtime-job-foot"><span>${escape(job.kind)} · ${new Date(job.createdAt).toLocaleString()}</span><span>${job.progress}%</span></div><div class="runtime-job-actions"><span><span class="material-symbols-rounded">${job.reversible?'undo':'lock'}</span>${job.reversible?'Recovery state retained':'Non-reversible'}</span><div>${job.status==='scheduled'?`<button type="button" data-runtime-start="${escape(job.id)}">Start now</button>`:''}${['running','scheduled'].includes(job.status)?`<button type="button" data-runtime-cancel="${escape(job.id)}">Cancel safely</button>`:''}${scope==='root'?`<a href="job-detail.html?id=${encodeURIComponent(job.id)}">Details</a>`:''}<a href="${audit}">Audit trail</a></div></div></article>`;
  }
  const filters=()=>`<div class="runtime-job-controls" role="group" aria-label="Filter jobs">${['all',...statuses].map(value=>`<button class="runtime-job-filter" type="button" data-runtime-filter="${value}" aria-pressed="${filter===value}">${value==='all'?'All':labels[value]}</button>`).join('')}</div>`;
  const shell=document.createElement('div');
  shell.innerHTML=`<button class="runtime-backdrop" type="button" aria-label="Close Job Center" hidden></button><aside class="runtime-job-center" role="dialog" aria-modal="true" aria-hidden="true" aria-labelledby="runtimeJobTitle" inert><div class="runtime-job-head"><div><div class="runtime-job-title" id="runtimeJobTitle">Job Center</div><div class="runtime-job-sub">Local simulation · ${scope.toUpperCase()}</div></div><button class="root-drawer-close" type="button" data-runtime-close aria-label="Close"><span class="material-symbols-rounded">close</span></button></div>${filters()}<div class="runtime-job-list"></div></aside>`;
  document.body.append(...shell.children);
  const panel=document.querySelector('.runtime-job-center'),backdrop=document.querySelector('.runtime-backdrop');
  document.querySelectorAll('#wcpJobsDrawer').forEach(drawer=>drawer.remove());
  document.querySelectorAll('[data-open-drawer="wcpJobsDrawer"],button[aria-label*="Job Center"],button[aria-label*="Installation jobs"]').forEach(button=>{button.removeAttribute('data-open-drawer');button.dataset.runtimeJobs='';button.setAttribute('aria-expanded','false');});
  function renderNotifications(){
    const notes=state.notifications.filter(item=>item.scope===scope),popover=document.querySelector('#wcpNotifications');
    const markup=notes.slice(0,15).map(item=>`<article class="runtime-notification"><strong>${escape(item.title)}</strong><small>${escape(item.detail)} · ${new Date(item.createdAt).toLocaleString()}</small></article>`).join('')||'<p class="runtime-job-empty">No notifications yet.</p>';
    if(popover)popover.innerHTML=`<div class="wcp-popover-title">Notifications · ${scope.toUpperCase()}</div>${markup}<div class="runtime-notification-actions"><button type="button" class="wcp-action-btn" data-runtime-read>Mark all as read</button><button type="button" class="wcp-action-btn" data-runtime-jobs aria-expanded="${opened}">Job Center</button></div>`;
    document.querySelectorAll('[data-toggle-notifications]').forEach(button=>{const unread=notes.filter(item=>!item.read).length;button.setAttribute('aria-label',`Notifications, ${unread} unread`);const badge=button.querySelector('.wcp-notification-badge');if(badge){badge.textContent=String(unread);badge.hidden=!unread;}});
    const feed=document.querySelector('[data-runtime-notifications]');if(feed)feed.innerHTML=markup;
  }
  function preserveFocus(){
    const focused=document.activeElement;
    const root=[panel.querySelector('.runtime-job-list'),document.querySelector('[data-runtime-page]'),document.querySelector('[data-runtime-detail]'),document.querySelector('#wcpNotifications'),document.querySelector('[data-runtime-notifications]')].find(item=>item?.contains?.(focused));
    if(!root)return ()=>{};
    const attrs=['data-runtime-start','data-runtime-cancel','data-runtime-read','data-runtime-jobs','href'].filter(name=>focused?.hasAttribute?.(name)).map(name=>[name,focused.getAttribute(name)]);
    const jobId=focused.closest?.('[data-runtime-job]')?.getAttribute('data-runtime-job');
    return ()=>{
      if(focused.isConnected)return;
      const candidates=[...(root.querySelectorAll?.('button,a[href],input')||[])];
      const replacement=attrs.length?candidates.find(item=>attrs.every(([name,value])=>item.getAttribute(name)===value)&&(!jobId||item.closest?.('[data-runtime-job]')?.getAttribute('data-runtime-job')===jobId)):null;
      if(replacement){replacement.focus({preventScroll:true});return;}
      if(root===panel.querySelector('.runtime-job-list'))panel.querySelector('[data-runtime-close]').focus({preventScroll:true});
      else{root.setAttribute('tabindex','-1');root.focus({preventScroll:true});}
    };
  }
  function render(){
    const restoreFocus=preserveFocus();
    const jobs=scoped().filter(job=>(filter==='all'||job.status===filter)&&(!search||normalize([job.name,job.id,job.kind].join(' ')).includes(search)));
    const markup=jobs.map(jobMarkup).join('')||'<div class="runtime-job-empty"><span class="material-symbols-rounded">task_alt</span>No jobs in this view.</div>';
    const jobList=panel.querySelector('.runtime-job-list'),scroll=jobList.scrollTop;
    jobList.innerHTML=markup;jobList.scrollTop=scroll;
    const inline=document.querySelector('[data-runtime-page]');if(inline)inline.innerHTML=markup;
    const detail=document.querySelector('[data-runtime-detail]');if(detail){const id=new URLSearchParams(location.search).get('id'),job=scoped().find(item=>item.id===id);detail.innerHTML=job?jobMarkup(job):'<div class="runtime-job-empty">Job not found in this portal. <a href="jobs.html">Return to jobs</a></div>';}
    document.querySelectorAll('[data-runtime-filter]').forEach(button=>{const active=button.dataset.runtimeFilter===filter;button.classList.toggle('active',active);button.setAttribute('aria-pressed',String(active));});
    const running=scoped().filter(job=>job.status==='running').length;
    document.querySelectorAll('[data-runtime-jobs]').forEach(button=>{let badge=button.querySelector('.runtime-job-badge');if(!badge){badge=document.createElement('span');badge.className='runtime-job-badge';button.appendChild(badge);}badge.textContent=String(running);badge.hidden=!running;});
    document.querySelectorAll('[data-runtime-summary]').forEach(item=>item.textContent=`${scoped().length} jobs · ${running} running · ${scoped().filter(job=>job.status==='scheduled').length} scheduled`);
    renderNotifications();restoreFocus();document.dispatchEvent(new CustomEvent('wcp:runtime-change',{detail:{scope}}));
  }
  const backgroundState=new Map();let opened=false,oldOverflow;
  function open(){
    if(opened){panel.querySelector('[data-runtime-close]').focus();return;}
    const trigger=document.activeElement;document.dispatchEvent(new CustomEvent('wcp:overlay-opening',{detail:{source:'runtime'}}));
    opened=true;returnFocus=trigger?.isConnected&&!trigger.closest?.('[inert]')?trigger:document.activeElement;document.querySelectorAll('.wcp-popover.open').forEach(popover=>{popover.classList.remove('open');popover.setAttribute('aria-hidden','true');});document.querySelectorAll('[data-toggle-notifications],[data-toggle-profile]').forEach(button=>button.setAttribute('aria-expanded','false'));render();
    for(const child of document.body.children||[]){if(child===panel||child===backdrop||['SCRIPT','STYLE','LINK'].includes(child.tagName))continue;backgroundState.set(child,child.inert);child.inert=true;}
    if(document.body.style){oldOverflow=document.body.style.overflow;document.body.style.overflow='hidden';}
    panel.inert=false;panel.classList.add('open');backdrop.hidden=false;backdrop.classList.add('open');panel.setAttribute('aria-hidden','false');document.querySelectorAll('[data-runtime-jobs]').forEach(button=>button.setAttribute('aria-expanded','true'));panel.querySelector('[data-runtime-close]').focus();
  }
  function close(){
    if(!opened)return;opened=false;panel.classList.remove('open');panel.setAttribute('aria-hidden','true');panel.inert=true;backdrop.classList.remove('open');backdrop.hidden=true;
    for(const [element,inert] of backgroundState)element.inert=inert;backgroundState.clear();if(document.body.style)document.body.style.overflow=oldOverflow;
    document.querySelectorAll('[data-runtime-jobs]').forEach(button=>button.setAttribute('aria-expanded','false'));if(returnFocus?.isConnected&&!returnFocus.closest?.('[inert]'))returnFocus.focus?.();
  }
  function changeJob(id,action){
    read();const job=state.jobs.find(item=>item.id===id&&item.scope===scope);if(!job)return;
    if(action==='cancel'&&['running','scheduled'].includes(job.status)){job.status='cancelled';note(`${job.name} cancelled safely`,job.id);}
    else if(action==='start'&&job.status==='scheduled'){job.status='running';job.startedAt=Date.now();job.progress=4;note(`${job.name} started`,job.id);}else return;
    save();render();
  }
  function tick(){
    read();let changed=false;
    scoped().forEach(job=>{if(job.status!=='running')return;const progress=Math.max(job.progress,Math.min(100,Math.floor((Date.now()-job.startedAt)/job.duration*100)));if(progress===job.progress&&progress!==100)return;job.progress=progress;changed=true;if(progress===100){job.status='completed';job.completedAt=Date.now();note(`${job.name} completed`,job.id);}});
    if(changed){save();render();}
  }
  document.addEventListener('click',event=>{
    if(event.target.closest('[data-runtime-jobs]')){event.preventDefault();open();return;}
    if(event.target.closest('[data-runtime-close]')||event.target===backdrop){close();return;}
    const control=event.target.closest('[data-runtime-filter]');if(control){filter=control.dataset.runtimeFilter;render();return;}
    const cancel=event.target.closest('[data-runtime-cancel]');if(cancel){changeJob(cancel.dataset.runtimeCancel,'cancel');return;}
    const start=event.target.closest('[data-runtime-start]');if(start){changeJob(start.dataset.runtimeStart,'start');return;}
    if(event.target.closest('[data-runtime-read]')){read();state.notifications.filter(item=>item.scope===scope).forEach(item=>item.read=true);save();render();}
  });
  document.addEventListener('input',event=>{if(event.target.matches('[data-runtime-search]')){search=normalize(event.target.value.trim());render();}});
  document.addEventListener('keydown',event=>{
    if(!panel.classList.contains('open'))return;
    if(event.key==='Escape'){event.preventDefault();close();}
    if(event.key==='Tab'){const focusable=[...panel.querySelectorAll('button,a[href],input,select,textarea,[tabindex]')].filter(item=>!item.disabled&&item.tabIndex>=0&&item.offsetParent!==null),first=focusable[0],last=focusable.at(-1);if(!first){event.preventDefault();return;}if(event.shiftKey&&(document.activeElement===first||!panel.contains(document.activeElement))){event.preventDefault();last.focus();}else if(!event.shiftKey&&(document.activeElement===last||!panel.contains(document.activeElement))){event.preventDefault();first.focus();}}
  });
  document.addEventListener('wcp:operation',event=>addJob(event.detail||{}));
  document.addEventListener('wcp:overlay-opening',event=>{if(opened&&event.detail?.source!=='runtime')close();});
  document.addEventListener('wcp:notification',event=>notify(event.detail?.title||'Notification',event.detail?.detail||''));
  window.addEventListener('storage',event=>{if(event.key===key){read();render();}});
  read();save();
  window.WCPRuntime={addJob,notify,getState:()=>{read();return structuredClone(state);},openJobs:open,cancelJob:id=>changeJob(id,'cancel'),startJob:id=>changeJob(id,'start')};
  tick();render();setInterval(tick,1000);document.dispatchEvent(new CustomEvent('wcp:runtime-ready'));
})();
