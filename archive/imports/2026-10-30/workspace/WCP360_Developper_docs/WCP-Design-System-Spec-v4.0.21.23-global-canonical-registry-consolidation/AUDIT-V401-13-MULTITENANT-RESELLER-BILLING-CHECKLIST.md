# V4.0.1 --- Multitenant Reseller Billing Checklist

Valider Root→Reseller→HostingAccount sans confondre hiérarchie et RBAC.
Délégation bornée, plans/entitlements, overcommit, quotas
disk/bandwidth/CPU/RAM/PIDs/DB/mail/domain,
suspension/termination/grace, upgrade/downgrade, ownership transfer,
white-label, hostname, impersonation et audit. Billing ne devient jamais
source technique ni exécuteur root.

## V4.0.9 normative deepening

- `API-409-13-BILLING-INTEGRATION-MODEL.md`
- `API-409-14-WHMCS-ADAPTER-CONTRACT.md`
- `API-409-15-BLESTA-FOSSBILLING-HOSTBILL.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
