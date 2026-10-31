# Executable Core development candidate

Status: experimental implementation on implementation/core-executable-foundation-20261031. The project owner's 2026-10-31 instruction starts work across the seven implementation stages. It does not constitute independent Engineering/Security acceptance, change an owner contract or qualify a stable release.

## What runs

A real Symfony 7.4 LTS application on PHP 8.4, Twig, AssetMapper, Turbo and Stimulus now supports local account enrollment, separate ADMIN/USER runtime contexts, login/logout, explicit tenant memberships, hosting-plan submissions, durable jobs, append-only audit occurrences, a Go plan builder and a detached reference plugin.

The application requires APP_ENV=dev/test and WCP_CANDIDATE=1. Production boot is refused. HTTP hosts are restricted to local development names. No demo password or embedded initial administrator is supplied.

The candidate uses deliberately separate wcp_candidate_* tables and /workbench routes. It does not claim to implement the canonical DB Model v1.0, the 86 accepted API operations or IPC-B. Its narrow permission candidate.hosting.plan is experimental and is not added to the 82-entry canonical permission registry.

## Seven-stage delivery status

| Stage | Concrete implementation | Remaining acceptance |
| :--- | :--- | :--- |
| Source audit | Audited immutable PHP candidate 284daabc8d57a65d3ad11e326baee32898e64254; 31 tests and source-fingerprint/registry checks pass. The 86 handlers are ports, not 86 completed features. | Review individual policies and concrete adapters before promotion. |
| Development environment | Composer/importmap locks, Debian 13 candidate Dockerfile, PostgreSQL 17 Compose, separate context processes/Unix IDs and secrets, PHP 8.4 and Go 1.27.3. | The first complete Linux workflow passes; this environment is not the addendum's pinned canonical offline qualification environment. |
| Symfony Core | Kernel, CLI, routes, dependency injection, migration, login/dashboard/job templates, responsive CSS, Turbo/Stimulus and AssetMapper. | Production packaging, immutable dependency/asset attestations and component design review. |
| Foundations | Sodium/Argon2id hashing, login throttling/CSRF, context-bound sessions with idle/absolute lifetime, tenant permissions, relational tenant FK, atomic enqueue/audit, idempotency, revocation recheck and interrupted-job handling. | Canonical IAM schema, MFA, entitlements, delegations, separate database grants/RLS, audit retention/export, fenced privileged execution and a full security review. |
| Hosting path | PHP submits a durable plan job; a worker invokes the fixed Go executable. A disposable Linux probe starts real Nginx/PHP-FPM and checks an HTTP PHP response, hidden-file denial and missing-PHP rejection. | Reviewed IPC-B/wcpd/wcp-privd implementation, real enrollment/provisioning, verified activation/reload, reconciliation and failure injection on the supported target. |
| SDK and plugin | Strict standalone summary decoder; detached report process; install/enable/disable/uninstall metadata; lifecycle and tenant-boundary tests, including disable/uninstall with an unavailable package; Core works without plugins. | Signed packages, installation storage, process supervision, scoped service credentials, revocation, compatibility and upgrade policy. |
| Installation and release | Non-root development installer, interactive account wizard, preflight, backup, migration upgrade and empty-database restore rehearsal; CI has read-only repository permissions and no publishing step. | Fresh-system installer, production recovery, migration review, complete functional/security acceptance, independent E3 and a reviewed release publisher. No stable version or latest pointer is produced. |

## Run with Docker Compose

Install Docker with Compose, then open a terminal at WCP360_Build/environments/core-candidate. Generate a private .env file with independent random hexadecimal values:

~~~sh
umask 077
for name in WCP_DEV_DB_PASSWORD WCP_USER_SECRET WCP_ADMIN_SECRET WCP_WORKER_SECRET WCP_SETUP_SECRET; do
  printf '%s=%s\n' "$name" "$(openssl rand -hex 32)"
done > .env

docker compose build
docker compose up -d database
docker compose run --rm setup php bin/console doctrine:migrations:migrate --no-interaction
docker compose run --rm setup
# Run enrollment again to create the separate ADMIN account.
docker compose run --rm setup
docker compose up -d user admin worker
~~~

Open http://user.localhost:8080 and http://admin.localhost:8081. Select the appropriate context in the CLI wizard. Choose a lowercase .test hostname to submit a plan. The worker records the generated configuration; it does not activate a website on the host.

