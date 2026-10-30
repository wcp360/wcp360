'use strict';
const test=require('node:test'),assert=require('node:assert/strict');
const classify=require('./browser_diagnostics.cjs');
test('browser diagnostics export only fixed codes',()=>{
 for(const [text,code] of [['GLIBC_2.38 not found','BROWSER_ABI_UNAVAILABLE'],['error while loading shared libraries: libx.so','BROWSER_LIBRARY_MISSING'],['No usable sandbox!','SANDBOX_UNAVAILABLE'],['Failed to move to new namespace','NAMESPACE_DENIED'],['chrome_crashpad_handler: --database is required','CRASHPAD_CONFIGURATION'],['Permission denied','BROWSER_PERMISSION_DENIED'],['SIGTRAP','BROWSER_SIGTRAP'],['https://example.test/?sig=SYNTHETIC_SECRET','BROWSER_SUITE_FAILED']]){
  assert.equal(classify(new Error(text+' SYNTHETIC_SECRET')),code);
  assert(!classify(new Error(text+' SYNTHETIC_SECRET')).includes('SYNTHETIC_SECRET'));
 }
});

test('launch metadata excludes arbitrary diagnostic text',()=>{
 const e=new Error('<launched> pid=123 <process did exit: exitCode=127, signal=SIGABRT> SYNTHETIC_SECRET');
 assert.deepEqual(classify.metadata(e),{error_type:'Error',launched:true,exit_code:127,signal:'SIGABRT'});
 assert(!JSON.stringify(classify.metadata(e)).includes('SYNTHETIC_SECRET'));
 assert.equal(classify(new Error('Timeout 30000ms exceeded SYNTHETIC_SECRET')),'BROWSER_LAUNCH_TIMEOUT');
});

test('sandbox failures stay fixed and do not suggest bypass',()=>{
 assert.equal(classify(new Error('The SUID sandbox helper binary was found, but is not configured correctly. SYNTHETIC_SECRET')),'SUID_HELPER_UNUSABLE');
 assert.equal(classify(new Error('apparmor denied SYNTHETIC_SECRET')),'APPARMOR_DENIED');
 assert.equal(classify(new Error('exit signal=SIGABRT SYNTHETIC_SECRET')),'BROWSER_SIGABRT');
 assert.equal(classify(new Error('exit signal=SIGSEGV SYNTHETIC_SECRET')),'BROWSER_SIGSEGV');
});

test('fatal sites use a reviewed static source registry, never raw messages',()=>{
 const d=classify.fatal(new Error('[pid][err] FATAL:content/browser/zygote_host/zygote_host_impl_linux.cc:208] Check failed: SYNTHETIC_SECRET https://example.test/?sig=SYNTHETIC_SECRET sandbox'));
 assert.deepEqual(d.sites,[{component:'ZYGOTE_HOST',line:208}]);assert(d.fatal&&d.check&&d.sandbox);
 assert(!JSON.stringify(d).includes('SYNTHETIC_SECRET'));
 const unknown=classify.fatal(new Error('FATAL:private/SYNTHETIC_SECRET.cc:12] SYNTHETIC_SECRET'));
 assert(unknown.unclassified_fatal);assert.deepEqual(unknown.sites,[]);
 assert(!JSON.stringify(unknown).includes('SYNTHETIC_SECRET'));
});

test('child RAW_CHECK and zygote exits export only static codes and bounded integers',()=>{
 const e=new Error('Check failed: sys_chroot("/proc/self/fdinfo/") == 0\nZygote process exited prematurely with exit code 134\nSYNTHETIC_SECRET https://example.test/?sig=SYNTHETIC_SECRET');
 const d=classify.fatal(e);assert.deepEqual(d.raw_check_codes,['RAW_CHROOT_FDINFO']);assert.deepEqual(d.zygote_exit_codes,[134]);
 assert(!JSON.stringify(d).includes('/proc/'));assert(!JSON.stringify(d).includes('SYNTHETIC_SECRET'));
 assert.deepEqual(classify.fatal(new Error('Check failed: SYNTHETIC_SECRET')).raw_check_codes,[]);
});
