"""Parse pinned tool outputs; never export raw output or arbitrary JSON keys."""
import json
import re


def composer_version(data):
    match = re.match(rb'Composer version ([0-9]+\.[0-9]+\.[0-9]+)(?:\s|$)', data)
    if match is None:
        raise ValueError('COMPOSER_VERSION_INVALID')
    return match[1].decode('ascii')


def audit_result(result):
    # Composer 2.11.1 Auditor.php defines these fields; optional failures cannot be ignored.
    try:
        if result['timed_out'] or result['output_limited']:
            raise ValueError('INCOMPLETE')
        data = json.loads(result['data'])
        required = {'advisories', 'abandoned', 'filter', 'cooldown'}
        optional = {'ignored-advisories', 'unreachable-repositories'}
        if not isinstance(data, dict) or not required <= data.keys() or data.keys() - required - optional:
            raise ValueError('SCHEMA')
        if any(not isinstance(v, (dict, list)) for v in data.values()):
            raise ValueError('SCHEMA')
        counts = {k: len(data.get(k, [])) for k in required | optional}
        return {'status': 'PASS' if result['returncode'] == 0 and not any(counts.values()) else 'FAIL',
                'affected_packages': counts['advisories'], 'abandoned_packages': counts['abandoned'],
                'filtered_packages': counts['filter'], 'cooldown_packages': counts['cooldown'],
                'ignored_packages': counts['ignored-advisories'],
                'unreachable_repositories': counts['unreachable-repositories']}
    except (ValueError, TypeError, KeyError):
        return {'status': 'FAIL', 'failure': 'ADVISORY_QUERY_INCOMPLETE'}


def write_report(path, report):
    # Only the already-filtered report is shared with the host runner; private temp files stay 0600.
    path.write_text(json.dumps(report, indent=2) + '\n')
    path.chmod(0o644)


def junit_summary(path):
    import hashlib
    import xml.etree.ElementTree as ET
    if not path.is_file():
        return {'junit':'MISSING','tests':0,'assertions':0,'errors':0,'failures':0,'skipped':0,'cases':[]}
    root=ET.parse(path).getroot()
    cases=list(root.iter('testcase'))
    summary={'junit':'PARSED','tests':len(cases),'assertions':sum(int(n.get('assertions','0')) for n in cases),'errors':0,'failures':0,'skipped':0,'cases':[]}
    for case in cases:
        state='PASS'
        for tag,key in [('failure','failures'),('error','errors'),('skipped','skipped')]:
            if case.find(tag) is not None: summary[key]+=1;state=tag.upper()
        identity=case.get('classname','')+'::'+case.get('name','')
        summary['cases'].append({'id':hashlib.sha256(identity.encode()).hexdigest()[:16],'status':state})
    return summary


def build_summary(result):
    # Registry errors can include signed blob URLs. Keep the entire raw build log private.
    raw=result['data']+result.get('private_stderr',b'')
    codes=[]
    for needle,code in [(b'Forbidden','PROXY_OR_UPSTREAM_FORBIDDEN'),(b'Could not resolve','DNS_FAILED'),(b'Failed to fetch','APT_FETCH_FAILED'),(b'Unable to locate package','APT_PACKAGE_MISSING'),(b'did NOT match','CHECKSUM_MISMATCH'),(b'Connection refused','CONNECTION_REFUSED'),(b'Connection timed out','CONNECTION_TIMEOUT'),(b'No matching distribution','DEPENDENCY_MISSING')]:
        if needle in raw:codes.append(code)
    ok=result['returncode']==0 and not result['timed_out'] and not result['output_limited']
    return {'status':'PASS' if ok else 'FAIL','exit_code':result['returncode'],'timed_out':result['timed_out'],'output_limited':result['output_limited'],'diagnostic_codes':codes}


def browser_failure(data):
    try:
        result=json.loads(data)
        stages={'initialization','php-readiness','browser-launch','browser-version','browser-identity','sandbox-attestation','ui'}
        codes={'NAMESPACE_DENIED','BROWSER_BINARY_MISSING','BROWSER_SUITE_FAILED','BROWSER_ABI_UNAVAILABLE','BROWSER_LIBRARY_MISSING','SANDBOX_UNAVAILABLE','CRASHPAD_CONFIGURATION','BROWSER_PERMISSION_DENIED','BROWSER_SIGTRAP'}
        if result.get('stage') not in stages or result.get('code') not in codes or type(result.get('completed')) is not int or not 0<=result['completed']<=28:
            raise ValueError('SCHEMA')
        return {'browser_stage':result['stage'],'browser_code':result['code'],'completed':result['completed']}
    except (ValueError,TypeError,AttributeError):
        return {'browser_code':'UNRECOGNIZED_REPORT'}
