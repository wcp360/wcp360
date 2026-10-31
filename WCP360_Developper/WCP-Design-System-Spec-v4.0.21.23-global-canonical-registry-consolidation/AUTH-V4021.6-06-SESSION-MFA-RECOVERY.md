# V4.0.21.6 --- Session, MFA, Recovery & Anti-Abuse Contract

## Sessions

Idle timeout and absolute lifetime are separate. Session identifiers
rotate on authentication privilege changes. Logout/revocation is
server-side. Password/critical credential rotation can trigger
configurable family-wide revocation.

## MFA

Privileged WCP-ADMIN identities require MFA by policy. WCP-USER MFA can
be required by platform/reseller/account security policy. Factor removal
requires step-up and cannot leave a privileged account below its
security floor.

## Recovery

Recovery is deliberately more restrictive than normal login: short-lived
single-use challenges, attempt limits, generic public responses, audit,
notification, and optional cooling period for privileged accounts.

## Rate limiting

Separate buckets for login identifier, source/network reputation,
recovery, MFA challenge, token creation and privileged step-up. Rate
limiting is server-side and cannot reveal whether the identifier exists.

## SSH/FTP relation

Panel MFA does not magically make SSH/FTP MFA-capable. Access-service
authentication policies are separate adapters/policies and must be
represented honestly in UI.
