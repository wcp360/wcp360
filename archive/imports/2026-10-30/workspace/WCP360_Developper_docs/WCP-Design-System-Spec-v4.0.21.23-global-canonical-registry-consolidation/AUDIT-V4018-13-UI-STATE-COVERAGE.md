# V4.0.18 — UI/API State Contract Audit

## Statut
**NORMATIVE AUDIT — V4.0.18 — NO CODE**

## Base auditée
V4.0.17 et ses 30 contrats `IMPL-4017-*`. Cette passe n'invente pas les mappings absents : `PASS` signifie explicitement supporté par le corpus ; `PARTIAL` signifie que la surface existe mais que la chaîne n'est pas fermée ; `N/A` doit être justifié ; `BLOCKED` empêche le Repository Entry Gate.

## PASS
La UI doit refléter desired/observed/lifecycle/Job state et ne pas inventer un succès optimiste pour une mutation système.

## PARTIAL
Chaque resource/action CORE doit encore déclarer les états UI obligatoires : loading, queued, running, verifying, success, warning/degraded, blocked, failed, stale/unknown et permission denied lorsque pertinents.
