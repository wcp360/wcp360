# Installation preflight

**Status: candidate read-only checks are implemented; they perform no installation or host mutation.**

Read-only discovery and validation before selecting or applying an installation plan.

From the repository root:

~~~sh
php WCP360_Core/installer/preflight/platform.php
php WCP360_Core/installer/preflight/candidate.php --target --dependencies
php WCP360_Core/installer/preflight/runtime-plan.php
php WCP360_Core/installer/preflight/runtime-readiness.php
php WCP360_Core/installer/preflight/native-plan.php
~~~

`platform.php` reads actual OS identity without Composer or Symfony and admits the exact Debian 12/13 and Ubuntu 22.04/24.04/26.04 LTS targets from [the shared catalogue](../../config/platforms/targets.json). It reports `runtime_validation: not_checked`; recognizing an OS is not a complete readiness result. Unknown releases and derivatives are rejected rather than inferred from `ID_LIKE`.

`candidate.php --target --dependencies` checks PHP 8.4, required extensions, dependency presence, exact target identity and amd64 architecture. It does not connect to a database or verify its server version/service health. Native PHP 8.2 on Debian 12, 8.1 on Ubuntu 22.04, 8.3 on Ubuntu 24.04 and 8.5 on Ubuntu 26.04 do not satisfy Core's current `~8.4.0` constraint. Supply a separately qualified PHP 8.4 runtime and PostgreSQL 17 provider/service where native defaults differ; these checks never add repositories or replace packages.

`runtime-plan.php` validates the [shared runtime-provider manifest](../../config/platforms/runtime-providers.json) and describes the selected development source-build profile for the actual target. It requires PHP 8.1 or newer, loads neither Composer nor Symfony, changes no files/services and leaves runtime validation unchecked. The fixed PHP CLI/FPM paths are planned development paths, not a claim that they exist or have a system service. Unsupported targets return a failing result.

`runtime-readiness.php` checks the executing PHP 8.4 process and required extensions, then connects to the explicit process `DATABASE_URL` using PDO with bounded timeouts and read-only session settings. It executes only `SHOW server_version_num`; the observed server must be PostgreSQL 17. The URL's `serverVersion` hint cannot establish that result. It deliberately loads no `.env`, PHPUnit bootstrap or test URL override. Missing/invalid connection settings, connection failure, the wrong major version or a failed PHP prerequisite produce a failing JSON report without credentials, DSNs or raw database errors. A successful report still sets `qualified_release: false`.

The installer exposes runtime planning as `bash WCP360_Core/installer/recipes/candidate-install.sh --plan`, runtime readiness as `--check-runtime` and the native-installation handoff below as `--host-check`. These options return before checkout preparation, journaling or migrations. See the [runtime matrix guide](../../../documentation/development/CORE-RUNTIME-MATRIX.md) for the corresponding five-target development image, application journey and evidence requirements.

## Read-only native-installation handoff

[native-plan.php](native-plan.php) combines live observations from [LinuxHostInventory](../../src/Shared/Platform/LinuxHostInventory.php) with the [Setup native-installation plan](../../src/Module/Setup/Application/NativeInstallationPlan.php). It works with PHP 8.1 or newer without Composer, Symfony, `.env`, database access, subprocesses or package/service commands.

Optional requirements are explicit inputs to this diagnostic. For example:

~~~sh
php WCP360_Core/installer/preflight/native-plan.php --tcp=80,443 --udp=53 --memory-mib=2048 --disk-mib=10240
~~~

These numbers are an example request, not accepted production ports or minimum sizing. `--memory-mib` compares the requested threshold with **available memory**, not total installed RAM; `--disk-mib` compares it with free space on the current `/var` filesystem. One MiB is 1,048,576 bytes. No requirements are assumed: omitted port or capacity requirements remain `not_checked` and block installation admission. The wrapper's `--host-check` passes no requirements; use the standalone command to supply them.

Use `--tcp=none` or `--udp=none` to explicitly request no ports for that protocol. This differs from omitting the option: its requested-port check passes with an empty requirement, while the independently reported socket-table observation can still be unknown. Options accept no duplicate flag, duplicate port, port range or arbitrary command.

| Observation | Scope and limit |
| :--- | :--- |
| OS and architecture | Exact shared five-target catalogue and actual architecture; no derivative/version inference. |
| Memory and disk | Visible total/available Linux memory and total/free space on the `/var` filesystem at observation time. Capacity checks use available memory and free disk space. Cgroup limits and capacity on other filesystems are not checked; these values neither establish an effective container budget nor reserve capacity. |
| TCP/UDP ports | Bounded IPv4/IPv6 socket-table reads in the current network namespace. This does not identify a service, inspect another namespace or prove a future bind will succeed. Unreadable or incomplete observations remain unknown. |
| Managed paths | Observe `/usr/lib/wcp360`, `/var/lib/wcp360`, `/run/wcp360` and `/var/cache/wcp360`. Existing objects require fresh-install review; the report grants no overwrite, ownership or repair permission. |
| Identity and systemd | Observe the process effective UID and `/run/systemd/system` marker. Neither establishes privileged authorization, a running service manager or service health. |
| Runtime provider | Includes the existing development runtime plan as reference. Source-built PHP and isolated PostgreSQL verification are not a qualified native provider or installed service. |

The report explicitly keeps native application blocked: `native_apply_allowed: false`, `host_changes: false` and `qualified_release: false`. Artifact trust, native provider qualification, registered host effects and the remaining host checks need their own implementation and evidence. No listener result establishes service ownership or readiness, and no preflight qualifies hostname/DNS/time or the full host topology by implication.

