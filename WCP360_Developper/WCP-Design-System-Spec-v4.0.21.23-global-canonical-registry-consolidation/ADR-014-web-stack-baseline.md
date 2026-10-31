# ADR-014 --- Web/PHP Stack Baseline

## Statut

**ACCEPTED --- V3.6 Phase 1**

Cette décision est un **choix d'architecture WCP**. Lorsqu'une donnée
externe est citée, elle sert uniquement à vérifier la viabilité du choix
et ne remplace pas la décision produit.

## Décision

Nginx et Apache sont tous deux supportés comme WebServerAdapter.
**Nginx + PHP-FPM** est le profil baseline recommandé ; Apache reste
Tier 1 pour compatibilité `.htaccess`/workloads historiques.
LiteSpeed/OpenLiteSpeed sont adapters optionnels.

PHP-FPM est le seul mode PHP baseline. Les versions PHP certifiées sont
définies par la matrice package/OS et non codées en dur dans l'UI.