The database volume persists candidate state. Runtime caches and browser sessions are ephemeral and separate per process. These development containers share candidate database credentials; they do not satisfy the production requirement for separate database grants. Credentials, .env files, generated dependencies/assets and database backups are ignored by Git.

## Native development

Require PHP 8.4 with the extensions checked by installer/preflight/candidate.php, Composer and Go 1.27.3. SQLite is allowed only for portable development/tests; PostgreSQL 17 is the control-plane target.

From WCP360_Core:

~~~sh
composer install
cp .env.example .env
# Replace APP_SECRET with a fresh random value before running.
mkdir -p var/bin
(cd agents/wcpd/candidate/planner && go build -trimpath -o ../../../../var/bin/wcp-plan ./cmd/wcp-plan)
php bin/console importmap:install
php bin/console doctrine:migrations:migrate --no-interaction
WCP_CONTEXT=setup php bin/console wcp:candidate:enroll
php -S 127.0.0.1:8080 -t public
~~~

Use a separate terminal with WCP_CONTEXT=worker php bin/console wcp:candidate:work to process one job. For ADMIN use a separate process with WCP_CONTEXT=admin, WCP_PUBLIC_HOST=admin.localhost and a different APP_SECRET. On Windows, build wcp-plan.exe and adjust WCP_PLANNER in the private .env.

For native frontend editing, rebuild assets after source changes; a compiled public/assets directory takes precedence over development mapping.

The Debian helper installer/recipes/candidate-install.sh automates dependencies, plan compilation, assets and migrations in a complete checkout. It refuses root and does not modify /etc, install services, fetch an unqualified release or enroll a default privileged account.

## Verification and recovery

~~~sh
composer test
php bin/console lint:container
php bin/console lint:yaml config
php bin/console lint:twig templates
php bin/console asset-map:compile
(cd agents/wcpd/candidate/planner && go test ./... && go vet ./...)
~~~

The GitHub workflow .github/workflows/core-executable-candidate.yml builds Debian 13, runs the application tests against PostgreSQL 17 and executes the isolated Nginx/PHP-FPM and backup/restore probes. The first complete run passed; see [PR #116](https://github.com/yyogas/wcp360/pull/116) for results bound to the latest candidate head. Actual outcomes belong in the linked workflow run; defining a test is not evidence that it passed.

The backup helpers accept only wcp360_workbench or wcp360_test. The restore helper accepts only an empty wcp360_restore_test database and a verified backup under Core var/backups. Supply PGHOST/PGUSER/PGPASSWORD securely; never put credentials in a command line or commit them. Stop all candidate workers before using wcp:candidate:work --reconcile-interrupted. This marks interrupted jobs UNKNOWN_OUTCOME and does not resubmit them.

## Trust boundaries

- Core never includes plugin code, runs hooks or alters its Composer graph during a plugin transition.
- A plugin summary is tenant-authorized data, not authority to call Linux services.
- Go accepts one bounded, exact JSON schema and generates fixed-path configurations from UUIDv7 IDs and ASCII .test names. It has no listener, filesystem effects or subprocess capability.
- The worker rechecks the original actor's current tenant permission and never changes a plan into a claim of physical activation.
- Audit persistence and logical mutation commit together. Database triggers reject audit UPDATE/DELETE.
- A password change, account deactivation, context mismatch or session expiry invalidates the relevant session. ADMIN MFA is still missing, so this candidate is deliberately prevented from production use.
- Source-audit checks, application tests, Linux probes and independent reviews are distinct forms of evidence. None substitutes for another.

~~~mermaid
flowchart LR
  B[USER browser] --> S[Symfony authentication and tenant permission]
  S --> T[Atomic plan + job + audit transaction]
  T --> W[Separate worker]
  W --> R[Recheck actor permission]
  R --> G[Fixed Go plan builder]
  G --> P[Nginx / PHP-FPM plan]
  P --> D[Durable result + audit]
  D --> B
  C[Disposable Linux probe] --> N[Real Nginx and isolated PHP-FPM]
  P -. reviewed test input .-> C
  D -. enabled and tenant-authorized summary .-> SDK[Standalone SDK]
  SDK --> X[Detached reference process]
~~~

## Promotion boundary

The eight accepted specification artifacts and WCP360-BM-0001 remain byte-identical. CODE-BASELINE stays NONE and existing limited-slice approvals are not reassigned to this candidate. Runtime changes remain on a draft implementation branch pending the applicable owner handoffs and genuine independent review. No E3, stable version, release checksum, signature or supported-production claim is fabricated.
