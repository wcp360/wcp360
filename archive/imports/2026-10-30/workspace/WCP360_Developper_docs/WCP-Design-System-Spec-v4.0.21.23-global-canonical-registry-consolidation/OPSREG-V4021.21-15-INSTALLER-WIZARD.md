# V4.0.21.21 --- Installer / Finalization Wizard Deep Contract

Phases: preflight → host-profile detection → compatibility blockers →
package/provider plan → trusted installation → control-plane bootstrap →
initial admin creation → MFA/security baseline → hostname/network/IPAM →
DNS resolver/time → service selection → mail identity/rDNS guidance →
backup target → monitoring/alerts → final Doctor → READY.

Wizard is resumable/idempotent. Secrets are never echoed into install
logs. Re-running cannot duplicate admin/system resources. Unsupported
host combination is blocked before destructive mutation.
