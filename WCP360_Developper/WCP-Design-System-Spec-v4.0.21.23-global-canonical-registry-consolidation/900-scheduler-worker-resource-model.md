# 900 --- Scheduler / Worker Resource Model

## Statut

**CANONICAL --- V3.8 CLOSURE / IMPLEMENTATION CONTRACT**

Ce document complète le corpus existant. Les règles de sécurité
`650–669` et les contrats `700–874` restent normatifs.

Ressources : `ScheduledJob`, `Schedule`, `WorkerPool`, `Lease`,
`Execution`, `DeadLetterEntry`.

Le scheduler crée des Jobs du Control Plane ; il ne possède pas une
seconde implémentation de tâche. Les workers consomment le même Job
contract.
