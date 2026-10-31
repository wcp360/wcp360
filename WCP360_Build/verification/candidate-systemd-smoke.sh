#!/usr/bin/env bash
# Destructive fault injection is restricted to the owned disposable VM services.
set -Eeuo pipefail
[[ "$(id -u)" == 0 && "${WCP_ISOLATED_TEST_VM:-}" == 1 && ! -e /.dockerenv && "$(cat /sys/class/dmi/id/product_name)" == 'WCP360 disposable candidate VM' ]] || exit 78
node=/usr/local/libexec/wcp360/wcp-candidate-node
root=/var/lib/wcp360-candidate-recovery-test
state=/var/lib/wcp360-provision-candidate
verify=/opt/wcp360/WCP360_Build/verification
cd /opt/wcp360/WCP360_Core
cleanup(){ status=$?; trap - EXIT; if ((status!=0)); then journalctl --no-pager -u wcp-candidate-vm-arm -u wcp-candidate-broker -u wcp-candidate-agent -n 100; cat "$state"/*error.log 2>/dev/null || true; fi; exit "$status"; }
trap cleanup EXIT
export APP_ENV=test APP_DEBUG=1 APP_SECRET=provision-test-only-secret WCP_CANDIDATE=1 WCP_CONTEXT=worker WCP_PUBLIC_HOST=localhost WCP_ALLOW_ISOLATED_PROVISIONING=1 WCP_PLANNER=/usr/local/libexec/wcp360/wcp-plan DATABASE_URL=sqlite:////var/lib/wcp360-candidate-test-worker/workbench.db
wait_services(){
    for attempt in {1..90}; do
        if systemctl is-active --quiet wcp-candidate-broker && systemctl is-active --quiet wcp-candidate-agent; then return; fi
        sleep 1
    done
    echo 'Supervised service readiness deadline exceeded.' >&2; return 1
}
verify_state(){
    wait_services
    php "$verify/candidate-recovery-state.php" verify
    runuser -u wcp-worker-test -- php "$verify/candidate-provisioning-fixture.php" reconcile
}
case "${1:-}" in
exercise)
    wait_services
    install -d -m 0700 "$root"
    cp /tmp/wcp-provision-requests.json "$root/requests.json"
    php "$verify/candidate-recovery-state.php" capture
    # A second broker must fail before replacing the live IPC socket.
    set +e
    WCP_ALLOW_CANDIDATE_RECOVERY=1 "$node" broker --agent-uid 31002 --worker-uid 31003 --ipc-gid 31004
    status=$?
    set -e
    [[ "$status" == 78 ]]
    systemctl restart wcp-candidate-broker
    verify_state
    printf 'PASS: clean broker service restart and outcome reconciliation.\n'
    # Kill only the PHP-FPM master that is a direct child of our owned broker.
    old_pid="$(systemctl show -p MainPID --value wcp-candidate-broker)"
    child="$(pgrep -P "$old_pid" -x php-fpm8.4)"
    [[ "$child" =~ ^[0-9]+$ && "$(readlink "/proc/$child/exe")" == /usr/sbin/php-fpm8.4 ]]
    printf 'Injecting PHP-FPM master crash: broker=%s child=%s\n' "$old_pid" "$child"
    kill -KILL "$child"
    for attempt in {1..90}; do
        new_pid="$(systemctl show -p MainPID --value wcp-candidate-broker)"
        if [[ "$new_pid" != 0 && "$new_pid" != "$old_pid" ]] && systemctl is-active --quiet wcp-candidate-broker; then break; fi
        sleep 1
    done
    [[ "$new_pid" != 0 && "$new_pid" != "$old_pid" ]]
    verify_state
    printf 'PASS: verified master crash triggers broker replacement and state reconciliation.\n'
    # Missing/changed configuration must refuse recovery, without automatic repair.
    site="$(php -r '$r=json_decode(file_get_contents("/var/lib/wcp360-candidate-recovery-test/requests.json"),true,16,JSON_THROW_ON_ERROR);echo $r[0]["site_id"];')"
    systemctl stop wcp-candidate-broker
    cp "$state/nginx-sites/$site.conf" "$root/nginx.original"
    printf '# unreviewed fixture change\n' >>"$state/nginx-sites/$site.conf"
    if systemctl start wcp-candidate-broker; then echo 'Changed configuration was accepted.' >&2; exit 1; fi
    systemctl stop wcp-candidate-broker
    cmp -s "$root/nginx.original" "$state/nginx-sites/$site.conf" && exit 1
    cp "$root/nginx.original" "$state/nginx-sites/$site.conf"
    systemctl reset-failed wcp-candidate-broker
    systemctl start wcp-candidate-broker
    verify_state
    systemctl stop wcp-candidate-broker
    cp "$state/records/$site.json" "$root/record.original"
    php -r '$p=$argv[1];$r=json_decode(file_get_contents($p),true,16,JSON_THROW_ON_ERROR);$r["state"]="PREPARING";file_put_contents($p,json_encode($r,JSON_THROW_ON_ERROR));' "$state/records/$site.json"
    if systemctl start wcp-candidate-broker; then echo 'Incomplete journal was replayed.' >&2; exit 1; fi
    systemctl stop wcp-candidate-broker
    grep -q PREPARING "$state/records/$site.json"
    cp "$root/record.original" "$state/records/$site.json"
    systemctl reset-failed wcp-candidate-broker
    systemctl start wcp-candidate-broker
    verify_state
    printf 'PASS: systemd readiness/restart, child crash recovery, single-broker lock, changed-config and PREPARING refusal.\n'
    ;;
after-reboot)
    [[ -d "$root" ]]
    cp "$root/requests.json" /tmp/wcp-provision-requests.json
    verify_state
    for service in wcp-candidate-vm-arm wcp-candidate-broker wcp-candidate-agent; do systemctl is-enabled --quiet "$service"; done
    printf 'PASS: automatic candidate boot authorization, systemd recovery and persistent sessions/state after actual guest reboot.\n'
    ;;
*) exit 2;;
esac
