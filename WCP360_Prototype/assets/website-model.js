/* Shared browser-local website inventory; no filesystem or service calls. */
((global)=>{
  'use strict';
  const key='wcp360-website-center-v1',owner='example.com';
  const defaults={tab:'websites',sites:[
    {id:'site-main',domain:'example.com',label:'Main website',type:'PHP',framework:'Laravel',runtime:'PHP',version:'8.4',docroot:'/public_html',environment:'Production',status:'Online',ssl:'Valid',sslDays:74,storage:18.8,requests:'1.24M',response:182,region:'Paris · EU',placement:'Managed by ROOT',branch:'main',lastDeploy:'18 min ago',maintenance:false,cache:true,forceHttps:true,hsts:true,aliases:['www.example.com']},
    {id:'site-shop',domain:'shop.example.com',label:'Online store',type:'WordPress',framework:'WordPress 6.8',runtime:'PHP',version:'8.3',docroot:'/sites/shop/public',environment:'Production',status:'Online',ssl:'Valid',sslDays:61,storage:42.2,requests:'842K',response:246,region:'Paris · EU',placement:'Managed by ROOT',branch:'production',lastDeploy:'2 hours ago',maintenance:false,cache:true,forceHttps:true,hsts:true,aliases:[]},
    {id:'site-api',domain:'api.example.com',label:'Public API',type:'Node.js',framework:'Node.js service',runtime:'Node.js',version:'22 LTS',docroot:'/apps/api',environment:'Production',status:'Online',ssl:'Valid',sslDays:68,storage:3.4,requests:'3.82M',response:94,region:'Paris · EU',placement:'Managed by ROOT',branch:'main',lastDeploy:'46 min ago',maintenance:false,cache:false,forceHttps:true,hsts:true,aliases:[]},
    {id:'site-stage',domain:'staging.example.com',label:'Staging',type:'PHP',framework:'Laravel',runtime:'PHP',version:'8.4',docroot:'/staging/public',environment:'Staging',status:'Protected',ssl:'Valid',sslDays:72,storage:7.1,requests:'24K',response:211,region:'Paris · EU',placement:'Managed by ROOT',branch:'develop',lastDeploy:'Yesterday',maintenance:false,cache:false,forceHttps:true,hsts:false,aliases:[]}
  ],access:[
    {id:'access-deploy',name:'example-deploy',type:'SFTP + SSH',root:'/home/example',auth:'SSH key',lastUsed:'12 min ago',status:'Active'},
    {id:'access-ci',name:'github-actions',type:'Deploy key',root:'example.com',auth:'Ed25519 · read/write',lastUsed:'18 min ago',status:'Active'},
    {id:'access-support',name:'agency-support',type:'SFTP',root:'/sites/shop',auth:'Password + MFA approval',lastUsed:'3 days ago',status:'Expires in 4 days'}
  ],settings:{autoSsl:true,autoRenew:true,forceHttps:true,healthChecks:true,malwareScan:true,accessLogs:30,errorLogs:30,deployRetention:20,stagingProtection:true}};
  function read(){let saved={};try{saved=JSON.parse(global.localStorage.getItem(key))||{};}catch{}return {...structuredClone(defaults),...saved,sites:Array.isArray(saved.sites)?saved.sites:structuredClone(defaults.sites),access:Array.isArray(saved.access)?saved.access:structuredClone(defaults.access),settings:{...defaults.settings,...saved.settings}};}
  function write(state){global.localStorage.setItem(key,JSON.stringify(state));}
  const owned=site=>site&&(!site.owner||site.owner===owner);
  global.WCPWebsiteModel={key,owner,read,write,owned};
})(globalThis);
