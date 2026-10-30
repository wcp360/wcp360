# 705 --- Go Adapter Interface Contract

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 1**

Ce document transforme les décisions V3.6 déjà acceptées en contrat de
développement. Il ne remplace pas les règles de
`MASTER-SECURE-DEVELOPMENT-STANDARD.md`.

## Interface conceptuelle

Chaque adapter expose `Name`, `Version`, `Detect`, `Capabilities`,
`Preflight`, `Apply/Execute`, `Verify`, `Health` et, lorsque sûr,
`Recover/Rollback`.

## Résultats

Pas de parsing libre dans l'UI. L'adapter retourne structures typées,
evidence minimale, exit/result classification et timestamps.

## Sécurité

Exécutables connus par registry ; timeout ; environnement minimal ;
aucune interpolation shell ; stdout/stderr redacted avant persistence.
