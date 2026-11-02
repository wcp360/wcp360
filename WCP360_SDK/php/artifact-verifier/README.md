# WCP360 artifact staging verifier — candidate 0.1.0

Independent PHP 8.3+ library for **offline admission of a private ZIP snapshot**, using JSON, Sodium and ZIP extensions. It is separate from the Registry HTTP client, Core dependency graph, production extension manifest and Package Manager. It never downloads, extracts, executes or installs a package.

## Signed bytes and trust

Profile: [DSSE 1.0.2](https://github.com/secure-systems-lab/dsse/blob/master/protocol.md), one Ed25519 detached signature through [PHP Sodium](https://www.php.net/sodium-crypto-sign-verify-detached). The payload type is exactly `application/vnd.wcp360.artifact-staging.v1+json`. The signed bytes are DSSE PAE: `DSSEv1 <type-byte-length> <type> <payload-byte-length> <payload>`. Lengths are decimal UTF-8 byte lengths, not character counts. The official PAE example is tested. There is no custom JSON canonicalization requirement: verify the exact payload bytes, then parse those same bytes once.

The envelope follows the [DSSE envelope definition](https://github.com/secure-systems-lab/dsse/blob/master/envelope.md). Standard and URL-safe base64, padded or unpadded, are accepted with canonical pad bits. The unauthenticated `keyid` is only a lookup hint into local operator-managed trust. It is the lowercase SHA256 of the raw 32-byte public key; it cannot introduce a new key. Unknown envelope/signature fields are ignored and never used for authorization. The signed statement and local trust records reject unknown fields. All JSON rejects duplicate names, including Unicode-escaped aliases.

The caller supplies the expected **type, slug and exact SemVer version** independently of the envelope. Trust limits each key to a publisher, allowed types, slug prefix and UTC validity window. `slug_prefix: ""` explicitly permits all slugs for that publisher and allowed types; prefer a narrow prefix. A revoked, unknown, premature or expired key fails. The statement must be currently valid, wholly within the key's validity and live for at most seven days. The local clock and trust file are trusted inputs.

Machine contracts: [statement](contracts/statement.schema.json), [envelope](contracts/envelope.schema.json), [local trust](contracts/trust.schema.json). JSON Schema describes structure; PHP additionally checks exact dates, duplicate keys, cross-field validity, signatures, identity, scope and archive content. This is a **candidate staging statement**, not a production extension manifest, SLSA provenance claim, authenticated key-rotation protocol or license token.

```php
require '/path/to/artifact-verifier/autoload.php';

$trust = new Wcp360\Sdk\Artifacts\TrustStore(
    Wcp360\Sdk\Artifacts\StrictJson::object($locallyManagedTrustJson)
);
$verifier = new Wcp360\Sdk\Artifacts\ArtifactVerifier(
    new Wcp360\Sdk\Artifacts\EnvelopeVerifier($trust)
);
$result = $verifier->verify($privateSnapshotPath, $boundedEnvelopeJson, [
    'type' => 'plugin', 'slug' => 'example', 'version' => '1.0.0'
]);
```

The result is point-in-time verification metadata. Supply a private immutable snapshot; an untrusted OS writer replacing paths during verification is outside this library's boundary. Do not treat the result as permission to extract, execute, install or serve a file later. Recheck current trust, expiration and exact stored bytes for each subsequent use.

## Archive admission profile

Bounds: envelope 32 KiB, decoded statement 16 KiB, at most 32 local keys, ZIP 128 MiB, 5,000 entries, 256 MiB expanded total, 64 MiB per entry and expansion ratio 200 per entry. Constructor limits can only tighten the ZIP defaults. Inspection reads bounded decompressed streams and validates sizes and CRC; it writes no extracted files.

Only single-disk non-Zip64 ZIPs with store/deflate, at least one regular file and no prefix/trailer are accepted. Encrypted entries are refused. Paths use a deliberately conservative portable ASCII profile: no absolute paths, traversal, backslashes, Windows streams/reserved device names, trailing dots/spaces, duplicates, case collisions or file/directory conflicts; maximum depth 16, path 512 bytes and component 240 bytes. Unix symlinks, special types and privileged mode bits are refused. [ZIP external attributes](https://www.php.net/manual/en/ziparchive.getexternalattributesindex.php) are interpreted for Unix entries. This does not qualify every platform's extra fields or extraction semantics.

Inspection is not a malware scan, source review, permission/capability review, dependency resolver or sandbox. A trusted publisher can still sign malicious application code. A future extractor must independently enforce its own destination, link, ownership and write policy; this verifier performs no extraction.

## Offline build signer

`bin/sign-artifact.php` belongs **only in the protected build environment, outside the website**. It takes an existing ZIP, a descriptor JSON and a new envelope path. It accepts the base64 64-byte Ed25519 secret key on **standard input only**, never in arguments, a website setting, an example key or its output. Supply stdin from an appropriately protected secret manager or build runner; do not paste a real secret into shell history. No production key is generated by this package.

```text
php bin/sign-artifact.php artifact.zip descriptor.json artifact.dsse.json
```

The command waits for the protected stdin stream to close. The descriptor has exactly `package` (`type`, `slug`, `version`, `publisher`), `issued_at` and `expires_at`. Dates are exact UTC `YYYY-MM-DDTHH:MM:SSZ`. The tool inspects the ZIP, derives size and SHA256, signs the statement, self-verifies the signature and refuses to overwrite an envelope. Keep the build snapshot inaccessible to concurrent untrusted writers. A successful signature does not add the public key to any trust store.

The Grav plugin includes only a pinned copy of the verifier and its schemas, **excluding this signer and its tests**. Review source changes and refresh the provenance hashes explicitly when updating that copy.

## Tests and remaining gates

`php tests/run.php /private/temp/workspace` uses ephemeral in-memory keys and synthetic archives. The Grav plugin's `tests/artifacts.php` also exercises the real signer subprocess with a secret stdin pipe, immutable storage, corruption, revocation and simultaneous admissions. Test archives and public trust fixtures are isolated from live catalogs and trust. No secret key is saved to disk.

This profile does not implement update selection, rollback/freeze resistance, persistent client trust state, threshold roles, authenticated root rotation or clock-rollback detection. Those require a qualified update protocol, such as a maintained implementation of [TUF](https://theupdateframework.github.io/specification/), before automatic distribution. It also does not implement commercial entitlements, download tickets or Core integration. Tests on local PHP 8.3 do not qualify Core PHP 8.4, Go interoperability, remote filesystems, OS ACLs or production deployment.
