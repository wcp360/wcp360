#!/usr/bin/env bash
set -euo pipefail
[[ ${WCP_ISOLATED_TEST_CONTAINER:-} == 1 && -f /.dockerenv && ${EUID} == 0 ]] || { echo 'Run only in the disposable CI container, without host service/filesystem mounts.' >&2; exit 1; }
core_root="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/../../WCP360_Core" && pwd -P)"
test_root="$(mktemp -d /tmp/wcp360-hosting-smoke.XXXXXXXX)"
site_id=019a3c3f-0000-7000-8000-000000000001
unix_user=wcp_73f7d6e1b8986230876da578
cleanup() {
    if [[ -f "$test_root/nginx.pid" ]]; then kill "$(cat "$test_root/nginx.pid")" 2>/dev/null || true; fi
    if [[ -f "$test_root/fpm.pid" ]]; then kill "$(cat "$test_root/fpm.pid")" 2>/dev/null || true; fi
}
trap cleanup EXIT
printf '%s' '{"site_id":"019a3c3f-0000-7000-8000-000000000001","hostname":"example.test"}' | "${WCP_PLANNER:?Set the trusted planner binary path}" plan > "$test_root/plan.json"
php -r '$p=json_decode(file_get_contents($argv[1]),true,16,JSON_THROW_ON_ERROR);file_put_contents($argv[2],$p["nginx"]);file_put_contents($argv[3],$p["php_fpm"]);' "$test_root/plan.json" "$test_root/site.conf" "$test_root/pool.conf"
id "$unix_user" >/dev/null 2>&1 || useradd --system --user-group --no-create-home --shell /usr/sbin/nologin "$unix_user"
mkdir -p "/srv/wcp360-candidate/vhosts/$site_id/public" "/srv/wcp360-candidate/vhosts/$site_id/tmp/sessions" /run/wcp360-candidate
printf '%s' '<?php echo "wcp360-candidate-ok";' > "/srv/wcp360-candidate/vhosts/$site_id/public/index.php"
printf '%s' '<?php echo json_encode(["uid"=>posix_geteuid(),"owner"=>fileowner(__FILE__)],JSON_THROW_ON_ERROR);' > "/srv/wcp360-candidate/vhosts/$site_id/public/identity.php"
printf '%s' 'must-not-be-served' > "/srv/wcp360-candidate/vhosts/$site_id/public/.secret"
printf '%s' '<?php echo "hidden-must-not-execute";' > "/srv/wcp360-candidate/vhosts/$site_id/public/.hidden.php"
chown -R "$unix_user:www-data" "/srv/wcp360-candidate/vhosts/$site_id"
chmod 0710 "/srv/wcp360-candidate/vhosts/$site_id"
chmod 0750 "/srv/wcp360-candidate/vhosts/$site_id/public"
chmod 0640 "/srv/wcp360-candidate/vhosts/$site_id/public/index.php" "/srv/wcp360-candidate/vhosts/$site_id/public/identity.php" "/srv/wcp360-candidate/vhosts/$site_id/public/.secret"
chmod 0700 "/srv/wcp360-candidate/vhosts/$site_id/tmp" "/srv/wcp360-candidate/vhosts/$site_id/tmp/sessions"
runuser -u nobody -- test ! -r "/srv/wcp360-candidate/vhosts/$site_id/public/index.php"
chown www-data:www-data /run/wcp360-candidate
chmod 0750 /run/wcp360-candidate
# Nginx workers need access to the fixture configuration directory.
chmod 0755 "$test_root"
cat > "$test_root/nginx.conf" <<EOF
user www-data;
worker_processes 1;
pid $test_root/nginx.pid;
error_log $test_root/nginx-error.log;
events { worker_connections 64; }
http {
    access_log off;
    server { listen 8080 default_server; return 444; }
    include $test_root/site.conf;
}
EOF
cat > "$test_root/fpm.conf" <<EOF
[global]
pid = $test_root/fpm.pid
error_log = $test_root/fpm-error.log
daemonize = no
include = $test_root/pool.conf
EOF
fpm_command="$(command -v php-fpm || command -v php-fpm8.4)"
nginx -t -c "$test_root/nginx.conf"
"$fpm_command" -t -y "$test_root/fpm.conf"
"$fpm_command" -y "$test_root/fpm.conf" > "$test_root/fpm-output.log" 2>&1 &
nginx -c "$test_root/nginx.conf"
for attempt in {1..20}; do
    if [[ "$(curl --silent --show-error --fail --max-time 2 -H 'Host: example.test' http://127.0.0.1:8080/index.php 2>/dev/null || true)" == wcp360-candidate-ok ]]; then break; fi
    sleep 0.2
done
[[ "$(curl --silent --show-error --fail --max-time 3 -H 'Host: example.test' http://127.0.0.1:8080/index.php)" == wcp360-candidate-ok ]]
curl --silent --show-error --fail --max-time 3 -H 'Host: example.test' http://127.0.0.1:8080/identity.php > "$test_root/identity.json"
php -r '$r=json_decode(file_get_contents($argv[1]),true,8,JSON_THROW_ON_ERROR);$expected=(int)$argv[2];if($expected===0||$r!==["uid"=>$expected,"owner"=>$expected])exit(1);' "$test_root/identity.json" "$(id -u "$unix_user")"
[[ "$(curl --silent --max-time 3 -o /dev/null -w '%{http_code}' -H 'Host: example.test' http://127.0.0.1:8080/.secret)" == 403 ]]
[[ "$(curl --silent --max-time 3 -o /dev/null -w '%{http_code}' -H 'Host: example.test' http://127.0.0.1:8080/missing.php)" == 404 ]]
[[ "$(curl --silent --max-time 3 -o /dev/null -w '%{http_code}' -H 'Host: example.test' http://127.0.0.1:8080/.hidden.php)" == 403 ]]
printf '%s\n' 'PASS: real Nginx -> Unix socket -> isolated PHP-FPM -> PHP response with expected non-root UID; hidden files denied; missing PHP rejected.'
