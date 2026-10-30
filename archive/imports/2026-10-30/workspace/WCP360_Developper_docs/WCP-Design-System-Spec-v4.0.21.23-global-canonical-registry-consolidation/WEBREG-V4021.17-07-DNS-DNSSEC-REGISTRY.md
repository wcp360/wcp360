# V4.0.21.17 --- DNS / DNSSEC Registry

Operations: `DNS-ZONE-CREATE`, `DNS-ZONE-DELETE`, `DNS-RECORD-CREATE`,
`DNS-RECORD-UPDATE`, `DNS-RECORD-DELETE`, `DNS-ZONE-IMPORT`,
`DNS-ZONE-EXPORT`, `DNSSEC-ENABLE`, `DNSSEC-DISABLE`, `DNSSEC-ROTATE`,
`DNS-RECONCILE`.

Typed records validate name/type/value/TTL and provider constraints.
Zone ownership and delegation are distinct facts. External DNS mode may
be read-only or guidance-only according to capability.

DNSSEC key material is privileged. DS publication state is tracked
separately from local signing state so WCP cannot claim DNSSEC healthy
before delegation is actually consistent.
