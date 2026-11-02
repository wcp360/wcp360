"""Online dependency audit, separated from offline product tests; no raw logs exported."""
import datetime
import json
import os
from pathlib import Path
from safe_process import execute
from reporting import audit_result, write_report
from network_probe import proxy_probe

os.umask(0o077)
Path(os.environ['HOME']).mkdir(exist_ok=True)
network=proxy_probe(os.environ['https_proxy'])
r = execute(['composer','--no-plugins','audit','--locked','--format=json'], seconds=120)
report = {'status':'FAIL','scope':'DEPENDENCY_ADVISORY_QUERY_ONLY',
          'network':network, 'queried_at':datetime.datetime.now(datetime.timezone.utc).isoformat(),
          'exit_code':r['returncode'],'timed_out':r['timed_out']}
report.update(audit_result(r))
write_report(Path('/evidence/audit.json'), report)
print(json.dumps(report))
raise SystemExit(0 if report['status']=='PASS' else 1)
