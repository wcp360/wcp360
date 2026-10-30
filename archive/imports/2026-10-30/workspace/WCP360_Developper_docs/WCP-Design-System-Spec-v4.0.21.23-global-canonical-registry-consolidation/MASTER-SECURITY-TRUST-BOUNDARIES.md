# MASTER SECURITY & TRUST BOUNDARIES

## Zones

1.  Browser/user device.
2.  Public WCP edge/API.
3.  Control-plane services.
4.  State database/cache/queue.
5.  Secret/key boundary.
6.  Node agent.
7.  Managed services/backends.
8.  Third-party providers/connectors.
9.  Telemetry/SIEM destinations.

## Règles

Aucune zone n'est implicitement trusted. AuthN/AuthZ, encryption, least
privilege et validation sont appliqués à chaque frontière pertinente.

## SSRF

Connecteurs URL, S3-compatible, webhooks, migrations et external
providers doivent appliquer une politique egress/SSRF.

## Supply chain

Releases WCP, agents, plugins et packages/adapters nécessitent
provenance/intégrité/signature selon le mécanisme retenu.
