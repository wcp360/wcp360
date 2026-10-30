# V4.0.21.21 --- Metrics / Logs / Traces / Health Model

Four signals are distinct: metrics, logs, traces and health/readiness.

Every signal has resource/tenant scope, timestamp, source and retention
class. High-cardinality/unbounded tenant labels are controlled.
Secrets/credentials/ message bodies are redacted according to source
policy.

Health is evidence-derived and does not equal process-running. Readiness
can aggregate dependency health and capability availability with reason
codes.
