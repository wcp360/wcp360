import {readdirSync} from 'node:fs';
const tests=readdirSync(new URL('./',import.meta.url)).filter(name => /^audit_wcp360_.*\.mjs$/.test(name)).sort();
let failures=0;
for(const file of tests){
  try { await import(new URL(file,import.meta.url));if(process.exitCode){failures++;process.exitCode=0;} }
  catch(error){failures++;console.error(file+': '+error.stack);}
}
console.log(JSON.stringify({suite:'WCP360 Prototype',suites:tests.length,failures,browser:'Separate opt-in Chrome runtime audits; not part of this static/adapter runner.'},null,2));
process.exitCode=failures?1:0;
