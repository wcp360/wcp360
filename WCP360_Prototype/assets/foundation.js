/* WCP360 UI Foundation 1.1.1 — standalone, no private dependencies. */
(function(global){
  'use strict';
  if(global.WCPUI)return;
  const doc=global.document;
  const clone=value=>structuredClone(value);
  const normalize=value=>String(value??'').normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase();
  const escape=value=>String(value??'').replace(/[&<>"']/g,char=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[char]));
  let sequence=0;
  const uid=prefix=>`${prefix}-${++sequence}`;
  const node=(tag,className,text)=>{const element=doc.createElement(tag);if(className)element.className=className;if(text!==undefined)element.textContent=text;return element;};
  const button=(label,attrs={})=>{const element=node('button','wcp-ui-button',label);element.type='button';Object.entries(attrs).forEach(([key,value])=>element.setAttribute(key,value));return element;};
  function captureFocus(){
    const element=doc.activeElement,grid=element?.closest?.('.wcp-ui-grid');
    const attrs=['data-ui-sort','data-ui-select','data-ui-select-page','data-ui-row-action','data-row-id','data-ui-bulk-action','data-ui-remove-filter'].filter(name=>element?.hasAttribute?.(name)).map(name=>[name,element.getAttribute(name)]);
    return {element,grid,attrs};
  }
  function restoreFocus(token,force=false){
    if(token.element?.isConnected&&!token.element.disabled){if(force)token.element.focus({preventScroll:true});return;}
    if(!token.grid?.isConnected)return;
    const replacement=token.attrs.length?[...token.grid.querySelectorAll('button,input')].find(element=>!element.disabled&&token.attrs.every(([name,value])=>element.getAttribute(name)===value)):null;
    (replacement||token.grid.querySelector('[data-ui-query]'))?.focus({preventScroll:true});
  }

  function createStore({app='core',scope='design',owner='demo',module,version=1,storage}={}){
    if(!module)throw new Error('A store requires a module namespace.');
    if(storage===undefined)try{storage=global.localStorage;}catch{}
    const key=['wcp360-ui',app,scope,owner,module,version].map(part=>encodeURIComponent(String(part))).join(':');
    let memory={};
    function load(){try{const envelope=JSON.parse(storage?.getItem(key)||'null');if(envelope?.version===version&&envelope.data&&typeof envelope.data==='object'&&!Array.isArray(envelope.data))memory=envelope.data;}catch{}return clone(memory);}
    return {key,read:load,write(data){memory=clone(data);try{if(!storage?.setItem)return false;storage.setItem(key,JSON.stringify({version,data:memory}));return true;}catch{return false;}},clear(){memory={};try{storage?.removeItem(key);}catch{}}};
  }

  const numeric=value=>(typeof value==='number'||typeof value==='string'&&value.trim()!=='')&&Number.isFinite(Number(value));
  const operators={
    contains:(a,b)=>normalize(a).includes(normalize(b)),
    equals:(a,b)=>normalize(a)===normalize(b),
    notEquals:(a,b)=>normalize(a)!==normalize(b),
    startsWith:(a,b)=>normalize(a).startsWith(normalize(b)),
    gt:(a,b)=>numeric(a)&&numeric(b)&&Number(a)>Number(b),
    gte:(a,b)=>numeric(a)&&numeric(b)&&Number(a)>=Number(b),
    lt:(a,b)=>numeric(a)&&numeric(b)&&Number(a)<Number(b),
    lte:(a,b)=>numeric(a)&&numeric(b)&&Number(a)<=Number(b),
    empty:a=>a===null||a===undefined||a==='',
    notEmpty:a=>a!==null&&a!==undefined&&a!==''
  };
  function createCollection({rows=[],columns=[],rowKey='id',pageSize=10}={}){
    const columnMap=new Map(columns.map(column=>[column.key,column]));
    if(columnMap.size!==columns.length)throw new Error('Column keys must be unique.');
    let data=[],query='',filters=[],sort=[],page=1,size=10;
    const selected=new Set();
    const value=(row,column)=>column.value?column.value(row):row[column.key];
    const id=row=>String(typeof rowKey==='function'?rowKey(row):row[rowKey]);
    function setRows(next){
      if(!Array.isArray(next))throw new Error('Rows must be an array.');
      const ids=next.map(id);
      if(ids.some(key=>key==='undefined'||key==='null'||key==='')||new Set(ids).size!==ids.length)throw new Error('Every row needs a unique stable ID.');
      data=next.slice();for(const key of selected)if(!ids.includes(key))selected.delete(key);
    }
    function ordered(){
      return data.map((row,index)=>({row,index})).filter(({row})=>{
        const matchesQuery=!query||columns.some(column=>column.searchable!==false&&normalize(value(row,column)).includes(normalize(query)));
        return matchesQuery&&filters.every(filter=>operators[filter.operator](value(row,columnMap.get(filter.field)),filter.value));
      }).sort((a,b)=>{
        for(const rule of sort){
          const column=columnMap.get(rule.field),av=value(a.row,column),bv=value(b.row,column);
          const aEmpty=av===null||av===undefined||av==='',bEmpty=bv===null||bv===undefined||bv==='';
          if(aEmpty!==bEmpty)return aEmpty?1:-1;
          let result=0;
          if(!aEmpty&&!bEmpty){
            if(column.type==='number')result=(Number(av)||0)-(Number(bv)||0);
            else if(column.type==='date')result=(Date.parse(av)||0)-(Date.parse(bv)||0);
            else result=normalize(av).localeCompare(normalize(bv),undefined,{numeric:true});
          }
          if(result)return rule.direction==='desc'?-result:result;
        }
        return a.index-b.index;
      }).map(item=>item.row);
    }
    function snapshot(){const filtered=ordered(),pages=Math.max(1,Math.ceil(filtered.length/size));page=Math.max(1,Math.min(page,pages));return {rows:filtered.slice((page-1)*size,page*size),total:data.length,count:filtered.length,page,pages,pageSize:size,query,filters:clone(filters),sort:clone(sort),selected:[...selected]};}
    function setFilters(next){
      if(!Array.isArray(next)||next.some(rule=>!columnMap.has(rule.field)||columnMap.get(rule.field).filterable===false||!Object.hasOwn(operators,rule.operator)))throw new Error('Unsupported filter.');
      filters=clone(next);page=1;selected.clear();return snapshot();
    }
    function setSort(next){
      if(!Array.isArray(next)||next.some(rule=>!columnMap.has(rule.field)||columnMap.get(rule.field).sortable===false||!['asc','desc'].includes(rule.direction))||new Set(next.map(rule=>rule.field)).size!==next.length)throw new Error('Unsupported sort.');
      sort=clone(next);page=1;return snapshot();
    }
    function setPageSize(next){if(![5,10,25,50,100].includes(Number(next)))throw new Error('Unsupported page size.');size=Number(next);page=1;return snapshot();}
    const api={snapshot,id,value,ordered,setRows:next=>{setRows(next);return snapshot();},setFilters,setSort,setPageSize,
      setQuery(next){query=String(next||'');page=1;selected.clear();return snapshot();},
      setPage(next){page=Number.isFinite(Number(next))?Math.trunc(Number(next)):1;return snapshot();},
      select(key,on){key=String(key);if(data.some(row=>id(row)===key)){if(on)selected.add(key);else selected.delete(key);}return snapshot();},
      clearSelection(){selected.clear();},selectedRows:()=>data.filter(row=>selected.has(id(row))),
      export(format='csv',keys=columns.filter(column=>column.exportable!==false).map(column=>column.key)){
        const fields=keys.map(key=>columnMap.get(key)).filter(column=>column&&column.exportable!==false);
        const records=ordered().map(row=>Object.fromEntries(fields.map(column=>[column.key,value(row,column)])));
        if(format==='json')return JSON.stringify(records,null,2);
        if(format!=='csv')throw new Error('Unsupported export format.');
        const csv=value=>{let text=String(value??'');if(typeof value!=='number'&&/^[\s]*[=+\-@\t\r]/.test(text))text="'"+text;return '"'+text.replaceAll('"','""')+'"';};
        return [fields.map(column=>csv(column.label||column.key)).join(','),...records.map(row=>fields.map(column=>csv(row[column.key])).join(','))].join('\r\n');
      }};
    setRows(rows);setPageSize(pageSize);return api;
  }

  const rules={
    domain:value=>/^(?=.{1,253}$)(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z](?:[a-z0-9-]{0,61}[a-z0-9])?$/i.test(String(value)),
    identifier:value=>/^[a-z][a-z0-9_]{0,47}$/i.test(String(value)),
    absolutePath:value=>/^\/(?!\/)/.test(String(value))&&!String(value).split('/').some(part=>part==='..')&&!/[\u0000-\u001f\\]/.test(String(value)),
    email:value=>/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(value))
  };
  function validateValues(fields,values){
    const errors={};
    for(const field of fields){
      const value=values[field.name],empty=value===undefined||value===null||typeof value==='string'&&value.trim()===''||(field.type==='checkbox'&&!value);
      let message='';
      if(field.required&&empty)message='This field is required.';
      else if(!empty){
        if(field.type==='number'&&!Number.isFinite(Number(value)))message='Enter a valid number.';
        else if(field.type==='number'&&field.min!==undefined&&Number(value)<field.min)message=`Minimum: ${field.min}.`;
        else if(field.type==='number'&&field.max!==undefined&&Number(value)>field.max)message=`Maximum: ${field.max}.`;
        else if(field.minLength&&String(value).length<field.minLength)message=`Use at least ${field.minLength} characters.`;
        else if(field.maxLength&&String(value).length>field.maxLength)message=`Use at most ${field.maxLength} characters.`;
        else if(field.options&&!field.options.some(option=>String(typeof option==='object'?option.value:option)===String(value)))message='Choose an available option.';
        else if(field.rule&&(!rules[field.rule]||!rules[field.rule](value)))message=field.ruleMessage||'Enter a valid value.';
        else if(field.type==='email'&&!rules.email(value))message='Enter a valid email address.';
      }
      if(!message&&field.validate)message=field.validate(value,values)||'';
      if(message)errors[field.name]=message;
    }
    return {valid:Object.keys(errors).length===0,errors,values};
  }
  const attachedForms=new WeakMap();
  function formValues(form,fields){return Object.fromEntries(fields.map(field=>{const input=form.elements.namedItem(field.name);return [field.name,field.type==='checkbox'?!!input?.checked:input?.value??''];}));}
  function attachForm(form,{fields,onSubmit}={}){
    attachedForms.get(form)?.destroy();
    const oldNoValidate=form.noValidate;form.noValidate=true;
    const summary=node('div','wcp-ui-form-summary');summary.hidden=true;summary.setAttribute('role','alert');form.prepend(summary);
    const original=new Map();
    for(const field of fields){const input=form.elements.namedItem(field.name);if(!input)continue;original.set(input,{describedBy:input.getAttribute('aria-describedby'),invalid:input.getAttribute('aria-invalid')});if(!input.id)input.id=uid('wcp-field');const error=node('small','wcp-ui-field-error');error.id=input.id+'-error';error.hidden=true;input.insertAdjacentElement('afterend',error);input.setAttribute('aria-describedby',[input.getAttribute('aria-describedby'),error.id].filter(Boolean).join(' '));}
    function validate(){
      const result=validateValues(fields,formValues(form,fields));summary.replaceChildren();summary.hidden=result.valid;
      if(!result.valid)summary.append(node('strong','', 'Please review the highlighted fields.'));
      for(const field of fields){const input=form.elements.namedItem(field.name);if(!input)continue;const error=doc.getElementById(input.id+'-error'),message=result.errors[field.name];input.setAttribute('aria-invalid',String(!!message));if(error){error.hidden=!message;error.textContent=message||'';}if(message){const link=node('a','',`${field.label||field.name}: ${message}`);link.href='#'+input.id;link.onclick=event=>{event.preventDefault();input.focus();};summary.append(link);}}
      return result;
    }
    async function submit(event){const result=validate();if(!result.valid){event.preventDefault();event.stopImmediatePropagation();form.elements.namedItem(Object.keys(result.errors)[0])?.focus();return;}if(onSubmit){event.preventDefault();event.stopImmediatePropagation();const submitter=event.submitter;if(submitter)submitter.disabled=true;try{await onSubmit(result.values);}catch(error){summary.hidden=false;summary.replaceChildren(node('strong','',error.message||'Operation failed.'));}finally{if(submitter)submitter.disabled=false;}}}
    form.addEventListener('submit',submit,true);
    const api={validate,destroy(){form.removeEventListener('submit',submit,true);form.noValidate=oldNoValidate;summary.remove();for(const [input,attrs] of original){doc.getElementById(input.id+'-error')?.remove();for(const [name,value] of [['aria-describedby',attrs.describedBy],['aria-invalid',attrs.invalid]])if(value===null)input.removeAttribute(name);else input.setAttribute(name,value);}attachedForms.delete(form);}};
    attachedForms.set(form,api);return api;
  }
  function populateForm(form,{fields,values={},submitLabel='Save',cancelLabel='Cancel',onCancel,onSubmit}={}){
    attachedForms.get(form)?.destroy();form.replaceChildren();form.classList.add('wcp-ui-form');
    const grid=node('div','wcp-ui-form-grid');
    for(const field of fields){
      const label=node('label','wcp-ui-field'),caption=node('span','',field.label||field.name);let input;
      if(field.type==='select'){input=node('select');for(const option of field.options||[]){const item=typeof option==='object'?option:{value:option,label:option};const element=node('option','',item.label);element.value=item.value;input.append(element);}}
      else if(field.type==='textarea')input=node('textarea');
      else{input=node('input');input.type=field.type||'text';}
      input.name=field.name;input.id=uid('wcp-field');input.required=!!field.required;
      for(const attr of ['min','max','minLength','maxLength','placeholder','autocomplete'])if(field[attr]!==undefined)input[attr]=field[attr];
      if(field.type==='checkbox')input.checked=!!(values[field.name]??field.default);else input.value=values[field.name]??field.default??'';
      label.append(caption,input);if(field.help){const help=node('small','wcp-ui-field-help',field.help);help.id=input.id+'-help';input.setAttribute('aria-describedby',help.id);label.append(help);}grid.append(label);
    }
    const actions=node('div','wcp-ui-form-actions'),cancel=button(cancelLabel);cancel.addEventListener('click',()=>onCancel?.());const submit=button(submitLabel);submit.type='submit';submit.classList.add('primary');actions.append(cancel,submit);form.append(grid,actions);
    return attachForm(form,{fields,onSubmit});
  }

  function toast({title='WCP360',message='',tone='info',timeout=4500}={}){
    let stack=doc.querySelector('#wcpToastStack');if(!stack){stack=node('div','wcp-toast-stack');stack.id='wcpToastStack';doc.body.append(stack);}
    const element=node('div','wcp-toast wcp-ui-toast');element.setAttribute('role',tone==='error'?'alert':'status');
    const content=node('div');content.append(node('strong','wcp-toast-title',title),node('div','wcp-toast-sub',message));const close=button('×',{'aria-label':'Dismiss notification'});close.onclick=()=>element.remove();element.append(content,close);stack.append(element);if(timeout>0)setTimeout(()=>element.remove(),timeout);return {dismiss:()=>element.remove()};
  }
  function notify({title='WCP360',message='',tone='info'}={}){if(global.WCPRuntime)global.WCPRuntime.notify(title,message);else toast({title,message,tone});}
  const theme={apply(value){const mode=['system','light','dark'].includes(value)?value:'system';doc.documentElement.setAttribute('data-theme',mode);try{global.localStorage.setItem('wcp360-theme',mode);}catch{}doc.querySelectorAll('[data-wcp-theme],[data-theme-choice]').forEach(control=>{const on=(control.dataset.wcpTheme||control.dataset.themeChoice)===mode;control.classList.toggle('active',on);if(control.matches('button'))control.setAttribute('aria-pressed',String(on));});return mode;}};

  let activeDialog=null;
  function openDialog({title,description='',content,onClose}={}){
    activeDialog?.close();const previous=captureFocus(),dialog=node('dialog','wcp-ui-dialog');const heading=node('h2','',title||'WCP360');heading.id=uid('wcp-dialog-title');dialog.setAttribute('aria-labelledby',heading.id);const header=node('div','wcp-ui-dialog-head'),close=button('×',{'aria-label':'Close dialog'});header.append(heading,close);dialog.append(header);if(description){const copy=node('p','',description);copy.id=uid('wcp-dialog-description');dialog.setAttribute('aria-describedby',copy.id);dialog.append(copy);}if(content)dialog.append(content);doc.body.append(dialog);
    let resolveClosed;const closed=new Promise(resolve=>resolveClosed=resolve);let done=false;
    function finish(value){if(done)return;done=true;if(dialog.open)dialog.close();dialog.remove();activeDialog=null;restoreFocus(previous,true);try{onClose?.(value);}finally{resolveClosed(value);}}
    close.onclick=()=>finish(false);dialog.addEventListener('cancel',event=>{event.preventDefault();finish(false);});dialog.addEventListener('close',()=>finish(false));
    const api={element:dialog,closed,close:finish};activeDialog=api;dialog.showModal();return api;
  }
  async function confirmDialog({title='Confirm operation',message='',confirmLabel='Confirm',typed=''}={}){
    const content=node('div','wcp-ui-confirm');let input;
    if(typed){const label=node('label','wcp-ui-field'),caption=node('span','',`Type ${typed} to continue`);input=node('input');input.autocomplete='off';label.append(caption,input);content.append(label);}
    const actions=node('div','wcp-ui-form-actions'),cancel=button('Cancel'),confirm=button(confirmLabel);confirm.classList.add('primary');confirm.disabled=!!typed;actions.append(cancel,confirm);content.append(actions);const dialog=openDialog({title,description:message,content});cancel.onclick=()=>dialog.close(false);confirm.onclick=()=>dialog.close(true);if(input)input.addEventListener('input',()=>{confirm.disabled=input.value!==typed;});return dialog.closed;
  }

  function download(content,filename,type){const blob=new Blob([type==='text/csv'?'\uFEFF'+content:content],{type:type+';charset=utf-8'}),url=URL.createObjectURL(blob),link=node('a');link.href=url;link.download=filename;link.click();setTimeout(()=>URL.revokeObjectURL(url),1000);}
  const mountedGrids=new WeakMap();
  function mountGrid(host,{id,title='Records',rows=[],columns=[],rowKey='id',pageSize=10,store,bulkActions=[],rowActions=[],onRetry}={}){
    mountedGrids.get(host)?.destroy();if(!id||!/^[a-z][\w:.-]*$/i.test(id))throw new Error('A grid requires a stable HTML ID.');if(!columns.length)throw new Error('A grid requires at least one column.');
    const model=createCollection({rows,columns,rowKey,pageSize}),prefs=store?.read()||{};
    let order=columns.map(column=>column.key),visible=columns.filter(column=>column.hidden!==true).map(column=>column.key),views=[],status='ready',statusMessage='',busy=false;
    const selectable=bulkActions.length>0;
    function applyView(saved){
      try{if(saved?.query!==undefined)model.setQuery(saved.query);if(saved?.filters)model.setFilters(saved.filters);if(saved?.sort)model.setSort(saved.sort);if(saved?.pageSize)model.setPageSize(saved.pageSize);}catch{model.setQuery('');model.setFilters([]);model.setSort([]);}
      if(Array.isArray(saved?.order)){const valid=saved.order.filter(key=>columns.some(column=>column.key===key));order=[...new Set([...valid,...columns.map(column=>column.key)])];}
      if(Array.isArray(saved?.visible)){const valid=saved.visible.filter(key=>columns.some(column=>column.key===key));if(valid.length)visible=[...new Set(valid)];}
    }
    applyView(prefs.current);views=Array.isArray(prefs.views)?prefs.views.filter(view=>typeof view.name==='string'&&view.state).slice(0,15):[];
    const viewState=()=>{const state=model.snapshot();return {query:state.query,filters:state.filters,sort:state.sort,pageSize:state.pageSize,order:[...order],visible:[...visible]};};
    const persist=()=>store?.write({current:viewState(),views});
    host.classList.add('wcp-ui-grid');
    host.innerHTML=`<div class="wcp-ui-grid-toolbar"><strong>${escape(title)}</strong><label class="wcp-ui-search">Search<input type="search" data-ui-query aria-label="Search ${escape(title)}" placeholder="Search records"></label><button type="button" class="wcp-ui-button" data-ui-export="csv">CSV</button><button type="button" class="wcp-ui-button" data-ui-export="json">JSON</button></div><div class="wcp-ui-grid-settings"><details><summary>Filters & sort</summary><form data-ui-filter class="wcp-ui-inline-form"><label>Field<select name="field">${columns.filter(column=>column.filterable!==false).map(column=>`<option value="${escape(column.key)}">${escape(column.label||column.key)}</option>`).join('')}</select></label><label>Condition<select name="operator">${Object.keys(operators).map(operator=>`<option>${operator}</option>`).join('')}</select></label><label>Value<input name="value" aria-label="Filter value"></label><button type="submit" class="wcp-ui-button">Add filter</button></form><div class="wcp-ui-inline-form"><label>Sort field<select data-ui-sort-field>${columns.filter(column=>column.sortable!==false).map(column=>`<option value="${escape(column.key)}">${escape(column.label||column.key)}</option>`).join('')}</select></label><label>Direction<select data-ui-sort-direction><option value="asc">Ascending</option><option value="desc">Descending</option></select></label><button type="button" class="wcp-ui-button" data-ui-add-sort>Add sort</button><button type="button" class="wcp-ui-button" data-ui-clear>Clear filters & sort</button></div></details><details><summary>Columns</summary><div data-ui-columns></div></details><details><summary>Saved views</summary><div class="wcp-ui-inline-form"><label>View name<input data-ui-view-name maxlength="60"></label><button type="button" class="wcp-ui-button" data-ui-save-view>Save view</button><label>Saved views<select data-ui-views aria-label="Saved views"></select></label><button type="button" class="wcp-ui-button" data-ui-load-view>Load</button><button type="button" class="wcp-ui-button" data-ui-delete-view>Remove view</button></div></details></div><div class="wcp-ui-grid-criteria" data-ui-criteria></div><div class="wcp-ui-grid-bulk" data-ui-bulk></div><p class="wcp-ui-sr-only" id="${escape(id)}-help">Use column buttons to sort. Shift plus click adds a secondary sort. Advanced sorting is also available under Filters & sort.</p><div class="wcp-ui-table-wrap" role="region" aria-label="${escape(title)} table" tabindex="0" aria-describedby="${escape(id)}-help"><table class="wcp-ui-table"><caption class="wcp-ui-sr-only">${escape(title)}</caption><thead></thead><tbody></tbody></table></div><div data-ui-state class="wcp-ui-state" role="status"></div><nav class="wcp-ui-pagination" aria-label="${escape(title)} pagination"><span data-ui-count aria-live="polite"></span><label>Rows<select data-ui-page-size>${[5,10,25,50,100].map(value=>`<option value="${value}">${value}</option>`).join('')}</select></label><button type="button" class="wcp-ui-button" data-ui-page="first" aria-label="First page">First</button><button type="button" class="wcp-ui-button" data-ui-page="previous" aria-label="Previous page">Previous</button><span data-ui-page-label></span><button type="button" class="wcp-ui-button" data-ui-page="next" aria-label="Next page">Next</button><button type="button" class="wcp-ui-button" data-ui-page="last" aria-label="Last page">Last</button></nav>`;
    const find=selector=>host.querySelector(selector),all=selector=>[...host.querySelectorAll(selector)],abort=new AbortController();
    const activeColumns=()=>order.filter(key=>visible.includes(key)).map(key=>columns.find(column=>column.key===key));
    function renderPreferences(){
      find('[data-ui-columns]').innerHTML=order.map((key,index)=>{const column=columns.find(item=>item.key===key);return `<div class="wcp-ui-column"><label><input type="checkbox" data-ui-visible="${escape(key)}" ${visible.includes(key)?'checked':''}>${escape(column.label||key)}</label><button type="button" class="wcp-ui-button" data-ui-move="${escape(key)}" data-direction="-1" ${index===0?'disabled':''} aria-label="Move ${escape(column.label||key)} earlier">↑</button><button type="button" class="wcp-ui-button" data-ui-move="${escape(key)}" data-direction="1" ${index===order.length-1?'disabled':''} aria-label="Move ${escape(column.label||key)} later">↓</button></div>`;}).join('');
      find('[data-ui-views]').innerHTML=views.length?views.map((view,index)=>`<option value="${index}">${escape(view.name)}</option>`).join(''):'<option value="">No saved views</option>';
      find('[data-ui-query]').value=model.snapshot().query;find('[data-ui-page-size]').value=model.snapshot().pageSize;
    }
    function render(){
      const previousFocus=captureFocus();
      const state=model.snapshot(),fields=activeColumns(),head=find('thead'),body=find('tbody'),scroll=find('.wcp-ui-table-wrap').scrollLeft;head.replaceChildren();body.replaceChildren();
      const tr=node('tr');
      if(selectable){const th=node('th'),input=node('input');input.type='checkbox';input.dataset.uiSelectPage='';input.setAttribute('aria-label','Select all records on this page');const selected=state.rows.filter(row=>state.selected.includes(model.id(row))).length;input.checked=state.rows.length>0&&selected===state.rows.length;input.indeterminate=selected>0&&selected<state.rows.length;input.disabled=!state.rows.length;th.append(input);tr.append(th);}
      for(const column of fields){const th=node('th');th.scope='col';const index=state.sort.findIndex(rule=>rule.field===column.key),rule=state.sort[index];if(column.sortable===false)th.textContent=column.label||column.key;else{const sortButton=button((column.label||column.key)+(rule?` ${rule.direction==='asc'?'↑':'↓'} ${index+1}`:''),{'data-ui-sort':column.key});th.setAttribute('aria-sort',index===0?(rule.direction==='asc'?'ascending':'descending'):'none');th.append(sortButton);}tr.append(th);}
      if(rowActions.length){const th=node('th','','Actions');th.scope='col';tr.append(th);}head.append(tr);
      for(const row of state.rows){
        const tr=node('tr');tr.dataset.uiRow=model.id(row);
        if(selectable){const td=node('td'),input=node('input');input.type='checkbox';input.dataset.uiSelect=model.id(row);input.checked=state.selected.includes(model.id(row));input.setAttribute('aria-label','Select '+String(model.value(row,columns[0])));td.append(input);tr.append(td);}
        for(const column of fields){const td=node('td'),value=model.value(row,column);if(column.render){const rendered=column.render(row,value);if(rendered?.nodeType)td.append(rendered);else td.textContent=rendered??'';}else td.textContent=value??'—';tr.append(td);}
        if(rowActions.length){const td=node('td','wcp-ui-row-actions');rowActions.forEach((action,index)=>{if(action.when&&!action.when(row))return;const control=button(action.label,{'data-ui-row-action':index,'data-row-id':model.id(row)});control.setAttribute('aria-label',action.label+' '+String(model.value(row,columns[0])));td.append(control);});tr.append(td);}body.append(tr);
      }
      const count=state.count,start=count?(state.page-1)*state.pageSize+1:0,end=Math.min(count,state.page*state.pageSize);find('[data-ui-count]').textContent=`${start}–${end} of ${count} · ${state.total} total`;find('[data-ui-page-label]').textContent=`${state.page} / ${state.pages}`;
      all('[data-ui-page]').forEach(control=>control.disabled=['first','previous'].includes(control.dataset.uiPage)?state.page===1:state.page===state.pages);
      find('[data-ui-criteria]').innerHTML=state.filters.map((filter,index)=>`<button type="button" class="wcp-ui-chip" data-ui-remove-filter="${index}" aria-label="Remove filter ${escape(filter.field)}">${escape(columns.find(column=>column.key===filter.field)?.label||filter.field)} ${escape(filter.operator)} ${escape(filter.value??'')} ×</button>`).join('')+state.sort.map(rule=>`<span class="wcp-ui-chip">Sort: ${escape(rule.field)} ${rule.direction}</span>`).join('');
      const bulk=find('[data-ui-bulk]');bulk.hidden=status!=='ready'||!selectable||!state.selected.length;bulk.innerHTML=`<span>${state.selected.length} selected</span>`+bulkActions.map((action,index)=>`<button type="button" class="wcp-ui-button" data-ui-bulk-action="${index}" ${busy?'disabled':''}>${escape(action.label)}</button>`).join('');
      all('[data-ui-export]').forEach(control=>control.disabled=status!=='ready');
      const stateNode=find('[data-ui-state]');stateNode.textContent=status==='loading'?'Loading records…':status==='error'?(statusMessage||'Records could not be loaded.'):!count?'No records match your search and filters.':'';stateNode.hidden=status==='ready'&&count>0;find('.wcp-ui-table-wrap').hidden=status!=='ready'||!count;host.setAttribute('aria-busy',String(status==='loading'||busy));
      if(status==='error'&&onRetry){const retry=button('Retry');retry.onclick=onRetry;stateNode.append(retry);}
      find('.wcp-ui-table-wrap').scrollLeft=scroll;
      restoreFocus(previousFocus);
      persist();
    }
    function refresh(){render();}
    host.addEventListener('input',event=>{if(event.target.matches('[data-ui-query]')){model.setQuery(event.target.value);refresh();}},{signal:abort.signal});
    host.addEventListener('change',event=>{
      const target=event.target;
      if(target.matches('[data-ui-page-size]'))model.setPageSize(target.value);
      else if(target.matches('[data-ui-select]'))model.select(target.dataset.uiSelect,target.checked);
      else if(target.matches('[data-ui-select-page]'))model.snapshot().rows.forEach(row=>model.select(model.id(row),target.checked));
      else if(target.matches('[data-ui-visible]')){const key=target.dataset.uiVisible;if(!target.checked&&visible.length===1){target.checked=true;toast({title:'Keep one column visible'});return;}visible=target.checked?[...visible,key]:visible.filter(item=>item!==key);}
      else return;refresh();
    },{signal:abort.signal});
    host.addEventListener('submit',event=>{
      if(!event.target.matches('[data-ui-filter]'))return;event.preventDefault();const form=event.target,field=form.elements.field.value,operator=form.elements.operator.value,value=form.elements.value.value;
      if(['gt','gte','lt','lte'].includes(operator)&&(!value.trim()||!Number.isFinite(Number(value)))){toast({title:'Invalid filter',message:'Enter a numeric value.',tone:'error'});form.elements.value.focus();return;}
      model.setFilters([...model.snapshot().filters,{field,operator,value}]);refresh();
    },{signal:abort.signal});
    host.addEventListener('click',async event=>{
      const control=event.target.closest('button');if(!control||!host.contains(control))return;
      const data=control.dataset,state=model.snapshot();
      if(status!=='ready'&&(data.uiRowAction!==undefined||data.uiBulkAction!==undefined))return;
      if(data.uiSort!==undefined){const current=state.sort.find(rule=>rule.field===data.uiSort),next={field:data.uiSort,direction:current?.direction==='asc'?'desc':'asc'};model.setSort(event.shiftKey?[...state.sort.filter(rule=>rule.field!==next.field),next]:[next]);}
      else if(data.uiPage){model.setPage(data.uiPage==='first'?1:data.uiPage==='last'?state.pages:state.page+(data.uiPage==='next'?1:-1));}
      else if(data.uiRemoveFilter!==undefined)model.setFilters(state.filters.filter((_,index)=>index!==Number(data.uiRemoveFilter)));
      else if(data.uiAddSort!==undefined){const rule={field:find('[data-ui-sort-field]').value,direction:find('[data-ui-sort-direction]').value};model.setSort([...state.sort.filter(item=>item.field!==rule.field),rule]);}
      else if(data.uiClear!==undefined){model.setQuery('');model.setFilters([]);model.setSort([]);find('[data-ui-query]').value='';}
      else if(data.uiExport){if(status!=='ready')return;download(model.export(data.uiExport,activeColumns().map(column=>column.key)),id+'.'+data.uiExport,data.uiExport==='csv'?'text/csv':'application/json');notify({title:'Export ready',message:`${state.count} filtered records exported.`});return;}
      else if(data.uiMove!==undefined){const from=order.indexOf(data.uiMove),to=from+Number(data.direction);if(to>=0&&to<order.length)[order[from],order[to]]=[order[to],order[from]];renderPreferences();all('[data-ui-move]').find(item=>item.dataset.uiMove===data.uiMove&&item.dataset.direction===data.direction&&!item.disabled)?.focus();}
      else if(data.uiSaveView!==undefined){const name=find('[data-ui-view-name]').value.trim();if(!name){find('[data-ui-view-name]').focus();return;}const existing=views.find(view=>view.name===name);if(existing)existing.state=viewState();else views.unshift({name,state:viewState()});views=views.slice(0,15);renderPreferences();notify({title:'View saved',message:name});}
      else if(data.uiLoadView!==undefined){const value=find('[data-ui-views]').value;if(value==='')return;applyView(views[Number(value)]?.state);renderPreferences();}
      else if(data.uiDeleteView!==undefined){const value=find('[data-ui-views]').value;if(value==='')return;views.splice(Number(value),1);renderPreferences();}
      else if(data.uiRowAction!==undefined){const row=model.ordered().find(row=>model.id(row)===data.rowId),action=rowActions[Number(data.uiRowAction)];if(!row||!action||(action.when&&!action.when(row)))return;try{await action.run(row);}catch(error){toast({title:'Action failed',message:error.message,tone:'error'});}return;}
      else if(data.uiBulkAction!==undefined){if(busy)return;const action=bulkActions[Number(data.uiBulkAction)],selection=model.selectedRows();if(!selection.length||!action)return;if(action.confirm&&!await confirmDialog({title:action.label,message:`Apply this action to ${selection.length} selected records?`,confirmLabel:action.label,typed:action.typed||''}))return;busy=true;refresh();try{await action.run(selection);model.clearSelection();}catch(error){toast({title:'Bulk action failed',message:error.message,tone:'error'});}finally{busy=false;} }
      else return;refresh();
    },{signal:abort.signal});
    renderPreferences();render();
    const api={model,setRows(next){model.setRows(next);refresh();},setStatus(next,message=''){if(!['ready','loading','error'].includes(next))throw new Error('Unsupported grid state.');status=next;statusMessage=message;refresh();},destroy(){abort.abort();mountedGrids.delete(host);},refresh};mountedGrids.set(host,api);return api;
  }

  function can(context,capability,target={}){return !!context&&Array.isArray(context.capabilities)&&context.capabilities.includes(capability)&&(!target.scope||target.scope===context.scope)&&(!target.owner||target.owner===context.owner);}
  global.WCPUI=Object.freeze({version:'1.1.1',normalize,escape,createStore,createCollection,download,operators:Object.keys(operators),rules,validateValues,Form:{populate:populateForm,attach:attachForm,detach:form=>attachedForms.get(form)?.destroy()},DataGrid:{mount:mountGrid},Dialog:{open:openDialog,confirm:confirmDialog},toast,notify,theme,can});
  if(doc?.currentScript?.src&&!doc.querySelector('link[data-wcp-foundation],link[href$="/foundation.css"]')){const link=doc.createElement('link');link.rel='stylesheet';link.href=new URL('foundation.css',doc.currentScript.src).href;link.dataset.wcpFoundation='';doc.head.append(link);}
})(globalThis);
