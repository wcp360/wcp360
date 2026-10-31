'use strict';
const $=id=>document.getElementById(id);
let cwd='',page=1,total=0,trash=false,csrf='',canWrite=false,editing=null,baseline='',sequence=0,busy=false,view='list',items=[],selected=new Set(),clipboard=null,anchor=-1;
let treeSequence=0,searchTimer,promptHandler;
const expanded=new Set(['']);
const notice=msg=>{$('notice').textContent=msg;};
const size=n=>n==null?'—':n<1024?n+' o':n<1048576?(n/1024).toFixed(1)+' Kio':(n/1048576).toFixed(1)+' Mio';
const date=t=>t?new Date(t*1000).toLocaleString('fr-FR',{dateStyle:'short',timeStyle:'short'}):'—';
const join=(dir,name)=>(dir?dir+'/':'')+name;
const key=item=>trash?item.id:item.path;
const chosen=()=>items.filter(item=>selected.has(key(item)));
function element(tag,text,cls){const e=document.createElement(tag);if(text!==undefined)e.textContent=text;if(cls)e.className=cls;return e;}
function button(text,handler,cls){const b=element('button',text,cls);b.type='button';b.onclick=e=>{e.stopPropagation();Promise.resolve().then(()=>handler(e)).catch(err=>notice(err.message));};return b;}
async function api(op,data={},post=false){
 const options={headers:{Accept:'application/json'},credentials:'same-origin'};let url='/api/v1/files/'+op;
 if(post){options.method='POST';options.headers['X-CSRF-Token']=csrf;if(data instanceof FormData)options.body=data;else{if(typeof editorLocks!=='undefined'&&data.path&&editorLocks.has(data.path)&&!data.lock_token)data={...data,lock_token:editorLocks.get(data.path)};options.headers['Content-Type']='application/json';options.body=JSON.stringify(data);}}
 else url+='?'+new URLSearchParams(data);
 const r=await fetch(url,options);if(r.redirected||!r.headers.get('content-type')?.includes('application/json'))throw new Error('Session expirée ou réponse inattendue. Rechargez la page.');
 const body=await r.json();if(!r.ok)throw new Error((body.error?.message||'Opération impossible.')+(body.request_id?' Réf. '+body.request_id:''));return body.data;
}
function selectable(item){return trash||item.kind!=='blocked';}
function updateSelection(){
 const available=items.filter(selectable),n=chosen().length;
 $('selectedCount').textContent=n?n+' sélectionné'+(n>1?'s':''):'Sélectionner la page';
 $('selectAll').checked=available.length>0&&n===available.length;$('selectAll').indeterminate=n>0&&n<available.length;
 document.querySelectorAll('[data-selection-key]').forEach(e=>{const on=selected.has(e.dataset.selectionKey);if(e.matches('input')){e.checked=on;e.disabled=busy||e.dataset.blocked==='1';}else e.classList.toggle('selected',on);});
 for(const id of ['copyBtn','cutBtn','duplicateBtn','trashBtn']){$(id).hidden=!canWrite||trash;$(id).disabled=busy||!n;}
 $('restoreBtn').hidden=!canWrite||!trash;$('restoreBtn').disabled=busy||!n;
 $('pasteBtn').hidden=!canWrite||trash;$('pasteBtn').disabled=busy||!clipboard?.entries.length;
 $('clipboardInfo').textContent=clipboard?`${clipboard.entries.length} élément(s) à ${clipboard.mode==='copy'?'copier':'déplacer'}`:'';
 for(const id of ['uploadBtn','newFile','newFolder']){$(id).hidden=!canWrite||trash;$(id).disabled=busy;}
 $('save').hidden=!canWrite;$('selectAll').disabled=busy;$('prev').disabled=busy||trash||page<=1;$('next').disabled=busy||trash||page*100>=total;
}
function checkbox(item,index){const c=document.createElement('input');c.type='checkbox';c.dataset.selectionKey=key(item);c.checked=selected.has(key(item));c.disabled=!selectable(item)||busy;c.dataset.blocked=selectable(item)?'0':'1';c.setAttribute('aria-label','Sélectionner '+(item.name||item.path));
 c.onclick=e=>{e.stopPropagation();if(e.shiftKey&&anchor>=0){const from=Math.min(anchor,index),to=Math.max(anchor,index);for(let i=from;i<=to;i++)if(selectable(items[i])){if(c.checked)selected.add(key(items[i]));else selected.delete(key(items[i]));}}else{if(c.checked)selected.add(key(item));else selected.delete(key(item));}anchor=index;updateSelection();};return c;}
