#!/usr/bin/env bash
set -euo pipefail
umask 077
core_root="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/../.." && pwd -P)"
if [[ ${EUID} -eq 0 ]]; then echo 'Run the development candidate installer as a non-root operator.' >&2; exit 1; fi
for tool in php composer go; do command -v "$tool" >/dev/null || { echo "Missing tool: $tool" >&2; exit 1; }; done
php "$core_root/installer/preflight/candidate.php" --target || { echo 'Install PHP 8.4, dependencies and required extensions first.' >&2; exit 1; }
[[ -f "$core_root/../WCP360_SDK/php/CandidateJobSummary.php" && -f "$core_root/../WCP360_Plugins/wcp360-reference-report/candidate-plugin.json" ]] || { echo 'Use the complete repository checkout.' >&2; exit 1; }
cd "$core_root"
if [[ ! -e .env ]]; then
    php -r '$s=file_get_contents(".env.example"); $s=str_replace("replace-with-a-random-local-secret",bin2hex(random_bytes(32)),$s); if(file_put_contents(".env",$s,LOCK_EX)===false)exit(1);'
    chmod 600 .env
fi
composer install --no-interaction --prefer-dist --no-progress
php installer/preflight/candidate.php --target --dependencies
mkdir -p var/bin
(cd agents/wcpd/candidate/planner && CGO_ENABLED=0 go build -trimpath -o ../../../../var/bin/wcp-plan ./cmd/wcp-plan)
php bin/console importmap:install
php bin/console asset-map:compile
php bin/console doctrine:migrations:migrate --no-interaction
printf '%s\n' 'Development candidate prepared. Enroll accounts with WCP_CONTEXT=setup php bin/console wcp:candidate:enroll.' 'No production release or system service was installed.'
