# Public PHP SDK

Reserve public client/DTO/result/error code and testable transport abstractions derived from approved APIs. Maintain an independent library manifest/version and compatibility tests; do not expose private Core entities, repositories or broker clients.

Available for isolated evaluation: [vendor Registry candidate client 0.1.0](registry-client/README.md), with an independent Composer manifest, bounded transport, pinned schemas and synthetic tests. This is a read-only website API consumer, not the Core PHP SDK or a Package Manager integration.

See the [SDK status](../README.md) and [development strategy](../../documentation/development/README.md).

Also available independently: [offline artifact staging verifier 0.1.0](artifact-verifier/README.md), with DSSE/Ed25519 verification, bounded ZIP inspection, candidate schemas and a build-only signer. No automatic downloads, extraction, installation, licensing or qualified production update protocol.

Also available independently: [pairing client 0.1.0](pairing-client/README.md), used by the standalone `tools/hub-pair.php` command. An account-owned code binds a local key to a candidate identity; confirmation checks possession and revocation. Provider supports direct loopback development only. No Core startup or hosting dependency, license authorization, heartbeat or production protocol is introduced.
