# Prototype verification

Run from the `WCP360_Prototype` directory with Node.js. The static and controller suite has no package installation step:

```sh
node tests/run.mjs
```

The runner executes every `audit_wcp360_*.mjs` file in this directory. It checks HTML references, duplicate IDs, JavaScript syntax, canonical navigation, shared collections, schemas and real controllers through DOM adapters. The setup suite covers invalid input, Linux failure fixtures, credential migration, runtime selection, failed persistence and multilingual search.

The latest run passed 25 suites. The [System Settings restoration record](../28-system-settings-functional-restoration.md) records the 24 sections, 248 settings and lists, implementation and verification limits. Its targeted suites are:

| Suite | Scope | Latest result |
| :--- | :--- | ---: |
| `audit_wcp360_settings_model.mjs` | Field and collection definitions, endpoint/IP validation, migration, diffs and canonical ROOT links | 263 checks |
| `audit_wcp360_settings_store.mjs` | Separate drafts, v2/v3 migration, collection publication, history, recovery and conflicts | 46 scenarios |
| `audit_wcp360_settings_controller.mjs` | Actual controller, overview, search and record editing with DOM/storage adapters | 27 scenarios |
| `audit_wcp360_setup.mjs` | Setup validation and persistence, including configured identity/IP values | 57 checks |
| `audit_wcp360_root_control.mjs` | Four ROOT pages, panels, targets and stored draft inputs | 10 checks |
| `audit_wcp360_governance.mjs` | Three governance pages, data validation and failed writes | 13 checks |
| `audit_wcp360_root_centers.mjs` | ROOT catalog review, exact software fields, targeted mail forms and queue filtering | 23 scenarios |

The settings suites also cover blank numeric values after draft reload, malformed or truncated saved drafts, confirmed draft removal and visible/retryable removal failures. Complete v2 drafts retain their original 86 fields and migrate additively; v3 snapshots must contain all current fields. Collection scenarios cover duplicates, subnet overlap, before/after review, write failures and restoration. Link tests resolve the actual canonical registry and verify ROOT ownership.

The [page-by-page inventory](../26-page-verification-inventory.md) and [CSV](../26-page-verification.csv) distinguish structure checks from targeted controller evidence. Regenerate them after changes with:

```sh
node tests/audit_wcp360_page_coverage.mjs --write
```

The inventory inspects all 232 initial HTML documents. It identifies 32 pages with targeted controller-adapter evidence and 200 without that evidence in the listed suites, including compatibility redirects. It does not execute the referenced suites or a browser. Shared component tests are not counted as proof of every consuming page's workflow.

To verify either local preview server, start it first, then run:

```sh
node tests/http_preview.mjs 8790
```

This sends GET/HEAD/POST requests to loopback, checks all HTML pages and shared assets, and verifies handling of malformed, outside-root and unsupported paths. It does not operate a browser.

The latest preview check passed 254 controls, including the 232 HTML pages.

## Separate browser tests

The existing Chrome runtime audits are included for an environment with a working Chrome sandbox and a Node runtime exposing `WebSocket`:

```sh
node tests/runtime_audit_wcp360.mjs
node tests/runtime_audit_extension_lifecycle.mjs
node tests/runtime_audit_interactions.mjs
```

Set `CHROME_PATH` to the installed Chrome executable when it differs from the Windows default. Run one browser audit at a time: they own loopback servers and isolated temporary Chrome profiles. They use fictional data and do not use the operator's Chrome profile. Stop a failed or interrupted audit before retrying. Browser audits are deliberately separate from the static runner.

`runtime_audit_wcp360.mjs` covers desktop/mobile layouts and workflows; `runtime_audit_extension_lifecycle.mjs` checks plugin lifecycle isolation. Native focus, real form validation, contrast and visual rendering still require a completed browser review. VM adapters cannot certify these properties.

For the System Settings restoration, `runtime_audit_wcp360.mjs` was updated and syntax-checked but was not executed. The loaded Chrome page exposed all 24 sections in its accessibility tree; final visual capture was inconclusive and native interaction/mobile reviews remain incomplete. The automated results do not establish WCAG compliance or full browser coverage of all pages.

No test here verifies Linux commands, a real installation, SMTP delivery, DNS propagation, ACME issuance, production permissions, encryption or real backups. Results do not change the canonical development gates.
