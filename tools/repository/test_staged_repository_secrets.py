#!/usr/bin/env python3
"""Staged-index and real pre-commit tests; isolated synthetic repositories only."""
from __future__ import annotations

from contextlib import redirect_stdout
import hashlib
import importlib.util
import io
import json
import os
from pathlib import Path
import shutil
import subprocess
import tempfile
import unittest
from unittest.mock import patch

HERE = Path(__file__).resolve().parent
SPEC = importlib.util.spec_from_file_location('staged_secret_checker', HERE / 'check_repository_secrets.py')
if SPEC is None or SPEC.loader is None:
    raise RuntimeError('Cannot load checker')
checker = importlib.util.module_from_spec(SPEC)
SPEC.loader.exec_module(checker)
HOOK = HERE.parents[1] / '.githooks' / 'pre-commit'


def token() -> bytes:
    return b'ghp_' + b'Ab12Cd34Ef56' * 3


class Fixture(unittest.TestCase):
    def setUp(self):
        self.tmp = tempfile.TemporaryDirectory(prefix='wcp-staged-tests-')
        self.addCleanup(self.tmp.cleanup)
        self.repo = Path(self.tmp.name)
        self.env = {'PATH': os.environ.get('PATH', ''), 'HOME': str(self.repo),
                    'GIT_CONFIG_NOSYSTEM': '1', 'GIT_CONFIG_GLOBAL': os.devnull,
                    'GIT_CONFIG_SYSTEM': os.devnull, 'GIT_TERMINAL_PROMPT': '0',
                    'LC_ALL': 'C'}
        self.git('init', '-q')
        self.git('config', 'user.email', 'fixture@example.invalid')
        self.git('config', 'user.name', 'Synthetic fixture')
        self.write('README.md', b'safe\n')
        self.git('add', '-A')
        self.git('commit', '-qm', 'fixture')

    def git(self, *args, extra_env=None, hooks=False, check=True, input_data=None):
        cmd = ['git', '-C', str(self.repo)]
        if not hooks:
            cmd += ['-c', 'core.hooksPath=' + os.devnull]
        return subprocess.run(cmd + list(args), capture_output=True, check=check,
                              input=input_data, env=dict(self.env, **(extra_env or {})), timeout=15)

    def write(self, name, content):
        path = self.repo / name
        path.parent.mkdir(parents=True, exist_ok=True)
        path.write_bytes(content)
        return path

    def cli(self, *args, repo=None):
        stream = io.StringIO()
        with redirect_stdout(stream):
            code = checker.main(['--repo', str(repo or self.repo), *args])
        report = json.loads(stream.getvalue())
        self.assertNotIn(token().decode(), stream.getvalue())
        return code, report

    def install_fixture_hook(self):
        # Configuration is changed only inside this disposable test repository.
        target = self.repo / '.githooks' / 'pre-commit'
        target.parent.mkdir()
        shutil.copyfile(HOOK, target)
        target.chmod(0o755)
        out = self.repo / 'tools/repository/check_repository_secrets.py'
        out.parent.mkdir(parents=True)
        shutil.copyfile(HERE / 'check_repository_secrets.py', out)
        self.git('add', '-A')
        self.git('commit', '-qm', 'fixture hook sources')
        self.git('config', 'core.hooksPath', '.githooks')


