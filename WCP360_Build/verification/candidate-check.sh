#!/usr/bin/env bash
set -euo pipefail
repo_root="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/../.." && pwd -P)"
cd "$repo_root/WCP360_Core"
php installer/preflight/candidate.php --target --dependencies
composer validate --strict
composer check-platform-reqs
composer audit --locked
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console lint:container
php bin/console lint:yaml config
php bin/console lint:twig templates
php bin/console asset-map:compile
php vendor/phpunit/phpunit/phpunit --colors=never --testdox
find src migrations tests -type f -name '*.php' -print0 | xargs -0 -n1 php -l >/dev/null
find installer ../WCP360_Build/verification -type f -name '*.sh' -print0 | xargs -0 -n1 bash -n
printf '%s\n' 'PASS: candidate application checks. This does not accept the canonical production baseline.'
