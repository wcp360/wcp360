/* Demo delivery contracts. Metadata only; no Git, build or shell execution. */
((global)=>{
  'use strict';
  const owner='example.com',profiles=['Static website','Node application','PHP application'];
  const pending=item=>['running','scheduled'].includes(item.status);
  const branch=value=>typeof value==='string'&&value.length<=100&&value.split('/').every(part=>/^[a-zA-Z0-9_-][a-zA-Z0-9._-]*$/.test(part)&&!part.endsWith('.')&&!part.endsWith('.lock')&&!part.includes('..'));
  function remote(value){try{const url=new URL(value);return url.protocol==='https:'&&!!url.hostname&&!url.username&&!url.password&&!url.search&&!url.hash&&url.pathname!=='/'&&!/[\s\\]/.test(value)&&!/%(?:40|3a)/i.test(url.host);}catch{return false;}}
  const commit=value=>/^[a-f0-9]{7,40}$/i.test(String(value||''));
  function repoFields({repositories=[],current}={}){return [
    {name:'name',label:'Repository name',required:true,default:current?.name||'',maxLength:60,validate:value=>!/^[-a-zA-Z0-9_]{2,60}$/.test(value)?'Use 2–60 letters, digits, dashes or underscores.':repositories.some(item=>item.id!==current?.id&&item.name.toLowerCase()===value.toLowerCase())?'A repository with this name already exists.':''},
    {name:'remote',label:'HTTPS repository URL (metadata)',required:true,default:current?.remote||'',validate:value=>remote(value)?'':'Use an HTTPS repository URL without credentials, query or fragment.'},
    {name:'branch',label:'Branch label',required:true,default:current?.branch||'main',validate:value=>branch(value)?'':'Use branch segments with letters, digits, dash, underscore or internal dots.'},
    {name:'profile',label:'Build profile',type:'select',required:true,options:profiles,default:current?.profile||profiles[0]},
    {name:'protected',label:'Allow this branch to reach production',type:'checkbox',default:current?.protected??true}
  ];}
  const sourceFields=current=>[{name:'commit',label:'Demo source revision (7–40 hexadecimal characters)',required:true,default:current.commit||'',validate:value=>!commit(value)?'Enter a 7–40 character hexadecimal revision.':value.toLowerCase()===current.commit?'Choose a different demo revision.':''}];
  function buildFields({releases=[],operations=[]}={}){return [
    {name:'label',label:'Release label',required:true,maxLength:60,validate:value=>!/^[-a-zA-Z0-9_.]{2,60}$/.test(value)?'Use 2–60 letters, digits, dashes, underscores or dots.':[...releases,...operations.filter(item=>item.kind==='build'&&pending(item)).map(item=>item.payload)].some(item=>item.label?.toLowerCase()===value.toLowerCase())?'This release label already exists or is queued.':''},
    {name:'outcome',label:'Simulated build result',type:'select',required:true,options:['Pass','Fail'],default:'Pass'}
  ];}
  function deployFields({target,release,rollback=false}={}){return [
    {name:'reason',label:rollback?'Rollback reason':'Change note',type:'textarea',required:true,minLength:5,maxLength:300,validate:value=>value.trim().length>=5?'':'Describe the change in at least five characters.'},
    {name:'health',label:'Simulated health check',type:'select',required:true,options:['Pass','Fail'],default:'Pass'},
    ...(target==='production'?[{name:'confirmation',label:'Type '+owner+' to '+(rollback?'roll back':'promote')+' '+release.label,required:true,validate:value=>value===owner?'':'Type the exact production hostname.'}]:[])
  ];}
  const retained=(state,id)=>state.history.some(item=>item.target==='production'&&item.releaseId===id&&item.health==='Pass');
  function deploymentIssue(state,release,target,{rollback=false}={}){
    if(!['staging','production'].includes(target))return 'Unknown account environment.';
    if(!release||release.owner!==owner||release.status!=='Ready')return 'Choose an available successful build.';
    if(state.operations.some(item=>pending(item)&&item.target===target))return 'A job is already pending for this environment.';
    if(state.targets[target].releaseId===release.id)return 'This version is already active in that environment.';
    if(rollback){if(target!=='production'||!retained(state,release.id))return 'Rollback requires a previously healthy production version.';return '';}
    const repo=state.repositories.find(item=>item.id===release.repoId&&item.owner===owner);
    if(!repo||repo.archived)return 'The source repository is archived or unavailable.';
    if(target==='production'){
      if(!repo.protected||repo.branch!==release.branch)return 'Production requires the currently approved source branch.';
      if(state.targets.staging.releaseId!==release.id||state.targets.staging.health!=='Pass')return 'Deploy this exact version to staging with a passing health check first.';
    }
    return '';
  }
  global.WCPDeliverySchemas={owner,profiles,branch,remote,commit,repoFields,sourceFields,buildFields,deployFields,retained,deploymentIssue};
})(globalThis);
