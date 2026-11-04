# Core HTTP boundaries in a persistent FrankenPHP worker

Status: executable verification candidate; not independent acceptance or production qualification.

## Scope

The existing `frankenphp-worker` CI job now loads the actual `BoundarySubscriber`, `RequestReference`, `RequestId` and `RuntimeContext` sources with dependencies installed from the unchanged Core `composer.lock`. It uses the existing immutable FrankenPHP base and the Composer version already used by the Core verification image. No dependency versions or application contracts are changed. Platform requirements remain enforced; Composer scripts and plugins are disabled in this isolated component image.

`fixtures/core-boundary-worker.php` creates one persistent Symfony HttpKernel and pair of Core issuers per logical USER/ADMIN context. Its renderer and reference event wiring are test fixtures, not the complete application's container, authentication stack or HttpErrorSubscriber. The outer Nginx host remains `worker.test`; fixed inner `.localhost` hosts exercise the unchanged component policy, not production reverse-proxy routing.

`fixtures/verify-core-worker.php` sends 505 sequential real HTTP requests through Nginx and FrankenPHP. It alternates USER/ADMIN and normal, forged-attribute, early-wrong-host-error and subrequest cases. It checks actual response headers against the component-rendered values, verifies early reference continuity, rejects cross-request nonce/reference reuse, and checks that cyclic Request objects are collectible while their kernel and services remain alive. A boot identifier and monotonic counters prove persistent reuse and exactly one planned recycle at the existing 500-request limit. OS identity, neighboring-file isolation and request-marker cleanup assertions remain in force.

The real worker fixture is copied only into the disposable verification image. It creates no application endpoint, session, database, permission, daemon operation, installer or production runtime.

## Evidence

Source definitions and static syntax checks are not a runtime PASS. Record the exact commit, Actions run, `frankenphp-worker` result and its final `PASS: 505 real FrankenPHP requests` line in PR #127 and issue #130. The full exact-source CI and independent Security/QA/Engineering/DevOps dispositions remain separate gates. This probe does not close the full authentication, production isolation, proxy trust, concurrent load, memory-budget or release requirements.

Relevant runtime semantics: [FrankenPHP worker lifecycle](https://frankenphp.dev/docs/worker/). PHPUnit doubles used only to inject recovery race callbacks are test stubs, not interaction mocks; assertions on preserved credentials remain unchanged ([PHPUnit test doubles](https://docs.phpunit.de/en/12.5/test-doubles.html)).
