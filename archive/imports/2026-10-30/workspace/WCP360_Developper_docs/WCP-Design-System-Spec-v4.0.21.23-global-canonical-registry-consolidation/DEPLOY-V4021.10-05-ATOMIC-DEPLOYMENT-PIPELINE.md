# V4.0.21.10 --- Atomic Git/Application Deployment Pipeline

## Pipeline canonique

Trigger → authorize → acquire source → verify revision → build workspace
→ dependency install → tests/health gates optionnels → release directory
→ secrets/env binding → pre-activation hooks → atomic release switch →
runtime reload/restart → health check → commit.

En cas d'échec après activation : rollback vers release précédente +
runtime restart + health verification.

## Répertoires

`releases/<immutable-id>`, `current` comme référence contrôlée, `shared`
pour données persistantes explicitement déclarées. Le modèle exact de
chemin reste défini par le filesystem layout canonique.

## Concurrence

Un lock de déploiement par application/site. Les triggers
supplémentaires sont coalescés/queueés selon policy.

## Hooks

Catalogue typé de hooks ; pas de champ shell libre. Les commandes
applicatives spécifiques doivent être des actions déclarées dans un
manifest autorisé et exécutées sous l'identité non privilégiée du
compte.
