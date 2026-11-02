# Future package verification

Define meaningful tests with the actual implementation, using synthetic data and disposable private targets. Every claimed version/OS/runtime/provider combination needs its own evidence.

Cover ordinary behavior, wrong tenant/role, revoked authority, stale resource versions, replay, input/output bounds, secret redaction, provider outages, uncertain effects and restart recovery. Verify independent Core hosting/control after module disablement, expiry, crash and removal. Performance results identify workload and environment.

AppInstall recipes additionally exercise install, update, supported rollback, backup/restore and dependency-aware removal; review upstream licensing and compatibility before release. Static planning checks do not establish a passing runtime or production qualification. Current instructions keep local executable tests stopped; use separately authorized remote verification for actual code.
