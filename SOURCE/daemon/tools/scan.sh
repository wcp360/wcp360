#!/bin/sh
set -eu
repo_root=$(CDPATH= cd -- "$(dirname -- "$0")/../../.." && pwd)
output="$repo_root/out/sc-m01/daemon"
export GOTOOLCHAIN=local GOPROXY=off GOSUMDB=sum.golang.org
export GOPRIVATE= GONOSUMDB= GOWORK=off CGO_ENABLED=0 GOOS=linux GOARCH=amd64
export GOCACHE=/tmp/wcp-go-build GOMODCACHE=/tmp/wcp-go-mod
export GOVULNDB=https://vuln.go.dev
trap 'result=$?; if [ "$result" -ne 0 ]; then printf "daemon-vulncheck\tFAIL\t%s\n" "$result" >> "$output/gates.tsv"; fi' EXIT
test "$(id -u)" = 10001
test "$(go version)" = 'go version go1.27.3 linux/amd64'
govulncheck -version > "$output/govulncheck-version.txt"
go version -m /opt/tools/govulncheck > "$output/govulncheck-build-info.txt"
grep -Eq 'golang.org/x/vuln[[:space:]]+v1\.9\.0' "$output/govulncheck-build-info.txt"
cd "$repo_root/SOURCE/daemon"
govulncheck -test ./... > "$output/vulnerability.txt" 2>&1
printf 'daemon-vulncheck\tPASS\t0\n' >> "$output/gates.tsv"
