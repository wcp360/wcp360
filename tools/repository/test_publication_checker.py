#!/usr/bin/env python3
"""Synthetic publication-checker tests; no WCP360 application is executed."""
from __future__ import annotations

import hashlib
import importlib.util
import json
import os
from pathlib import Path
import subprocess
import tempfile


def main() -> None:
    module_path = Path(__file__).resolve().with_name('check_publication.py')
    spec = importlib.util.spec_from_file_location('publication_checker', module_path)
    if spec is None or spec.loader is None:
        raise RuntimeError('Cannot load publication checker')
    checker = importlib.util.module_from_spec(spec)
    spec.loader.exec_module(checker)
    saved_cwd = Path.cwd()
    saved_git_env = {k: v for k, v in os.environ.items() if k.startswith('GIT_')}
    for key in saved_git_env:
        os.environ.pop(key, None)
    os.environ.update(GIT_CONFIG_NOSYSTEM='1', GIT_CONFIG_GLOBAL=os.devnull,
                      GIT_CONFIG_SYSTEM=os.devnull, GIT_TERMINAL_PROMPT='0')
    results = []
    try:
        with tempfile.TemporaryDirectory(prefix='wcp-publication-tests-') as folder:
            root = Path(folder)
            os.chdir(root)

            def git(*args: str) -> None:
                subprocess.run(['git', '-c', 'core.hooksPath=' + os.devnull, *args],
                               check=True, capture_output=True, timeout=10)

            def commit() -> None:
                git('add', '-A')
                git('commit', '-qm', 'Synthetic fixture only')

            git('init', '-q')
            git('config', 'user.email', 'test@example.invalid')
            git('config', 'user.name', 'Synthetic validation')
            data = b'# Synthetic\n'
            oid = hashlib.sha1(b'blob ' + str(len(data)).encode() + b'\0' + data).hexdigest()
            mirror = root / checker.MIRROR
            mirror.mkdir(parents=True)
            target = mirror / 'sample.md'
            target.write_bytes(data)
            manifest = mirror / 'MANIFEST.md'
            row = f'| 1 | `sample.md` | `{oid}` | {len(data)} |\n'
            manifest.write_text(f'SOURCE MATERIALIZED MARKDOWN FILES = 1\nTOTAL SOURCE BYTES = {len(data)}\n' + row, encoding='utf-8')
            for name in ('README.md', 'INDEX.md', 'COMPLEMENT-2026-10-02.md', 'gitignore-reproduction.json'):
                out = root / 'governance/audits/AUDIT-WCP-0100' / name
                out.parent.mkdir(parents=True, exist_ok=True)
                out.write_text('synthetic test', encoding='utf-8')
            commit()
            report = checker.inspect('HEAD')
            assert report['mirror']['exact_matches'] == 1 and report['file_count'] == 6
            results.append('valid_inventory')
            target.write_text('changed', encoding='utf-8')
            commit()
            assert len(checker.inspect('HEAD')['mirror']['changed']) == 1
            results.append('changed_blob_detected')
            target.unlink()
            commit()
            assert checker.inspect('HEAD')['mirror']['missing'] == [checker.MIRROR + 'sample.md']
            results.append('missing_file_detected')
            (mirror / 'extra.md').write_text('extra', encoding='utf-8')
            commit()
            assert checker.inspect('HEAD')['mirror']['unlisted'] == [checker.MIRROR + 'extra.md']
            results.append('unlisted_file_detected')
            try:
                checker.inspect('--help')
            except ValueError:
                results.append('unsafe_ref_rejected')
            else:
                raise AssertionError('Unsafe ref accepted')
            manifest.write_text(f'SOURCE MATERIALIZED MARKDOWN FILES = 2\nTOTAL SOURCE BYTES = {len(data)}\n' + row, encoding='utf-8')
            commit()
            try:
                checker.inspect('HEAD')
            except ValueError:
                results.append('invalid_manifest_count_rejected')
            else:
                raise AssertionError('Invalid row count accepted')
    finally:
        os.chdir(saved_cwd)
        for key in list(os.environ):
            if key.startswith('GIT_'):
                os.environ.pop(key, None)
        os.environ.update(saved_git_env)
    assert len(results) == 6
    print(json.dumps({'scope': 'synthetic repository checker only',
                      'passed': len(results), 'cases': results,
                      'application_tests_executed': 0}))


if __name__ == '__main__':
    main()
