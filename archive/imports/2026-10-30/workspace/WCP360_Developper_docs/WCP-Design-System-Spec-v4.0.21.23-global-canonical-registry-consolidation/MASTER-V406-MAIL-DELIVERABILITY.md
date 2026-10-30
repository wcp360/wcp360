# MASTER V4.0.6 --- Mail & Deliverability Deep Specification

## Architecture cible

Internet → Postfix inbound → anti-spam/antivirus → Dovecot
delivery/storage → IMAP/POP3/Webmail.

Client authentifié → Submission/SASL → sender authorization → outbound
policy/rate limits → DKIM signing → remote MX.

DNS complète cette chaîne avec MX, SPF, DKIM, DMARC,
AutoConfig/AutoDiscover et, optionnellement, MTA-STS/TLS-RPT.

## Principes

-   Pas d'open relay.
-   Port 25 et submission sont des rôles différents.
-   Sender authorization empêche le spoofing cross-tenant.
-   Mailbox quota et host disk pressure sont deux problèmes différents.
-   DKIM private key reste secrète.
-   SPF/DMARC présents ne signifient pas nécessairement valides/aligned.
-   Scanner indisponible n'équivaut pas à message clean.
-   Queue management est scopé et protège le contenu.
-   WCP observe la réputation ; il ne garantit jamais l'inbox placement.
-   Le support/impersonation n'accorde pas implicitement la lecture du
    contenu mail.

## Suite

**V4.0.7 --- Files, Backup, Security & Recovery Deep Specification** :
File Manager, SFTP/FTP, permissions/ACL, archives/editor,
backups/snapshots/remote providers, restore/disaster recovery,
malware/AV, WAF/security center, Fail2ban/CrowdSec, SSH keys, secret
handling et cross-tenant filesystem security.

## V4.0.14 normative deepening

- `BOOT-4014-12-MAIL-STACK-BOOTSTRAP.md`
- `BOOT-4014-13-MAIL-SECURITY-ANTISPAM.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
