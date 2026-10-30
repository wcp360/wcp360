# MASTER V4.0.14 --- Installation, Finalization, Repair & Server Lifecycle

## Chaîne canonique

Supported machine → Preflight → Verified installer → Bootstrap → First
Boot → Setup Wizard → Service Profiles → Security/Backup/Observability →
Functional Verification → Production Readiness →
READY/READY_WITH_WARNINGS/NOT_READY.

## Après installation

Reconfigure → Diagnose → Repair/Reconcile → Rescue/Recovery →
Transfer/Migrate → Decommission.

## Mail

Mail bootstrap intègre désormais explicitement antispam, antivirus
policy, SPF/DKIM/DMARC/ARC, Rspamd, quarantine, outbound
abuse/reputation et client autoconfiguration.

## Invariant final

`installation completed` ne doit jamais être utilisé comme synonyme de
`server production ready`.

## Suite

V4.0.15 doit être un **re-audit**, pas une nouvelle vague de
fonctionnalités.
