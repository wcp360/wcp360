# WCP360 plugins

Each plugin has one source directory, its own version/dependency graph, tests, documentation and independently qualified artifact. Core owns the host and essential hosting services; optional plugins use the public SDK and approved APIs.

## Existing packages

| Directory | Current scope |
| :--- | :--- |
| [wcp360-file-manager-xampp](wcp360-file-manager-xampp/README.md) | Standalone File Manager candidate; private single-owner storage. Core/multi-tenant integration needs a separately reviewed adapter and confinement contract. |
| [wcp360-insights-connector](wcp360-insights-connector/README.md) | Insights UI/manifest prototype, optional and disabled by default. Manifest versions/security declarations are not executed qualification. |
| [_template](_template/README.md) | Authoring/layout template; not a plugin discovered or installed by Core. |

Package sources and their existing manifest shapes are preserved. Their directory names do not set trust, permission, production compatibility or installability. The existing 149-entry catalogue describes target integrations, not 149 implemented packages.

## Premium connector proposals

The [premium proposal inventory](premium/README.md) contains **97 optional provider/connectors in 19 categories**. Its [JSON catalogue](premium/CATALOG.json) is authoritative; [CATALOG.md](premium/CATALOG.md) is the readable view. Each proposal has a documented Core boundary, exact references to the existing 149-entry catalogue and an acceptance record.

All 97 proposals are `design`, `installable: false` and `source_model: undecided`; no implemented package or production manifest is implied. The separate [premium template](premium/_template/README.md) preserves the existing template above. The [44 business module proposals](../WCP360_Modules/CATALOG.md) describe optional workflows rather than provider connectors.

The proposals explicitly cover advanced multi-server workflows, Docker/Kubernetes/VM providers, storage platforms, CDN/performance profiles and defensive cybersecurity/DDoS integrations. Coverage also includes external application/proxy platforms, tenant database services, consented notifications and additional ACME authority profiles. Core retains native controls, compression, target Apache access rules, local email/TLS/wildcard and recovery; interfaces, maintenance, resource ownership and OS support still require qualification.

The seven `engine.*` adapters and the **233 essential hosting capabilities remain free Core scope**. The chosen Core license is [AGPL-3.0-only](../WCP360_Core/LICENSING.md). No premium package license, price, source distribution model, key or vendor URL is chosen here. A separate API/process does not automatically establish legal independence for a proprietary derivative; see [editions and premium boundaries](../documentation/development/EDITIONS-AND-PREMIUM.md).

## Package layout

```text
<plugin>/
├── README.md
├── PLUGIN.md                    # Ownership, contract and acceptance record
├── src/                         # Independent plugin application/services
├── config/                      # Plugin-owned settings and secret references
├── templates/                   # Views rendered by the plugin's isolated service
├── tests/                       # Standalone, SDK/contract and lifecycle tests
└── docs/
```

When the plugin's actual runtime is bootstrapped, add the versioned production manifest, composer.json/application lock for PHP (or the approved language manifests), dependency license/integrity inventory and reproducible build entry points. Do not create a manifest parser compatibility claim from the authoring template.

vendor, node_modules, runtime data and generated output are not source. A PHP plugin installs its own dependencies; it never edits WCP360_Core/vendor or adds an arbitrary bundle to the Core container. Package dependencies are assembled in the controlled build lane.

## Development and activation

Develop and unit-test independently against SDK fixtures, then test against supported Core versions. Installation defaults to disabled; activation follows trust, compatibility, dependency, capability, health and operator checks. Stop/revoke/drain on disable, and retain owned data by default on uninstall. Optional plugin failure must preserve Core and hosted workloads.

- [Template and authoring record](_template/README.md)
- [Public SDK](../WCP360_SDK/README.md)
- [Development strategy](../documentation/development/README.md)
- [Lifecycle and isolation requirements](../documentation/development/PLUGIN-LIFECYCLE.md)
- [Full plugin catalogue](../PLUGIN_CATALOG.md)
