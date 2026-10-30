# 1041 --- Job / Action Catalog Blueprint

## Statut

**CANONICAL --- DEVELOPER BLUEPRINT --- V3.9**

Ce document traduit les contrats `IMPLEMENTATION-READY` V3.8 en
organisation de code et ordre de construction. Il ne modifie pas les
décisions normatives de sécurité `650–669`.

Chaque action async possède : `action_id`, owning module, input schema,
permission, idempotency class, timeout, retry policy, locks,
adapter/capability requirements, audit class, recovery semantics.

Une action nécessitant root référence une action fermée du
`ActionRegistry`; elle ne transporte jamais une commande libre.

## V4.0.10 normative deepening

- `OPS-4010-06-LOGGING-CANONICAL.md`
- `OPS-4010-07-LOG-PIPELINE-RETENTION.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
