#!/usr/bin/env bash
set -euo pipefail
umask 077
core_root="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/../.." && pwd -P)"
if [[ ${1:-} == --web-stacks ]]; then
    shift
    command -v php >/dev/null || { echo 'PHP 8.1 or newer is required for read-only inspection.' >&2; exit 1; }
    exec php "$core_root/installer/preflight/runtime-plan.php" --web-stacks "$@"
fi
if [[ ${1:-} == --plan && ${2:-} == --web-stack=* ]]; then
    shift
    command -v php >/dev/null || { echo 'PHP 8.1 or newer is required for read-only inspection.' >&2; exit 1; }
    exec php "$core_root/installer/preflight/runtime-plan.php" "$@"
fi
[[ $# -le 1 ]] || { echo 'Usage: candidate-install.sh [--plan|--check-runtime|--host-check|--status|--resume]' >&2; exit 64; }
case "${1:-}" in
    --plan|--check-runtime)
        command -v php >/dev/null || { echo 'PHP 8.1 or newer is required for read-only inspection.' >&2; exit 1; }
        if [[ $1 == --plan ]]; then exec php "$core_root/installer/preflight/runtime-plan.php"; fi
        exec php "$core_root/installer/preflight/runtime-readiness.php"
        ;;
    --status)
        command -v php >/dev/null || { echo 'PHP is required for read-only installation status.' >&2; exit 1; }
        exec php "$core_root/installer/recipes/candidate-install.php" --status
        ;;
    --host-check)
        command -v php >/dev/null || { echo 'PHP 8.1 or newer is required for read-only host inspection.' >&2; exit 1; }
        exec php "$core_root/installer/preflight/native-plan.php"
        ;;
    ''|--resume) ;;
    *) echo 'Usage: candidate-install.sh [--plan|--check-runtime|--host-check|--status|--resume]' >&2; exit 64 ;;
esac
if [[ ${EUID} -eq 0 ]]; then echo 'Run the development candidate installer as a non-root operator.' >&2; exit 1; fi
for tool in php composer go; do command -v "$tool" >/dev/null || { echo "Missing tool: $tool" >&2; exit 1; }; done
cd "$core_root"
exec php "$core_root/installer/recipes/candidate-install.php" "$@"
