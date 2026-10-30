"""Reversible firewall/proxy setup on disposable GitHub VM. Never a tenant host."""
import json
import os
from pathlib import Path
import shutil
import signal
import socket
import subprocess
import sys
import time
from network_probe import direct_denied

HERE=Path(__file__).resolve().parent
STATE=Path('/tmp/fm-egress')
PROXY_UID='19001'


def run(args,check=True):
    return subprocess.run(args,check=check,stdout=subprocess.DEVNULL,stderr=subprocess.DEVNULL,timeout=30)


def ipt(binary,*args,check=True):return run([binary,'-w','10',*args],check=check)


def setup():
    assert os.getuid()==0 and os.environ.get('GITHUB_ACTIONS')=='true'
    STATE.mkdir(mode=0o755,exist_ok=True)
    run(['useradd','--uid',PROXY_UID,'--no-create-home','--shell','/usr/sbin/nologin','fmproxy'])
    for name in ['egress_proxy.py','network_policy.json']:shutil.copyfile(HERE/name,STATE/name)
    receipt=STATE/'egress.json';receipt.touch();os.chown(receipt,int(PROXY_UID),int(PROXY_UID))
    process=subprocess.Popen(['runuser','-u','fmproxy','--','python3',str(STATE/'egress_proxy.py'),str(STATE/'network_policy.json'),str(receipt)],stdout=subprocess.DEVNULL,stderr=subprocess.DEVNULL,start_new_session=True)
    (STATE/'proxy.pid').write_text(str(process.pid))
    for _ in range(50):
        try:
            with socket.create_connection(('127.0.0.1',18080),timeout=.2):break
        except OSError:time.sleep(.1)
    else:raise RuntimeError('PROXY_NOT_READY')
    # Docker daemon must tunnel via proxy. Root-owned host processes cannot bypass it.
    for binary in ['iptables','ip6tables']:
        ipt(binary,'-N','FM_OUTPUT')
        ipt(binary,'-A','FM_OUTPUT','-o','lo','-j','RETURN')
        ipt(binary,'-A','FM_OUTPUT','-m','conntrack','--ctstate','ESTABLISHED,RELATED','-j','RETURN')
        ipt(binary,'-A','FM_OUTPUT','-m','owner','--uid-owner','0','-j','REJECT')
        ipt(binary,'-A','FM_OUTPUT','-j','RETURN')
        ipt(binary,'-I','OUTPUT','1','-j','FM_OUTPUT')
    # No forwarded internet traffic from build/audit containers, including direct DNS.
    ipt('iptables','-N','FM_FORWARD')
    ipt('iptables','-A','FM_FORWARD','-j','REJECT')
    ipt('iptables','-I','FORWARD','1','-j','FM_FORWARD')
    ipt('ip6tables','-N','FM_FORWARD')
    ipt('ip6tables','-A','FM_FORWARD','-j','REJECT')
    ipt('ip6tables','-I','FORWARD','1','-j','FM_FORWARD')
    # Proxy listener is reachable only via loopback or Docker's local bridge.
    ipt('iptables','-N','FM_INPUT')
    for interface in ['lo','docker0']:ipt('iptables','-A','FM_INPUT','-i',interface,'-j','RETURN')
    ipt('iptables','-A','FM_INPUT','-p','tcp','-m','multiport','--dports','18080,18081','-j','REJECT')
    ipt('iptables','-A','FM_INPUT','-j','RETURN');ipt('iptables','-I','INPUT','1','-j','FM_INPUT')
    dropin=Path('/etc/systemd/system/docker.service.d/fm-egress.conf');dropin.parent.mkdir(exist_ok=True)
    assert not dropin.exists()
    dropin.write_text('[Service]\nEnvironment="HTTP_PROXY=http://127.0.0.1:18080" "HTTPS_PROXY=http://127.0.0.1:18080" "NO_PROXY=localhost,127.0.0.1"\n')
    run(['systemctl','daemon-reload']);run(['systemctl','restart','docker'])
    # Docker restart may insert FORWARD rules. Reassert the deny chain at position one.
    ipt('iptables','-D','FORWARD','-j','FM_FORWARD');ipt('iptables','-I','FORWARD','1','-j','FM_FORWARD')
    gateway=subprocess.check_output(['docker','network','inspect','bridge','--format={{(index .IPAM.Config 0).Gateway}}'],text=True).strip()
    (STATE/'gateway').write_text(gateway)
    probes=direct_denied()
    print(json.dumps({'network_policy':'ENFORCED','proxy_uid':int(PROXY_UID),'direct_container_egress':'DENY','direct_daemon_egress':'DENY','root_probes':probes}))


def cleanup():
    for binary,chain,hook in [('ip6tables','FM_FORWARD','FORWARD'),('iptables','FM_FORWARD','FORWARD'),('iptables','FM_INPUT','INPUT'),('iptables','FM_OUTPUT','OUTPUT'),('ip6tables','FM_OUTPUT','OUTPUT')]:
        ipt(binary,'-D',hook,'-j',chain,check=False);ipt(binary,'-F',chain,check=False);ipt(binary,'-X',chain,check=False)
    Path('/etc/systemd/system/docker.service.d/fm-egress.conf').unlink(missing_ok=True)
    run(['systemctl','daemon-reload'],check=False);run(['systemctl','restart','docker'],check=False)
    if (STATE/'proxy.pid').exists():
        try:os.killpg(int((STATE/'proxy.pid').read_text()),signal.SIGTERM)
        except ProcessLookupError:pass
        time.sleep(.3)
    out=Path(os.environ['RUNNER_TEMP'])/'fm-evidence';out.mkdir(exist_ok=True)
    if (STATE/'egress.json').exists() and (STATE/'egress.json').stat().st_size:
        data=json.loads((STATE/'egress.json').read_text())
        (out/'egress.json').write_text(json.dumps(data,indent=2)+'\n');(out/'egress.json').chmod(0o644)


if __name__=='__main__':
    if sys.argv[1]=='setup':setup()
    elif sys.argv[1]=='cleanup':cleanup()
    elif sys.argv[1]=='audit':ipt('iptables','-I','FM_INPUT','1','-i','docker0','-p','tcp','--dport','18080','-j','REJECT')
    else:raise SystemExit(2)
