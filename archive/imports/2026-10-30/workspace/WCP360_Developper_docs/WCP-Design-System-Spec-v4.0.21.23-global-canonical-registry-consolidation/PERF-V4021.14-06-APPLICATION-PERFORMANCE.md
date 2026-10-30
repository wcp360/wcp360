# V4.0.21.14 --- Application Performance / APM Model

## Ressources

`performance_profile`, `trace_session`, `performance_finding`,
`slow_request_finding`, `slow_query_finding`.

## Sources

PHP/runtime timing, web latency, DB slow query metadata, external HTTP
latency, WordPress/plugin attribution when safely detectable, optional
CloudLinux X-Ray adapter.

## Sécurité

Tracing opt-in/policy-bound, duration bounded, sampling, redaction, no
request secrets/body capture by default, tenant isolation and retention.

## UI

Performance overview → bottleneck → evidence → remediation suggestion.
Une suggestion n'exécute jamais automatiquement une mutation
privilégiée.
