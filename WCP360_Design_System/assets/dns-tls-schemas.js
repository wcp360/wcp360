/* Account DNS and TLS contracts. These validate prototype metadata, not real DNS or keys. */
((global)=>{
  'use strict';
  const UI=global.WCPUI,zone='example.com';
  const supported=['A','AAAA','CNAME','MX','TXT','SRV','CAA','TLSA','SSHFP'];
  const defaultTypes=supported.slice(0,7);
  const domain=value=>UI.rules.domain(String(value).toLowerCase().replace(/\.$/,''));
  const ipv4=value=>typeof value==='string'&&value.split('.').length===4&&value.split('.').every(part=>/^(0|[1-9]\d{0,2})$/.test(part)&&Number(part)<=255);
  const ipv6=value=>{if(typeof value!=='string'||!value.includes(':')||/[\s%\[\]]/.test(value))return false;try{return new URL('http://['+value+']/').hostname.startsWith('[');}catch{return false;}};
  const within=(name,root=zone)=>name===root||name.endsWith('.'+root);
  function ownerName(value,root=zone){
    const input=String(value||'').trim().toLowerCase();if(input==='@'||input===root||input===root+'.')return root;
    const absolute=input.endsWith('.'),name=absolute?input.slice(0,-1):within(input,root)?input:input+'.'+root;
    if(!within(name,root)||name.length>253)throw new Error('The record owner must be inside '+root+'.');
    const labels=name.split('.');if(labels.some((label,index)=>label!=='*'&&!/^[a-z0-9_](?:[a-z0-9_\-]{0,61}[a-z0-9_])?$/.test(label)||label==='*'&&index!==0))throw new Error('Use DNS labels of at most 63 characters; a wildcard may only be the first label.');
    return name;
  }
  const relative=(name,root=zone)=>name===root?'@':name.slice(0,-root.length-1);
  function dnsPolicy(saved){
    const valid=saved?.schema===1,policies=valid?saved.policies||{}:{};
    const raw=String(policies.minimumTtl??'60 seconds').trim(),match=raw.match(/^(\d+)(?:\s+seconds?)?$/i);
    const minimum=match?Number(match[1]):null;
    const minimumTtl=Number.isInteger(minimum)&&minimum>=0&&minimum<=2147483647?minimum:null;
    const configured=valid&&Array.isArray(saved.recordTypes)?saved.recordTypes.filter(item=>item.enabled).map(item=>item.id):defaultTypes;
    const defaultMatch=String(policies.defaultTtl??'300 seconds').trim().match(/^(\d+)(?:\s+seconds?)?$/i),defaultValue=defaultMatch?Number(defaultMatch[1]):300;
    return {types:supported.filter(type=>configured.includes(type)),minimumTtl,defaultTtl:Math.max(minimumTtl??60,Number.isInteger(defaultValue)&&defaultValue<=2147483647?defaultValue:300),dnssec:policies.dnssec||'Required for production',error:minimumTtl===null?'The ROOT minimum TTL is not a supported integer in seconds. Ask ROOT to correct the policy.':''};
  }
  function normalizeRecord(values,{root=zone,id,protected:protectedRecord=false}={}){
    const type=String(values.type).toUpperCase(),name=ownerName(values.name,root);let value=String(values.value??'');if(type!=='TXT')value=value.trim();
    if(['CNAME','MX','SRV'].includes(type))value=value.replace(/\.$/,'').toLowerCase()+'.';
    if(type==='AAAA'&&ipv6(value))value=new URL('http://['+value+']/').hostname.slice(1,-1);
    if(type==='A')value=value.split('.').map(Number).join('.');
    const record={id,name,type,value,ttl:Number(values.ttl),protected:protectedRecord};
    if(['MX','SRV'].includes(type))record.priority=Number(values.priority);
    if(type==='SRV'){record.weight=Number(values.weight);record.port=Number(values.port);}
    return record;
  }
  const dataKey=record=>[record.type,record.name,record.type==='TXT'?record.value:String(record.value).toLowerCase(),record.priority??'',record.weight??'',record.port??''].join('|');
  function valueError(type,value,name){
    if(type==='A')return ipv4(value)?'':'Enter a dotted IPv4 address (0–255 per octet).';
    if(type==='AAAA')return ipv6(value)?'':'Enter a valid IPv6 address without a scope identifier.';
    if(['CNAME','MX','SRV'].includes(type))return domain(value)?'':'Enter an absolute target hostname, for example mail.example.com.';
    if(type==='TXT')return /[\r\n\x00]/.test(value)?'Use a single-line TXT value. Multi-string zone-file syntax is not supported.':'';
    if(type==='CAA')return /^(0|128) (issue|issuewild|iodef) "[^"\r\n]*"$/.test(value)?'':'Use the supported CAA format: 0 issue "letsencrypt.org".';
    if(type==='TLSA'){const match=value.match(/^([0-3]) ([01]) ([012]) ([a-f0-9]+)$/i);return match&&(match[3]==='1'?match[4].length===64:match[3]==='2'?match[4].length===128:match[4].length%2===0)?'':'Use usage selector matching-type hexadecimal-data (SHA-256: 64 hex characters; SHA-512: 128).';}
    if(type==='SSHFP')return /^(1|2|3|4|6) (1 [a-f0-9]{40}|2 [a-f0-9]{64})$/i.test(value)?'':'Use algorithm fingerprint-type hexadecimal-fingerprint.';
    return 'This record type is not supported by the account editor.';
  }
  function recordError(record,records,root=zone){
    const other=records.filter(item=>item.id!==record.id),sameName=other.filter(item=>item.name===record.name);
    if(record.type==='CNAME'&&record.name===root)return 'A CNAME cannot replace the zone apex.';
    if(sameName.some(item=>record.type==='CNAME'||item.type==='CNAME'))return 'A CNAME owner cannot coexist with other records.';
    if(other.some(item=>dataKey(item)===dataKey(record)))return 'This record already exists.';
    if(sameName.some(item=>item.type===record.type&&item.ttl!==record.ttl))return 'All records in this RRset must use the same TTL.';
    const next=[...other,record],aliases=new Map(next.filter(item=>item.type==='CNAME').map(item=>[item.name,item.value.replace(/\.$/,'')]));
    for(const start of aliases.keys()){let current=start;const seen=new Set();while(aliases.has(current)){if(seen.has(current))return 'This change creates a CNAME loop.';seen.add(current);current=aliases.get(current);}}
    if(next.some(item=>['MX','SRV'].includes(item.type)&&aliases.has(item.value.replace(/\.$/,''))))return 'MX and SRV targets must not name a CNAME in this zone.';
    return '';
  }
  function recordFields({records,policy,record,root=zone}={}){
    return [{name:'type',label:'Record type',type:'select',required:true,options:policy.types,default:record?.type||policy.types[0]||'',validate:()=>policy.error||(record?.protected?'This record is protected.':'')},
      {name:'name',label:'Name / owner',required:true,maxLength:254,default:record?relative(record.name,root):'@',help:'Use @, a relative name, or an absolute name ending with a dot.',validate:value=>{try{ownerName(value,root);return '';}catch(error){return error.message;}}},
      {name:'value',label:'Value / target',required:true,maxLength:2048,default:record?.value||'',help:'MX/SRV use separate numeric fields below. TXT values are literal single-line text.',validate:(value,values)=>{const error=valueError(values.type,value,values.name);if(error)return error;try{return recordError(normalizeRecord({...values,value},{root,id:record?.id}),records,root);}catch(error){return error.message;}}},
      {name:'ttl',label:'TTL (seconds)',type:'number',required:true,min:policy.minimumTtl??60,max:2147483647,default:record?.ttl??policy.defaultTtl,validate:value=>Number.isInteger(Number(value))?'':'TTL must be a whole number of seconds.'},
      ...['priority','weight','port'].map(name=>({name,label:{priority:'MX / SRV priority',weight:'SRV weight',port:'SRV port'}[name],type:'number',min:0,max:65535,default:record?.[name]??0,validate:(value,values)=>{const needed=name==='priority'?['MX','SRV'].includes(values.type):values.type==='SRV';return needed&&(value===''||!Number.isInteger(Number(value)))?'Enter a whole number from 0 to 65535.':'';}})),
      {name:'note',label:'Change note',maxLength:160,default:record?.note||''}
    ];
  }
  function validateBatch(values,records,policy,{root=zone,uid=()=>''}={}){
    if(!Array.isArray(values)||!values.length||values.length>100)throw new Error('Import a JSON array containing 1 to 100 records.');
    const next=[...records],created=[];
    for(const value of values){if(!value||typeof value!=='object'||Array.isArray(value))throw new Error('Every import item must be a record object.');const result=UI.validateValues(recordFields({records:next,policy,root}),value);if(!result.valid)throw new Error(Object.values(result.errors)[0]);const record={...normalizeRecord(value,{root,id:uid()}),note:String(value.note||'')};next.push(record);created.push(record);}
    return created;
  }
  function dnsIssues(records,policy,{baseline=[]}={}){return records.flatMap(record=>{const previous=baseline.find(item=>item.id===record.id);if(record.protected||previous&&dataKey(previous)===dataKey(record)&&previous.ttl===record.ttl)return [];const result=UI.validateValues(recordFields({records,policy,record}),{...record,name:relative(record.name)});return Object.entries(result.errors).map(([field,message])=>({recordId:record.id,field,message}));});}
  const names=value=>String(value||'').split(/[\s,]+/).filter(Boolean).map(name=>name.toLowerCase().replace(/\.$/,''));
  const ownedHostname=(name,root=zone)=>{const host=name.startsWith('*.')?name.slice(2):name;return domain(host)&&within(host,root)&&(!name.includes('*')||name.startsWith('*.')&&!host.includes('*'));};
  const covers=(certificate,hostname)=>certificate.sans.some(name=>name===hostname||name.startsWith('*.')&&hostname.endsWith(name.slice(1))&&hostname.split('.').length===name.split('.').length);
  const usable=(certificate,now=Date.now())=>!!certificate&&certificate.owner===zone&&certificate.status==='Valid'&&certificate.chainValid===true&&Date.parse(certificate.expiresAt)>now;
  function bindingStatus(state,hostname){
    hostname=String(hostname||'').toLowerCase();const raw=state?.bindings?.[hostname],binding=typeof raw==='string'?{certificateId:raw,generation:1}:raw;
    const certificate=Array.isArray(state?.certificates)?state.certificates.find(item=>item?.id===binding?.certificateId):null;
    if(!binding||!certificate)return {usable:false,status:'Not verified',label:'Open SSL / TLS to inspect the account binding'};
    if(!Array.isArray(certificate.sans)||!certificate.sans.every(name=>typeof name==='string')||!Number.isInteger(certificate.generation)||certificate.generation<1||!Number.isInteger(binding.generation)||binding.generation<1||!usable(certificate)||!covers(certificate,hostname))return {usable:false,status:'Unavailable',label:'Certificate is expired, revoked, invalid or does not cover this hostname'};
    if(binding.generation!==certificate.generation)return {usable:false,status:'Deployment needed',label:'The current certificate version is not bound to this website'};
    return {usable:true,status:'Bound (demo)',label:certificate.label,generation:certificate.generation,expiresAt:certificate.expiresAt};
  }
  function issueFields({certificates,operations=[],dnsAllowed=true}={}){return [
    {name:'label',label:'Certificate label',required:true,maxLength:100,validate:value=>certificates.some(item=>item.label.toLowerCase()===String(value).trim().toLowerCase())||operations.some(item=>item.kind==='issue'&&['running','scheduled'].includes(item.status)&&item.payload.label.toLowerCase()===String(value).trim().toLowerCase())?'This label already exists or is pending.':''},
    {name:'sans',label:'Account hostnames (SANs)',type:'textarea',required:true,maxLength:2000,default:'api.example.com',help:'Comma or space separated. Only example.com and its subdomains are accepted.',validate:value=>{const list=names(value);if(!list.length||list.length>20||list.some(name=>!ownedHostname(name)))return 'Enter 1–20 owned DNS hostnames, with an optional leading *. wildcard.';if(new Set(list).size!==list.length)return 'Remove duplicate hostnames.';const key=[...list].sort().join(',');return certificates.some(item=>item.status!=='Revoked'&&[...item.sans].sort().join(',')===key)||operations.some(item=>item.kind==='issue'&&['running','scheduled'].includes(item.status)&&[...item.payload.sans].sort().join(',')===key)?'A certificate or pending order already has this exact SAN set.':'';}},
    {name:'challenge',label:'Validation method',type:'select',required:true,options:['HTTP-01','DNS-01'],default:'HTTP-01',validate:(value,values)=>value==='DNS-01'&&!dnsAllowed?'The effective DNS policy does not allow TXT challenge records.':names(values.sans).some(name=>name.startsWith('*.'))&&value!=='DNS-01'?'Wildcard hostnames require DNS-01 in this prototype.':''},
    {name:'algorithm',label:'Key profile',type:'select',required:true,options:['ECDSA P-256','RSA 2048'],default:'ECDSA P-256'},
    {name:'autoRenew',label:'Enable renewal preview',type:'checkbox',default:true}
  ];}
  function deployFields({certificate}={}){return [{name:'hostname',label:'Website hostname',required:true,default:certificate.sans.find(name=>!name.startsWith('*.'))||'www.example.com',validate:value=>!ownedHostname(value)||value.includes('*')?'Choose one owned website hostname.':!usable(certificate)?'This certificate is expired, revoked or unavailable.':!covers(certificate,value)?'The certificate SANs do not cover this hostname.':''},{name:'target',label:'Deployment target',type:'select',required:true,options:['Website HTTPS'],default:'Website HTTPS'}];}
  global.WCPDNSTLSSchemas={zone,supported,defaultTypes,domain,ipv4,ipv6,within,ownerName,relative,dnsPolicy,normalizeRecord,dataKey,recordError,recordFields,validateBatch,dnsIssues,names,ownedHostname,covers,usable,bindingStatus,issueFields,deployFields};
})(globalThis);
