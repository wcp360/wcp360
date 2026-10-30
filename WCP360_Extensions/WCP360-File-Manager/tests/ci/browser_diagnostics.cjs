'use strict';
module.exports=function classify(error){
 const text=String(error?.message||'');
 if(/Executable doesn't exist/.test(text))return 'BROWSER_BINARY_MISSING';
 if(/GLIBC_[0-9]/.test(text))return 'BROWSER_ABI_UNAVAILABLE';
 if(/error while loading shared libraries/.test(text))return 'BROWSER_LIBRARY_MISSING';
 if(/SUID sandbox helper binary.*not configured correctly/s.test(text))return 'SUID_HELPER_UNUSABLE';
 if(/apparmor.*denied|denied.*apparmor/is.test(text))return 'APPARMOR_DENIED';
 if(/No usable sandbox/i.test(text))return 'SANDBOX_UNAVAILABLE';
 if(/Operation not permitted|Failed to move to new namespace/i.test(text))return 'NAMESPACE_DENIED';
 if(/chrome_crashpad_handler.*--database is required/s.test(text))return 'CRASHPAD_CONFIGURATION';
 if(/Permission denied/i.test(text))return 'BROWSER_PERMISSION_DENIED';
 if(/Timeout.*exceeded/i.test(text))return 'BROWSER_LAUNCH_TIMEOUT';
 if(/\bEACCES\b/.test(text))return 'BROWSER_PERMISSION_DENIED';
 if(/\bENOENT\b/.test(text))return 'BROWSER_BINARY_MISSING';
 if(/SIGSEGV/.test(text))return 'BROWSER_SIGSEGV';
 if(/SIGABRT/.test(text))return 'BROWSER_SIGABRT';
 if(/SIGTRAP/.test(text))return 'BROWSER_SIGTRAP';
 return 'BROWSER_SUITE_FAILED';
};

module.exports.metadata=function(error){
 const text=String(error?.message||'');
 const exit=text.match(/exitCode=(-?\d+)/),signal=text.match(/signal=(SIG[A-Z]+)/);
 return {error_type:['Error','TypeError','TimeoutError'].includes(error?.name)?error.name:'OTHER',
 launched:text.includes('<launched>'),
 exit_code:exit?Number(exit[1]):null,
 signal:signal&&['SIGABRT','SIGSEGV','SIGTRAP','SIGSYS','SIGKILL','SIGTERM'].includes(signal[1])?signal[1]:null};
};
