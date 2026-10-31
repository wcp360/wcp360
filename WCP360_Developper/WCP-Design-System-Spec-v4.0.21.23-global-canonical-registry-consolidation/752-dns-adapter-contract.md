# 752 --- DNS Adapter Contract

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 2**

Ce document dérive des décisions et contrats déjà présents dans la
V3.5/V3.6/V3.7. Les règles `650–669` restent normatives et prioritaires.

## Contrat

Detect/version/capabilities, zone render/validate/apply, record diff,
serial management, DNSSEC capability, primary/secondary, NOTIFY,
AXFR/IXFR/TSIG lorsque supportés, health/query verification.

## Sécurité

TSIG secret via SecretStore ; ACL explicites ; aucune zone non autorisée
accessible par ID deviné.

## BIND

Les fragments WCP sont identifiables comme gérés ; validation de
configuration avant reload.
