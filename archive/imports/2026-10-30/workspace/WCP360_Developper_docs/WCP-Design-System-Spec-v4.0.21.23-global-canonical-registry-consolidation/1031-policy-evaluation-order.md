# 1031 --- Authorization Evaluation Order

## Statut

**CANONICAL --- DEVELOPER BLUEPRINT --- V3.9**

Ce document traduit les contrats `IMPLEMENTATION-READY` V3.8 en
organisation de code et ordre de construction. Il ne modifie pas les
décisions normatives de sécurité `650–669`.

Ordre obligatoire : 1. authentification ; 2. account/session state ; 3.
permission ; 4. tenant/resource scope ; 5. entitlement/plan ; 6.
resource lifecycle state ; 7. step-up/MFA si requis ; 8. policy context
; 9. action.

Un refus est audit-able sans révéler d'information permettant
l'énumération cross-tenant.