class StagedTests(Fixture):
    def test_staged_secret_is_detected_before_commit(self):
        self.write('new.txt', token()); self.git('add', 'new.txt')
        self.assertEqual(self.cli()[0], 0)
        code, report = self.cli('--staged')
        self.assertEqual(code, 1)
        self.assertEqual(report['snapshot_kind'], 'index')
        self.assertIsNone(report['commit'])

    def test_clean_worktree_does_not_hide_staged_secret(self):
        self.write('new.txt', token()); self.git('add', 'new.txt')
        self.write('new.txt', b'cleaned but not staged\n')
        self.assertEqual(self.cli('--staged')[0], 1)

    def test_worktree_secret_not_misrepresented_as_staged(self):
        self.write('README.md', token())
        code, report = self.cli('--staged')
        self.assertEqual(code, 0)
        self.assertEqual(report['bytes_scanned'], len(b'safe\n'))

    def test_untracked_secret_is_outside_index(self):
        self.write('untracked.txt', token())
        self.assertEqual(self.cli('--staged')[1]['files_scanned'], 1)

    def test_first_commit_index(self):
        fresh = self.repo / 'fresh'; fresh.mkdir()
        subprocess.run(['git', 'init', '-q', str(fresh)], check=True, capture_output=True, env=self.env)
        (fresh / 'first.txt').write_bytes(token())
        subprocess.run(['git', '-C', str(fresh), 'add', '.'], check=True, capture_output=True, env=self.env)
        self.assertEqual(self.cli('--staged', repo=fresh)[0], 1)

    def test_empty_initial_index(self):
        fresh = self.repo / 'empty'; fresh.mkdir()
        subprocess.run(['git', 'init', '-q', str(fresh)], check=True, capture_output=True, env=self.env)
        code, report = self.cli('--staged', repo=fresh)
        self.assertEqual((code, report['files_scanned']), (0, 0))

    def test_staged_deletion_does_not_claim_history_clean(self):
        self.write('old.txt', token()); self.git('add', '.'); self.git('commit', '-qm', 'synthetic')
        self.git('rm', '-q', 'old.txt')
        self.assertEqual(self.cli()[0], 1)
        code, report = self.cli('--staged')
        self.assertEqual(code, 0)
        self.assertFalse(report['history_scanned'])

    def test_forced_ignored_env(self):
        self.write('.gitignore', b'.env\n'); self.write('.env', b'EXAMPLE=not-a-secret\n')
        self.git('add', '-f', '.env')
        self.assertEqual(self.cli('--staged')[0], 1)

    def test_staged_subdirectory_scan_covers_root(self):
        self.write('root.txt', token()); self.write('sub/safe.md', b'safe')
        self.git('add', '.')
        code, report = self.cli('--staged', repo=self.repo / 'sub')
        self.assertEqual((code, report['files_scanned']), (1, 3))

    def test_committed_subdirectory_scan_regression(self):
        self.write('root.txt', token()); self.write('sub/safe.md', b'safe')
        self.git('add', '.'); self.git('commit', '-qm', 'synthetic')
        root = self.cli()[1]; code, sub = self.cli(repo=self.repo / 'sub')
        self.assertEqual(code, 1)
        self.assertEqual(sub, root)

    def test_staged_binary_scanned(self):
        self.write('example.bin', b'\x00\xff\n' + token()); self.git('add', '.')
        code, report = self.cli('--staged')
        self.assertEqual(code, 1)
        self.assertEqual(report['binary_files_scanned_as_raw_bytes'], 1)

    def test_staged_non_utf8_filename(self):
        raw = os.fsencode(self.repo) + b'/odd-\xff\xfe'
        with open(raw, 'wb') as handle: handle.write(token())
        self.git('add', '-A')
        code, report = self.cli('--staged')
        self.assertEqual(code, 1)
        self.assertNotIn('odd-', json.dumps(report))

    def test_conflicts_are_rejected(self):
        oid = self.git('rev-parse', 'HEAD:README.md').stdout.strip()
        self.git('update-index', '--index-info', input_data=b'100644 ' + oid + b' 1\tconflict.txt\n'
                 + b'100644 ' + oid + b' 2\tconflict.txt\n')
        self.assertEqual(self.cli('--staged')[1]['error_code'], 'UNMERGED_INDEX')

    def test_staged_symlink_is_rejected(self):
        (self.repo / 'link').symlink_to('/never-read-this-target'); self.git('add', 'link')
        self.assertEqual(self.cli('--staged')[1]['error_code'], 'UNSUPPORTED_TREE_ENTRY')

    def test_staged_gitlink_is_rejected(self):
        oid = self.git('rev-parse', 'HEAD').stdout.decode().strip()
        self.git('update-index', '--add', '--cacheinfo', '160000,' + oid + ',module')
        self.assertEqual(self.cli('--staged')[1]['error_code'], 'UNSUPPORTED_TREE_ENTRY')

    def test_missing_staged_blob_is_error(self):
        self.git('update-index', '--add', '--cacheinfo', '100644,' + 'f' * 40 + ',missing.txt')
        self.assertEqual(self.cli('--staged')[0], 2)

    def test_explicit_alternate_index_is_used(self):
        alt = self.repo / '.git/alternate-index'
        shutil.copyfile(self.repo / '.git/index', alt)
        self.write('only-alternate.txt', token())
        self.git('add', 'only-alternate.txt', extra_env={'GIT_INDEX_FILE': str(alt)})
        self.assertEqual(self.cli('--staged')[0], 0)
        self.assertEqual(self.cli('--staged', '--index-file', str(alt))[0], 1)

    def test_inherited_alternate_index_is_not_silently_ignored(self):
        with patch.dict(os.environ, {'GIT_INDEX_FILE': str(self.repo / '.git/index')}):
            code, report = self.cli('--staged')
        self.assertEqual((code, report['error_code']), (2, 'ALTERNATE_INDEX_NOT_SELECTED'))

    def test_missing_explicit_index_is_rejected(self):
        self.assertEqual(self.cli('--staged', '--index-file', str(self.repo / 'absent'))[1]['error_code'],
                         'INVALID_INDEX_FILE')

    def test_symlink_explicit_index_is_rejected(self):
        link = self.repo / 'index-link'; link.symlink_to(self.repo / '.git/index')
        self.assertEqual(self.cli('--staged', '--index-file', str(link))[0], 2)

    def test_corrupt_explicit_index_error_does_not_leak(self):
        bad = self.write('bad-index', token())
        code, report = self.cli('--staged', '--index-file', str(bad))
        self.assertEqual((code, report['error_code']), (2, 'GIT_READ_FAILED'))

    def test_staged_and_ref_are_exclusive(self):
        self.assertEqual(self.cli('--staged', '--ref', 'HEAD')[1]['error_code'], 'INVALID_ARGUMENTS')

    def test_ref_then_staged_are_exclusive(self):
        self.assertEqual(self.cli('--ref', 'HEAD', '--staged')[1]['error_code'], 'INVALID_ARGUMENTS')

    def test_empty_explicit_ref_is_not_defaulted(self):
        self.assertEqual(self.cli('--ref', '')[1]['error_code'], 'INVALID_REF')

    def test_index_selection_requires_staged(self):
        self.assertEqual(self.cli('--index-file', str(self.repo / '.git/index'))[1]['error_code'],
                         'INDEX_REQUIRES_STAGED')

    def test_index_and_worktree_are_not_modified(self):
        self.write('new.txt', b'staged'); self.git('add', '.')
        self.write('new.txt', b'local unstaged change')
        before = (self.repo / '.git/index').read_bytes()
        config = (self.repo / '.git/config').read_bytes()
        self.assertEqual(self.cli('--staged')[0], 0)
        self.assertEqual(before, (self.repo / '.git/index').read_bytes())
        self.assertEqual(config, (self.repo / '.git/config').read_bytes())
        self.assertEqual((self.repo / 'new.txt').read_bytes(), b'local unstaged change')

    def test_index_snapshot_digest_is_repeatable(self):
        first = self.cli('--staged')[1]; second = self.cli('--staged')[1]
        self.assertEqual(first, second)
        self.assertRegex(first['index_snapshot_sha256'], '^[0-9a-f]{64}$')
        self.assertTrue(first['index_stable_at_recheck'])

    def test_index_mutation_during_scan_is_rejected(self):
        real_run = subprocess.run
        calls = 0
        def changed(cmd, **kwargs):
            nonlocal calls
            result = real_run(cmd, **kwargs)
            if 'ls-files' in cmd:
                calls += 1
                if calls == 2:
                    result.stdout += b'changed-' + token()
            return result
        with patch.object(checker.subprocess, 'run', side_effect=changed):
            code, report = self.cli('--staged')
        self.assertEqual((code, report['error_code']), (2, 'INDEX_CHANGED_DURING_SCAN'))

    def test_index_limit_fails_closed(self):
        with patch.object(checker, 'MAX_BLOB', 1):
            self.assertEqual(self.cli('--staged')[1]['error_code'], 'BLOB_SIZE_LIMIT')

    def test_assume_unchanged_entry_is_still_scanned(self):
        self.write('README.md', token()); self.git('add', 'README.md')
        self.git('update-index', '--assume-unchanged', 'README.md')
        self.assertEqual(self.cli('--staged')[0], 1)

    def test_skip_worktree_entry_is_still_scanned(self):
        self.write('README.md', token()); self.git('add', 'README.md')
        self.git('update-index', '--skip-worktree', 'README.md')
        self.assertEqual(self.cli('--staged')[0], 1)


