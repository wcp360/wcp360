# 1081 --- Production Package Layout

## Statut

**CANONICAL --- DEVELOPER BLUEPRINT --- V3.9**

Ce document traduit les contrats `IMPLEMENTATION-READY` V3.8 en
organisation de code et ordre de construction. Il ne modifie pas les
décisions normatives de sécurité `650–669`.

Le packaging transforme les artefacts du repository vers :
`/opt/wcp/releases/<version>`, `/etc/wcp`, `/var/lib/wcp`,
`/var/cache/wcp`, `/var/log/wcp`, `/run/wcp`.

Units systemd, tmpfiles/sysusers lorsque adaptés, log rotation,
migrations et pre/post-install sont versionnés sous `deploy/`.

Upgrade : preflight → DB expand migration → install release → switch →
health → contract migration/cleanup ultérieure.

## V4.0.12 normative deepening

- `LIFE-4012-08-OS-PACKAGE-LIFECYCLE.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.
