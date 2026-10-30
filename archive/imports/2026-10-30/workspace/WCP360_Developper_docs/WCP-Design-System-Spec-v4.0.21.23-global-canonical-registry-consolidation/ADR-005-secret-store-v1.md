# ADR-005 --- Secret Store V1

## Statut

**ACCEPTED --- V3.6 Phase 1**

Cette décision est un **choix d'architecture WCP**. Lorsqu'une donnée
externe est citée, elle sert uniquement à vérifier la viabilité du choix
et ne remplace pas la décision produit.

## Décision

V1 : SecretStore local avec **envelope encryption** ; données chiffrées
stockées hors code, clé maître root-only séparée de la base, permissions
minimales, AEAD et version de clé.

## Extensions

KMS/Vault/HSM restent adapters Enterprise. Une rotation crée une
nouvelle key version et rewrap/re-encrypt de façon contrôlée.

## Recovery

Le matériel de récupération de clé est sauvegardé séparément du backup
DB et son accès est fortement restreint. Perdre la clé sans recovery
signifie perdre les secrets : ce risque doit être testé dans le DR.
