#!/usr/bin/env bash
set -euo pipefail
umask 077
[[ ${PGDATABASE:-} == wcp360_restore_test ]] || { echo 'Only the isolated wcp360_restore_test database is supported.' >&2; exit 1; }
[[ $# -eq 1 ]] || { echo 'Supply a candidate backup file.' >&2; exit 1; }
core_root="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/../.." && pwd -P)"
backup_file="$(realpath -e -- "$1")"
[[ "$backup_file" == "$core_root/var/backups/"* && "$backup_file" == *.dump && ! -L "$1" ]] || exit 1
(cd -- "$(dirname -- "$backup_file")" && sha256sum --check --status "$(basename -- "$backup_file").sha256")
[[ "$(psql -XAt --no-password -v ON_ERROR_STOP=1 -c "SELECT count(*) FROM information_schema.tables WHERE table_schema NOT IN ('pg_catalog','information_schema')")" == 0 ]] || { echo 'Restore-test target must be empty.' >&2; exit 1; }
pg_restore --no-password --exit-on-error --single-transaction --no-owner --no-acl --dbname="$PGDATABASE" "$backup_file"
psql -XAt --no-password -v ON_ERROR_STOP=1 -c 'SELECT count(*) FROM wcp_candidate_users'
