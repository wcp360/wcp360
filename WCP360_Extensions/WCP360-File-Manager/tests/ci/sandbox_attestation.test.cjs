'use strict';
const test=require('node:test'),assert=require('node:assert/strict');
const attest=require('./sandbox_attestation.cjs');
const rows=()=>[['Layer 1 Sandbox','Namespace'],['PID namespaces','Yes'],['Network namespaces','Yes'],['Seccomp-BPF sandbox','Yes']];

test('the pinned Linux table requires all four sandbox properties',()=>{
 const result=attest(rows());
 assert.equal(result.status,'PASS');
 for(const key of ['namespace','pid_namespace','network_namespace','seccomp_bpf'])assert.equal(result[key],true);
});
test('each missing property fails closed',()=>{
 for(let i=0;i<4;i++)assert.equal(attest(rows().filter((_,j)=>i!==j)).status,'FAIL');
});
test('each duplicate property fails closed even if both values are positive',()=>{
 for(let i=0;i<4;i++)assert.equal(attest([...rows(),rows()[i]]).status,'FAIL');
});
test('SUID, None and every negative namespace/seccomp value are rejected',()=>{
 for(const value of ['SUID','None']){const input=rows();input[0][1]=value;assert.equal(attest(input).status,'FAIL');}
 for(let i=1;i<4;i++){const input=rows();input[i][1]='No';assert.equal(attest(input).status,'FAIL');}
});
test('legacy labels and malformed tables cannot attest this browser version',()=>{
 for(const input of [null,{},[],[['Namespace sandbox','Yes'],['Seccomp-BPF sandbox','Yes']]])assert.equal(attest(input).status,'FAIL');
 const malformed=rows();malformed[0].push('ignored');assert.equal(attest(malformed).status,'FAIL');
});
test('unknown labels and values never escape as diagnostics',()=>{
 const input=rows();input[0][1]='SYNTHETIC_SECRET https://example.test/?sig=SYNTHETIC_SECRET';
 input.push(['SYNTHETIC_SECRET','SYNTHETIC_SECRET']);
 const result=attest(input);assert.equal(result.status,'FAIL');assert.equal(result.states.layer1,'UNKNOWN');
 assert(!JSON.stringify(result).includes('SYNTHETIC_SECRET'));
});
test('unrelated status rows cannot substitute for the required exact labels',()=>{
 const input=rows().slice(0,3);input.push(['Seccomp-BPF sandbox supports TSYNC','Yes']);
 assert.equal(attest(input).status,'FAIL');
 assert.equal(attest([...rows(),['Ptrace Protection with Yama LSM (Non-broker)','No']]).status,'PASS');
});
