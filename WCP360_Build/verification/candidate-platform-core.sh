#!/usr/bin/env bash
# Disposable, non-root Core userspace verification; never a host installer.
set -euo pipefail
umask 077
[[ -f /.dockerenv && ${EUID} -eq 10001 && ${WCP_CANDIDATE:-} == 1 ]] || { echo 'Requires the disposable non-root candidate image.' >&2; exit 1; }
core_root=/opt/wcp360/WCP360_Core
fixture=/opt/wcp360/WCP360_Build/verification/candidate-platform-core-fixture.php
cd "$core_root"
[[ -n ${DATABASE_URL:-} && -n ${WCP_TEST_DATABASE_URL:-} && -n ${APP_SECRET:-} ]] || { echo 'Explicit disposable database configuration is required.' >&2; exit 1; }
case "${WCP_TARGET_PROFILE:-}" in debian-12|debian-13|ubuntu-22.04|ubuntu-24.04|ubuntu-26.04) ;; *) echo 'Unknown runtime target.' >&2; exit 1 ;; esac
php -r 'require "src/Shared/Platform/TargetPlatform.php"; $p=\Wcp360\Core\Shared\Platform\TargetPlatform::detectHost(); if ($p===null || $p["id"]!==getenv("WCP_TARGET_PROFILE") || PHP_MAJOR_VERSION!==8 || PHP_MINOR_VERSION!==4) exit(1);'
work_dir="$(mktemp -d /tmp/wcp360-platform-core.XXXXXX)"
fpm_pid=''
nginx_pid=''
cleanup() {
    status=$?
    trap - EXIT INT TERM
    if [[ -n $nginx_pid ]]; then kill "$nginx_pid" 2>/dev/null || true; wait "$nginx_pid" 2>/dev/null || true; fi
    if [[ -n $fpm_pid ]]; then kill "$fpm_pid" 2>/dev/null || true; wait "$fpm_pid" 2>/dev/null || true; fi
    case "$work_dir" in /tmp/wcp360-platform-core.*) rm -rf -- "$work_dir" ;; esac
    exit "$status"
}
trap cleanup EXIT
trap 'exit 130' INT
trap 'exit 143' TERM

# These modes must inspect the existing runtime before checkout preparation.
bash installer/recipes/candidate-install.sh --plan > "$work_dir/runtime-plan.json"
php -r '$p=json_decode(file_get_contents($argv[1]),true,32,JSON_THROW_ON_ERROR); if ($p["plan"]["platform"]!==getenv("WCP_TARGET_PROFILE")) exit(1);' "$work_dir/runtime-plan.json"
bash installer/recipes/candidate-install.sh --check-runtime
bash installer/recipes/candidate-install.sh
composer validate --strict
composer check-platform-reqs
composer audit --locked
composer check
php "$fixture" enroll

# The Core itself runs behind Nginx and PHP-FPM as UID 10001. All process files
# live in a private directory; no host service or privileged daemon is admitted.
mkdir -p "$work_dir"/{client,proxy,fastcgi,uwsgi,scgi}
# Debian's current FastCGI defaults normalize HTTP_HOST through $host, which
# drops a non-default port. Keep the other distribution hardening directives,
# then provide this fixture's fixed authority after validating both host forms.
awk '!($1 == "fastcgi_param" && $2 == "HTTP_HOST")' /etc/nginx/fastcgi_params > "$work_dir/fastcgi_params"
cat > "$work_dir/fpm.conf" <<EOF
[global]
pid = $work_dir/fpm.pid
error_log = $work_dir/fpm.log
daemonize = no
[core]
listen = $work_dir/core.sock
listen.mode = 0600
pm = static
pm.max_children = 2
clear_env = yes
catch_workers_output = yes
EOF
cat >> "$work_dir/fpm.conf" <<'EOF'
env[APP_ENV] = dev
env[APP_DEBUG] = 0
env[APP_SECRET] = $APP_SECRET
env[DATABASE_URL] = $DATABASE_URL
env[WCP_CANDIDATE] = 1
env[WCP_CONTEXT] = user
env[WCP_PUBLIC_HOST] = user.localhost
env[WCP_PLANNER] = $WCP_PLANNER
env[WCP_ALLOW_ISOLATED_PROVISIONING] = 0
env[WCP_ALLOW_FRANKENPHP_PLANS] = 0
EOF
cat > "$work_dir/nginx.conf" <<EOF
worker_processes 1;
pid $work_dir/nginx.pid;
error_log $work_dir/nginx.log warn;
events { worker_connections 128; }
http {
    include /etc/nginx/mime.types;
    default_type application/octet-stream;
    access_log off;
    client_body_temp_path $work_dir/client;
    proxy_temp_path $work_dir/proxy;
    fastcgi_temp_path $work_dir/fastcgi;
    uwsgi_temp_path $work_dir/uwsgi;
    scgi_temp_path $work_dir/scgi;
    server {
        listen 127.0.0.1:8080;
        server_name user.localhost;
        root $core_root/public;
        if (\$host != "user.localhost") { return 400; }
        if (\$http_host != "user.localhost:8080") { return 400; }
        location / { try_files \$uri /index.php\$is_args\$args; }
        location = /index.php {
            include $work_dir/fastcgi_params;
            fastcgi_param HTTP_HOST user.localhost:8080;
            fastcgi_param SCRIPT_FILENAME \$document_root/index.php;
            fastcgi_pass unix:$work_dir/core.sock;
        }
        location ~ \.php$ { return 404; }
        location ~ /\. { deny all; }
    }
}
EOF
php-fpm --test --fpm-config "$work_dir/fpm.conf"
nginx -t -c "$work_dir/nginx.conf"
php-fpm --nodaemonize --fpm-config "$work_dir/fpm.conf" > "$work_dir/fpm.stdout" 2>&1 &
fpm_pid=$!
nginx -c "$work_dir/nginx.conf" -g 'daemon off;' > "$work_dir/nginx.stdout" 2>&1 &
nginx_pid=$!
ready=0
for attempt in {1..30}; do
    if curl --fail --silent --max-time 2 -H 'Host: user.localhost:8080' http://127.0.0.1:8080/health > /dev/null; then ready=1; break; fi
    kill -0 "$fpm_pid" "$nginx_pid" 2>/dev/null || break
    sleep 1
done
[[ $ready == 1 ]] || { echo 'The isolated Core HTTP runtime did not become ready.' >&2; exit 1; }
for rejected_host in user.localhost other.localhost:8080 user.localhost:8081; do
    [[ "$(curl --silent --max-time 3 -o /dev/null -w '%{http_code}' -H "Host: $rejected_host" http://127.0.0.1:8080/health)" == 400 ]] || { echo 'The fixed HTTP authority boundary was not enforced.' >&2; exit 1; }
done
[[ "$(curl --silent --max-time 3 -o /dev/null -w '%{http_code}' --request-target 'http://other.localhost:8080/health' -H 'Host: user.localhost:8080' http://127.0.0.1:8080/health)" == 400 ]] || { echo 'A conflicting absolute request target was admitted.' >&2; exit 1; }
printf '%s\n' 'PASS: fixed HTTP authority; missing/wrong port, foreign host and conflicting absolute target refused.'
php "$fixture" submit
WCP_CONTEXT=worker WCP_PUBLIC_HOST=localhost php bin/console wcp:candidate:work
php "$fixture" verify
php "$fixture" snapshot > var/platform-core-snapshot.json
printf 'PASS: %s Core PHP 8.4/PostgreSQL 17 installer/enrollment/HTTP/job/plugin runtime\n' "$WCP_TARGET_PROFILE"
