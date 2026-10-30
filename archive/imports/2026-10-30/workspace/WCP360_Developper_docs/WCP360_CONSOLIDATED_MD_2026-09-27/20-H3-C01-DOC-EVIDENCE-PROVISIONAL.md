# WCP360 — H3-C01-DOC-EVIDENCE — PROVISIONAL

## Authority

`PROVISIONAL — MASTER EVIDENCE EXTRACTION`

This is not Documentation-owner approval.

## Major evidence-supported findings

### Typed DNS chain
Evidence supports:
- `domains.write` → permission;
- `domains.record.update` → action;
- `dns.change.apply` → job;
- `dns.record.updated` → audit-event type.

The source Permission Registry also lists the latter three as permissions, demonstrating real namespace contamination.

### Process chain
Evidence overlaps:
- `processes.kill`;
- `processes.terminate`;
- `process.kill`;
- `process.term`;

across permission/action/job/daemon-operation semantics. Typed normalization is still required.

### Privileged architecture drift
Older source material permits an elevated daemon/single-socket model. Current MASTER architecture requires:

`backend → wcpd(non-root) → wcp-privd(root minimal)`

Old wording is therefore evidence/history pending Documentation confirmation/update.

### Filesystem drift
Older source uses `/usr/local/wcp360`; later specialized work proposes `/usr/lib/wcp360` for package-managed product assets.

### Unix identity drift
Older source identities (`wcp360-web`, `wcp360-daemon`) do not represent the full current logical split.

## Documentation-related blockers
- `H3V-B01 = OPEN`
- `H3V-B02 = CONFLICT`
- `H3V-B10 = OPEN`

Any final documentation-owned conclusion remains:

`PENDING DOCUMENTATION OWNER CONFIRMATION`
