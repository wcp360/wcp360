# V4.0.21.19 --- Anti-Spam / Antivirus Registry

## Providers

Rspamd/SpamAssassin-like anti-spam and ClamAV/other certified AV
adapters are capability providers; WCP policy remains canonical.

## Policy levels

Platform security floor → reseller ceiling → account/domain/mailbox
allowed overrides. Users cannot disable mandatory platform scanning.

Operations: `SPAM-POLICY-SET`, `SPAM-ALLOWLIST-UPDATE`,
`SPAM-BLOCKLIST-UPDATE`, `AV-POLICY-SET`, `MAIL-SECURITY-RECONCILE`.

Actions may reject, tag, quarantine or score according to
policy/provider. Quarantine access is privacy-sensitive, scoped and
audited. Signature/rule update health is monitored separately from
scanner process health.
