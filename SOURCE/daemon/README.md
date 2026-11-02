# Inert wcpd foundation

This first SC-M01 executable prints help, version or an inert-state report, then exits. It opens no listener, accesses no database and performs no privileged action.

```sh
./out/sc-m01/daemon/wcpd --help
./out/sc-m01/daemon/wcpd --version
./out/sc-m01/daemon/wcpd --check
./out/sc-m01/daemon/wcpd --contracts
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

## Negative gate evidence and host isolation

CI runs seven deliberate-invalid fixtures in disposable writable copies inside a separate offline, nonroot container. They verify rejection at the exact expected gate: simulated wrong Go version output, forbidden dependency, stale unused go.sum entry, formatting drift, vet printf diagnostic, failing unit assertion and a main wrapper that discards the CLI exit code. Expected fixture failures are recorded as REJECTED_AS_EXPECTED in negative-gates.tsv; they are not product failures and do not replace the normal candidate gate report.

The scanner's host-directed traffic is denied on INPUT as well as external forwarded traffic on DOCKER-USER. An empty-directory host listener bound only to the dedicated bridge provides a live positive control; the scanner must fail to connect while host checks succeed before and after. Nonzero host REJECT counters are required. The temporary listener and rules are removed after the job.

Linked scanner versions and h1 checksums must exactly match tools/govulncheck.modules. That file locks the previously observed upstream closure; owner review of the disclosed telemetry pseudo-version remains required.

## CLI process contract verification

The `daemon-smoke` gate runs `tools/check-cli.sh` against the actual compiled executable. Its 23 process cases check all six successful invocations, nine rejected argument forms and eight output-failure paths. Exact stdout/stderr bytes and process exit codes are checked; synthetic credential/path arguments must never appear in the response. Debian's `/dev/full`, first verified to reject writes, supplies deterministic stdout/stderr failures. Per-case results are retained in `out/sc-m01/daemon/cli-process.tsv` and the CI artifact.

The seventh negative fixture removes `os.Exit` in a disposable main wrapper. Unit tests of `cli.Run` still pass, but `daemon-smoke` must fail on the executable's incorrect exit code. This verifies the process boundary beyond unit coverage of the CLI function. These are inert CLI checks, not API/IPC, session, privileged-operation or full-product QA acceptance. No runtime Go source or canonical contract is changed by this test extension. Prior PR #108 owner assessments remain bound to their original SHA; this follow-up needs its own applicable review and independent E3 before merge.

## Retained candidate integration

This directory is the standalone historical candidate described in [SOURCE/README.md](../README.md). The active application is [WCP360_Core](../../WCP360_Core/README.md); retaining this module does not connect it to Core or authorize host effects. The pure policies and embedded catalogue from the domain-foundation candidate are retained without a listener or privileged adapter.

The finite --contracts command reports only the embedded catalogue counts: 82 permissions, 86 API contracts, 47 IPC operations and 23 error codes. It accepts no file, URL, credential or operation selector. The generator resolves the three original input paths to their reviewed documentation/specifications/ locations while preserving generated provenance and original blob hashes. The two pure-policy tests read the relocated Security Baseline and DB Model with the same original pins. Registry tests also resolve only the three reviewed contract-source relocations before verifying their original fingerprints and exact registry rows. Negative fixtures copy these five existing inputs into disposable test trees; no second repository authority is created.

The pinned Debian linux/amd64 builder is evidence for this retained candidate only. It is not qualification of all five target operating systems or of the active Core. The integrated revision must obtain its own remote CI results; no local runtime checks were executed for this reconciliation.
