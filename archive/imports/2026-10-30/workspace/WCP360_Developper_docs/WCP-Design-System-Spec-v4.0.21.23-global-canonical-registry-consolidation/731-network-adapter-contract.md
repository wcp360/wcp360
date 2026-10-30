# 731 --- Network Adapter Contract

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 1**

Ce document transforme les décisions V3.6 déjà acceptées en contrat de
développement. Il ne remplace pas les règles de
`MASTER-SECURE-DEVELOPMENT-STANDARD.md`.

Adapters possibles déjà définis : systemd-networkd, NetworkManager,
Netplan selon hôte ; firewall nftables/firewalld.

## MUST

Une seule source de vérité active par capability. L'adapter expose
`detect`, `render/stage`, `validate`, `diff`, `apply`,
`connectivity_check`, `recover`.

## Interdit

Modifier simultanément firewalld et nftables brut sans ownership
explicite.

## V4.0.14 normative deepening

- `BOOT-4014-07-NETWORK-IPAM-BOOTSTRAP.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
