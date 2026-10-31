# 802 --- Registrar Adapter Contract

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 3**

Ce document complète les contrats existants. Les règles de sécurité
`650–669` et les contrats systèmes `700–795` restent normatifs.

Contrat : capability detection, availability si supportée,
create/register, renew, transfer, nameservers, lock, lifecycle/status,
contact operations selon fournisseur. Chaque opération indique si elle
est synchrone, async ou nécessite validation externe. WCP ne prétend pas
uniformiser les fonctions absentes chez un registrar.
