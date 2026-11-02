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


def sandbox_summary(value):
    value = value if isinstance(value, dict) else {}
    schema = 'CHROMIUM_156_SANDBOX_INTERNALS'
    report = {'schema': schema if value.get('schema') == schema else 'UNKNOWN',
              'status': value.get('status') if value.get('status') in ('PASS', 'FAIL') else 'UNKNOWN'}
    for key in ('namespace', 'pid_namespace', 'network_namespace', 'seccomp_bpf'):
        report[key] = value.get(key) if type(value.get(key)) is bool else False
    states = value.get('states', {})
    states = states if isinstance(states, dict) else {}
    report['states'] = {}
    for key, allowed in {'layer1': ('Namespace', 'SUID', 'None'),
                         'pid': ('Yes', 'No'), 'network': ('Yes', 'No'), 'seccomp': ('Yes', 'No')}.items():
        state = states.get(key, 'MISSING')
        report['states'][key] = state if state in allowed + ('MISSING', 'DUPLICATE', 'UNKNOWN') else 'UNKNOWN'
    return report


def browser_evidence(result, expected_version):
    expected = {'schema': 'CHROMIUM_156_SANDBOX_INTERNALS', 'status': 'PASS',
                'namespace': True, 'pid_namespace': True, 'network_namespace': True, 'seccomp_bpf': True,
                'states': {'layer1': 'Namespace', 'pid': 'Yes', 'network': 'Yes', 'seccomp': 'Yes'}}
    sandbox = sandbox_summary(result.get('sandbox'))
    if (result.get('browser_version') != expected_version or type(result.get('browser_uid')) is not int
            or result['browser_uid'] != 10001 or type(result.get('completed')) is not int
            or result['completed'] != 28 or sandbox != expected):
        raise ValueError('BROWSER_EVIDENCE_INVALID')
    return {'browser_version': expected_version, 'browser_uid': 10001, 'completed': 28, 'sandbox': sandbox}


def browser_failure(data, expected_browser_version=None):
    try:
        result=json.loads(data)
        stages={'initialization','php-readiness','browser-launch','browser-preflight','userns-probe','browser-version','browser-identity','sandbox-attestation','ui'}
        codes={'NAMESPACE_DENIED','BROWSER_BINARY_MISSING','BROWSER_SUITE_FAILED','BROWSER_ABI_UNAVAILABLE','BROWSER_LIBRARY_MISSING','SANDBOX_UNAVAILABLE','CRASHPAD_CONFIGURATION','BROWSER_PERMISSION_DENIED','BROWSER_SIGTRAP','BROWSER_LAUNCH_TIMEOUT','SUID_HELPER_UNUSABLE','APPARMOR_DENIED','BROWSER_SIGSEGV','BROWSER_SIGABRT'}
        if result.get('stage') not in stages or result.get('code') not in codes or type(result.get('completed')) is not int or not 0<=result['completed']<=28:
            raise ValueError('SCHEMA')
        report={'browser_stage':result['stage'],'browser_code':result['code'],'completed':result['completed']}
        probe=result.get('probe',{})
        filtered={}
        for key in ['binary_exists','binary_executable','abi_unavailable','userns_created','apparmor_enforcing']:
            if type(probe.get(key)) is bool:filtered[key]=probe[key]
        for key in ['ldd_exit','missing_libraries','userns_probe_exit','userns_errno','userns_uid','apparmor_restrict_userns','userns_clone_enabled']:
            if type(probe.get(key)) is int and -999<=probe[key]<=10001:filtered[key]=probe[key]
        report['probe']=filtered
        launch=result.get('launch',{})
        filtered={}
        if launch.get('error_type') in ['Error','TypeError','TimeoutError','OTHER']:filtered['error_type']=launch['error_type']
        if type(launch.get('launched')) is bool:filtered['launched']=launch['launched']
        if type(launch.get('exit_code')) is int and -255<=launch['exit_code']<=255:filtered['exit_code']=launch['exit_code']
        if launch.get('signal') in ['SIGABRT','SIGSEGV','SIGTRAP','SIGSYS','SIGKILL','SIGTERM']:filtered['signal']=launch['signal']
        report['launch']=filtered
        fatal=result.get('fatal',{})
        filtered={}
        for key in ['fatal','check','sandbox','unclassified_fatal']:
            if type(fatal.get(key)) is bool:filtered[key]=fatal[key]
        from pathlib import Path
        registry=json.loads((Path(__file__).parent/'chromium_diagnostic_sources.json').read_text());components=registry['components']
        sites=fatal.get('sites',[])
        filtered['sites']=[{'component':s['component'],'line':s['line']} for s in sites[:12] if isinstance(s,dict) and s.get('component') in components and type(s.get('line')) is int and 0<s['line']<=20000] if isinstance(sites,list) else []
        raw=fatal.get('raw_check_codes',[])
        filtered['raw_check_codes']=[c for c in raw[:6] if isinstance(c,str) and c in registry['raw_checks']] if isinstance(raw,list) else []
        exits=fatal.get('zygote_exit_codes',[])
        filtered['zygote_exit_codes']=[c for c in exits[:4] if type(c) is int and -255<=c<=255] if isinstance(exits,list) else []
        sites=fatal.get('check_sites',[])
        filtered['check_sites']=[{'component':s['component'],'line':s['line']} for s in sites[:12] if isinstance(s,dict) and s.get('component') in components and type(s.get('line')) is int and 0<s['line']<=20000] if isinstance(sites,list) else []
        report['fatal']=filtered
        report['attestation']=sandbox_summary(result.get('attestation'))
        identity=result.get('browser_identity',{})
        filtered={}
        if isinstance(identity,dict):
            if type(identity.get('version_match')) is bool:
                filtered['version_match']=(identity['version_match'] and expected_browser_version is not None
                                           and identity.get('browser_version')==expected_browser_version)
            if expected_browser_version is not None and identity.get('browser_version')==expected_browser_version:
                filtered['browser_version']=expected_browser_version
            if type(identity.get('browser_uid')) is int and 0<=identity['browser_uid']<=65535:
                filtered['browser_uid']=identity['browser_uid']
        report['browser_identity']=filtered
        return report
    except (ValueError,TypeError,AttributeError):
        return {'browser_code':'UNRECOGNIZED_REPORT'}