function dropTarget(node,destination){
 node.ondragover=e=>{if(!canWrite||busy||trash||!e.dataTransfer.types.includes('application/x-wcp-files'))return;e.preventDefault();e.dataTransfer.dropEffect='move';node.classList.add('dragover');};
 node.ondragleave=()=>node.classList.remove('dragover');node.ondrop=e=>{node.classList.remove('dragover');e.preventDefault();e.stopPropagation();if(!canWrite||busy||trash)return;
 // Never accept arbitrary paths from another browser tab: use the in-memory source captured at dragstart.
 if(!dragged?.length||!e.dataTransfer.types.includes('application/x-wcp-files'))return;const entries=dragged;dragged=null;
 if(!confirm(`Déplacer ${entries.length} élément(s) vers /${destination} ?`))return;
 transfer(entries,destination,'cut').catch(err=>notice(err.message));};
}
let dragged=null;
function interactions(node,item){node.dataset.selectionKey=key(item);node.oncontextmenu=e=>{if(busy)return;e.preventDefault();if(selectable(item)&&!selected.has(key(item))){selected.clear();selected.add(key(item));updateSelection();}context(item,e.clientX,e.clientY);};
 if(!trash&&canWrite&&item.kind!=='blocked'){node.draggable=true;node.ondragstart=e=>{if(busy){e.preventDefault();return;}dragged=selected.has(key(item))?chosen().map(x=>({...x})):[{...item}];e.dataTransfer.setData('application/x-wcp-files','internal');e.dataTransfer.effectAllowed='move';};node.ondragend=()=>{dragged=null;document.querySelectorAll('.dragover').forEach(n=>n.classList.remove('dragover'));};if(item.kind==='directory')dropTarget(node,item.path);}}
