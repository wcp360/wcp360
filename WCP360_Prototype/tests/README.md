# Prototype verification

Run from any directory with Node.js. The static and controller suite has no package installation step:

```sh
node WCP360_Prototype/tests/run.mjs
```

The runner executes every `audit_wcp360_*.mjs` file in this directory. It checks HTML references, duplicate IDs, JavaScript syntax, canonical navigation, shared collections, schemas and real controllers through DOM adapters. The setup suite covers invalid input, Linux failure fixtures, credential migration, runtime selection, failed persistence and multilingual search.

To verify either local preview server, start it first, then run:

```sh
node WCP360_Prototype/tests/http_preview.mjs 8790
```

This sends GET/HEAD/POST requests to loopback, checks all HTML pages and shared assets, and verifies handling of malformed, outside-root and unsupported paths. It does not operate a browser.

## Separate browser tests

The existing Chrome runtime audits are included for an environment with a working Chrome sandbox and a Node runtime exposing `WebSocket`:

```sh
node WCP360_Prototype/tests/runtime_audit_wcp360.mjs
node WCP360_Prototype/tests/runtime_audit_extension_lifecycle.mjs
```

Set `CHROME_PATH` to the installed Chrome executable when it differs from the Windows default. Run one browser audit at a time: they own loopback servers and isolated temporary Chrome profiles. They use fictional data and do not use the operator's Chrome profile. Stop a failed or interrupted audit before retrying. Browser audits are deliberately separate from the static runner.

`runtime_audit_wcp360.mjs` covers desktop/mobile layouts and workflows; `runtime_audit_extension_lifecycle.mjs` checks plugin lifecycle isolation. Native focus, real form validation, contrast and visual rendering still require a completed browser review. VM adapters cannot certify these properties.

No test here verifies Linux commands, a real installation, SMTP delivery, DNS propagation, ACME issuance, production permissions, encryption or real backups. Results do not change the canonical development gates.
