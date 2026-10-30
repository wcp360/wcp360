# V4.0.21.20 --- Staging / Preview / Clone Contract

Clone plan enumerates files, DB, secrets handling, domain/URL rewrite,
mail suppression policy, cache and external integrations.

Staging defaults prevent accidental production email/payment/webhook
side effects through configurable safe-mode controls. Secrets may be
copied, substituted or withheld according to policy.

Preview domains/TLS/DNS are ordinary scoped resources with expiry
capability, not hidden unmanaged vhosts.
