# ADR-009 --- Browser Support Policy

## Statut

**ACCEPTED --- V3.6 Phase 1**

Cette décision est un **choix d'architecture WCP**. Lorsqu'une donnée
externe est citée, elle sert uniquement à vérifier la viabilité du choix
et ne remplace pas la décision produit.

## Décision

WCP Web supporte les **deux dernières versions stables majeures** de
Chrome/Chromium, Firefox, Edge et Safari, plus Safari courant sur
iOS/iPadOS lorsque l'interface est utilisée sur mobile/tablette.

Pas de support Internet Explorer. Les fonctions critiques ne doivent pas
dépendre d'une API expérimentale sans fallback.

## Tests

CI/E2E cible Chromium + Firefox ; campagne release couvre Edge/Safari
pour les parcours critiques.
