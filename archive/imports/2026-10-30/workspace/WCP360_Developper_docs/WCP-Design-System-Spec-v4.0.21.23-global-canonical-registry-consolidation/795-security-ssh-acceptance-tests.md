# 795 --- Security / SSH Acceptance Tests

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 2**

Ce document dérive des décisions et contrats déjà présents dans la
V3.5/V3.6/V3.7. Les règles `650–669` restent normatives et prioritaires.

-   SSH config validation before reload ;
-   anti-lockout ;
-   authorized key isolation ;
-   SFTP-only/chroot capability honesty ;
-   malware scan scoped ;
-   quarantine permission separation ;
-   Fail2ban/CrowdSec adapter capability detection ;
-   firewall/security interaction ;
-   cross-tenant denial ;
-   secrets absent from logs/audit ;
-   root capability review gate.

## V4.0.7 normative deepening

- `SECOPS-407-28-CROSS-TENANT-FILESYSTEM-TESTS.md`
- `SECOPS-407-29-SECURITY-BACKUP-E2E.md`

Ces contrats approfondissent cette surface et conservent les exigences antérieures compatibles.
