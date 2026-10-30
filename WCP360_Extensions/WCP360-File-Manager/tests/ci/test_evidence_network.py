import json
import socket
from pathlib import Path
import tempfile
import unittest
from egress_proxy import allowed_target, validate_addresses
from reporting import junit_summary


class EvidenceNetworkTest(unittest.TestCase):
    def test_nested_suites_count_every_case_once(self):
        with tempfile.TemporaryDirectory() as d:
            p=Path(d)/'junit.xml'
            p.write_text('<testsuites><testsuite><testcase name="direct" assertions="3"/><testsuite><testcase name="provider" assertions="2"/></testsuite></testsuite></testsuites>')
            report=junit_summary(p)
            self.assertEqual((report['tests'],report['assertions']),(2,5))

    def test_junit_failure_ids_do_not_export_details(self):
        with tempfile.TemporaryDirectory() as d:
            p=Path(d)/'junit.xml'
            p.write_text('<testsuites><testsuite><testcase name="SYNTHETIC_SECRET" assertions="1"><failure>SYNTHETIC_SECRET</failure><system-out>SYNTHETIC_SECRET</system-out></testcase><testcase name="skip"><skipped/></testcase></testsuite></testsuites>')
            report=junit_summary(p)
            self.assertEqual((report['failures'],report['skipped']),(1,1))
            self.assertNotIn('SYNTHETIC_SECRET',json.dumps(report))

    def test_missing_junit_is_not_a_pass(self):
        self.assertEqual(junit_summary(Path('/nonexistent-unit.xml'))['junit'],'MISSING')

    def test_mcr_class_acquisition_only_and_label_grammar(self):
        hosts=['mcr.microsoft.com']
        for host in ['mcr.microsoft.com','westus.data.mcr.microsoft.com']:
            self.assertEqual(allowed_target(host+':443',hosts,mcr_class=True),host)
        denied=['data.mcr.microsoft.com','foo.bar.data.mcr.microsoft.com','evil.data.mcr.microsoft.com.example.com','-bad.data.mcr.microsoft.com','bad-.data.mcr.microsoft.com','x'*64+'.data.mcr.microsoft.com']
        for host in denied:
            with self.assertRaises(ValueError):allowed_target(host+':443',hosts,mcr_class=True)
        with self.assertRaises(ValueError):allowed_target('westus.data.mcr.microsoft.com:80',hosts,mcr_class=True)
        with self.assertRaises(ValueError):allowed_target('westus.data.mcr.microsoft.com:443',['repo.packagist.org'],mcr_class=False)

    def test_non_global_or_mixed_resolution_is_denied(self):
        def addr(ip):return (socket.AF_INET,socket.SOCK_STREAM,0,'',(ip,443))
        validate_addresses([addr('1.1.1.1')])
        for ip in ['127.0.0.1','10.0.0.1','169.254.169.254','::1','fe80::1','::ffff:127.0.0.1']:
            with self.assertRaises(ValueError):validate_addresses([addr('1.1.1.1'),addr(ip)])
        with self.assertRaises(ValueError):validate_addresses([])

    def test_allowlist_exact_host_and_tls_port_only(self):
        self.assertEqual(allowed_target('repo.packagist.org:443',['repo.packagist.org']),'repo.packagist.org')
        for host in ['repo.packagist.org.evil:443','evil:443','repo.packagist.org:80','user@repo.packagist.org:443','127.0.0.1:443','repo.packagist.org.:443']:
            with self.assertRaises(ValueError):allowed_target(host,['repo.packagist.org'])


if __name__=='__main__':unittest.main()
