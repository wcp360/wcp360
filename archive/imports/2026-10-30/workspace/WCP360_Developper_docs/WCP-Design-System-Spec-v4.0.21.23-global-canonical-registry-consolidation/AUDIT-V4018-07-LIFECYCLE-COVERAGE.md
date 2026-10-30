# V4.0.18 — Lifecycle & State-machine Coverage Audit

## Statut
**NORMATIVE AUDIT — V4.0.18 — NO CODE**

## Base auditée
V4.0.17 et ses 30 contrats `IMPL-4017-*`. Cette passe n'invente pas les mappings absents : `PASS` signifie explicitement supporté par le corpus ; `PARTIAL` signifie que la surface existe mais que la chaîne n'est pas fermée ; `N/A` doit être justifié ; `BLOCKED` empêche le Repository Entry Gate.

## PASS
Provision, suspension, deletion et Job lifecycle ont des state machines canoniques. Desired/observed/lifecycle/job state sont distincts.

## PARTIAL
Tous les 33 types CORE ne déclarent pas explicitement quelle state machine ils utilisent, quels états sont non applicables et quelles transitions sont autorisées par action.

## Risque
Des ressources control-plane simples pourraient recevoir des états inutiles tandis que des ressources externes pourraient manquer `DEGRADED/UNKNOWN`.
