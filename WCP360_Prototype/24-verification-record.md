# Prototype verification record — 2026-10-31

This record concerns the rename from `WCP360_Design_System/` to `WCP360_Prototype/`, the home journeys/search, setup validation and fixtures, preview servers, documentation and portable test sources. It does not qualify the Symfony/Go product or authorize another system-runtime slice.

## Completed checks

| Check | Result | Scope |
| :--- | :--- | :--- |
| Static/controller runner | **14 suites, zero failures** | Shared foundation, hosting, files/resellers, protection, DNS/TLS, delivery, applications, observability, web/mail rules, lifecycle, setup and search |
| Behavior checks | **547 passed** | Real models and controllers through UI/storage adapters; includes 53 setup/search cases |
| Integration inventory | **232 HTML pages, 62 asset scripts, 2,716 local references, 4,004 assertions** | JavaScript parsing, references, IDs, ownership and shared state integration |
| Compatibility routes | **64 checked** | Redirect targets and query/hash preservation in the integration suite |
| HTML audit | **No findings** | Required document metadata, duplicate IDs, local assets, controls and probable encoding errors |
| Portable Node preview | **254 HTTP checks passed** | All 232 HTML routes, shared assets, GET/HEAD/POST, MIME, malformed and outside-root requests |
| Windows PowerShell preview | **254 HTTP checks passed** | The same loopback HTTP contract on a second port |

Execution environment: Windows, an installed Node-compatible runtime reporting `v24.21.0`, and Windows PowerShell. No package dependencies were installed. The Node preview source is portable; execution on a Linux host has not been verified in this session.

## Corrected behaviors

- Management identity, database metadata, paths, endpoints, resource bounds and service dependencies are validated through a shared model.
- The control-plane setup offers PostgreSQL 17; tenant database engines remain in their own service center.
- Linux fixtures can fail for disk space, DNS, ports or time drift. Edits and reloads invalidate previous preflight results.
- Literal credentials in old wizard drafts are removed. Setup fields accept synthetic references; arbitrary credentials are rejected and cleared.
- Runtime choices are saved directly with settings. Failed settings/draft writes do not queue an initialization operation or display completion.
- Malformed prior settings and revision metadata have explicit recovery behavior.
- Home search supports accented French terms, plurals and multiple tokens, including safe handling of inherited object-property names.
- The HTML encoding check no longer reports a valid French circumflex as broken UTF-8.
- Preview servers restrict requests to loopback and supported files within the prototype. Node resolves real paths; PowerShell rejects linked/reparse paths.

## Browser review was not completed

The isolated Chrome audit did not yield a completed result in this environment. A separate Chrome startup check exited with a GPU-process failure under the available Windows permissions.

The Computer Use fallback then stopped because it could not determine the current Windows browser URL with enough confidence to enforce its policy. No further browser actions were performed. Desktop/mobile rendering, native form behavior, keyboard focus and visual contrast remain **unverified for this revision**. The browser audit sources are retained in `tests/` for a suitable environment; this record does not claim they passed.

## Reproduce

```sh
node WCP360_Prototype/tests/run.mjs
node WCP360_Prototype/serve-prototype.mjs
# In a second terminal:
node WCP360_Prototype/tests/http_preview.mjs 8790
```

For the PowerShell preview, use the command in [README.md](README.md), then run the HTTP suite against its selected port. The execution-policy option applies only to that PowerShell process; it does not change the machine's saved policy.

The checks do not validate real permissions, privileged IPC, SMTP, DNS propagation, ACME issuance, encryption, backups, service reloads or production performance. Historical design baselines and branches remain unchanged.
