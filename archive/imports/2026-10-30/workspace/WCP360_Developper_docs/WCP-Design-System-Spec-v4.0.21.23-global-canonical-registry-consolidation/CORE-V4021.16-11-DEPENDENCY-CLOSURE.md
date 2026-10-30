# V4.0.21.16 --- Core Dependency Closure

## Required ordering

Identity/Auth → RBAC/Scope → Reseller/Account → Plan/Entitlement/Quota →
IsolationProfile → SystemIdentity → ManagedRoot → service modules.

Job/Decision/Audit are transversal and available before service
provisioning.

## Failure behavior

Auth/RBAC unavailable: fail closed for mutations. Entitlement service
unavailable: no privilege expansion. Isolation capability unavailable:
provisioning requiring it is BLOCKED. Audit sink degraded: security
events remain durably buffered according to platform policy; no silent
loss.
