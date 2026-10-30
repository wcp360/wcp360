# V4.0.13 --- Cross-Corpus Security Invariants

## Invariants consolidés

-   UI/API ne parlent pas directement au système privilégié en
    contournant les services métier.
-   Root daemon expose uniquement des actions typées/allowlistées ;
    aucune primitive shell arbitraire.
-   Real actor/effective actor conservés pour support/impersonation.
-   Secrets référencés, redacted, rotatables et distribués selon
    need-to-know.
-   Tenant isolation appliquée server-side à chaque ressource/query.
-   Builds/hooks/webhooks sont considérés comme entrées potentiellement
    hostiles.
-   Webhooks sortants protégés SSRF/TLS ; entrants
    signés/authentifiés/dédupliqués.
-   Retry de mutation exige idempotence/reconciliation.
-   Failover stateful exige ownership/fencing/data readiness.
-   Replication/HA ne remplace jamais backup/DR.
-   Update privilégié exige artefact vérifié et compatibilité.
-   Feature flag/licensing/telemetry ne peuvent jamais devenir des
    mécanismes d'autorisation implicites.