function openItem(item){if(trash)return;if(item.kind==='directory')return navigate(item.path);if(item.kind==='blocked'){notice('Lien ou fichier spécial : accès bloqué.');return;}return openEditor(item.path);}
function render(){
 $('rows').replaceChildren();$('grid').replaceChildren();$('title').textContent=trash?'Corbeille':'Mes fichiers';$('filesNav').classList.toggle('active',!trash);$('trashNav').classList.toggle('active',trash);
 $('breadcrumbs').hidden=trash;$('search').disabled=trash;$('hiddenToggle').disabled=trash;
 items.forEach((item,index)=>{
 const tr=document.createElement('tr');interactions(tr,item);const check=element('td',undefined,'checkcell');check.append(checkbox(item,index));tr.append(check);
 const name=element('td');const icon=element('span',trash?'♲':item.kind==='directory'?'▰':item.kind==='blocked'?'⊘':'≡','icon');name.append(icon,button(item.name||item.path,()=>openItem(item)));tr.append(name);
 for(const value of [size(item.size),date(trash?item.deleted_at:item.mtime),item.mode??'—',item.uid==null?'—':item.uid+' / '+item.gid])tr.append(element('td',value));
 const actions=element('td');if(trash){if(canWrite)actions.append(button('Restaurer',()=>runBatch([item],async i=>api('restore',{id:i.id},true),'Restauration')));}else if(item.kind!=='blocked'){actions.append(button('Propriétés',()=>properties(item.path)));if(item.kind==='file')actions.append(button('Télécharger',()=>download(item.path)));}
 actions.append(button('⋯',e=>{const r=e.target.getBoundingClientRect();if(!selected.has(key(item))){selected.clear();selected.add(key(item));updateSelection();}context(item,r.left,r.bottom);},'more'));tr.append(actions);$('rows').append(tr);
 const card=element('article',undefined,'file-card');interactions(card,item);const top=element('div',undefined,'card-top');top.append(checkbox(item,index),element('span',trash?'♲':item.kind==='directory'?'▰':item.kind==='blocked'?'⊘':'≡','file-icon'),button('⋯',e=>{const r=e.target.getBoundingClientRect();if(!selected.has(key(item))){selected.clear();selected.add(key(item));updateSelection();}context(item,r.left,r.bottom);},'more'));
 card.append(top,button(item.name||item.path,()=>openItem(item),'name'),element('small',trash?'Supprimé le '+date(item.deleted_at):item.kind==='directory'?'Dossier':size(item.size)),element('small',trash?'Restauration disponible':date(item.mtime)));$('grid').append(card);
 });
 $('tableWrap').hidden=view==='grid';$('grid').hidden=view!=='grid';$('empty').hidden=items.length!==0;
 if(!items.length&&view==='grid')$('grid').append(element('p','Aucun élément dans cet espace.'));
 $('count').textContent=total+' élément'+(total>1?'s':'');$('page').textContent=trash?'':`Page ${page} / ${Math.max(1,Math.ceil(total/100))}`;$('prev').disabled=busy||trash||page<=1;$('next').disabled=busy||trash||page*100>=total;
 breadcrumbs();updateSelection();
}
async function load(quiet=false){const seq=++sequence;const requestPath=cwd,requestTrash=trash;if(!quiet)notice('Chargement…');const data=await api(trash?'trash_list':'list',{path:cwd,page,query:$('search').value,hidden:$('hiddenToggle').checked?'1':'0'});
 if(seq!==sequence||requestPath!==cwd||requestTrash!==trash)return;
 if(!trash&&page>1&&data.items.length===0){page=Math.max(1,Math.ceil(data.total/100));return load(quiet);}
 items=data.items;total=data.total??items.length;const available=new Set(items.map(key));selected=new Set([...selected].filter(k=>available.has(k)));render();if(!quiet)notice('');}
async function navigate(path){if(busy)return;cwd=path;page=1;trash=false;selected.clear();anchor=-1;$('search').value='';await load();}
function breadcrumbs(){const n=$('breadcrumbs');const root=button('Mes fichiers',()=>navigate(''));dropTarget(root,'');n.replaceChildren(root);let part='';for(const s of cwd.split('/').filter(Boolean)){n.append(element('span','/'));part=join(part,s);const target=part;const b=button(s,()=>navigate(target));dropTarget(b,target);n.append(b);}}
async function refreshTree(){const seq=++treeSequence;const root=document.createElement('ul');$('tree').replaceChildren(root);await treeNode(root,'','Mes fichiers',0,seq);}
async function treeNode(parent,path,name,depth,seq){
 const li=document.createElement('li'),line=document.createElement('div'),children=document.createElement('ul');let loaded=false,loading=false;const toggle=button(expanded.has(path)?'▾':'▸',async()=>{if(loading)return;if(expanded.has(path)){expanded.delete(path);children.hidden=true;toggle.textContent='▸';toggle.setAttribute('aria-expanded','false');}else{expanded.add(path);children.hidden=false;toggle.textContent='▾';toggle.setAttribute('aria-expanded','true');if(!loaded)await fill();}});
 toggle.setAttribute('aria-label','Déplier ou replier '+name);toggle.setAttribute('aria-expanded',String(expanded.has(path)));
 const label=button(name,()=>navigate(path),'tree-label');label.title='/'+path;line.append(toggle,label);dropTarget(line,path);li.append(line,children);parent.append(li);children.hidden=!expanded.has(path);
 async function fill(pageNumber=1){loading=true;try{const data=await api('list',{path,page:pageNumber,directories:'1',hidden:$('hiddenToggle').checked?'1':'0'});if(seq!==treeSequence)return;if(pageNumber===1)children.replaceChildren();for(const item of data.items)await treeNode(children,item.path,item.name,depth+1,seq);loaded=true;
 if(data.total>pageNumber*100){const more=button('Afficher la suite…',async()=>{more.remove();await fill(pageNumber+1);});children.append(more);}
 }catch(e){children.append(element('li',e.message));}finally{loading=false;}}
 if(expanded.has(path)&&depth<32)await fill();
}
function ask(title,help,value,handler){if(busy)return;$('promptTitle').textContent=title;$('promptHelp').textContent=help;$('promptValue').value=value;promptHandler=handler;$('prompt').showModal();$('promptValue').focus();}
$('cancelPrompt').onclick=()=>$('prompt').close();$('promptForm').onsubmit=async e=>{e.preventDefault();const value=$('promptValue').value;$('prompt').close();try{await promptHandler(value);}catch(err){notice(err.message);}};
async function runBatch(entries,action,label){if(busy||!entries.length)return;busy=true;updateSelection();const results=[];
 try{for(let i=0;i<entries.length;i++){notice(`${label} ${i+1}/${entries.length}…`);const item=entries[i];try{await action(item);results.push({item,ok:true});}catch(e){results.push({item,ok:false,error:e.message});}}
 selected.clear();$('operationResults').hidden=false;const failed=results.filter(r=>!r.ok);$('operationSummary').textContent=`${label} : ${results.length-failed.length} réussi(s), ${failed.length} échec(s)`;$('operationItems').replaceChildren(...results.map(r=>element('li',(r.ok?'✓ ':'✕ ')+(r.item.path||r.item.name)+(r.ok?' — effectué':' — '+r.error))));$('operationResults').open=failed.length>0;
 await load(true);await refreshTree();notice($('operationSummary').textContent);return results;
 }finally{busy=false;updateSelection();}}
