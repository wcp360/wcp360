# Reusable PHP application profiles

The Core separates the application's routing needs from its web server and PHP runtime. WordPress is a reference case, not the definition of the engine. The same pipeline renders ordinary PHP, front-controller frameworks and compatible CMS applications without duplicating six application installers.

## Current implementation

The [catalogue](../../Shared/Platform/ApplicationRecipeCatalogue.php), [Go renderer](../../../agents/wcpd/candidate/planner/application.go) and strict [document](Domain/ApplicationPlanDocument.php) implement inert configuration previews. They do not install an application, database, package or service, reserve listeners, create a durable site/job, enable native activation or establish production compatibility.

| Recipe | Web layer | PHP runtime | Native .htaccess in this recipe |
| :--- | :--- | :--- | :--- |
| nginx_php_fpm | Nginx | Per-site PHP-FPM pool | No; managed server rules |
| nginx_frankenphp | Nginx to private HTTP Unix socket | FrankenPHP classic | No; managed server rules |
| apache_php_fpm | Apache event target | Per-site PHP-FPM pool | Restricted rewrite directives |
| apache_frankenphp | Apache HTTP proxy to private Unix socket | FrankenPHP classic | No; proxy bypasses local filesystem handling |
| caddy_php_fpm | Caddy | Per-site PHP-FPM pool | No; managed server rules |
| caddy_frankenphp | Caddy embedded in FrankenPHP | FrankenPHP classic, direct | No; managed server rules |

Profiles are `php` (existing scripts/directory indexes, no general fallback), `front_controller` (index.php routing, for suitably installed Symfony/Laravel and similar applications) and `wordpress` (a front-controller reference case). Framework dependencies, PHP extensions, database needs, writable directories and application-specific security remain separate acceptance requirements. No arbitrary CMS/plugin compatibility is inferred.

FrankenPHP classic serves requests without a persistent application worker. The existing generic Worker plan/2 and Modern preview/plan/3 keep their semantics and compatibility acknowledgement; these profiles do not silently convert an application to Worker mode. Future Worker adapters require explicit application integration, state-reset, isolation, recycling and endurance evidence.

## Interfaces and boundaries

ROOT settings offer a shared form. `GET /workbench/v1/application/recipes` returns the fixed descriptions. Browser `POST /workbench/settings/application-preview` and JSON `POST /workbench/v1/application/preview` accept hostname, recipe, optional application (default php), optional require_htaccess and optional check_syntax (default false). JSON is limited to 4,096 bytes; exact Origin, dedicated application-plan-preview CSRF, current ROOT authority and fresh authentication protect previews. MFA stays optional; enabled factors must complete. A common [preview request boundary](../../Shared/Http/RootPlanPreviewRequest.php) is also used by Modern, with distinct CSRF intents.

The CLI adds explicit application=php|front_controller|wordpress plus recipe selectors to its fixed plan command. Any application_server, web_stack or worker_acknowledged field conflicts with this family; recipe without application is refused. Existing inputs/outputs retain their meaning.

The ordered `wcp360.candidate.application-plan/1` envelope contains schema, application, recipe, web_server, application_server and exactly the recipe's artifact fields. String/JSON limits reuse [PlanDocumentFields](Domain/PlanDocumentFields.php). Old plan decoders reject it. The fixed-binary process keeps its scrubbed environment, five-second deadline and bounded stdout; renderer errors are generic and browser artifacts escaped.

Each preview generates a synthetic UUIDv7 and fixed application paths, site UID, sockets and non-root pool/service text. The frontend is HTTP on unreserved 127.0.0.1:8082; proxy backends use private Unix sockets (their Caddy address is not a TCP reservation). Neither caller-selected resources/paths/ports/commands nor deployment flags are accepted. All three profiles use the same bounded renderer. No real application or tenant is selected, no filesystem is read/written by the renderer, and no business state/audit is changed.

Native .htaccess is an explicit compatibility requirement. An unsupported combination is refused rather than translated arbitrarily or silently substituted. Apache/FPM emits a fixed rewrite example and directive allowlist; the allowlist does not make arbitrary user RewriteRule flags safe. Future deployment must own configuration changes, filesystem/network authority and module admission. Apache/FrankenPHP HTTP proxying is not advertised as native .htaccess handling.

## Optional native syntax checks

The preview request accepts optional `check_syntax` (JSON boolean or browser checkbox; false by default). Existing generation remains inert when omitted. No arbitrary configuration, path, executable or argv can be submitted. The [syntax use-case port](Application/ApplicationPlanSyntax.php) receives only the already checked generated document; current ROOT authority is checked again after the requested probes.

The additive `syntax_checks` member uses `wcp360.candidate.application-syntax/1`. Every result names its artifact, tool, check stage, status and the SHA-256 of the exact displayed configuration bytes. The four statuses remain distinct: `passed`, `failed`, `not_configured`, `not_run`. Unknown/missing/reordered parser evidence is rejected; failures and native stdout/stderr are never reflected into the response. Coverage, activation authorization and runtime validation remain false, including when a syntax stage passes.

