# Offline artifact staging candidate

The [PHP verifier candidate 0.1.0](../../php/artifact-verifier/README.md) owns its versioned statement, envelope and operator-managed public trust schemas under `php/artifact-verifier/contracts/`. They describe offline private artifact admission, independently of the Registry HTTP contract and Core production extension manifest.

The vendor website includes an exact pinned source/schema copy, with per-file SHA256 in `user/plugins/wcp360-registry/lib/artifact-verifier/PROVENANCE.json`. The signer stays in the build-side SDK package, outside the webroot. Production key management, TUF integration, download authorization and Core installation remain unqualified.
