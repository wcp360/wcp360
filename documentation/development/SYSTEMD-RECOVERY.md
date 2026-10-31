# Candidate systemd supervision and reboot recovery

This is a guarded continuation of the disposable Debian 13 VM experiment. It does not install production wcpd/wcp-privd, implement canonical IPC-B, qualify arbitrary hosted applications or open stable release gates. Consult the exact PR workflow before treating a defined check as a successful run.

## Service boundary

| Unit | Identity | Responsibility |
| :--- | :--- | :--- |
| wcp-candidate-vm-arm | root, oneshot | Reissue the current boot marker only with explicit root-owned, machine-bound recovery opt-in and the disposable VM/Debian identity |
| wcp-candidate-broker | root | Validate existing sites, own the fixed Nginx/PHP-FPM child processes and serialize typed privileged operations |
| wcp-candidate-agent | dedicated UID 31002 | Authenticate worker UID 31003 and forward the bounded protocol to the broker |

The units live in WCP360_Core/resources/services/systemd/candidate. They use fixed candidate identities, executables and paths. The VM harness installs/enables them only inside its already-guarded disposable guest. No Windows service or host KVM permissions are changed. The agent remains separate from the privileged broker; the Symfony Core is not a child of either service.

Both long-running units announce systemd readiness only after their Unix socket exists with the required ownership. The broker validates and starts previously confirmed sites before announcing readiness. Restart=on-failure and KillMode=control-group tie the owned child processes to the broker lifetime. An unexpected child exit causes broker failure and controlled group restart; no external PID file is treated as an instruction to signal a process. A kernel-held exclusive lock prevents a concurrent broker from replacing the live socket.

The agent has no capabilities, a read-only filesystem with only its socket directory writable, restricted address families and no privilege escalation. The broker needs root and writes to account databases and candidate directories; it has a broader privilege boundary. Its unit applies no-new-privileges, protected system/home/kernel paths, private temporary/devices views and task/memory limits. These controls do not constitute a production sandbox or an independent security review.

The broker deliberately uses the system manager’s default root identity and also checks its effective UID in Go. On the tested Debian systemd 257 path, redundant User=root combined with seccomp-implying restrictions removes CAP_SETUID before exec; no-new-privileges prevents exec from restoring it. Nginx then cannot drop worker privileges. Omitting this redundant identity directive retains the same root identity and the existing sandbox settings, without granting ambient capabilities. The preflight reads identity and capability directives as well as the sandbox settings from the actual unit, then verifies the required non-root transition. See the [systemd 257 execution implementation](https://github.com/systemd/systemd/blob/v257/src/core/exec-invoke.c).

Managed commands use direct /dev/null output descriptors, rather than Go output-copy pipes. This lets Cmd.Wait observe a dead master even while its surviving workers still hold stdout/stderr open; the systemd control group can then clean up and restart. A Linux regression test covers a master that exits while a bounded descendant retains those descriptors. See [Go process and I/O waiting](https://pkg.go.dev/os/exec#Cmd.Wait).

## Explicit boot opt-in

Ordinary VM execution remains refused. Boot recovery additionally requires:

- Debian 13 and the exact harness SMBIOS product WCP360 disposable candidate VM.
- A root-owned 0700 /etc/wcp360-candidate directory.
- A root-owned, single-link, regular 0600 allow-boot-recovery file in that directory, with the exact wcp360.candidate.vm-recovery/1 marker and this guest's /etc/machine-id.
- WCP_ISOLATED_TEST_VM=1; the container profile must be absent.

Only the trusted disposable-VM harness creates this persistent opt-in. The arm-vm command has no caller-selected path, command or identifier. It refuses a missing, malformed or differently bound policy. It recreates the root-owned /run marker for the current kernel boot ID; it does not authorize a generic Linux server. Without this explicit opt-in, the existing per-boot expiration behavior remains intact.

Removing the opt-in prevents boot re-arming; already-running processes must be stopped separately if an operator intends immediate shutdown. This is trusted-administrator intent, not cryptographic attestation against root or a hypervisor administrator.

## Recovery rules

1. Read at most four bounded journal records, rejecting unknown/duplicate JSON keys, invalid request identity/fingerprint, unexpected filenames and duplicate host/job bindings.
2. Accept only ACTIVE records. PREPARING or ambiguous journal artifacts require inspection; recovery never invokes provisioning again.
3. Check each Unix account and its group memberships; validate public/private directory ownership and permissions.
4. Compare root-owned application/probe source and Nginx/PHP-FPM configuration with the actual embedded package and shared renderer. Reject symlinks, hardlinks, unexpected files, changed code/configuration and writable or tenant-owned managed files.
5. Run the fixed syntax checks, start the fixed child executables and verify each real HTTP site/tenant/effective-UID identity.
6. Leave activation journals, accounts, application code/configuration and tenant-private state unchanged. Application-level uncertain outcomes are reconciled through the existing inspection operation.

The first candidate deliberately fails recovery for the whole small broker group when any recorded site is incomplete or inconsistent. It neither repairs unknown state nor silently publishes an unreviewed configuration. Core availability is independent, but the affected hosted sites remain unavailable until trusted investigation. This is not per-site fault containment or high availability.

## Automated rehearsal

Run the [clean Debian VM harness](DEBIAN-VM.md). A preflight executes the actual broker sandbox settings in a transient disposable-VM unit and verifies the required root-to-www-data identity transition before package installation. On failure it prints bounded process capability/security metadata and individual-setting diagnostics; these probes do not change installed service settings.

The harness then exercises:

- Initial Symfony → worker → agent → broker activation through the actual systemd units.
- A clean broker service restart, with exact persistent identity/code/configuration/journal hashes preserved.
- An injected SIGKILL of the verified PHP-FPM master belonging to this broker, followed by systemd recovery.
- Concurrent-broker rejection and refusal of changed configuration / PREPARING state. The trusted fixture restores its own injected artifacts explicitly before proceeding; the daemon performs no repair.
- A real reboot of the same QEMU guest. The host verifies a changed kernel boot ID, keeps strict pinned SSH host-key trust and does not manually arm or start services after boot.
- Automatic service readiness and preservation of the same PHP sessions/CSRF tokens, tenant counters and Unix identities after reboot, followed by application outcome inspection.

Synthetic fixture database/state and session cookies are kept privately in the disposable guest; ephemeral SSH keys and seed files are not published as artifacts. QEMU cleanup removes the owned VM and disks at the end.

References: [systemd service lifecycle](https://www.freedesktop.org/software/systemd/man/latest/systemd.service.html), [control-group process cleanup](https://www.freedesktop.org/software/systemd/man/latest/systemd.kill.html), [execution restrictions](https://www.freedesktop.org/software/systemd/man/latest/systemd.exec.html).

## Qualification limits

A passed rehearsal establishes controlled process-crash and ordered reboot recovery for the tested candidate/package. It does not establish sudden power-loss durability, partial-operation completion/rollback, automatic repair, zero-downtime changes, production authorization/revocation fencing, customer application uploads, signed deployment, TLS, quotas, FrankenPHP runtime switching or Ubuntu/AlmaLinux support. The VM profile and fixture identities must not be repurposed as a stable installer.
