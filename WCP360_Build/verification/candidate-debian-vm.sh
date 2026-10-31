#!/usr/bin/env bash
# Run on a disposable Linux CI runner; QEMU itself is unprivileged.
set -Eeuo pipefail
[[ "${WCP_DISPOSABLE_VM_HARNESS:-}" == 1 && "$(uname -s)" == Linux && "$(id -u)" != 0 ]] || { echo 'Explicit non-root disposable Linux VM harness required.' >&2; exit 78; }
for binary in qemu-system-x86_64 qemu-img cloud-localds ssh ssh-keygen curl python3 docker; do command -v "$binary" >/dev/null; done
repo_root="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
work_dir="$(mktemp -d /tmp/wcp360-vm.XXXXXXXX)"
chmod 0700 "$work_dir"
qemu_pid=
container_id=
cleanup(){
    status=$?
    trap - EXIT
    if (( status != 0 )); then tail -n 100 "$work_dir/serial.log" "$work_dir/qemu.log" 2>/dev/null || true; fi
    [[ -z "$qemu_pid" ]] || { kill "$qemu_pid" 2>/dev/null || true; wait "$qemu_pid" 2>/dev/null || true; }
    [[ -z "$container_id" ]] || docker rm "$container_id" >/dev/null 2>&1 || true
    # Only our newly-created private temporary directory can be removed.
    [[ "$work_dir" == /tmp/wcp360-vm.* && -d "$work_dir" && ! -L "$work_dir" ]] && rm -rf -- "$work_dir"
    exit "$status"
}
trap cleanup EXIT
image=debian-13-genericcloud-amd64-20261001-2618.qcow2
image_url="https://cloud.debian.org/images/cloud/trixie/20261001-2618/$image"
image_sha=f46f0671a6e5bdec5291ab8972bae2f10e5408c2f64a74078f11efc2f06a436a9d0313ed50e0472542eeabf780e9f7c792ac0a314c6c20507fcd9fd81b468c3d
curl --fail --location --proto '=https' --tlsv1.2 --retry 3 "$image_url" -o "$work_dir/base.qcow2"
printf '%s  %s\n' "$image_sha" "$work_dir/base.qcow2" | sha512sum --check --strict
qemu-img create -f qcow2 -F qcow2 -b "$work_dir/base.qcow2" "$work_dir/overlay.qcow2" 12G
ssh-keygen -q -t ed25519 -N '' -f "$work_dir/client-key"
ssh-keygen -q -t ed25519 -N '' -f "$work_dir/host-key"
# Generate only ephemeral credentials. Neither seed nor keys enter artifacts/logs.
python3 - "$work_dir" <<'PY'
import json, pathlib, sys
p = pathlib.Path(sys.argv[1])
pub = (p/'client-key.pub').read_text().strip()
host_pub = (p/'host-key.pub').read_text().strip()
config = {
 'hostname':'wcp360-disposable', 'manage_etc_hosts':True,
 'users':[{'name':'wcp-vm','groups':['sudo'],'sudo':'ALL=(ALL) NOPASSWD:ALL','shell':'/bin/bash','lock_passwd':True,'ssh_authorized_keys':[pub]}],
 'ssh_pwauth':False, 'disable_root':True, 'ssh_deletekeys':True,
 'ssh_keys':{'ed25519_private':(p/'host-key').read_text(),'ed25519_public':host_pub},
 'ssh_publish_hostkeys':{'enabled':False}, 'ssh_quiet_keygen':True,
 'runcmd':[['bash','-ec', 'test "$(cat /sys/class/dmi/id/product_name)" = "WCP360 disposable candidate VM"; umask 022; { printf "wcp360.candidate.vm/1\n"; cat /proc/sys/kernel/random/boot_id; } > /run/wcp360-candidate-vm; chmod 0644 /run/wcp360-candidate-vm']]
}
# JSON is valid YAML; cloud-config requires its first-line format marker.
(p/'user-data').write_text('#cloud-config\n'+json.dumps(config)+'\n')
(p/'meta-data').write_text('instance-id: wcp360-disposable-vm\nlocal-hostname: wcp360-disposable\n')
(p/'known_hosts').write_text('wcp360-candidate-vm '+host_pub+'\n')
PY
cloud-localds "$work_dir/seed.img" "$work_dir/user-data" "$work_dir/meta-data"
accel=(-accel tcg,thread=multi -cpu max)
if [[ -r /dev/kvm && -w /dev/kvm ]]; then accel=(-accel kvm -cpu host); fi
printf 'VM accelerator: %s\n' "${accel[1]}"
# No host folders/devices are exposed. Only SSH is forwarded on loopback.
qemu-system-x86_64 -machine q35 "${accel[@]}" -smp 2 -m 3072 \
    -smbios 'type=1,manufacturer=WCP360,product=WCP360 disposable candidate VM' \
    -drive "file=$work_dir/overlay.qcow2,if=virtio,format=qcow2" \
    -drive "file=$work_dir/seed.img,if=virtio,format=raw,readonly=on" \
    -netdev user,id=net0,hostfwd=tcp:127.0.0.1:22222-:22 -device virtio-net-pci,netdev=net0 \
    -display none -monitor none -serial "file:$work_dir/serial.log" -no-reboot >"$work_dir/qemu.log" 2>&1 &
