#!/usr/bin/env bash
# Trusted harness, copied into a freshly booted disposable QEMU guest only.
set -Eeuo pipefail
[[ "$(id -u)" == 0 && ! -e /.dockerenv && "$(cat /sys/class/dmi/id/product_name)" == 'WCP360 disposable candidate VM' ]] || exit 78
[[ "$(stat -c '%u:%a' /run/wcp360-candidate-vm)" == 0:644 ]] || exit 78
{ printf 'wcp360.candidate.vm/1\n'; cat /proc/sys/kernel/random/boot_id; } | cmp -s - /run/wcp360-candidate-vm || exit 78
source /etc/os-release
[[ "$ID" == debian && "$VERSION_ID" == 13 ]] || exit 78
[[ ! -e /var/lib/wcp360-provision-candidate && ! -e /srv/wcp360-candidate ]] || { echo 'Guest is not clean.' >&2; exit 78; }
# Prevent package postinst from starting default web services. Only this guest is affected.
[[ ! -e /usr/sbin/policy-rc.d ]] || exit 78
printf '#!/bin/sh\nexit 101\n' >/usr/sbin/policy-rc.d
chmod 0755 /usr/sbin/policy-rc.d
export DEBIAN_FRONTEND=noninteractive
apt-get update -qq
apt-get install -y --no-install-recommends nginx php8.4-cli php8.4-fpm php8.4-curl php8.4-intl php8.4-mbstring php8.4-xml php8.4-pgsql php8.4-sqlite3 php8.4-zip curl ca-certificates
rm -- /usr/sbin/policy-rc.d
systemctl is-active --quiet nginx && exit 1
systemctl is-active --quiet php8.4-fpm && exit 1
install -d -m 0755 /usr/local/libexec/wcp360
install -m 0755 /opt/wcp360/bin/wcp-plan /usr/local/libexec/wcp360/wcp-plan
install -m 0755 /opt/wcp360/bin/wcp-candidate-node /usr/local/libexec/wcp360/wcp-candidate-node
# Without root intent, both language gates must reject before touching the machine.
set +e
/usr/local/libexec/wcp360/wcp-candidate-node guard-check --agent-uid 31002 --worker-uid 31003 --ipc-gid 31004
status=$?
set -e
[[ "$status" == 78 ]] || exit 1
cd /opt/wcp360/WCP360_Core
php -r 'require "vendor/autoload.php"; try { Wcp360\Core\Module\Hosting\Infrastructure\DisposableExecution::requireEnvironment(); exit(1); } catch (RuntimeException) { echo "PASS: unflagged VM rejected.\n"; }'
# A boot marker alone does not authorize persistent boot recovery.
set +e
WCP_ISOLATED_TEST_VM=1 /usr/local/libexec/wcp360/wcp-candidate-node arm-vm
status=$?
set -e
[[ "$status" == 78 ]] || exit 1
# This boot's marker is only accepted with the explicit VM flag.
export WCP_ISOLATED_TEST_VM=1 WCP_CANDIDATE_SYSTEMD=1
unset WCP_ISOLATED_TEST_CONTAINER
install -d -m 0755 var/cache/test
bash /opt/wcp360/WCP360_Build/verification/candidate-provisioning-smoke.sh
bash /opt/wcp360/WCP360_Build/verification/candidate-systemd-smoke.sh exercise
php --version | head -n 1
/usr/sbin/php-fpm8.4 --version | head -n 1
dpkg-query -W -f='${Package} ${Version}\n' nginx php8.4-fpm
printf 'PASS: Debian 13 VM qualification is limited to this isolated candidate path; no production installer, hard power-loss durability, TLS or quotas claimed.\n'
