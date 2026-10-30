# V4.0.21.11 --- Logs / Audit / Retention / Privacy Contract

## Classes

control-plane application logs, privileged daemon logs, job logs,
service logs, security events, authentication/audit events, access logs.

## Rules

Correlation/request/job IDs communs. Structured logging. Redaction
centralisée. Secrets, passwords, API tokens, private keys, recovery
codes et message bodies ne sont jamais des champs normaux de log.

## Access

Log query est tenant/scope aware. Support/impersonation ne donne pas
automatiquement accès à tous les logs sensibles.

## Retention

Policy par classe, rotation, compression, storage budget, deletion and
export. Audit append-only/tamper-evidence policy est distincte du log
opérationnel.
