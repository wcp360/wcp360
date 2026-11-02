# Core runtime verification on Debian and Ubuntu

This disposable development image runs the locked Symfony Core with **the reviewed PHP 8.4 build compiled from upstream source inside each selected target distribution**: Debian 12/13 and Ubuntu 22.04/24.04/26.04 LTS. It complements the [native package probe](../platform-smoke/README.md), which intentionally exercises each distribution's own PHP family.

The shared inputs are [runtime-providers.json](../../../WCP360_Core/config/platforms/runtime-providers.json) and [targets.json](../../../WCP360_Core/config/platforms/targets.json). The workflow supplies the reviewed PHP version and SHA-256 as mandatory build arguments and resolves the selected base image to its registry digest. A build refuses a missing version/hash, rejects PHP series other than 8.4, checks the downloaded archive before extraction, and verifies the installed version and required extensions.

## Runtime ownership and boundaries

| Component | Provider | Verification boundary |
| :--- | :--- | :--- |
| Target userspace | The selected Debian or Ubuntu image, resolved to a digest | Actual target `/etc/os-release`, native libraries and native Nginx packages. |
| PHP CLI and FPM | Upstream PHP source at the reviewed version/hash; compiled in that userspace | Fixed prefix `/opt/wcp360/runtime/php/8.4`; no replacement of the target's system PHP packages. |
| Symfony and tests | Committed Composer lockfile | The same application dependencies across all five targets. |
| PostgreSQL 17 | An isolated `postgres:17-trixie` verification service, resolved to a digest | Real PostgreSQL 17 application integration; this does not test native PostgreSQL packages on the PHP container's distribution. |
| Go planner | Pinned Go toolchain; static `CGO_ENABLED=0` build | Pure configuration generation only; no privileged broker or systemd admission. |

PHP is compiled against each target's own libc, OpenSSL, ICU, libxml, libpq and other libraries. Copying a Debian PHP binary into an Ubuntu image would not provide this evidence. The build follows the hardening/configuration pattern of the [Docker Official PHP image recipe](https://github.com/docker-library/php/blob/master/8.4/trixie/fpm/Dockerfile), with CLI/FPM, the Core extensions, SQLite test support and XML modules needed by PHPUnit. [PHP's compilation requirements](https://www.php.net/manual/en/install.unix.source.php) and [FPM options](https://www.php.net/manual/en/install.fpm.install.php) define the upstream build interface.

The upstream [PHP source download page](https://www.php.net/downloads.php?source=Y) publishes source archive hashes and detached signatures. The shared manifest records the reviewed archive version and SHA-256 in one place. This development build checks that hash over HTTPS; it does not claim a detached-signature verification step. Any version/hash change requires updating the manifest and rerunning the target matrix. A fixed source input is not a claim of byte-for-byte reproducibility: native dependency repositories still receive updates, and exact package/image revisions belong in execution evidence.

## Running the verification

From the repository root, using an owned disposable Docker environment and `jq`, read `php.version` and `php.sha256` from the manifest and pass them explicitly:

```sh
reviewed_php_version="$(jq -er '.php.version' WCP360_Core/config/platforms/runtime-providers.json)"
reviewed_php_sha256="$(jq -er '.php.sha256' WCP360_Core/config/platforms/runtime-providers.json)"
docker pull ubuntu:24.04
base_ref="$(docker image inspect --format '{{index .RepoDigests 0}}' ubuntu:24.04)"
docker build \
  --build-arg "BASE_IMAGE=$base_ref" \
  --build-arg "PHP_VERSION=$reviewed_php_version" \
  --build-arg "PHP_SHA256=$reviewed_php_sha256" \
  -f WCP360_Build/environments/platform-core/Dockerfile \
  -t wcp360-platform-core .
```

The [candidate workflow](../../../.github/workflows/core-executable-candidate.yml) owns the PostgreSQL service, database credentials, target identity, test invocation and cleanup. The [verification script](../../verification/candidate-platform-core.sh) requires that disposable context. The image defaults to UID/GID `10001:10001`; its trusted Core/SDK/reference-plugin checkout belongs to that account so the candidate installation helper can prepare its environment, dependencies, assets and Go outputs without root. The installed PHP/Go toolchains remain root-owned, and Composer/Go caches stay inside Core's `var` directory with `GOTOOLCHAIN=local`. This writable checkout is specific to the development harness; it is not a production deployment permission model. Never mount production data, host services or the Docker socket into it.

This is an executable development provider, **not a production host installer**. It neither chooses a production PHP package provider nor adds a PPA/third-party APT trust key. It does not install PostgreSQL onto each target host, qualify VM restart/reboot, activate privileged daemons or establish production support. Those outcomes require separate install, service, recovery and security evidence described in the [platform qualification guide](../../../documentation/development/SUPPORTED-PLATFORMS.md). A successful native PHP probe or pending Core job cannot be reported as a successful Core runtime check.
