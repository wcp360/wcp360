# 740 --- Web/PHP Resource Model

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 1**

Ce document transforme les décisions V3.6 déjà acceptées en contrat de
développement. Il ne remplace pas les règles de
`MASTER-SECURE-DEVELOPMENT-STANDARD.md`.

## Ressources

`WebServerInstance`, `Website`, `VirtualHost`, `PhpRuntime`,
`PhpFpmPool`, `PhpExtension`, `PhpIniProfile`, `RuntimeMapping`.

## Relations

Website → node → web adapter → vhost ; Website → PhpRuntime/Pool. Les
fichiers générés restent des projections de l'état métier, pas la source
de vérité.

## Baseline

Conformément à ADR-014 : Nginx + PHP-FPM recommandé ; Apache Tier 1 ;
LiteSpeed/OLS optionnels.

## V4.0.4 normative deepening

Les contrats approfondis suivants s'appliquent :

- `HOSTING-404-09-WEBSITE-VHOST-CANONICAL-MODEL.md`
- `HOSTING-404-10-WEB-ENGINE-CAPABILITY-MATRIX.md`

En cas d'ambiguïté, la V4.0.4 précise la sémantique sans annuler les exigences de sécurité antérieures.
