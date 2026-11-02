# Core development tools

This directory holds named development/CI tooling that supports the Core source and its locked toolchain. It is separate from the operator-facing [product toolbox](../resources/toolbox/README.md), specialized [diagnostics](../diagnostics/README.md) and implemented [product commands](../bin/COMMANDS.md).

A tool under this directory does not become a shipped service, tenant feature, plugin hook or privileged operation. The [Core structure](../STRUCTURE.md) retains those ownership boundaries.

## Existing PHPStan integration

| Source | Actual responsibility |
| :--- | :--- |
| [phpstan/bootstrap.php](phpstan/bootstrap.php) | Prepare the synthetic analysis environment and return the candidate Kernel used by analysis support. |
| [phpstan/prepare.php](phpstan/prepare.php) | Boot the test/user analysis Kernel and require its current Symfony container XML at the configured path. |
| [phpstan/console-application.php](phpstan/console-application.php) | Provide the Symfony Console application to the static-analysis integration. |
| [phpstan/fixtures.neon](phpstan/fixtures.neon) | Scope/configuration for the deliberate-error Symfony-rule regressions. |
| [phpstan/fixtures](phpstan/fixtures) | Synthetic valid and invalid container/Console examples; deliberately invalid examples are not production source. |
| [phpstan/verify.php](phpstan/verify.php) | Require exactly the expected missing/private-service and unknown-option diagnostics while accepting valid lookups/options. |

The main [phpstan.neon](../phpstan.neon) owns the current analysis scope and level 8 configuration. [composer.json](../composer.json) declares the existing analysis/preparation/regression scripts. Their presence describes the configured development checks; it does not report an execution result.

The regression verifier expects PHPStan to reject its deliberate-error fixture run, then checks the precise diagnostics. That expected fixture exit is distinct from an unexpected tool failure or an accepted production defect. Generated analysis caches/container XML belong under ignored var paths, not source.

## Adding a development tool

Add a specifically named directory when an implementation needs it. Document its maintainer purpose, inputs, output, runtime/dependency ownership, expected success/refusal behavior and resource bounds. Use synthetic fixtures and private disposable output locations. Never commit secrets, captured tenant data or generated dependency trees.

PHP development tooling uses the Core's reviewed Composer graph; a separate ecosystem/tool needs its own manifest/lock and reviewed installation boundary. Do not install unrelated tooling into the Core vendor tree, copy vendor code into source or create a catch-all dependencies/functions directory.

A source checker is not a product health monitor. A development script is not a generic subprocess API. Move reusable business behavior into its owning module rather than importing maintenance scripts from controllers or plugins. Product diagnostics require their own authorized, bounded and redacted contract.

- [Core structure](../STRUCTURE.md)
- [Command inventory](../bin/COMMANDS.md)
- [Shared foundations](../src/Shared/README.md)
- [Core test map](../tests/README.md)
