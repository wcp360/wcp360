# Inert wcpd foundation

This first SC-M01 executable prints help, version or an inert-state report, then exits. It opens no listener, accesses no database and performs no privileged action.

```sh
./out/sc-m01/daemon/wcpd --help
./out/sc-m01/daemon/wcpd --version
./out/sc-m01/daemon/wcpd --check
```

Exit codes: 0 for a successful local command, 2 for unsupported arguments, 1 for output failure. A successful check is not daemon health/readiness or authorization for future operations. Unsupported input is not echoed.

## Build and verify

Canonical toolchain: Go 1.27.3, linux/amd64, Debian 13.7, standard library only. The exact compiler checksum and image digest are pinned in the workflow and owner addendum. Use GOTOOLCHAIN=local. No go.sum is needed until external runtime modules are approved.

From the repository root in the canonical UID/GID 10001 builder:

```sh
sh SOURCE/daemon/tools/check.sh
sh SOURCE/daemon/tools/scan.sh
```

The first script runs with no network: version/identity/platform, module integrity, formatting, vet, unit/coverage, import-boundary, build, CLI smoke and fresh-cache reproducibility checks. The second uses govulncheck v1.9.0 with HTTPS egress limited to vuln.go.dev; product dependencies remain offline.

The GitHub workflow assembles the pinned builder before introducing repository source. The Ubuntu host only orchestrates; product compilation/tests run inside the Debian container as UID/GID 10001, without a Docker socket or capabilities. Source is a read-only git archive of the exact tested commit; no .git directory enters the container. Thus buildvcs=true finds no VCS metadata to embed: provenance.txt and the archive SHA bind the binary to the actual source commit. Two fresh-cache builds must be byte-identical.

The scanner's TLS CA bundle comes from the host transport environment, is mounted read-only only during scanning and has its SHA recorded. It is not a runtime dependency or linked product input. Scanner DB responses are time-dependent; results and the scanner build information are retained per attempt.

Outputs are under out/sc-m01/daemon/, including gates.tsv, unit.jsonl, coverage, tool/environment records, binary hashes and vulnerability report. CI artifacts expire after 30 days; retain exact run/SHA/digest summaries in the PR for durable review. Every PASS applies only to the executed gate and candidate SHA.

## Authority and path adaptation

Owner addendum: specifications/devops/WCP360_SC-M01_DAEMON_FOUNDATION_TOOLCHAIN_ADDENDUM_v1.0.md, blob f8608d6258f87b8c1099b9b3a7e32ec95c72dc27.

MASTER authorization: 3fd2f8007baa49c2454cc89c0a5488a57c77911b. The human-requested SOURCE/ root relocates daemon/ to SOURCE/daemon/; the Go module identity is unchanged. Relative output paths are adjusted to ../../out/. No toolchain/security policy is changed.

This I1/R1 candidate requires E1 and independent E3 plus applicable DevOps/Security review before merge. No implementation baseline, production deployment, socket/IPC-B, wcp-privd, database migration or web behavior is activated.

## Review follow-up (PR #108)

The scanner now uses a single resolved vuln.go.dev IPv4 address pinned in the container's hosts file. Arbitrary DNS is unavailable, IPv6 is disabled in its namespace, and only that IPv4 destination on TCP 443 is allowed. A reachable external control must fail from inside the scanner container. Firewall counters must prove both allowed and denied packets traversed the enforced chain. These checks fail closed on DNS/CDN changes.

This is an IP/port boundary, not a TLS hostname-aware proxy. Redirects requiring another hostname cannot resolve; redirects to another address cannot connect. Same-address HTTPS behavior remains subject to govulncheck's normal TLS checks. DevOps/Security must explicitly review this residual policy; the probes do not substitute for their approval.

Scanner bootstrap uses fresh caches, exact version v1.9.0, the checksum database with no VCS fallback, and records all linked transitive module versions/h1 sums, module download provenance and scanner binary SHA256. Upstream transitive pseudo-versions are exposed for owner review, not implicitly approved.

The 13-entry gate inventory is checked for exact names, absence of duplicates and successful results. Reproducibility initially means two clean builds within one run; comparing binary hashes across the independent push and PR runners provides additional evidence, not a general reproducible-release guarantee. A durable review record must preserve executed SHA/run IDs, gate results, binary/scanner hashes and transitive checksums before merge; expiring ZIPs alone are insufficient.
