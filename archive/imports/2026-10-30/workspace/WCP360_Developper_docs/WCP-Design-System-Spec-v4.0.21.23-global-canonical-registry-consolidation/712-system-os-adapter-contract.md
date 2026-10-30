# 712 --- OS Adapter Contract

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 1**

Ce document transforme les décisions V3.6 déjà acceptées en contrat de
développement. Il ne remplace pas les règles de
`MASTER-SECURE-DEVELOPMENT-STANDARD.md`.

## Tier 1

Le contrat suit la matrice V3.6 : Debian 13, Ubuntu 24.04 LTS, AlmaLinux
9 et Rocky Linux 9 dans le périmètre certifié initial.

## Sous-adapters

Package manager, service manager, user/group, scheduler, log/journal,
storage inventory, reboot/maintenance.

## Détection

Distribution/version/architecture détectées ; `detected` ne vaut pas
`certified`. Une version hors matrice passe `unsupported` ou `unknown`,
jamais silencieusement `supported`.

## V4.0.3 normative deepening

Voir `SYSTEM-403-26-OS-ADAPTER-CONTRACT-DEEP.md`. Les exigences de ce contrat approfondi s'appliquent à cette spécification.
