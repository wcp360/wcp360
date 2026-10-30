# V4.0.21.19 --- Deliverability / Reputation Diagnostics

Diagnostics correlate MX, SPF, DKIM, DMARC, PTR/rDNS, HELO/EHLO
alignment, TLS capability, outbound source IP, queue behavior and
provider-visible errors.

WCP distinguishes configuration correctness from reputation. It must not
claim an IP/domain has good reputation merely because DNS/authentication
is correct.

Optional adapters may surface blocklist/reputation signals under their
own terms/capabilities. Remediation recommendations are advisory unless
an explicit operation is approved.
