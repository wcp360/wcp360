/* UI adapter shared by account operations and schedule centers. No server operations. */
((global)=>{
  'use strict';
  const UI=global.WCPUI;let serial=0;
  const uid=()=>global.crypto?.randomUUID?.()||Date.now()+'-'+(++serial);
  function load(key,defaults,arrays=[]){let saved={};try{saved=JSON.parse(global.localStorage.getItem(key))||{};}catch{}const state={...structuredClone(defaults),...saved};for(const name of arrays)if(!Array.isArray(state[name]))state[name]=structuredClone(defaults[name]||[]);return state;}
  function save(key,state){try{global.localStorage.setItem(key,JSON.stringify(state));}catch{UI.notify({title:'Local storage unavailable',message:'Changes remain in this page session only.',tone:'error'});}}
  function form({title,description,fields,submitLabel='Save',onSubmit}){
    const content=document.createElement('form');const dialog=UI.Dialog.open({title,description,content,onClose:()=>UI.Form.detach(content)});
    UI.Form.populate(content,{fields,submitLabel,onCancel:()=>dialog.close(),onSubmit:async values=>{await onSubmit(values);dialog.close();}});return dialog;
  }
  function validate(fields,values){const result=UI.validateValues(fields,values);if(!result.valid)throw new Error(Object.values(result.errors)[0]);return result.values;}
  function job({name,kind,scope,status='running',reason=''}){if(!global.WCPRuntime)throw new Error('The Job Center is still loading. Try again in a moment.');return global.WCPRuntime.addJob({name,kind,scope,status,reason,actor:'Prototype operator'});}
  const jobs=()=>global.WCPRuntime?.getState().jobs||[];
  const pending=record=>['running','scheduled'].includes(record.status);
  const safe=run=>async(...args)=>{try{return await run(...args);}catch(error){UI.notify({title:'Operation blocked',message:error.message,tone:'error'});}};
  global.WCPPrototypeOperations={uid,load,save,form,validate,job,jobs,pending,safe};
})(globalThis);
