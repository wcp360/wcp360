# Specification Audit Checklist

## Security
- [ ] No generic root command endpoint exists.
- [ ] Every privileged operation is allowlisted.
- [ ] Every secret field is classified.
- [ ] Existing secrets are never re-displayed.
- [ ] Impersonation preserves real actor.
- [ ] Break-glass has hard expiry.
- [ ] Approval does not bypass authorization.
- [ ] DEMO is restrictive, never additive.

## Filesystem
- [ ] Every daemon file target is bounded.
- [ ] Ownership/mode expectations are explicit.
- [ ] Generated files have provenance.
- [ ] User file manager cannot escape application root.
- [ ] Secrets are not stored in ordinary editable files by default.

## Jobs
- [ ] Retry semantics specified.
- [ ] Unknown-outcome handling specified.
- [ ] Dependencies specified.
- [ ] Cancellation semantics specified.
- [ ] Concurrency/locking specified.

## API
- [ ] Scope model explicit.
- [ ] Error envelope consistent.
- [ ] Idempotency behavior explicit.
- [ ] Pagination strategy explicit.
- [ ] Rate-limit classes defined.
- [ ] Audit parity with UI.

## Configuration
- [ ] Validation mechanism known.
- [ ] Diff preview specified.
- [ ] reload vs restart explicit.
- [ ] rollback/reconciliation specified.
- [ ] service-specific schema versioned.

## Traceability
- [ ] Every action → permission.
- [ ] Every mutation → audit.
- [ ] Every long operation → job.
- [ ] Every privileged mutation → daemon operation.
- [ ] Every failure → canonical error class.