# 753 --- ACME / Certificate Adapter Contract

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 2**

Ce document dérive des décisions et contrats déjà présents dans la
V3.5/V3.6/V3.7. Les règles `650–669` restent normatives et prioritaires.

## Contrat

Account, order, authorization/challenge, finalize, deploy, renew, revoke
si supporté, expiry health.

## Challenges

HTTP-01 et DNS-01 selon capabilities. Wildcard uniquement avec méthode
compatible.

## Déploiement

Web/mail/autres targets explicitement enregistrés. Un certificat valide
n'est pas considéré `deployed` avant vérification du service cible.

## Sécurité

Private keys jamais dans logs/audit/API ; permissions minimales ; CA
configurable conformément aux specs ACME avancées.