async function mutate(op,data){return runBatch([{path:data.path||data.id}],()=>api(op,data,true),'Opération');}
function setClipboard(mode,entries=chosen()){if(busy||!canWrite||trash||!entries.length)return;clipboard={mode,entries:entries.map(i=>({...i}))};updateSelection();notice(entries.length+' élément(s) dans le presse-papiers interne.');}
async function transfer(entries,destination,mode){return runBatch(entries,item=>api(mode==='copy'?'copy':'rename',{path:item.path,destination:join(destination,item.path.split('/').pop())},true),mode==='copy'?'Copie':'Déplacement');}
async function paste(){if(!clipboard||busy)return;const current=clipboard;const results=await transfer(current.entries,cwd,current.mode);if(current.mode==='cut'&&results){clipboard={mode:'cut',entries:results.filter(r=>!r.ok).map(r=>r.item)};if(!clipboard.entries.length)clipboard=null;}updateSelection();}
function duplicateName(item){const name=item.path.split('/').pop();const dot=name.lastIndexOf('.');return item.kind==='file'&&dot>0?name.slice(0,dot)+' - copie'+name.slice(dot):name+' - copie';}
async function duplicate(entries=chosen()){return runBatch(entries,item=>api('copy',{path:item.path,destination:join(cwd,duplicateName(item))},true),'Duplication');}
async function removeEntries(entries=chosen()){if(!entries.length||!confirm(`Mettre ${entries.length} élément(s) à la corbeille, avec leur contenu ?`))return;
 return runBatch(entries,async item=>{const data={path:item.path};if(item.kind==='file'){const info=await api('properties',{path:item.path});data.revision=info.revision;}return api('trash',data,true);},'Corbeille');}
