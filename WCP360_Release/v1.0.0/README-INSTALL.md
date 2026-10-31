# WCP360 1.0.0 — Installation Guide

**Status: unreleased directory scaffold. Installation is unavailable.**

This directory reserves the distribution layout for WCP360 1.0.0. It is not a stable release or an installable panel.

## Package contents

| File | Current state |
| :--- | :--- |
| `wcp360-installer.sh` | Non-operational Bash template; stops before any system change. |
| `wcp360-core-v1.0.0.tar.gz` | Expected build output; absent until the approved backend/frontend package exists. |
| `wcpd-debian13-amd64` | Expected Linux/amd64 production build; absent. An inert CLI is not substituted for a production agent. |
| `README-INSTALL.md` | This guide. |

## Target environment

The planned platform is Debian 13 on amd64. PHP/Symfony, PostgreSQL, workers and agents follow the approved component and deployment contracts. This scaffold does not verify or provision prerequisites.

## Inspect the template

From this directory, on a machine with Bash:

```sh
bash ./wcp360-installer.sh --help
bash ./wcp360-installer.sh --version
bash ./wcp360-installer.sh --check
```

The availability check and invocation without arguments return **78** because this version is unreleased. These commands do not download artifacts, install packages, create users, write service configuration, or start services.

## Publication conditions

A distributable version requires the approved Core package and daemon, a reviewed installer, artifact integrity/signature verification, pinned compatibility, installation evidence, and documented recovery/rollback limits. Production installation commands belong here once that package is qualified.

See the [release directory guide](../README.md), [architecture](../../ARCHITECTURE.md), and [current baseline manifest](https://github.com/yyogas/wcp360/blob/main/documentation/manifests/WCP360-BM-0001.md).
