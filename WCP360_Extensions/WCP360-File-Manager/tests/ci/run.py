"""Bounded, offline, non-root test runner. Exports only structured allowlisted results."""
import hashlib
import json
import os
from pathlib import Path
import sys
import platform
import xml.etree.ElementTree as ET
from safe_process import execute
from network_probe import direct_denied
from reporting import composer_version, write_report, junit_summary, browser_failure

ROOT = Path(__file__).resolve().parents[2]
OUT = Path('/evidence')
REPORT = {'status': 'FAIL', 'scope': 'STANDALONE_ALPHA_ONLY', 'suites': []}


def command(args, cwd=ROOT, seconds=180):
    r = execute(args, cwd=cwd, seconds=seconds)
    if r['returncode'] != 0 or r['timed_out'] or r['output_limited']:
        raise RuntimeError('COMMAND_FAILED')
    return r['data']


def suite(name, args, *, cwd=ROOT, json_count=None, seconds=180):
    item = {'suite': name, 'status': 'FAIL'}
    REPORT['suites'].append(item)
    result_process=execute(args,cwd=cwd,seconds=seconds)
    item.update(exit_code=result_process['returncode'],timed_out=result_process['timed_out'],output_limited=result_process['output_limited'])
    data=result_process['data']
    if name=='browser-sandboxed' and result_process['returncode']!=0:
        item.update(browser_failure(data))
    if result_process['returncode']!=0 or result_process['timed_out'] or result_process['output_limited']:
        raise RuntimeError('COMMAND_FAILED')
    if json_count is not None:
        result = json.loads(data)
        checks = result['checks']
        if result['status'] != 'PASS' or len(checks) != json_count:
            raise RuntimeError('COUNT_MISMATCH')
        item['executed'] = len(checks)
        if name == 'browser-sandboxed':
            item['browser_version'] = result['browser_version']
            item['sandbox'] = result['sandbox']
    item['status'] = 'PASS'


