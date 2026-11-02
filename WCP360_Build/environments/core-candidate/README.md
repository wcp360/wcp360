# Executable Core candidate environment

Use the [complete run guide](../../../documentation/development/EXECUTABLE-CANDIDATE.md). This is a local development environment and integration harness, not a production deployment.

| Component | Candidate input |
| :--- | :--- |
| OS for this complete Core image | Debian 13 (trixie); Linux amd64 required for target preflight |
| PHP | 8.4.27 |
| Symfony | 7.4 LTS; exact component/dependency versions in Core composer.lock |
| Database | PostgreSQL 17; Compose volume persists development data |
| Planner | Go 1.27.3; standard library only |
| Frontend | Twig, AssetMapper, Turbo 8.0.23, Stimulus 3.2.2 |
| Redis | Not required by this narrow durable PostgreSQL job queue |
| Contexts | Separate USER, ADMIN, worker and interactive setup processes; distinct Unix IDs and application secrets |

Generate the five private .env values described by the guide, build the image, start the database, migrate, enroll accounts and start the runtimes. Only loopback browser ports are published. Docker never receives a host Docker socket, host filesystem roots, service directories or production credentials.

Core does not require an optional package to boot. The image includes only the explicit development reference package. It has no production marketplace fetcher, privileged broker or signed release installer.

Dependencies and compiled assets are assembled during image build. The Go executable lives outside the ephemeral var mount. Runtime cache/session state is ephemeral, separate from the database. No secret .env, local vendor tree or generated workspace output enters the build context.

The disposable CI probe starts real Nginx and PHP-FPM as an isolated test inside the container, with one generated fixture identity and Unix socket. It creates no host services. The application itself only generates a plan and never marks a server website active.

WCP360's declared targets also include Debian 12 and Ubuntu 22.04, 24.04 and 26.04 LTS. The separate `platform-smoke` environment and five-profile CI matrix use each distribution's native Nginx/PHP-FPM packages to check exact OS detection, HTTP execution identity and file boundaries. They report Core's PHP 8.4 prerequisite refusal on the other native PHP streams. This Debian 13 image remains the complete Symfony/PHP 8.4/PostgreSQL 17 candidate; those package probes neither reproduce it on all targets nor qualify their systemd/reboot behavior. See the [platform guide](../../../documentation/development/SUPPORTED-PLATFORMS.md) and exact workflow outcomes.

Stable qualification still needs immutable image digests, production database grants, full IAM/MFA and canonical schema/IPC adapters, independent review, clean-install/upgrade/recovery tests and complete security acceptance. This image does not replace the pinned canonical inert-daemon qualification environment.
