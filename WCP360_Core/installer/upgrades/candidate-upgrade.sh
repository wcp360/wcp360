#!/usr/bin/env bash
set -euo pipefail
[[ $# -eq 1 ]] || { echo 'Supply the verified candidate backup created before upgrading.' >&2; exit 1; }
core_root="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/../.." && pwd -P)"
backup_file="$(realpath -e -- "$1")"
[[ "$backup_file" == "$core_root/var/backups/"* && "$backup_file" == *.dump ]] || exit 1
(cd -- "$(dirname -- "$backup_file")" && sha256sum --check --status "$(basename -- "$backup_file").sha256")
cd "$core_root"
php bin/console doctrine:migrations:migrate --no-interaction
printf '%s\n' 'Candidate migrations applied. This helper does not switch application releases or roll back external effects.'
