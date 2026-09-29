# WCP360 V2.5.3.19 — Backups & Restore Functional Depth

## Style lock
V2.5.3.14 remains the visual baseline. No shell redesign was introduced.

## Developed family
- backups overview
- restore points
- backup policy
- backup storage
- restore testing
- backup activity
- restore wizard
- restore impact review
- WCP-ADMIN backup infrastructure

## Recovery / security rules
- existence of a backup is not sufficient; verification and restore testing are separate controls;
- off-site copies use a separate failure domain;
- encryption posture is visible;
- full production restore is treated as high impact;
- impact is previewed before execution;
- typed confirmation and re-auth/MFA can be required by policy;
- a pre-restore snapshot is created whenever technically feasible;
- partial restore is supported for files and databases;
- restore-to-staging is a first-class safer option;
- restore executes as an observable background job;
- repository capacity and global backup worker health remain WCP-ADMIN responsibilities.

## Next family
Security & Access.