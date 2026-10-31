# 662 --- Secret Storage Policy

## Statut

**CANONICAL / NORMATIVE --- V3.5**

Les termes **MUST**, **MUST NOT**, **SHOULD**, **SHOULD NOT** et **MAY**
définissent le niveau d'exigence WCP. Toute exception à une règle
MUST/MUST NOT exige une décision d'architecture documentée et une revue
sécurité.

## Emplacement local

`/etc/wcp/secrets/` est séparé par consommateur (`admin/`, `user/`,
`daemon/`, `privileged/`, `database/`, `integrations/`, `keys/`) avec
ownership/ACL minimaux.

## MUST NOT

Secrets dans `/opt/wcp`, Git, `.env` versionné, logs, traces, URLs ou
audit payload.

## Abstraction

Les consommateurs utilisent des secret references via une interface
SecretStore. Local encrypted store, KMS/Vault/HSM peuvent être des
adapters ; le backend définitif reste soumis à ADR.
