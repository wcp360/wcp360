# 772 --- Database Adapter Contract

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 2**

Ce document dérive des décisions et contrats déjà présents dans la
V3.5/V3.6/V3.7. Les règles `650–669` restent normatives et prioritaires.

## Contrat

Detect/version/capabilities, create/drop DB, user, grants, connection
health, import/export hooks, backup/restore hooks, usage metrics.

## Engines

MySQL/MariaDB/PostgreSQL conservent leurs différences de permissions et
fonctionnalités ; WCP n'invente pas une parité inexistante.

## Sécurité

Pas de credentials DB dans arguments CLI persistants ; fichiers
temporaires protégés ; noms/identifiants validés par adapter.
