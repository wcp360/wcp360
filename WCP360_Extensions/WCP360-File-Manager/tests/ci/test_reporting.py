import json
import os
from pathlib import Path
import sys
import tempfile
import unittest
from reporting import audit_result, composer_version, write_report, build_summary, browser_failure
from safe_process import execute


class ReportingTest(unittest.TestCase):
    def result(self, data, **extra):
        return dict(returncode=0, timed_out=False, output_limited=False, data=json.dumps(data).encode(), **extra)

    def clean(self):
        return {'advisories': [], 'abandoned': [], 'filter': [], 'cooldown': []}

    def test_stderr_does_not_corrupt_structured_stdout(self):
        cmd = "import sys,json; print('SYNTHETIC_SECRET warning',file=sys.stderr); print(json.dumps(" + repr(self.clean()) + "))"
        r = execute([sys.executable, '-c', cmd])
        self.assertNotIn(b'SYNTHETIC_SECRET', r['data'])
        self.assertEqual(audit_result(r)['status'], 'PASS')

    def test_version_requires_expected_format(self):
        self.assertEqual(composer_version(b'Composer version 2.11.1 2026-10-01\n'), '2.11.1')
        with self.assertRaises(ValueError):
            composer_version(b'warning SYNTHETIC_SECRET\nComposer version 2.11.1')

    def test_invalid_or_missing_json_is_not_pass(self):
        for data in [{}, {'status': 'PASS'}, [], None]:
            self.assertEqual(audit_result(self.result(data))['status'], 'FAIL')

    def test_nonzero_timeout_and_truncation_are_fail(self):
        for key, value in [('returncode', 1), ('timed_out', True), ('output_limited', True)]:
            result = self.result(self.clean()); result[key] = value
            self.assertEqual(audit_result(result)['status'], 'FAIL')

    def test_advisory_ignored_or_unreachable_never_passes(self):
        for key in ['advisories', 'abandoned', 'filter', 'cooldown', 'ignored-advisories', 'unreachable-repositories']:
            data = self.clean(); data[key] = ['SYNTHETIC_SECRET']
            report = audit_result(self.result(data))
            self.assertEqual(report['status'], 'FAIL')
            self.assertNotIn('SYNTHETIC_SECRET', json.dumps(report))

    def test_unknown_field_fails_without_echo(self):
        data = self.clean(); data['secret'] = 'SYNTHETIC_SECRET'
        report = audit_result(self.result(data))
        self.assertEqual(report['status'], 'FAIL')
        self.assertNotIn('SYNTHETIC_SECRET', json.dumps(report))

    def test_build_failure_never_exports_signed_urls(self):
        result={'returncode':1,'timed_out':False,'output_limited':False,'data':b'', 'private_stderr':b'Forbidden https://region.data.mcr.microsoft.com/blob?sig=SYNTHETIC_SECRET'}
        report=build_summary(result)
        self.assertEqual(report['diagnostic_codes'],['PROXY_OR_UPSTREAM_FORBIDDEN'])
        self.assertNotIn('SYNTHETIC_SECRET',json.dumps(report))

    def test_browser_failure_schema_never_echoes_arbitrary_text(self):
        result=browser_failure(b'{"stage":"SYNTHETIC_SECRET","code":"SYNTHETIC_SECRET","completed":0}')
        self.assertNotIn('SYNTHETIC_SECRET',json.dumps(result))
        self.assertEqual(result['browser_code'],'UNRECOGNIZED_REPORT')

    def test_browser_probe_drops_unknown_or_wrong_type_fields(self):
        data={'stage':'browser-launch','code':'BROWSER_SUITE_FAILED','completed':0,
              'probe':{'binary_exists':True,'ldd_exit':0,'secret':'SYNTHETIC_SECRET','missing_libraries':'SYNTHETIC_SECRET'},
              'launch':{'error_type':'Error','signal':'SYNTHETIC_SECRET','exit_code':127,'secret':'SYNTHETIC_SECRET'}}
        report=browser_failure(json.dumps(data).encode())
        self.assertEqual(report['probe'],{'binary_exists':True,'ldd_exit':0})
        self.assertEqual(report['launch'],{'error_type':'Error','exit_code':127})
        self.assertNotIn('SYNTHETIC_SECRET',json.dumps(report))

    def test_fatal_sites_reject_unknown_components_and_arbitrary_fields(self):
        data={'stage':'browser-launch','code':'BROWSER_SIGABRT','completed':0,'fatal':{'fatal':True,'secret':'SYNTHETIC_SECRET','sites':[{'component':'ZYGOTE_HOST','line':208,'message':'SYNTHETIC_SECRET'},{'component':'SYNTHETIC_SECRET','line':12}]}}
        report=browser_failure(json.dumps(data).encode())
        self.assertEqual(report['fatal']['sites'],[{'component':'ZYGOTE_HOST','line':208}])
        self.assertNotIn('SYNTHETIC_SECRET',json.dumps(report))

    def test_report_readable_despite_private_umask(self):
        with tempfile.TemporaryDirectory() as d:
            old = os.umask(0o077)
            try:
                p = Path(d)/'results.json'; write_report(p, {'status': 'FAIL'})
                self.assertEqual(p.stat().st_mode & 0o777, 0o644)
            finally:
                os.umask(old)


if __name__ == '__main__':
    unittest.main()
