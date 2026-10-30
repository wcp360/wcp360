# V4.0.21.12 --- Global Security Invariants

1.  default deny;
2.  tenant boundary before resource access;
3.  least privilege and explicit scope;
4.  no arbitrary root shell;
5.  no user-controlled privileged filesystem path;
6.  secrets by reference and redaction;
7.  privileged mutations through closed actions;
8.  validate/stage/apply/verify/rollback for service config;
9.  idempotency and concurrency controls;
10. immutable/correlated audit evidence;
11. fail-safe security floor;
12. capability truthfulness: unavailable means unavailable;
13. desired state never masquerades as observed health;
14. DEMO cannot mutate real infrastructure;
15. support impersonation preserves initiator identity.
