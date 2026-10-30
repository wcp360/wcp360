# 1151 --- Action Registry Negative Tests

## Statut

**CANONICAL --- V4.0 IMPLEMENTATION FOUNDATION --- SPRINT 0**

Ce document matérialise le Sprint 0 défini par `1090-sprint0-plan.md`.
Les normes `650–669` et contrats `700–1093` restent prioritaires en cas
de conflit.

Doivent être refusés : action inconnue, `service_id` inconnu, paramètre
supplémentaire interdit, path absolu fourni au probe, traversal,
NUL/control chars, payload oversized, peer PHP, protocol downgrade,
timeout invalide, tentative d'injecter `;`, newline ou shell
metacharacters dans un champ enum/string borné.

Les tests démontrent également qu'aucune action Sprint 0 n'accepte
`command`, `binary`, `unit_name`, `shell`, `args[]` opaques ou path
utilisateur arbitraire.