def main():
    os.umask(0o077)
    Path(os.environ['HOME']).mkdir(exist_ok=True)
    REPORT['stage']='uid'
    assert os.getuid() == 10001
    status = dict(line.split(':', 1) for line in Path('/proc/self/status').read_text().splitlines() if ':' in line)
    REPORT['stage']='capabilities'
    assert int(status['CapEff'].strip(), 16) == 0
    REPORT['stage']='no-new-privileges'
    assert status['NoNewPrivs'].strip() == '1'
    REPORT['stage']='seccomp'
    assert status['Seccomp'].strip() == '2'
    REPORT['stage']='network'
    assert {p.name for p in Path('/sys/class/net').iterdir()} == {'lo'}
    REPORT['network_probes']=direct_denied()
    REPORT['stage']='toolchain'
    lock = json.loads((ROOT/'tests/ci/toolchain.json').read_text())
    lane = os.environ['FM_PHP_LANE']
    REPORT['stage']='php-version'
    actual_php = command(['php', '-r', 'echo PHP_VERSION;']).decode()
    REPORT['stage']='node-version'
    actual_node = command(['node', '--version']).decode().strip()
    REPORT['stage']='composer-version'
    actual_composer = composer_version(command(['composer', '--no-plugins', '--version', '--no-ansi']))
    REPORT['stage']='php-version-match'
    assert actual_php == lock['php'][lane]['version']
    REPORT['stage']='node-version-match'
    assert actual_node == lock['tools']['node']
    REPORT['stage']='composer-version-match'
    assert actual_composer == lock['tools']['composer']
    REPORT['stage']='extensions'
    REPORT['environment'] = {
        'php': actual_php, 'node': actual_node, 'composer': actual_composer,
        'python': sys.version.split()[0], 'uid': os.getuid(), 'network': 'loopback-only',
        'capabilities': 'none', 'no_new_privileges': True,
        'php_extensions': json.loads(command(['php', '-r', 'echo json_encode(get_loaded_extensions());'])),
        'composer_lock_sha256': hashlib.sha256((ROOT/'composer.lock').read_bytes()).hexdigest(),
        'npm_lock_sha256': hashlib.sha256((ROOT/'tests/ci/package-lock.json').read_bytes()).hexdigest(),
        'dpkg_inventory_sha256': hashlib.sha256(Path('/opt/dpkg-inventory.txt').read_bytes()).hexdigest(),
    }
    for e in ['json','fileinfo','session','ctype','iconv','filter','xml','dom','xmlwriter','libxml','mbstring','phar','tokenizer','zip']:
        assert e in [s.lower() for s in REPORT['environment']['php_extensions']]
    REPORT['stage']='python-identity'
    assert platform.python_implementation()==lock['python']['implementation']
    assert platform.python_version()==lock['python']['version']
    packages={}
    for line in Path('/opt/dpkg-inventory.txt').read_text().splitlines():
        name,version=line.split('\t')
        packages[name]=version
    for name,version in lock['python']['packages'].items(): assert packages[name]==version
    write_report(OUT/'packages.json', {'packages':packages})
    REPORT['stage']='suites'
    suite('harness-boundaries', ['python3', '-m', 'unittest', 'discover', '-s', 'tests/ci', '-p', 'test_*.py'])
    suite('composer-validate', ['composer','--no-plugins','validate','--strict'])
    suite('real-platform', ['composer','--no-plugins','check-platform-reqs'])
    item={'suite':'php-lint','status':'FAIL'}; REPORT['suites'].append(item)
    files=sorted(p for d in ['src','standalone','tests'] for p in (ROOT/d).rglob('*.php'))
    assert files
    for f in files: command(['php','-l',str(f)])
    item.update(status='PASS',executed=len(files))
    for f in ['resources/public/app.js','resources/public/pro.js','tests/browser.cjs']:
        command(['node','--check',f])
    item={'suite':'phpunit','status':'FAIL'}; REPORT['suites'].append(item)
    unit=execute(['php','vendor/bin/phpunit','--do-not-cache-result','--cache-directory','/tmp/phpunit-cache','--log-junit','/tmp/unit.xml'],cwd=ROOT,seconds=180)
    item.update(exit_code=unit['returncode'],timed_out=unit['timed_out'],output_limited=unit['output_limited'])
    item.update(junit_summary(Path('/tmp/unit.xml')))
    assert unit['returncode']==0 and not unit['timed_out'] and not unit['output_limited']
    assert item['tests'] >= 50 and item['assertions'] >= 122
    assert item['failures'] == item['errors'] == item['skipped'] == 0
    item['status']='PASS'
    suite('setup', ['python3','tests/setup_smoke.py'], json_count=5)
    suite('http', ['python3','tests/http_smoke.py'], json_count=46)
    suite('runtime-platform', ['composer','--no-plugins','check-platform-reqs','--no-dev'], cwd=Path('/runtime'))
    suite('runtime-http-no-dev', ['python3','tests/http_smoke.py'], cwd=Path('/runtime'),json_count=46)
    suite('runtime-setup-no-dev', ['python3','tests/setup_smoke.py'], cwd=Path('/runtime'),json_count=5)
    suite('browser-sandboxed', ['node','tests/browser.cjs'], json_count=28, seconds=300)
    REPORT['status']='PASS'


if __name__ == '__main__':
    try:
        main()
    except Exception:
        # No exception text, source lines, responses, credentials or raw child output in artifacts.
        REPORT['failure']='TEST_GATE_FAILED'
        if REPORT['suites']: REPORT['suites'][-1]['status']='FAIL'
    finally:
        OUT.mkdir(exist_ok=True)
        write_report(OUT/'results.json', REPORT)
        print(json.dumps(REPORT))
    sys.exit(0 if REPORT['status']=='PASS' else 1)
