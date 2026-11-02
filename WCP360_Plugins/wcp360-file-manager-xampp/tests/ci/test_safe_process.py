import contextlib
import io
import os
from pathlib import Path
import sys
import tempfile
import time
import unittest
from safe_process import execute


class ProcessBoundaryTest(unittest.TestCase):
    def test_failure_output_is_not_forwarded(self):
        out, err = io.StringIO(), io.StringIO()
        with contextlib.redirect_stdout(out), contextlib.redirect_stderr(err):
            r = execute([sys.executable, '-c', "import sys; print('SYNTHETIC_SECRET'); sys.exit(7)"])
        self.assertEqual(r['returncode'], 7)
        self.assertIn(b'SYNTHETIC_SECRET', r['data'])
        self.assertEqual(out.getvalue() + err.getvalue(), '')

    def test_timeout_kills_descendant(self):
        with tempfile.TemporaryDirectory() as d:
            marker = str(Path(d) / 'escaped')
            child = "import time; from pathlib import Path; time.sleep(1); Path(" + repr(marker) + ").touch()"
            parent = "import subprocess,sys,time; subprocess.Popen([sys.executable,'-c'," + repr(child) + "]); time.sleep(30)"
            r = execute([sys.executable, '-c', parent], seconds=0.15)
            self.assertTrue(r['timed_out'])
            time.sleep(1.1)
            self.assertFalse(Path(marker).exists())

    def test_normal_exit_also_reaps_descendant(self):
        with tempfile.TemporaryDirectory() as d:
            marker = str(Path(d) / 'escaped')
            child = "import time; from pathlib import Path; time.sleep(1); Path(" + repr(marker) + ").touch()"
            parent = "import subprocess,sys; subprocess.Popen([sys.executable,'-c'," + repr(child) + "])"
            r = execute([sys.executable, '-c', parent])
            self.assertEqual(r['returncode'], 0)
            time.sleep(1.1)
            self.assertFalse(Path(marker).exists())

    def test_large_output_cannot_be_accepted(self):
        r = execute([sys.executable, '-c', "print('x'*(4*1024*1024+1))"])
        self.assertTrue(r['output_limited'])


if __name__ == '__main__':
    unittest.main()
