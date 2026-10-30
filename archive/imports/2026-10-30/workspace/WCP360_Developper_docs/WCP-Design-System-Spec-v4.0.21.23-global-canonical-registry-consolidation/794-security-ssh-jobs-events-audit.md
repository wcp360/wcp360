# 794 --- Security / SSH Jobs, Events & Audit

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 2**

Ce document dérive des décisions et contrats déjà présents dans la
V3.5/V3.6/V3.7. Les règles `650–669` restent normatives et prioritaires.

Jobs : `malware.scan`, `security.policy.apply`, `ssh.server.apply`,
`ssh.key.add/remove`, `ban.action`.

Events : `malware.detected`, `security.finding_changed`,
`ssh.policy_changed`, `ssh.key_changed`, `intrusion.detected`.

Audit : actor, target, finding/policy/action, result ; secrets/key
material exclus.

## V4.0.7 normative deepening

- `SECOPS-407-11-SSH-SERVER-ANTI-LOCKOUT.md`
- `SECOPS-407-24-INTRUSION-FAIL2BAN-CROWDSEC.md`

Ces contrats approfondissent cette surface et conservent les exigences antérieures compatibles.
