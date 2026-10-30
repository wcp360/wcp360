# V4.0.18 — RBAC & Scope Coverage Audit

## Statut
**NORMATIVE AUDIT — V4.0.18 — NO CODE**

## Base auditée
V4.0.17 et ses 30 contrats `IMPL-4017-*`. Cette passe n'invente pas les mappings absents : `PASS` signifie explicitement supporté par le corpus ; `PARTIAL` signifie que la surface existe mais que la chaîne n'est pas fermée ; `N/A` doit être justifié ; `BLOCKED` empêche le Repository Entry Gate.

## PASS
Deny-by-default, capability+scope, tenant enforcement au service/data layer, real/effective actor et impersonation sont fermés conceptuellement.

## PARTIAL
Les rôles sont volontairement des bundles, mais le catalogue exact des capabilities CORE et leur scope admissible n'est pas exhaustif. Les exemples `domain.*`, `mailbox.*`, etc. ne constituent pas encore une registry normative.

## Closure requise
Capability ID stable → actions autorisées → scopes → roles default bundles → step-up requirement → audit requirement.
