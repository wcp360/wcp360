# ADR-013 --- DNS Stack Baseline

## Statut

**ACCEPTED --- V3.6 Phase 1**

Cette décision est un **choix d'architecture WCP**. Lorsqu'une donnée
externe est citée, elle sert uniquement à vérifier la viabilité du choix
et ne remplace pas la décision produit.

## Décision

**BIND 9** est le moteur authoritative local baseline V1. Le contrat
`DNSAdapter` reste fournisseur-agnostique ; PowerDNS/Knot/NSD peuvent
être ajoutés sans changer WCP-USER.

Primary/secondary, DNSSEC, AXFR/IXFR/TSIG et records avancés sont
certifiés uniquement lorsque l'adapter déclare la capability.
