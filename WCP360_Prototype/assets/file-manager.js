/* Shared-grid controller. All filesystem changes affect browser metadata only. */
(()=>{
  'use strict';
  const host=document.querySelector('#fileManagerApp');if(!host)return;
  const UI=window.WCPUI,S=window.WCPWorkspaceSchemas.files,key='wcp360-file-manager-v2';
  const seeds=[['fld-assets','assets','folder',0,'0755',false],['fld-uploads','uploads','folder',0,'0755',false],['fld-wellknown','.well-known','folder',0,'0755',true],['file-index','index.php','php',1740,'0644',false],['file-robots','robots.txt','text',418,'0644',false],['file-htaccess','.htaccess','config',2355,'0644',true],['file-env','.env','secret',1126,'0600',true],['file-release','release-2026-10-28.zip','archive',15518925,'0644',false]];
  const bytes=value=>Number(value)<1024?Number(value)+' B':Number(value)<1048576?(Number(value)/1024).toFixed(1)+' KiB':(Number(value)/1048576).toFixed(1)+' MiB';
  const defaults={path:'/public',tab:'files',showHidden:true,items:seeds.map(([id,name,type,size,permissions,protectedResource])=>({id,name,type,path:'/public',size:type==='folder'?'—':bytes(size),bytes:size,permissions,owner:'www-data',group:'example',modified:'Demo seed',protected:protectedResource})),trash:[],transfers:[],keys:[],settings:{defaultFile:'0644',defaultFolder:'0755',encoding:'UTF-8',virusScan:true,snapshots:true}};
  let stored={};try{stored=JSON.parse(localStorage.getItem(key))||{};}catch{}
  const state={...defaults,...stored,settings:{...defaults.settings,...stored.settings}};
  for(const name of ['items','trash','transfers','keys'])if(!Array.isArray(state[name]))state[name]=defaults[name];
  if(!S.paths(state.items).includes(state.path))state.path='/public';
  const tabs=['files','transfers','access','trash','settings'];
  if(tabs.includes(location.hash.slice(1)))state.tab=location.hash.slice(1);
  if(!tabs.includes(state.tab))state.tab='files';
  let grid=null,dialog=null,sequence=0;
  const uid=()=>window.crypto?.randomUUID?.()||'file-'+Date.now()+'-'+(++sequence);
  const save=()=>{try{localStorage.setItem(key,JSON.stringify(state));return true;}catch{UI.notify({title:'File Manager',message:'Browser storage is unavailable. Changes last only for this page.',tone:'error'});return false;}};
  const escape=UI.escape;
  const notify=message=>UI.notify({title:'File Manager',message});
  const safe=run=>async(...args)=>{try{return await run(...args);}catch(error){UI.notify({title:'File operation blocked',message:error.message,tone:'error'});}};
  const job=(name,kind)=>window.WCPRuntime?.addJob({name,kind,scope:'user',actor:'Demo operator'})?.id||null;
  const button=(action,label)=>`<button type="button" class="wcp-action-btn" data-fm-action="${action}">${label}</button>`;
  host.className='fm-page';
  host.innerHTML=`<div class="wcp-page-head"><div><div class="wcp-page-eyebrow">USER · Scoped filesystem</div><h2 class="wcp-page-heading">File Manager</h2><p class="wcp-page-sub">Explore and organize simulated files inside this hosting account. File contents never leave your device.</p></div><div class="wcp-page-actions">${button('upload','Upload')}${button('new-folder','New folder')}${button('new-file','New file')}</div></div><div class="fm-metrics"><article><span>Simulated storage</span><strong data-fm-storage></strong><small>Metadata only · no server upload</small></article><article><span>Files & folders</span><strong data-fm-count></strong><small>In the current account</small></article><article><span>Trash resources</span><strong data-fm-trash-count></strong><small>Recoverable until manually purged</small></article><article><span>Protected resources</span><strong data-fm-protected></strong><small>Including read-only /backups</small></article></div><nav class="fm-tabs" aria-label="File Manager views">${tabs.map(name=>`<button type="button" data-fm-tab="${name}" aria-pressed="false">${{files:'Files',transfers:'Transfers',access:'SFTP / SSH',trash:'Trash',settings:'Settings'}[name]}</button>`).join('')}</nav><div class="fm-view" data-fm-view></div>`;
  const view=host.querySelector('[data-fm-view]');
  function openForm(title,description,fields,onSubmit){
    const form=document.createElement('form');
    dialog=UI.Dialog.open({title,description,content:form,onClose:()=>UI.Form.detach(form)});
    const current=dialog;
    UI.Form.populate(form,{fields,onCancel:()=>current.close(),onSubmit:async values=>{await onSubmit(values,form);current.close();save();render();}});
    return form;
  }
  function checkWritable(path=state.path){if(!S.writable(path,state.items))throw new Error('This folder is protected or no longer exists.');}
  function inspect(record){const content=document.createElement('div');content.className='fm-resource-details';content.innerHTML=`<div class="fm-inspector-head"><strong>${escape(record.name)}</strong></div><dl><div><dt>Full path</dt><dd>${escape(S.full(record))}</dd></div><div><dt>Type</dt><dd>${escape(record.type)}</dd></div><div><dt>Size</dt><dd>${escape(record.size)}</dd></div><div><dt>Owner / group</dt><dd>${escape(record.owner)} / ${escape(record.group)}</dd></div><div><dt>Permissions</dt><dd>${escape(record.permissions)}</dd></div></dl><p>Prototype metadata. This panel does not read, edit or download actual file contents.</p>`;UI.Dialog.open({title:'Resource details',content});}
  function navigate(path){if(!S.paths(state.items).includes(path))throw new Error('This folder no longer exists.');state.path=path;save();render();}
  async function remove(ids){
    const records=S.topmost(state.items,ids);if(!records.length)return;
    if(records.some(item=>!S.editable(item,state.items)))throw new Error('The selection contains protected resources or folders containing protected resources.');
    if(!await UI.Dialog.confirm({title:'Move to Trash',message:`Move ${records.length} selected resources and their descendants to Trash?`,confirmLabel:'Move to Trash',typed:'DELETE'}))return;
    const count=S.trash(state,ids,uid);save();render();notify(count+' resources moved to Trash.');
  }
  function relocate(ids,copy){
    const records=S.topmost(state.items,ids),fields=S.destinationFields({items:state.items,records});
    if(!records.length)throw new Error('Select a resource first.');
    openForm(copy?'Copy resources':'Move resources','Includes all descendants. Existing destination names are never overwritten.',fields,values=>{const count=S.relocate(state,ids,values.destination,copy,uid);notify(count+' resources '+(copy?'copied.':'moved.'));});
  }
  function archive(ids){
    checkWritable();const records=S.topmost(state.items,ids);if(!records.length)throw new Error('Select resources to archive.');
    if(records.some(item=>!S.editable(item,state.items)))throw new Error('Protected resources cannot be archived.');
    const name='archive-'+uid()+'.zip',size=state.items.filter(item=>records.some(record=>record.id===item.id||record.type==='folder'&&S.within(item.path,S.full(record)))).reduce((sum,item)=>sum+Number(item.bytes||0),0);
    state.items.push({id:uid(),name,path:state.path,type:'archive',bytes:size,size:bytes(size),permissions:state.settings.defaultFile,owner:'www-data',group:'example',modified:'just now',protected:false});save();render();notify('Simulated archive created. It contains metadata only.');
  }
  function rename(record){openForm('Rename resource',S.full(record),S.fields({items:state.items,path:record.path,record}),values=>S.rename(state,record.id,values.name));}
  function permissions(record){
    if(!S.editable(record,state.items))throw new Error('This resource is protected.');
    openForm('Edit permissions',S.full(record),[{name:'permissions',label:'Numeric mode',type:'select',required:true,options:record.type==='folder'?['0755','0700','0775']:['0644','0600','0664'],default:record.permissions},{name:'owner',label:'Owner',type:'select',required:true,options:['www-data','deploy'],default:record.owner}],values=>{const current=state.items.find(item=>item.id===record.id);if(!S.editable(current,state.items))throw new Error('This resource is protected or unavailable.');current.permissions=values.permissions;current.owner=values.owner;current.modified='just now';});
  }
  function renderFiles(){
    const rows=state.items.filter(item=>item.path===state.path&&(state.showHidden||!item.name.startsWith('.')));
    view.innerHTML=`<div class="fm-safety"><span class="material-symbols-rounded">shield</span><div><strong>Application root · /home/example/apps/web</strong><small>All actions modify local prototype metadata. Protected resources remain read-only.</small></div></div><div class="fm-toolbar"><strong>${escape(state.path)}</strong><div class="fm-toolbar-actions">${button('refresh','Refresh')}${button('toggle-hidden',state.showHidden?'Hide dotfiles':'Show dotfiles')}</div></div><div class="fm-layout fm-shared-layout"><aside class="fm-tree"><div class="fm-tree-title">Application folders</div>${S.paths(state.items).map(path=>`<button type="button" class="${state.path===path?'active':''}" data-fm-path="${escape(path)}"><span class="material-symbols-rounded">${S.writable(path,state.items)?'folder':'lock'}</span><span>${escape(path)}</span></button>`).join('')}</aside><section class="fm-files"><div class="fm-path"><button type="button" data-fm-path="/">Application root</button>${state.path.split('/').filter(Boolean).map((part,index,parts)=>`<span>/</span><button type="button" data-fm-path="/${escape(parts.slice(0,index+1).join('/'))}">${escape(part)}</button>`).join('')}</div><div data-fm-grid></div></section></div>`;
    grid=UI.DataGrid.mount(view.querySelector('[data-fm-grid]'),{id:'file-resources',title:'Current folder',rows,store:UI.createStore({scope:'user',owner:'example',module:'file-resources'}),columns:[
      {key:'name',label:'Name'}, {key:'type',label:'Type'}, {key:'bytes',label:'Size (bytes)',type:'number'}, {key:'permissions',label:'Permissions'}, {key:'owner',label:'Owner'}, {key:'protected',label:'Protected',value:item=>S.editable(item,state.items)?'No':'Yes'}, {key:'modified',label:'Modified',sortable:false,hidden:true}
    ],rowActions:[{label:'Open folder',when:item=>item.type==='folder',run:safe(item=>navigate(S.full(item)))},{label:'Inspect',run:inspect},{label:'Rename',when:item=>S.editable(item,state.items),run:safe(rename)},{label:'Permissions',when:item=>S.editable(item,state.items),run:safe(permissions)},{label:'Trash',when:item=>S.editable(item,state.items),run:safe(item=>remove([item.id]))}],bulkActions:[{label:'Copy',run:safe(rows=>relocate(rows.map(item=>item.id),true))},{label:'Move',run:safe(rows=>relocate(rows.map(item=>item.id),false))},{label:'Archive',run:safe(rows=>archive(rows.map(item=>item.id)))},{label:'Move to Trash',run:safe(rows=>remove(rows.map(item=>item.id)))}]});
  }
  function syncTransfers(){const jobs=window.WCPRuntime?.getState().jobs||[];let changed=false;for(const transfer of state.transfers){const current=jobs.find(job=>job.id===transfer.jobId);if(current&&(transfer.progress!==current.progress||transfer.status!==current.status)){transfer.progress=current.progress;transfer.status=current.status;changed=true;}}if(changed)save();return changed;}
  function renderTransfers(){syncTransfers();view.innerHTML=`<div class="fm-section-head"><div><strong>Transfers</strong><small>Simulated jobs. Upload stores names and sizes; remote imports are preflight jobs.</small></div><div>${button('upload','Choose files')}${button('remote-import','Import URL')}<a class="wcp-action-btn" href="jobs.html">Job Center</a></div></div><div data-fm-grid></div>`;grid=UI.DataGrid.mount(view.querySelector('[data-fm-grid]'),{id:'file-transfers',title:'Transfer activity',rows:state.transfers,columns:[{key:'name',label:'Name'},{key:'direction',label:'Direction'},{key:'size',label:'Size'},{key:'progress',label:'Progress (%)',type:'number'},{key:'status',label:'Status'}]});}
  function renderTrash(){view.innerHTML='<div class="fm-section-head"><div><strong>Trash</strong><small>Folders restore as one group. Conflicting names block the entire restoration.</small></div></div><div data-fm-grid></div>';grid=UI.DataGrid.mount(view.querySelector('[data-fm-grid]'),{id:'file-trash',title:'Deleted resources',rows:state.trash.filter(item=>!item.trashRoot||item.trashRoot===item.id),columns:[{key:'name',label:'Name'},{key:'originalPath',label:'Original folder'},{key:'deletedAt',label:'Deleted',type:'date'},{key:'type',label:'Type'}],rowActions:[{label:'Restore',run:safe(async item=>{if(!await UI.Dialog.confirm({title:'Restore resource',message:'Restore '+item.name+' and all its descendants to the original folder?'}))return;const count=S.restore(state,item.id);save();render();notify(count+' resources restored.');})},{label:'Delete permanently',run:safe(async item=>{if(!await UI.Dialog.confirm({title:'Delete permanently',message:'Permanently remove '+item.name+' and its archived descendants?',confirmLabel:'Delete permanently',typed:'DELETE '+item.name}))return;S.purge(state,item.id);save();render();})}]});}
  function renderAccess(){view.innerHTML=`<div class="fm-section-head"><div><strong>SFTP / SSH access</strong><small>Public keys and connection metadata for this account.</small></div><a class="wcp-action-btn" href="file-access.html">Advanced policy</a></div><div class="fm-access-grid"><section class="fm-card"><div class="fm-list-title">SFTP connection</div><dl><div><dt>Host</dt><dd>sftp.example.com</dd></div><div><dt>Port</dt><dd>22</dd></div><div><dt>Root</dt><dd>/home/example/apps/web</dd></div></dl>${button('copy-command','Copy SFTP command')}</section><section class="fm-card"><div class="fm-list-title">Authorized public keys</div>${state.keys.map(item=>`<div class="fm-key-row"><strong>${escape(item.name)}</strong><small>${escape(item.access)}</small></div>`).join('')||'<p>No public keys added to this demo.</p>'}${button('ssh-key','Add public key')}</section></div>`;}
  function renderSettings(){view.innerHTML=`<div class="fm-section-head"><div><strong>File Manager preferences</strong><small>Local display and creation defaults.</small></div></div><form data-fm-settings></form>`;UI.Form.populate(view.querySelector('[data-fm-settings]'),{fields:[{name:'showHidden',label:'Show hidden files',type:'checkbox',default:state.showHidden},{name:'encoding',label:'Encoding',type:'select',options:['UTF-8','ISO-8859-1'],default:state.settings.encoding},{name:'defaultFile',label:'File permissions',type:'select',options:['0644','0600','0664'],default:state.settings.defaultFile},{name:'defaultFolder',label:'Folder permissions',type:'select',options:['0755','0700','0775'],default:state.settings.defaultFolder},{name:'virusScan',label:'Simulate malware scan',type:'checkbox',default:state.settings.virusScan},{name:'snapshots',label:'Simulate pre-change snapshot',type:'checkbox',default:state.settings.snapshots}],onCancel:()=>setTab('files'),onSubmit:values=>{state.showHidden=values.showHidden;for(const name of ['encoding','defaultFile','defaultFolder','virusScan','snapshots'])state.settings[name]=values[name];save();notify('Preferences saved.');}});}
  function render(){
    grid?.destroy();grid=null;UI.Form.detach(view.querySelector('[data-fm-settings]'));
    host.querySelector('[data-fm-count]').textContent=state.items.length;
    host.querySelector('[data-fm-trash-count]').textContent=state.trash.length;
    host.querySelector('[data-fm-storage]').textContent=bytes(state.items.reduce((sum,item)=>sum+Number(item.bytes||0),0));
    host.querySelector('[data-fm-protected]').textContent=state.items.filter(item=>!S.editable(item,state.items)).length;
    host.querySelectorAll('[data-fm-tab]').forEach(control=>{const active=control.dataset.fmTab===state.tab;control.classList.toggle('active',active);control.setAttribute('aria-pressed',String(active));});
    ({files:renderFiles,transfers:renderTransfers,trash:renderTrash,access:renderAccess,settings:renderSettings})[state.tab]();
  }
  function setTab(tab){if(!tabs.includes(tab))return;state.tab=tab;save();if(location.hash.slice(1)!==tab)location.hash=tab;render();}
  function runAction(action){
    if(action==='toggle-hidden'){state.showHidden=!state.showHidden;save();render();return;}
    if(action==='refresh'){render();return;}
    if(action==='copy-command'){navigator.clipboard?.writeText('sftp example-deploy@sftp.example.com').then(()=>notify('SFTP command copied.'),()=>notify('SFTP: example-deploy@sftp.example.com'));return;}
    if(action==='new-file'||action==='new-folder'){
      checkWritable();const folder=action==='new-folder',creationPath=state.path;
      openForm(folder?'Create folder':'Create file',creationPath+' · prototype metadata',S.fields({items:state.items,path:creationPath,folder,permissions:folder?state.settings.defaultFolder:state.settings.defaultFile}),values=>{window.WCPWorkspaceSchemas.assertValid(S.fields({items:state.items,path:creationPath,folder}),values);state.items.push({id:uid(),name:values.name,path:creationPath,type:folder?'folder':'text',bytes:0,size:folder?'—':'0 B',permissions:values.permissions,owner:'www-data',group:'example',modified:'just now',protected:['.env','.htaccess','.well-known'].includes(values.name)});});return;
    }
    if(action==='upload'){
      checkWritable();const destination=state.path;
      const form=openForm('Upload files','Only names and sizes are saved in this demo; file contents are not read.',[{name:'files',label:'Files',type:'file',required:true}],(_values,target)=>{
        checkWritable(destination);const files=[...target.elements.namedItem('files').files];if(!files.length)throw new Error('Choose at least one file.');
        const names=new Set();for(const file of files){if(!S.validName(file.name)||names.has(UI.normalize(file.name))||state.items.some(item=>item.path===destination&&UI.normalize(item.name)===UI.normalize(file.name)))throw new Error('An upload name is invalid or already exists. No files were added.');names.add(UI.normalize(file.name));}
        const id=uid(),jobId=job('Simulate upload to '+destination,'file-upload');
        state.items.push(...files.map(file=>({id:uid(),name:file.name,path:destination,type:'text',bytes:file.size,size:bytes(file.size),permissions:state.settings.defaultFile,owner:'www-data',group:'example',modified:'just now',protected:['.env','.htaccess'].includes(file.name)})));
        state.transfers.unshift({id,name:files.map(file=>file.name).join(', '),direction:'Upload',size:bytes(files.reduce((sum,file)=>sum+file.size,0)),jobId,progress:jobId?4:100,status:jobId?'running':'completed'});
      });form.elements.namedItem('files').multiple=true;return;
    }
    if(action==='remote-import'){
      checkWritable();const destination=state.path;
      openForm('Remote import preflight','Creates a simulated job. No remote file is fetched.',[{name:'source',label:'Public HTTPS URL',required:true,validate:value=>{try{const url=new URL(value);return url.protocol==='https:'&&!url.username&&!url.password?'':'Use HTTPS without embedded credentials.';}catch{return 'Enter a valid HTTPS URL.';}}}],values=>{checkWritable(destination);const name=new URL(values.source).pathname.split('/').pop()||'Remote import',jobId=job('Import preflight to '+destination,'file-import');state.transfers.unshift({id:uid(),name,direction:'Import preflight',size:'Metadata only',jobId,progress:jobId?4:100,status:jobId?'running':'completed'});});return;
    }
    if(action==='ssh-key')openForm('Add public SSH key','Saved locally for the prototype. Never paste a private key.',[{name:'name',label:'Key name',required:true,maxLength:80,validate:value=>state.keys.some(item=>UI.normalize(item.name)===UI.normalize(value))?'This key name already exists.':''},{name:'key',label:'Public key',type:'textarea',required:true,validate:value=>/^(ssh-ed25519|ssh-rsa|ecdsa-sha2-nistp256) [A-Za-z0-9+/]{32,}={0,2}(?: [^\r\n]*)?$/.test(value)?'':'Enter a supported public SSH key.'},{name:'access',label:'Access',type:'select',required:true,options:['SFTP only','SFTP and restricted shell']}],values=>{if(state.keys.some(item=>item.key.split(' ').slice(0,2).join(' ')===values.key.split(' ').slice(0,2).join(' ')))throw new Error('This public key is already authorized.');state.keys.push({id:uid(),name:values.name,key:values.key,access:values.access});});
  }
  host.addEventListener('click',event=>{const tab=event.target.closest('[data-fm-tab]');if(tab){setTab(tab.dataset.fmTab);return;}const folder=event.target.closest('[data-fm-path]');if(folder){safe(()=>navigate(folder.dataset.fmPath))();return;}const action=event.target.closest('[data-fm-action]');if(action)safe(()=>runAction(action.dataset.fmAction))();});
  window.addEventListener('hashchange',()=>{const tab=location.hash.slice(1);if(tabs.includes(tab)&&tab!==state.tab){state.tab=tab;save();render();}});
  document.addEventListener('wcp:runtime-change',()=>{if(syncTransfers()&&state.tab==='transfers'&&grid)grid.setRows(state.transfers);});
  render();
  if(new URLSearchParams(location.search).get('action')==='upload'){
    safe(()=>runAction('upload'))();const clean=new URL(location.href);clean.searchParams.delete('action');history.replaceState(null,'',clean.pathname+clean.search+clean.hash);
  }
})();
