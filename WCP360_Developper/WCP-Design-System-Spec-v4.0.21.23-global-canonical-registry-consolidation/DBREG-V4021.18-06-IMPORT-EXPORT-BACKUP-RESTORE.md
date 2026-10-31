# V4.0.21.18 --- DB Import / Export / Backup / Restore

Import pipeline: upload/source ref → type/size validation → safe staging
→ optional malware/content policy → target compatibility → Job →
provider import → verification → audit.

Export/backup: consistent snapshot/logical dump according to adapter
capability, checksum, encryption-at-rest through backup policy,
retention and verification.

Restore requires target ownership, compatibility and conflict plan.
Overwrite restore requires step-up and explicit confirmation. Temporary
dump files are privileged, bounded and securely removed according to
policy.
