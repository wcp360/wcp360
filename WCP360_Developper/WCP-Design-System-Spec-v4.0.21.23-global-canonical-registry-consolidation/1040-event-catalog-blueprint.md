# 1040 --- Domain Event Catalog Blueprint

## Statut

**CANONICAL --- DEVELOPER BLUEPRINT --- V3.9**

Ce document traduit les contrats `IMPLEMENTATION-READY` V3.8 en
organisation de code et ordre de construction. Il ne modifie pas les
décisions normatives de sécurité `650–669`.

Format : `<module>.<resource>.<past_tense>` ou événements stables déjà
contractés (`job.started`, `certificate.issued`, etc.).

Envelope : event_id, type, schema_version, occurred_at, actor_ref si
applicable, tenant_ref, resource_ref, correlation_id, causation_id,
payload redacted.

Les events décrivent un fait ; ils ne sont pas des commandes. Les
consommateurs doivent tolérer retry/duplication.

## V4.0.10 normative deepening

- `OPS-4010-06-LOGGING-CANONICAL.md`
- `OPS-4010-07-LOG-PIPELINE-RETENTION.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
