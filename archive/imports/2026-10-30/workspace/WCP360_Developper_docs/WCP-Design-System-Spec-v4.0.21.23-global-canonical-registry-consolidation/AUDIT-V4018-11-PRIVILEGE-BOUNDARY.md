# V4.0.18 — Privilege Boundary Audit

## Statut
**NORMATIVE AUDIT — V4.0.18 — NO CODE**

## Base auditée
V4.0.17 et ses 30 contrats `IMPL-4017-*`. Cette passe n'invente pas les mappings absents : `PASS` signifie explicitement supporté par le corpus ; `PARTIAL` signifie que la surface existe mais que la chaîne n'est pas fermée ; `N/A` doit être justifié ; `BLOCKED` empêche le Repository Entry Gate.

## PASS
La frontière Browser → PHP non-root → Worker → Unix socket → `wcpd` root est cohérente. Les RPC génériques `Exec/Shell/RunCommand/WriteAnyFile/ReadAnyFile` sont interdits.

## PARTIAL
Le catalogue RPC doit encore associer chaque méthode à : caller capability interne, allowed target classes/path roots, input constraints, idempotency behavior et verification evidence.

## Verdict
Architecture de privilège = PASS ; **method-level authorization registry = PARTIAL**.
