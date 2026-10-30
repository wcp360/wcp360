# 840 --- Developer Runtime / Git Resource Model

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 3**

Ce document complète les contrats existants. Les règles de sécurité
`650–669` et les contrats systèmes `700–795` restent normatifs.

Ressources : `RuntimeApp`, `RuntimeVersion`, `ProcessDefinition`,
`EnvironmentVariableRef`, `GitRepository`, `Deployment`,
`WebhookEndpoint`, `DependencyOperation`.

Managers : Node.js, Python WSGI/ASGI, Ruby/Passenger, Go apps selon
installed capabilities.
