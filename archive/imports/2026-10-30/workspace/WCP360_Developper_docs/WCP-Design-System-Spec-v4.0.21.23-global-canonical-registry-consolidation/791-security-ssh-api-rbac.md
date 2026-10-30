# 791 --- Security / SSH API & RBAC

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 2**

Ce document dérive des décisions et contrats déjà présents dans la
V3.5/V3.6/V3.7. Les règles `650–669` restent normatives et prioritaires.

## API

`/api/v1/security/findings`, `/malware/scans`, `/firewall/policies`,
`/ssh/server`, `/ssh/access`, `/ssh/keys`.

## Permissions

Admin : `security.read/manage`, `malware.scan/manage`,
`ssh.server.manage`, `ssh.access.manage`. User : clés/accès SSH de son
propre compte seulement si plan/policy l'autorise.

## Step-up

Changement root login/password auth/firewall management access ou
récupération MFA peut exiger step-up.

## V4.0.7 normative deepening

- `SECOPS-407-10-SSH-USER-KEYS-SHELL.md`
- `SECOPS-407-24-INTRUSION-FAIL2BAN-CROWDSEC.md`

Ces contrats approfondissent cette surface et conservent les exigences antérieures compatibles.

## V4.0.9 normative deepening

- `API-409-01-API-FIRST-CANONICAL-CONTRACT.md`
- `API-409-02-API-AUTH-SERVICE-ACCOUNTS.md`
- `API-409-05-IDEMPOTENCY-MUTATIONS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
