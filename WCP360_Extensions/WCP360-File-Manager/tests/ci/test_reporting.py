import json
import os
from pathlib import Path
import sys
import tempfile
import unittest
from reporting import audit_result, composer_version, write_report, build_summary, browser_failure, sandbox_summary, browser_evidence
from safe_process import execute


class ReportingTest(unittest.TestCase):
    def browser_result(self):
        return {'browser_version':'156.0.8078.4','browser_uid':10001,'completed':28,
                'sandbox':{'schema':'CHROMIUM_156_SANDBOX_INTERNALS','status':'PASS',
                           'namespace':True,'pid_namespace':True,'network_namespace':True,'seccomp_bpf':True,
                           'states':{'layer1':'Namespace','pid':'Yes','network':'Yes','seccomp':'Yes'}}}

    def test_browser_success_requires_actual_identity_count_and_all_sandbox_properties(self):
        original=self.browser_result()
        self.assertEqual(browser_evidence(original,'156.0.8078.4')['completed'],28)
        for key,value in [('browser_version','wrong'),('browser_uid',0),('completed',27)]:
            report=self.browser_result();report[key]=value
            with self.assertRaises(ValueError):browser_evidence(report,'156.0.8078.4')
        for key in ['namespace','pid_namespace','network_namespace','seccomp_bpf']:
            report=self.browser_result();report['sandbox'][key]=False
            with self.assertRaises(ValueError):browser_evidence(report,'156.0.8078.4')

    def test_sandbox_rows_cannot_be_replaced_by_a_pass_label(self):
        for key,value in [('layer1','SUID'),('pid','No'),('network','MISSING'),('seccomp','DUPLICATE')]:
            report=self.browser_result();report['sandbox']['states'][key]=value
            with self.assertRaises(ValueError):browser_evidence(report,'156.0.8078.4')
        self.assertEqual(sandbox_summary(None)['status'],'UNKNOWN')

    def test_attestation_failure_exports_only_closed_status_and_verified_identity(self):
        data={'stage':'sandbox-attestation','code':'BROWSER_SUITE_FAILED','completed':0,
              'attestation':{'schema':'SYNTHETIC_SECRET','status':'SYNTHETIC_SECRET',
                             'states':{'layer1':'SYNTHETIC_SECRET'},'secret':'SYNTHETIC_SECRET'},
              'browser_identity':{'browser_uid':10001,'browser_version':'SYNTHETIC_SECRET','version_match':True,'secret':'SYNTHETIC_SECRET'}}
        report=browser_failure(json.dumps(data).encode(),'156.0.8078.4')
        self.assertEqual(report['attestation']['states']['layer1'],'UNKNOWN')
        self.assertEqual(report['browser_identity'],{'browser_uid':10001,'version_match':False})
        self.assertNotIn('SYNTHETIC_SECRET',json.dumps(report))

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

    def test_child_checks_and_exit_codes_reject_arbitrary_values(self):
        data={'stage':'browser-launch','code':'BROWSER_SIGABRT','completed':0,'fatal':{'raw_check_codes':['RAW_CHROOT_FDINFO','SYNTHETIC_SECRET'],'zygote_exit_codes':[134,'SYNTHETIC_SECRET',99999],'check_sites':[{'component':'NAMESPACE_UTILS','line':42,'text':'SYNTHETIC_SECRET'}]}}
        report=browser_failure(json.dumps(data).encode())
        self.assertEqual(report['fatal']['raw_check_codes'],['RAW_CHROOT_FDINFO'])
        self.assertEqual(report['fatal']['zygote_exit_codes'],[134])
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
