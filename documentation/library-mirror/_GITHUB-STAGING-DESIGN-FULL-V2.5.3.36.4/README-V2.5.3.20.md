# WCP360 V2.5.3.20 — Security & Access Functional Depth

## Style lock
V2.5.3.14 remains the visual baseline. No shell redesign was introduced.

## Developed family
- Security Center
- MFA / passkeys / authentication policy
- sessions
- SSH keys
- API tokens
- IP restrictions
- WAF
- security audit
- privileged sessions
- impersonation
- break-glass
- WCP-ADMIN platform security

## Security rules
- passkeys/WebAuthn preferred for privileged authentication where supported;
- MFA required for administrative roles;
- re-authentication is separate from normal login;
- privileged sessions are temporary, scoped, visible and audited;
- context switching never changes privilege;
- API secrets are SHOW_ONCE and never re-displayed;
- SSH key scope does not imply server-wide shell access;
- IP restrictions include lockout protection;
- impersonation is read-only by default and always visibly disclosed;
- impersonation write capability requires explicit temporary escalation;
- break-glass is emergency-only, short-lived, strongly authenticated and post-reviewed;
- WAF protection is application scoped while global baselines stay WCP-ADMIN;
- security audit events are immutable and correlation-friendly.

## Next family
Monitoring / Metrics / Incidents.