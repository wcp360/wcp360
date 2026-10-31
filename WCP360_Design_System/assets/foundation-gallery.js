(() => {
  'use strict';
  const host=document.querySelector('[data-foundation-grid]');if(!host)return;
  const UI=window.WCPUI;
  const dataStore=UI.createStore({app:'core',scope:'design',owner:'gallery',module:'demo-resources'});
  const preferences=UI.createStore({app:'core',scope:'design',owner:'gallery',module:'grid-preferences'});
  const defaults=Array.from({length:32},(_,index)=>({id:'sample-'+index,name:['app','shop','api','staging'][index%4]+'-'+(index+1)+'.example.com',type:['Website','Database','Mailbox','Account'][index%4],status:index%5===0?'Suspended':'Active',quota:5+(index%8)*5,owner:'demo@example.com',created:new Date(Date.UTC(2026,9,30-index)).toISOString().slice(0,10)}));
  const saved=dataStore.read();let rows=Array.isArray(saved.rows)&&saved.rows.every(row=>row&&typeof row.id==='string')?saved.rows:defaults;
  let role='manager',grid;
  const context=()=>({scope:'design',owner:'gallery',capabilities:role==='manager'?['resource.write']:[]});
  const writable=()=>UI.can(context(),'resource.write',{scope:'design',owner:'gallery'});
  const save=()=>dataStore.write({rows});
  function edit(record){
    if(!writable()){UI.notify({title:'Read-only demo',message:'Select Manager to create or edit records.'});return;}
    const form=document.createElement('form');let dialog;
    const controller=UI.Form.populate(form,{values:record||{},submitLabel:record?'Save resource':'Create resource',onCancel:()=>dialog.close(false),fields:[
      {name:'name',label:'Resource name',required:true,maxLength:100,default:'new.example.com',validate:value=>rows.some(row=>row.id!==record?.id&&UI.normalize(row.name)===UI.normalize(value))?'This name is already used.':''},
      {name:'type',label:'Resource type',type:'select',required:true,options:['Website','Database','Mailbox','Account'],default:'Website'},
      {name:'status',label:'Status',type:'select',required:true,options:['Active','Suspended'],default:'Active'},
      {name:'quota',label:'Storage quota (GB)',type:'number',required:true,min:1,max:200,default:10},
      {name:'owner',label:'Owner email',type:'email',required:true,default:'demo@example.com'}
    ],onSubmit:values=>{
      if(!writable())throw new Error('The demo role no longer permits editing.');
      const item={...values,quota:Number(values.quota),id:record?.id||crypto.randomUUID(),created:record?.created||new Date().toISOString().slice(0,10)};
      if(record)rows=rows.map(row=>row.id===record.id?item:row);else rows.push(item);
      save();grid.setRows(rows);dialog.close(true);UI.notify({title:record?'Resource updated':'Resource created',message:item.name});
    }});
    dialog=UI.Dialog.open({title:record?'Edit resource':'Create demo resource',description:'Try an empty name, an invalid email or a quota outside 1–200 GB to inspect validation.',content:form,onClose:()=>controller.destroy()});
  }
  function mount(){
    grid=UI.DataGrid.mount(host,{id:'foundation-demo',title:'Demo resources',rows,store:preferences,columns:[
      {key:'name',label:'Resource'},{key:'type',label:'Type'},{key:'status',label:'Status'},{key:'quota',label:'Quota (GB)',type:'number'},{key:'owner',label:'Owner'},{key:'created',label:'Created',type:'date'}
    ],rowActions:[{label:'Edit',when:writable,run:edit}],bulkActions:writable()?[
      {label:'Suspend selected',confirm:true,typed:'SUSPEND',run:selection=>{if(!writable())throw new Error('Read-only role.');const ids=new Set(selection.map(row=>row.id));rows=rows.map(row=>ids.has(row.id)?{...row,status:'Suspended'}:row);save();grid.setRows(rows);UI.notify({title:'Resources suspended',message:selection.length+' demo records updated.'});}},
      {label:'Reactivate selected',confirm:true,run:selection=>{if(!writable())throw new Error('Read-only role.');const ids=new Set(selection.map(row=>row.id));rows=rows.map(row=>ids.has(row.id)?{...row,status:'Active'}:row);save();grid.setRows(rows);UI.notify({title:'Resources reactivated',message:selection.length+' demo records updated.'});}}
    ]:[],onRetry:()=>grid.setStatus('ready')});
    document.querySelectorAll('[data-gallery-create]').forEach(control=>control.disabled=!writable());
  }
  document.addEventListener('click',event=>{
    if(event.target.closest('[data-gallery-create]'))edit();
    if(event.target.closest('[data-gallery-toast]'))UI.notify({title:'Shared notification service',message:'One service is used by the gallery and product workflows.'});
    const state=event.target.closest('[data-gallery-state]');if(state)grid.setStatus(state.dataset.galleryState,'The demo data source is unavailable. Use Retry or Show records.');
  });
  document.querySelector('[data-gallery-role]').addEventListener('change',event=>{role=event.target.value;mount();});
  mount();
})();
