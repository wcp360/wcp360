"""Online dependency audit, separated from offline product tests; no raw logs exported."""
import datetime
import json
import os
from pathlib import Path
from safe_process import execute

os.umask(0o077)
Path(os.environ['HOME']).mkdir(exist_ok=True)
r = execute(['composer','--no-plugins','audit','--locked','--format=json'], seconds=120)
report = {'status':'FAIL','scope':'DEPENDENCY_ADVISORY_QUERY_ONLY',
          'queried_at':datetime.datetime.now(datetime.timezone.utc).isoformat(),
          'exit_code':r['returncode'],'timed_out':r['timed_out']}
try:
    data=json.loads(r['data'])
    assert not r['output_limited'] and not r['timed_out']
    assert 'advisories' in data and 'abandoned' in data
    report['affected_packages']=len(data['advisories'])
    report['abandoned_packages']=len(data['abandoned'])
    if r['returncode']==0 and report['affected_packages']==report['abandoned_packages']==0:
        report['status']='PASS'
except Exception:
    report['failure']='ADVISORY_QUERY_INCOMPLETE'
Path('/evidence/audit.json').write_text(json.dumps(report,indent=2)+'\n')
print(json.dumps(report))
raise SystemExit(0 if report['status']=='PASS' else 1)
