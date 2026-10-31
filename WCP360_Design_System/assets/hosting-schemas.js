/* Business schemas shared by the hosting prototype and its contract tests. */
((global)=>{
  'use strict';
  const UI=global.WCPUI;
  const same=(a,b)=>UI.normalize(String(a||'').trim())===UI.normalize(String(b||'').trim());
  const localPart=value=>/^[a-z0-9_+\-]+(?:\.[a-z0-9_+\-]+)*$/i.test(String(value))&&String(value).length<=64;
  const addresses=value=>String(value||'').split(',').map(item=>item.trim()).filter(Boolean);
  const isExternal=(value,domain)=>addresses(value).some(address=>!same(address.split('@')[1],domain));
  function mailbox({mailboxes=[],aliases=[],clients=[],domain='example.com',record,limit=25}={}){
    return [
      {name:'local',label:'Email address (local part)',required:true,maxLength:64,default:record?.address.split('@')[0]||'',validate:value=>{
        if(!localPart(value))return 'Use letters, numbers, dots, underscores, + or - without empty segments.';
        const address=value+'@'+domain;
        if(mailboxes.some(item=>item.id!==record?.id&&same(item.address,address))||aliases.some(item=>same(item.source,address)))return 'This address is already in use.';
        return !record&&mailboxes.length>=limit?'The mailbox allowance is exhausted.':'';
      }},
      {name:'domain',label:'Domain',type:'select',required:true,options:[domain],default:domain},
      {name:'displayName',label:'Display name',required:true,maxLength:80,default:record?.name||''},
      {name:'quota',label:'Storage quota (GB)',type:'number',required:true,min:.1,default:record?.quota??10,help:'The quota cannot be lower than current usage.',validate:value=>Number(value)<Number(record?.used||0)?'The quota is below the space already used.':''},
      {name:'spam',label:'Spam policy',type:'select',required:true,options:['Standard','Strict','Relaxed'],default:record?.spam||'Standard'},
      {name:'client',label:'Preferred webmail',type:'select',options:clients.length?clients.map(client=>({value:client.id,label:client.name})):[{value:'',label:'No webmail client available'}],default:clients.some(client=>client.id===record?.client)?record.client:clients[0]?.id||''},
      ...(!record?[
        {name:'password',label:'Temporary password',type:'password',required:true,minLength:16,autocomplete:'new-password',help:'Used only for this simulated submission; never saved in the browser state.'},
        {name:'forceChange',label:'Require a password change at first sign-in',type:'checkbox',default:true}
      ]:[])
    ];
  }
  function route({mailboxes=[],aliases=[],domain='example.com',record}={}){
    return [
      {name:'type',label:'Route type',type:'select',required:true,options:['Alias','Forwarder','Send-only identity'],default:record?.type||'Alias'},
      {name:'source',label:'Source address (local part)',required:true,maxLength:64,default:record?.source.split('@')[0]||'',validate:value=>{
        if(!localPart(value))return 'Enter a valid local address.';
        const address=value+'@'+domain;
        return aliases.some(item=>item.id!==record?.id&&same(item.source,address))||mailboxes.some(item=>same(item.address,address))?'This address is already in use.':'';
      }},
      {name:'domain',label:'Domain',type:'select',required:true,options:[domain],default:domain},
      {name:'destination',label:'Destination',default:record?.destination||mailboxes[0]?.address||'',help:'For aliases choose an existing mailbox. Forwarders accept comma-separated email addresses. Send-only identities have no destination.',validate:(value,values)=>{
        if(values.type==='Send-only identity')return '';
        if(record&&record.type===values.type&&record.destination===value)return '';
        const targets=addresses(value);
        if(!targets.length||targets.some(address=>!UI.rules.email(address)))return 'Enter one or more valid email addresses.';
        if(targets.some(address=>same(address,values.source+'@'+domain)))return 'A route cannot forward to itself.';
        if(values.type==='Alias'&&(targets.length!==1||!mailboxes.some(item=>same(item.address,targets[0]))))return 'An alias must target an existing account mailbox.';
        return '';
      }},
      {name:'verify',label:'Verify new external destinations before activation',type:'checkbox',default:true,validate:(value,values)=>values.type==='Forwarder'&&isExternal(values.destination,domain)&&!value?'External destinations require verification.':''}
    ];
  }
  const topologies=['Single server','Primary + secondary','Cluster'];
  const resourceNames={cpu:'Additional vCPU',ram:'Additional RAM (GB)',storage:'Additional storage (GB)',iops:'Additional IOPS',bandwidth:'Additional bandwidth (Mbps)',transfer:'Additional transfer (TB/month)'};
  function placement({plans=[],servers=[],record={},create=false}={}){
    const targets=servers.map(server=>({value:server.id,label:server.name}));
    return [
      {name:'plan',label:'Hosting plan',type:'select',required:true,options:plans.map(plan=>({value:plan.id,label:plan.name})),default:record.plan||plans.find(plan=>plan.id==='business')?.id||plans[0]?.id||''},
      ...(create?[{name:'placementPolicy',label:'Placement policy',type:'select',required:true,options:['Auto-select best fit','Pin to selected server','Managed cluster'],default:'Auto-select best fit'}]:[]),
      {name:'primary',label:'Primary server or cluster',type:'select',required:true,options:[...targets,{value:'cluster-eu-web',label:'EU-WEB-01 cluster'}],default:record.primary||servers[0]?.id||'',validate:(value,values)=>{
        if((values.topology==='Cluster')!==(value==='cluster-eu-web'))return 'Choose a cluster target for Cluster topology, or a server for other topologies.';
        return values.placementPolicy==='Managed cluster'&&values.topology!=='Cluster'?'Managed cluster placement requires Cluster topology.':'';
      }},
      {name:'topology',label:'Topology',type:'select',required:true,options:topologies,default:record.topology||'Single server'},
      {name:'secondary',label:'Secondary server',type:'select',options:[{value:'',label:'None'},...targets],default:record.secondary||'',validate:(value,values)=>{
        if(values.topology==='Primary + secondary'&&(!value||value===values.primary))return 'Choose a different secondary server.';
        if(values.topology==='Single server'&&value)return 'Single-server topology must not keep a secondary server.';
        return '';
      }},
      ...(!create?Object.entries(resourceNames).map(([name,label])=>({name,label,type:'number',required:true,min:0,default:record.overrides?.[name]||0})):[])
    ];
  }
  function account({accounts=[],plans=[],servers=[]}={}){
    return [
      {name:'domain',label:'Primary domain',required:true,rule:'domain',default:'new-account.example',validate:value=>accounts.some(item=>same(item.domain,value))?'This domain already belongs to a hosting account.':''},
      {name:'owner',label:'Account owner',required:true,maxLength:100,default:'Customer name'},
      {name:'email',label:'Contact email',type:'email',required:true,default:'owner@new-account.example'},
      {name:'username',label:'Account username',required:true,minLength:3,maxLength:32,default:'newaccount',validate:value=>{
        if(!/^[a-z][a-z0-9_]*$/.test(value)||['root','admin','mail','mysql','postgres','www_data'].includes(value))return 'Choose a non-reserved lowercase account name.';
        return accounts.some(item=>same(item.username,value))?'This username is already in use.':'';
      }},
      ...placement({plans,servers,create:true}),
      {name:'reseller',label:'Owner / reseller',type:'select',required:true,options:['ROOT owned','Example Agency reseller','Nova Hosting reseller'],default:'ROOT owned'},
      {name:'blueprint',label:'Provisioning blueprint',type:'select',required:true,options:['Business Standard · v18','Agency Multi-runtime · v11','External Mail · v7','Legacy Import · v4'],default:'Business Standard · v18'},
      {name:'featureList',label:'Feature list',type:'select',required:true,options:['Standard Web','Developer','Commerce','Web only'],default:'Standard Web'},
      {name:'defaultRuntime',label:'Default runtime',type:'select',required:true,options:['PHP 8.4','Node.js 22 LTS','Python 3.13','Ruby 3.3'],default:'PHP 8.4'},
      {name:'locale',label:'Account locale',type:'select',required:true,options:['English (United States)','Français (France)','Deutsch (Deutschland)','Español'],default:'English (United States)'},
      {name:'autoDns',label:'Create the DNS zone',type:'checkbox',default:true},
      {name:'autoSsl',label:'Request AutoSSL',type:'checkbox',default:true},
      {name:'mailService',label:'Provision mail services',type:'checkbox',default:true},
      {name:'welcome',label:'Prepare the secure welcome message',type:'checkbox',default:true}
    ];
  }
  function lifecycle(record,mode){
    if(mode==='terminate')return [
      {name:'finalBackup',label:'Create a final account archive',type:'checkbox',default:true,help:'Retain the account archive for 30 days.'},
      {name:'notify',label:'Prepare notice for '+record.email,type:'checkbox',default:true},
      {name:'confirmWord',label:'Type '+record.domain+' to confirm termination',required:true,autocomplete:'off',validate:value=>value===record.domain?'':'The domain must match exactly.'}
    ];
    return [
      {name:'reason',label:'Suspension reason',type:'select',required:true,options:['Billing issue','Terms-of-service review','Security incident','Customer request','Administrative hold'],default:'Administrative hold'},
      {name:'handling',label:'Service handling',type:'select',required:true,options:['Suspend web, login, SFTP and outbound mail','Suspend web and login only','Show warning page without blocking services'],default:'Suspend web, login, SFTP and outbound mail'},
      {name:'notify',label:'Prepare notice for '+record.email,type:'checkbox',default:true},
      {name:'preserveJobs',label:'Preserve backups and scheduled recovery jobs',type:'checkbox',default:true}
    ];
  }
  function canAccountTransition(record,action){
    if(!record||record._pending)return false;
    if(action==='suspend')return ['Active','Demo'].includes(record.status);
    if(action==='resume')return record.status==='Suspended';
    if(action==='backup')return ['Active','Demo','Suspended'].includes(record.status);
    return ['manage','terminate'].includes(action)&&['Active','Demo','Review','Suspended'].includes(record.status);
  }
  function canRecovery(record,action){
    if(!record||record._pending||!['Review','Restorable','Restored'].includes(record.state))return false;
    if(action==='restore')return record.state==='Restorable';
    if(action==='purge')return !record.legalHold;
    return action==='verify'&&record.state!=='Restored';
  }
  global.WCPHostingSchemas=Object.freeze({mailbox,route,account,placement,lifecycle,localPart,addresses,isExternal,canAccountTransition,canRecovery});
})(globalThis);
