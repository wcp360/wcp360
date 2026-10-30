"""Capture subprocess diagnostics privately; never forward untrusted stdout/stderr.

Used by the CI runner, not by the product. A fresh process group bounds descendants.
"""
import os
import signal
import subprocess
import tempfile


def execute(command, *, cwd=None, seconds=120):
    with tempfile.TemporaryFile() as output, tempfile.TemporaryFile() as errors:
        process = subprocess.Popen(command, cwd=cwd, stdout=output,
                                   stderr=errors, start_new_session=True)
        timed_out = False
        try:
            process.wait(timeout=seconds)
        except subprocess.TimeoutExpired:
            timed_out = True
        finally:
            # Kill surviving children even if the group leader exited normally.
            try:
                os.killpg(process.pid, signal.SIGKILL)
            except ProcessLookupError:
                pass
            process.wait(timeout=10)
        size = output.tell() + errors.tell()
        output.seek(0)
        # Bounded read; reject oversized diagnostics instead of leaking or truncating into PASS.
        data = output.read(4 * 1024 * 1024 + 1)
        return {
            'returncode': process.returncode,
            'timed_out': timed_out,
            'output_limited': size > 4 * 1024 * 1024,
            'data': data,
        }
