/* Business contracts for the simulated filesystem and delegated reseller workspace. */
((global)=>{
  'use strict';
  const UI=global.WCPUI;
  const same=(a,b)=>UI.normalize(String(a).trim())===UI.normalize(String(b).trim());
  const roots=['/','/public','/app','/config','/storage','/logs','/backups'];
  const full=item=>(item.path==='/'?'':item.path)+'/'+item.name;
  const within=(path,parent)=>path===parent||path.startsWith(parent+'/');
  const validPath=value=>typeof value==='string'&&(value==='/'||/^\/(?:[^/\\\x00-\x1f]+(?:\/[^/\\\x00-\x1f]+)*)?$/.test(value)&&!value.split('/').some(part=>part==='.'||part==='..')&&!value.endsWith('/'));
  const validName=value=>typeof value==='string'&&value===value.trim()&&value.length>0&&value.length<=255&&!/[<>:"/\\|?*\x00-\x1f]/.test(value)&&!['.','..'].includes(value)&&!value.endsWith('.');
  const paths=items=>[...new Set([...roots,...items.filter(item=>item.type==='folder').map(full)])];
  const protectedRow=item=>item.protected||['.env','.htaccess','.well-known'].includes(item.name);
  const writable=(path,items)=>validPath(path)&&paths(items).includes(path)&&!within(path,'/backups')&&!items.some(item=>protectedRow(item)&&item.type==='folder'&&within(path,full(item)));
  const collision=(items,path,name,except)=>items.some(item=>item.id!==except&&item.path===path&&same(item.name,name));
  const members=(items,record)=>items.filter(item=>item.id===record.id||record.type==='folder'&&within(item.path,full(record)));
  const editable=(record,items)=>!!record&&writable(record.path,items)&&members(items,record).every(item=>!protectedRow(item));
  const topmost=(items,ids)=>{
    const selected=items.filter(item=>ids.includes(item.id));
    return selected.filter(item=>!selected.some(parent=>parent.id!==item.id&&parent.type==='folder'&&within(item.path,full(parent))));
  };
  function fileFields({items,path,record,folder=false,permissions='0644'}={}){
    return [
      {name:'name',label:record?'New name':folder?'Folder name':'File name',required:true,maxLength:255,default:record?.name||'',validate:value=>!validName(value)?'Enter a single name without path separators or reserved characters.':!writable(path,items)||record&&!editable(record,items)?'This location or resource is protected.':collision(items,path,value,record?.id)?'A resource with this name already exists.':''},
      ...(!record?[{name:'permissions',label:'Permissions',type:'select',required:true,options:folder?['0755','0700','0775']:['0644','0600','0664'],default:permissions}]:[])
    ];
  }
  function destinationFields({items,records}={}){
    return [{name:'destination',label:'Destination folder',type:'select',required:true,options:paths(items).filter(path=>writable(path,items)),validate:path=>{
      if(!writable(path,items))return 'Choose an existing writable folder.';
      if(records.some(item=>!editable(item,items)))return 'Protected resources cannot be copied or moved.';
      if(records.some(item=>item.type==='folder'&&within(path,full(item))))return 'A folder cannot be placed inside itself.';
      return records.some(item=>collision(items,path,item.name))?'The destination already contains a selected name.':'';
    }}];
  }
  function assertValid(fields,values){const result=UI.validateValues(fields,values);if(!result.valid)throw new Error(Object.values(result.errors)[0]);return result.values;}
  function rename(state,id,name){
    const record=state.items.find(item=>item.id===id);if(!record)throw new Error('The resource no longer exists.');
    assertValid(fileFields({items:state.items,path:record.path,record}),{name});
    const old=full(record),next=(record.path==='/'?'':record.path)+'/'+name;
    if(record.type==='folder')state.items.forEach(item=>{if(within(item.path,old))item.path=next+item.path.slice(old.length);});
    record.name=name;record.modified='just now';
  }
  function relocate(state,ids,destination,copy,uid){
    const records=topmost(state.items,ids);if(!records.length)throw new Error('Select an existing resource.');
    assertValid(destinationFields({items:state.items,records}),{destination});
    const groups=records.map(record=>({record,rows:members(state.items,record)}));
    // Check all roots before changing any row, including two equal names from different folders.
    if(new Set(records.map(record=>UI.normalize(record.name))).size!==records.length)throw new Error('Selected names collide at the destination.');
    const changes=groups.flatMap(({record,rows})=>rows.map(item=>{
      const next={...item,path:item.id===record.id?destination:(destination==='/'?'':destination)+'/'+record.name+item.path.slice(full(record).length),modified:'just now'};
      if(copy)next.id=uid();return {item,next};
    }));
    if(copy)state.items.push(...changes.map(change=>change.next));else changes.forEach(({item,next})=>Object.assign(item,next));
    return changes.length;
  }
  function trash(state,ids,uid){
    const records=topmost(state.items,ids);if(!records.length)throw new Error('Select an existing resource.');
    if(records.some(item=>!editable(item,state.items)))throw new Error('Protected resources and their parent folders cannot be deleted.');
    const removed=new Set();
    records.forEach(record=>{const batch=uid();members(state.items,record).forEach(item=>{removed.add(item.id);state.trash.push({...item,trashBatch:batch,trashRoot:record.id,originalPath:item.path,deletedAt:new Date().toISOString()});});});
    state.items=state.items.filter(item=>!removed.has(item.id));return removed.size;
  }
  const trashGroup=(state,id)=>{const item=state.trash.find(row=>row.id===id);return !item?[]:item.trashBatch?state.trash.filter(row=>row.trashBatch===item.trashBatch):[item];};
  function restore(state,id){
    const rows=trashGroup(state,id);if(!rows.length)throw new Error('This archive no longer exists.');
    const combined=[...state.items,...rows];
    for(const item of rows){if(!writable(item.path,combined)||state.items.some(row=>row.id===item.id)||collision(state.items,item.path,item.name))throw new Error('Restore blocked: the original folder is missing, protected, or a name already exists.');}
    const restored=rows.map(item=>{const next={...item};for(const key of ['trashBatch','trashRoot','originalPath','deletedAt'])delete next[key];return next;});
    state.items.push(...restored);const ids=new Set(rows.map(item=>item.id));state.trash=state.trash.filter(item=>!ids.has(item.id));return rows.length;
  }
  function purge(state,id){const ids=new Set(trashGroup(state,id).map(item=>item.id));state.trash=state.trash.filter(item=>!ids.has(item.id));return ids.size;}
  const planCaps={Starter:{storage:25,sites:5,cpu:2,mailboxes:10},Business:{storage:200,sites:25,cpu:8,mailboxes:100},Agency:{storage:500,sites:100,cpu:16,mailboxes:200}};
  const packageDefaults=[
    {id:'starter',name:'Starter',source:'Starter',storage:25,sites:5,cpu:2,mailboxes:10,status:'Published'},
    {id:'business',name:'Business',source:'Business',storage:200,sites:25,cpu:8,mailboxes:100,status:'Published'},
    {id:'agency',name:'Agency Pro',source:'Agency',storage:500,sites:100,cpu:16,mailboxes:200,status:'Draft'},
    {id:'trial',name:'Trial',source:'Starter',storage:5,sites:1,cpu:1,mailboxes:5,status:'Published'}
  ];
  function customer({customers,packages,record,limit=25}={}){
    return [
      {name:'name',label:'Customer organization',required:true,maxLength:100,default:record?.name||'',validate:()=>!record&&customers.length>=limit?'The delegated account allowance is exhausted.':''},
      {name:'owner',label:'Owner email',type:'email',required:true,maxLength:254,default:record?.owner||''},
      {name:'domain',label:'Primary domain',required:true,rule:'domain',default:record?.domain||'',validate:value=>customers.some(item=>item.id!==record?.id&&same(item.domain,value))?'This domain already belongs to a customer.':''},
      {name:'package',label:'Package',type:'select',required:true,options:packages.filter(item=>item.status==='Published'||item.name===record?.package).map(item=>item.name),default:record?.package||'Business',validate:value=>!packages.some(item=>item.name===value&&(item.status==='Published'||value===record?.package))?'Choose a published package.':''},
      {name:'region',label:'Region',type:'select',required:true,options:['EU · automatic placement','France · automatic placement'],default:record?.region||'EU · automatic placement'}
    ];
  }
  function packageFields({packages,customers=[],record}={}){
    return [
      {name:'name',label:'Package name',required:true,maxLength:80,default:record?.name||'',validate:value=>packages.some(item=>item.id!==record?.id&&same(item.name,value))?'This package name already exists.':''},
      {name:'source',label:'ROOT plan source',type:'select',required:true,options:Object.keys(planCaps),default:record?.source||'Business'},
      ...['storage','sites','cpu','mailboxes'].map(name=>({name,label:{storage:'Storage (GiB)',sites:'Websites',cpu:'vCPU',mailboxes:'Mailboxes'}[name],type:'number',required:true,min:name==='mailboxes'?0:1,default:record?.[name]??planCaps.Business[name],validate:(value,values)=>!Number.isInteger(Number(value))?'Enter a whole number.':Number(value)>(planCaps[values.source]?.[name]??0)?'This value exceeds the delegated ROOT plan ceiling.':record&&customers.some(item=>item.package===record.name)&&Number(value)<Number(record[name])?'An assigned package cannot be reduced in this prototype.':''})),
      {name:'status',label:'Publication',type:'select',required:true,options:['Draft','Published'],default:record?.status||'Draft',validate:value=>value==='Draft'&&record?.status==='Published'&&customers.some(item=>item.package===record.name)?'An assigned package must stay published.':''}
    ];
  }
  global.WCPWorkspaceSchemas={files:{roots,full,within,validPath,validName,paths,writable,editable,topmost,fields:fileFields,destinationFields,rename,relocate,trash,trashGroup,restore,purge},reseller:{planCaps,packageDefaults,customer,package:packageFields},assertValid};
})(typeof window!=='undefined'?window:globalThis);
