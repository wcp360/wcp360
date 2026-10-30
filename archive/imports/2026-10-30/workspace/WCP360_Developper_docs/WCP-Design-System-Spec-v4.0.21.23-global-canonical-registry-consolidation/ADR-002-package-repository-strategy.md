# ADR-002 --- Package & Repository Strategy

## Statut

**ACCEPTED --- V3.6 Phase 1**

Cette décision est un **choix d'architecture WCP**. Lorsqu'une donnée
externe est citée, elle sert uniquement à vérifier la viabilité du choix
et ne remplace pas la décision produit.

## Décision

Le `OSPackageAdapter` est la seule couche autorisée à installer/mettre à
jour des paquets. `apt` et `dnf` ne sont jamais appelés depuis PHP.

Ordre de préférence : dépôts officiels OS → dépôts tiers explicitement
approuvés/signés pour une capability nécessaire → package WCP signé.
Chaque source possède ID, key/fingerprint, URL, distribution scope et
policy.

Les changements de repository sont des actions WCP-ADMIN auditées. Les
versions critiques peuvent être pin/held par policy ; WCP expose la
provenance réelle du paquet.

## V4.0.12 normative deepening

- `LIFE-4012-08-OS-PACKAGE-LIFECYCLE.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.
