# ADR-006 --- Control Plane Availability & DR

## Statut

**ACCEPTED --- V3.6 Phase 1**

Cette décision est un **choix d'architecture WCP**. Lorsqu'une donnée
externe est citée, elle sert uniquement à vérifier la viabilité du choix
et ne remplace pas la décision produit.

## Décision

Le profil **Standalone Production** est READY en V1 : un control plane
par installation, sauvegardes automatisées et procédure DR testée. La HA
active-active reste un profil P1 ultérieur et ne bloque pas le
lancement.

## Objectifs internes V1

RPO cible control-plane **≤ 15 min** pour l'état transactionnel
sauvegardable et RTO cible **≤ 60 min** pour une restauration documentée
sur infrastructure prête. Ce sont des objectifs d'ingénierie, pas un SLA
commercial.

## Restore test

Test automatique de restaurabilité au minimum hebdomadaire et exercice
DR complet périodique selon exploitation.
