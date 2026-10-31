# 655 --- WCP-ADMIN / WCP-USER Isolation

## Statut

**CANONICAL / NORMATIVE --- V3.5**

Les termes **MUST**, **MUST NOT**, **SHOULD**, **SHOULD NOT** et **MAY**
définissent le niveau d'exigence WCP. Toute exception à une règle
MUST/MUST NOT exige une décision d'architecture documentée et une revue
sécurité.

WCP-ADMIN et WCP-USER sont deux applications de sécurité distinctes :
Unix users, PHP-FPM pools, cookies/session namespaces, RBAC/scopes, rate
limits et hostnames séparables.

## MUST

Un cookie/session WCP-USER ne peut authentifier WCP-ADMIN. L'élévation
vers une opération administrative exige une autorisation côté serveur
et, selon policy, réauthentification/MFA.

## Web roots

Uniquement `/opt/wcp/current/admin/public/` et
`/opt/wcp/current/user/public/`.
