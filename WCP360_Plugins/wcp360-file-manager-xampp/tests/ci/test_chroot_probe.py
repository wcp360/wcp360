import unittest
from chroot_probe import accepted


class ChrootProbeTest(unittest.TestCase):
    def report(self, mapped=False, allowed=False):
        return {'initial_uid':10001,'initial_cap_eff':0,'nnp':1,'seccomp':2,
                'namespace_created':mapped,'mapped_uid':0 if mapped else 10001,
                'chroot_ok':allowed,'errno':0 if allowed else 1,'mapped_cap_sys_chroot':mapped,'apparmor_enforcing':True}

    def test_baseline_denied_and_candidate_namespaced_success(self):
        self.assertTrue(accepted(self.report(),mapped=False,allowed=False))
        self.assertTrue(accepted(self.report(True),mapped=True,allowed=False))
        self.assertTrue(accepted(self.report(True,True),mapped=True,allowed=True))

    def test_outside_namespace_success_is_failure(self):
        self.assertFalse(accepted(self.report(False,True),mapped=False,allowed=False))

    def test_wrong_uid_caps_nnp_or_seccomp_is_failure(self):
        for key,value in [('initial_uid',0),('initial_cap_eff',1),('nnp',0),('seccomp',0),('mapped_uid',0),('apparmor_enforcing',False),('mapped_cap_sys_chroot',True)]:
            report=self.report();report[key]=value
            self.assertFalse(accepted(report,mapped=False,allowed=False))

    def test_unknown_fields_or_non_permission_failure_are_rejected(self):
        report=self.report();report['secret']='SYNTHETIC_SECRET'
        self.assertFalse(accepted(report,mapped=False,allowed=False))
        report=self.report();report['errno']=2
        self.assertFalse(accepted(report,mapped=False,allowed=False))


if __name__=='__main__':unittest.main()
