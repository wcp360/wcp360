# First Boot & Finalization Wizard

## Goal

A technically installed server is not “ready” until security and operational settings are finalized.

## Wizard sections

### 1. System identity
- hostname/FQDN;
- server display name;
- timezone;
- locale.

### 2. Administrator
- initial WCP-ADMIN account;
- strong password/passkey;
- MFA enrollment;
- recovery codes.

### 3. Network
- public/private IP verification;
- panel reachability;
- firewall admin path;
- DNS resolver review.

### 4. Web stack
- Nginx;
- PHP versions;
- default runtime policy.

### 5. Database
- MariaDB/PostgreSQL availability;
- local-only defaults;
- root/admin credential handling.

### 6. Mail
Optional enablement:
- MTA;
- IMAP;
- antispam;
- hostname/rDNS readiness warnings.

### 7. Backups
- repository;
- encryption;
- schedule defaults;
- restore test policy.

### 8. Notifications
- admin email;
- provider;
- critical delivery test.

### 9. Security
- privileged session policy;
- break-glass policy;
- approval defaults;
- IP access restrictions.

### 10. Final self-test
Validate:
- daemon;
- socket;
- web/API;
- DB connectivity;
- service validation;
- DNS;
- backup target;
- notification delivery;
- audit writes.

## Completion

System becomes `READY` only if mandatory checks pass.

Optional subsystems may remain disabled but must be explicitly marked.