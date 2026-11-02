#!/usr/bin/env python3
"""Generate exact, inert registry data from pinned owner-approved sources."""
import argparse
import hashlib
import json
from pathlib import Path
import re

ROOT = Path(__file__).resolve().parents[4]
OUT = ROOT / 'SOURCE/daemon/internal/contracts/registry.json'
SOURCES = {
    'api': ('specifications/api/WCP360_API_Contract_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md', '4893c5191b4928ecfa5a70f4dc14b39a8939fcea'),
    'permission': ('specifications/security/WCP360_Permission_Registry_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md', '81f3d425f36470167aba4bfb7ba069268626342f'),
    'ipc': ('specifications/ipc/WCP360_IPC_Contract_WCP360-IPC-B-1_CANONICAL_IMPLEMENTATION_EXPORT.md', 'fec9c74e8413db822662e47c76c40486e282334f'),
}

# Keep historical paths in generated provenance; resolve only reviewed relocations.
LOCATIONS = {
    'specifications/api/WCP360_API_Contract_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md': 'documentation/specifications/api/WCP360_API_Contract_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md',
    'specifications/security/WCP360_Permission_Registry_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md': 'documentation/specifications/security/WCP360_Permission_Registry_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md',
    'specifications/ipc/WCP360_IPC_Contract_WCP360-IPC-B-1_CANONICAL_IMPLEMENTATION_EXPORT.md': 'documentation/specifications/ipc/WCP360_IPC_Contract_WCP360-IPC-B-1_CANONICAL_IMPLEMENTATION_EXPORT.md',
}

def source(name):
    path, expected = SOURCES[name]
    raw = (ROOT / LOCATIONS[path]).read_bytes()
    actual = hashlib.sha1(b'blob ' + str(len(raw)).encode() + b'\0' + raw).hexdigest()
    if actual != expected:
        raise ValueError(f'{name}: owner source changed; review and update source pin')
    return raw.decode('utf-8')

def rows(text, pattern, width):
    result = []
    for line in text.splitlines():
        if not re.match(pattern, line):
            continue
        cells = [c.strip().strip('`') for c in line.strip('|').split('|')]
        if len(cells) != width:
            raise ValueError('unsupported registry row width')
        result.append(cells)
    return result

def unique(records, count):
    if len(records) != count or len({r['id'] for r in records}) != count:
        raise ValueError(f'expected {count} distinct records')
    return sorted(records, key=lambda r: r['id'])

def generate():
    api = source('api')
    permissions = unique([dict(zip(('id', 'surface', 'status'), r)) for r in rows(source('permission'), r'^\| `[a-z][a-z0-9_.-]+` \|.*\| ACTIVE \|$', 3)], 82)
    api_keys = ('id', 'version', 'producer', 'surface', 'authentication', 'permission', 'action', 'resource', 'scope', 'request_schema', 'response_schema', 'errors', 'timeout', 'retry', 'idempotency', 'audit', 'security', 'compatibility', 'test')
    apis = unique([dict(zip(api_keys, r)) for r in rows(api, r'^\| `API-WCP-[0-9]{4}` \|', 19)], 86)
    ipc_keys = ('id', 'action', 'job_type', 'operation', 'resource_identifier', 'input_schema', 'primitive', 'timeout', 'idempotency', 'concurrency', 'reconciliation', 'test')
    operations = unique([dict(zip(ipc_keys, r)) for r in rows(source('ipc'), r'^\| `IPC-WCP-[0-9]{4}` \|', 12)], 47)
    errors = re.search(r'## 5\. Error Registry\s+```text\n(.*?)\n```', api, re.S).group(1).splitlines()
    if len(errors) != 23 or len(set(errors)) != 23:
        raise ValueError('expected 23 canonical error codes')
    known = {p['id'] for p in permissions}
    if {a['permission'] for a in apis} != known:
        raise ValueError('API/permission coverage mismatch')
    if len({o['operation'] for o in operations}) != 47:
        raise ValueError('duplicate operation')
    result = {'schema_version': 1, 'sources': [{'path': p, 'blob': sha} for p, sha in SOURCES.values()], 'permissions': permissions, 'apis': apis, 'operations': operations, 'errors': sorted(errors)}
    return json.dumps(result, indent=2, ensure_ascii=True) + '\n'

def main():
    parser = argparse.ArgumentParser()
    parser.add_argument('--check', action='store_true')
    args = parser.parse_args()
    content = generate()
    if args.check:
        if OUT.read_text() != content:
            raise SystemExit('generated registry drift; regenerate and review')
    else:
        OUT.write_text(content)

if __name__ == '__main__':
    main()
