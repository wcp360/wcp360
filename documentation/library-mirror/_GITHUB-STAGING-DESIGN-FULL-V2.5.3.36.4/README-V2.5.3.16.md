# WCP360 V2.5.3.16 — Databases Functional Depth

## Style lock
V2.5.3.14 remains the visual baseline. No shell redesign was introduced.

## Developed family
- Databases portfolio
- Database resource detail
- Database users and privileges
- User detail and credential policy
- Network access policy
- Database backups
- Import/export jobs
- Diagnostics
- Database creation workflow
- Database user creation workflow

## Security model
- local-only access by default;
- no public exposure without explicit high-risk review;
- existing database passwords are NEVER_REVEAL;
- newly generated credentials are SHOW_ONCE;
- rotation instead of re-display;
- application users receive least-privilege presets;
- high-risk administrative privileges are excluded from normal WCP-USER workflows;
- imports can create a pre-import snapshot;
- large exports are background jobs;
- WCP-USER database tuning stays application-scoped; server-level tuning belongs to WCP-ADMIN.

## Next family
Email.