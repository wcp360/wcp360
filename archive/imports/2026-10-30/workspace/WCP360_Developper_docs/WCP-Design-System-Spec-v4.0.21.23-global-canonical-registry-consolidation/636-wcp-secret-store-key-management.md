# 636 --- WCP Secret Store & Key Management

## Statut de provenance

**\[V3.5 --- architecture proposée à partir des lacunes de la V3.4\]**
Ce contrat n'était pas spécifié en profondeur dans les documents
précédents. Il doit être validé par l'architecture technique avant
implémentation.

## Objectif

Compléter les règles d'affichage des secrets par leur stockage interne.

## Contrat

Secret references, encryption at rest, envelope/key hierarchy selon
architecture retenue, rotation, access audit, backup/restore policy.

## Master/root keys

Ne jamais stocker une clé racine dans la même forme et au même endroit
que les données qu'elle protège. HSM/KMS/external secret manager restent
des adapters possibles.

## À décider

Backend exact, rotation cadence, recovery/escrow et intégration KMS/HSM.
