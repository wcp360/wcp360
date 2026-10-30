# MASTER PACKAGE & REPOSITORY STRATEGY

## Statut

**DECISION REQUIRED.**

## Problème

PHP multi-version, runtimes, mail, DB et services dépendent de paquets
dont disponibilité et noms varient selon OS.

## Contrat requis

OS adapter → repository sources → package capability inventory → version
pinning/hold → install/update/remove job → health validation.

## Sécurité

Signature des dépôts/paquets, provenance, refus d'un dépôt non approuvé,
audit des changements de source.

## Règle

Le cœur WCP ne construit pas une commande `apt`/`dnf` directement depuis
une entrée UI.

## V4.0.12 normative deepening

- `LIFE-4012-08-OS-PACKAGE-LIFECYCLE.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.
