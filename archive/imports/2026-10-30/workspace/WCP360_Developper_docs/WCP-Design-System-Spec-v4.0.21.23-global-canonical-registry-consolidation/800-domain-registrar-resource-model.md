# 800 --- Domain & Registrar Resource Model

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 3**

Ce document complète les contrats existants. Les règles de sécurité
`650–669` et les contrats systèmes `700–795` restent normatifs.

## Ressources

`Domain`, `DomainAlias`, `Subdomain`, `RegistrarConnection`,
`RegistrarDomain`, `NameserverSet`, `DomainContactRef`,
`DomainLifecycleState`.

## Relations

Le `Domain` WCP est la ressource d'hébergement canonique. Registrar,
DNS, Web, Mail et Certificate sont des sous-systèmes liés mais
distincts.

## États

`active`, `pending`, `suspended`, `expired`, `transfer_pending`,
`external`. Un état registrar observé ne doit pas être confondu avec
l'état d'hébergement WCP.

## V4.0.4 normative deepening

Les contrats approfondis suivants s'appliquent :

- `HOSTING-404-06-DOMAIN-CANONICAL-MODEL.md`

En cas d'ambiguïté, la V4.0.4 précise la sémantique sans annuler les exigences de sécurité antérieures.
