# 774 --- Database / Cache Jobs, Events & Audit

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 2**

Ce document dérive des décisions et contrats déjà présents dans la
V3.5/V3.6/V3.7. Les règles `650–669` restent normatives et prioritaires.

Jobs : `database.create/drop/import/export/restore`,
`database.user.rotate`, `cache.instance.apply`.

Events : `database.created/dropped`, `database.grant_changed`,
`database.restore_completed`, `cache.instance_changed`.

Audit : metadata et grants, jamais dump complet, query content
arbitraire ou password.
