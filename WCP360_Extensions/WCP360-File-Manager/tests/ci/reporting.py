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
