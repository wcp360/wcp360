# 721 --- API Authentication & Service Accounts

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 1**

Ce document transforme les décisions V3.6 déjà acceptées en contrat de
développement. Il ne remplace pas les règles de
`MASTER-SECURE-DEVELOPMENT-STANDARD.md`.

## Identités

Session utilisateur, personal token si produit l'autorise, et Service
Account sont distincts.

## Credentials

Création : secret affiché une fois ; stockage hashé ou secret-store
selon mécanisme ; rotation crée un nouveau credential avant révocation
de l'ancien.

## Restrictions

Scopes minimaux, expiration configurable, last-used, disable/revoke,
restrictions réseau seulement si l'implémentation peut les appliquer de
façon fiable.

## WCP integrations

Billing, CI/CD et IaC utilisent Service Accounts, pas le compte
personnel d'un administrateur.

## V4.0.9 normative deepening

- `API-409-01-API-FIRST-CANONICAL-CONTRACT.md`
- `API-409-02-API-AUTH-SERVICE-ACCOUNTS.md`
- `API-409-05-IDEMPOTENCY-MUTATIONS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
