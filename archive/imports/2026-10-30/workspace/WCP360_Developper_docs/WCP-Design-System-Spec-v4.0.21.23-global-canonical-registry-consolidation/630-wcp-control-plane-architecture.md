# 630 --- WCP Control Plane Architecture

## Statut de provenance

**\[V3.5 --- architecture proposée à partir des lacunes de la V3.4\]**
Ce contrat n'était pas spécifié en profondeur dans les documents
précédents. Il doit être validé par l'architecture technique avant
implémentation.

## Objectif

Séparer le **control plane WCP** de ce qu'il administre.

## Couches logiques

API/UI Gateway → Authentication/Authorization → Domain Services → Job
Orchestrator → Adapter Layer → Node/Service Agents ou connecteurs
locaux/distants → systèmes gérés.

## Règles

-   L'UI n'exécute jamais une commande système directement.
-   Les services de domaine portent permissions, idempotence, audit et
    transitions d'état.
-   Les adapters traduisent le contrat WCP vers le backend réel.
-   Les jobs longs possèdent ID, état, cible, tentative, timestamps et
    correlation ID.
-   Les capacités d'un nœud sont découvertes et versionnées.

## Déploiement

Le contrat doit supporter un mode mono-nœud simple et permettre une
évolution vers un control plane séparé/multi-nœud sans changer les
ressources métier.
