import json
from pathlib import Path
import tempfile
import unittest
from egress_proxy import allowed_target
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

    def test_allowlist_exact_host_and_tls_port_only(self):
        self.assertEqual(allowed_target('repo.packagist.org:443',['repo.packagist.org']),'repo.packagist.org')
        for host in ['repo.packagist.org.evil:443','evil:443','repo.packagist.org:80','user@repo.packagist.org:443','127.0.0.1:443','repo.packagist.org.:443']:
            with self.assertRaises(ValueError):allowed_target(host,['repo.packagist.org'])


if __name__=='__main__':unittest.main()