function renameItem(item){ask('Renommer ou déplacer','Chemin relatif à la racine. Aucun écrasement.',item.path,path=>mutate('rename',{path:item.path,destination:path}));}
async function properties(path){const info=await api('properties',{path});$('propertyList').replaceChildren();const values={'Nom':info.name,'Chemin':'/'+info.path,'Type':info.kind==='directory'?'Dossier':'Fichier','Taille':size(info.size),'Modifié':date(info.mtime),'Permissions':info.platform==='Windows'?info.mode+' (indicatives)':info.mode,'UID / GID':info.platform==='Windows'?'Non applicable sous Windows':info.uid+' / '+info.gid,'Système':info.platform};for(const [k,v]of Object.entries(values))$('propertyList').append(element('dt',k),element('dd',v));$('properties').showModal();}
function closeContext(){$('contextMenu').hidden=true;}
function context(item,x,y){closeContext();const menu=$('contextMenu');menu.replaceChildren();const add=(label,fn)=>menu.append(button(label,()=>{closeContext();return fn();}));
 if(trash){if(canWrite)add('Restaurer la sélection',()=>runBatch(chosen(),i=>api('restore',{id:i.id},true),'Restauration'));if(typeof proCapabilities!=='undefined'&&proCapabilities.admin)add('Supprimer définitivement',()=>purgeSelectedTrash());}
 else if(item.kind!=='blocked'){add(item.kind==='directory'?'Ouvrir le dossier':'Éditer',()=>openItem(item));add('Propriétés',()=>properties(item.path));if(item.kind==='file')add('Télécharger',()=>download(item.path));if(canWrite){add('Copier la sélection',()=>setClipboard('copy'));add('Couper la sélection',()=>setClipboard('cut'));if(clipboard)add('Coller ici',()=>paste());add('Dupliquer la sélection',()=>duplicate());if(chosen().length===1)add('Renommer / déplacer',()=>renameItem(item));add('Mettre la sélection à la corbeille',()=>removeEntries());}}
 if(!menu.childElementCount)return;menu.hidden=false;const rect=menu.getBoundingClientRect();menu.style.left=Math.max(4,Math.min(x,innerWidth-rect.width-4))+'px';menu.style.top=Math.max(4,Math.min(y,innerHeight-rect.height-4))+'px';menu.querySelector('button')?.focus();}
