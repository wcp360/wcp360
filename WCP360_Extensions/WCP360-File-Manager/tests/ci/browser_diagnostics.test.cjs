'use strict';
const test=require('node:test'),assert=require('node:assert/strict');
const classify=require('./browser_diagnostics.cjs');
test('browser diagnostics export only fixed codes',()=>{
 for(const [text,code] of [['GLIBC_2.38 not found','BROWSER_ABI_UNAVAILABLE'],['error while loading shared libraries: libx.so','BROWSER_LIBRARY_MISSING'],['No usable sandbox!','SANDBOX_UNAVAILABLE'],['Failed to move to new namespace','NAMESPACE_DENIED'],['chrome_crashpad_handler: --database is required','CRASHPAD_CONFIGURATION'],['Permission denied','BROWSER_PERMISSION_DENIED'],['SIGTRAP','BROWSER_SIGTRAP'],['https://example.test/?sig=SYNTHETIC_SECRET','BROWSER_SUITE_FAILED']]){
  assert.equal(classify(new Error(text+' SYNTHETIC_SECRET')),code);
  assert(!classify(new Error(text+' SYNTHETIC_SECRET')).includes('SYNTHETIC_SECRET'));
 }
});
