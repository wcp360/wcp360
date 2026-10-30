# 843 --- Composer / npm / pip Dependency Contract

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 3**

Ce document complète les contrats existants. Les règles de sécurité
`650–669` et les contrats systèmes `700–795` restent normatifs.

Composer/npm-yarn/pnpm/pip operations s'exécutent sous l'identité de
l'application, jamais root. Command/options sont construits depuis
schémas allowlistés ; timeouts/output limits. Lockfiles respectés ;
opération `update` distincte d'`install`.
