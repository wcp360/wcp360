"""Docker orchestration on the disposable CI host, no deployment or repository writes."""
import hashlib
import json
import os
from pathlib import Path
import subprocess
import sys

ROOT=Path(__file__).resolve().parents[2]
LOCK=json.loads((ROOT/'tests/ci/toolchain.json').read_text())
LANE=os.environ['FM_PHP_LANE']
assert LANE in LOCK['php']
OUT=Path(os.environ.get('FM_EVIDENCE', str(Path(os.environ.get('RUNNER_TEMP','/tmp'))/'fm-evidence'))).resolve()
OUT.mkdir(parents=True,exist_ok=True);OUT.chmod(0o777)
IMAGE='fm-ci:'+LANE
NAME='fm-test-'+os.environ.get('GITHUB_RUN_ID','local')+'-'+LANE

def run(args, **kwargs):
    return subprocess.run(args,check=True,**kwargs)

def capture(args):
    return subprocess.check_output(args,text=True).strip()

if sys.argv[1]=='build':
    baseline=json.loads((ROOT/'tests/ci/import-baseline.json').read_text())
    for entry in baseline['files']:
        if not entry['harness_delta_allowed']:
            assert hashlib.sha256((ROOT/entry['path']).read_bytes()).hexdigest()==entry['sha256'], 'IMPORT_BASELINE_MISMATCH'
    lock=LOCK['tools']
    args=['docker','build','--no-cache','--platform','linux/amd64','-t',IMAGE,'-f',str(ROOT/'tests/ci/Dockerfile')]
    for k,v in {'PHP_IMAGE':LOCK['php'][LANE]['image'],'BROWSER_IMAGE':LOCK['browser_image'],
                'NODE_VERSION':lock['node'],'NODE_SHA256':lock['node_sha256'],
                'COMPOSER_VERSION':lock['composer'],'COMPOSER_SHA256':lock['composer_sha256'],
                'DEBIAN_SNAPSHOT':LOCK['snapshot']}.items():
        args+=['--build-arg',k+'='+v]
    run(args+[str(ROOT)],timeout=1500)
    identity={'checked_out_sha':capture(['git','rev-parse','HEAD']),
              'parents':capture(['git','show','-s','--format=%P','HEAD']).split(),
              'candidate_sha':os.environ['FM_CANDIDATE_SHA'],
              'run':os.environ.get('GITHUB_RUN_ID'),'attempt':os.environ.get('GITHUB_RUN_ATTEMPT'),
              'image_id':capture(['docker','image','inspect','--format={{.Id}}',IMAGE]),
              'docker':capture(['docker','version','--format={{.Server.Version}}']),
              'kernel':capture(['uname','-r']), 'lane':LANE,'toolchain':LOCK,
              'seccomp_sha256':hashlib.sha256((ROOT/'tests/ci/seccomp.json').read_bytes()).hexdigest()}
    if os.environ.get('GITHUB_EVENT_NAME')=='pull_request':
        assert len(identity['parents'])==2 and identity['parents'][1]==identity['candidate_sha']
    else:assert identity['checked_out_sha']==identity['candidate_sha']
    (OUT/'identity.json').write_text(json.dumps(identity,indent=2)+'\n')
    print(json.dumps(identity))
elif sys.argv[1] in ['test','audit']:
    audit=sys.argv[1]=='audit'
    args=['docker','run','--name',NAME,'--rm','--init','--platform','linux/amd64',
          '--user','10001:10001','--read-only','--cap-drop=ALL','--security-opt','no-new-privileges',
          '--security-opt','seccomp='+str(ROOT/'tests/ci/seccomp.json'),
          '--network','bridge' if audit else 'none','--pids-limit','512','--memory','3g','--cpus','2',
          '--shm-size','512m','--tmpfs','/tmp:rw,nosuid,nodev,size=1g,mode=1777',
          '--mount','type=bind,src='+str(OUT)+',dst=/evidence',
          '--env','FM_PHP_LANE='+LANE, IMAGE,'python3','tests/ci/'+('audit.py' if audit else 'run.py')]
    try:run(args,timeout=180 if audit else 900)
    finally:subprocess.run(['docker','rm','-f',NAME],stdout=subprocess.DEVNULL,stderr=subprocess.DEVNULL)
else:raise SystemExit('UNKNOWN_STAGE')
