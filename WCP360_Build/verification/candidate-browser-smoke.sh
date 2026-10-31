#!/usr/bin/env bash
set -Eeuo pipefail
[[ -f /.dockerenv && "$(id -u)" != 0 ]] || { echo 'Non-root browser test container required.' >&2; exit 78; }
cd /opt/wcp360/WCP360_Core
cleanup(){
    status=$?
    trap - EXIT
    if (( status != 0 )); then
        cat /tmp/wcp-browser/chromedriver.log /tmp/wcp-browser/chromium.log 2>/dev/null || true
    fi
    exit "$status"
}
trap cleanup EXIT
composer test:browser
