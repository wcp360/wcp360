# WCP360 Release Directory

**Status: distribution layout in preparation. No stable release is published by this scaffold.**

This directory reserves the versioned delivery structure for WCP360. The local `WCP360_Core/` directory is empty; a complete backend/frontend archive and a qualified production daemon cannot yet be produced from it.

## Current structure

```text
WCP360_Release/
├── README.md
├── v1.0.0/
│   ├── wcp360-installer.sh
│   └── README-INSTALL.md
├── v1.1.0/
│   ├── wcp360-installer.sh
│   └── README-INSTALL.md
└── latest -> v1.0.0/
```

Both versions are **unreleased**. `latest` points to the requested v1.0.0 directory as a workspace layout alias; it does not certify that version as stable.

## Reserved build outputs

| Version | Core package to produce | Production daemon to produce |
| :--- | :--- | :--- |
| 1.0.0 | `v1.0.0/wcp360-core-v1.0.0.tar.gz` | `v1.0.0/wcpd-debian13-amd64` |
| 1.1.0 | `v1.1.0/wcp360-core-v1.1.0.tar.gz` | `v1.1.0/wcpd-debian13-amd64` |

These outputs are absent. Empty files, documentation-only archives and inert binaries are not presented as complete release artifacts.

## Installer templates

Each version contains a guarded Bash template. `--help` and `--version` describe the scaffold; `--check` and invocation without arguments return 78. No installation or privileged operation is implemented.

- [v1.0.0 installation guide](v1.0.0/README-INSTALL.md)
- [v1.1.0 installation guide](v1.1.0/README-INSTALL.md)

## The latest link

Git stores `latest` as a relative symbolic link to `v1.0.0`. The local Windows workspace may use a directory junction for the same target when native symlink creation is unavailable. Windows checkout behavior depends on the available symbolic-link support.

The repository link reserves the requested directory layout. A distributed stable channel may point at v1.0.0 only after that target is qualified as the selected stable release. Channel updates must preserve existing immutable release directories.

## Release publication boundary

Release artifacts must be built from identified approved source revisions, checked for platform/contract compatibility, and accompanied by integrity/signature evidence, installation verification, and recovery instructions. Updating a directory name or link is not a release approval.

See [ARCHITECTURE.md](../ARCHITECTURE.md) and the [baseline manifest](https://github.com/yyogas/wcp360/blob/main/manifests/WCP360-BM-0001.md) for the current implementation scope.
