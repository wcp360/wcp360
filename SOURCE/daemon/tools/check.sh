#!/bin/sh
# Run only inside the approved unprivileged Debian builder; outputs are evidence.
set -eu
repo_root=$(CDPATH= cd -- "$(dirname -- "$0")/../../.." && pwd)
cd "$repo_root"
output="$repo_root/out/sc-m01/daemon"
mkdir -p "$output"
export GOTOOLCHAIN=local GOPROXY=off GOSUMDB=sum.golang.org
export GOPRIVATE= GONOSUMDB= GOWORK=off CGO_ENABLED=0 GOOS=linux GOARCH=amd64
export GOCACHE=/tmp/wcp-go-build GOMODCACHE=/tmp/wcp-go-mod
gate=daemon-toolchain
trap 'result=$?; if [ "$result" -ne 0 ]; then printf "%s\tFAIL\t%s\n" "$gate" "$result" >> "$output/gates.tsv"; fi' EXIT
: > "$output/gates.tsv"
pass() { printf '%s\tPASS\t0\n' "$gate" >> "$output/gates.tsv"; }
test "$(id -u)" = 10001
test "$(id -g)" = 10001
test "$(uname -m)" = x86_64
test "$(cat /etc/debian_version)" = 13.7
test "$(go version)" = 'go version go1.27.3 linux/amd64'
{
    go version
    go env GOOS GOARCH CGO_ENABLED GOTOOLCHAIN GOPROXY GOSUMDB
    cat /etc/os-release
    id
} > "$output/environment.txt"
pass

gate=daemon-no-third-party-runtime-deps
test "$(go -C SOURCE/daemon list -m all)" = github.com/yyogas/wcp360/daemon
# No workspace/local replacements or undeclared runtime modules.
test ! -e SOURCE/daemon/go.work
test ! -e go.work
if grep -Eq '^[[:space:]]*(require|replace|exclude)[[:space:](]' SOURCE/daemon/go.mod; then
    echo "Unexpected dependency directive" >&2
    exit 1
fi
go -C SOURCE/daemon list -m all > "$output/modules.txt"
pass

gate=daemon-mod-tidy
go -C SOURCE/daemon mod tidy -diff > "$output/mod-tidy.txt" 2>&1
test ! -s "$output/mod-tidy.txt"
pass
gate=daemon-mod-verify
go -C SOURCE/daemon mod download
go -C SOURCE/daemon mod verify > "$output/mod-verify.txt" 2>&1
pass

gate=daemon-gofmt
gofmt -l SOURCE/daemon > "$output/gofmt.txt"
test ! -s "$output/gofmt.txt"
pass
gate=daemon-vet
go -C SOURCE/daemon vet -mod=readonly ./... > "$output/vet.txt" 2>&1
pass
gate=daemon-unit
go -C SOURCE/daemon test -mod=readonly -count=1 -json \
    -coverprofile=../../out/sc-m01/daemon/coverage.out ./... > "$output/unit.jsonl" 2>&1
pass

gate=daemon-inert-import-boundary
# Production packages may import only the CLI boundary, io and os. This gate is
# deliberately narrow and must be explicitly changed when another slice opens.
go -C SOURCE/daemon list -f '{{join .Imports "\n"}}' ./cmd/wcpd ./internal/cli > "$output/imports.txt"
while IFS= read -r imported; do
    case "$imported" in
        ''|io|os|github.com/yyogas/wcp360/daemon/internal/cli) ;;
        *) printf 'Unexpected production import: %s\n' "$imported" >&2; exit 1 ;;
    esac
done < "$output/imports.txt"
pass

gate=daemon-build
go -C SOURCE/daemon build -mod=readonly -trimpath -buildvcs=true \
    -o ../../out/sc-m01/daemon/wcpd ./cmd/wcpd
go version -m "$output/wcpd" > "$output/binary-build-info.txt"
sha256sum "$output/wcpd" > "$output/binary.sha256"
pass

gate=daemon-smoke
sh SOURCE/daemon/tools/check-cli.sh
pass

gate=daemon-reproducible-build
GOCACHE=/tmp/wcp-go-build-fresh go -C SOURCE/daemon build \
    -mod=readonly -trimpath -buildvcs=true \
    -o ../../out/sc-m01/daemon/wcpd-rebuilt ./cmd/wcpd
cmp "$output/wcpd" "$output/wcpd-rebuilt"
pass

gate=daemon-evidence
test -s "$output/unit.jsonl"
test -s "$output/coverage.out"
test -s "$output/binary.sha256"
pass
printf 'Offline foundation gates complete; vulnerability scan is a separate gate.\n'
