#!/usr/bin/env python3
# No filesystem/network mutation. Run only by the disposable-VM test harness.
import os, sys

def status():
    for line in open('/proc/self/status'):
        if line.split(':',1)[0] in ('Uid','Gid','CapInh','CapPrm','CapEff','CapBnd','CapAmb','NoNewPrivs','Seccomp'):
            print(line.strip(), flush=True)

status()
pid=os.fork()
if pid==0:
    try:
        os.setgid(33)
        status()
        os.initgroups("www-data",33)
        status()
        os.setuid(33)
        if os.getuid()!=33 or os.geteuid()!=33: raise RuntimeError('identity mismatch')
        print('PASS: service sandbox permits root to drop to UID/GID 33.',flush=True)
        status()
        os._exit(0)
    except Exception as error:
        print('FAIL: service identity drop: '+repr(error),flush=True)
        status()
        os._exit(42)
_, result=os.waitpid(pid,0)
sys.exit(os.waitstatus_to_exitcode(result))
