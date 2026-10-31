#!/usr/bin/env bash
set -euo pipefail
umask 077
: "${PGDATABASE:?Set PGDATABASE to wcp360_workbench or wcp360_test}"
[[ "$PGDATABASE" == wcp360_workbench || "$PGDATABASE" == wcp360_test ]] || { echo 'This helper supports candidate databases only.' >&2; exit 1; }
core_root="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/../.." && pwd -P)"
backup_root="$core_root/var/backups"
[[ ! -L "$core_root/var" && ! -L "$backup_root" ]] || { echo 'Symlink backup roots refused.' >&2; exit 1; }
mkdir -p "$backup_root"
backup_root="$(cd -- "$backup_root" && pwd -P)"
[[ "$backup_root" == "$core_root/var/backups" ]] || exit 1
backup_file="$(mktemp "$backup_root/candidate.XXXXXXXX.dump")"
pg_dump --no-password --format=custom --file="$backup_file"
(cd -- "$backup_root" && sha256sum "$(basename -- "$backup_file")" > "$(basename -- "$backup_file").sha256")
printf '%s\n' "$backup_file"