class HookTests(Fixture):
    def test_hook_rejects_commit_without_creating_it(self):
        self.install_fixture_hook()
        before = self.git('rev-parse', 'HEAD').stdout
        self.write('new.txt', token()); self.git('add', 'new.txt')
        result = self.git('commit', '-qm', 'must fail', hooks=True, check=False)
        self.assertNotEqual(result.returncode, 0)
        self.assertNotIn(token(), result.stdout + result.stderr)
        self.assertEqual(before, self.git('rev-parse', 'HEAD').stdout)

    def test_hook_accepts_clean_commit(self):
        self.install_fixture_hook()
        self.write('clean.txt', b'safe\n'); self.git('add', 'clean.txt')
        self.assertEqual(self.git('commit', '-qm', 'clean', hooks=True, check=False).returncode, 0)

    def test_partial_commit_uses_git_prepared_index(self):
        self.install_fixture_hook()
        self.write('README.md', b'clean change')
        self.write('not-selected.txt', token()); self.git('add', 'not-selected.txt')
        result = self.git('commit', '-qm', 'partial', '--only', '--', 'README.md', hooks=True, check=False)
        self.assertEqual(result.returncode, 0, result.stderr.decode(errors='replace'))
        self.assertEqual(self.cli()[0], 0)
        self.assertEqual(self.cli('--staged')[0], 1)

    def test_partial_commit_catches_selected_worktree_secret(self):
        self.install_fixture_hook()
        self.write('README.md', token())
        result = self.git('commit', '-qm', 'partial must fail', '--only', '--', 'README.md', hooks=True, check=False)
        self.assertNotEqual(result.returncode, 0)
        self.assertNotIn(token(), result.stdout + result.stderr)

    def test_commit_all_catches_unstaged_tracked_secret(self):
        self.install_fixture_hook()
        self.write('README.md', token())
        result = self.git('commit', '-qam', 'all must fail', hooks=True, check=False)
        self.assertNotEqual(result.returncode, 0)
        self.assertNotIn(token(), result.stdout + result.stderr)

    def test_missing_checker_causes_hook_failure(self):
        self.install_fixture_hook()
        (self.repo / 'tools/repository/check_repository_secrets.py').unlink()
        result = self.git('commit', '--allow-empty', '-qm', 'must fail', hooks=True, check=False)
        self.assertNotEqual(result.returncode, 0)
        self.assertIn(b'CHECKER_UNAVAILABLE', result.stderr)


if __name__ == '__main__':
    unittest.main(verbosity=2)
