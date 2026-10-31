# Disposable Debian 13 VM rehearsal

This harness boots a clean official Debian 13 amd64 cloud image under QEMU and runs the isolated candidate with Debian's packaged PHP 8.4 / PHP-FPM and Nginx. It is a development verification path, not a production installer or certification of all WCP360 features. Check the linked PR's exact workflow result before claiming a successful run.

## What is exercised

1. Boot an untouched pinned Debian cloud image with a new writable overlay and NoCloud seed.
2. Authenticate SSH with freshly generated client and host keys. The host public key is pinned before boot; strict host-key checking remains enabled.
3. Wait for cloud-init; verify Debian 13, the harness SMBIOS product, a root-owned current-boot marker and absence of prior candidate state.
4. Install distribution packages in the guest while preventing package hooks from starting default Nginx/PHP-FPM services.
5. Copy the tested Go binaries, source and Composer-locked vendor tree from the candidate build. PHP itself comes from Debian, not Docker. Guest Symfony caches are generated afresh by the non-root worker.
6. Verify that unflagged Go and PHP entry points refuse this VM before physical effects.
7. Run Symfony jobs through separate worker, agent and root broker identities, provision two PHP-FPM tenants, exercise the embedded application and inspect uncertain outcomes without replaying mutations.
8. Check non-root PHP execution, private writable application state, immutable public code, CSRF rejection, cross-site file denial, hidden-path denial and unauthorized IPC peer/schema rejection.
9. Stop the owned QEMU process and remove the temporary overlay, seed, payload and ephemeral keys, including on failure.

No host directory, host block device, Docker socket or host process namespace is mounted in the guest. QEMU runs as the CI user with user-mode NAT. Only guest SSH is forwarded, on host loopback port 22222; guest web services have no host forwarding. Guest Internet access is needed for Debian's signed package repositories. This is not containment against a malicious hypervisor/root administrator.

## Reproduce

Use a disposable Linux runner with Docker, QEMU system x86, qemu-img, cloud-localds, OpenSSH client, curl and Python 3 installed. Run as a non-root user with access to Docker. The script does not install host packages itself.

~~~sh
docker build -f WCP360_Build/environments/core-candidate/Dockerfile -t wcp360-candidate .
WCP_DISPOSABLE_VM_HARNESS=1 bash WCP360_Build/verification/candidate-debian-vm.sh
~~~

The existing core workflow installs QEMU tools only on its disposable GitHub runner, then invokes this same script. KVM is used when accessible; otherwise QEMU uses TCG. Startup and guest execution have bounded deadlines. Accelerator selection and package versions are printed to the run log. The marker in /run expires with the boot; it is never automatically re-created by an installed service.

## Pinned input and trust

| Input | Value |
| :--- | :--- |
| Official image | debian-13-genericcloud-amd64-20261001-2618.qcow2 |
| Source | [Debian cloud image directory](https://cloud.debian.org/images/cloud/trixie/20261001-2618/) |
| SHA-512 source | [Debian SHA512SUMS](https://cloud.debian.org/images/cloud/trixie/20261001-2618/SHA512SUMS), retrieved over HTTPS and pinned in the harness |
| SHA-512 | f46f0671a6e5bdec5291ab8972bae2f10e5408c2f64a74078f11efc2f06a436a9d0313ed50e0472542eeabf780e9f7c792ac0a314c6c20507fcd9fd81b468c3d |
| Disk | New 12 GiB qcow2 overlay; base is never modified |
| VM resources | 2 vCPU, 3 GiB RAM |
| Runtime package source | Debian 13 repositories; actual installed versions are reported, not assumed fixed |

The pinned digest detects image changes; the harness does not claim independent OpenPGP signature verification. The candidate payload is hashed before transfer and checked in the guest; it is a test artifact, not a signed stable release.

Upstream references: [cloud-init NoCloud drive format](https://docs.cloud-init.io/en/latest/reference/datasources/nocloud.html), [cloud-init SSH host keys](https://docs.cloud-init.io/en/latest/reference/modules.html#ssh), [QEMU user-mode networking](https://www.qemu.org/docs/master/system/invocation.html).

## Limits

Passing this rehearsal establishes only this initial provisioning and packaged-application path on the reported Debian image and packages. It does not qualify reboot/power-loss reconstruction, systemd service recovery, arbitrary application uploads, live runtime migration, production authorization/fencing, MFA, TLS, quotas, signed updates or release promotion. FrankenPHP worker verification remains its separate container test; this VM path uses PHP-FPM. Ubuntu and AlmaLinux remain unqualified. Production boot and stable publishing gates remain closed.
