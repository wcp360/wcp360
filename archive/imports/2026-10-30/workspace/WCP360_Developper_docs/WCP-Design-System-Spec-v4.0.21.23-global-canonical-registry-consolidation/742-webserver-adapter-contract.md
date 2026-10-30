# 742 --- WebServer Adapter Contract

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 1**

Ce document transforme les décisions V3.6 déjà acceptées en contrat de
développement. Il ne remplace pas les règles de
`MASTER-SECURE-DEVELOPMENT-STANDARD.md`.

## Interface

Detect/version/capabilities, render vhost, validate config, diff, apply
atomic, reload, health, logs references.

## Ownership

WCP n'administre que ses fragments gérés. Apache/Nginx globaux ne sont
pas écrasés hors contrat explicite.

## Validation

Nginx/Apache config-test avant reload. Un échec laisse la dernière
configuration fonctionnelle active.
