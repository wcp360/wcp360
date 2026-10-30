# V4.0.21.21 --- Backup Integrity / Encryption / Verification

Backup manifest binds resource IDs, versions, timestamps, checksums,
format/ adapter version and encryption metadata. Encryption keys are
SecretStore/KMS-like references and are not stored alongside backup
payload as plaintext secrets.

Verification levels: transport/checksum, archive readability, logical
structure, and restore-test where policy requires. `VERIFIED` requires
configured verification level to pass.

Tamper/corruption detection yields FAILED/DEGRADED evidence; WCP never
marks a backup healthy merely because an object exists at destination.
