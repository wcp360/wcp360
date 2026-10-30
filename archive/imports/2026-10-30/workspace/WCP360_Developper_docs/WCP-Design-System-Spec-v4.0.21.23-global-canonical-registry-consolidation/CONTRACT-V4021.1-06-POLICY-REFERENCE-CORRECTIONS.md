# V4.0.21.1 --- Explicit Policy Reference Registry

## Statut

**NORMATIVE CORRECTION**

  --------------------------------------------------------------------------------------
  Policy ID               Replaces vague wording  Required decision fields
  ----------------------- ----------------------- --------------------------------------
  `POL-ID-001`            identity uniqueness     normalized login uniqueness = global;
                          policy                  normalized primary email uniqueness =
                                                  global for login identities

  `POL-RET-001`           identity retention      disable immediately; sessions/tokens
                          policy                  revoke immediately; PII purge
                                                  eligibility after configured legal
                                                  retention; audit actor tombstone
                                                  retained

  `POL-DATA-001`          dataset retention       disposition enum `RETAIN/PURGE`;
                          policy                  retention-until; legal-hold; purge
                                                  verifier

  `POL-DNS-001`           DNS/TLS artifacts       managed-local records are removed only
                          policy                  by explicit delete plan; externally
                                                  managed DNS is never deleted by WCP

  `POL-AUD-001`           audit privacy class     payload fields classified
                                                  PUBLIC/OPERATIONAL/SENSITIVE/SECRET;
                                                  SECRET forbidden, SENSITIVE
                                                  redacted/hash/reference-only

  `POL-PLAN-001`          plan enforcement policy lowering below observed usage =\>
                                                  reject by default; explicit operator
                                                  migration plan required before
                                                  enforcement

  `POL-ANTIENUM-001`      anti-enumeration policy cross-tenant direct resource lookup
                                                  returns non-disclosing not-found
                                                  semantics; denial still recorded
                                                  internally
  --------------------------------------------------------------------------------------

A normative row must cite a `POL-*` identifier rather than say merely
`according to policy`.
