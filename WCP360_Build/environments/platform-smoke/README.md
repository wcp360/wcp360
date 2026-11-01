# Native Debian and Ubuntu package probes

This disposable image tests the five profiles in [Core's target catalogue](../../../WCP360_Core/config/platforms/targets.json): Debian 12/13 and Ubuntu 22.04/24.04/26.04 LTS. It contains distribution PHP-FPM/Nginx packages and the standalone Go plan renderer. It does not install Composer dependencies, the Symfony application or a privileged broker.

The [workflow](../../../.github/workflows/core-executable-candidate.yml) generates its matrix from that catalogue, resolves each base image to a digest before building, and records native package versions. `fail-fast: false` keeps results for the other systems visible when one profile fails.

The probe requires:

- Exact OS identity and the expected native PHP series.
- Successful read-only Core preflight on native PHP 8.4, or its explicit refusal on native PHP 8.1/8.2/8.3/8.5.
- Actual Nginx/FPM configuration validation and HTTP through a private Unix socket.
- The expected non-root site UID and file owner, denied unrelated-user file access, hidden-file refusal and missing-script refusal.

An expected runtime refusal is a passing assertion about prerequisite enforcement. It is not a passing Core installation. See the [platform qualification guide](../../../documentation/development/SUPPORTED-PLATFORMS.md).

For an owned disposable Docker environment, from the repository root:

```sh
docker pull ubuntu:24.04
base_ref="$(docker image inspect --format '{{index .RepoDigests 0}}' ubuntu:24.04)"
docker build --build-arg "BASE_IMAGE=$base_ref" -f WCP360_Build/environments/platform-smoke/Dockerfile -t wcp360-platform-probe .
docker run --rm --network none --user 0:0 --pids-limit 128 --memory 512m --security-opt no-new-privileges -e WCP_ISOLATED_TEST_CONTAINER=1 -e WCP_TARGET_PROFILE=ubuntu-24.04 wcp360-platform-probe
```

Do not mount host services, customer storage or the Docker socket into the probe. Its root account creates synthetic users/files only inside the disposable container. The test-only `php-fpm` symlink selects the installed native package; production daemon executable allowlists and Debian 13 VM admission remain unchanged.
