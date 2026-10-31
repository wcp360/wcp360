#!/usr/bin/env bash
# WCP360 1.0.0: release-layout scaffold, not a production installer.
set -euo pipefail

readonly RELEASE_VERSION='1.0.0'
readonly RELEASE_STATE='unreleased'
readonly EXIT_UNAVAILABLE=78

usage() {
    printf 'WCP360 %s installer template (%s)\n' "$RELEASE_VERSION" "$RELEASE_STATE"
    printf 'Usage: bash wcp360-installer.sh [--help | --version | --check]\n'
    printf 'Target: Debian 13 / amd64. This template performs no installation.\n'
}

if (( $# > 1 )); then
    usage >&2
    exit 64
fi

case "${1:-}" in
    --help|-h)
        usage
        exit 0
        ;;
    --version)
        printf 'wcp360-installer template %s (%s)\n' "$RELEASE_VERSION" "$RELEASE_STATE"
        exit 0
        ;;
    ''|--check)
        printf 'WCP360 %s is not available for installation.\n' "$RELEASE_VERSION" >&2
        printf 'The approved Core archive, production daemon and installer implementation are pending.\n' >&2
        printf 'See README-INSTALL.md. No system changes have been made.\n' >&2
        exit "$EXIT_UNAVAILABLE"
        ;;
    *)
        printf 'Unsupported option: %s\n' "$1" >&2
        usage >&2
        exit 64
        ;;
esac
