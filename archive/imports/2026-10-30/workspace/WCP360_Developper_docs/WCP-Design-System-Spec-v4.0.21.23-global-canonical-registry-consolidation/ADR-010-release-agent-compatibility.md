# ADR-010 --- WCP Release & Agent Compatibility

## Statut

**ACCEPTED --- V3.6 Phase 1**

Cette décision est un **choix d'architecture WCP**. Lorsqu'une donnée
externe est citée, elle sert uniquement à vérifier la viabilité du choix
et ne remplace pas la décision produit.

## Décision

Canal initial : **Stable**. Preview/edge pourra être ajouté plus tard.
SemVer pour releases WCP.

Le control plane N supporte les agents N et N-1 pendant une fenêtre
d'upgrade ; un agent N+1 n'est pas accepté par un control plane N sauf
compatibilité explicitement déclarée.

Les protocoles agent/API ont version/capabilities négociées. Upgrade :
control plane d'abord lorsque matrice le permet, puis agents par vagues
avec health gates.

## V4.0.12 normative deepening

- `LIFE-4012-14-COMPATIBILITY-MATRIX.md`
- `LIFE-4012-01-PLATFORM-RELEASE-MODEL.md`
- `LIFE-4012-02-RELEASE-MANIFEST-ARTIFACTS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.
