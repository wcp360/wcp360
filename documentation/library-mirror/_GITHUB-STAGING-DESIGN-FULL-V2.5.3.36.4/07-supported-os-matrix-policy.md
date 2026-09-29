# Supported OS Matrix Policy

## Principle

Do not claim “Linux support” generically.

Support must be versioned by:
- distribution;
- major version;
- architecture;
- init system;
- firewall framework;
- package manager;
- PHP/runtime availability;
- DB/mail service compatibility.

## Initial baseline candidates

The specification should target a conservative server baseline such as:
- Debian stable;
- Ubuntu LTS.

Additional distributions must be added only after adapter verification.

## Architecture

Initial target:
- `x86_64/amd64`.

Optional later:
- `arm64/aarch64`.

## Mandatory capabilities

- systemd or explicitly supported init abstraction;
- modern kernel;
- Unix domain sockets;
- nftables or supported firewall adapter;
- supported PHP/runtime packages;
- supported database engine packages.

## Capability discovery

Installer detects:
- distro ID/version;
- architecture;
- package manager;
- active firewall engine;
- existing web/mail/db services;
- ports in use;
- conflicting panels;
- filesystem space;
- hostname/FQDN;
- DNS/reverse DNS observability where applicable.

## Unsupported state

Installer must fail safely rather than continue on an unknown OS profile.