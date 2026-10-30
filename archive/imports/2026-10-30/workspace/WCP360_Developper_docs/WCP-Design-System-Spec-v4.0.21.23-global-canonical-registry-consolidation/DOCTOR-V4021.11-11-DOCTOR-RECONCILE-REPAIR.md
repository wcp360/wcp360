# V4.0.21.11 --- Doctor / Diagnostics / Repair Planning

## Doctor

Read-only par défaut. Vérifie host profile, filesystem layout,
permissions, SecretStore, DB/control-plane, daemon RPC, services, ports,
DNS, TLS, mail, backup, scanner, cgroups/quotas, clocks/NTP and update
state.

## Résultat

Chaque check retourne code stable, severity, evidence-safe summary,
affected resource, remediation class and automation eligibility.

## Repair

`doctor repair-plan` génère un plan ; il ne répare pas implicitement.
Les réparations mutantes repassent par Operation ID → authorization →
Job → closed action → verify → audit.

## Support bundle

Export expurgé, borné, avec consentement/scope. Aucun secret brut.
