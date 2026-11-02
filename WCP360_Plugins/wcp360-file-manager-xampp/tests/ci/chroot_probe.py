"""Controlled syscall probe in disposable children, never against a real filesystem root."""
import ctypes
import errno
import json
import os
from pathlib import Path
import sys
import tempfile
from safe_process import execute
from reporting import write_report


def child(mode, target):
    state=dict(line.split(':',1) for line in Path('/proc/self/status').read_text().splitlines() if ':' in line)
    assert os.getuid()==os.getgid()==10001
    assert int(state['CapEff'].strip(),16)==0
    assert state['NoNewPrivs'].strip()=='1' and state['Seccomp'].strip()=='2'
    path=Path(target)
    assert str(path.resolve()).startswith('/tmp/fm-chroot-probe-') and not path.is_symlink() and path.is_dir() and not any(path.iterdir())
    assert '(enforce)' in Path('/proc/self/attr/current').read_text()
    report={'initial_uid':os.getuid(),'initial_cap_eff':0,'nnp':1,'seccomp':2,'namespace_created':False,'mapped_uid':os.getuid()}
    if mode=='mapped':
        libc=ctypes.CDLL(None,use_errno=True)
        libc.unshare.argtypes=[ctypes.c_int];libc.unshare.restype=ctypes.c_int
        assert libc.unshare(0x10000000)==0
        report['namespace_created']=True
        Path('/proc/self/setgroups').write_text('deny\n')
        Path('/proc/self/uid_map').write_text('0 10001 1\n')
        Path('/proc/self/gid_map').write_text('0 10001 1\n')
        report['mapped_uid']=os.getuid()
        assert os.getuid()==os.getgid()==0
    elif mode!='outside':raise ValueError('PROBE_MODE')
    current=dict(line.split(':',1) for line in Path('/proc/self/status').read_text().splitlines() if ':' in line)
    assert current['NoNewPrivs'].strip()=='1' and current['Seccomp'].strip()=='2'
    report['mapped_cap_sys_chroot']=bool(int(current['CapEff'].strip(),16)&(1<<18))
    report['apparmor_enforcing']='(enforce)' in Path('/proc/self/attr/current').read_text()
    try:
        os.chroot(path)
    except OSError as e:
        report.update(chroot_ok=False,errno=e.errno)
    else:
        os.chdir('/')
        report.update(chroot_ok=True,errno=0)
    print(json.dumps(report))


def accepted(report, *, mapped, allowed):
    expected={'initial_uid':10001,'initial_cap_eff':0,'nnp':1,'seccomp':2,
              'namespace_created':mapped,'mapped_uid':0 if mapped else 10001,'chroot_ok':allowed,
              'mapped_cap_sys_chroot':mapped,'apparmor_enforcing':True}
    if not isinstance(report,dict) or set(report)!=set(expected)|{'errno'}:return False
    if any(type(report.get(k)) is not type(v) or report[k]!=v for k,v in expected.items()):return False
    return type(report['errno']) is int and report['errno'] in ({0} if allowed else {errno.EPERM,errno.EACCES})


def main(expect):
    if expect not in ('denied','allowed'):raise ValueError('EXPECTATION')
    report={'status':'FAIL','scope':'CHROOT_SYSCALL_PROBE','expected_inside':expect,'checks':[]}
    try:
        with tempfile.TemporaryDirectory(prefix='fm-chroot-probe-') as target:
            for mode in ('outside','mapped'):
                result=execute([sys.executable,__file__,'--child',mode,target],seconds=15)
                item={'mode':mode,'status':'FAIL','exit_code':result['returncode'],'timed_out':result['timed_out']}
                report['checks'].append(item)
                if result['returncode']!=0 or result['timed_out'] or result['output_limited']:raise ValueError('PROCESS')
                data=json.loads(result['data'])
                fields={'initial_uid':int,'initial_cap_eff':int,'nnp':int,'seccomp':int,'namespace_created':bool,'mapped_uid':int,'chroot_ok':bool,'errno':int,'mapped_cap_sys_chroot':bool,'apparmor_enforcing':bool}
                if not isinstance(data,dict) or set(data)!=set(fields) or any(type(data[k]) is not kind for k,kind in fields.items()) or any(not 0<=data[k]<=10001 for k,kind in fields.items() if kind is int):raise ValueError('SCHEMA')
                item.update(data)
                if mode=='outside' and data['chroot_ok']:
                    report['failure']='OUTER_CHROOT_SUCCEEDED';raise ValueError('OUTER')
                if not accepted(data,mapped=mode=='mapped',allowed=mode=='mapped' and expect=='allowed'):
                    report['failure']='CHROOT_EXPECTATION_FAILED';raise ValueError('EXPECTATION')
                item['status']='PASS'
        report['status']='PASS'
    except Exception:
        report.setdefault('failure','CHROOT_PROBE_FAILED')
    write_report(Path('/evidence')/('chroot-baseline.json' if expect=='denied' else 'chroot-candidate.json'),report)
    print(json.dumps(report))
    return 0 if report['status']=='PASS' else 1


if __name__=='__main__':
    if len(sys.argv)==4 and sys.argv[1]=='--child':child(sys.argv[2],sys.argv[3])
    elif len(sys.argv)==2:raise SystemExit(main(sys.argv[1]))
    else:raise SystemExit(2)
