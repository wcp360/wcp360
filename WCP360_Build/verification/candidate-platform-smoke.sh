#!/usr/bin/env bash
# Native package compatibility probe; never a production installer or VM guard.
set -Eeuo pipefail
[[ ${WCP_ISOLATED_TEST_CONTAINER:-} == 1 && -f /.dockerenv && ${EUID} == 0 ]] || exit 78
[[ ${WCP_ISOLATED_TEST_VM:-} == '' ]] || exit 78
[[ ${WCP_TARGET_PROFILE:-} =~ ^(debian-(12|13)|ubuntu-(22|24|26)\.04)$ ]] || exit 64
core_root="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/../../WCP360_Core" && pwd -P)"
work_dir="$(mktemp -d /tmp/wcp360-platform.XXXXXXXX)"
php "$core_root/installer/preflight/platform.php" > "$work_dir/platform.json"
cat "$work_dir/platform.json"
set +e
php "$core_root/installer/preflight/candidate.php" --target > "$work_dir/preflight.json"
preflight_status=$?
set -e
cat "$work_dir/preflight.json"
php -r '
$identity=json_decode(file_get_contents($argv[1]),true,32,JSON_THROW_ON_ERROR);
$preflight=json_decode(file_get_contents($argv[2]),true,32,JSON_THROW_ON_ERROR);
$profile=$identity["platform"]??null;
if(!is_array($profile)||($identity["supported_target"]??false)!==true||($profile["id"]??null)!==$argv[3]||($identity["qualified_release"]??null)!==false)exit(1);
$series=PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;
if(($profile["native_php_series"]??null)!==$series)throw new RuntimeException("Native PHP package stream changed; review the target profile.");
$checks=$preflight["checks"]??[];
if(($checks["supported_os"]??false)!==true||($checks["amd64"]??false)!==true)exit(1);
foreach($checks as $name=>$passed)if($name!=="php_8_4"&&$passed!==true)throw new RuntimeException("Missing native probe prerequisite: ".$name);
$compatible=$series==="8.4";
if(($checks["php_8_4"]??null)!==$compatible||(int)$argv[4]!==($compatible?0:1))exit(1);
echo $compatible?"PASS: target identity and Core PHP 8.4 prerequisites.\n":"PASS: target identity; full Core preflight correctly refuses native PHP ".$series." (PHP 8.4 must be supplied separately).\n";
' "$work_dir/platform.json" "$work_dir/preflight.json" "$WCP_TARGET_PROFILE" "$preflight_status"
php --version | head -n 1
nginx -v
dpkg-query -W -f='${Package} ${Version}\n' nginx php-cli php-fpm
bash "$core_root/../WCP360_Build/verification/candidate-linux-smoke.sh"
printf 'PASS: %s native package/identity/hosting probe; full Core and systemd VM qualification are separate.\n' "$WCP_TARGET_PROFILE"
