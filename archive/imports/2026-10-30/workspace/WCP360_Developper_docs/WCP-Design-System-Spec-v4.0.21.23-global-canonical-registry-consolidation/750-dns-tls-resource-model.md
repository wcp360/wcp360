# 750 --- DNS / TLS Resource Model

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 2**

Ce document dérive des décisions et contrats déjà présents dans la
V3.5/V3.6/V3.7. Les règles `650–669` restent normatives et prioritaires.

## Ressources

`DnsZone`, `DnsRecord`, `DnsZoneRole`, `DnsTransferPolicy`,
`TsigKeyRef`, `DnssecState`, `DnsProviderConnection`, `Certificate`,
`AcmeAccount`, `CertificateOrder`, `CertificateDeployment`.

## Relations

Domain → DnsZone → Records. Certificate → Domain/SANs → ACME order →
challenge → deployment targets.

## État

Serial, desired/observed records, provider/backend, DNSSEC health,
certificate expiry/renewal/deployment state.

## Baseline

BIND 9 est le backend authoritative V1 retenu par la V3.6. Les
fournisseurs externes restent des `DNSProviderAdapter`.
