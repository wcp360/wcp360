# WCP360 pairing client 0.1.0 — local candidate

Independent PHP ≥8.3 library (JSON, Sodium; cURL for HTTP), evaluated under the site PHP 8.3 and standalone PHP 8.4 toolchains. No Core vendor, dependency lock, startup hook or hosting authorization is changed. This is a candidate website identity handshake, not the accepted Core API, machine attestation, licensing client or production SDK integration.

The provider [contract](../../../../../htdocs/grav/user/plugins/verify-license/PAIRING.md) defines exact signed bytes, fields, limits and recovery semantics. Verify License 0.2.0 currently supports **only direct loopback HTTP** with explicit operator opt-in. This library requires HTTPS by default and verifies TLS; that does not imply a qualified HTTPS provider exists. No redirects, proxy, cookies or Admin credentials; bounded bodies / headers and deadlines. A fresh cURL handle leaves NETRC ignored (the libcurl default); it never loads credentials from a caller-owned shared handle.

## Standalone tool

The executable [tools/hub-pair.php](../../../tools/hub-pair.php) runs separately from Core. Required pair options: pinned `--hub`, absolute `--state-dir` outside webroots, declared SemVer `--core-version`, `--os` and `--architecture`. The metadata are declarations, not auto-detected attestations. Allowed values are documented in the provider contract; `unknown` remains explicit. No business or paid feature is enabled by pairing.

1. Give the intended Grav account `site.login` and the dedicated `site.wcp360_pairing` permission deliberately. Sign in to the website, open Installations, enter a label and choose the code lifetime.
2. Create an operator-owned private parent directory (Linux: no group / other write; private state directory 0700, files 0600). On Windows verify NTFS ACLs independently; chmod does not establish them. State must not be served by Apache, an alias, backups or another local user. Shared hostile filesystems are unsupported.
3. Run the tool below, then paste the one-time code into its standard input and press Enter. Never put the code in argv, an environment variable, a URL or shell history. The tool waits for stdin; the command line contains no code.

```powershell
& 'C:\xampp\projects\WCP360_Standalone\WCP360_Build\build\toolchains\php84\php.exe' 'C:\xampp\projects\WCP360_Standalone\tools\hub-pair.php' pair --state-dir=C:\xampp\projects\WCP360_Instance_Pairing --hub=http://localhost/grav --core-version=1.0.0 --os=unknown --architecture=unknown --allow-loopback-http
```

The parent `C:\xampp\projects` exists in this development example. Choose a separate protected directory for each instance. The tool creates a local Ed25519 key and saves a pending request before any network operation. Its stdout contains only status, instance ID and public-key fingerprint. Success atomically replaces pending request / raw code with the public receipt; it retains the private key locally. The Vault holds an exclusive nonblocking lock and checks paths, links, file count / size and permissions supported by the platform. Atomic replacement and crash durability still require Linux target qualification.

```powershell
# Current remote key-possession check; failures do not delete the identity.
& 'C:\xampp\projects\WCP360_Standalone\WCP360_Build\build\toolchains\php84\php.exe' 'C:\xampp\projects\WCP360_Standalone\tools\hub-pair.php' check --state-dir=C:\xampp\projects\WCP360_Instance_Pairing --allow-loopback-http
# Read disk only; explicitly reports remote_verification=false.
& 'C:\xampp\projects\WCP360_Standalone\WCP360_Build\build\toolchains\php84\php.exe' 'C:\xampp\projects\WCP360_Standalone\tools\hub-pair.php' local-status --state-dir=C:\xampp\projects\WCP360_Instance_Pairing
```

After an uncertain response use `resume` with the same state directory and explicit loopback opt-in. It sends the identical signed pending request. The server can recover the same public receipt **only while the original code is valid**, the owner enabled and the identity active. Do not delete state, regenerate a key or blindly create another code after an uncertain outcome. After the deadline, operator recovery / reconciliation remains unimplemented; preserve the key and resolve the possible server identity explicitly. There is no automatic reset, transfer, recovery or rotation.

An identity can be revoked in the owner’s portal. A fresh `check` is then refused, while local-status remains a disk observation. Pairing does not establish a session, grant licenses, authorize an installation, implement a heartbeat or execute remote actions. Server receipts are not publisher-signed; transport authenticates them. Private key disclosure requires revocation; no central secret or Grav administrator credential belongs in the Vault.

## Validation

`tests/run.php` tests an independent consumer against synthetic transports; an optional private loopback router exercises actual cURL redirect, size and credential behavior. [validate-pairing.ps1](../../../../WCP360_Quality/validate-pairing.ps1) adds real MariaDB, Grav sessions, provider / consumer proof interoperability, two-worker consumption, rollback, restricted SQL, foreign ownership, replay / expiry / revocation, actual PHP 8.4 CLI and Chrome. PHPStan level 5 uses its own tooling, not Core’s vendor.

Reports contain exact hashes and explicit `not_run` / `not_configured` gaps. HTTPS / proxy deployment, Linux filesystem and Core startup, Admin2 privileged session, SAST / secret scanning, independent protocol review, capacity, retention and recovery / rotation block production qualification. No claim of complete security or Internet readiness follows from local tests.
