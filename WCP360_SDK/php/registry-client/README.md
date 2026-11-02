# WCP360 vendor Registry client — candidate 0.1.0

Independent, read-only PHP library for the **vendor website Registry**. It does not call the standalone Core API, share its database or dependency graph, enforce entitlements, download artifacts, install packages or execute jobs. The Core public SDK and production extension manifest remain unqualified.

PHP ≥ 8.3 and JSON are required; `CurlTransport` also requires cURL. Consume this directory through an application's own Composer graph. No change to Core `composer.json`, lock, vendor or boot sequence is required. The small `autoload.php` supports isolated evaluation without installing dependencies.

```php
require '/path/to/registry-client/autoload.php';

use Wcp360\Sdk\Registry\{RegistryClient, CurlTransport};

$client = new RegistryClient(
    'https://wcp360.com/api/v1/wcp360-registry',
    new CurlTransport(),
    acceptCandidate: true,
);
$plugins = $client->catalog('plugin', ['page'=>1, 'per_page'=>20]);
```

The production URL above is a deployment example, not a claim that the remote service is published. Local development uses `http://localhost/grav/api/v1/wcp360-registry` with **both** `new CurlTransport(allowLoopbackHttp: true)` and client `allowLoopbackHttp: true`. HTTP is otherwise rejected. Configure the base URL from trusted application configuration; never from an end-user URL.

Available methods: `status`, `catalog`, `package`, `versions`, `releases`, `updates`. Responses are validated arrays according to the pinned candidate schema. `updates` requires the complete Core/PHP/API/SDK versions, OS and architecture; it offers metadata only. Exact field definitions are in [OpenAPI](contracts/openapi.yaml) and [JSON Schema](contracts/response.schema.json). The deployment directory `/grav` is a local base path, not part of the vendor contract.

The transport verifies TLS, disables redirects, bounds connection time (3 s), total time (10 s), headers (32 KiB) and body (2 MiB), and never forwards credentials. No automatic retry is performed, including on `429`. ETags are retained in memory for at most eight validated public responses and revalidated on every request. Errors expose a stable local code and optional HTTP status; upstream bodies and query values are not included. A custom `Transport` must preserve these guarantees. Text fields remain untrusted and must be escaped by the rendering application.

Run `php tests/run.php`. The Grav provider additionally runs a provider/consumer check against this client with synthetic fixtures. These checks on XAMPP PHP 8.3 do not qualify the standalone PHP 8.4 runtime, distribution trust or production integration.

Contract provenance and hashes are recorded in [PROVENANCE.json](contracts/PROVENANCE.json). These copies originate in the vendor Registry plugin and do not replace or ratify any owner-approved Core specification. The validator implements only the bounded JSON Schema subset used by these pinned files; it is not a general-purpose JSON Schema engine. A contract change requires coordinated schema, client, provider tests and provenance updates.
