# V4.0.21.21 --- WCP / OS / Provider Update Orchestration

Update classes are separated: WCP control-plane, OS packages/security
updates, runtime/provider packages, rules/signatures and application
updates.

Pipeline: inventory → compatibility → advisory/security impact →
dependency plan → preflight/backup/checkpoint → maintenance/drain if
required → staged apply → migrations → service health → observed
verification → commit/rollback or recovery.

A package manager is not exposed as arbitrary root install to WCP-USER.
Automatic security update policy is admin-controlled and
maintenance-aware.
