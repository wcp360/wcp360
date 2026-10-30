# 730 --- Network Resource & API Contract

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 1**

Ce document transforme les décisions V3.6 déjà acceptées en contrat de
développement. Il ne remplace pas les règles de
`MASTER-SECURE-DEVELOPMENT-STANDARD.md`.

## Ressources

`NetworkInterface`, `IPAddress`, `Route`, `ResolverConfig`,
`HostnameConfig`, `Vlan`, `Bond`, `Bridge`, `FirewallPolicy`,
`FirewallRule`.

## API

Lecture réseau par node. Mutations `stage` puis `apply` via Job pour les
changements susceptibles de couper l'accès.

## Permissions

`network.read`, `network.manage`, `firewall.read`, `firewall.manage`.

## Anti-lockout

Toute modification management IP/route/firewall/SSH-adjacent exige
preflight, snapshot/recovery et connectivity confirmation.

## V4.0.9 normative deepening

- `API-409-01-API-FIRST-CANONICAL-CONTRACT.md`
- `API-409-02-API-AUTH-SERVICE-ACCOUNTS.md`
- `API-409-05-IDEMPOTENCY-MUTATIONS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.

## V4.0.14 normative deepening

- `BOOT-4014-07-NETWORK-IPAM-BOOTSTRAP.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
