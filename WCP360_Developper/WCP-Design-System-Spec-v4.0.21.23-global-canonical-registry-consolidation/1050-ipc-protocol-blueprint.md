# 1050 --- wcpd / wcp-privd IPC Protocol

## Statut

**CANONICAL --- DEVELOPER BLUEPRINT --- V3.9**

Ce document traduit les contrats `IMPLEMENTATION-READY` V3.8 en
organisation de code et ordre de construction. Il ne modifie pas les
décisions normatives de sécurité `650–669`.

Transport local Unix socket avec peer identity contrôlée. Messages
versionnés et typés : request_id, protocol_version, action_id, resource
context, typed params.

Réponse : status, typed result, error_code, evidence metadata redacted.

Le broker refuse versions/actions/params inconnus. Timeouts et tailles
bornées. Le socket `wcp-privd` n'est pas accessible aux PHP-FPM users.
