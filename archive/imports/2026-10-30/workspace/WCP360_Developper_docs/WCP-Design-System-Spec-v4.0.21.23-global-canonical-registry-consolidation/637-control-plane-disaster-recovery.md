# 637 --- WCP Control Plane Disaster Recovery

## Statut de provenance

**\[V3.5 --- architecture proposée à partir des lacunes de la V3.4\]**
Ce contrat n'était pas spécifié en profondeur dans les documents
précédents. Il doit être validé par l'architecture technique avant
implémentation.

## Scope

Sauvegarder et restaurer **WCP lui-même** : base/state, configuration,
secrets/keys selon stratégie, plugin metadata, identities, audit
metadata et références d'agents.

## Runbook

Declare incident → select recovery point → restore isolated/standby
control plane → recover trust/keys → reconnect/re-enroll nodes as
required → consistency checks → resume jobs safely.

## Règle

Les backups clients ne remplacent pas le backup du control plane.

## V4.0.7 normative deepening

- `SECOPS-407-19-DISASTER-RECOVERY.md`

Ces contrats approfondissent cette surface et conservent les exigences antérieures compatibles.