`checks` distinguishes a satisfied observation (`pass`), a known refusal such as an occupied requested port or insufficient capacity (`fail`), unavailable evidence (`unknown`), an omitted/unimplemented check (`not_checked`) and unavailable production boundaries (`blocked`). `blockers` includes every check that is not `pass`. Passing the implemented checks never removes the native-provider, artifact-trust or registered-effect blockers.

The command always exits **78** while native application is blocked, including when its implemented observations pass. Invalid arguments exit **64**; an unexpected collection/planning failure exits **1** with a redacted message. There is no `--apply` mode. A future installer must reobserve and authorize its concrete effects; this diagnostic is neither a privilege grant nor a substitute for live checks at mutation time.

## Offline candidate artifact verification

[artifact.php](artifact.php) verifies a local candidate payload through [CandidateArtifactVerifier](../../src/Module/Setup/Application/CandidateArtifactVerifier.php) and the bounded file reader [ArtifactInput](../../src/Module/Setup/Infrastructure/ArtifactInput.php). It requires PHP 8.1 or newer with sodium and works before Composer installation. It loads no `.env`, accesses no database, starts no subprocess and changes no host files. It neither downloads nor extracts or installs a package.

All seven options are required exactly once. Supply absolute paths within private operator-controlled staging, an independently pinned public key and the expected artifact identity:

~~~sh
php WCP360_Core/installer/preflight/artifact.php \
  --artifact=/absolute/staging/payload \
  --manifest=/absolute/staging/manifest.json \
  --signature=/absolute/staging/manifest.sig \
  --public-key=REPLACE_WITH_64_LOWERCASE_HEXADECIMAL_CHARACTERS \
  --component=core \
  --version=1.0.0-rc.1 \
  --target=debian-13
~~~

The public-key placeholder must be replaced with the operator's Ed25519 public key obtained through an independent trusted channel. No official publisher key or private signing material is supplied. A key delivered alongside an untrusted payload does not establish its publisher. `--component` accepts `core`, `wcpd` or `wcp-privd`; `--target` accepts the exact shared catalogue IDs `debian-12`, `debian-13`, `ubuntu-22.04`, `ubuntu-24.04` and `ubuntu-26.04`. The expected target binds artifact identity; the command does not establish compatibility with the executing host.

The candidate manifest is compact UTF-8 JSON followed by one LF, limited to 16 KiB, with exactly these seven keys:

| Key | Required value |
| :--- | :--- |
| `schema` | `wcp360.candidate-artifact/1` |
| `component` | The expected `core`, `wcpd` or `wcp-privd` component. |
| `version` | The exact expected candidate version, for example `1.0.0-rc.1`. This example does not name a published release. |
| `target` | The exact expected shared target catalogue ID. |
| `architecture` | `amd64` |
| `size` | Positive integer payload length, at most 2,147,483,648 bytes. |
| `sha256` | The expected payload SHA-256, encoded as 64 lowercase hexadecimal characters. |

Its bytes must match PHP's `json_encode($metadata, JSON_UNESCAPED_SLASHES)."\n"` representation. Whitespace, a BOM, duplicate keys, escaped alternatives and additional line endings are refused even when signed. This is a bounded candidate encoding, not a claim of JSON Canonicalization Scheme compliance. Versions use three decimal components without leading zeros and an optional dot-separated prerelease suffix, up to 64 characters; build metadata with `+` is not accepted.

No path, URL, key or command field is accepted. The separate signature file contains exactly 64 raw bytes: an ordinary Ed25519 detached signature over the exact manifest bytes, including its final LF. This is not Ed25519ph or an ASCII/base64 signature. Signature verification occurs before JSON decoding; payload length and SHA-256 are then checked by reading the local payload. The file reader refuses symbolic links, non-regular files and multiple hard links rather than reading arbitrary streams. On Linux it also checks file/ancestor ownership and write permissions, including protection of the immediate staging directory. Windows execution does not establish Linux permissions or Windows ACL protection.

Exit **0** reports `candidate_verified: true` for the bytes observed during this invocation. A verification refusal, unavailable sodium or unreadable input exits **1** with a fixed redacted JSON result. Malformed arguments exit **64** with fixed usage. Reports retain `native_apply_allowed: false` and `qualified_release: false`. The checkout shell wrapper has no artifact-verification option; invoke this standalone command directly.

This candidate format and operator-pinned verification primitive do not select the official release signing scheme, publisher/key distribution, revocation, freshness/rollback policy, provenance or package-content/permission checks. They do not make later reuse of a staging path safe: an eventual installer must consume the same verified immutable bytes through its accepted staging and execution boundary. The native plan's `artifact_identity_and_trust` blocker remains in place. [Release trust and qualification requirements](../../../documentation/development/DELIVERY-AND-RELEASES.md)

The five-platform native Nginx/PHP-FPM matrix tests native package behavior; the separate source-runtime matrix exercises PHP 8.4 Core application behavior within those userspaces. The privileged systemd/reboot VM remains Debian 13-specific. Neither matrix qualifies a production host installer. An expected prerequisite refusal is not permission to run Symfony on an unsupported PHP version. [Platform and evidence boundaries](../../../documentation/development/SUPPORTED-PLATFORMS.md)

Setup business state and the restricted wizard remain in the [Setup module](../../src/Module/Setup/README.md). Service configuration text has one source in [resources/services](../../resources/services/README.md); installer code consumes the accepted boundaries rather than creating a second generic system executor.

- [Installer source map](../README.md)
- [Installation and upgrade verification](../../tests/Installer/README.md)
- [Release and recovery acceptance](../../../documentation/development/DELIVERY-AND-RELEASES.md)
