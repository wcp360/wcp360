'use strict';

// Pinned primary source: Chromium 156.0.8078.4,
// chrome/browser/resources/sandbox_internals/sandbox_internals.ts, linuxHandler.
const schema='CHROMIUM_156_SANDBOX_INTERNALS';
const requirements={
 layer1:{label:'Layer 1 Sandbox',values:['Namespace','SUID','None']},
 pid:{label:'PID namespaces',values:['Yes','No']},
 network:{label:'Network namespaces',values:['Yes','No']},
 seccomp:{label:'Seccomp-BPF sandbox',values:['Yes','No']},
};

module.exports=function attest(rows){
 const states={};
 for(const [key,rule] of Object.entries(requirements)){
  const matches=Array.isArray(rows)?rows.filter(row=>Array.isArray(row)&&row[0]===rule.label):[];
  states[key]=matches.length===0?'MISSING':matches.length!==1?'DUPLICATE':
   matches[0].length!==2||!rule.values.includes(matches[0][1])?'UNKNOWN':matches[0][1];
 }
 const pid_namespace=states.pid==='Yes',network_namespace=states.network==='Yes';
 const namespace=states.layer1==='Namespace'&&pid_namespace&&network_namespace;
 const seccomp_bpf=states.seccomp==='Yes';
 return {schema,status:namespace&&seccomp_bpf?'PASS':'FAIL',namespace,pid_namespace,network_namespace,seccomp_bpf,states};
};
