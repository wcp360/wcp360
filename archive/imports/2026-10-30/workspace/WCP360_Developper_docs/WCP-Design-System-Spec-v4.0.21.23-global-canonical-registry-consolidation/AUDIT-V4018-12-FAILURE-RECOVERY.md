# V4.0.18 — Failure, Retry & Recovery Audit

## Statut
**NORMATIVE AUDIT — V4.0.18 — NO CODE**

## Base auditée
V4.0.17 et ses 30 contrats `IMPL-4017-*`. Cette passe n'invente pas les mappings absents : `PASS` signifie explicitement supporté par le corpus ; `PARTIAL` signifie que la surface existe mais que la chaîne n'est pas fermée ; `N/A` doit être justifié ; `BLOCKED` empêche le Repository Entry Gate.

## PASS
Timeout ambigu, reconciliation-before-retry, rollback de génération, repair/rescue, disk/inode/OOM et restore sont couverts.

## PARTIAL
La retry class et le recovery path ne sont pas assignés individuellement à chaque Job du catalogue. Cette décision ne doit pas être laissée au worker implementation.
