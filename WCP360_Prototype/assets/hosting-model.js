/* Single browser-local owner for hosting accounts and retained archives. */
((global)=>{
  'use strict';
  const key='wcp360-hosting-capacity-v1',legacyRecoveryKey='wcp360-account-recovery-v1';
  const defaults={tab:'accounts',plans:[
    {id:'starter',name:'Starter',cpu:1,ram:2,storage:50,iops:1000,bandwidth:100,transfer:2,sites:2,databases:5,mailboxes:10,backup:7,topology:'Single server',accounts:128,status:'Active'},
    {id:'business',name:'Business',cpu:4,ram:8,storage:200,iops:5000,bandwidth:500,transfer:10,sites:25,databases:25,mailboxes:50,backup:30,topology:'Single + replica ready',accounts:164,status:'Active'},
    {id:'agency',name:'Agency',cpu:8,ram:16,storage:500,iops:10000,bandwidth:1000,transfer:30,sites:100,databases:100,mailboxes:200,backup:90,topology:'Cluster ready',accounts:50,status:'Active'}
  ],accounts:[
    {id:'acc-example',domain:'example.com',owner:'Example Company',email:'admin@example.com',plan:'business',primary:'srv-par-01',secondary:'',cluster:'',topology:'Single server',overrides:{cpu:0,ram:0,storage:100,iops:0,bandwidth:0,transfer:0},usage:{cpu:38,ram:52,storage:146,transfer:4.8},status:'Active'},
    {id:'acc-nova',domain:'atelier-nova.fr',owner:'Nova SAS',email:'hosting@atelier-nova.fr',plan:'agency',primary:'cluster-eu-web',secondary:'srv-ams-02',cluster:'EU-WEB-01',topology:'Cluster',overrides:{cpu:2,ram:8,storage:0,iops:5000,bandwidth:0,transfer:10},usage:{cpu:63,ram:68,storage:311,transfer:17.2},status:'Active'},
    {id:'acc-demo',domain:'demo-retail.io',owner:'Retail Demo',email:'demo@example.com',plan:'starter',primary:'srv-par-04',secondary:'',cluster:'',topology:'Single server',overrides:{cpu:0,ram:0,storage:0,iops:0,bandwidth:0,transfer:0},usage:{cpu:12,ram:21,storage:8,transfer:.3},status:'Demo'},
    {id:'acc-legacy',domain:'legacy-shop.fr',owner:'Acme SARL',email:'it@legacy-shop.fr',plan:'business',primary:'srv-par-03',secondary:'',cluster:'',topology:'Single server',overrides:{cpu:0,ram:0,storage:0,iops:0,bandwidth:0,transfer:0},usage:{cpu:81,ram:88,storage:191,transfer:9.1},status:'Review'}
  ],servers:[
    {id:'srv-par-01',name:'srv-par-01',region:'Paris · AZ1',role:'Web + data',cpu:64,cpuUsed:68,ram:256,ramUsed:71,storage:8,storageUsed:62,iops:80000,iopsUsed:43,bandwidth:10,accounts:84,status:'Healthy'},
    {id:'srv-par-02',name:'srv-par-02',region:'Paris · AZ2',role:'Web + data',cpu:64,cpuUsed:54,ram:256,ramUsed:58,storage:8,storageUsed:49,iops:80000,iopsUsed:37,bandwidth:10,accounts:71,status:'Healthy'},
    {id:'srv-ams-02',name:'srv-ams-02',region:'Amsterdam · AZ1',role:'Replica',cpu:48,cpuUsed:42,ram:192,ramUsed:46,storage:6,storageUsed:44,iops:60000,iopsUsed:31,bandwidth:10,accounts:52,status:'Healthy'},
    {id:'srv-par-04',name:'srv-par-04',region:'Paris · Sandbox',role:'Demo accounts',cpu:32,cpuUsed:29,ram:128,ramUsed:33,storage:4,storageUsed:28,iops:30000,iopsUsed:22,bandwidth:5,accounts:38,status:'Healthy'}
  ],addons:[
    {id:'storage-100',icon:'hard_drive',name:'+100 GB NVMe storage',resource:'storage',amount:100,unit:'GB',scope:'Account or project',description:'Expands persistent account storage without changing the base plan.'},
    {id:'ram-2',icon:'memory',name:'+2 GB guaranteed RAM',resource:'ram',amount:2,unit:'GB',scope:'Account',description:'Adds guaranteed memory to PHP, workers and application services.'},
    {id:'cpu-1',icon:'developer_board',name:'+1 vCPU',resource:'cpu',amount:1,unit:'vCPU',scope:'Account',description:'Adds schedulable compute capacity to the account container.'},
    {id:'iops-1000',icon:'speed',name:'+1,000 IOPS',resource:'iops',amount:1000,unit:'IOPS',scope:'Storage volume',description:'Raises the guaranteed disk operation budget.'},
    {id:'transfer-5',icon:'swap_vert',name:'+5 TB monthly transfer',resource:'transfer',amount:5,unit:'TB',scope:'Account',description:'Extends monthly outbound transfer allowance.'},
    {id:'bandwidth-500',icon:'network_check',name:'+500 Mbps bandwidth',resource:'bandwidth',amount:500,unit:'Mbps',scope:'Account',description:'Raises the account network throughput ceiling.'},
    {id:'secondary',icon:'dns',name:'Secondary server',resource:'secondary',amount:1,unit:'node',scope:'Project',description:'Adds a warm application replica on another server or availability zone.'},
    {id:'cluster',icon:'hub',name:'Managed cluster placement',resource:'cluster',amount:1,unit:'cluster',scope:'Project',description:'Places workloads behind a managed multi-node topology with failover.'}
  ]};
  const seed=[{id:'rec-old-demo',domain:'old-demo.example.com',owner:'Example Company',email:'admin@example.com',plan:'starter',archive:'ARC-2026-1042',size:'8.4 GB',terminated:'2026-10-24',expires:'2026-11-23',state:'Restorable'},{id:'rec-legacy',domain:'legacy-campaign.example.net',owner:'Nova Agency',email:'ops@example.com',plan:'business',archive:'ARC-2026-1031',size:'42.1 GB',terminated:'2026-10-18',expires:'2026-11-17',state:'Review'}];
  function read(){let saved={},legacy;try{saved=JSON.parse(global.localStorage.getItem(key)||'null')||{};}catch{}try{legacy=JSON.parse(global.localStorage.getItem(legacyRecoveryKey)||'null');}catch{}const state={...structuredClone(defaults),...saved};for(const name of ['plans','accounts','servers','addons'])if(!Array.isArray(state[name]))state[name]=structuredClone(defaults[name]);state.archives=Array.isArray(saved.archives)?saved.archives:Array.isArray(legacy)?legacy:structuredClone(seed);state.accounts.forEach(account=>{account.email=account.email||'admin@'+account.domain;});return state;}
  function write(state){global.localStorage.setItem(key,JSON.stringify(state));try{global.localStorage.setItem(legacyRecoveryKey,JSON.stringify(state.archives));}catch{}}
  global.WCPHostingModel=Object.freeze({key,legacyRecoveryKey,read,write});
})(globalThis);
