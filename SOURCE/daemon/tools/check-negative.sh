#!/bin/sh
# Execute only in the same unprivileged, offline builder as check.sh.
# Mutations affect disposable copies, never the candidate or its gate report.
set -eu
repo_root=$(CDPATH= cd -- "$(dirname -- "$0")/../../.." && pwd)
output="$repo_root/out/sc-m01/daemon"
fixtures=$(mktemp -d /tmp/wcp-negative.XXXXXX)
real_go=$(command -v go)
mkdir -p "$output/negative"
: > "$output/negative-gates.tsv"

prepare() {
    fixture="$fixtures/$1"
    mkdir -p "$fixture/SOURCE" "$fixture/out" "$fixture/bin"
    cp -R "$repo_root/SOURCE/daemon" "$fixture/SOURCE/daemon"
    # Preserve the five pinned source inputs required by policy/registry tests.
    # Fixture copies are not a second repository authority.
    for input in \
        documentation/specifications/api/WCP360_API_Contract_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md \
        documentation/specifications/security/WCP360_Permission_Registry_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md \
        documentation/specifications/ipc/WCP360_IPC_Contract_WCP360-IPC-B-1_CANONICAL_IMPLEMENTATION_EXPORT.md \
        documentation/specifications/security/WCP360_Security_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md \
        documentation/specifications/database/WCP360_DB_Model_v1.0_Canonical_Implementation_Contract.md
    do
        mkdir -p "$fixture/$(dirname "$input")"
        cp "$repo_root/$input" "$fixture/$input"
    done
    chmod -R u+w "$fixture"
}
reject_at() {
    case_name=$1
    expected_gate=$2
    set +e
    PATH="$fixture/bin:$PATH" sh "$fixture/SOURCE/daemon/tools/check.sh" > "$output/negative/$case_name.log" 2>&1
    result=$?
    set -e
    test "$result" -ne 0
    report="$fixture/out/sc-m01/daemon/gates.tsv"
    test -s "$report"
    awk -F '\t' -v expected="$expected_gate" '
        $2 == "FAIL" {count++; if ($1 != expected || $3 == "0") exit 1}
        END {if (count != 1) exit 1}
    ' "$report"
    cp "$report" "$output/negative/$case_name-gates.tsv"
    printf '%s\tREJECTED_AS_EXPECTED\t%s\n' "$case_name" "$expected_gate" >> "$output/negative-gates.tsv"
}

prepare wrong-go-version
printf '#!/bin/sh\nif [ "$1" = version ]; then echo "go version go1.27.2 linux/amd64"; else exec "%s" "$@"; fi\n' "$real_go" > "$fixture/bin/go"
chmod u+x "$fixture/bin/go"
reject_at wrong-go-version daemon-toolchain

prepare forbidden-dependency
printf '\nrequire example.invalid/forbidden v1.0.0\n' >> "$fixture/SOURCE/daemon/go.mod"
reject_at forbidden-dependency daemon-no-third-party-runtime-deps

prepare stale-module-lock
printf 'golang.org/x/mod v0.42.0 h1:ICeL3t2L30ljSPHHxDnR4fqVcBt/zR2zJAb9juavmQg=\n' > "$fixture/SOURCE/daemon/go.sum"
reject_at stale-module-lock daemon-mod-tidy
test -s "$fixture/out/sc-m01/daemon/mod-tidy.txt"

prepare formatting-drift
sed -i 's/func Run(args \[\]string, stdout, stderr io.Writer) int {/func Run(args []string,stdout,stderr io.Writer)int {/' "$fixture/SOURCE/daemon/internal/cli/cli.go"
reject_at formatting-drift daemon-gofmt

prepare vet-diagnostic
cat > "$fixture/SOURCE/daemon/internal/cli/vet_failure_test.go" <<'GO'
package cli

import "fmt"

func vetFixture() {
	fmt.Printf("%d", "not-an-integer")
}
GO
reject_at vet-diagnostic daemon-vet
test -s "$fixture/out/sc-m01/daemon/vet.txt"

prepare failing-unit
sed -i 's/{"default", nil, "Usage: wcpd"}/{"default", nil, "INTENTIONAL NEGATIVE FIXTURE"}/' "$fixture/SOURCE/daemon/internal/cli/cli_test.go"
reject_at failing-unit daemon-unit

prepare ignored-cli-exit
sed -i 's/os.Exit(cli.Run(os.Args\[1:\], os.Stdout, os.Stderr))/cli.Run(os.Args[1:], os.Stdout, os.Stderr)/' "$fixture/SOURCE/daemon/cmd/wcpd/main.go"
reject_at ignored-cli-exit daemon-smoke

test "$(wc -l < "$output/negative-gates.tsv")" -eq 7
cat "$output/negative-gates.tsv"
