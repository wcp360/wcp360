# PR #108 — correction evidence for Engineering review 5676455437

Date: 2026-10-28. Classification: G1 evidence record / E2 same-account verification, not independent approval.
Candidate: `5bb8bb988ec035e57edccef463d1f020703a9b7c`.
Previous reviewed SHA: `281ccaa8694250fd06410f329dd79240096b82f1`.
Review: https://github.com/yyogas/wcp360/pull/108#pullrequestreview-5676455437

## Multi-agent hardening — latest candidate 71deefa2976a7005983d410907dc2050287be05f

Three agents reviewed CLI correctness, CI/security and authorized requirements; root integrated and verified the changes. All review remains E2 same-account, not independent E3.

- Candidate run https://github.com/yyogas/wcp360/actions/runs/40613003105: SUCCESS.
- Integration run https://github.com/yyogas/wcp360/actions/runs/40613008116: SUCCESS; tested merge cc2581bc21013377d8e3b3242be633a98015ee51.
- 13/13 nominal gates PASS on both runs; exact inventory checked.
- Six disposable offline fixtures rejected at their expected gates: simulated wrong Go version, forbidden dependency, stale go.sum, formatting drift, vet diagnostic, failing unit test. Expected failures are isolated from nominal gates.tsv.
- Host-directed scanner access is now blocked on INPUT; live host listener checks succeed before/after while the scanner connection fails. PR host REJECT counter = 2 packets. External egress denial, blocked DNS and disabled IPv6 remain verified.
- Scanner linked modules must exactly match the committed version/h1 inventory. The previously recorded five-module closure remains unchanged.
- Both runners produce wcpd SHA256 9bbb7a499d77d4bd77e12554e915064815fbfa10b2c020e9763f93621f1f5446 and scanner SHA256 6fb96c19707e3bcda959438f0b336cf2ea8da380250d34d6734c99e27692a71f.
- PR evidence ZIP SHA256 6c13cddbb73b7b4d85f2c70cb2917713a2a3297a551ff7f398b429dafb70702d.
- Push evidence ZIP SHA256 5d03a3c1a635b66071b5cee986cbf17fa3a6f6b5455aa57a16f40b834a661887.

No additional CLI defect was found. No new runtime slice was authorized. The IP/port-vs-hostname policy and telemetry pseudo-version still require DevOps/Security disposition. Engineering reassessment and independent E3 remain required; MERGE HOLD, CODE-BASELINE NONE, SC-M02 NOT AUTHORIZED.

The remaining sections document the previous candidate and retain its exact execution evidence.

## Executed results

- Direct candidate: https://github.com/yyogas/wcp360/actions/runs/40611911884 — SUCCESS, attempt 1.
- Integration: https://github.com/yyogas/wcp360/actions/runs/40611917777 — SUCCESS, attempt 1.
- Tested integration SHA: `d14dae4ac51b95244fc5460e67c08504547f0541`, base `3d378ed46caa924d74dff63ef7071f0270b547b2`.
- Exact 13-gate inventory step succeeded on both runs; it rejects missing, duplicate, extra or non-PASS rows.
- Five top-level CLI tests (including TestShortWriteIsNotSuccess) and four help/version subtests pass. CLI statement coverage 100%; wrapper unit coverage 0%, wrapper exercised by binary smoke.
- Scanner reported no vulnerabilities in either execution. This is not a claim about all 199 product cases.

```text
daemon-toolchain	PASS	0
daemon-no-third-party-runtime-deps	PASS	0
daemon-mod-tidy	PASS	0
daemon-mod-verify	PASS	0
daemon-gofmt	PASS	0
daemon-vet	PASS	0
daemon-unit	PASS	0
daemon-inert-import-boundary	PASS	0
daemon-build	PASS	0
daemon-smoke	PASS	0
daemon-reproducible-build	PASS	0
daemon-evidence	PASS	0
daemon-vulncheck	PASS	0
```

## Correction disposition