document.addEventListener('click',e=>{if(!$('contextMenu').contains(e.target)&&!e.target.closest('.more'))closeContext();});window.addEventListener('resize',closeContext);window.addEventListener('scroll',closeContext,true);
async function openEditor(path){const data=await api('read',{path});editing=data;baseline=data.content;$('editorTitle').textContent=path;$('content').value=data.content;$('content').readOnly=!canWrite;$('editorStatus').textContent='UTF-8 · '+size(new TextEncoder().encode(data.content).length);$('editor').showModal();}
const dirty=()=>editing&&$('content').value!==baseline;
$('editor').addEventListener('cancel',e=>{if(dirty()&&!confirm('Abandonner les modifications ?'))e.preventDefault();});$('closeEditor').onclick=e=>{if(dirty()&&!confirm('Abandonner les modifications ?'))e.preventDefault();};
window.addEventListener('beforeunload',e=>{if(busy||($('editor').open&&dirty())){e.preventDefault();e.returnValue='';}});
async function saveEditor(){if(!editing||!canWrite||$('save').disabled)return;$('save').disabled=true;const content=$('content').value;try{const result=await api('save',{path:editing.path,content,revision:editing.revision},true);editing.revision=result.revision;baseline=content;$('editorStatus').textContent='Enregistré.';await load(true);}catch(e){$('editorStatus').textContent=e.message;}finally{$('save').disabled=false;}}$('save').onclick=saveEditor;
async function download(path){const r=await fetch('/api/v1/files/download?'+new URLSearchParams({path}),{credentials:'same-origin'});if(!r.ok||r.redirected||!r.headers.get('content-type')?.includes('application/octet-stream')){let message='Téléchargement impossible.';try{message=(await r.json()).error.message;}catch{}throw new Error(message);}const url=URL.createObjectURL(await r.blob());const a=document.createElement('a');a.href=url;a.download=path.split('/').pop();a.click();setTimeout(()=>URL.revokeObjectURL(url),1000);}
$('copyBtn').onclick=()=>setClipboard('copy');$('cutBtn').onclick=()=>setClipboard('cut');$('pasteBtn').onclick=()=>paste().catch(e=>notice(e.message));$('duplicateBtn').onclick=()=>duplicate().catch(e=>notice(e.message));$('trashBtn').onclick=()=>removeEntries().catch(e=>notice(e.message));$('restoreBtn').onclick=()=>runBatch(chosen(),i=>api('restore',{id:i.id},true),'Restauration').catch(e=>notice(e.message));
$('selectAll').onclick=()=>{for(const item of items.filter(selectable)){if($('selectAll').checked)selected.add(key(item));else selected.delete(key(item));}updateSelection();};
$('newFile').onclick=()=>ask('Nouveau fichier','Exemple : notes.txt. Le fichier sera créé vide.','',name=>mutate('create',{path:join(cwd,name),content:''}));$('newFolder').onclick=()=>ask('Nouveau dossier','Le dossier parent doit déjà exister.','',name=>mutate('mkdir',{path:join(cwd,name)}));
$('uploadBtn').onclick=()=>$('upload').click();$('upload').onchange=async()=>{const file=$('upload').files[0];if(!file)return;try{if(file.size>20971520)throw new Error('Import limité à 20 Mio.');const body=new FormData();body.append('file',file);body.append('path',join(cwd,file.name));await runBatch([{path:file.name}],()=>api('upload',body,true),'Import');}catch(e){notice(e.message);}finally{$('upload').value='';}};
$('filesNav').onclick=()=>navigate(cwd).catch(e=>notice(e.message));$('trashNav').onclick=()=>{if(busy)return;trash=true;selected.clear();load().catch(e=>notice(e.message));};
$('refresh').onclick=()=>{if(busy)return;Promise.all([load(),refreshTree()]).catch(e=>notice(e.message));};$('treeRefresh').onclick=()=>refreshTree().catch(e=>notice(e.message));
$('prev').onclick=()=>{page--;selected.clear();load().catch(e=>notice(e.message));};$('next').onclick=()=>{page++;selected.clear();load().catch(e=>notice(e.message));};
$('search').oninput=()=>{clearTimeout(searchTimer);searchTimer=setTimeout(()=>{if(busy)return;page=1;selected.clear();load().catch(e=>notice(e.message));},250);};
$('hiddenToggle').onchange=()=>{if(busy)return;page=1;selected.clear();Promise.all([load(),refreshTree()]).catch(e=>notice(e.message));};
for(const [id,mode]of [['listView','list'],['gridView','grid']])$(id).onclick=()=>{view=mode;$('listView').setAttribute('aria-pressed',String(view==='list'));$('gridView').setAttribute('aria-pressed',String(view==='grid'));render();};
document.addEventListener('keydown',e=>{if(e.key==='Escape'){closeContext();return;}if((e.ctrlKey||e.metaKey)&&e.key.toLowerCase()==='s'&&$('editor').open){e.preventDefault();saveEditor();return;}if(e.target.closest('input,textarea,dialog')||busy)return;const control=e.ctrlKey||e.metaKey;let action;
 if(control&&e.key.toLowerCase()==='a'){e.preventDefault();items.filter(selectable).forEach(i=>selected.add(key(i)));updateSelection();return;}
 if(control&&e.key.toLowerCase()==='c')action=()=>setClipboard('copy');if(control&&e.key.toLowerCase()==='x')action=()=>setClipboard('cut');if(control&&e.key.toLowerCase()==='v'&&!trash&&canWrite)action=paste;
 if(e.key==='Delete'&&!trash&&canWrite)action=removeEntries;if(e.key==='F2'&&chosen().length===1&&!trash&&canWrite)action=()=>renameItem(chosen()[0]);if(action){e.preventDefault();Promise.resolve().then(action).catch(err=>notice(err.message));}});
setInterval(()=>{if(!csrf||!$('autoRefresh').checked||document.hidden||busy||document.querySelector('dialog[open]')||!$('contextMenu').hidden)return;Promise.all([load(true),refreshTree()]).catch(e=>notice(e.message));},15000);
const fmReady=(async()=>{try{const session=await api('session');csrf=session.csrf;canWrite=session.can_write;$('user').textContent=session.user;$('logoutToken').value=session.logout_csrf;await load();await refreshTree();}catch(e){notice(e.message);}})();
