# 732 --- Network Jobs / Events / Audit

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 1**

Ce document transforme les décisions V3.6 déjà acceptées en contrat de
développement. Il ne remplace pas les règles de
`MASTER-SECURE-DEVELOPMENT-STANDARD.md`.

Jobs : `network.config.apply`, `firewall.policy.apply`,
`hostname.change`, `resolver.change`.

Events : `network.interface_changed`, `network.connectivity_failed`,
`firewall.policy_changed`.

Audit : diff redacted, node, actor, recovery plan, validation result et
connectivity result.

## V4.0.14 normative deepening

- `BOOT-4014-07-NETWORK-IPAM-BOOTSTRAP.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
