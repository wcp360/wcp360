# 842 --- Git / CI-CD Deployment Contract

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 3**

Ce document complète les contrats existants. Les règles de sécurité
`650–669` et les contrats systèmes `700–795` restent normatifs.

Git connection → repo/branch → checkout/worktree controlled → optional
dependency/build steps allowlisted → atomic/current release switch when
model supports it → health → deployment result. Webhook :
signature/secret verification, replay protection, event allowlist, rate
limit. Auto-pull ne modifie jamais le code WCP lui-même.
