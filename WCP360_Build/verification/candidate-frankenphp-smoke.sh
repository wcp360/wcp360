#!/usr/bin/env bash
set -Eeuo pipefail
[[ "${WCP_ISOLATED_TEST_CONTAINER:-}" == 1 && -f /.dockerenv && "$(id -u)" == 0 ]] || { echo "Disposable root test container required." >&2; exit 78; }
site_id=019a3c3f-0000-7000-8000-000000000001
site_user=wcp_73f7d6e1b8986230876da578
site_root="/srv/wcp360-candidate/vhosts/$site_id"
socket_dir="/run/wcp360-frankenphp/$site_id"
config_dir="/var/lib/wcp360-candidate/frankenphp/$site_id"
state_dir="/var/lib/wcp360-frankenphp/$site_id"
worker_pid=
nginx_pid=
cleanup(){
    status=$?
    trap - EXIT
    if [[ -n "$nginx_pid" ]]; then kill "$nginx_pid" 2>/dev/null || true; fi
    if [[ -n "$worker_pid" ]]; then kill "$worker_pid" 2>/dev/null || true; fi
    if (( status != 0 )); then cat /tmp/frankenphp-worker.log /tmp/nginx-worker-error.log 2>/dev/null || true; fi
    exit "$status"
}
trap cleanup EXIT
useradd --system --no-create-home --user-group --shell /usr/sbin/nologin "$site_user"
# Nginx may reach this site's socket. The application never joins Nginx's shared group.
usermod -aG "$site_user" www-data
install -d -m 0755 /srv/wcp360-candidate/vhosts /run/wcp360-frankenphp
install -d -m 0750 -o root -g "$site_user" "$site_root" "$site_root/public"
install -d -m 0750 -o "$site_user" -g "$site_user" "$socket_dir"
install -d -m 0700 -o "$site_user" -g "$site_user" "$state_dir"
install -d -m 0755 "$config_dir"
install -d -m 0700 /srv/wcp360-candidate/neighbor
printf 'neighbor secret\n' >/srv/wcp360-candidate/neighbor/private.txt
chmod 0600 /srv/wcp360-candidate/neighbor/private.txt
install -m 0640 -o root -g "$site_user" /opt/wcp360/worker-smoke.php "$site_root/public/index.php"
printf 'must not be exposed\n' >"$site_root/public/.secret"
printf '<?php echo "unexpected";\n' >"$site_root/public/other.php"
printf '{"site_id":"%s","hostname":"worker.test","application_server":"frankenphp_worker","worker_acknowledged":true}\n' "$site_id" | /usr/local/libexec/wcp360/wcp-plan plan >/tmp/worker-plan.json
php -r '$p=json_decode(file_get_contents("/tmp/worker-plan.json"),true,16,JSON_THROW_ON_ERROR);file_put_contents($argv[1]."/Caddyfile",$p["caddy"]);file_put_contents("/tmp/nginx-worker-site.conf",$p["nginx"]);' "$config_dir"
runuser -u "$site_user" -- test ! -r /srv/wcp360-candidate/neighbor/private.txt
runuser -u "$site_user" -- env -i PATH=/usr/local/bin:/usr/bin:/bin HOME="$state_dir" MAX_REQUESTS=500 XDG_CONFIG_HOME="$state_dir/config" XDG_DATA_HOME="$state_dir/data" frankenphp validate --config "$config_dir/Caddyfile" --adapter caddyfile
runuser -u "$site_user" -- env -i PATH=/usr/local/bin:/usr/bin:/bin HOME="$state_dir" MAX_REQUESTS=500 XDG_CONFIG_HOME="$state_dir/config" XDG_DATA_HOME="$state_dir/data" frankenphp run --config "$config_dir/Caddyfile" --adapter caddyfile >/tmp/frankenphp-worker.log 2>&1 &
worker_pid=$!
cat >/tmp/nginx-worker.conf <<'NGINX'
user www-data;
worker_processes 1;
pid /tmp/nginx-worker.pid;
error_log /tmp/nginx-worker-error.log info;
events { worker_connections 64; }
http { access_log off; include /tmp/nginx-worker-site.conf; }
NGINX
nginx -t -c /tmp/nginx-worker.conf
nginx -c /tmp/nginx-worker.conf -g 'daemon off;' &
nginx_pid=$!
for attempt in {1..60}; do
    if curl -fsS -H 'Host: worker.test' -H 'X-Request-Marker: warmup' http://127.0.0.1:8080/ >/tmp/worker-warmup.json; then break; fi
    kill -0 "$worker_pid"
    sleep 0.5
done
[[ "$(stat -c '%a' "$socket_dir/http.sock")" == 660 ]]
[[ "$(stat -c '%U:%G' "$socket_dir/http.sock")" == "$site_user:$site_user" ]]
runuser -u nobody -- test ! -w "$socket_dir/http.sock"
curl -fsS -H 'Host: worker.test' -H 'X-Request-Marker: alice-request' http://127.0.0.1:8080/app/one >/tmp/worker-one.json
curl -fsS -H 'Host: worker.test' -H 'X-Request-Marker: bob-request' http://127.0.0.1:8080/app/two >/tmp/worker-two.json
php -r '$a=json_decode(file_get_contents("/tmp/worker-one.json"),true,16,JSON_THROW_ON_ERROR);$b=json_decode(file_get_contents("/tmp/worker-two.json"),true,16,JSON_THROW_ON_ERROR);if($a["pid"]!==$b["pid"] || $b["requests"]!==$a["requests"]+1 || $a["marker"]!=="alice-request" || $b["marker"]!=="bob-request" || $a["previous"]!==null || $b["previous"]!==null || $a["neighbor_readable"] || $b["neighbor_readable"] || $a["euid"]===0 || $b["euid"]===0)exit(1);'
[[ "$(curl -sS -o /dev/null -w '%{http_code}' -H 'Host: worker.test' http://127.0.0.1:8080/.secret)" == 403 ]]
[[ "$(curl -sS -o /dev/null -w '%{http_code}' -H 'Host: worker.test' http://127.0.0.1:8080/other.php)" == 404 ]]

# Retain marker/OS/recycling assertions and exercise the actual Core HTTP services.
php /opt/wcp360/verify-core-worker.php
# No Caddy TCP listener: the application is reachable only through its Unix socket.
if curl --connect-timeout 1 -sS http://127.0.0.1:8081/ >/dev/null 2>&1; then exit 1; fi
if curl --connect-timeout 1 -sS http://127.0.0.1:2019/config/ >/dev/null 2>&1; then exit 1; fi
printf 'FrankenPHP worker: private socket, unprivileged process, persistent worker, recycling and per-request cleanup verified.\n'
