# V4.0.21.11 --- OS / WCP / Service Update & Patching Contract

## Classes

OS security updates, service packages, runtime versions, WCP control
plane, WCP privileged daemon, signatures/rulesets.

## Workflow

check → inventory → compatibility → impact → maintenance/drain if needed
→ backup/checkpoint → apply → service validation → health →
commit/rollback path.

## Reboot

Reboot-required est un état explicite. Le scheduler distingue reboot
automatique autorisé, maintenance window et admin approval.

## WCP self-update

Version compatibility matrix entre UI/API/control-plane/daemon/schema.
Database migrations sont forward/backward policy aware. Une mise à jour
ne remplace jamais aveuglément un binaire sans
provenance/checksum/signature policy.
