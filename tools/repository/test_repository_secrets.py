#!/usr/bin/env python3
"""Regression tests use only synthetic strings and disposable local Git repos."""
from __future__ import annotations

from contextlib import redirect_stdout
import importlib.util
import io
import json
import os
from pathlib import Path
import subprocess
import tempfile
import unittest
from unittest.mock import patch

MODULE_PATH = Path(__file__).resolve().with_name('check_repository_secrets.py')
SPEC = importlib.util.spec_from_file_location('repo_secret_checker', MODULE_PATH)
if SPEC is None or SPEC.loader is None:
    raise RuntimeError('Cannot load checker')
checker = importlib.util.module_from_spec(SPEC)
SPEC.loader.exec_module(checker)


def fake_token(prefix: bytes = b'ghp_') -> bytes:
    return prefix + b'A1b2C3d4E5f6' * 3


def fake_key(kind: bytes = b'') -> bytes:
    return b'-----BEGIN ' + kind + b'PRIVATE' + b' KEY-----\nsynthetic-only\n'


class RuleTests(unittest.TestCase):
    def test_clean_text(self):
        self.assertEqual(checker.blob_findings(b'README.md', b'No credentials here', 1), [])

    def test_private_key_variants(self):
        for kind in (b'', b'RSA ', b'EC ', b'DSA ', b'OPENSSH ', b'ENCRYPTED '):
            with self.subTest(kind=kind):
                result = checker.blob_findings(b'fixture.txt', b'line1\n' + fake_key(kind), 1)
                self.assertEqual(result[0]['rule'], 'REPO-SECRET-PRIVATE-KEY')
                self.assertEqual(result[0]['line'], 2)

    def test_public_key_not_classed_private(self):
        public = b'-----BEGIN ' + b'PUBLIC KEY-----\n'
        self.assertFalse(checker.blob_findings(b'public.pem', public, 1))

    def test_classic_token_prefixes(self):
        for prefix in (b'ghp_', b'gho_', b'ghu_', b'ghs_', b'ghr_'):
            with self.subTest(prefix=prefix):
                self.assertEqual(checker.blob_findings(b'doc.md', fake_token(prefix), 1)[0]['rule'],
                                 'REPO-SECRET-GITHUB-TOKEN')

    def test_fine_grained_token(self):
        token = b'github_pat_' + b'abc123_' * 12
        self.assertTrue(checker.blob_findings(b'doc.md', token, 1))

    def test_installation_jwt_shape(self):
        token = b'ghs_' + b'12345_' + b'eyJA' * 10 + b'.' + b'eyJB' * 50 + b'.' + b'aB_-' * 12
        self.assertEqual(checker.blob_findings(b'doc.md', token, 1)[0]['rule'],
                         'REPO-SECRET-GITHUB-APP-JWT')

    def test_short_placeholder(self):
        self.assertFalse(checker.blob_findings(b'doc.md', b'ghp_EXAMPLE github_pat_PLACEHOLDER', 1))

    def test_binary_content_not_skipped(self):
        self.assertTrue(checker.blob_findings(b'image.png', b'\xff\x00\n' + fake_token(), 1))

    def test_report_never_includes_path_or_secret(self):
        token = fake_token()
        report = checker.blob_findings(b'sensitive-' + token + b'.txt', token, 3)
        serialized = json.dumps(report).encode()
        self.assertNotIn(token, serialized)
        self.assertNotIn(b'sensitive-', serialized)
        self.assertEqual(report[0]['path_index'], 3)

    def test_sensitive_paths(self):
        for path in (b'.env', b'app/.env.production', b'BACKUPS/db.bin', b'x/.ssh/id_rsa',
                     b'client.p12', b'old.sql.gz', b'config/KEY.KEY', b'project/.aws/credentials'):
            with self.subTest(path=path):
                self.assertTrue(checker.path_is_sensitive(path))

    def test_source_paths_allowed(self):
        for path in (b'.env.example', b'app/.env.example', b'database/migrations/001.sql',
                     b'tests/fixtures/example.sql', b'documentation/secrets.md'):
            with self.subTest(path=path):
                self.assertFalse(checker.path_is_sensitive(path))

    def test_non_utf8_filename_is_not_decoded(self):
        result = checker.blob_findings(b'bad-\xff\xfe.txt', fake_token(), 1)
        self.assertTrue(result)
        self.assertNotIn('bad-', json.dumps(result))

    def test_example_env_still_scanned(self):
        self.assertTrue(checker.blob_findings(b'.env.example', fake_token(), 1))


