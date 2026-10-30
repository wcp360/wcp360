# 754 --- DNS / TLS Jobs, Events & Audit

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 2**

Ce document dérive des décisions et contrats déjà présents dans la
V3.5/V3.6/V3.7. Les règles `650–669` restent normatives et prioritaires.

Jobs : `dns.zone.apply`, `dns.dnssec.enable/rotate`,
`dns.transfer.apply`, `certificate.issue`, `certificate.renew`,
`certificate.deploy`.

Events : `dns.zone_changed`, `dnssec.state_changed`,
`certificate.issued`, `certificate.renewal_failed`,
`certificate.deployed`.

Audit : actor, domain/zone/certificate IDs, provider/backend, diff
metadata, challenge type, result ; jamais clé privée/TSIG secret.
