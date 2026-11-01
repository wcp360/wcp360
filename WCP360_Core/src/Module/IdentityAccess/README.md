# IdentityAccess

**Status: a bounded development authentication candidate is implemented. Complete identity/access management and production qualification remain unfinished.**

Local authentication, MFA, credentials, sessions, authorization, revocation and administrator recovery.

Start here when changing: **Identity, roles, session and recovery behavior.**

| Implemented candidate source | Responsibility |
| :--- | :--- |
| [Domain/Principal.php](Domain/Principal.php) | Candidate identity and separate administrator/user roles. |
| [Infrastructure/PrincipalProvider.php](Infrastructure/PrincipalProvider.php) | Context-bound identity lookup and session refresh; inactive accounts cannot retain access. |
| [Shared security](../../Shared/Security/README.md) | Runtime separation, session boundaries and bounded session lifetimes. |
| [Security configuration](../../../config/packages/security.yaml) | Password hashing, CSRF-protected form login/logout and login throttling. |

The candidate checks active tenant membership for the single `candidate.hosting.plan` permission through [WorkbenchStore](../Operations/Infrastructure/WorkbenchStore.php). It does not yet provide a general permission/delegation engine. Account and tenant lifecycle, plans and quotas belong to [Tenancy](../Tenancy/README.md).

MFA enrollment/challenges, passkeys, access recovery, sensitive-action reauthentication, service accounts, reseller delegation and the complete permission lifecycle remain to be implemented and qualified. The shared MFA policy declaration is not an executable MFA flow. Candidate code and passing tests do not establish production readiness.

Use the common [module layout](../README.md) for Domain, Application, Infrastructure and UI code. Hosting resource policy stays with its owning domain; reuse the common authorization boundary.

- [Core roadmap and completion estimate](../../../ROADMAP.md)
- [Core navigation index](../../../NAVIGATION.md)
- [System-service resources](../../../resources/services/README.md)
- [Core implementation and acceptance](../../../../documentation/development/CORE-ENGINE.md)
