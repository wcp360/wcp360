#!/usr/bin/env python3
"""Check the exact historical branding ZIP; never extract or approve a logo.

Missing input stays NOT_PUBLISHED even when --optional lets maintenance CI pass.
Python standard library only. Source files are read, never rewritten.
"""
from __future__ import annotations

import argparse
import hashlib
import io
import json
from pathlib import Path
import stat
import sys
import tempfile
import zipfile

ARCHIVE_NAME = 'WCP360-unpublished-source-assets-2026-10-02.zip'
ARCHIVE_BYTES = 6596441
ARCHIVE_SHA256 = '3580a3f79aa194b77abb3c9b1ed026cf99f226ac7b179a6da78d96d2dbda53d0'
PNG_SIGNATURE = b'\x89PNG\r\n\x1a\n'
# Exact original names, lengths and SHA-256, including both archive sidecars.
EXPECTED = {
    'branding-candidates/Logo WCP360 : Nuage et serveurs.png': (1053011, 'd35edd827425628b5f81fe6ade6aeff59362ac5c3c9f027f9990a8ed843bc34c'),
    'branding-candidates/Logo WCP360 : nuage et infrastructure numérique.png': (829345, '2dabf82f720825b772a1f037035bab09e1da83a4c71f9b07bc6d669ce6934cef'),
    'branding-candidates/Logo futuriste WCP360 bleuté.png': (918486, '994b1239f8b79b3d4d8f55a4290ad10ebd5b0c9cac18095e2984bca50a3debac'),
    'branding-candidates/Logo moderne WCP360 avec panneau serveur.png': (809162, 'ef33a05e9e5232f9b0a6b04223c7ca87128e34d41cf38d71452f61c5dc15e649'),
    'branding-candidates/Logo technologique WCP360 en bleu brillant.png': (970278, '55f7a20d5aafdef5fee63a869a81b6345dafc1c4ead03e62e16c7a0e73cfda35'),
    'branding-candidates/Logo technologique WCP360 en orbite.png': (1117942, '06d43adbb65f7128ad6fdc658aa1be7035957154c590da50354988031833a757'),
    'branding-candidates/Présentation de marque WCP360 en bleu.png': (991716, 'a1d6e798b269cfc21744ba36da0637b833630d2e8f43fd7b993d685529789e0f'),
    'MANIFEST.json': (2706, '46b9ef53aa271b43f3609ac51319fdbb51e5860c1727634f3dd82b1d08be5ce6'),
    'README.md': (367, 'c895c80e428359b6218d466997b98d32bfc32ca908d6e604d2e3ed18da4a088c'),
}


def validate_members(data: bytes, expected: dict) -> list:
    """Bounded read of allowlisted entries. A test fixture may supply its own set."""
    with zipfile.ZipFile(io.BytesIO(data)) as archive:
        entries = archive.infolist()
        names = [entry.filename for entry in entries]
        if len(names) != len(set(names)) or set(names) != set(expected):
            raise ValueError('Archive members differ from the exact allowlist')
        rows = []
        for entry in entries:
            size, digest = expected[entry.filename]
            mode = stat.S_IFMT(entry.external_attr >> 16)
            if entry.is_dir() or mode not in (0, stat.S_IFREG) or entry.flag_bits & 1:
                raise ValueError('Non-regular or encrypted member')
            if entry.file_size != size or entry.compress_type not in (zipfile.ZIP_STORED, zipfile.ZIP_DEFLATED):
                raise ValueError('Unexpected member size or compression')
            # No extraction, source filename execution, or writes to the worktree.
            with archive.open(entry) as handle:
                body = handle.read(size + 1)
            if len(body) != size or hashlib.sha256(body).hexdigest() != digest:
                raise ValueError('Member content identity mismatch')
            if entry.filename.endswith('.png') and not body.startswith(PNG_SIGNATURE):
                raise ValueError('PNG signature missing')
            blob = hashlib.sha1(b'blob ' + str(size).encode() + b'\0' + body).hexdigest()
            rows.append({'archive_member': entry.filename, 'bytes': size,
                         'sha256': digest, 'git_blob_sha1': blob})
    return sorted(rows, key=lambda row: row['archive_member'])


