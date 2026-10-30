# 743 --- PHP Runtime Adapter Contract

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 1**

Ce document transforme les décisions V3.6 déjà acceptées en contrat de
développement. Il ne remplace pas les règles de
`MASTER-SECURE-DEVELOPMENT-STANDARD.md`.

## Fonctions

Inventory versions/extensions, install/remove via package layer, pool
render/validate/apply, INI effective value, runtime health.

## Isolation

Pool WCP-USER/site avec Unix identity appropriée ; PHP ne contacte
jamais `wcp-privd` directement.

## Removal

Preflight obligatoire : une version utilisée par un Website ne peut être
supprimée sans remapping explicite.

## V4.0.4 normative deepening

Les contrats approfondis suivants s'appliquent :

- `HOSTING-404-19-PHP-RUNTIME-VERSION-LIFECYCLE.md`
- `HOSTING-404-20-PHP-FPM-POOL-DEEP.md`

En cas d'ambiguïté, la V4.0.4 précise la sémantique sans annuler les exigences de sécurité antérieures.