| Finding | Implementation / evidence | Remaining decision |
|---|---|---|
| CLI short write | Both normal and diagnostic writes require full byte count; shortWriter regression exercises truncation with nil error, including excess arguments. | Engineering re-review |
| Explicit tidy result | Successful command plus empty mod-tidy.txt required. | Engineering re-review |
| Scanner transitive provenance | Fresh caches; exact v1.9.0; checksum database without VCS fallback; linked h1 sums, download JSON, module verification and scanner digest retained. | DevOps/Engineering review of upstream transitive pseudo-version |
| Network negative proof | Host connects to external control 1.1.1.1:443; same endpoint blocked inside scanner; arbitrary DNS fails; IPv6 disabled in namespace; allowed/rejected firewall counters nonzero. | DevOps/Security acceptance of residual policy |
| Reproducibility/retention | Two fresh-cache builds per run plus identical binary hashes across separate push/PR runners. This file preserves evidence after ZIP expiry. | No general release-reproducibility claim |

Network details: vuln.go.dev was pinned to 34.117.213.18 in /etc/hosts. IPv6 all/default disable flags were 1. The PR runner's destination ACCEPT rule recorded 2 packets; REJECT recorded 6. The push runner likewise recorded 2 destination ACCEPT packets and 6 REJECT packets. Both reached the real vulnerability service successfully.

Residual boundary: this is IPv4 address/port filtering, not a TLS hostname-aware proxy. Other DNS names cannot resolve; other IP destinations are rejected. Same-address HTTPS behavior still depends on govulncheck's normal certificate checks. CDN/DNS drift fails closed; no fallback destination is enabled. These facts do not constitute Security owner approval.

## Durable hashes and immutable scanner provenance

Both runs produced identical SHA256 values:

```text
wcpd = 9bbb7a499d77d4bd77e12554e915064815fbfa10b2c020e9763f93621f1f5446
govulncheck = 6fb96c19707e3bcda959438f0b336cf2ea8da380250d34d6734c99e27692a71f
Go archive = 65efbc80fdc1fa2cd2631cad5b481777a68e8725f34e89f50fc6ac714e379b40
Debian image = sha256:918311b7b6c4c6f68b232ba516584925f6c78ad82b6fd534b98979df6438e483
	mod	golang.org/x/vuln	v1.9.0	h1:12VaqnI9kJfqwRoPptxoqsUhQtdfrSbdksT7X/KY/sc=
	dep	golang.org/x/mod	v0.42.0	h1:ICeL3t2L30ljSPHHxDnR4fqVcBt/zR2zJAb9juavmQg=
	dep	golang.org/x/sync	v0.24.0	h1:iqiDrGoDgyuvAKrhLQthUEzU/R378N78gXbBjxed7oA=
	dep	golang.org/x/telemetry	v0.0.0-20261010193011-f71dc92da45e	h1:P6NGsugec4oASJ+yKMNgTndeo0h2CbixZLvQIe9h5R4=
	dep	golang.org/x/tools	v0.52.0	h1:ryxg4U63oaUYk7uOLTF37n43f4qgtGvqV4Ioit1BaEs=
```

Scanner module verification: all modules verified. The telemetry pseudo-version above is an upstream scanner dependency, not an application runtime dependency and not silently owner-approved.

- Push artifact 12783457798: ZIP SHA256 `275b6c0d25a28d563ab25bb5dde163cf63ad2d31904f93976b0c298432906888`.
- PR artifact 12782718978: ZIP SHA256 `4d9004d3e31b689b90287d22d86b94b1c5ad0303eaaa1c8a4d01a2ffc8976b23`.
- Full ZIP retention is 30 days. This committed record preserves the scope, inputs, execution identities, results and digests beyond that expiry, but does not preserve the ZIP bytes.

## Authority

Technical corrections are submitted, not declared approved. Review 5676455437 remains E2 SAME-ACCOUNT / CHANGES REQUESTED until the reviewer reassesses the corrected SHA. Distinct independent E3 and applicable DevOps/Security approval remain missing. No review thread is auto-resolved.

MERGE = HOLD. CODE-BASELINE = NONE. SC-M02 = NOT AUTHORIZED.
