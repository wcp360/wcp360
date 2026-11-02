/* Configuration workspace. Every persisted revision remains a local simulation. */
(() => {
  'use strict';
  const host=document.querySelector('#systemSettingsCenter');
  if(!host)return;
  const model=window.WCPSettingsModel,UI=window.WCPUI;
  if(!model||!UI||!window.WCPSettingsStore){host.textContent='Les paramètres n’ont pas pu être chargés. Rechargez la page.';return;}
  const store=window.WCPSettingsStore.create({model});
  const escape=UI.escape,normalize=UI.normalize,sections=model.sections;
  const fields=sections.flatMap(section=>section.groups.flatMap(group=>group.fields.map(field=>({...field,section:section.id}))));
  const byPath=new Map(fields.map(field=>[field.path,field]));
  const aliases={changes:'history',providers:'integrations',runtimes:'integrations',databases:'integrations',mail:'integrations',backups:'integrations',livepatch:'integrations',ipam:'integrations',forensics:'integrations',storage:'integrations',whitelabel:'branding',aiops:'integrations',edge:'integrations',sustainability:'integrations',observability:'integrations',zerotrust:'access',behavior:'governance'};
  const resolve=value=>{const id=String(value||'overview').toLowerCase();return sections.some(section=>section.id===id)||['overview','history'].includes(id)?id:aliases[id]||'overview';};
  const params=new URLSearchParams(location.search);
  let active=resolve(params.get('section')||location.hash.slice(1)||'overview'),query=params.get('q')||'',showErrors=false,feedback=null,recordSequence=0;
  const touched=new Set();
  const icon=name=>`<span class="material-symbols-rounded" aria-hidden="true">${name}</span>`;
  const button=(text,action,kind='',extra='')=>`<button class="ss-button ${kind}" type="button" data-ss-action="${action}" ${extra}>${text}</button>`;
  const idFor=path=>'ss-field-'+path.replace(/\./g,'-');
  const date=value=>{try{return new Intl.DateTimeFormat('fr-FR',{dateStyle:'medium',timeStyle:'short',timeZone:'Europe/Paris'}).format(new Date(value));}catch{return 'Date indisponible';}};
  const display=(value,field)=>field?.type==='collection'?JSON.stringify(value,null,2):field?.type==='checkbox'?(value?'Activé':'Désactivé'):field?.options?.find(option=>option.value===value)?.label??(value===''?'Non défini':String(value??'Non défini'));
  const riskLabel={low:'faible',medium:'modéré',high:'élevé'};
  const plural=(n,single,many=single+'s')=>n+' '+(n===1?single:many);
  const match=(section,group,field)=>normalize(query).trim().split(/\s+/).filter(Boolean).every(word=>normalize([section.title,group.title,field.label,field.help,field.path].join(' ')).includes(word));

  host.innerHTML=`<div class="ss-page">
    <header class="ss-header"><div><p class="ss-eyebrow">${icon('tune')} CENTRE DE CONFIGURATION ROOT</p><h2>Paramètres de la plateforme</h2><p class="ss-intro">Configurez le socle, les services d’hébergement, l’infrastructure et les politiques avancées. Préparez vos changements dans un brouillon, puis comparez et enregistrez leur révision.</p><div class="ss-badges"><span class="ss-status" data-tone="info">ROOT · portée globale</span><span class="ss-status">${sections.length} rubriques · ${fields.length} réglages</span><span class="ss-status">Démonstration locale</span></div></div><div class="ss-header-actions">${button(icon('upload')+'Importer','import')}${button(icon('download')+'Exporter','export')}<a class="ss-button ss-button-quiet" href="setup-wizard.html">${icon('auto_awesome')}Assistant initial</a></div></header>
    <div data-ss-notices></div>
    <section class="ss-overview" aria-label="État de la configuration">${[['history','Révision enregistrée','version','Aucune commande serveur exécutée'],['edit_note','Modifications du brouillon','changes','Comparées à la révision enregistrée'],['fact_check','Contrôles à corriger','issues','Valeurs et dépendances vérifiées'],['settings','Réglages disponibles','fields',sections.length+' rubriques de configuration']].map(([symbol,label,key,help])=>`<article class="ss-overview-card"><span class="ss-card-icon">${icon(symbol)}</span><div><p class="ss-metric-label">${label}</p><p class="ss-metric-value" data-ss-metric="${key}">—</p><p class="ss-metric-help">${help}</p></div></article>`).join('')}</section>
    <div class="ss-layout"><aside class="ss-sidebar" aria-label="Rubriques des paramètres"><div class="ss-searchbar">${icon('search')}<label class="ss-sr-only" for="ss-settings-search">Rechercher un réglage dans toutes les rubriques</label><input id="ss-settings-search" type="search" placeholder="Rechercher un réglage…" data-ss-search autocomplete="off"><button type="button" data-ss-action="clear-search" aria-label="Effacer la recherche">${icon('close')}</button></div><div class="ss-mobile-select"><label for="ss-section-select">Rubrique</label><select id="ss-section-select" data-ss-section-select></select></div><nav class="ss-nav" aria-label="Navigation des paramètres" data-ss-nav></nav><p class="ss-help">Les brouillons sont conservés dans ce navigateur. Aucun changement n’est appliqué au serveur.</p></aside>
    <section class="ss-workspace" id="ss-workspace" aria-labelledby="ss-section-title"><div class="ss-error-summary" data-ss-error-summary hidden></div><div data-ss-view></div></section></div>
    <footer class="ss-savebar" data-ss-savebar data-dirty="false"><div><strong data-ss-dirty-label role="status" aria-live="polite">Aucune modification</strong><p data-ss-draft-status></p></div><div class="ss-header-actions">${button('Annuler les modifications','discard','ss-button-quiet')}${button(icon('difference')+'Revoir les modifications','review','ss-button-primary')}</div></footer>
  </div>`;
  const qs=selector=>host.querySelector(selector),qsa=selector=>[...host.querySelectorAll(selector)];
  qs('[data-ss-search]').value=query;

  function notice(text,tone='info'){feedback={text,tone};renderNotices();}
  function renderNotices(){
    const state=store.getState(),rows=[];
    if(feedback)rows.push(`<div class="ss-callout" data-tone="${feedback.tone}" role="status"><p>${escape(feedback.text)}</p></div>`);
    if(state.readError)rows.push(`<div class="ss-callout" data-tone="danger" role="alert"><strong>Stockage local indisponible</strong><p>La configuration est consultable. Aucune révision ne peut être enregistrée tant que le stockage reste inaccessible.</p>${button('Réessayer la lecture','reload')}</div>`);
    else if(state.loadIssues.length&&!state.recovering)rows.push(`<div class="ss-callout" data-tone="warning"><strong>Configuration enregistrée à vérifier</strong><p>${escape(state.loadIssues[0].message)} Les données d’origine restent conservées jusqu’à une réparation explicitement revue.</p>${button('Préparer la configuration réparée','recover')}</div>`);
    if(state.conflict)rows.push(`<div class="ss-callout" data-tone="warning" role="alert"><strong>Une autre révision a été enregistrée</strong><p>Votre brouillon est conservé. Rechargez la configuration ou réappliquez vos modifications sur la dernière révision avant de les revoir.</p><div class="ss-header-actions">${button('Recharger la révision','reload')}${button('Réappliquer mon brouillon','rebase')}</div></div>`);
    if(state.staleDraft)rows.push(`<div class="ss-callout" data-tone="warning"><strong>Un brouillon plus ancien est disponible</strong><p>Il n’a pas été chargé automatiquement car sa révision de départ a changé.</p>${button('Préparer cet ancien brouillon','restore-draft')}</div>`);
    if(state.draftStatus==='invalid')rows.push(`<div class="ss-callout" data-tone="warning" role="alert"><strong>Le brouillon enregistré est illisible</strong><p>Il n’a pas été chargé. La configuration enregistrée reste intacte.</p>${button('Supprimer ce brouillon','clear-draft')}</div>`);
    if(state.draftStatus==='cleanup-error')rows.push(`<div class="ss-callout" data-tone="danger" role="alert"><strong>L’ancien brouillon n’a pas pu être supprimé</strong><p>La configuration enregistrée reste intacte. Cet ancien brouillon pourrait réapparaître au rechargement.</p>${button('Réessayer la suppression','clear-draft')}</div>`);
    if(state.draftStatus==='unsaved')rows.push(`<div class="ss-callout" data-tone="danger" role="alert"><strong>Le brouillon reste uniquement en mémoire</strong><p>L’écriture locale a échoué. Conservez cet onglet ouvert ou exportez le brouillon avant de quitter la page.</p>${button('Exporter le brouillon','export-draft')}</div>`);
    qs('[data-ss-notices]').innerHTML=rows.join('');
  }
  function renderNav(){
    const state=store.getState(),overview={id:'overview',title:'Vue d’ensemble',icon:'dashboard',groups:[]},historyItem={id:'history',title:'Historique des révisions',icon:'history',groups:[]},items=[overview,...model.navigationGroups.flatMap(group=>group.sections.map(id=>sections.find(section=>section.id===id))),historyItem];
    const navItem=section=>{
      const count=state.changes.filter(change=>change.section===section.id).length,errors=state.issues.filter(issue=>issue.section===section.id).length;
      return `<button type="button" class="ss-nav-item" data-ss-section="${section.id}" ${active===section.id&&!query?'aria-current="page"':''} aria-controls="ss-workspace">${icon(section.icon)}<span>${escape(section.title)}</span>${errors?`<span class="ss-nav-count" aria-label="${errors} erreurs">${errors}</span>`:count?`<span class="ss-nav-count" aria-label="${count} modifications">${count}</span>`:''}</button>`;
    };
    qs('[data-ss-nav]').innerHTML=navItem(overview)+model.navigationGroups.map(group=>`<div class="ss-nav-group"><p class="ss-nav-label">${escape(group.title)}</p>${group.sections.map(id=>navItem(sections.find(section=>section.id===id))).join('')}</div>`).join('')+'<div class="ss-nav-group"><p class="ss-nav-label">Révisions locales</p>'+navItem(historyItem)+'</div>';
    qs('[data-ss-section-select]').innerHTML=items.map(section=>`<option value="${section.id}" ${active===section.id?'selected':''}>${escape(section.title)}</option>`).join('');
  }
  function renderField(field,state){
    if(field.type==='collection')return renderCollection(field,state);
    const value=model.get(state.draft,field.path),id=idFor(field.path),changed=state.changes.some(change=>change.path===field.path);
    const messages=state.issues.filter(issue=>issue.path===field.path).map(issue=>issue.message),invalid=messages.length>0&&(showErrors||touched.has(field.path));
    const common=`id="${id}" data-ss-path="${field.path}" name="${field.path}" aria-describedby="${id}-help ${id}-error" aria-invalid="${invalid}" ${field.required?'required':''}`;
    let control;
    if(field.type==='checkbox')control=`<label class="ss-toggle" for="${id}"><span class="ss-field-label">${escape(field.label)}</span><input type="checkbox" ${common} ${value?'checked':''}></label>`;
    else{
      const label=`<label class="ss-field-label" for="${id}">${escape(field.label)}${field.required?' <span aria-hidden="true">*</span>':''}</label>`;
      if(field.type==='select')control=label+`<select ${common}>${field.options.map(option=>`<option value="${escape(option.value)}" ${option.value===value?'selected':''}>${escape(option.label)}</option>`).join('')}</select>`;
      else if(field.type==='textarea')control=label+`<textarea ${common} rows="3" maxlength="4000" spellcheck="false">${escape(value)}</textarea>`;
      else control=label+`<div class="ss-input-wrap"><input type="${field.type}" ${common} value="${escape(value)}" ${field.type==='number'?`min="${field.min}" max="${field.max}" step="${field.step||1}"`:'maxlength="512"'} ${['email','url'].includes(field.type)?'autocapitalize="none" spellcheck="false"':''}>${field.unit?`<span class="ss-unit">${escape(field.unit)}</span>`:''}</div>`;
    }
    return `<div class="ss-field" data-ss-field="${field.path}" data-invalid="${invalid}">${control}<p class="ss-help" id="${id}-help">${escape(field.help||(field.unit?'Valeur exprimée en '+field.unit+'.':''))}</p><p class="ss-field-error" id="${id}-error" ${invalid?'':'hidden'}>${escape(messages.join(' '))}</p><div class="ss-field-meta"><span class="ss-changed-marker" ${changed?'':'hidden'}>Modifié</span><button type="button" class="ss-button ss-button-quiet" data-ss-reset-field="${field.path}" ${changed?'':'hidden'} aria-label="Rétablir ${escape(field.label)}">Rétablir</button></div></div>`;
  }
  function renderOverview(state){
    const config=state.draft;
    const summaries=[['dns','Serveur',config.identity.hostname,'server'],['code_blocks','Pile web',config.runtime.webEngine+' · PHP '+config.runtime.php,'runtimes'],['backup','Protection des données',config.backups.enabled?config.backups.schedule+' · '+config.backups.retentionDays+' jours':'Politique désactivée','backups'],['hub','Connecteurs déclarés',String(config.providers.length)+' connecteur(s) · '+config.storage.targets.length+' destination(s)','providers']];
    return `<div class="ss-section-head"><div><p class="ss-eyebrow">PILOTER LA CONFIGURATION</p><h2 id="ss-section-title" tabindex="-1">Vue d’ensemble</h2><p>Retrouvez tous les réglages par domaine. Les aperçus reflètent votre brouillon ; les révisions enregistrées restent accessibles dans l’historique.</p></div>${button('Voir l’historique','show-history','ss-button-quiet')}</div>
      <div class="ss-config-summary">${summaries.map(([symbol,label,value,id])=>`<button type="button" class="ss-summary-item" data-ss-section="${id}">${icon(symbol)}<span><small>${escape(label)}</small><strong>${escape(value)}</strong></span>${icon('arrow_forward')}</button>`).join('')}</div>
      ${model.navigationGroups.map(group=>`<section class="ss-domain" aria-labelledby="ss-domain-${group.sections[0]}"><div class="ss-domain-head"><h3 id="ss-domain-${group.sections[0]}">${escape(group.title)}</h3><span>${group.sections.length} rubriques</span></div><div class="ss-category-grid">${group.sections.map(id=>{
        const section=sections.find(item=>item.id===id),count=section.groups.flatMap(item=>item.fields).length,changed=state.changes.filter(item=>item.section===id).length,errors=state.issues.filter(item=>item.section===id).length;
        return `<button type="button" class="ss-category" data-ss-section="${id}"><span class="ss-category-icon">${icon(section.icon)}</span><span class="ss-category-content"><strong>${escape(section.title)}</strong><span>${escape(section.description)}</span><small>${count} réglages${errors?' · '+errors+' contrôle(s) à corriger':changed?' · '+changed+' modification(s)':''}</small></span>${icon('chevron_right')}</button>`;
      }).join('')}</div></section>`).join('')}`;
  }
  function renderCollection(field,state){
    const records=model.get(state.draft,field.path),id=idFor(field.path),messages=state.issues.filter(issue=>issue.path===field.path).map(issue=>issue.message),invalid=messages.length>0&&(showErrors||touched.has(field.path)),changed=state.changes.some(change=>change.path===field.path);
    return `<div class="ss-field ss-wide ss-collection" data-ss-field="${field.path}" data-invalid="${invalid}"><div class="ss-collection-head"><label class="ss-field-label" for="${id}">${escape(field.label)} <span class="ss-status">${records.length} entrée(s)</span></label><button class="ss-button" id="${id}" type="button" data-ss-path="${field.path}" data-ss-add="${field.path}" aria-describedby="${id}-help ${id}-error" aria-invalid="${invalid}">${icon('add')}Ajouter une entrée</button></div><p class="ss-help" id="${id}-help">${escape(field.help)}</p><p class="ss-field-error" id="${id}-error" ${invalid?'':'hidden'}>${escape(messages.join(' '))}</p>
      <div class="ss-record-list">${records.length?records.map((record,index)=>`<article class="ss-record"><div class="ss-record-copy"><strong>${escape(record.name||record.id)}</strong><p>${escape([record.kind,record.endpoint,record.cidr,record.bucket,record.region].filter(Boolean).join(' · '))}</p><small>${escape(record.id)}</small></div><span class="ss-status" ${record.enabled?'data-tone="info"':''}>${record.enabled?'Activé dans le brouillon':'Désactivé'}</span><div class="ss-record-actions"><button class="ss-button ss-button-quiet" type="button" data-ss-edit="${field.path}" data-ss-record-index="${index}" aria-label="Modifier ${escape(record.name||record.id)}">Modifier</button><button class="ss-button ss-button-quiet" type="button" data-ss-remove="${field.path}" data-ss-record-index="${index}" aria-label="Retirer ${escape(record.name||record.id)}">Retirer</button></div></article>`).join(''):`<div class="ss-record-empty">${icon('add_circle')}<strong>Aucune entrée configurée</strong><p>Ajoutez une première entrée avec ses paramètres et sa référence de clé si nécessaire.</p></div>`}</div>
      <div class="ss-field-meta"><span class="ss-changed-marker" ${changed?'':'hidden'}>Modifié</span><button type="button" class="ss-button ss-button-quiet" data-ss-reset-field="${field.path}" ${changed?'':'hidden'} aria-label="Rétablir ${escape(field.label)}">Rétablir</button></div></div>`;
  }
  function openCollection(path,index){
    const field=byPath.get(path);if(field?.type!=='collection')return;
    const records=structuredClone(model.get(store.getState().draft,path)),editing=Number.isInteger(index)&&index>=0&&index<records.length;
    if(!editing&&records.length>=100){notice('Cette liste est limitée à cent entrées.','warning');return;}
    const record=editing?records[index]:{},content=document.createElement('div');
    content.innerHTML=`<p class="ss-help">${escape(field.label)} · les modifications seront ajoutées au brouillon, puis soumises à votre revue. Aucun test de connexion n’est exécuté.</p><form><div class="ss-grid">${field.columns.map(column=>{
      const id='ss-record-'+path.replace(/\./g,'-')+'-'+column.key,value=record[column.key]??(column.type==='checkbox'?true:column.type==='number'?column.min||0:column.type==='select'?column.options[0]:'');
      const attrs=`id="${id}" name="${column.key}" ${column.required?'required':''}`;
      if(column.type==='checkbox')return `<div class="ss-field"><label class="ss-toggle" for="${id}"><span>${escape(column.label)}</span><input ${attrs} type="checkbox" ${value?'checked':''}></label></div>`;
      return `<div class="ss-field"><label class="ss-field-label" for="${id}">${escape(column.label)}${column.required?' *':''}</label>${column.type==='select'?`<select ${attrs}>${column.options.map(option=>`<option ${value===option?'selected':''}>${escape(option)}</option>`).join('')}</select>`:`<input ${attrs} type="${column.type||'text'}" value="${escape(value)}" ${column.type==='number'?`min="${column.min}" max="${column.max}" step="1"`:'maxlength="512"'}>`}${column.help?`<p class="ss-help">${escape(column.help)}</p>`:''}</div>`;
    }).join('')}</div><p data-ss-record-feedback role="alert" hidden></p><div class="ss-review-actions"><button type="button" class="ss-button" data-ss-dialog-cancel>Annuler</button><button type="submit" class="ss-button ss-button-primary">${editing?'Modifier':'Ajouter'} au brouillon</button></div></form>`;
    const dialog=UI.Dialog.open({title:editing?'Modifier une entrée':'Ajouter une entrée',content});dialog.element.classList.add('ss-review-dialog');content.querySelector('[data-ss-dialog-cancel]').addEventListener('click',()=>dialog.close());
    const form=content.querySelector('form');
    form.addEventListener('submit',event=>{
      event.preventDefault();if(!form.reportValidity())return;
      const next={...record};for(const column of field.columns){const input=form.elements[column.key];next[column.key]=column.type==='checkbox'?input.checked:column.type==='number'?input.value===''?'':Number(input.value):input.value.trim();}
      if(!next.id)next.id='setting-'+Date.now().toString(36)+'-'+(++recordSequence);
      const proposed=structuredClone(records);if(editing)proposed[index]=next;else proposed.push(next);
      const messages=model.validateCollection(path,proposed),feedbackNode=content.querySelector('[data-ss-record-feedback]');
      if(messages.length){feedbackNode.hidden=false;feedbackNode.textContent=messages.join(' ');feedbackNode.className='ss-callout';feedbackNode.dataset.tone='danger';return;}
      store.update(path,proposed);touched.add(path);dialog.close();renderWorkspace();updateStatus();qs('#'+idFor(path))?.focus();
    });
  }
  async function removeCollection(path,index){
    const field=byPath.get(path),records=structuredClone(model.get(store.getState().draft,path));if(field?.type!=='collection'||!Number.isInteger(index)||!records[index])return;
    if(!await UI.Dialog.confirm({title:'Retirer cette entrée ?',message:'L’entrée « '+(records[index].name||records[index].id)+' » sera retirée du brouillon. La configuration enregistrée reste inchangée jusqu’à la revue.',confirmLabel:'Retirer du brouillon'}))return;
    records.splice(index,1);store.update(path,records);touched.add(path);renderWorkspace();updateStatus();qs('#'+idFor(path))?.focus();
  }
  function renderHistory(state){
    return `<div class="ss-section-head"><div><p class="ss-eyebrow">TRAÇABILITÉ LOCALE</p><h2 id="ss-section-title" tabindex="-1">Historique des révisions</h2><p>Les instantanés proviennent des enregistrements effectués ici. Restaurer prépare un brouillon à revoir.</p></div><span class="ss-status">${state.initialized?'Révision active : v'+state.version:'Aucune révision locale'}</span></div>${state.history.length?`<div class="ss-table-wrap"><table class="ss-history-table"><caption class="ss-sr-only">Révisions de la configuration locale</caption><thead><tr><th scope="col">Révision</th><th scope="col">Date</th><th scope="col">Motif</th><th scope="col">Action</th></tr></thead><tbody>${state.history.map(row=>`<tr><th scope="row">v${row.version}${row.current?' · active':''}</th><td>${escape(date(row.createdAt))}</td><td>${escape(row.reason||'Configuration enregistrée')}</td><td><button class="ss-button" type="button" data-ss-restore="${escape(row.id)}" ${row.current?'disabled':''}>Préparer la restauration<span class="ss-sr-only"> de la révision ${row.version}</span></button></td></tr>`).join('')}</tbody></table></div>`:`<div class="ss-empty">${icon('history')}<h3>Aucune révision enregistrée ici</h3><p>Le premier enregistrement créera un instantané de la nouvelle configuration et de son état précédent.</p></div>`}<div class="ss-callout" data-tone="info"><p>Les anciennes listes de versions sans instantané ne sont pas présentées comme restaurables. Les configurations des centres spécialisés ne sont pas restaurées depuis cette page.</p></div>`;
  }
  function renderWorkspace(focus=false){
    const state=store.getState(),section=sections.find(section=>section.id===active);
    if(active==='overview'&&!query)qs('[data-ss-view]').innerHTML=renderOverview(state);
    else if(active==='history'&&!query)qs('[data-ss-view]').innerHTML=renderHistory(state);
    else{
      const candidates=query?sections:section?[section]:[],groups=candidates.flatMap(area=>area.groups.map(group=>({area,group,fields:group.fields.filter(field=>!query||match(area,group,field))}))).filter(group=>group.fields.length);
      const count=groups.reduce((total,group)=>total+group.fields.length,0);
      const heading=query?`<p class="ss-eyebrow">RECHERCHE DANS TOUS LES RÉGLAGES</p><h2 id="ss-section-title" tabindex="-1">${plural(count,'résultat')} pour « ${escape(query)} »</h2><p>Les champs trouvés peuvent être modifiés directement.</p>`:`<p class="ss-eyebrow">RÉGLAGES GLOBAUX</p><h2 id="ss-section-title" tabindex="-1">${escape(section.title)}</h2><p>${escape(section.description)}</p>`;
      qs('[data-ss-view]').innerHTML=`<div class="ss-section-head"><div>${heading}</div>${query?button('Effacer la recherche','clear-search'):''}</div>${!count?`<div class="ss-empty">${icon('search_off')}<h3>Aucun réglage trouvé</h3><p>Essayez « session », « mémoire » ou « contact ».</p>${button('Afficher les réglages','clear-search')}</div>`:`<form class="ss-form" data-ss-settings-form novalidate>${groups.map(({area,group,fields})=>`<section class="ss-group" aria-labelledby="ss-group-${area.id}-${group.id}"><div class="ss-group-head"><div>${query?`<button class="ss-button ss-button-quiet" type="button" data-ss-section="${area.id}">${escape(area.title)}</button>`:''}<h3 id="ss-group-${area.id}-${group.id}">${escape(group.title)}</h3>${group.description?`<p>${escape(group.description)}</p>`:''}</div></div><div class="ss-group-body"><div class="ss-grid ss-fields">${fields.map(field=>renderField(field,state)).join('')}</div></div></section>`).join('')}<button hidden type="submit">Revoir les modifications</button></form>`}${!query&&section.links?.length?`<section class="ss-group"><div class="ss-group-head"><div><h3>Continuer dans les centres spécialisés</h3><p>Les inventaires et les opérations sont gérés dans leurs espaces dédiés.</p></div></div><div class="ss-owner-links">${section.links.map(link=>`<a class="ss-owner-link" href="${escape(link.href)}"><span>${icon('open_in_new')}</span><div><strong>${escape(link.label)}</strong><p>${escape(link.description)}</p></div>${icon('arrow_forward')}</a>`).join('')}</div></section>`:''}`;
    }
    renderErrorSummary();if(focus)qs('#ss-section-title')?.focus({preventScroll:true});
  }
  function renderErrorSummary(){
    const state=store.getState(),node=qs('[data-ss-error-summary]');node.hidden=!showErrors||!state.issues.length;
    node.innerHTML=showErrors&&state.issues.length?`<strong>${plural(state.issues.length,'contrôle à corriger','contrôles à corriger')}</strong><ul>${state.issues.map(issue=>`<li><a href="#${idFor(issue.path)}" data-ss-focus="${escape(issue.path)}">${escape(byPath.get(issue.path)?.label||'Configuration')} : ${escape(issue.message)}</a></li>`).join('')}</ul>`:'';
  }
  function updateStatus(){
    const state=store.getState();
    for(const [name,value] of Object.entries({version:state.initialized?'v'+state.version:'Référence',changes:state.changes.length,issues:state.issues.length,fields:fields.length}))qs(`[data-ss-metric="${name}"]`).textContent=value;
    qs('[data-ss-savebar]').dataset.dirty=String(state.dirty);
    qs('[data-ss-dirty-label]').textContent=state.recovering?'Réparation de la configuration à revoir':state.dirty?plural(state.changes.length,'modification en attente','modifications en attente'):state.initialized?'Configuration enregistrée · aucune modification':'Configuration de référence · aucune révision enregistrée';
    qs('[data-ss-draft-status]').textContent=state.draftStatus==='unsaved'?'Brouillon non sauvegardé — exportez-le avant de quitter.':state.dirty?'Brouillon local · la révision enregistrée reste inchangée.':'Les modifications nécessitent une revue avant enregistrement.';
    qs('[data-ss-action="discard"]').disabled=!state.dirty;qs('[data-ss-action="review"]').disabled=!state.dirty||!!state.readError;
    qsa('[data-ss-field]').forEach(wrapper=>{
      const path=wrapper.dataset.ssField,messages=state.issues.filter(issue=>issue.path===path).map(issue=>issue.message),invalid=messages.length>0&&(showErrors||touched.has(path)),changed=state.changes.some(change=>change.path===path);
      wrapper.dataset.invalid=String(invalid);wrapper.querySelector('[data-ss-path]')?.setAttribute('aria-invalid',String(invalid));const error=wrapper.querySelector('.ss-field-error');error.hidden=!invalid;error.textContent=messages.join(' ');wrapper.querySelector('.ss-changed-marker').hidden=!changed;wrapper.querySelector('[data-ss-reset-field]').hidden=!changed;
    });
    renderNav();renderNotices();renderErrorSummary();
  }
  function updateUrl(){const url=new URL(location.href);url.searchParams.delete('section');if(query)url.searchParams.set('q',query);else url.searchParams.delete('q');url.hash=active;history.replaceState(null,'',url);}
  function navigate(id,focus=true){active=resolve(id);query='';qs('[data-ss-search]').value='';updateUrl();renderWorkspace(focus);updateStatus();}
  const valueFrom=input=>input.type==='checkbox'?input.checked:input.type==='number'?input.value===''?'':Number(input.value):input.value;
  function diffMarkup(changes){return `<div class="ss-review-list">${changes.map(change=>`<article class="ss-diff"><div class="ss-diff-row"><div><strong>${escape(change.label)}</strong><p>${escape(sections.find(section=>section.id===change.section)?.title||'Configuration')} · impact ${riskLabel[change.risk]}</p></div><code>${escape(display(change.before,byPath.get(change.path)))}</code>${icon('arrow_forward')}<code>${escape(display(change.after,byPath.get(change.path)))}</code></div></article>`).join('')}</div>`;}
  function openReview(){
    const state=store.review();showErrors=true;updateStatus();
    if(!state.dirty){notice('Aucune modification à enregistrer.');return;}
    if(state.conflict){notice('La configuration a changé dans un autre onglet. Résolvez le conflit avant de poursuivre.','warning');return;}
    if(state.readError||state.loadIssues.length&&!state.recovering){notice('La configuration enregistrée doit être vérifiée avant de poursuivre.','warning');return;}
    if(state.issues.length){const first=state.issues[0];navigate(first.section);qs('#'+idFor(first.path))?.focus();return;}
    const sensitive=state.recovering||state.changes.some(change=>change.risk==='high'),content=document.createElement('div');
    content.innerHTML=`<div class="ss-review-head"><p>${state.initialized?'Révision v'+state.version:'Configuration de référence'} → v${state.version+1} · ${plural(state.changes.length,'réglage modifié')}</p><p>Cette opération enregistre uniquement une configuration de démonstration dans ce navigateur.</p></div><div class="ss-review-body">${state.recovering?'<p class="ss-callout" data-tone="warning">Les données enregistrées illisibles seront remplacées par la configuration revue.</p>':''}${diffMarkup(state.changes)}<form><label class="ss-field"><span class="ss-field-label">Motif de la modification</span><textarea name="reason" required minlength="8" maxlength="500" rows="3" placeholder="Ex. : ajuster les quotas des nouveaux comptes"></textarea><span class="ss-help">Au moins huit caractères. Ce motif figure dans l’historique local.</span></label>${sensitive?'<label class="ss-check"><input type="checkbox" name="acknowledged" required><span>J’ai revu les réglages sensibles et leurs conséquences.</span></label>':''}<p data-ss-review-feedback role="alert" hidden></p><div class="ss-review-actions"><button class="ss-button" type="button" data-ss-dialog-cancel>Revenir au brouillon</button><button class="ss-button ss-button-primary" type="submit">Enregistrer la révision locale</button></div></form></div>`;
    const dialog=UI.Dialog.open({title:'Revoir les modifications',content});dialog.element.classList.add('ss-review-dialog');content.querySelector('[data-ss-dialog-cancel]').addEventListener('click',()=>dialog.close());
    const form=content.querySelector('form');let pending=false;
    form.addEventListener('submit',event=>{
      event.preventDefault();if(pending||!form.reportValidity())return;pending=true;const submit=form.querySelector('[type="submit"]');submit.disabled=true;
      try{
        const result=store.publish({reason:form.elements.reason.value,acknowledged:!!form.elements.acknowledged?.checked});dialog.close(true);touched.clear();showErrors=false;
        let tracked=false;if(window.WCPRuntime){document.dispatchEvent(new CustomEvent('wcp:operation',{detail:{name:'Paramètres système · révision '+result.version,scope:'root',kind:'system-settings',risk:sensitive?'high':'medium',actor:'ROOT · démonstration'}}));tracked=true;}
        navigate('history');notice('Révision v'+result.version+' enregistrée localement.'+(tracked?' Une tâche de suivi simulée a été créée.':' Aucun service système n’a été modifié.'),'success');
      }catch(error){const node=content.querySelector('[data-ss-review-feedback]');node.hidden=false;node.textContent=error.message;node.className='ss-callout';node.dataset.tone='danger';updateStatus();}
      finally{pending=false;submit.disabled=false;}
    });
  }
  function exportConfig(draft=false){try{UI.download(store.exportConfig({draft}),`wcp360-parametres-${draft?'brouillon':'v'+store.getState().version}.json`,'application/json');notice((draft?'Brouillon exporté':'Configuration enregistrée exportée')+' · les identifiants secrets sont retirés.','success');}catch(error){notice('L’export a échoué : '+error.message,'danger');}}
  function openImport(){
    const content=document.createElement('div');content.innerHTML='<form><p class="ss-help">Sélectionnez un export JSON WCP360 (2 Mio maximum). Les réglages reconnus sont ajoutés au brouillon ; aucune révision n’est appliquée sans revue.</p><label class="ss-field"><span class="ss-field-label">Fichier de configuration</span><input name="configuration" type="file" accept=".json,application/json" required></label><p data-ss-import-error role="alert" hidden></p><div class="ss-review-actions"><button type="button" class="ss-button" data-ss-dialog-cancel>Annuler</button><button type="submit" class="ss-button ss-button-primary">Préparer le brouillon</button></div></form>';
    const dialog=UI.Dialog.open({title:'Importer des réglages',content});dialog.element.classList.add('ss-review-dialog');content.querySelector('[data-ss-dialog-cancel]').addEventListener('click',()=>dialog.close());
    const form=content.querySelector('form');let busy=false;
    form.addEventListener('submit',async event=>{event.preventDefault();if(busy||!form.reportValidity())return;const file=form.elements.configuration.files[0];if(!file)return;const submit=form.querySelector('[type="submit"]');busy=true;submit.disabled=true;
      try{if(file.size>2097152)throw new Error('Le fichier dépasse la limite de 2 Mio.');const text=await file.text();if(!dialog.element.isConnected)return;store.stageImport(text);dialog.close();showErrors=true;renderWorkspace();updateStatus();notice('Import préparé dans le brouillon. Vérifiez les contrôles puis revoyez les modifications.','success');}
      catch(error){const node=content.querySelector('[data-ss-import-error]');node.hidden=false;node.textContent=error.message;}
      finally{busy=false;submit.disabled=false;}
    });
  }
  async function act(action){
    if(action==='show-history'){navigate('history');return;}
    if(action==='review'){openReview();return;}
    if(action==='export'||action==='export-draft'){exportConfig(action==='export-draft');return;}
    if(action==='import'){openImport();return;}
    if(action==='clear-search'){query='';qs('[data-ss-search]').value='';updateUrl();renderWorkspace();updateStatus();qs('[data-ss-search]').focus();return;}
    if(action==='clear-draft'){
      if(!await UI.Dialog.confirm({title:'Supprimer le brouillon enregistré ?',message:'Seul le brouillon de ce navigateur sera supprimé. La configuration enregistrée reste disponible.',confirmLabel:'Supprimer le brouillon'}))return;
      store.discard();touched.clear();showErrors=false;feedback=null;
    }
    if(action==='discard'){
      if(!store.getState().dirty||!await UI.Dialog.confirm({title:'Annuler ce brouillon ?',message:'Les modifications du brouillon seront abandonnées. La révision enregistrée reste disponible.',confirmLabel:'Annuler les modifications'}))return;
      store.discard();touched.clear();showErrors=false;feedback=null;
    }
    if(action==='reload'){
      if(store.getState().dirty&&!await UI.Dialog.confirm({title:'Recharger la révision enregistrée ?',message:'Le brouillon sera abandonné au profit de la dernière révision locale.',confirmLabel:'Recharger'}))return;
      store.discard();store.reload();touched.clear();showErrors=false;feedback=null;
    }
    if(action==='recover'){
      if(!await UI.Dialog.confirm({title:'Préparer la configuration réparée ?',message:'Les valeurs lisibles sont conservées et les données invalides sont normalisées dans un brouillon. La configuration d’origine sera remplacée uniquement après votre revue.',confirmLabel:'Préparer le brouillon'}))return;
      store.recover();showErrors=true;feedback={text:'Configuration réparée préparée. Revoyez les réglages avant d’enregistrer.',tone:'warning'};
    }
    if(action==='rebase'){
      if(!await UI.Dialog.confirm({title:'Réappliquer votre brouillon ?',message:'Les champs modifiés seront reportés sur la dernière révision. Le résultat devra être revu avant enregistrement.',confirmLabel:'Réappliquer mes modifications'}))return;
      const result=store.rebase();feedback={text:result.collisions.length?plural(result.collisions.length,'réglage également modifié ailleurs','réglages également modifiés ailleurs')+' : votre valeur est proposée. Vérifiez le comparatif.':'Brouillon réappliqué sur la dernière révision.',tone:'warning'};
    }
    if(action==='restore-draft'){
      if(!await UI.Dialog.confirm({title:'Charger l’ancien brouillon ?',message:'Les réglages de cet ancien brouillon seront proposés sur la révision actuelle. Rien ne sera enregistré sans revue.',confirmLabel:'Préparer l’ancien brouillon'}))return;
      store.restoreStaleDraft();showErrors=true;feedback={text:'Ancien brouillon préparé. Vérifiez chaque modification avant l’enregistrement.',tone:'warning'};
    }
    renderWorkspace();updateStatus();
  }
  host.addEventListener('click',event=>{
    const action=event.target.closest('[data-ss-action]');if(action){act(action.dataset.ssAction).catch(error=>notice(error.message,'danger'));return;}
    const nav=event.target.closest('[data-ss-section]');if(nav){navigate(nav.dataset.ssSection);return;}
    const add=event.target.closest('[data-ss-add]');if(add){openCollection(add.dataset.ssAdd);return;}
    const edit=event.target.closest('[data-ss-edit]');if(edit){openCollection(edit.dataset.ssEdit,Number(edit.dataset.ssRecordIndex));return;}
    const remove=event.target.closest('[data-ss-remove]');if(remove){removeCollection(remove.dataset.ssRemove,Number(remove.dataset.ssRecordIndex)).catch(error=>notice(error.message,'danger'));return;}
    const reset=event.target.closest('[data-ss-reset-field]');if(reset){const path=reset.dataset.ssResetField;store.update(path,model.get(store.getState().baseline,path));renderWorkspace();updateStatus();qs('#'+idFor(path))?.focus();return;}
    const error=event.target.closest('[data-ss-focus]');if(error){event.preventDefault();const path=error.dataset.ssFocus;navigate(byPath.get(path)?.section);qs('#'+idFor(path))?.focus();return;}
    const restore=event.target.closest('[data-ss-restore]');if(restore){UI.Dialog.confirm({title:'Préparer une restauration ?',message:'Les réglages de cet instantané seront proposés dans un brouillon. Les autres centres ne sont pas modifiés.',confirmLabel:'Préparer la restauration'}).then(ok=>{if(!ok)return;store.stageHistory(restore.dataset.ssRestore);showErrors=true;navigate('server');notice('Restauration préparée dans le brouillon. Revoyez le comparatif avant d’enregistrer.','warning');}).catch(error=>notice(error.message,'danger'));}
  });
  host.addEventListener('input',event=>{
    if(event.target.matches('[data-ss-search]')){query=event.target.value;updateUrl();renderWorkspace();updateStatus();return;}
    const input=event.target.closest('[data-ss-path]');if(!input)return;
    try{touched.add(input.dataset.ssPath);store.update(input.dataset.ssPath,valueFrom(input));feedback=null;updateStatus();}catch(error){notice(error.message,'danger');}
  });
  host.addEventListener('change',event=>{
    if(event.target.matches('[data-ss-section-select]')){navigate(event.target.value);return;}
    if(event.target.matches('select[data-ss-path],input[type="checkbox"][data-ss-path]')){touched.add(event.target.dataset.ssPath);store.update(event.target.dataset.ssPath,valueFrom(event.target));updateStatus();}
  });
  host.addEventListener('submit',event=>{if(event.target.matches('[data-ss-settings-form]')){event.preventDefault();openReview();}});
  qs('[data-ss-search]').addEventListener('keydown',event=>{if(event.key==='Escape'&&query){event.preventDefault();act('clear-search');}});
  window.addEventListener('hashchange',()=>{active=resolve(location.hash.slice(1));query=new URLSearchParams(location.search).get('q')||'';qs('[data-ss-search]').value=query;renderWorkspace();updateStatus();});
  window.addEventListener('storage',event=>{if(event.key!==window.WCPSettingsStore.key)return;store.checkExternal();if(!store.getState().dirty){store.reload();renderWorkspace();}updateStatus();});
  window.addEventListener('beforeunload',event=>{const state=store.getState();if(state.dirty&&state.draftStatus==='unsaved'){event.preventDefault();event.returnValue='';}});
  renderWorkspace();updateStatus();
  window.WCPSystemSettings=Object.freeze({getState:()=>store.getState(),navigate});
})();