| Artifact | Implemented check | Boundaries and remaining prerequisites |
| :--- | :--- | :--- |
| Nginx | Fixed `nginx -t`, unchanged generated artifact | A private 0700 temporary prefix contains fixed 0600 files, stderr logging and all temporary paths. Cleanup removes only known regular files/empty directories and refuses links. No runtime user, socket or application directory is created. |
| Caddy/FPM | Fixed Caddy `adapt --config - --adapter caddyfile` | Stdin only; bounded JSON object with HTTP servers is required. This is adaptation, not module provisioning or semantic/runtime acceptance. |
| FrankenPHP classic | The same adaptation command using FrankenPHP only | No fallback to plain Caddy. No `validate`, `--validate`, run, reload, start, import or environment expansion. The new recipes still need their own real native-provider harness and HTTP acceptance. |
| PHP-FPM pool | `not_run` for this synthetic preview | FPM `-t` also resolves pool/socket users and groups through NSS; synthetic identities are deliberately not installed or substituted to manufacture a pass. |
| Apache | `not_configured` | A separately controlled module/configuration harness is still required. |
| .htaccess / systemd unit | `not_run` | HTTP rewrite behavior and supervisor validation need their own bounded environments. |

[NativeSyntaxTools](Infrastructure/NativeSyntaxTools.php) admits only fixed installed binary locations on non-root Linux with POSIX identity support. The resolved binary and canonical parents must be root-owned and not group/world writable; setuid/setgid executables are refused. Missing/inadmissible providers return `not_configured`; the Core never downloads or installs one. Presence does not establish version/module provenance or release admission.

[SyntaxProcessRunner](Infrastructure/SyntaxProcessRunner.php) scrubs inherited and explicit process environment values, uses fixed unavailable HOME/XDG locations, empties PHP_INI_SCAN_DIR and enforces a five-second deadline, 256 KiB captured stdout and 16 KiB stderr. Failed/overflowing/timed-out children discard output and are stopped; the callback stops throwing after the first limit violation so cleanup can drain and terminate a continuing writer. These capture bounds are not OS memory/CPU sandboxing or hostile-module admission.

The current PHP CI images contain Nginx but do not contain the new Caddy/FrankenPHP adapters. The existing immutable Worker image does not run these Core tests. Thus this increment can exercise native Nginx, real bounded child processes and the adapter contract/refusals in CI; it does not establish that all 12 Caddyfile combinations have been natively adapted. This absence remains explicit and does not become an ignored pass.

## Performance and security acceptance

Each recipe uses direct static-file serving where its web layer supports it, bounded per-site PHP resources, OPcache, recycling and time limits. These are candidate baselines, not tuned production capacity or a speed guarantee. Process/file ownership, writable paths, socket reachability and installed modules must be admitted before activation. A document root is not a filesystem jail.

Dynamic page caching is off. Before enabling it, define tenant-isolated cache ownership/keys, purge rules and read/write bypasses for mutations, Authorization, cookies, query strings, admin/login, previews, REST and commerce/cart/account/checkout paths; respect Set-Cookie and private/no-store responses. An optional object cache requires its own isolated credentials and application integration, separate from Core queues. Do not emit an unavailable Caddy cache plugin or cache a missing static-file request that falls back to PHP.

Qualification must exercise the same application on each selected combination: installation/login/admin, directory/script entry points, pretty URLs/query preservation, static files, uploads, REST/AJAX/cron, errors and application updates. CMS plugins/themes and framework dependencies need representative cases. Private/config files, executable uploads, neighboring-site access, CSRF/session isolation, interrupted changes and recovery need negative checks.

Performance gates must record CPU/RAM/provider versions, concurrency, cache state/hit ratio, request mix, throughput, TTFB/p50/p95/p99, errors and endurance. Compare cached public traffic, uncached dynamic pages and authenticated/mutating flows separately. A single synthetic request or green renderer test does not measure these properties.

Current test definitions cover the 18 profile/recipe combinations, strict fields, old-contract separation, ROOT/USER/anonymous/MFA/CSRF/Origin/refusal behavior, no business writes and native Nginx syntax in disposable Linux CI. Native Apache/Caddy/FrankenPHP recipe syntax, live application execution and comparative performance remain unqualified until their own explicit CI/runtime harness is connected. Existing generic Worker and FPM VM successes do not qualify this new family. Local executable tests remain stopped.

## Primary references

- [FrankenPHP classic](https://frankenphp.dev/docs/classic/) and [WordPress reference](https://frankenphp.dev/docs/wordpress/).
- [Caddy adaptation versus provisioning](https://caddyserver.com/docs/command-line#caddy-adapt), [Nginx test behavior](https://nginx.org/en/docs/switches.html) and [PHP-FPM user resolution](https://github.com/php/php-src/blob/php-8.4.27/sapi/fpm/fpm/fpm_unix.c).
- [FrankenPHP configuration](https://frankenphp.dev/docs/config/) and [Caddy PHP-FastCGI](https://caddyserver.com/docs/caddyfile/directives/php_fastcgi).
- [WordPress Nginx routing](https://developer.wordpress.org/advanced-administration/server/web-server/nginx/).
- [Apache FastCGI](https://httpd.apache.org/docs/2.4/mod/mod_proxy_fcgi.html), [HTTP proxy](https://httpd.apache.org/docs/2.4/mod/mod_proxy.html) and [override controls](https://httpd.apache.org/docs/2.4/mod/core.html#allowoverridelist).
- [WordPress cache layers](https://developer.wordpress.org/advanced-administration/wordpress/wp-config/), [Nginx cache controls](https://nginx.org/en/docs/http/ngx_http_fastcgi_module.html) and [WooCommerce exclusions](https://developer.woocommerce.com/docs/best-practices/performance/configuring-caching-plugins/).
