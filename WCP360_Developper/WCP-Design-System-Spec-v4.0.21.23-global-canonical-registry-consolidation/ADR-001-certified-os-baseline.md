# ADR-001 --- Certified OS Baseline

## Statut

**ACCEPTED --- V3.6 Phase 1**

Cette décision est un **choix d'architecture WCP**. Lorsqu'une donnée
externe est citée, elle sert uniquement à vérifier la viabilité du choix
et ne remplace pas la décision produit.

## Décision

Tier 1 de lancement : **Debian 13 amd64**, **Ubuntu 24.04 LTS amd64**,
**AlmaLinux 9 x86_64** et **Rocky Linux 9 x86_64**.

Tier 2/certification suivante : Ubuntu 26.04 LTS, AlmaLinux 10, Rocky
Linux 10, puis arm64 selon matrice de tests.

## Politique

WCP certifie une **major release** et exige les mises à jour
mineures/security courantes. `Detected` n'est jamais synonyme de
`Certified`. Une version EOL passe `unsupported` et les opérations
risquées peuvent être bloquées.

## Motivation

Réduire la matrice initiale tout en couvrant Debian/Ubuntu et Enterprise
Linux.
