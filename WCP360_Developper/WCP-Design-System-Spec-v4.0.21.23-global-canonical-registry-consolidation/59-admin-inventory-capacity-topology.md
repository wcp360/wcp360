# 59 --- WCP-ADMIN : inventaire, capacité et topologie

## Objectif V2

Donner une vision exploitable des ressources physiques/logiques.

## Inventaire

Serveurs/nœuds, CPU, RAM, stockage, interfaces, versions OS/runtime,
services et capacités déclarées.

## Capacité

Utilisé, réservé, disponible, tendance, seuils. Ne pas confondre
consommation instantanée et capacité allouée.

## Topologie

Cluster → nœuds → services/VM/conteneurs → stockage/réseau. La vue
graphique complète une vue tabulaire accessible ; elle ne la remplace
jamais.

## Drill-down

Toute anomalie agrégée doit permettre d'atteindre la ressource
responsable.

## V4.0.10 normative deepening

- `OPS-4010-06-LOGGING-CANONICAL.md`
- `OPS-4010-07-LOG-PIPELINE-RETENTION.md`
- `OPS-4010-12-CAPACITY-FORECASTING.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
