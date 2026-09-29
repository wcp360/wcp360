# WCP360 V2.5.3.17 — Email Functional Depth

## Style lock
V2.5.3.14 remains the visual baseline. No shell redesign introduced.

## Developed family
- email domain portfolio
- mail domain overview
- mailboxes
- mailbox detail
- aliases and forwarding
- SPF / DKIM / DMARC
- antispam and quarantine
- deliverability / reputation
- domain-scoped mail logs
- mailbox creation workflow
- alias creation workflow
- mail-domain onboarding
- WCP-ADMIN mail infrastructure view

## Security / operational rules
- credentials are SHOW_ONCE then NEVER_REVEAL;
- reset/rotation instead of password redisplay;
- SPF/DKIM/DMARC treated as one authentication posture;
- antispam / malware / spoofing are separate but coordinated controls;
- external forwarding surfaces DMARC / SRS / ARC implications;
- WCP-USER sees domain-scoped deliverability and remediation;
- shared MTA queues, global sender reputation and platform incidents remain WCP-ADMIN;
- logs redact credentials and sensitive payloads;
- blocked mail does not automatically mark the entire platform as critical.

## Next family
Files / File Manager.