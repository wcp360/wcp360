# V4.0.21.20 --- Files / Runtime Negative Security Cases

P0 negative cases: path traversal, symlink/hardlink escape, TOCTOU
replacement, zip-slip/tar path escape, archive bomb, upload quota
bypass, special-device creation, SSH key cross-tenant assignment,
terminal privilege escalation, Git SSRF, malicious hook as root,
dependency lifecycle script privilege escape, secret in Job log, cron
frequency bypass, worker service-name injection, staging sending
production mail, WP SSO replay and application installer root-shell
recipe.

Every denial maps to stable error/audit reason and no privileged side
effect.
