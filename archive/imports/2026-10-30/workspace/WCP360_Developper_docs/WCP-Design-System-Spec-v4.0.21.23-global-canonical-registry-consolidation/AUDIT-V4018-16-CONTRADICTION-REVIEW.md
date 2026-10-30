# V4.0.18 — Cross-contract Contradiction Review

## Statut
**NORMATIVE AUDIT — V4.0.18 — NO CODE**

## Base auditée
V4.0.17 et ses 30 contrats `IMPL-4017-*`. Cette passe n'invente pas les mappings absents : `PASS` signifie explicitement supporté par le corpus ; `PARTIAL` signifie que la surface existe mais que la chaîne n'est pas fermée ; `N/A` doit être justifié ; `BLOCKED` empêche le Repository Entry Gate.

## Contradictions bloquantes détectées
**Aucune contradiction architecturale bloquante démontrée dans les 30 contrats V4.0.17 examinés.**

## Tensions à clarifier
- `Toute mutation privilégiée passe par Job puis wcpd` doit rester distinct de mutations purement control-plane qui n'ont ni Job système ni RPC : elles nécessitent une décision `N/A`, pas un faux Job.
- `node` est CORE alors que V1 est single-node : son lifecycle doit être limité et ne pas introduire implicitement le cluster.
- `secret_ref` est une ressource canonique interne mais ne doit pas devenir une API de lecture plaintext.
- `external-unmanaged` doit empêcher `reconcile` destructif.

## Verdict
Clarifications nécessaires, pas de refonte.