def verify(path: Path, optional: bool = False) -> dict:
    common = {'format': 'WCP360-BRANDING-ARCHIVE-CHECK/1',
              'archive': path.name, 'authority': 'HISTORICAL_CANDIDATES_NOT_APPROVED',
              'overall_project_publication': 'NOT_CERTIFIED_COMPLETE',
              'application_tests_executed': 0, 'extracted_files': 0,
              'github_publication_verified_by_this_local_check': False}
    if path.is_symlink():
        raise ValueError('Symlink input is forbidden')
    if not path.exists():
        if not optional:
            raise FileNotFoundError('Original archive is missing')
        return dict(common, status='NOT_PUBLISHED', archive_present=False,
                    original_bytes_verified=False, verified_png_count=0)
    if not path.is_file() or path.stat().st_size != ARCHIVE_BYTES:
        raise ValueError('Input must be the exact regular source ZIP')
    # Bound the read even if the file changes between stat and open.
    with path.open('rb') as handle:
        data = handle.read(ARCHIVE_BYTES + 1)
    if len(data) != ARCHIVE_BYTES or hashlib.sha256(data).hexdigest() != ARCHIVE_SHA256:
        raise ValueError('Original archive SHA-256 mismatch')
    rows = validate_members(data, EXPECTED)
    png_count = sum(row['archive_member'].endswith('.png') for row in rows)
    if png_count != 7:
        raise ValueError('Expected seven original PNG files')
    return dict(common, status='ORIGINAL_BYTES_VERIFIED', archive_present=True,
                original_bytes_verified=True, verified_png_count=png_count,
                archive_bytes=len(data), archive_sha256=ARCHIVE_SHA256,
                verified_member_count=len(rows), members=rows)


def self_test() -> dict:
    """Small adversarial archive fixtures, independent of original availability."""
    def digest(data: bytes) -> str:
        return hashlib.sha256(data).hexdigest()

    def fixture(items: list) -> bytes:
        buffer = io.BytesIO()
        with zipfile.ZipFile(buffer, 'w', zipfile.ZIP_DEFLATED) as archive:
            for name, content in items:
                archive.writestr(name, content)
        return buffer.getvalue()

    cases = []
    payload = PNG_SIGNATURE + b'synthetic fixture, not an image proposal'
    expected = {'candidate.png': (len(payload), digest(payload))}
    good = fixture([('candidate.png', payload)])
    if len(validate_members(good, expected)) != 1:
        raise AssertionError('Valid fixture rejected')
    cases.append('valid_allowlisted_fixture')

    def rejects(name: str, function) -> None:
        try:
            function()
        except (ValueError, FileNotFoundError, zipfile.BadZipFile):
            cases.append(name)
        else:
            raise AssertionError('Expected rejection: ' + name)

    rejects('changed_member', lambda: validate_members(fixture([('candidate.png', payload[:-1] + b'X')]), expected))
    rejects('missing_member', lambda: validate_members(fixture([]), expected))
    rejects('extra_member', lambda: validate_members(fixture([('candidate.png', payload), ('extra', b'x')]), expected))
    rejects('traversal_member', lambda: validate_members(fixture([('../candidate.png', payload)]), expected))
    link = zipfile.ZipInfo('candidate.png')
    link.create_system = 3
    link.external_attr = (stat.S_IFLNK | 0o777) << 16
    rejects('symlink_member', lambda: validate_members(fixture([(link, payload)]), expected))
    with tempfile.TemporaryDirectory(prefix='wcp-branding-check-') as folder:
        missing = Path(folder) / 'absent.zip'
        report = verify(missing, optional=True)
        if report['status'] != 'NOT_PUBLISHED' or report['original_bytes_verified']:
            raise AssertionError('Missing input must not become verified')
        cases.append('optional_missing_stays_unpublished')
        rejects('required_missing', lambda: verify(missing))
        wrong = Path(folder) / 'wrong.zip'
        wrong.write_bytes(good)
        rejects('wrong_original_archive', lambda: verify(wrong))
    if len(cases) != 9:
        raise AssertionError('Unexpected self-test count')
    return {'scope': 'synthetic archive validation only', 'passed': len(cases),
            'cases': cases, 'application_tests_executed': 0}


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument('--archive', type=Path,
                        default=Path(__file__).resolve().parents[2] / 'documentation/branding/candidates' / ARCHIVE_NAME)
    parser.add_argument('--optional', action='store_true')
    parser.add_argument('--self-test', action='store_true')
    parser.add_argument('--output', type=Path)
    args = parser.parse_args()
    code = 0
    try:
        report = self_test() if args.self_test else verify(args.archive, args.optional)
    except (OSError, ValueError, zipfile.BadZipFile, RuntimeError, AssertionError) as exc:
        report = {'status': 'ERROR', 'reason': str(exc), 'original_bytes_verified': False}
        code = 1
    rendered = json.dumps(report, indent=2, ensure_ascii=False) + '\n'
    if args.output:
        args.output.parent.mkdir(parents=True, exist_ok=True)
        # Existing evidence is never overwritten by this command.
        with args.output.open('x', encoding='utf-8') as handle:
            handle.write(rendered)
    print(rendered, end='')
    return code


if __name__ == '__main__':
    sys.exit(main())
