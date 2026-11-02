/* Local configuration lifecycle. No server or infrastructure command is executed. */
(function(global){
  'use strict';
  const key='wcp360-system-settings-v1',draftKey='wcp360-system-settings-draft-v2';
  const clone=value=>structuredClone(value);
  const token=raw=>{let hash=2166136261;for(const char of String(raw??''))hash=Math.imul(hash^char.charCodeAt(0),16777619);return (hash>>>0).toString(36)+':'+String(raw??'').length;};
  function create({model=global.WCPSettingsModel,storage,now=()=>new Date().toISOString()}={}){
    if(!model)throw new Error('Le modèle des paramètres est indisponible.');
    if(storage===undefined)try{storage=global.localStorage;}catch{}
    let raw=null,baseline,draft,loadIssues=[],draftStatus='empty',staleDraft=null,readError='',writeError='',conflict=false,recovering=false;
    const safe=config=>JSON.parse(model.serialize(config));
    const fields=model.sections.flatMap(section=>section.groups.flatMap(group=>group.fields));
    function read(){if(!storage?.getItem)throw new Error('Stockage local indisponible.');return storage.getItem(key);}
    function changes(){return model.diff(baseline,draft);}
    function loadDraft(config){
      if(!config||typeof config!=='object'||Array.isArray(config))throw new Error('Brouillon invalide.');
      const requiredPaths=config.schemaVersion===2?model.legacyDraftPaths:fields.map(field=>field.path);
      if(requiredPaths.some(path=>model.get(config,path)===undefined))throw new Error('Instantané de brouillon incomplet.');
      const loaded=model.load(JSON.stringify(config));
      // Active-state migration normalizes types. An empty number is instead a
      // valid intermediate edit: preserve it so reload cannot invent a value.
      const blanks=fields.filter(field=>field.type==='number'&&model.get(config,field.path)==='');
      const blankPaths=new Set(blanks.map(field=>field.path));
      if(loaded.issues.some(issue=>!blankPaths.has(issue.path)))throw new Error('Brouillon invalide.');
      for(const field of blanks)model.set(loaded.config,field.path,'');
      return loaded.config;
    }
    function restoreDraft(){
      staleDraft=null;draftStatus='empty';
      try{
        const saved=JSON.parse(storage?.getItem(draftKey)||'null');
        if(saved===null)return;
        if(!saved||typeof saved!=='object'||Array.isArray(saved))throw new Error('Brouillon invalide.');
        const restored=loadDraft(saved.config);
        if(saved.baseToken!==token(raw)){staleDraft=saved;draftStatus='stale';return;}
        draft=restored;
        if(changes().length||saved.recovering){draftStatus='restored';recovering=!!saved.recovering;}
      }catch{draftStatus='invalid';}
    }
    function load(){
      readError='';writeError='';conflict=false;recovering=false;
      try{raw=read();}catch(error){raw=null;readError=error.message||'Lecture impossible.';}
      const loaded=model.load(raw);baseline=clone(loaded.config);draft=clone(baseline);loadIssues=loaded.issues||[];
      if(!readError)restoreDraft();
    }
    function saveDraft(){
      if(!changes().length&&!recovering){
        try{storage?.removeItem(draftKey);draftStatus='empty';writeError='';return true;}
        catch(error){draftStatus='cleanup-error';writeError=error.message||'Le brouillon enregistré ne peut pas être supprimé.';return false;}
      }
      try{
        if(!storage?.setItem)throw new Error('Stockage local indisponible.');
        storage.setItem(draftKey,JSON.stringify({baseToken:token(raw),config:safe(draft),recovering,updatedAt:now()}));
        draftStatus='saved';writeError='';return true;
      }catch(error){draftStatus='unsaved';writeError=error.message||'Le brouillon ne peut pas être enregistré.';return false;}
    }
    function update(path,value){model.set(draft,path,value);saveDraft();return state();}
    function discard(){draft=clone(baseline);recovering=false;staleDraft=null;saveDraft();return state();}
    function revision(config){return Number.isInteger(config.version)&&config.version>=0?config.version:0;}
    function history(){
      const rows=Array.isArray(baseline.settingsHistory)?baseline.settingsHistory:[];
      const seen=new Set();
      return rows.filter(row=>{
        if(!row||typeof row!=='object'||typeof row.id!=='string'||!Number.isInteger(row.version)||!row.snapshot||typeof row.snapshot!=='object'||Array.isArray(row.snapshot)||seen.has(row.id))return false;
        seen.add(row.id);return true;
      }).slice(0,12).map(row=>({...clone(row),current:row.version===revision(baseline)}));
    }
    function state(){
      return {baseline:clone(baseline),draft:clone(draft),changes:changes(),issues:model.validate(draft),loadIssues:clone(loadIssues),
        readError,writeError,draftStatus,conflict,recovering,initialized:raw!==null,staleDraft:!!staleDraft,dirty:changes().length>0||recovering,version:revision(baseline),history:history()};
    }
    function checkExternal(){try{conflict=read()!==raw;}catch(error){readError=error.message||'Lecture impossible.';}return state();}
    function recover(){
      if(readError)throw new Error('La lecture du stockage doit être rétablie avant de réparer la configuration.');
      // The loaded baseline already contains safe defaults and migration fixes.
      // Keep valid legacy settings, extension metadata and snapshots during repair.
      draft=clone(baseline);recovering=true;saveDraft();return state();
    }
    function rebase(){
      const own=changes();const latest=read(),loaded=model.load(latest);
      if(loaded.issues?.length)throw new Error('La configuration enregistrée doit être réparée avant de fusionner le brouillon.');
      const next=clone(loaded.config),collisions=[];
      for(const change of own){if(JSON.stringify(model.get(next,change.path))!==JSON.stringify(change.before))collisions.push(change.path);model.set(next,change.path,model.get(draft,change.path));}
      raw=latest;baseline=clone(loaded.config);draft=next;loadIssues=[];conflict=false;recovering=false;saveDraft();
      return {state:state(),collisions};
    }
    function stageHistory(id){
      const record=history().find(row=>row.id===id);if(!record)throw new Error('Cet instantané local n’est plus disponible.');
      const loaded=model.load(JSON.stringify(record.snapshot));
      for(const field of fields)model.set(draft,field.path,model.get(loaded.config,field.path));
      saveDraft();return state();
    }
    function restoreStaleDraft(){
      if(!staleDraft)throw new Error('Aucun ancien brouillon disponible.');
      const restored=loadDraft(staleDraft.config);
      for(const field of fields)model.set(draft,field.path,model.get(restored,field.path));
      staleDraft=null;saveDraft();return state();
    }
    function stageImport(text){
      let parsed;try{parsed=JSON.parse(text);}catch{throw new Error('Le fichier ne contient pas un document JSON valide.');}
      if(!parsed||typeof parsed!=='object'||Array.isArray(parsed))throw new Error('Le fichier doit contenir un objet de configuration.');
      if(!fields.some(field=>model.get(parsed,field.path)!==undefined))throw new Error('Aucun réglage WCP360 reconnu dans ce fichier.');
      const loaded=model.load(text);
      if(loaded.issues.length)throw new Error('Le fichier contient des valeurs illisibles : '+loaded.issues[0].message);
      for(const field of fields)if(model.get(parsed,field.path)!==undefined)model.set(draft,field.path,model.get(loaded.config,field.path));
      saveDraft();return state();
    }
    function review(){checkExternal();return state();}
    function snapshot(config){const result=safe(config);delete result.settingsHistory;return result;}
    function publish({reason='',acknowledged=false}={}){
      if(readError)throw new Error('Le stockage local ne peut pas être lu. Aucune révision n’a été enregistrée.');
      const latest=read();if(latest!==raw){conflict=true;throw new Error('La configuration a changé dans un autre onglet. Rechargez-la ou réappliquez votre brouillon.');}
      if(loadIssues.length&&!recovering)throw new Error('Réparez la configuration enregistrée avant de publier une révision.');
      const delta=changes(),validation=model.validate(draft);
      if(validation.length)throw new Error('Corrigez les champs signalés avant d’enregistrer.');
      if(!delta.length&&!recovering)throw new Error('Aucune modification à enregistrer.');
      if(String(reason).trim().length<8)throw new Error('Décrivez la modification en au moins huit caractères.');
      if((recovering||delta.some(change=>change.risk==='high'))&&!acknowledged)throw new Error('Confirmez la revue des réglages sensibles.');
      const date=now(),version=revision(baseline)+1,next=snapshot(draft);
      next.version=version;next.schemaVersion=model.schemaVersion;
      const previous=history();
      if(!previous.some(row=>row.version===revision(baseline)))previous.unshift({id:'baseline-'+revision(baseline)+'-'+token(raw),version:revision(baseline),createdAt:date,reason:'Configuration précédente',snapshot:snapshot(baseline)});
      const entry={id:'settings-'+version+'-'+token(date+model.serialize(next)),version,createdAt:date,reason:String(reason).trim(),changes:delta.map(change=>({path:change.path,label:change.label})),snapshot:snapshot(next)};
      next.settingsHistory=[entry,...previous].slice(0,12).map(({current,...record})=>record);
      next.versions=[{version,actor:'ROOT · démonstration',date,summary:entry.reason,status:'Active'},...(Array.isArray(baseline.versions)?baseline.versions:[]).filter(row=>row&&typeof row==='object').map(row=>({...row,status:'Superseded'}))].slice(0,12);
      const serialized=model.serialize(next);
      try{if(!storage?.setItem)throw new Error('Stockage local indisponible.');storage.setItem(key,serialized);}catch(error){writeError=error.message||'Écriture impossible.';throw new Error('La révision n’a pas été enregistrée. Le brouillon est conservé.');}
      raw=serialized;baseline=model.load(raw).config;draft=clone(baseline);loadIssues=[];recovering=false;conflict=false;writeError='';
      try{storage.removeItem(draftKey);draftStatus='empty';}catch(error){draftStatus='cleanup-error';writeError=error.message||'L’ancien brouillon ne peut pas être supprimé.';}
      return {version,changes:delta.length,entry:clone(entry),state:state()};
    }
    function exportConfig({draft:useDraft=false}={}){return model.serialize(useDraft?draft:baseline);}
    load();
    return {getState:state,update,saveDraft,discard,review,publish,history,stageHistory,stageImport,recover,rebase,restoreStaleDraft,checkExternal,reload(){load();return state();},exportConfig};
  }
  global.WCPSettingsStore=Object.freeze({create,key,draftKey});
})(globalThis);
