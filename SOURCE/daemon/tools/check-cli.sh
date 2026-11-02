#!/bin/sh
# Black-box tests for the built inert CLI, inside the canonical offline builder.
set -eu
repo_root=$(CDPATH= cd -- "$(dirname -- "$0")/../../.." && pwd)
output="$repo_root/out/sc-m01/daemon"
binary="$output/wcpd"
cases="$output/cli-process"
mkdir -p "$cases"
: > "$output/cli-process.tsv"
: > "$cases/empty.expected"
printf '%s\n' 'Usage: wcpd [--help | --version | --check | --contracts]' \
    'SC-M01 inert foundation: no listener, database or privileged operation.' > "$cases/help.expected"
printf '%s\n' 'wcpd: SC-M01 inert foundation (CODE-BASELINE NONE)' > "$cases/version.expected"
printf '%s\n' '{"component":"wcpd","mode":"inert-foundation","control_transport":"disabled","privileged_operations":"disabled"}' > "$cases/check.expected"
printf '%s\n' '{"permissions":82,"api_contracts":86,"ipc_operations":47,"error_codes":23}' > "$cases/contracts.expected"
printf '%s\n' 'wcpd: unsupported arguments; use --help' > "$cases/rejection.expected"

run_case() {
    name=$1
    expected_code=$2
    expected_stdout=$3
    expected_stderr=$4
    shift 4
    if "$binary" "$@" > "$cases/$name.stdout" 2> "$cases/$name.stderr"; then
        actual_code=0
    else
        actual_code=$?
    fi
    test "$actual_code" -eq "$expected_code"
    cmp "$cases/$expected_stdout.expected" "$cases/$name.stdout"
    cmp "$cases/$expected_stderr.expected" "$cases/$name.stderr"
    printf '%s\tPASS\t%s\n' "$name" "$actual_code" >> "$output/cli-process.tsv"
}

run_case default 0 help empty
run_case help 0 help empty --help
run_case short-help 0 help empty -h
run_case version 0 version empty --version
run_case check 0 check empty --check
run_case contracts 0 contracts empty --contracts
cp "$cases/contracts.stdout" "$output/contracts.json"
cp "$cases/check.stdout" "$output/smoke-status.json"

# Byte-for-byte comparisons catch echoed input, stream mix-ups and extra output.
run_case empty-argument 2 empty rejection ''
run_case unknown-command 2 empty rejection serve
run_case listener-option 2 empty rejection --listen=127.0.0.1:8080
run_case config-option 2 empty rejection --config=/etc/wcp360/synthetic-secret
run_case credential-option 2 empty rejection --token=synthetic-secret
run_case help-extra 2 empty rejection --help synthetic-secret
run_case combined-options 2 empty rejection --check --version
run_case option-separator 2 empty rejection --
run_case wrong-case 2 empty rejection --CHECK

# /dev/full deterministically rejects writes with ENOSPC on Debian. Verify the
# fixture itself before testing that main propagates Run's output-failure code.
test -c /dev/full
if printf 'fixture probe\n' > /dev/full 2>/dev/null; then
    echo 'Output-failure fixture unexpectedly accepted a write' >&2
    exit 1
fi
stdout_failure() {
    name=$1
    shift
    if "$binary" "$@" > /dev/full 2> "$cases/$name.stderr"; then
        actual_code=0
    else
        actual_code=$?
    fi
    test "$actual_code" -eq 1
    cmp "$cases/empty.expected" "$cases/$name.stderr"
    printf '%s\tPASS\t%s\n' "$name" "$actual_code" >> "$output/cli-process.tsv"
}
stdout_failure default-output-failure
stdout_failure help-output-failure --help
stdout_failure short-help-output-failure -h
stdout_failure version-output-failure --version
stdout_failure check-output-failure --check
stdout_failure contracts-output-failure --contracts

stderr_failure() {
    name=$1
    shift
    if "$binary" "$@" > "$cases/$name.stdout" 2> /dev/full; then
        actual_code=0
    else
        actual_code=$?
    fi
    test "$actual_code" -eq 1
    cmp "$cases/empty.expected" "$cases/$name.stdout"
    printf '%s\tPASS\t%s\n' "$name" "$actual_code" >> "$output/cli-process.tsv"
}
stderr_failure rejection-output-failure --token=synthetic-secret
stderr_failure extra-argument-output-failure --help synthetic-secret

test "$(wc -l < "$output/cli-process.tsv")" -eq 23
cat "$output/cli-process.tsv"