class GitTests(unittest.TestCase):
    def setUp(self):
        self.tmp = tempfile.TemporaryDirectory(prefix='wcp-secret-tests-')
        self.addCleanup(self.tmp.cleanup)
        self.repo = Path(self.tmp.name)
        self.env = {'PATH': os.environ.get('PATH', ''), 'HOME': str(self.repo),
                    'GIT_CONFIG_NOSYSTEM': '1', 'GIT_CONFIG_GLOBAL': os.devnull,
                    'GIT_CONFIG_SYSTEM': os.devnull, 'GIT_TERMINAL_PROMPT': '0'}
        self.git('init', '-q')
        self.git('config', 'user.email', 'fixture@example.invalid')
        self.git('config', 'user.name', 'Synthetic fixture')
        self.write('README.md', b'fixture\n')
        self.commit()

    def git(self, *args):
        return subprocess.run(['git', '-C', str(self.repo), '-c', 'core.hooksPath=' + os.devnull, *args],
                              capture_output=True, check=True, timeout=10, env=self.env).stdout

    def write(self, path, data):
        target = self.repo / path
        target.parent.mkdir(parents=True, exist_ok=True)
        target.write_bytes(data)

    def commit(self):
        self.git('add', '-A')
        self.git('commit', '-qm', 'fixture')
        return self.git('rev-parse', 'HEAD').decode().strip()

    def cli(self, *args):
        output = io.StringIO()
        with redirect_stdout(output):
            code = checker.main(['--repo', str(self.repo), *args])
        return code, json.loads(output.getvalue())

    def test_clean_commit(self):
        code, report = self.cli()
        self.assertEqual(code, 0)
        self.assertEqual(report['files_scanned'], 1)
        self.assertFalse(report['complete_secret_audit'])

    def test_committed_secret_fails(self):
        self.write('config.txt', fake_token())
        self.commit()
        code, report = self.cli()
        self.assertEqual(code, 1)
        self.assertEqual(report['finding_count'], 1)

    def test_dirty_and_untracked_not_misrepresented(self):
        self.write('README.md', fake_token())
        self.write('untracked.txt', fake_key())
        code, report = self.cli()
        self.assertEqual(code, 0)
        self.assertEqual(report['files_scanned'], 1)
        self.assertEqual((self.repo / 'README.md').read_bytes(), fake_token())

    def test_exact_commit_is_used(self):
        before = self.git('rev-parse', 'HEAD').decode().strip()
        self.write('later.txt', fake_token())
        self.commit()
        code, report = self.cli('--ref', before)
        self.assertEqual(code, 0)
        self.assertEqual(report['commit'], before)
        self.assertEqual(self.cli()[0], 1)

    def test_forced_tracked_env_detected(self):
        self.write('.gitignore', b'.env\n')
        self.write('.env', b'NON_SECRET=example\n')
        self.git('add', '-f', '.env')
        self.commit()
        self.assertEqual(self.cli()[0], 1)

    def test_symlink_not_followed(self):
        (self.repo / 'link').symlink_to('/this-target-must-not-be-read')
        self.commit()
        code, report = self.cli()
        self.assertEqual(code, 2)
        self.assertEqual(report['error_code'], 'UNSUPPORTED_TREE_ENTRY')

    def test_submodule_not_silently_skipped(self):
        oid = self.git('rev-parse', 'HEAD').decode().strip()
        self.git('update-index', '--add', '--cacheinfo', '160000,' + oid + ',external')
        self.git('commit', '-qm', 'synthetic gitlink')
        self.assertEqual(self.cli()[1]['error_code'], 'UNSUPPORTED_TREE_ENTRY')

    def test_invalid_ref_sanitized(self):
        value = '--bad-' + fake_token().decode()
        code, report = self.cli('--ref=' + value)
        self.assertEqual(code, 2)
        self.assertNotIn(value, json.dumps(report))
        self.assertEqual(report['error_code'], 'INVALID_REF')

    def test_unknown_argument_not_echoed(self):
        value = '--unknown=' + fake_token().decode()
        code, report = self.cli(value)
        self.assertEqual(code, 2)
        self.assertEqual(report['error_code'], 'INVALID_ARGUMENTS')
        self.assertNotIn(fake_token().decode(), json.dumps(report))

    def test_git_unavailable(self):
        with patch.object(checker.shutil, 'which', return_value=None):
            self.assertEqual(self.cli()[1]['error_code'], 'GIT_UNAVAILABLE')

    def test_time_limit(self):
        with patch.object(checker, 'MAX_SECONDS', 0):
            self.assertEqual(self.cli()[1]['error_code'], 'TIME_LIMIT')

    def test_finding_limit(self):
        self.write('token.txt', fake_token())
        self.commit()
        with patch.object(checker, 'MAX_FINDINGS', 0):
            self.assertEqual(self.cli()[1]['error_code'], 'FINDING_COUNT_LIMIT')

    def test_git_error_output_not_echoed(self):
        result = subprocess.CompletedProcess([], 128, stdout=fake_token(), stderr=fake_key())
        with patch.object(checker.subprocess, 'run', return_value=result):
            code, report = self.cli()
        self.assertEqual(code, 2)
        self.assertEqual(report['error_code'], 'GIT_READ_FAILED')
        self.assertNotIn(fake_token().decode(), json.dumps(report))

    def test_nonexistent_object_fails(self):
        self.assertEqual(self.cli('--ref', 'f' * 40)[0], 2)

    def test_file_count_limit(self):
        with patch.object(checker, 'MAX_FILES', 0):
            self.assertEqual(self.cli()[1]['error_code'], 'FILE_COUNT_LIMIT')

    def test_blob_size_limit(self):
        with patch.object(checker, 'MAX_BLOB', 1):
            self.assertEqual(self.cli()[1]['error_code'], 'BLOB_SIZE_LIMIT')

    def test_total_size_limit(self):
        with patch.object(checker, 'MAX_TOTAL', 1):
            self.assertEqual(self.cli()[1]['error_code'], 'TOTAL_SIZE_LIMIT')

    def test_timeout_does_not_echo_subprocess_output(self):
        exception = subprocess.TimeoutExpired('private-' + fake_token().decode(), 10,
                                             output=fake_token(), stderr=fake_key())
        with patch.object(checker.subprocess, 'run', side_effect=exception):
            code, report = self.cli()
        self.assertEqual(code, 2)
        self.assertEqual(report['error_code'], 'GIT_TIMEOUT')
        self.assertNotIn(fake_token().decode(), json.dumps(report))

    def test_environment_cannot_redirect_repository(self):
        with patch.dict(os.environ, {'GIT_DIR': '/not/the/repository', 'GIT_CONFIG_COUNT': '9'}):
            self.assertEqual(self.cli()[0], 0)

    def test_existing_report_is_not_overwritten(self):
        output = self.repo / 'report.json'
        output.write_text('original', encoding='utf-8')
        self.assertEqual(self.cli('--output', str(output))[0], 2)
        self.assertEqual(output.read_text(), 'original')

    def test_output_symlink_is_not_followed(self):
        target = self.repo / 'keep.txt'
        target.write_text('original', encoding='utf-8')
        link = self.repo / 'report.json'
        link.symlink_to(target)
        self.assertEqual(self.cli('--output', str(link))[0], 2)
        self.assertEqual(target.read_text(), 'original')

    def test_json_report_matches_stdout(self):
        output = self.repo / 'report.json'
        code, report = self.cli('--output', str(output))
        self.assertEqual(code, 0)
        self.assertEqual(json.loads(output.read_text()), report)

    def test_encoded_filename_never_echoed(self):
        name = 'odd\nname\t' + fake_token().decode()
        self.write(name, fake_token())
        self.commit()
        code, report = self.cli()
        self.assertEqual(code, 1)
        self.assertNotIn(fake_token().decode(), json.dumps(report))
        self.assertEqual(report['files_scanned'], 2)

    def test_archive_coverage_reported(self):
        self.write('historical.zip', b'PK\x03\x04\x00synthetic archive marker only')
        self.commit()
        code, report = self.cli()
        self.assertEqual(code, 0)
        self.assertEqual(report['archive_files_not_decompressed'], 1)


if __name__ == '__main__':
    unittest.main(verbosity=2)
