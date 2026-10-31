/* Account application contracts. Values and observations remain demo metadata. */
((global)=>{
  'use strict';
  const owner='example.com',reserved=['APP_ENV','WCP_ACCOUNT','PATH','HOME','LD_PRELOAD','LD_LIBRARY_PATH','NODE_OPTIONS','PHPRC','PHP_INI_SCAN_DIR','SHELL','USER','LOGNAME'];
  const reference=value=>/^demo:\/\/secrets\/[a-zA-Z0-9_-]{2,80}$/.test(value);
  const sensitive=name=>/(?:PASSWORD|SECRET|TOKEN|PRIVATE_KEY|APP_KEY)/.test(name);
  const production=site=>String(site.environment).toLowerCase()==='production';
  const fingerprint=site=>JSON.stringify([site.id,site.domain,site.environment,site.runtime,site.version,!!site.maintenance,site.status]);
  function allowedVersions(raw,family){
    if(family==='Static')return ['None'];
    if(raw==null)return ({PHP:['8.4','8.3'],'Node.js':['22 LTS','20 LTS'],Python:['3.13','3.12'],Ruby:['3.3']})[family]||[];
    if(!Array.isArray(raw.versions))return [];
    return [...new Set(raw.versions.filter(item=>item.family===family&&['Recommended','Supported'].includes(item.status)&&Number(item.nodes)>0).map(item=>{const match=String(item.version).match(family==='Node.js'?/^(\d+)/:/^(\d+\.\d+)/);return match?match[1]+(family==='Node.js'?' LTS':''):'';}).filter(Boolean))];
  }
  function runtimeFields({site,current,policy}={}){return [
    {name:'version',label:site.runtime+' version (ROOT inventory)',type:'select',required:true,options:allowedVersions(policy,site.runtime),default:current.version},
    {name:'memory',label:'Demo memory budget (MiB, account ceiling 512)',type:'number',required:true,min:64,max:512,default:current.memory,validate:value=>Number.isInteger(Number(value))?'':'Use an integer memory budget.'},
    {name:'timezone',label:'Timezone',required:true,maxLength:80,default:current.timezone,validate:value=>{try{new Intl.DateTimeFormat('en',{timeZone:value});return '';}catch{return 'Enter an IANA timezone such as Europe/Paris.';}}}
  ];}
  function variableFields({variables=[],current}={}){return [
    {name:'name',label:'Variable name',required:true,maxLength:64,default:current?.name||'',validate:value=>!/^[_A-Z][_A-Z0-9]{0,63}$/.test(value)?'Use uppercase letters, digits and underscores.':reserved.includes(value)?'This variable is managed by the platform.':variables.some(item=>item.id!==current?.id&&item.name===value)?'This name already exists for the selected site.':''},
    {name:'kind',label:'Value type',type:'select',required:true,options:['Plain demo value','Secret reference'],default:current?.kind||'Plain demo value'},
    {name:'value',label:'Demo value or demo://secrets/reference-id',type:'textarea',required:true,maxLength:512,default:current?.value||'',validate:(value,values)=>values.kind==='Secret reference'?(reference(value)?'':'Use a demo://secrets/ reference; secret material is not accepted here.'):sensitive(values.name)?'Use a Secret reference for this variable.':/[\r\n\0]/.test(value)?'Use a single-line demo value.':''}
  ];}
  function seed(site){const config={runtime:{version:site.runtime==='Static'?'None':site.version,memory:256,timezone:'Europe/Paris'},variables:[{id:'cache',name:'CACHE_DRIVER',kind:'Plain demo value',value:'redis'},{id:'app-key',name:'APP_KEY',kind:'Secret reference',value:'demo://secrets/app-key'}]};return {draft:structuredClone(config),published:structuredClone(config),revision:1,publishedRevision:1,generation:1,maintenance:{enabled:!!site.maintenance,message:'Maintenance in progress. Please retry shortly.',generation:1},checks:[{id:'http',label:'HTTP readiness',path:'/health/ready',expectedStatus:200,timeout:2000,enabled:true,revision:1}],observations:[]};}
  const configIssue=(site,config,policy)=>{
    if(!config?.runtime||!Array.isArray(config.variables))return 'The configuration is incomplete.';
    const fields=runtimeFields({site,current:config.runtime,policy}),result=global.WCPUI.validateValues(fields,config.runtime);if(!result.valid)return Object.values(result.errors)[0];
    for(const variable of config.variables){const result=global.WCPUI.validateValues(variableFields({variables:config.variables,current:variable}),variable);if(!result.valid)return Object.values(result.errors)[0];}return '';
  };
  function checkFields(current){return [
    {name:'label',label:'Check label',required:true,maxLength:60,default:current.label,validate:value=>value.trim()?'':'Enter a label.'},
    {name:'path',label:'Application path (no network request)',required:true,maxLength:200,default:current.path,validate:value=>/^\/[a-zA-Z0-9/_.-]*$/.test(value)&&!value.startsWith('//')&&!value.split('/').includes('..')?'':'Use an application path without host, query, escapes or traversal.'},
    {name:'expectedStatus',label:'Expected response',type:'select',required:true,options:['200','204'],default:String(current.expectedStatus)},
    {name:'timeout',label:'Timeout (ms)',type:'number',required:true,min:100,max:30000,default:current.timeout,validate:value=>Number.isInteger(Number(value))?'':'Use an integer timeout.'},
    {name:'enabled',label:'Enable this demo check',type:'checkbox',default:current.enabled}
  ];}
  function reviewFields(site,{maintenance=false}={}){return [
    ...(maintenance?[{name:'message',label:'Maintenance response message',type:'textarea',required:true,maxLength:200,default:'Maintenance in progress. Please retry shortly.',validate:value=>value.trim()?'':'Enter a message.'}]:[]),
    {name:'reason',label:'Change reason',type:'textarea',required:true,maxLength:300,validate:value=>value.trim().length>=5?'':'Describe the change in at least five characters.'},
    {name:'health',label:'Simulated result',type:'select',required:true,options:['Pass','Fail'],default:'Pass'},
    ...(production(site)?[{name:'confirmation',label:'Type '+site.domain+' to confirm the production target',required:true,validate:value=>value===site.domain?'':'Type the exact production hostname.'}]:[])
  ];}
  function changes(before,after){const changes=[];for(const key of ['version','memory','timezone'])if(before.runtime[key]!==after.runtime[key])changes.push('Runtime '+key+': '+before.runtime[key]+' → '+after.runtime[key]);for(const item of after.variables){const old=before.variables.find(row=>row.id===item.id);if(!old)changes.push('Add '+item.name);else if(JSON.stringify(old)!==JSON.stringify(item))changes.push('Update '+old.name+(old.name!==item.name?' → '+item.name:''));}for(const item of before.variables)if(!after.variables.some(row=>row.id===item.id))changes.push('Remove '+item.name);return changes;}
  global.WCPApplicationSchemas={owner,reserved,reference,sensitive,production,fingerprint,allowedVersions,runtimeFields,variableFields,seed,configIssue,checkFields,reviewFields,changes};
})(globalThis);
