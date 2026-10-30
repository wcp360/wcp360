# V4.0.18 — Job → RPC → Adapter Coverage Audit

## Statut
**NORMATIVE AUDIT — V4.0.18 — NO CODE**

## Base auditée
V4.0.17 et ses 30 contrats `IMPL-4017-*`. Cette passe n'invente pas les mappings absents : `PASS` signifie explicitement supporté par le corpus ; `PARTIAL` signifie que la surface existe mais que la chaîne n'est pas fermée ; `N/A` doit être justifié ; `BLOCKED` empêche le Repository Entry Gate.

## PASS
Les catégories de Jobs, retry classes, locking, timeout ambiguity, RPC allowlist et interface d'adapter sont bien définies.

## PARTIAL
Le corpus ne fournit pas encore une ligne canonique pour chaque Job avec : locks, retry class, timeout class, cancel semantics, RPC sequence/N/A, adapter method, verification evidence, compensation/recovery et audit event.

## Risque
Sans ce registre, deux développeurs peuvent implémenter le même Job avec des frontières root ou des stratégies de retry différentes.