qemu_pid=$!
ssh_opts=(-p 22222 -i "$work_dir/client-key" -o IdentitiesOnly=yes -o BatchMode=yes -o ConnectTimeout=5 -o StrictHostKeyChecking=yes -o "UserKnownHostsFile=$work_dir/known_hosts" -o HostKeyAlias=wcp360-candidate-vm -o LogLevel=ERROR)
ready=0
for attempt in {1..150}; do
    kill -0 "$qemu_pid"
    if ssh "${ssh_opts[@]}" wcp-vm@127.0.0.1 true 2>/dev/null; then ready=1; break; fi
    sleep 2
done
[[ "$ready" == 1 ]] || { echo 'VM SSH startup deadline exceeded.' >&2; exit 1; }
ssh "${ssh_opts[@]}" wcp-vm@127.0.0.1 'sudo cloud-init status --wait --long'
# Copy the already tested candidate and its locked vendor tree, not Docker's PHP.
mkdir "$work_dir/payload"
container_id="$(docker create wcp360-candidate)"
docker cp "$container_id:/opt/wcp360/." "$work_dir/payload/"
mkdir -p "$work_dir/payload/bin" "$work_dir/payload/WCP360_Build/verification"
docker cp "$container_id:/usr/local/libexec/wcp360/wcp-plan" "$work_dir/payload/bin/wcp-plan"
docker cp "$container_id:/usr/local/libexec/wcp360/wcp-candidate-node" "$work_dir/payload/bin/wcp-candidate-node"
cp "$repo_root/WCP360_Build/verification/candidate-vm-guest.sh" "$work_dir/payload/WCP360_Build/verification/"
# Runtime caches must be generated by the guest's own unprivileged worker.
tar --exclude='./WCP360_Core/var' -C "$work_dir/payload" -czf "$work_dir/candidate.tar.gz" .
sha256sum "$work_dir/candidate.tar.gz" | cut -d' ' -f1 >"$work_dir/payload.sha256"
ssh "${ssh_opts[@]}" wcp-vm@127.0.0.1 'cat > /home/wcp-vm/candidate.tar.gz' <"$work_dir/candidate.tar.gz"
expected_payload="$(cat "$work_dir/payload.sha256")"
ssh "${ssh_opts[@]}" wcp-vm@127.0.0.1 "printf '%s  /home/wcp-vm/candidate.tar.gz\n' '$expected_payload' | sha256sum --check --strict && sudo install -d -m 0755 /opt/wcp360 && sudo tar --no-same-owner -xzf /home/wcp-vm/candidate.tar.gz -C /opt/wcp360"
ssh "${ssh_opts[@]}" wcp-vm@127.0.0.1 'sudo timeout 900 bash /opt/wcp360/WCP360_Build/verification/candidate-vm-guest.sh'
printf 'PASS: clean pinned Debian 13 cloud VM; packaged PHP-FPM; two tenants and stateful application.\n'
