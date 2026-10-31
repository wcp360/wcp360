/* Business contracts for browser-local backup and schedule simulations. */
((global)=>{
  'use strict';
  const UI=global.WCPUI,day=86400000;
  const same=(a,b)=>UI.normalize(String(a).trim())===UI.normalize(String(b).trim());
  const resources=['files','databases','mail','config'];
  const repositoryCatalog=[{id:'managed-eu',name:'Managed EU repository'},{id:'offsite-eu',name:'Assigned off-site EU vault'},{id:'archive-eu',name:'Assigned EU archive vault'}];
  function selected(values){return resources.filter(name=>values[name]);}
  function resourceFields(defaults,allowed=resources){return resources.filter(name=>allowed.includes(name)).map((name,index)=>({name,label:{files:'Application files',databases:'Databases',mail:'Mailboxes',config:'Account configuration'}[name],type:'checkbox',default:!!defaults?.[name],validate:(_value,values)=>index===0&&!selected(values).some(name=>allowed.includes(name))?'Select at least one resource.':''}));}
  function backup({destinations,policy}={}){return [{name:'label',label:'Restore point label',required:true,maxLength:100},{name:'destinationId',label:'Assigned destination',type:'select',required:true,options:destinations.filter(item=>item.enabled).map(item=>({value:item.id,label:item.name})),default:destinations.find(item=>item.enabled&&item.primary)?.id||destinations.find(item=>item.enabled)?.id||'',validate:value=>destinations.some(item=>item.id===value&&item.enabled)?'':'An enabled destination is required.'},...resourceFields(policy),{name:'verify',label:'Verify the completed demo backup',type:'checkbox',default:true}];}
  function policyFields(policy){return [{name:'cadence',label:'Preview schedule',type:'select',required:true,options:['Every 6 hours','Daily','Weekly'],default:policy.cadence},{name:'retentionDays',label:'Retention (days)',type:'number',required:true,min:1,max:90,default:policy.retentionDays,validate:value=>Number.isInteger(Number(value))?'':'Enter a whole number of days.'},{name:'immutableDays',label:'Immutable retention (days)',type:'number',required:true,min:0,max:90,default:policy.immutableDays,validate:(value,values)=>!Number.isInteger(Number(value))?'Enter a whole number of days.':Number(value)>Number(values.retentionDays)?'Immutable retention cannot exceed total retention.':''},...resourceFields(policy)];}
  const eligible=(point,now=Date.now())=>!!point&&point.owner==='example.com'&&point.verified===true&&Date.parse(point.expiresAt)>now;
  function restore({points,record,now=Date.now()}={}){
    const choices=record?[record]:points.filter(point=>eligible(point,now));const allowed=record?.resources||resources,defaults=record?.resources||choices[0]?.resources||[];
    return [{name:'pointId',label:'Restore point',type:'select',required:true,options:choices.map(point=>({value:point.id,label:point.label})),default:record?.id||choices[0]?.id||'',validate:value=>eligible(points.find(point=>point.id===value),now)?'':'Choose a verified, unexpired point owned by this account.'},{name:'target',label:'Restore target',type:'select',required:true,options:['Staging','Production'],default:'Staging'},...resourceFields(Object.fromEntries(defaults.map(name=>[name,true])),allowed).map(field=>({...field,validate:(value,values)=>{const point=points.find(point=>point.id===values.pointId);if(value&&!point?.resources.includes(field.name))return 'This resource is not present in the chosen point.';return field.validate(value,values);}})),{name:'confirmation',label:'For Production, type example.com',default:'',validate:(value,values)=>values.target==='Production'&&value!=='example.com'?'Type the exact account domain to confirm production impact.':''}];
  }
  function destination({destinations,record}={}){return [{name:'name',label:'Destination label',required:true,maxLength:80,default:record?.name||'',validate:value=>destinations.some(item=>item.id!==record?.id&&same(item.name,value))?'This destination label already exists.':''},{name:'repositoryId',label:'ROOT-assigned repository',type:'select',required:true,options:repositoryCatalog.map(item=>({value:item.id,label:item.name})),default:record?.repositoryId||'offsite-eu',validate:value=>destinations.some(item=>item.id!==record?.id&&item.repositoryId===value)?'This repository is already attached.':''}];}
  function removablePoint(point,operations,now=Date.now()){return !!point&&point.owner==='example.com'&&!point.legalHold&&!(Date.parse(point.immutableUntil)>now)&&!operations.some(item=>item.pointId===point.id&&['running','scheduled'].includes(item.status));}
  function parseField(value,min,max){
    if(!/^[\d*,/\-]+$/.test(value))throw new Error('Use numeric cron fields with *, lists, ranges and steps.');
    const result=new Set();
    for(const part of value.split(',')){
      const pieces=part.split('/');if(pieces.length>2)throw new Error('Invalid cron step.');
      const step=pieces.length===2?Number(pieces[1]):1;if(!Number.isInteger(step)||step<1||step>max-min+1)throw new Error('Invalid cron step.');
      const range=pieces[0]==='*'?[min,max]:pieces[0].split('-').map(Number);
      if(range.length>2||!pieces[0]||pieces[0].endsWith('-'))throw new Error('Invalid cron range.');
      const start=range[0],end=range.length===2?range[1]:pieces.length===2?max:start;
      if(!Number.isInteger(start)||!Number.isInteger(end)||start<min||end>max||end<start)throw new Error('Cron value is outside its field range.');
      for(let n=start;n<=end;n+=step)result.add(n);
    }
    return [...result].sort((a,b)=>a-b);
  }
  function parseCron(expression){
    const fields=String(expression).trim().split(/\s+/);if(fields.length!==5)throw new Error('Enter five numeric fields: minute hour day month weekday.');
    if(fields[2]!=='*'&&fields[4]!=='*')throw new Error('Constrain either day of month or weekday; keep the other field as *.');
    const ranges=[[0,59],[0,23],[1,31],[1,12],[0,6]],parts=fields.map((value,index)=>parseField(value,...ranges[index]));
    const [minutes,hours,days,months,weekdays]=parts;
    const dates=[];for(let t=Date.UTC(2000,0,1);t<Date.UTC(2008,0,1);t+=day){const d=new Date(t);if(days.includes(d.getUTCDate())&&months.includes(d.getUTCMonth()+1)&&weekdays.includes(d.getUTCDay()))dates.push(t/day);}
    if(!dates.length)throw new Error('This expression has no valid calendar date.');
    const offsets=hours.flatMap(hour=>minutes.map(minute=>hour*60+minute));let minGap=Infinity;
    for(let i=1;i<offsets.length;i++)minGap=Math.min(minGap,offsets[i]-offsets[i-1]);
    for(let i=1;i<dates.length;i++)minGap=Math.min(minGap,(dates[i]-dates[i-1])*1440+offsets[0]-offsets.at(-1));
    return {minutes,hours,days,months,weekdays,minGap};
  }
  const timezoneValid=value=>{try{new Intl.DateTimeFormat('en',{timeZone:value}).format(0);return true;}catch{return false;}};
  function nextRuns(expression,timezone='UTC',{from=Date.now(),count=3,horizonDays=31}={}){
    const cron=parseCron(expression);if(!timezoneValid(timezone))throw new Error('Choose a valid IANA timezone.');
    const results=[],start=Math.floor(Number(new Date(from))/60000)*60000+60000,end=start+horizonDays*day;
    const formatter=timezone==='UTC'?null:new Intl.DateTimeFormat('en-GB',{timeZone:timezone,year:'numeric',month:'numeric',day:'numeric',hour:'numeric',minute:'numeric',hourCycle:'h23'});
    for(let t=start;t<end&&results.length<count;t+=60000){
      const utc=new Date(t);let minute,hour,date,month,weekday;
      if(!formatter){minute=utc.getUTCMinutes();hour=utc.getUTCHours();date=utc.getUTCDate();month=utc.getUTCMonth()+1;weekday=utc.getUTCDay();}
      else{const parts=Object.fromEntries(formatter.formatToParts(utc).map(item=>[item.type,item.value]));minute=Number(parts.minute);hour=Number(parts.hour);date=Number(parts.day);month=Number(parts.month);weekday=new Date(Date.UTC(Number(parts.year),month-1,date)).getUTCDay();}
      if(cron.minutes.includes(minute)&&cron.hours.includes(hour)&&cron.days.includes(date)&&cron.months.includes(month)&&cron.weekdays.includes(weekday))results.push(utc.toISOString());
    }
    return results;
  }
  const tenantPolicy={minimumMinutes:5,timeoutSeconds:900,concurrent:2};
  const platformOperations=['Reconcile account quotas','Renew managed certificates','Verify backup manifests','Close usage statements'];
  function schedule({rows,record,scope='user',policy=tenantPolicy}={}){
    return [{name:'name',label:'Schedule name',required:true,maxLength:100,default:record?.name||'',validate:value=>rows.some(item=>item.id!==record?.id&&same(item.name,value))?'This schedule name already exists.':''},{name:'expression',label:'Cron expression',required:true,default:record?.expression||'*/5 * * * *',help:'Five numeric fields. Weekday 0 = Sunday; constrain day or weekday, not both.',validate:value=>{try{return parseCron(value).minGap<(scope==='root'?5:policy.minimumMinutes)?'The expression is more frequent than the delegated minimum interval.':'';}catch(error){return error.message;}}},scope==='root'?{name:'operation',label:'Platform operation',type:'select',required:true,options:platformOperations,default:record?.operation||platformOperations[0]}:{name:'command',label:'Application command',required:true,maxLength:500,default:record?.command||'',help:'Stored as local demo text. It is never executed by the browser.',validate:value=>/[\r\n\x00]/.test(value)?'Use a single-line command.':''},{name:'timezone',label:'IANA timezone',required:true,default:scope==='root'?'UTC':record?.timezone||'Europe/Paris',validate:value=>scope==='root'&&value!=='UTC'?'Platform schedules use UTC.':timezoneValid(value)?'':'Enter a valid IANA timezone.'},{name:'timeoutSeconds',label:'Runtime ceiling (seconds)',type:'number',required:true,min:1,max:scope==='root'?3600:policy.timeoutSeconds,default:record?.timeoutSeconds||300,validate:value=>Number.isInteger(Number(value))?'':'Use a whole number of seconds.'},{name:'enabled',label:'Enable schedule preview',type:'checkbox',default:record?.enabled??true}];
  }
  function tenantPolicyFields(policy){return [{name:'minimumMinutes',label:'Minimum tenant interval (minutes)',type:'number',required:true,min:5,max:60,default:policy.minimumMinutes},{name:'timeoutSeconds',label:'Maximum tenant runtime (seconds)',type:'number',required:true,min:60,max:3600,default:policy.timeoutSeconds},{name:'concurrent',label:'Concurrent runs per account',type:'number',required:true,min:1,max:10,default:policy.concurrent}].map(field=>({...field,validate:value=>Number.isInteger(Number(value))?'':'Enter a whole number.'}));}
  global.WCPProtectionSchemas={resources,selected,repositoryCatalog,backup,policy:policyFields,restore,destination,eligible,removablePoint,parseCron,nextRuns,schedule,tenantPolicy,tenantPolicyFields,platformOperations};
})(globalThis);
