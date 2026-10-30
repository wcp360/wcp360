# 790 --- Security / SSH Resource Model

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 2**

Ce document dérive des décisions et contrats déjà présents dans la
V3.5/V3.6/V3.7. Les règles `650–669` restent normatives et prioritaires.

## Ressources

`FirewallPolicy`, `BanPolicy`, `MalwareScanPolicy`, `MalwareFinding`,
`SshServerPolicy`, `SshUserAccess`, `SshPublicKey`, `SecurityFinding`,
`MfaPolicy`, `SecurityEvent`.

## Séparation

SSH server global et SSH access utilisateur restent distincts. Firewall
est partagé avec Network mais Security porte policies/findings et
posture.

## Secrets

Private SSH host/user keys ne sont jamais exposées par lecture API.

## V4.0.7 normative deepening

- `SECOPS-407-10-SSH-USER-KEYS-SHELL.md`
- `SECOPS-407-21-SECURITY-CENTER-FINDINGS.md`

Ces contrats approfondissent cette surface et conservent les exigences antérieures compatibles.
