# 631 --- WCP Node Agent Contract

## Statut de provenance

**\[V3.5 --- architecture proposée à partir des lacunes de la V3.4\]**
Ce contrat n'était pas spécifié en profondeur dans les documents
précédents. Il doit être validé par l'architecture technique avant
implémentation.

## Objectif

Définir le contrat d'un agent éventuel sur les nœuds administrés.

## Responsabilités

Enrollment, identity, capability inventory, heartbeat/health, job
receive/execute/report, adapter access, telemetry forwarding et version
report.

## Limites

L'agent n'autorise pas une action parce qu'elle vient du réseau : il
valide identité du control plane, job contract et contraintes locales.

## Mode local

Une installation mono-serveur peut implémenter ce contrat localement
sans transport réseau, afin de conserver la même frontière
architecturale.

## V4.0.11 normative deepening

- `CLUSTER-4011-01-CLUSTER-NODE-MODEL.md`
- `CLUSTER-4011-02-NODE-ENROLLMENT-TRUST.md`
- `CLUSTER-4011-05-NODE-CORDON-DRAIN.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
