# V4.0.21.21 --- Logs / Audit / Retention / Privacy

Operational logs and immutable/security audit are separate
stores/contracts.

Audit includes actor/initiator, effective actor, operation/resource,
decision, scope, correlation/job IDs, before/after safe metadata,
outcome and reason. Secret values and sensitive message bodies are
prohibited.

Retention/export/deletion policies respect legal/security requirements.
Audit integrity/tamper evidence and sink degradation are monitored;
security events must not disappear silently during sink outage.
