#!/usr/bin/env python3
"""Inventory Git objects and verify the published mirror manifest.

Read-only Git inspection; no network or application execution. Results certify
repository content against its committed manifest, not unseen source archives,
owner approvals, semantic correctness or secret safety. Requires Python 3.9+.
"""
from __future__ import annotations

import argparse
from collections import Counter
import hashlib
import json
from pathlib import Path, PurePosixPath
import re
import subprocess
import sys

MIRROR = 'documentation/library-mirror/'
MANIFEST = MIRROR + 'MANIFEST.md'
ROW = re.compile(r'^\|\s*(\d+)\s*\|\s*`([^`]+)`\s*\|\s*`([0-9a-f]{40})`\s*\|\s*(\d+)\s*\|$', re.M)
MAX_FILES = 10000
MAX_BYTES = 256 * 1024 * 1024
MAX_BLOB = 32 * 1024 * 1024


def git(*args: str) -> bytes:
    return subprocess.run(['git', '--no-pager', *args], check=True,
                          capture_output=True, timeout=30).stdout


def inspect(ref: str) -> dict:
    if ref != 'HEAD' and not re.fullmatch('[0-9a-f]{40}', ref):
        raise ValueError('Use HEAD or an exact 40-character commit SHA')
    commit = git('rev-parse', '--verify', ref + '^{commit}').decode().strip()
    tree = git('rev-parse', '--verify', commit + '^{tree}').decode().strip()
    entries = git('ls-tree', '-r', '-l', '-z', commit).split(b'\0')
    records = []
    for entry in filter(None, entries):
        meta, path = entry.split(b'\t', 1)
        mode, kind, oid, size = meta.decode('ascii').split()
        records.append({'path': path.decode('utf-8'), 'mode': mode,
                        'type': kind, 'git_blob': oid,
                        'bytes': int(size) if size != '-' else None})
    if len(records) > MAX_FILES:
        raise ValueError('Inventory file-count limit exceeded')
    blobs = [r for r in records if r['type'] == 'blob']
    if sum(r['bytes'] for r in blobs) > MAX_BYTES:
        raise ValueError('Inventory aggregate-byte limit exceeded')
    if any(r['bytes'] > MAX_BLOB for r in blobs):
        raise ValueError('Inventory per-blob size limit exceeded')
    content = {}
    for rec in blobs:
        oid = rec['git_blob']
        if oid not in content:
            content[oid] = git('cat-file', 'blob', oid)
        data = content[oid]
        if len(data) != rec['bytes']:
            raise ValueError('Blob size mismatch')
        actual = hashlib.sha1(b'blob ' + str(len(data)).encode() + b'\0' + data).hexdigest()
        if actual != oid:
            raise ValueError('Blob identity mismatch')
        rec['sha256'] = hashlib.sha256(data).hexdigest()
    by_path = {r['path']: r for r in records}
    if len(by_path) != len(records) or MANIFEST not in by_path:
        raise ValueError('Duplicate path or missing mirror manifest')
    text = content[by_path[MANIFEST]['git_blob']].decode('utf-8')
    rows = ROW.findall(text)
    count = re.search(r'^SOURCE MATERIALIZED MARKDOWN FILES\s*=\s*(\d+)\s*$', text, re.M)
    total = re.search(r'^TOTAL SOURCE BYTES\s*=\s*(\d+)\s*$', text, re.M)
    if not count or not total or not rows or len(rows) != int(count[1]):
        raise ValueError('Manifest row-count declaration is invalid')
    expected = {}
    matches, missing, changed = [], [], []
    for number, rel, oid, size in rows:
        if int(number) != len(expected) + 1:
            raise ValueError('Manifest numbering is not consecutive')
        p = PurePosixPath(rel)
        if p.is_absolute() or '..' in p.parts or str(p) != rel or '\\' in rel:
            raise ValueError('Unsafe manifest path')
        path = MIRROR + rel
        if path in expected:
            raise ValueError('Duplicate mirror manifest path')
        expected[path] = {'git_blob': oid, 'bytes': int(size)}
        actual = by_path.get(path)
        if actual is None:
            missing.append(path)
        elif actual['type'] != 'blob' or actual['mode'] not in ('100644', '100755') or actual['git_blob'] != oid or actual['bytes'] != int(size):
            changed.append({'path': path, 'expected': expected[path], 'actual': actual})
        else:
            matches.append(path)
    if sum(v['bytes'] for v in expected.values()) != int(total[1]):
        raise ValueError('Manifest byte-count declaration is invalid')
    metadata = {MIRROR + 'README.md', MANIFEST}
    extras = sorted(p for p in by_path if p.startswith(MIRROR) and p not in expected and p not in metadata)
    audit_paths = ['governance/audits/AUDIT-WCP-0100/' + p for p in
                   ('README.md', 'INDEX.md', 'COMPLEMENT-2026-10-02.md', 'gitignore-reproduction.json')]
    canonical = {
        'API': 'specifications/api/WCP360_API_Contract_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md',
        'IPC': 'specifications/ipc/WCP360_IPC_Contract_WCP360-IPC-B-1_CANONICAL_IMPLEMENTATION_EXPORT.md',
        'Permission': 'specifications/security/WCP360_Permission_Registry_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md',
        'Security': 'specifications/security/WCP360_Security_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md',
        'DevOps': 'specifications/devops/WCP360_SC-M01_DEVOPS_BASELINE_v1.0.md',
        'Database': 'specifications/database/WCP360_DB_Model_v1.0_Canonical_Implementation_Contract.md',
        'Engineering': 'specifications/engineering/WCP360_ENGINEERING_Code_Review_ENG-WCP-RULESET-0001-v0.2_CANONICAL_IMPLEMENTATION_EXPORT.md',
    }
    statuses = {k: {'path': p, 'present': p in by_path} for k, p in canonical.items()}
    # QA has no final owner-approved exact filename: candidates do not satisfy it.
    statuses['QA'] = {'path': None, 'present': None,
                      'status': 'FINAL_OWNER_FILENAME_NOT_IDENTIFIED',
                      'directory_candidates': sorted(p for p in by_path if p.startswith('specifications/tests/'))}
    prefixes = ['documentation/design-system/V2.5.3.36.4/', 'documentation/snapshots/2026-09-27/']
    groups = {prefix: {'files': sum(r['path'].startswith(prefix) for r in blobs),
                      'bytes': sum(r['bytes'] for r in blobs if r['path'].startswith(prefix))}
              for prefix in prefixes}
    design_prefix = prefixes[0]
    source_prefix = MIRROR + '_GITHUB-STAGING-DESIGN-FULL-V2.5.3.36.4/'
    design_projection = []
    for path, rec in by_path.items():
        if path.startswith(source_prefix):
            target = design_prefix + path[len(source_prefix):]
            other = by_path.get(target)
            design_projection.append({'source': path, 'target': target,
                                      'blob_equal': bool(other and rec['git_blob'] == other['git_blob'])})
    return {'format': 'WCP360-PUBLICATION-INVENTORY/1', 'commit': commit, 'tree': tree,
            'scope': 'Git objects and committed mirror manifest only',
            'overall_project_publication': 'NOT_CERTIFIED_COMPLETE',
            'source_library_byte_equivalence': 'NOT_CHECKED_BY_THIS_SCRIPT',
            'application_tests_executed': 0, 'secret_scan': False,
            'file_count': len(blobs), 'total_bytes': sum(r['bytes'] for r in blobs),
            'extension_counts': dict(sorted(Counter(Path(r['path']).suffix.lower() or '(none)' for r in blobs).items())),
            'top_level_counts': dict(sorted(Counter(r['path'].split('/')[0] for r in blobs).items())),
            'mirror': {'expected': len(expected), 'exact_matches': len(matches),
                       'manifest_bytes': int(total[1]), 'missing': missing, 'changed': changed,
                       'unlisted': extras, 'verified_against_manifest': not (missing or changed or extras)},
            'audit': {'expected': len(audit_paths), 'present': sum(p in by_path for p in audit_paths),
                      'missing': [p for p in audit_paths if p not in by_path]},
            'canonical_presence': statuses, 'document_groups': groups,
            'design_mirror_projection': design_projection,
            'records': sorted(records, key=lambda r: r['path'])}


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument('--ref', default='HEAD')
    parser.add_argument('--output', type=Path, required=True)
    args = parser.parse_args()
    try:
        report = inspect(args.ref)
        args.output.parent.mkdir(parents=True, exist_ok=True)
        if args.output.exists():
            raise ValueError('Refusing to overwrite an existing report')
        args.output.write_text(json.dumps(report, indent=2, ensure_ascii=False) + '\n', encoding='utf-8')
        print(json.dumps({k: report[k] for k in ('commit', 'file_count', 'total_bytes', 'mirror', 'audit')}, ensure_ascii=True))
        return 0 if report['mirror']['verified_against_manifest'] and not report['audit']['missing'] else 1
    except (OSError, ValueError, UnicodeError, subprocess.SubprocessError) as exc:
        print(json.dumps({'status': 'ERROR', 'reason': str(exc)}, ensure_ascii=True), file=sys.stderr)
        return 2


if __name__ == '__main__':
    sys.exit(main())
