# ADR-008 --- Telemetry Storage & Retention

## Statut

**ACCEPTED --- V3.6 Phase 1**

Cette décision est un **choix d'architecture WCP**. Lorsqu'une donnée
externe est citée, elle sert uniquement à vérifier la viabilité du choix
et ne remplace pas la décision produit.

## Décision V1

WCP fonctionne avec son stockage/collecte native minimale ;
Prometheus/Loki/OpenTelemetry restent adapters.

Valeurs par défaut : métriques agrégées 30 jours, logs WCP 30 jours,
traces 7 jours lorsqu'activées, health/current state conservé tant que
la ressource existe. Les policies peuvent augmenter/réduire ces valeurs
selon capacité et exigences.

## Garde-fous

Quotas de stockage, cardinality limits, redaction et tenant isolation
obligatoires.
