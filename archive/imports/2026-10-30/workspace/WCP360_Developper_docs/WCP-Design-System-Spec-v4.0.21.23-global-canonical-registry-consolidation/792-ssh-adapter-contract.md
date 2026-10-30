# 792 --- SSH Adapter Contract

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 2**

Ce document dérive des décisions et contrats déjà présents dans la
V3.5/V3.6/V3.7. Les règles `650–669` restent normatives et prioritaires.

## Server

Detect config/capabilities, render governed fragments where supported,
syntax validate, staged apply, reload, connectivity/recovery check.

## User access

authorized_keys, shell/SFTP-only/chroot capabilities selon architecture,
key fingerprint/metadata.

## Anti-lockout

Port/auth/root-policy changes nécessitent recovery path. Aucune clé
privée SSH n'est persistée en clair dans audit/log.

## V4.0.7 normative deepening

- `SECOPS-407-11-SSH-SERVER-ANTI-LOCKOUT.md`

Ces contrats approfondissent cette surface et conservent les exigences antérieures compatibles.
