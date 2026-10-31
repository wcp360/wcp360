# ADR-003 --- Control Plane State Store

## Statut

**ACCEPTED --- V3.6 Phase 1**

Cette décision est un **choix d'architecture WCP**. Lorsqu'une donnée
externe est citée, elle sert uniquement à vérifier la viabilité du choix
et ne remplace pas la décision produit.

## Décision

**PostgreSQL** est la base canonique du control plane WCP. Le filesystem
ne constitue jamais la source de vérité métier.

## Données

Identités, RBAC, ressources, desired/observed metadata, jobs, events
indexés, configurations, inventory, migrations et références de secrets.
Les secrets eux-mêmes restent dans SecretStore.

## Règles

Transactions pour transitions cohérentes ; migrations versionnées ;
sauvegarde dédiée ; connexion TLS lorsque distante ; compte DB WCP
non-superuser.
