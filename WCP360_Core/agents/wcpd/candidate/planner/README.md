# Inert hosting-plan builder

Development candidate, standard-library-only Go 1.27.3. Build `cmd/wcp-plan` into Core `var/bin`, which is ignored by Git. The planner reads one bounded JSON object on stdin and emits a versioned configuration document. Template rendering implements no network listener, database access, command execution, filesystem mutation or privileged operation.

This is not the accepted wcpd daemon, the IPC-B protocol or wcp-privd. The reviewed inert daemon and domain candidates retain their own acceptance authority; this rendering work does not transfer that evidence.

## Versioned profiles

| Profile | Entry point and selection | Output fields in their fixed order |
| :--- | :--- | :--- |
| Classic | `Render` or `RenderRuntime(..., "php_fpm", ...)`; the CLI default. | `schema="wcp360.candidate.plan/1"`, `nginx`, `php_fpm`. |
| Advanced | `RenderRuntime(..., "frankenphp_worker", true)`; explicit Worker acknowledgment. | `schema="wcp360.candidate.plan/2"`, `nginx`, `application_server="frankenphp_worker"`, `caddy`, `systemd_unit`. |
| Modern preview | Separate `RenderModern(..., true)` returning `ModernPlan`. | `schema="wcp360.candidate.plan/3"`, `web_stack="modern"`, `application_server="frankenphp_worker"`, `caddy`, `systemd_unit`. |

Original rendering functions, templates and wire shapes retain their existing meaning. In plan/2, Nginx remains the edge and Caddy listens through a per-site Unix socket. Plan/3 is a separate direct topology and never reinterprets a stored `frankenphp_worker` assignment.

## CLI contract

The fixed command is `wcp-plan plan`. Inputs require a UUIDv7 `site_id` and lowercase ASCII `.test` hostname, with the existing label and 253-byte hostname limits. Linux locations and Unix identities are derived from those validated values; callers cannot choose executable paths, commands, identities, service names or configuration paths.

Default Classic input:

```json
{"site_id":"019a3c3f-0000-7000-8000-000000000001","hostname":"classic.test"}
```

Existing Advanced input:

```json
{"site_id":"019a3c3f-0000-7000-8000-000000000001","hostname":"advanced.test","application_server":"frankenphp_worker","worker_acknowledged":true}
```

Explicit Modern preview input:

```json
{"site_id":"019a3c3f-0000-7000-8000-000000000001","hostname":"modern.test","web_stack":"modern","worker_acknowledged":true}
```

`web_stack` accepts only `modern` and is mutually exclusive with any `application_server` field, including `frankenphp_worker`. Modern requires the boolean `worker_acknowledged=true`. Without `web_stack`, the original default/application-server behavior remains unchanged. Unknown/duplicate/null/wrong-type fields, invalid selectors, trailing JSON, missing identities and inputs over 4,096 bytes are refused with no output. Invalid requests return code 2; output-write failures return code 1.

## Modern loopback boundary

The dedicated [Caddyfile](templates/Caddyfile-modern.tmpl) renders `http://{hostname}:8082` with `bind 127.0.0.1`, `admin off` and `auto_https off`. This is one-site HTTP text for an unreserved fixed port; the planner neither binds nor allocates that port. Concurrent multi-site ownership is not implemented. No Nginx or PHP-FPM configuration, public listener, TLS, ACME or HTTP/3 capability is provided by this preview.

The dedicated [systemd template](templates/frankenphp-modern.service.tmpl) retains per-site non-root identities, worker recycling and memory/task limits, an empty capability set and service hardening. Configuration lives under `/var/lib/wcp360-candidate/modern/{id}`; runtime/state use separate `wcp360-modern/{id}` paths. These locations are generated text, not installed files or a claim that the service can be activated.

## Core integration

The [hosting module](../../../../src/Module/Hosting/README.md) exposes separate ROOT browser `POST /workbench/settings/modern-preview` and API `POST /workbench/v1/web-stacks/modern-preview` paths. They require the existing Worker planning opt-in, current ROOT authorization, fresh authentication, exact Origin and their own CSRF token. MFA is optional; an enabled factor must complete before preview. The service generates a new synthetic UUIDv7 rather than accepting an existing resource selector and rechecks ROOT authority after rendering.

PHP invokes only the configured executable with an argv array, a scrubbed inherited environment, a five-second timeout and at most 65,536 bytes of stdout. Explicit Modern document validation shares the existing bounded JSON/string checks: exact ordered fields/markers, at most 393,216 JSON bytes, depth 16, at most 65,536 decoded string bytes, valid UTF-8, no binary controls and no empty/non-string values. Old plan/1/2 decoding rejects plan/3. Browser text is escaped; rendering failures return a generic unavailable response without child output or private exception details.

Preview writes no jobs, sites, defaults, activations or business audit. It does not change enums, durable selectors, queue processing or stored-plan exports. Modern remains unavailable in the durable hosting catalogue and global/site default selectors. Driver preview implementation does not establish service activation, multi-site isolation, installation admission or production qualification.

## Application-profile recipes

[RenderApplication](application.go) generates a separate `wcp360.candidate.application-plan/1` family. Input adds `application` (php, front_controller or wordpress) and one of six Nginx/Apache/Caddy × PHP-FPM/FrankenPHP-classic recipe IDs. These selectors conflict with application_server, web_stack and worker_acknowledged; recipe without application is refused. The ordered output has schema/application/recipe/web_server/application_server followed by only that recipe's artifacts. Existing plan/1–3 paths and shapes remain unchanged.

The [profile guide](../../../../src/Module/Hosting/APPLICATION-PROFILES.md) defines routing, ownership, native .htaccess boundaries and qualification. WordPress is a reference application. No persistent worker is implied. All profiles are inert, loopback-only and unreserved; no application, database, package, config or service is installed. Page caching stays disabled. The CLI never accepts caller-chosen paths, ports, executables or deployment flags. Source/template tests do not establish live Apache/Caddy/FrankenPHP/application correctness or performance.

## Verification

[Driver definitions](modern_test.go) and [CLI definitions](cmd/wcp-plan/main_modern_test.go) cover Modern markers/artifacts, acknowledgment, selector conflicts, malformed input, byte limits, injection, non-root isolation bounds and original output compatibility. Existing planner definitions continue to cover Classic and Advanced.

The configured CI runs `go test ./...` and `go vet ./...` and checks existing Nginx/PHP-FPM configurations in isolated Linux environments. Modern template text and application journeys require their own exact-source evidence; source-level assertions alone do not prove Caddy syntax acceptance, listener ownership, service activation or production correctness. Local executable tests remain stopped; CI results and outstanding qualification are reported separately in draft PR #116.
