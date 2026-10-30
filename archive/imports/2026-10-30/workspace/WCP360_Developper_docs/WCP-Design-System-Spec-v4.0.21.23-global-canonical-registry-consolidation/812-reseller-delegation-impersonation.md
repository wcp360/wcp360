# 812 --- Reseller Delegation & Impersonation

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 3**

Ce document complète les contrats existants. Les règles de sécurité
`650–669` et les contrats systèmes `700–795` restent normatifs.

Le revendeur ne peut déléguer que ce qu'il possède lui-même.
`Log in as User` est une session d'impersonation explicitement marquée,
limitée, auditée et réversible ; l'identité réelle de l'opérateur reste
enregistrée. Actions sensibles peuvent être interdites pendant
impersonation ou exiger step-up.

## V4.0.4 normative deepening

Les contrats approfondis suivants s'appliquent :

- `HOSTING-404-02-RESELLER-DELEGATION-DEEP.md`

En cas d'ambiguïté, la V4.0.4 précise la sémantique sans annuler les exigences de sécurité antérieures.
