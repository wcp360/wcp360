# Target operating systems and qualification

WCP360 targets **Debian 12 and 13**, and **Ubuntu 22.04, 24.04 and 26.04 LTS**. The executable candidate distinguishes an operating-system target from an executed package probe, a complete Core installation and production qualification. Declaring a target does not establish the other three.

The machine-readable source is [Core's target catalogue](../../WCP360_Core/config/platforms/targets.json), schema `wcp360.target-platforms/1`. The candidate platform parser and read-only preflight consume this catalogue. Unsupported releases and derivatives are not admitted merely because they use APT or declare `ID_LIKE=debian`.

From the repository root, `php WCP360_Core/installer/preflight/platform.php` performs only actual OS identification, without Composer/Symfony, and reports `runtime_validation: not_checked`. `php WCP360_Core/installer/preflight/candidate.php --target --dependencies` separately checks the candidate's PHP version, required extensions, dependency presence and target architecture. It does not connect to PostgreSQL or verify its server version/service health. Neither command changes the host or accepts a simulated OS override.

## Distribution packages and the Core runtime

These are native distribution package families, not frozen patch revisions. Security updates must remain enabled and exact installed revisions belong in execution evidence.

| Target ID | Operating system | Codename | Native PHP-FPM | Native PostgreSQL | Core PHP 8.4 requirement |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `debian-12` | Debian 12 | bookworm | 8.2 | 15 | Supply a separately qualified PHP 8.4 runtime. |
| `debian-13` | Debian 13 | trixie | 8.4 | 17 | Native package family matches; extensions and exact revisions still need verification. |
| `ubuntu-22.04` | Ubuntu 22.04 LTS | jammy | 8.1 | 14 | Supply a separately qualified PHP 8.4 runtime. |
| `ubuntu-24.04` | Ubuntu 24.04 LTS | noble | 8.3 | 16 | Supply a separately qualified PHP 8.4 runtime. |
| `ubuntu-26.04` | Ubuntu 26.04 LTS | resolute | 8.5 | 18 | Supply a separately qualified PHP 8.4 runtime; native PHP 8.5 is outside the current Core constraint. |

Core currently requires PHP `~8.4.0`. Installing an unversioned `php-fpm` package on each target does not produce the same runtime. The platform matrix deliberately uses the native PHP-FPM family to exercise hosted-runtime configuration and reports the Core prerequisite refusal where PHP 8.4 is absent. It does not lower or widen the Core constraint to make a target appear compatible.

Control-plane data targets PostgreSQL 17. Outside Debian 13, use an explicitly qualified PostgreSQL 17 package provider, such as the upstream PostgreSQL APT repository for the correct distribution suite, or a separately managed PostgreSQL 17 service. The native `postgresql` metapackage is insufficient evidence of the required major version. Hosted customer databases may use other engines or versions through their own reviewed adapters.

No candidate preflight or package probe automatically adds a PPA, changes repository trust, substitutes a different distribution's suite or upgrades an existing database. A runtime-provider decision must identify package/image provenance, signing trust, extensions, update ownership, service paths, isolation and tested rollback. An Ubuntu PPA and a Debian package repository are separate providers; neither is assumed to cover every target release.

Primary package references: [Debian 12 PHP](https://packages.debian.org/bookworm/php-fpm), [Debian 13 PHP](https://packages.debian.org/trixie/php-fpm), [Ubuntu PHP families](https://packages.ubuntu.com/search?keywords=php8), [Debian 12 PostgreSQL](https://packages.debian.org/bookworm/postgresql), [Ubuntu PostgreSQL families](https://packages.ubuntu.com/postgresql), and upstream [Debian](https://www.postgresql.org/download/linux/debian/) / [Ubuntu](https://www.postgresql.org/download/linux/ubuntu/) PostgreSQL packages. Third-party PHP provider context: [maintainer repositories](https://deb.sury.org/) and [Ubuntu PHP PPA](https://launchpad.net/~ondrej/+archive/ubuntu/php).

## What each verification layer establishes

| Layer | Scope | Evidence boundary |
| :--- | :--- | :--- |
| Declared target | Exact IDs/releases in the shared catalogue. | A compatibility target, without an installation or security claim. |
| Native package matrix | Five disposable distribution profiles; exact OS recognition, read-only runtime preflight and real Nginx/PHP-FPM HTTP, execution-UID and file-boundary probes. | Configuration and probe results for the exact container image/source revision. Workflow configuration alone is pending execution. |
| Complete Core candidate | PHP 8.4/Symfony, PostgreSQL 17, PHPUnit, PHPStan, Panther, jobs and the selected hosting integrations. | The existing Debian 13 candidate environment; no inference that Symfony ran on all five native PHP streams. |
| Privileged lifecycle and reboot | Go broker/agent, explicit disposable-VM admission, systemd restart, crash refusal/recovery and same-guest reboot. | The existing Debian 13 VM profile only. Its restrictive guards remain in place. |
| Production qualification | Assembled artifacts, fresh install, upgrades, recovery, security acceptance and independent review on each supported combination. | No target currently has a qualified WCP360 production release. |

The five-profile package matrix does not start the privileged candidate broker or add systemd/VM admission to Ubuntu or Debian 12. Extending those paths requires reviewed runtime providers, distribution adapters and real VM evidence. The FrankenPHP Worker probe also remains a separate PHP 8.4 ZTS image test; it does not qualify a native worker installation on all five systems.

For execution outcomes, inspect the exact source SHA and jobs linked from [PR #116](https://github.com/yyogas/wcp360/pull/116). A successful Debian 13 run cannot qualify another operating system, and a pending or failed profile is never recorded as passing.

## Installer and service adaptation

Read `/etc/os-release`, falling back to `/usr/lib/os-release` only when the former is absent. Admission uses the exact supported `ID` and `VERSION_ID`; human-readable names and `ID_LIKE` are not substitutes. Parse these files as data without executing their contents. [Operating-system identification specification](https://www.freedesktop.org/software/systemd/man/latest/os-release.html)

Adapters must discover or select versioned PHP binaries, FPM units, pool/configuration directories, sockets, extensions and execution identities. They must check package-manager state, incompatible services and occupied listeners before changing a machine. Keep generated Nginx/FPM configuration under the owning adapter rather than copying a Debian 13 path into every target.

systemd families differ across these releases: Debian 12 uses 252, Debian 13 uses 257, Ubuntu 22.04 uses 249, Ubuntu 24.04 uses 255 and Ubuntu 26.04 uses 259. Validate units with the target's own tools and test effective identity, capabilities, AppArmor restrictions, child supervision and recovery in that target's VM. A unit accepted on a newer systemd version does not establish equivalent behavior on the oldest target. [Debian release notes](https://www.debian.org/releases/trixie/release-notes/whats-new.en.html), [Ubuntu 22.04 package files](https://packages.ubuntu.com/jammy/systemd), [Ubuntu 24.04 systemd](https://packages.ubuntu.com/noble-updates/systemd), [Ubuntu 26.04 systemd](https://packages.ubuntu.com/resolute/systemd)

## Distribution lifecycle

Ubuntu 26.04 LTS was released on 23 April 2026 and is included as an existing target. Ubuntu's LTS release lifecycle is separate from WCP360 qualification and from coverage for optional third-party packages. Debian 12 is in its LTS period, with package/architecture coverage to verify for each assembled release. [Ubuntu 26.04 release notes](https://documentation.ubuntu.com/release-notes/26.04/), [Ubuntu lifecycle](https://ubuntu.com/about/release-cycle), [Debian 12 lifecycle](https://www.debian.org/releases/bookworm/), [Debian 13 lifecycle](https://www.debian.org/releases/trixie/)

AlmaLinux and other distributions are outside this five-target matrix. Their future adapters require an explicit version scope, package provenance and their own qualification; Debian-family detection must not admit them accidentally.
