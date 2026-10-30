'use strict';
module.exports=function classify(error){
 const text=String(error?.message||'');
 if(/Executable doesn't exist/.test(text))return 'BROWSER_BINARY_MISSING';
 if(/GLIBC_[0-9]/.test(text))return 'BROWSER_ABI_UNAVAILABLE';
 if(/error while loading shared libraries/.test(text))return 'BROWSER_LIBRARY_MISSING';
 if(/No usable sandbox/i.test(text))return 'SANDBOX_UNAVAILABLE';
 if(/Operation not permitted|Failed to move to new namespace/i.test(text))return 'NAMESPACE_DENIED';
 if(/chrome_crashpad_handler.*--database is required/s.test(text))return 'CRASHPAD_CONFIGURATION';
 if(/Permission denied/i.test(text))return 'BROWSER_PERMISSION_DENIED';
 if(/SIGTRAP/.test(text))return 'BROWSER_SIGTRAP';
 return 'BROWSER_SUITE_FAILED';
};
