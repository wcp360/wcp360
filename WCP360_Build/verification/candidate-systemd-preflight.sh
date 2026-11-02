#!/usr/bin/env bash
set -Eeuo pipefail
[[ "$(id -u)" == 0 && ! -e /.dockerenv && "$(cat /sys/class/dmi/id/product_name)" == 'WCP360 disposable candidate VM' ]] || exit 78
unit=/opt/wcp360/WCP360_Core/resources/services/systemd/candidate/wcp-candidate-broker.service
probe=/opt/wcp360/WCP360_Build/verification/candidate-identity-probe.py
properties=()
while IFS='=' read -r key value; do
    case "$key" in User|Group|CapabilityBoundingSet|AmbientCapabilities|NoNewPrivileges|ProtectSystem|ProtectHome|PrivateTmp|PrivateDevices|ProtectKernelTunables|ProtectKernelModules|ProtectControlGroups|TasksMax|MemoryMax) properties+=(--property="$key=$value");; esac
done <"$unit"
set +e
systemd-run --quiet --wait --pipe --collect --unit=wcp-candidate-identity-full "${properties[@]}" /usr/bin/python3 "$probe"
status=$?
set -e
if ((status!=0)); then
    for key in NoNewPrivileges ProtectSystem ProtectHome PrivateTmp PrivateDevices ProtectKernelTunables ProtectKernelModules ProtectControlGroups; do
        printf 'DIAGNOSTIC: full profile except %s\n' "$key"
        systemd-run --quiet --wait --pipe --collect --unit="wcp-candidate-identity-$key" "${properties[@]}" --property="$key=no" /usr/bin/python3 "$probe" || true
    done
    exit 1
fi
printf 'PASS: actual broker systemd profile permits the required non-root identity transition.\n'
