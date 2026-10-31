# Isolated PHP-FPM provisioning candidate

This is a real-effects experiment limited to an explicitly prepared disposable Linux test container or Debian 13 QEMU VM. It is not canonical IPC-B, a production wcpd/wcp-privd implementation, a stable installer or general platform qualification. Production boot and publishing gates remain unchanged.

## Implemented path

~~~mermaid
flowchart LR
    UI[Authorized activation form] --> DB[Atomic activation record + audit]
    DB --> Worker[Non-root Symfony activation worker]
    Worker --> Agent[Separate non-root Go agent]
    Agent --> Broker[Minimal root candidate broker]
    Broker --> Site[Dedicated Unix identity + embedded demo application]
    Site --> HTTP[Nginx + PHP-FPM]
    HTTP --> Observe[Verify site, tenant and effective UID]
    Observe --> Outcome[Record observed result + audit]
~~~

A successful initial PHP-FPM plan at site revision 2 can be queued once for activation. An explicit per-user/per-tenant activation grant is required in addition to planning permission; there is no automatic grant migration. The disposable fixture grants this authority deliberately. Current tenant permission and activation-grant revocation, runtime mode, readiness and revision are rechecked before dispatch. WCP_ALLOW_ISOLATED_PROVISIONING defaults to 0. The protected form appears only when enabled; wcp:candidate:activate processes one queued operation in worker context. The transport additionally requires Linux and exactly one explicit disposable execution profile. The container profile requires WCP_ISOLATED_TEST_CONTAINER=1 and a regular container marker. The VM profile requires WCP_ISOLATED_TEST_VM=1, no container marker, a root-owned non-writable marker bound to the current boot ID, the harness-specific SMBIOS product and Debian 13. A normal VM or host is refused.

The Go component lives in WCP360_Core/agents/wcpd/candidate/provisioning, builds as wcp-candidate-node, and runs separate agent and broker processes. Its seven-field bounded JSON envelope accepts only provision_php_fpm and inspect_php_fpm. The socket authenticates Linux SO_PEERCRED against dedicated configured UIDs; even UID 0 is rejected as an ordinary caller. The worker cannot connect directly to the broker.

## Limits and privilege boundary

- Fixed .test names, UUIDv7 identities, initial revision and a reviewed embedded PHP demo package plus an immutable health probe; no application upload, arbitrary customer PHP code, chosen executable, output path, command, package operation or generic service control.
- At most four site records per disposable environment.
- Dedicated system user/group per site, root-owned non-writable public code, private writable tmp/var directories, and a PHP-FPM socket accessible to Nginx.
- Nginx joins the site's group; applications never join Nginx's shared group.
- Only fixed useradd/usermod and syntax-check commands execute. The broker supervises its own fixed Nginx/PHP-FPM children and sends only their predefined reload/stop signals.
- Configuration sources reuse the existing Go planner; there is no second template renderer.
- Managed parent directories must be root-owned, not links and not writable by peers. New files use exclusive creation; a durable PREPARING journal entry precedes effects.
- The broker verifies actual HTTP identity and effective non-root UID before acknowledging ACTIVE. It has no customer-selected network destination.

The container guard is an accident-prevention boundary, not a sandbox against a hostile administrator who already controls root. Never mount host /etc, /srv, /run, /var/lib, Docker sockets or host PID namespaces into the test.

## Uncertain outcomes and recovery

Transport failures after dispatch become UNKNOWN_OUTCOME. There is no automatic retry of physical mutations. The broker binds site/job/tenant/hostname/revision to a stored fingerprint and refuses conflicts or a repeated incomplete attempt.

The activation worker's --reconcile option only inspects uncertain operations; stop other activation workers first. Inspection can confirm an already-created healthy site without creating it again. Application audit/outcome publication failures leave the operation uncertain for inspection. Physical outcomes are retained even if permissions change after dispatch; access to their UI remains tenant-authorized.

The disposable Debian VM profile adds [systemd supervision and strict recovery](SYSTEMD-RECOVERY.md) of confirmed ACTIVE sites. Missing/changed managed files, ambiguous journals and PREPARING records refuse recovery and require trusted investigation. Recovery never completes a partially applied site or replays provisioning. Process-crash and ordered reboot checks are separate from sudden power-loss durability and production recovery qualification.

## Reproduce in an isolated container

From the repository root:

~~~sh
docker build -f WCP360_Build/environments/core-candidate/Dockerfile -t wcp360-candidate .
docker run --rm --user 0:0 -e WCP_ISOLATED_TEST_CONTAINER=1 --entrypoint bash wcp360-candidate /opt/wcp360/WCP360_Build/verification/candidate-provisioning-smoke.sh
~~~

No host bind mounts are needed. The probe creates dedicated agent/worker identities, queues two tenants through Symfony, provisions via the agents, checks both PHP responses, checks cross-site file denial, inspects uncertain outcomes and rejects unknown peers/commands. Consult the exact workflow run before claiming the test passed.

## Packaged application and clean VM rehearsal

Provisioning installs the compiled-in demo application as root-owned, non-writable code. Its public JSON endpoint uses native PHP sessions, CSRF-protected POST and a private per-site persistent counter. The health probe is a separate fixed endpoint; inspecting an activation does not execute an application mutation. Both tenant counters, cookie/session behavior, CSRF refusal and code/state ownership are exercised by the shared harness. This is a fixed reviewed fixture, not an application-upload API or a general package installer.

The Debian VM profile uses the distribution's fixed /usr/sbin/php-fpm8.4 executable; the container profile retains /usr/local/sbin/php-fpm. No executable path is caller-selected. See [the clean VM rehearsal](DEBIAN-VM.md) for the pinned image, SSH trust, commands and qualification limits. A workflow definition alone is not evidence of a passed run.

## Required next boundaries

Approved production contracts/identity/entitlement model, durable authorization/fencing across revocation, production supervision/power-loss/partial-operation recovery, customer-controlled application deployment, TLS, quota enforcement, signed installation/update/recovery and independent Engineering/Security review remain necessary. FrankenPHP live switching/draining and Ubuntu/AlmaLinux adapters remain separate work. This candidate does not alter authoritative specification artifacts or assert production readiness.
